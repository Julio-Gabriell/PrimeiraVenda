<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedido';

    protected $fillable = [
        'cliente_nome',
        'cliente_telefone',
        'valor_total',
        'status',
        'pix_copia_cola',
        'checkout_url',
        'pago_em',
    ];

    protected $dates = [
        'pago_em',
    ];
}
