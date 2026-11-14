<?php

namespace App\Http\Controllers;

use App\Models\ItemPedido;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ItemPedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $itens = ItemPedido::with(['pedido', 'produto'])->get();

        return response()->json($itens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $dados = $request->validate([
            'pedido_id' => ['required', 'integer', 'exists:pedido,id'],
            'produto_id' => ['required', 'integer', 'exists:produto,id'],
            'quantidade' => ['required', 'integer', 'min:1'],
            'preco_unitario' => ['required', 'numeric', 'min:0'],
        ]);

        $itemPedido = ItemPedido::create($dados);

        return response()->json($itemPedido, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $itemPedido = ItemPedido::with(['pedido', 'produto'])->findOrFail($id);

        return response()->json($itemPedido);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $itemPedido = ItemPedido::findOrFail($id);

        $dados = $request->validate([
            'pedido_id' => ['sometimes', 'required', 'integer', 'exists:pedido,id'],
            'produto_id' => ['sometimes', 'required', 'integer', 'exists:produto,id'],
            'quantidade' => ['sometimes', 'required', 'integer', 'min:1'],
            'preco_unitario' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        $itemPedido->update($dados);

        return response()->json($itemPedido);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $itemPedido = ItemPedido::findOrFail($id);

        $itemPedido->delete();

        return response()->noContent();
    }
}
