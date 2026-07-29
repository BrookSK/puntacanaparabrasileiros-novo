<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Voucher extends Model
{
    protected string $table = 'vouchers';
    protected array $fillable = [
        'booking_id', 'booking_item_id', 'transfer_booking_id',
        'reference_code', 'type', 'file_path', 'email_sent', 'whatsapp_sent',
    ];

    public function findByReference(string $reference): ?array
    {
        return $this->findWhere('reference_code', $reference);
    }

    public function getByBooking(int $bookingId): array
    {
        return $this->db->fetchAll(
            "SELECT v.*, 
                    COALESCE(t.title, CONCAT(tlo.title, ' → ', tld.title)) as trip_name
             FROM vouchers v
             LEFT JOIN booking_items bi ON v.booking_item_id = bi.id
             LEFT JOIN trips t ON bi.trip_id = t.id
             LEFT JOIN transfer_bookings tb ON v.transfer_booking_id = tb.id
             LEFT JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             LEFT JOIN transfer_locations tld ON tb.destination_id = tld.id
             WHERE v.booking_id = ?
             ORDER BY v.created_at DESC",
            [$bookingId]
        );
    }

    public function generateReference(): string
    {
        do {
            $reference = 'VCH-' . strtoupper(bin2hex(random_bytes(4)));
        } while ($this->exists('reference_code', $reference));
        return $reference;
    }

    public function markEmailSent(int $id): void
    {
        $this->db->update($this->table, ['email_sent' => 1], 'id = ?', [$id]);
    }

    public function markWhatsAppSent(int $id): void
    {
        $this->db->update($this->table, ['whatsapp_sent' => 1], 'id = ?', [$id]);
    }

    public function incrementDownload(int $id): void
    {
        $this->db->query(
            "UPDATE vouchers SET download_count = download_count + 1 WHERE id = ?",
            [$id]
        );
    }

    public function getExpired(int $days): array
    {
        return $this->where(
            'created_at < DATE_SUB(NOW(), INTERVAL ? DAY)',
            [$days],
            'created_at ASC'
        );
    }

    public function getAllWithDetails(int $page = 1, int $perPage = 20): array
    {
        $total = $this->count();
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT v.*,
                    CONCAT(b.billing_first_name, ' ', b.billing_last_name) as customer_name,
                    b.billing_email
             FROM vouchers v
             LEFT JOIN bookings b ON v.booking_id = b.id
             ORDER BY v.created_at DESC
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
}
