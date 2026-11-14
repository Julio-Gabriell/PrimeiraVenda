<?php

namespace App\Http\Controllers;

use App\Enums\Origem;
use App\Enums\StatusPedido;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pedidos = Pedido::with('itens.produto')->latest()->get();

        return response()->json($pedidos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'cliente_nome' => ['required', 'string', 'max:255'],
            'cliente_telefone' => ['nullable', 'string', 'max:20'],
            'valor_total' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::enum(StatusPedido::class)],
            'pix_copia_cola' => ['nullable', 'string'],
            'checkout_url' => ['nullable', 'string', 'max:500'],
            'pago_em' => ['nullable', 'date'],
            'itens' => ['nullable', 'array'],
            'itens.*.produto_id' => ['required_with:itens', 'integer', 'exists:produto,id'],
            'itens.*.quantidade' => ['required_with:itens', 'integer', 'min:1'],
            'itens.*.preco_unitario' => ['required_with:itens', 'numeric', 'min:0'],
        ]);

        $pedido = DB::transaction(function () use ($dados) {
            $pedido = Pedido::create([
                'cliente_nome' => $dados['cliente_nome'],
                'cliente_telefone' => $dados['cliente_telefone'] ?? null,
                'valor_total' => $dados['valor_total'],
                'status' => $dados['status'] ?? StatusPedido::pendente->value,
                'pix_copia_cola' => $dados['pix_copia_cola'] ?? null,
                'checkout_url' => $dados['checkout_url'] ?? null,
                'pago_em' => $dados['pago_em'] ?? null,
            ]);

            if (!empty($dados['itens'])) {
                foreach ($dados['itens'] as $itemData) {
                    ItemPedido::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $itemData['produto_id'],
                        'quantidade' => $itemData['quantidade'],
                        'preco_unitario' => $itemData['preco_unitario'],
                    ]);

                    // Baixa automática no estoque
                    $estoque = Estoque::firstOrNew(['produto_id' => $itemData['produto_id']]);
                    $estoque->quantidade = max(0, (int) $estoque->quantidade - (int) $itemData['quantidade']);
                    $estoque->ultima_origem = Origem::venda_online;
                    $estoque->atualizado_em = now();
                    $estoque->save();
                }
            }

            return $pedido;
        });

        $pedido->load('itens.produto');

        // Dispara webhook de venda para o n8n
        $resultadoWebhook = WebhookService::enviarVenda($pedido, 'venda_realizada');

        return response()->json([
            'pedido' => $pedido,
            'webhook' => $resultadoWebhook,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $pedido = Pedido::with('itens.produto')->findOrFail($id);

        return response()->json($pedido);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $pedido = Pedido::findOrFail($id);

        $dados = $request->validate([
            'cliente_nome' => ['sometimes', 'required', 'string', 'max:255'],
            'cliente_telefone' => ['nullable', 'string', 'max:20'],
            'valor_total' => ['sometimes', 'required', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::enum(StatusPedido::class)],
            'pix_copia_cola' => ['nullable', 'string'],
            'checkout_url' => ['nullable', 'string', 'max:500'],
            'pago_em' => ['nullable', 'date'],
        ]);

        $statusAnterior = $pedido->status;

        $pedido->update($dados);
        $pedido->refresh();

        $resultadoWebhook = null;
        // Se mudou para PAGO, dispara webhook de pedido pago
        $novoStatus = $pedido->status instanceof \BackedEnum ? $pedido->status->value : $pedido->status;
        $statusVelho = $statusAnterior instanceof \BackedEnum ? $statusAnterior->value : $statusAnterior;

        if ($novoStatus === StatusPedido::pago->value && $statusVelho !== StatusPedido::pago->value) {
            $resultadoWebhook = WebhookService::enviarVenda($pedido, 'pedido_pago');
        }

        return response()->json([
            'pedido' => $pedido->load('itens.produto'),
            'webhook' => $resultadoWebhook,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $pedido = Pedido::findOrFail($id);

        $pedido->delete();

        return response()->noContent();
    }

    /**
     * Reenvia manualmente o webhook de venda para o n8n.
     */
    public function reenviarWebhook(string $id): JsonResponse
    {
        $pedido = Pedido::with('itens.produto')->findOrFail($id);

        $resultado = WebhookService::enviarVenda($pedido, 'reenvio_manual');

        return response()->json([
            'mensagem' => 'Webhook disparado.',
            'resultado' => $resultado,
        ]);
    }
}
