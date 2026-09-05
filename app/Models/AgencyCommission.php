<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class AgencyCommission extends Model
{
    protected string $table = 'agency_commissions';
    protected array $fillable = [
        'agency_id', 'booking_id', 'amount', 'rate', 'base_amount',
        'status', 'paid_at', 'payout_reference', 'notes',
    ];

    /**
     * Comissões de uma agência (paginado), com número da reserva.
     */
    public function getByAgency(int $agencyId, int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll(
            "SELECT ac.*, b.booking_number
             FROM agency_commissions ac
             LEFT JOIN bookings b ON ac.booking_id = b.id
             WHERE ac.agency_id = ?
             ORDER BY ac.created_at DESC
             LIMIT " . (int) $perPage . " OFFSET " . (int) $offset,
            [$agencyId]
        );
    }

    /**
     * Lista todas as comissões (admin), com dados da agência. Filtro por status.
     */
    public function getAllWithAgency(int $page = 1, int $perPage = 20, string $status = 'all'): array
    {
        $where = '1=1';
        $params = [];
        if ($status !== 'all' && $status !== '') {
            $where = 'ac.status = ?';
            $params[] = $status;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM agency_commissions ac WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT ac.*, b.booking_number, ag.company_name, ag.trade_name, ag.ref_code
             FROM agency_commissions ac
             LEFT JOIN bookings b ON ac.booking_id = b.id
             LEFT JOIN agencies ag ON ac.agency_id = ag.id
             WHERE {$where}
             ORDER BY ac.created_at DESC
             LIMIT " . (int) $perPage . " OFFSET " . (int) $offset,
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / max(1, $perPage)),
        ];
    }

    public function getTotalPending(int $agencyId): float
    {
        return (float) $this->db->fetchColumn(
            "SELECT COALESCE(SUM(amount), 0) FROM agency_commissions WHERE agency_id = ? AND status = 'pending'",
            [$agencyId]
        );
    }

    public function markPaid(int $id, ?string $reference = null): void
    {
        $this->db->update($this->table, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'payout_reference' => $reference,
        ], 'id = ?', [$id]);
    }

    public function cancel(int $id, string $reason): void
    {
        $this->db->update($this->table, [
            'status' => 'cancelled',
            'notes' => $reason,
        ], 'id = ?', [$id]);
    }
}
