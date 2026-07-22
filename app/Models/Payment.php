<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Payment extends Model
{
    protected string $table = 'payments';
    protected array $fillable = [
        'booking_id', 'gateway', 'transaction_id', 'amount', 'currency',
        'status', 'type', 'gateway_response', 'payer_email',
    ];

    public function getByBooking(int $bookingId): array
    {
        return $this->where('booking_id = ?', [$bookingId], 'created_at DESC');
    }

    public function findByTransaction(string $transactionId): ?array
    {
        return $this->findWhere('transaction_id', $transactionId);
    }

    public function markCompleted(int $id, string $transactionId, ?string $gatewayResponse = null): int
    {
        $data = [
            'status' => 'completed',
            'transaction_id' => $transactionId,
        ];
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function markFailed(int $id, ?string $gatewayResponse = null): int
    {
        $data = ['status' => 'failed'];
        if ($gatewayResponse) {
            $data['gateway_response'] = $gatewayResponse;
        }
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function getTotalPaidForBooking(int $bookingId): float
    {
        $result = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE booking_id = ? AND status = 'completed'",
            [$bookingId]
        );
        return (float) $result;
    }
}
