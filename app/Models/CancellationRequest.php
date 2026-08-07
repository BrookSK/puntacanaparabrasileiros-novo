<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class CancellationRequest extends Model
{
    protected string $table = 'cancellation_requests';
    protected array $fillable = [
        'booking_id', 'user_id', 'reason', 'status',
        'admin_response', 'refund_status', 'refund_amount',
        'refund_notes', 'processed_by', 'processed_at', 'refunded_at',
    ];

    public function findByBooking(int $bookingId): ?array
    {
        return $this->findWhere('booking_id', (string) $bookingId);
    }

    public function getPending(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'pending'", [], 'created_at DESC');
    }

    public function getAll(int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }

        return $this->paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    public function approve(int $id, int $adminId, string $response): bool
    {
        return (bool) $this->db->update($this->table, [
            'status' => 'approved',
            'admin_response' => $response,
            'processed_by' => $adminId,
            'processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public function reject(int $id, int $adminId, string $response): bool
    {
        return (bool) $this->db->update($this->table, [
            'status' => 'rejected',
            'admin_response' => $response,
            'processed_by' => $adminId,
            'processed_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public function markRefunded(int $id, float $amount, string $notes = ''): bool
    {
        return (bool) $this->db->update($this->table, [
            'refund_status' => 'refunded',
            'refund_amount' => $amount,
            'refund_notes' => $notes,
            'refunded_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->paginate($page, $perPage, 'user_id = ?', [$userId], 'created_at DESC');
    }

    public function getPendingCount(): int
    {
        return $this->count("status = 'pending'");
    }
}
