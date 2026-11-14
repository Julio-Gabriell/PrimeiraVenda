<?php

use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ItemPedidoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

// Produtos
Route::apiResource('produtos', ProdutoController::class);

// Pedidos
Route::apiResource('pedidos', PedidoController::class);
Route::post('pedidos/{id}/reenviar-webhook', [PedidoController::class, 'reenviarWebhook']);

// Itens do Pedido
Route::apiResource('itens-pedido', ItemPedidoController::class);

// Estoque (Ajuste manual e consulta)
Route::get('estoque', [EstoqueController::class, 'index']);
Route::get('estoque/{produto_id}', [EstoqueController::class, 'show']);
Route::post('estoque/ajuste', [EstoqueController::class, 'ajustar']);
Route::put('estoque/{produto_id}', [EstoqueController::class, 'ajustar']);
