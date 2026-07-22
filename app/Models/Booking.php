<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Booking extends Model
{
    protected string $table = 'bookings';
    protected array $fillable = [
        'user_id', 'booking_number', 'status', 'subtotal', 'discount_amount',
        'total', 'paid_amount', 'due_amount', 'payment_mode', 'currency',
        'billing_first_name', 'billing_last_name', 'billing_email',
        'billing_phone', 'billing_address', 'billing_city', 'billing_country',
        'notes', 'admin_notes', 'affiliate_id', 'ip_address',
    ];

    public function findByNumber(string $bookingNumber): ?array
    {
        return $this->findWhere('booking_number', $bookingNumber);
    }

    public function generateBookingNumber(): string
    {
        do {
            $number = 'PCB-' . strtoupper(date('Ymd')) . '-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->exists('booking_number', $number));
        return $number;
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        return $this->paginate($page, $perPage, 'user_id = ?', [$userId], 'created_at DESC');
    }

    public function getItems(int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT bi.*, t.title as trip_title, t.slug as trip_slug, t.featured_image
             FROM booking_items bi
             INNER JOIN trips t ON bi.trip_id = t.id
             WHERE bi.booking_id = ?",
            [$bookingId]
        );
    }

    public function getTransferBookings(int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT tb.*, tv.title as vehicle_title, tv.image as vehicle_image,
                    tlo.title as origin_title, tld.title as destination_title
             FROM transfer_bookings tb
             INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
             INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
             WHERE tb.booking_id = ?
             ORDER BY tb.date ASC, tb.time ASC",
            [$bookingId]
        );
    }

    public function getPayments(int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC",
            [$bookingId]
        );
    }

    public function getTravelers(int $bookingItemId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM booking_travelers WHERE booking_item_id = ?",
            [$bookingItemId]
        );
    }

    public function getVouchers(int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM vouchers WHERE booking_id = ? ORDER BY created_at DESC",
            [$bookingId]
        );
    }

    public function updateStatus(int $id, string $status): int
    {
        return $this->db->update($this->table, ['status' => $status], 'id = ?', [$id]);
    }

    public function getRecent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT b.*, CONCAT(b.billing_first_name, ' ', b.billing_last_name) as customer_name
             FROM bookings b ORDER BY b.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getTodayCount(): int
    {
        return $this->count('DATE(created_at) = CURDATE()');
    }

    public function getMonthRevenue(): float
    {
        $result = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(paid_amount), 0) FROM bookings
             WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())
             AND status IN ('booked', 'completed', 'partially_paid')"
        );
        return (float) $result;
    }

    public function getPendingCount(): int
    {
        return $this->count("status = 'pending'");
    }

    public function getStats30Days(): array
    {
        return $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total) as revenue
             FROM bookings
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             GROUP BY DATE(created_at)
             ORDER BY date ASC"
        );
    }
}
