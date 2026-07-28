<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Serviço de integração com PagBank (PagSeguro) para pagamentos via PIX.
 */
class PagBankService
{
    private string $token;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $app = App::getInstance();
        $this->token = $app->setting('pagbank_token', '');
        $this->sandbox = $app->setting('pagbank_mode', 'sandbox') === 'sandbox';
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.api.pagseguro.com'
            : 'https://api.pagseguro.com';
    }

    /**
     * Cria uma cobrança PIX.
     *
     * @param float $amount Valor em USD (será convertido para BRL se necessário)
     * @param string $description Descrição da cobrança
     * @param array $customer Dados do cliente ['name', 'email', 'cpf']
     * @param string $referenceId ID de referência (booking_number)
     * @return array ['qr_code_url', 'qr_code_text', 'charge_id', 'expiration']
     */
    public function createPixCharge(float $amount, string $description, array $customer, string $referenceId): array
    {
        // Converter USD para BRL (usar taxa configurada ou fixa)
        $app = App::getInstance();
        $exchangeRate = (float) $app->setting('pagbank_usd_brl_rate', '5.50');
        $amountBRL = round($amount * $exchangeRate, 2);
        $amountCents = (int) ($amountBRL * 100);

        $payload = [
            'reference_id' => $referenceId,
            'description' => $description,
            'amount' => [
                'value' => $amountCents,
                'currency' => 'BRL',
            ],
            'payment_method' => [
                'type' => 'PIX',
                'pix' => [
                    'expires_at' => date('c', strtotime('+30 minutes')),
                ],
            ],
            'notification_urls' => [
                $app->setting('site_url', '') . '/api/webhooks/pagbank',
            ],
        ];

        // Adicionar dados do cliente se disponível
        if (!empty($customer['name'])) {
            $payload['customer'] = [
                'name' => $customer['name'],
                'email' => $customer['email'] ?? '',
                'tax_id' => $customer['cpf'] ?? '',
            ];
        }

        $response = $this->request('POST', '/charges', $payload);

        if (!$response || !isset($response['id'])) {
            throw new \RuntimeException('Falha ao criar cobrança PIX no PagBank: ' . json_encode($response));
        }

        // Extrair QR Code
        $qrCodes = $response['qr_codes'] ?? [];
        $qrCodeUrl = '';
        $qrCodeText = '';
        $expiration = '';

        if (!empty($qrCodes)) {
            $qr = $qrCodes[0];
            $qrCodeText = $qr['text'] ?? '';
            $expiration = $qr['expiration_date'] ?? '';
            // Links do QR Code (imagem)
            $links = $qr['links'] ?? [];
            foreach ($links as $link) {
                if (($link['rel'] ?? '') === 'QRCODE.PNG') {
                    $qrCodeUrl = $link['href'] ?? '';
                }
            }
        }

        return [
            'charge_id' => $response['id'],
            'status' => $response['status'] ?? 'WAITING',
            'qr_code_url' => $qrCodeUrl,
            'qr_code_text' => $qrCodeText,
            'expiration' => $expiration,
            'amount_brl' => $amountBRL,
            'exchange_rate' => $exchangeRate,
        ];
    }

    /**
     * Consulta status de uma cobrança.
     */
    public function getChargeStatus(string $chargeId): array
    {
        $response = $this->request('GET', '/charges/' . $chargeId);
        return $response ?? [];
    }

    /**
     * Verifica se o pagamento PIX foi confirmado.
     */
    public function isChargePaid(string $chargeId): bool
    {
        $charge = $this->getChargeStatus($chargeId);
        return ($charge['status'] ?? '') === 'PAID';
    }

    /**
     * Retorna o token para uso no frontend (se necessário).
     */
    public function getPublicKey(): string
    {
        $app = App::getInstance();
        return $app->setting('pagbank_public_key', '');
    }

    /**
     * Faz uma requisição HTTP à API do PagBank.
     */
    private function request(string $method, string $endpoint, ?array $data = null): ?array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
            'Accept: application/json',
            'x-api-version: 4.0',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || $response === false) {
            error_log("PagBank API Error [$httpCode]: $response");
            return null;
        }

        return json_decode($response, true);
    }
}
