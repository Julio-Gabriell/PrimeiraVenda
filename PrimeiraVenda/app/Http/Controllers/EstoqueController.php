<?php

namespace App\Http\Controllers;

use App\Enums\Origem;
use App\Models\Estoque;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EstoqueController extends Controller
{
    /**
     * Lista o estoque de todos os produtos.
     */
    public function index(): JsonResponse
    {
        $estoques = Estoque::with('produto')->get();

        return response()->json($estoques);
    }

    /**
     * Exibe o estoque de um produto específico.
     */
    public function show(string $produtoId): JsonResponse
    {
        $estoque = Estoque::with('produto')
            ->where('produto_id', $produtoId)
            ->first();

        if (!$estoque) {
            $produto = Produto::findOrFail($produtoId);
            $estoque = Estoque::create([
                'produto_id' => $produto->id,
                'quantidade' => 0,
                'ultima_origem' => Origem::ajuste_manual,
                'atualizado_em' => now(),
            ])->load('produto');
        }

        return response()->json($estoque);
    }

    /**
     * Ajusta o estoque manualmente (quantidade absoluta ou entrada/saída).
     */
    public function ajustar(Request $request, ?string $produtoId = null): JsonResponse
    {
        $dados = $request->validate([
            'produto_id' => [
                $produtoId ? 'nullable' : 'required',
                'integer',
                'exists:produto,id',
            ],
            'quantidade' => ['required', 'integer'],
            'operacao' => ['nullable', 'string', 'in:definir,entrada,saida,adicionar,remover'],
            'origem' => ['nullable', Rule::enum(Origem::class)],
        ]);

        $targetProdutoId = $produtoId ?: $dados['produto_id'];

        $estoque = Estoque::firstOrNew(['produto_id' => $targetProdutoId]);

        $operacao = $dados['operacao'] ?? 'definir';
        $quantidadeInformada = (int) $dados['quantidade'];

        $novaQuantidade = match ($operacao) {
            'entrada', 'adicionar' => max(0, (int) $estoque->quantidade + $quantidadeInformada),
            'saida', 'remover' => max(0, (int) $estoque->quantidade - $quantidadeInformada),
            default => max(0, $quantidadeInformada),
        };

        $origem = isset($dados['origem'])
            ? (Origem::tryFrom($dados['origem']) ?? Origem::ajuste_manual)
            : Origem::ajuste_manual;

        $estoque->quantidade = $novaQuantidade;
        $estoque->ultima_origem = $origem;
        $estoque->atualizado_em = now();
        $estoque->save();

        return response()->json([
            'mensagem' => 'Estoque atualizado com sucesso.',
            'estoque' => $estoque->load('produto'),
        ]);
    }
}

