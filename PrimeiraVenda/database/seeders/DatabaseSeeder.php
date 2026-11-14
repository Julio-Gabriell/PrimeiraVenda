<?php

namespace Database\Seeders;

use App\Enums\Origem;
use App\Enums\StatusPedido;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $p1 = Produto::create([
            'nome' => 'Camiseta Básica Preta G',
            'descricao' => 'Camiseta 100% algodão, cor preta, tamanho G.',
            'categoria' => 'Roupas',
            'preco' => 49.90,
            'url_imagem' => null,
            'ativo' => true,
        ]);

        $p2 = Produto::create([
            'nome' => 'Tênis Casual Branco Tam 40',
            'descricao' => 'Tênis casual branco, tamanho 40.',
            'categoria' => 'Calçados',
            'preco' => 189.90,
            'url_imagem' => null,
            'ativo' => true,
        ]);

        $p3 = Produto::create([
            'nome' => 'Relógio Couro Marrom',
            'descricao' => 'Relógio analógico com pulseira de couro marrom.',
            'categoria' => 'Acessórios',
            'preco' => 129.90,
            'url_imagem' => null,
            'ativo' => true,
        ]);

        Estoque::create([
            'produto_id' => $p1->id,
            'quantidade' => 5,
            'ultima_origem' => Origem::cadastro_whatsapp,
        ]);

        Estoque::create([
            'produto_id' => $p2->id,
            'quantidade' => 3,
            'ultima_origem' => Origem::cadastro_whatsapp,
        ]);

        Estoque::create([
            'produto_id' => $p3->id,
            'quantidade' => 2,
            'ultima_origem' => Origem::cadastro_whatsapp,
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Maria Silva',
            'cliente_telefone' => '5511999999999',
            'valor_total' => 49.90,
            'status' => StatusPedido::pendente,
            'pix_copia_cola' => '00020126580014br.gov.bcb.pix0136exemplo-chave-pix-0000-0000-00005204000053039865406049.905802BR5909PrimeiraVenda6009SAO PAULO62070503***6304ABCD',
            'checkout_url' => 'https://exemplo.com/checkout/1',
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $p1->id,
            'quantidade' => 1,
            'preco_unitario' => 49.90,
        ]);
    }
}
