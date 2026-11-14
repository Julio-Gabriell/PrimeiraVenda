<?php

namespace App\Enums;

enum StatusPedido: string {
    case pendente = 'PENDENTE';
    case pago = 'PAGO';
    case cancelado = 'CANCELADO';

    public function label(): string {
        return match ($this) {
            self::pendente => 'Status Pendente',
            self::pago => 'Status Pago',
            self::cancelado => 'Status Cancelado',
        };
    }
}
