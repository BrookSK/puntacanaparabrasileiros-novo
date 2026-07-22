<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Affiliate extends Model
{
    protected string $table = 'affiliates';
    protected array $fillable = [
        'user_id', 'status', 'commission_rate', 'cookie_days',
        'payment_email', 'payment_method', 'notes',
    ];

    public function findByUser(int $userId): ?array
    {
        return $this->findWhere('user_id', $userId);
    }

    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'created_at DESC');
    }

    public function getWithUserData(int $page = 1, int $perPage = 20): array
    {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT a.*, u.first_name, u.last_name, u.email
             FROM affiliates a
             INNER JOIN users u ON a.user_id = u.id
             ORDER BY a.created_at DESC
             LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function approve(int $id): int
    {
        return $this->db->update($this->table, ['status' => 'active'], 'id = ?', [$id]);
    }

    public function reject(int $id): int
    {
        return $this->db->update($this->table, ['status' => 'rejected'], 'id = ?', [$id]);
    }

    public function updateStats(int $id, float $saleAmount, float $commission): void
    {
        $this->db->query(
            "UPDATE affiliates SET
                total_referrals = total_referrals + 1,
                total_sales = total_sales + ?,
                total_earnings = total_earnings + ?
             WHERE id = ?",
            [$saleAmount, $commission, $id]
        );
    }

    public function trackVisit(int $affiliateId, string $ip, ?string $referrer, string $pageUrl, ?string $userAgent): int
    {
        return $this->db->insert('affiliate_visits', [
            'affiliate_id' => $affiliateId,
            'ip_address' => $ip,
            'referrer' => $referrer,
            'page_url' => $pageUrl,
            'user_agent' => $userAgent,
        ]);
    }

    public function incrementVisits(int $id): void
    {
        $this->db->query("UPDATE affiliates SET total_visits = total_visits + 1 WHERE id = ?", [$id]);
    }
}
