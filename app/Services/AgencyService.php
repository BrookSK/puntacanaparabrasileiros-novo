<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;
use App\Models\Agency;
use App\Models\AgencyCommission;

/**
 * Serviço de agências parceiras: atribuição de venda (cookie ?ag=) e comissões.
 */
class AgencyService
{
    private Agency $agencyModel;
    private AgencyCommission $commissionModel;
    private Database $db;
    private const COOKIE_NAME = 'pcb_ag';
    private const COOKIE_DAYS = 30;

    public function __construct()
    {
        $this->agencyModel = new Agency();
        $this->commissionModel = new AgencyCommission();
        $this->db = Database::getInstance();
    }

    /**
     * Registra a chegada via link ?ag=CODIGO e seta o cookie de atribuição.
     * O parâmetro é o ref_code da agência (não numérico).
     */
    public function trackVisit(string $refCode): void
    {
        $agency = $this->agencyModel->findByRefCode($refCode);
        if (!$agency || $agency['status'] !== 'active') {
            return;
        }
        setcookie(self::COOKIE_NAME, $agency['ref_code'], [
            'expires' => time() + (self::COOKIE_DAYS * 86400),
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Retorna a agência ativa atribuída (via cookie) ou null.
     */
    public function getActiveAgency(): ?array
    {
        $code = $_COOKIE[self::COOKIE_NAME] ?? null;
        if (!$code) return null;
        $agency = $this->agencyModel->findByRefCode((string) $code);
        if ($agency && $agency['status'] === 'active') {
            return $agency;
        }
        return null;
    }

    /**
     * Cria a comissão da agência após a venda confirmada.
     */
    public function createCommission(int $agencyId, int $bookingId, float $saleAmount): ?int
    {
        $agency = $this->agencyModel->find($agencyId);
        if (!$agency || $agency['status'] !== 'active') {
            return null;
        }

        // Evitar duplicidade para o mesmo booking
        $exists = $this->db->fetchColumn(
            "SELECT id FROM agency_commissions WHERE booking_id = ? LIMIT 1",
            [$bookingId]
        );
        if ($exists) return null;

        $rate = (float) $agency['commission_rate'];
        $commissionAmount = round($saleAmount * ($rate / 100), 2);
        if ($commissionAmount <= 0) return null;

        $commissionId = $this->commissionModel->create([
            'agency_id' => $agencyId,
            'booking_id' => $bookingId,
            'amount' => $commissionAmount,
            'rate' => $rate,
            'base_amount' => $saleAmount,
            'status' => 'pending',
        ]);

        $this->agencyModel->updateStats($agencyId, $saleAmount, $commissionAmount);

        return $commissionId;
    }

    /**
     * Gera o link de indicação da agência.
     */
    public function generateLink(string $refCode, string $url = '/'): string
    {
        $baseUrl = rtrim((string) App::getInstance()->setting('site_url', ''), '/');
        $separator = str_contains($url, '?') ? '&' : '?';
        return $baseUrl . $url . $separator . 'ag=' . rawurlencode($refCode);
    }
}
