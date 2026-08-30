<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;

/**
 * Centraliza as notificações WhatsApp enviadas aos afiliados.
 * Cada evento do ciclo de vida do afiliado dispara uma mensagem.
 * Todos os métodos são resilientes: nunca lançam exceção fatal.
 */
class AffiliateNotifier
{
    private WhatsappNotifier $notifier;
    private Database $db;

    public function __construct()
    {
        $this->notifier = new WhatsappNotifier();
        $this->db = Database::getInstance();
    }

    private function siteName(): string
    {
        return App::getInstance()->setting('site_name', 'Punta Cana para Brasileiros');
    }

    /**
     * Envia mensagem WhatsApp de forma segura.
     */
    private function send(?string $phone, string $name, string $message): void
    {
        if (empty($phone)) return;
        try {
            $this->notifier->sendToPhone($phone, $message, $name);
        } catch (\Throwable $e) {
            // Nunca quebrar o fluxo por causa de notificação
        }
    }

    /**
     * Busca telefone e nome de um afiliado ativo (via users).
     */
    private function getAffiliateContact(int $affiliateId): ?array
    {
        $row = $this->db->fetchOne(
            "SELECT u.phone, u.first_name, u.last_name
             FROM affiliates a
             INNER JOIN users u ON a.user_id = u.id
             WHERE a.id = ?",
            [$affiliateId]
        );
        if (!$row || empty($row['phone'])) return null;
        return [
            'phone' => $row['phone'],
            'name' => trim($row['first_name'] . ' ' . $row['last_name']),
        ];
    }

    // ─────────────────────────────────────────────
    // EVENTOS
    // ─────────────────────────────────────────────

    /**
     * 1. Cadastro recebido (solicitação pendente).
     */
    public function notifyRegistration(string $phone, string $name): void
    {
        $firstName = explode(' ', trim($name))[0] ?? $name;
        $msg = "✅ *Solicitação de afiliação recebida!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Recebemos sua solicitação para se tornar afiliado da {$this->siteName()}.\n\n";
        $msg .= "Nossa equipe vai analisar seu perfil e você receberá uma resposta em até 48 horas. ";
        $msg .= "Avisaremos por aqui assim que houver uma atualização.\n\n";
        $msg .= "Obrigado pelo interesse! 🌴";
        $this->send($phone, $name, $msg);
    }

    /**
     * 2. Afiliação aprovada.
     */
    public function notifyApproved(string $phone, string $name, string $siteUrl): void
    {
        $firstName = explode(' ', trim($name))[0] ?? $name;
        $msg = "🎉 *Parabéns! Sua afiliação foi aprovada!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Sua solicitação de afiliação foi *aprovada*. Agora você já pode acessar seu painel, ";
        $msg .= "gerar seus links e começar a ganhar comissões.\n\n";
        $msg .= "👉 Acesse: {$siteUrl}/painel-afiliado\n\n";
        $msg .= "Bem-vindo(a) ao time! 🚀";
        $this->send($phone, $name, $msg);
    }

    /**
     * 3. Afiliação recusada (com motivo).
     */
    public function notifyRejected(string $phone, string $name, string $reason): void
    {
        $firstName = explode(' ', trim($name))[0] ?? $name;
        $msg = "📋 *Atualização sobre sua solicitação de afiliação*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Analisamos sua solicitação de afiliação e, neste momento, ela *não foi aprovada*.\n\n";
        if (!empty($reason)) {
            $msg .= "*Motivo:* {$reason}\n\n";
        }
        $msg .= "Você pode tentar novamente futuramente. Qualquer dúvida, estamos à disposição.";
        $this->send($phone, $name, $msg);
    }

    /**
     * 4. Nova comissão gerada (venda realizada).
     */
    public function notifyCommissionEarned(int $affiliateId, float $commissionAmount, float $saleAmount): void
    {
        $contact = $this->getAffiliateContact($affiliateId);
        if (!$contact) return;

        $firstName = explode(' ', $contact['name'])[0] ?? $contact['name'];
        $msg = "💰 *Você recebeu uma nova comissão!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Uma venda foi realizada através do seu link de afiliado! 🎉\n\n";
        $msg .= "• Valor da venda: " . money($saleAmount) . "\n";
        $msg .= "• Sua comissão: *" . money($commissionAmount) . "*\n\n";
        $msg .= "A comissão está com status *pendente* e será paga conforme as regras do programa. ";
        $msg .= "Acompanhe tudo no seu painel de afiliado.";
        $this->send($contact['phone'], $contact['name'], $msg);
    }

    /**
     * 5. Comissão paga.
     */
    public function notifyCommissionPaid(int $affiliateId, float $amount, ?string $reference): void
    {
        $contact = $this->getAffiliateContact($affiliateId);
        if (!$contact) return;

        $firstName = explode(' ', $contact['name'])[0] ?? $contact['name'];
        $msg = "✅ *Sua comissão foi paga!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Uma boa notícia: sua comissão de *" . money($amount) . "* foi paga! 🎉\n\n";
        if (!empty($reference)) {
            $msg .= "• Referência do pagamento: {$reference}\n\n";
        }
        $msg .= "Obrigado por fazer parte do nosso programa de afiliados! 🌴";
        $this->send($contact['phone'], $contact['name'], $msg);
    }

    /**
     * 6. Comissão cancelada (com motivo).
     */
    public function notifyCommissionCancelled(int $affiliateId, float $amount, string $reason): void
    {
        $contact = $this->getAffiliateContact($affiliateId);
        if (!$contact) return;

        $firstName = explode(' ', $contact['name'])[0] ?? $contact['name'];
        $msg = "⚠️ *Atualização sobre uma comissão*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Uma comissão de " . money($amount) . " foi *cancelada*.\n\n";
        if (!empty($reason)) {
            $msg .= "*Motivo:* {$reason}\n\n";
        }
        $msg .= "Qualquer dúvida, entre em contato com nossa equipe.";
        $this->send($contact['phone'], $contact['name'], $msg);
    }
}
