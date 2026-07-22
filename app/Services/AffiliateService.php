<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Commission;
use Core\App;
use Core\Database;

/**
 * Serviço de gerenciamento de afiliados.
 * Cookie tracking, cálculo de comissões, payouts.
 */
class AffiliateService
{
    private Affiliate $affiliateModel;
    private Commission $commissionModel;
    private Database $db;
    private const COOKIE_NAME = 'pcb_ref';

    public function __construct()
    {
        $this->affiliateModel = new Affiliate();
        $this->commissionModel = new Commission();
        $this->db = Database::getInstance();
    }

    /**
     * Verifica se o programa de afiliados está ativo.
     */
    public function isEnabled(): bool
    {
        return App::getInstance()->setting('affiliate_enabled', '0') === '1';
    }

    /**
     * Processa visita via link de afiliado.
     * Seta cookie e registra visita.
     */
    public function trackVisit(int $affiliateId, string $ip, ?string $referrer, string $pageUrl, ?string $userAgent): void
    {
        $affiliate = $this->affiliateModel->find($affiliateId);
        if (!$affiliate || $affiliate['status'] !== 'active') {
            return;
        }

        // Setar cookie
        $cookieDays = (int) ($affiliate['cookie_days'] ?: App::getInstance()->setting('affiliate_cookie_days', '30'));
        $expiry = time() + ($cookieDays * 86400);

        setcookie(self::COOKIE_NAME, (string) $affiliateId, [
            'expires' => $expiry,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Registrar visita
        $this->affiliateModel->trackVisit($affiliateId, $ip, $referrer, $pageUrl, $userAgent);
        $this->affiliateModel->incrementVisits($affiliateId);
    }

    /**
     * Retorna o ID do afiliado ativo (via cookie).
     */
    public function getActiveAffiliateId(): ?int
    {
        $refId = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (!$refId) return null;

        $affiliate = $this->affiliateModel->find((int) $refId);
        if ($affiliate && $affiliate['status'] === 'active') {
            return (int) $affiliate['id'];
        }
        return null;
    }

    /**
     * Cria comissão após venda confirmada.
     */
    public function createCommission(int $affiliateId, int $bookingId, float $saleAmount): ?int
    {
        $affiliate = $this->affiliateModel->find($affiliateId);
        if (!$affiliate || $affiliate['status'] !== 'active') {
            return null;
        }

        $rate = (float) $affiliate['commission_rate'];
        $commissionAmount = round($saleAmount * ($rate / 100), 2);

        if ($commissionAmount <= 0) return null;

        $commissionId = $this->commissionModel->create([
            'affiliate_id' => $affiliateId,
            'booking_id' => $bookingId,
            'amount' => $commissionAmount,
            'rate' => $rate,
            'base_amount' => $saleAmount,
            'status' => 'pending',
        ]);

        // Atualizar stats do afiliado
        $this->affiliateModel->updateStats($affiliateId, $saleAmount, $commissionAmount);

        // Marcar visita como convertida
        $this->db->query(
            "UPDATE affiliate_visits SET converted = 1
             WHERE affiliate_id = ? AND converted = 0
             ORDER BY created_at DESC LIMIT 1",
            [$affiliateId]
        );

        return $commissionId;
    }

    /**
     * Gera link de afiliado.
     */
    public function generateLink(int $affiliateId, string $url = '/'): string
    {
        $baseUrl = App::getInstance()->setting('site_url', '');
        $separator = str_contains($url, '?') ? '&' : '?';
        return $baseUrl . $url . $separator . 'ref=' . $affiliateId;
    }

    /**
     * Relatório de performance do afiliado.
     */
    public function getAffiliateReport(int $affiliateId): array
    {
        $affiliate = $this->affiliateModel->find($affiliateId);
        if (!$affiliate) return [];

        $pendingCommissions = $this->commissionModel->getTotalPending($affiliateId);
        $recentCommissions = $this->commissionModel->getByAffiliate($affiliateId, 1, 10);

        return [
            'affiliate' => $affiliate,
            'pending_amount' => $pendingCommissions,
            'recent_commissions' => $recentCommissions,
        ];
    }
}
