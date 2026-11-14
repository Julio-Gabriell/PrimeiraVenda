<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Dispara o webhook de venda para o n8n.
     */
    public static function enviarVenda(Pedido $pedido, string $evento = 'venda_realizada'): array
    {
        $url = config('services.n8n.webhook_vendas', 'https://n8n.vendai.app.br/webhook-test/primeiravenda/vendas');

        if (empty($url)) {
            Log::warning('Webhook de vendas não configurado.');
            return ['sucesso' => false, 'motivo' => 'URL não configurada'];
        }

        $pedido->loadMissing('itens.produto');

        $payload = [
            'evento' => $evento,
            'pedido_id' => $pedido->id,
            'cliente' => [
                'nome' => $pedido->cliente_nome,
                'telefone' => $pedido->cliente_telefone,
            ],
            'valor_total' => (float) $pedido->valor_total,
            'status' => $pedido->status instanceof \BackedEnum ? $pedido->status->value : $pedido->status,
            'pix_copia_cola' => $pedido->pix_copia_cola,
            'checkout_url' => $pedido->checkout_url,
            'criado_em' => $pedido->created_at?->toIso8601String(),
            'pago_em' => $pedido->pago_em?->toIso8601String(),
            'itens' => $pedido->itens->map(fn ($item) => [
                'id' => $item->id,
                'produto_id' => $item->produto_id,
                'produto_nome' => $item->produto?->nome,
                'quantidade' => (int) $item->quantidade,
                'preco_unitario' => (float) $item->preco_unitario,
                'subtotal' => round($item->quantidade * $item->preco_unitario, 2),
            ])->values()->toArray(),
        ];

        try {
            Log::info("Disparando webhook de venda para {$url}", ['pedido_id' => $pedido->id]);

            $response = Http::timeout(10)->post($url, $payload);

            $sucesso = $response->successful();

            Log::info("Resposta do webhook n8n: {$response->status()}", [
                'pedido_id' => $pedido->id,
                'response_body' => $response->body(),
            ]);

            return [
                'sucesso' => $sucesso,
                'status_code' => $response->status(),
                'resposta' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error("Erro ao enviar webhook de venda: {$e->getMessage()}", [
                'pedido_id' => $pedido->id,
                'exception' => $e,
            ]);

            return [
                'sucesso' => false,
                'erro' => $e->getMessage(),
            ];
        }
    }
}
