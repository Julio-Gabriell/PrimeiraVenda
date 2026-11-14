<?php

namespace App\Http\Controllers;

use App\Enums\StatusPedido;
use App\Models\Pedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pedidos = Pedido::with('itens.produto')->get();

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
        ]);

        $pedido = Pedido::create($dados);

        return response()->json($pedido, 201);
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

        $pedido->update($dados);

        return response()->json($pedido);
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
}
