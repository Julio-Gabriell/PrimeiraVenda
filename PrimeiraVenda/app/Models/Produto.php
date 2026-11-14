<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produto';

    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'preco',
        'url_imagem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'preco' => 'decimal:2',
    ];

    public function estoque(): HasOne
    {
        return $this->hasOne(Estoque::class, 'produto_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'produto_id');
    }
}
