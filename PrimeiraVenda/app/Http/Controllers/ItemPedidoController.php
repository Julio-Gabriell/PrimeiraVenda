<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemPedido;

class ItemPedidoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = validated($request);

        $itemPedido = ItemPedido::create([
            'pedido_id' => $dados['pedido_id'],
            'produto_id' => $dados['produto_id'],
            'quantidade' => $dados['quantidade'],
            'preco_unitario' => $dados['preco_unitario']
        ]);

        return response()->json($itemPedido);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $itemPedido = ItemPedido::findOrFail($id);

        return response()->json($itemPedido);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $itemPedido = ItemPedido::findOrFail($id);

        $dados = validated($request);

        $itemPedido = ItemPedido::update([
            'pedido_id' => $dados['pedido_id'],
            'produto_id' => $dados['produto_id'],
            'quantidade' => $dados['quantidade'],
            'preco_unitario' => $dados['preco_unitario']
        ]);

        return response()->json($itemPedido);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $itemPedido = ItemPedido::findOrFail($id);
        $itemPedido::delete();
    }
}
