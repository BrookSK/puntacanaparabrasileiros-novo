<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TransferBooking extends Model
{
    protected string $table = 'transfer_bookings';
    protected array $fillable = [
        'booking_id', 'group_id', 'vehicle_id', 'origin_id', 'destination_id',
        'date', 'time', 'type', 'service_type', 'price', 'adults', 'children',
        'infants', 'customer_name', 'customer_email', 'customer_phone',
        'passengers', 'flight_number', 'flight_time', 'status',
    ];

    public function getByBooking(int $bookingId): array
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

    public function getByCustomerEmail(string $email, int $page = 1, int $perPage = 10): array
    {
        return $this->paginate(
            $page, $perPage,
            'customer_email = ?',
            [$email],
            'created_at DESC'
        );
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $sql = "SELECT tb.*, tv.title as vehicle_title, tv.image as vehicle_image,
                       tlo.title as origin_title, tld.title as destination_title
                FROM transfer_bookings tb
                INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
                INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
                INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
                INNER JOIN bookings b ON tb.booking_id = b.id
                WHERE b.user_id = ?
                ORDER BY tb.date DESC
                LIMIT ? OFFSET ?";
        $offset = ($page - 1) * $perPage;
        return $this->db->fetchAll($sql, [$userId, $perPage, $offset]);
    }

    public function updateStatus(int $id, string $status): int
    {
        return $this->db->update($this->table, ['status' => $status], 'id = ?', [$id]);
    }

    public function getRecent(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT tb.*, tv.title as vehicle_title,
                    tlo.title as origin_title, tld.title as destination_title
             FROM transfer_bookings tb
             INNER JOIN transfer_vehicles tv ON tb.vehicle_id = tv.id
             INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
             ORDER BY tb.created_at DESC LIMIT ?",
            [$limit]
        );
    }
}
