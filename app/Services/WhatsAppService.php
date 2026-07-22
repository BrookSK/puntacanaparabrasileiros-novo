<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Serviço de notificações WhatsApp via webhook.
 * Envia mensagens automáticas após confirmação de reserva.
 */
class WhatsAppService
{
    private string $webhookUrl;
    private bool $enabled;

    public function __construct()
    {
        $app = App::getInstance();
        $this->webhookUrl = $app->setting('whatsapp_webhook_url', '');
        $this->enabled = $app->setting('whatsapp_enabled', '0') === '1';
    }

    /**
     * Envia mensagem de confirmação de passeio.
     */
    public function sendTripConfirmation(array $booking, array $tripData): bool
    {
        if (!$this->enabled || !$this->webhookUrl) {
            return false;
        }

        $phone = $this->formatPhone($booking['billing_phone'] ?? '');
        if (!$phone) return false;

        $template = App::getInstance()->setting('whatsapp_trip_template', '');
        $message = $this->replaceVariables($template, [
            'customer_name' => $booking['billing_first_name'] . ' ' . $booking['billing_last_name'],
            'trip_name' => $tripData['title'] ?? '',
            'trip_date' => $tripData['date'] ?? '',
            'trip_time' => $tripData['time'] ?? '',
            'pax_info' => $tripData['pax_info'] ?? '',
            'total' => '$' . number_format((float) ($booking['total'] ?? 0), 2),
            'reference' => $tripData['reference'] ?? $booking['booking_number'] ?? '',
        ]);

        return $this->sendMessage($phone, $message);
    }

    /**
     * Envia mensagem de confirmação de transfer.
     */
    public function sendTransferConfirmation(array $transferData): bool
    {
        if (!$this->enabled || !$this->webhookUrl) {
            return false;
        }

        $phone = $this->formatPhone($transferData['customer_phone'] ?? '');
        if (!$phone) return false;

        $template = App::getInstance()->setting('whatsapp_transfer_template', '');
        $message = $this->replaceVariables($template, [
            'customer_name' => $transferData['customer_name'] ?? '',
            'vehicle_name' => $transferData['vehicle_title'] ?? '',
            'origin' => $transferData['origin_title'] ?? '',
            'destination' => $transferData['destination_title'] ?? '',
            'date' => $transferData['date'] ?? '',
            'time' => $transferData['time'] ?? '',
            'pax_info' => ($transferData['adults'] ?? 0) . ' adulto(s)',
            'reference' => $transferData['reference'] ?? '',
        ]);

        return $this->sendMessage($phone, $message);
    }

    /**
     * Envia mensagem genérica via webhook.
     */
    public function sendMessage(string $phone, string $message): bool
    {
        $payload = json_encode([
            'numero' => $phone,
            'message' => $message,
        ]);

        $ch = curl_init($this->webhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Formata número de telefone para formato internacional.
     */
    private function formatPhone(string $phone): string
    {
        // Remove tudo exceto dígitos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Se começa com 0, assumir Brasil e adicionar 55
        if (str_starts_with($phone, '0')) {
            $phone = '55' . substr($phone, 1);
        }

        // Se tem menos de 10 dígitos, inválido
        if (strlen($phone) < 10) {
            return '';
        }

        return $phone;
    }

    /**
     * Substitui variáveis no template.
     */
    private function replaceVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }
        return $template;
    }
}
