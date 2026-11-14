<?php

namespace App\Models;

use App\Enums\Origem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque';

    public $timestamps = false;

    protected $fillable = [
        'produto_id',
        'quantidade',
        'ultima_origem',
        'atualizado_em',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'ultima_origem' => Origem::class,
        'atualizado_em' => 'datetime',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}

