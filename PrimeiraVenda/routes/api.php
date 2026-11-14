<?php

use App\Http\Controllers\ItemPedidoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('produtos', ProdutoController::class);
Route::apiResource('pedidos', PedidoController::class);
Route::apiResource('itens-pedido', ItemPedidoController::class);

