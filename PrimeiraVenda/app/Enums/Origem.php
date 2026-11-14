<?php

namespace App\Enums;

enum Origem: string {
    case cadastro_whatsapp = 'CADASTRO_WHATSAPP';
    case venda_online = 'VENDA_ONLINE';
    case baixa_balcao = 'BAIXA_BALCAO';

    public function label(): string {
        return match ($this) {
            self::cadastro_whatsapp => 'Cadastro pelo Whatsapp',
            self::venda_online => 'Venda Online',
            self::baixa_balcao => 'Baixa pelo Balcão',
        };
    }
}