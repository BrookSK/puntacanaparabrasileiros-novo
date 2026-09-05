<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;

/**
 * Notificações para as agências parceiras (WhatsApp + e-mail),
 * seguindo o mesmo padrão do AffiliateNotifier.
 * Todos os métodos são resilientes: nunca lançam exceção fatal.
 */
class AgencyNotifier
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function app(): App
    {
        return App::getInstance();
    }

    private function siteName(): string
    {
        return $this->app()->setting('site_name', 'Punta Cana para Brasileiros');
    }

    private function siteUrl(): string
    {
        return rtrim((string) $this->app()->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/');
    }

    /**
     * Envia WhatsApp de forma segura (mesmo padrão comprovado do checkout/afiliados).
     */
    private function sendWhatsApp(?string $phone, string $message): void
    {
        if (empty($phone)) return;
        try {
            $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE connection_status = 'open' LIMIT 1");
            if (!$instance) {
                $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE is_default = 1 LIMIT 1");
            }
            if (!$instance) {
                error_log('[AgencyNotifier] Nenhuma instância WhatsApp disponível');
                return;
            }
            $api = EvolutionApi::fromInstance($instance);
            $normalizedPhone = EvolutionApi::normalizePhone($phone);
            $api->sendText($normalizedPhone, $message);
        } catch (\Throwable $e) {
            error_log('[AgencyNotifier] Erro ao enviar WhatsApp: ' . $e->getMessage());
        }
    }

    private function sendEmail(?string $to, string $toName, string $subject, string $template, array $data): void
    {
        if (empty($to)) return;
        try {
            (new EmailService())->sendTemplate($to, $toName, $subject, $template, $data);
        } catch (\Throwable $e) {
            error_log('[AgencyNotifier] Erro ao enviar email: ' . $e->getMessage());
        }
    }

    private function firstName(array $agency): string
    {
        $name = (string) ($agency['contact_name'] ?? '');
        if ($name === '') {
            $name = (string) ($agency['trade_name'] ?? $agency['company_name'] ?? '');
        }
        $parts = explode(' ', trim($name));
        return $parts[0] ?: ($agency['company_name'] ?? 'Parceiro');
    }

    // ─────────────────────────────────────────────
    // EVENTOS
    // ─────────────────────────────────────────────

    /**
     * Venda realizada: comissão gerada, aguardando pagamento.
     *
     * @param array $agency Registro completo da agência
     */
    public function notifySale(array $agency, float $commissionAmount, float $saleAmount, ?string $bookingNumber = null): void
    {
        $firstName = $this->firstName($agency);
        $site = $this->siteName();

        // WhatsApp
        $msg = "💰 *Você realizou uma venda!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Uma venda foi realizada através da sua parceria com a {$site}! 🎉\n\n";
        if ($bookingNumber) {
            $msg .= "• Reserva: *{$bookingNumber}*\n";
        }
        $msg .= "• Valor da venda: " . money($saleAmount) . "\n";
        $msg .= "• Sua comissão: *" . money($commissionAmount) . "*\n\n";
        $msg .= "A comissão está com status *pendente* e será paga conforme as regras da parceria. ";
        $msg .= "Você será avisado quando o pagamento for efetuado.";
        $this->sendWhatsApp($agency['phone'] ?? null, $msg);

        // E-mail
        $this->sendEmail(
            $agency['email'] ?? null,
            $agency['trade_name'] ?: $agency['company_name'],
            'Venda realizada - comissão aguardando pagamento - ' . $site,
            'agency-sale',
            [
                'firstName' => $firstName,
                'saleAmount' => money($saleAmount),
                'commissionAmount' => money($commissionAmount),
                'bookingNumber' => $bookingNumber,
                'siteUrl' => $this->siteUrl(),
            ]
        );
    }

    /**
     * Comissão paga.
     */
    public function notifyCommissionPaid(array $agency, float $amount, ?string $reference = null): void
    {
        $firstName = $this->firstName($agency);
        $site = $this->siteName();

        // WhatsApp
        $msg = "✅ *Sua comissão foi paga!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Boa notícia: sua comissão de *" . money($amount) . "* foi paga! 🎉\n\n";
        if (!empty($reference)) {
            $msg .= "• Referência do pagamento: {$reference}\n\n";
        }
        $msg .= "Obrigado por fazer parte das agências parceiras da {$site}! 🌴";
        $this->sendWhatsApp($agency['phone'] ?? null, $msg);

        // E-mail
        $this->sendEmail(
            $agency['email'] ?? null,
            $agency['trade_name'] ?: $agency['company_name'],
            'Sua comissão foi paga - ' . $site,
            'agency-commission-paid',
            [
                'firstName' => $firstName,
                'amount' => money($amount),
                'reference' => $reference,
                'siteUrl' => $this->siteUrl(),
            ]
        );
    }

    /**
     * Comissão cancelada (com motivo).
     */
    public function notifyCommissionCancelled(array $agency, float $amount, string $reason): void
    {
        $firstName = $this->firstName($agency);
        $site = $this->siteName();

        // WhatsApp
        $msg = "⚠️ *Atualização sobre uma comissão*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Uma comissão de " . money($amount) . " foi *cancelada*.\n\n";
        if ($reason !== '') {
            $msg .= "*Motivo:* {$reason}\n\n";
        }
        $msg .= "Qualquer dúvida, entre em contato com nossa equipe.";
        $this->sendWhatsApp($agency['phone'] ?? null, $msg);

        // E-mail
        $this->sendEmail(
            $agency['email'] ?? null,
            $agency['trade_name'] ?: $agency['company_name'],
            'Atualização sobre uma comissão - ' . $site,
            'agency-commission-cancelled',
            [
                'firstName' => $firstName,
                'amount' => money($amount),
                'reason' => $reason,
                'siteUrl' => $this->siteUrl(),
            ]
        );
    }
}
