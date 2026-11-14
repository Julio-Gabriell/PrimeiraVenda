<?php

namespace Tests\Feature;

use App\Enums\Origem;
use App\Enums\StatusPedido;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_and_create_produtos(): void
    {
        $response = $this->postJson('/api/produtos', [
            'nome' => 'Mouse Gamer',
            'descricao' => 'Mouse óptico 16000 DPI',
            'categoria' => 'Periféricos',
            'preco' => 199.90,
            'url_imagem' => 'https://exemplo.com/mouse.jpg',
            'ativo' => true,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Mouse Gamer']);

        $produtoId = $response->json('id');

        $this->getJson("/api/produtos/{$produtoId}")
             ->assertStatus(200)
             ->assertJsonFragment(['nome' => 'Mouse Gamer']);

        $this->putJson("/api/produtos/{$produtoId}", [
            'nome' => 'Mouse Gamer Pro',
        ])->assertStatus(200)
          ->assertJsonFragment(['nome' => 'Mouse Gamer Pro']);

        $this->deleteJson("/api/produtos/{$produtoId}")
             ->assertStatus(204);

        $this->assertDatabaseMissing('produto', ['id' => $produtoId]);
    }

    public function test_can_create_and_manage_pedidos(): void
    {
        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Carlos Pereira',
            'cliente_telefone' => '5511988887777',
            'valor_total' => 150.00,
            'status' => StatusPedido::pendente->value,
            'pix_copia_cola' => 'pix-string-example',
            'checkout_url' => 'https://exemplo.com/checkout/carlos',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['cliente_nome' => 'Carlos Pereira']);

        $pedidoId = $response->json('id');

        $this->getJson("/api/pedidos/{$pedidoId}")
             ->assertStatus(200)
             ->assertJsonFragment(['cliente_nome' => 'Carlos Pereira']);

        $this->putJson("/api/pedidos/{$pedidoId}", [
            'status' => StatusPedido::pago->value,
            'pago_em' => now()->toDateTimeString(),
        ])->assertStatus(200)
          ->assertJsonFragment(['status' => StatusPedido::pago->value]);

        $this->deleteJson("/api/pedidos/{$pedidoId}")
             ->assertStatus(204);

        $this->assertDatabaseMissing('pedido', ['id' => $pedidoId]);
    }

    public function test_can_create_item_pedido(): void
    {
        $produto = Produto::create([
            'nome' => 'Teclado Mecânico',
            'preco' => 250.00,
            'ativo' => true,
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Ana Souza',
            'cliente_telefone' => '5511977776666',
            'valor_total' => 250.00,
            'status' => StatusPedido::pendente,
        ]);

        $response = $this->postJson('/api/itens-pedido', [
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 250.00,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['quantidade' => 1]);

        $itemId = $response->json('id');

        $this->getJson("/api/itens-pedido/{$itemId}")
             ->assertStatus(200)
             ->assertJsonFragment(['preco_unitario' => '250.00']);
    }

    public function test_estoque_and_produto_relationship(): void
    {
        $produto = Produto::create([
            'nome' => 'Monitor 24',
            'preco' => 899.90,
            'ativo' => true,
        ]);

        $estoque = Estoque::create([
            'produto_id' => $produto->id,
            'quantidade' => 10,
            'ultima_origem' => Origem::cadastro_whatsapp,
        ]);

        $this->assertEquals($estoque->produto_id, $produto->estoque->produto_id);
        $this->assertEquals(Origem::cadastro_whatsapp, $estoque->ultima_origem);
    }
}

