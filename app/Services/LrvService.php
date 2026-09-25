<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Integração com a API do LRV (helpdeskON) — v1.
 *
 * Envia demandas de suporte da Punta Cana para o LRV via POST /api/v1/tickets,
 * autenticando com o cabeçalho X-Api-Key. Uma chave por empresa (o LRV já
 * resolve empresa/usuário a partir da chave — não enviamos essas infos).
 *
 * Credenciais ficam na tabela `settings`:
 *   - lrv_base_url  (ex.: https://SEU-DOMINIO)
 *   - lrv_api_key   (ex.: hk_live_xxx)
 *   - lrv_enabled   (0/1)
 *
 * Espelha o padrão dos demais Services (StripeService, EvolutionApi): cURL puro
 * e um método privado request().
 */
class LrvService
{
    private string $baseUrl;
    private string $apiKey;
    private bool $enabled;

    public function __construct()
    {
        $app = App::getInstance();
        $this->baseUrl = rtrim((string) $app->setting('lrv_base_url', ''), '/');
        $this->apiKey = (string) $app->setting('lrv_api_key', '');
        $this->enabled = (string) $app->setting('lrv_enabled', '0') === '1';
    }

    /**
     * Integração configurada e habilitada?
     */
    public function isConfigured(): bool
    {
        return $this->enabled && $this->baseUrl !== '' && $this->apiKey !== '';
    }

    /**
     * Cria um chamado no LRV.
     *
     * @param array $data Campos aceitos: title (obrigatório), description
     *                    (obrigatório), priority, category, requester_name,
     *                    requester_company, external_ref.
     *
     * @return array{ok:bool,status:int,idempotent:bool,data:array,error:?string}
     *         Resultado normalizado — nunca lança exceção, para que a demanda
     *         local seja sempre preservada mesmo com falha de rede.
     */
    public function createTicket(array $data): array
    {
        if (!$this->isConfigured()) {
            return $this->result(false, 0, false, [], 'Integração LRV não configurada.');
        }

        // Monta o payload apenas com os campos aceitos pela API.
        $payload = array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? null,
            'category' => $data['category'] ?? null,
            'requester_name' => $data['requester_name'] ?? null,
            'requester_company' => $data['requester_company'] ?? null,
            'external_ref' => $data['external_ref'] ?? null,
        ], static fn($v) => $v !== null && $v !== '');

        return $this->request('POST', '/api/v1/tickets', $payload);
    }

    /**
     * Request genérica à API do LRV.
     *
     * @return array{ok:bool,status:int,idempotent:bool,data:array,error:?string}
     */
    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl . $endpoint,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'X-Api-Key: ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_POSTFIELDS => $payload ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
        ]);

        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            return $this->result(false, 0, false, [], 'Falha de conexão com o LRV: ' . $curlErr);
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return $this->result(false, $httpCode, false, [], 'Resposta inválida do LRV (HTTP ' . $httpCode . ').');
        }

        // Sucesso: 201 (criado) ou 200 (idempotente — já existia).
        if ($httpCode === 201 || $httpCode === 200) {
            return $this->result(
                true,
                $httpCode,
                !empty($decoded['idempotent']),
                $decoded['data'] ?? [],
                null
            );
        }

        // Erro: extrai a mensagem no formato padrão { error: { code, message } }.
        $message = $decoded['error']['message']
            ?? ('Erro do LRV (HTTP ' . $httpCode . ').');

        return $this->result(false, $httpCode, false, $decoded['data'] ?? [], $message);
    }

    /**
     * @return array{ok:bool,status:int,idempotent:bool,data:array,error:?string}
     */
    private function result(bool $ok, int $status, bool $idempotent, array $data, ?string $error): array
    {
        return [
            'ok' => $ok,
            'status' => $status,
            'idempotent' => $idempotent,
            'data' => $data,
            'error' => $error,
        ];
    }
}
