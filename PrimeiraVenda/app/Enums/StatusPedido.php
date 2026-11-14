<?php

namespace App\Enums;

enum StatusPedido: string {
    case pendente = 'pendente';
    case pago = 'pago';
    case cancelado = 'cancelado';

    public function label(): string {
        return match ($this) {
            self::pendente => 'Status Pendente',
            self::pago => 'Status Pago',
            self::cancelado => 'Status Cancelado',
        };
    }
}