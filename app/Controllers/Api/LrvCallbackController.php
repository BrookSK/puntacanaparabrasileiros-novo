<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\SupportTicket;

/**
 * Recebe o callback de status do LRV (helpdeskON → Punta Cana).
 *
 * O LRV faz POST (JSON) nesta URL sempre que o status de um chamado criado via
 * API muda. Atualizamos o lrv_status da demanda local correspondente
 * (identificada pelo external_ref). Endpoint público — protegido por um token
 * no caminho da URL (a doc recomenda URL não adivinhável; o callback não é
 * assinado com HMAC nesta versão).
 *
 * Regras da doc:
 *  - Responder sempre HTTP 2xx quando processar com sucesso (senão o LRV reenvia).
 *  - Tratar de forma idempotente (o mesmo evento pode chegar mais de uma vez).
 *  - Ignorar o POST de teste (campo "test": true).
 */
class LrvCallbackController extends Controller
{
    public function handle(Request $request, Response $response): void
    {
        // 1) Valida o token da URL contra a setting (se um token estiver configurado).
        $configuredToken = (string) $this->setting('lrv_callback_token', '');
        $providedToken = (string) $request->param('token', '');
        if ($configuredToken !== '' && !hash_equals($configuredToken, $providedToken)) {
            $this->json(['success' => false, 'error' => 'invalid_token'], 403);
            return;
        }

        $body = $request->json();

        // 2) Só processa o evento esperado.
        if (!is_array($body) || ($body['event'] ?? '') !== 'ticket.status_changed') {
            $this->json(['success' => false, 'error' => 'bad_request'], 400);
            return;
        }

        // 3) POST de teste disparado pelo painel do LRV: apenas confirma o recebimento.
        if (!empty($body['test'])) {
            $this->json(['success' => true, 'test' => true], 200);
            return;
        }

        $externalRef = isset($body['external_ref']) ? (string) $body['external_ref'] : '';
        $status = isset($body['status']) ? (string) $body['status'] : '';

        // Dados insuficientes: responde 2xx mesmo assim para não gerar reenvio infinito.
        if ($externalRef === '' || $status === '') {
            $this->json(['success' => true, 'ignored' => 'missing_fields'], 200);
            return;
        }

        // Status fora da lista conhecida: registra assim mesmo (a doc pode evoluir),
        // mas não falha — apenas segue.
        $ticketModel = new SupportTicket();
        $updated = $ticketModel->updateStatusByExternalRef($externalRef, $status);

        // Sempre 2xx: processado (updated) ou idempotente/desconhecido (no-op).
        $this->json([
            'success' => true,
            'updated' => $updated,
            'external_ref' => $externalRef,
            'status' => $status,
        ], 200);
    }
}
