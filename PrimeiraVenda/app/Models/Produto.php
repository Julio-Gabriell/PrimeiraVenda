<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
}
