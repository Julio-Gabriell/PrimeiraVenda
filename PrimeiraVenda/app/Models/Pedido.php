<?php

namespace App\Models;

use App\Enums\StatusPedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'status' => StatusPedido::class,
        'pago_em' => 'datetime',
        'valor_total' => 'decimal:2',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }
}
