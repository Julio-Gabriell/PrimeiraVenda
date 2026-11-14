<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::all();

        return response()->json($produtos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $dados = validated($request);

        $produto = Produto::create([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'categoria' => $dados['categoria'],
            'preco' => $dados['preco'],
            'url_imagem' => $dados['url_imagem'],
            'ativo' => $dados['ativo'],
        ]);

        return response()->json($produto);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $produto = Produto::findOrFail($id);

        return response()->json($produto);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $produto = Produto::findOrFail($id);

        $dados = validated($request);

        $produto = Produto::update([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'categoria' => $dados['categoria'],
            'preco' => $dados['preco'],
            'url_imagem' => $dados['url_imagem'],
            'ativo' => $dados['ativo'],
        ]);

        return response()->json($produto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $produto = Produto::findOrFail($id);

        $produto::delete();
    }
}
