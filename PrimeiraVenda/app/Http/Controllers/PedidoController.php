<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::all();

        return response()->json($pedidos);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = validated($request);

        $pedido = Pedido::create([
            'cliente_nome' => $dados['cliente_nome'],
            'cliente_telefone' => $dados['client_telefone'],
            'valor_total' => $dados['valor_total'],
            'status' => $dados['status'],
            'pix_copia_cola' => $dados['pix_copia_cola'],
            'checkout_url' => $dados['checkout_url'],
            'pago_em' => $dados['pago_em'],
        ]);

        return response()->json($pedido);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pedido = Pedido::findOrFail($id);

        return response()-json($pedido);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pedido = Pedido::findOrFail($id);

        $dados = validated($request);

        $pedido = Pedido::update([
            'cliente_nome' => $dados['cliente_nome'],
            'cliente_telefone' => $dados['client_telefone'],
            'valor_total' => $dados['valor_total'],
            'status' => $dados['status'],
            'pix_copia_cola' => $dados['pix_copia_cola'],
            'checkout_url' => $dados['checkout_url'],
            'pago_em' => $dados['pago_em'],
        ]);

        return response()-json($pedido);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pedido = Pedido::findOrFail($id);
        $pedido::delete();
    }
}
