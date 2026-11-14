<?php

namespace Tests\Feature;

use App\Enums\Origem;
use App\Enums\StatusPedido;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

    public function test_can_create_and_manage_pedidos_and_dispatch_webhook(): void
    {
        Http::fake([
            'https://n8n.vendai.app.br/*' => Http::response(['status' => 'received'], 200),
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Carlos Pereira',
            'cliente_telefone' => '5511988887777',
            'valor_total' => 150.00,
            'status' => StatusPedido::pendente->value,
            'pix_copia_cola' => 'pix-string-example',
            'checkout_url' => 'https://exemplo.com/checkout/carlos',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('pedido.cliente_nome', 'Carlos Pereira');

        $pedidoId = $response->json('pedido.id');

        Http::assertSent(function ($request) use ($pedidoId) {
            return str_contains($request->url(), 'webhook-test/primeiravenda/vendas') &&
                   $request['pedido_id'] === $pedidoId &&
                   $request['evento'] === 'venda_realizada';
        });

        $this->getJson("/api/pedidos/{$pedidoId}")
             ->assertStatus(200)
             ->assertJsonFragment(['cliente_nome' => 'Carlos Pereira']);

        // Atualiza para PAGO -> deve disparar outro webhook
        $this->putJson("/api/pedidos/{$pedidoId}", [
            'status' => StatusPedido::pago->value,
            'pago_em' => now()->toDateTimeString(),
        ])->assertStatus(200)
          ->assertJsonPath('pedido.status', StatusPedido::pago->value);

        Http::assertSent(function ($request) use ($pedidoId) {
            return $request['pedido_id'] === $pedidoId &&
                   $request['evento'] === 'pedido_pago';
        });

        // Teste de reenvio manual
        $this->postJson("/api/pedidos/{$pedidoId}/reenviar-webhook")
             ->assertStatus(200)
             ->assertJsonPath('mensagem', 'Webhook disparado.');

        $this->deleteJson("/api/pedidos/{$pedidoId}")
             ->assertStatus(204);

        $this->assertDatabaseMissing('pedido', ['id' => $pedidoId]);
    }

    public function test_can_create_item_pedido_and_decrements_stock(): void
    {
        $produto = Produto::create([
            'nome' => 'Teclado Mecânico',
            'preco' => 250.00,
            'ativo' => true,
        ]);

        Estoque::create([
            'produto_id' => $produto->id,
            'quantidade' => 10,
            'ultima_origem' => Origem::cadastro_whatsapp,
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
            'quantidade' => 3,
            'preco_unitario' => 250.00,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['quantidade' => 3]);

        $itemId = $response->json('id');

        $this->getJson("/api/itens-pedido/{$itemId}")
             ->assertStatus(200)
             ->assertJsonFragment(['preco_unitario' => '250.00']);

        // Estoque deve ter reduzido de 10 para 7
        $this->assertDatabaseHas('estoque', [
            'produto_id' => $produto->id,
            'quantidade' => 7,
            'ultima_origem' => Origem::venda_online->value,
        ]);
    }

    public function test_estoque_ajuste_manual(): void
    {
        $produto = Produto::create([
            'nome' => 'Monitor 24',
            'preco' => 899.90,
            'ativo' => true,
        ]);

        // 1. Ajuste absoluto via POST /api/estoque/ajuste
        $this->postJson('/api/estoque/ajuste', [
            'produto_id' => $produto->id,
            'quantidade' => 15,
            'origem' => Origem::ajuste_manual->value,
        ])->assertStatus(200)
          ->assertJsonPath('estoque.quantidade', 15)
          ->assertJsonPath('estoque.ultima_origem', Origem::ajuste_manual->value);

        // 2. Ajuste relativo (entrada) via PUT /api/estoque/{produto_id}
        $this->putJson("/api/estoque/{$produto->id}", [
            'quantidade' => 5,
            'operacao' => 'entrada',
        ])->assertStatus(200)
          ->assertJsonPath('estoque.quantidade', 20);

        // 3. Ajuste relativo (saída)
        $this->putJson("/api/estoque/{$produto->id}", [
            'quantidade' => 8,
            'operacao' => 'saida',
        ])->assertStatus(200)
          ->assertJsonPath('estoque.quantidade', 12);

        // 4. Consulta via GET /api/estoque/{produto_id}
        $this->getJson("/api/estoque/{$produto->id}")
             ->assertStatus(200)
             ->assertJsonPath('quantidade', 12);
    }
}
