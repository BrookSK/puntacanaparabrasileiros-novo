<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Commission extends Model
{
    protected string $table = 'commissions';
    protected array $fillable = [
        'affiliate_id', 'booking_id', 'amount', 'rate',
        'base_amount', 'status', 'paid_at', 'payout_reference', 'notes',
    ];

    public function getByAffiliate(int $affiliateId, int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, 'affiliate_id = ?', [$affiliateId], 'created_at DESC');
    }

    public function getPending(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'pending'", [], 'created_at DESC');
    }

    public function approve(int $id): int
    {
        return $this->db->update($this->table, ['status' => 'approved'], 'id = ?', [$id]);
    }

    public function markPaid(int $id, ?string $payoutReference = null): int
    {
        return $this->db->update($this->table, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'payout_reference' => $payoutReference,
        ], 'id = ?', [$id]);
    }

    public function getTotalPending(int $affiliateId): float
    {
        $result = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(amount), 0) FROM commissions WHERE affiliate_id = ? AND status IN ('pending', 'approved')",
            [$affiliateId]
        );
        return (float) $result;
    }
}
