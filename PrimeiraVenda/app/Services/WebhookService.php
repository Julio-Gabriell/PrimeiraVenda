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
            'pedido' => [
                'id' => $pedido->id,
                'cliente_nome' => $pedido->cliente_nome,
                'valor_total' => (float) $pedido->valor_total,
                'status' => $pedido->status instanceof \BackedEnum ? $pedido->status->value : $pedido->status,
                'pago_em' => $pedido->pago_em?->toIso8601String() ?? $pedido->pago_em,
            ],
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