<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Booking extends Model
{
    protected string $table = 'bookings';
    protected array $fillable = [
        'user_id', 'booking_number', 'status', 'subtotal', 'discount_amount',
        'coupon_id', 'coupon_code',
        'total', 'paid_amount', 'due_amount', 'payment_mode', 'currency',
        'billing_first_name', 'billing_last_name', 'billing_email',
        'billing_phone', 'billing_address', 'billing_city', 'billing_country',
        'notes', 'admin_notes', 'affiliate_id', 'ip_address', 'flight_voucher_path',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'referrer',
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
            "SELECT bi.*, t.title as trip_title, t.slug as trip_slug, t.featured_image,
                    tp.title as package_title
             FROM booking_items bi
             INNER JOIN trips t ON bi.trip_id = t.id
             LEFT JOIN trip_packages tp ON bi.package_id = tp.id
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
            "SELECT v.*,
                    t.title as trip_name,
                    tlo.title as origin_title,
                    tld.title as destination_title,
                    CASE WHEN v.type = 'transfer' THEN CONCAT(COALESCE(tlo.title,''), ' → ', COALESCE(tld.title,'')) ELSE NULL END as route_name
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

    // ============================================================
    // RELATÓRIOS
    // ============================================================

    /**
     * Constrói cláusula WHERE de período (created_at entre datas) para reuso.
     */
    private function periodWhere(?string $from, ?string $to, array &$params): string
    {
        $where = "status IN ('booked','partially_paid','completed')";
        if ($from) {
            $where .= " AND DATE(created_at) >= ?";
            $params[] = $from;
        }
        if ($to) {
            $where .= " AND DATE(created_at) <= ?";
            $params[] = $to;
        }
        return $where;
    }

    /**
     * Resumo geral (KPIs) do período.
     */
    public function getReportSummary(?string $from, ?string $to): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) as total_bookings,
                    COALESCE(SUM(total), 0) as total_revenue,
                    COALESCE(SUM(paid_amount), 0) as total_paid,
                    COALESCE(AVG(total), 0) as avg_ticket
             FROM bookings WHERE {$where}",
            $params
        );
        return $row ?: ['total_bookings' => 0, 'total_revenue' => 0, 'total_paid' => 0, 'avg_ticket' => 0];
    }

    /**
     * Vendas por país (billing_country).
     */
    public function getSalesByCountry(?string $from, ?string $to): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        return $this->db->fetchAll(
            "SELECT COALESCE(NULLIF(billing_country, ''), 'Não informado') as country,
                    COUNT(*) as bookings,
                    COALESCE(SUM(total), 0) as revenue
             FROM bookings WHERE {$where}
             GROUP BY country
             ORDER BY revenue DESC",
            $params
        );
    }

    /**
     * Vendas por cidade (billing_city).
     */
    public function getSalesByCity(?string $from, ?string $to, int $limit = 15): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        return $this->db->fetchAll(
            "SELECT COALESCE(NULLIF(billing_city, ''), 'Não informado') as city,
                    COALESCE(NULLIF(billing_country, ''), '') as country,
                    COUNT(*) as bookings,
                    COALESCE(SUM(total), 0) as revenue
             FROM bookings WHERE {$where}
             GROUP BY city, country
             ORDER BY revenue DESC
             LIMIT {$limit}",
            $params
        );
    }

    /**
     * Volume de vendas por dia (série temporal).
     */
    public function getSalesTimeline(?string $from, ?string $to): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        return $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as bookings, COALESCE(SUM(total), 0) as revenue
             FROM bookings WHERE {$where}
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            $params
        );
    }

    /**
     * Origem dos clientes: afiliado, tráfego pago (UTM), direto.
     */
    public function getSalesByOrigin(?string $from, ?string $to): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        return $this->db->fetchAll(
            "SELECT
                CASE
                    WHEN utm_source IS NOT NULL AND utm_source <> '' THEN CONCAT('Tráfego: ', utm_source)
                    WHEN affiliate_id IS NOT NULL THEN 'Afiliado'
                    WHEN referrer IS NOT NULL AND referrer <> '' THEN 'Referência externa'
                    ELSE 'Direto'
                END as origin,
                COUNT(*) as bookings,
                COALESCE(SUM(total), 0) as revenue
             FROM bookings WHERE {$where}
             GROUP BY origin
             ORDER BY revenue DESC",
            $params
        );
    }

    /**
     * Relatório de campanhas de tráfego pago (por utm_campaign).
     */
    public function getSalesByCampaign(?string $from, ?string $to): array
    {
        $params = [];
        $where = $this->periodWhere($from, $to, $params);
        return $this->db->fetchAll(
            "SELECT
                COALESCE(NULLIF(utm_source, ''), '(sem origem)') as source,
                COALESCE(NULLIF(utm_medium, ''), '(sem mídia)') as medium,
                COALESCE(NULLIF(utm_campaign, ''), '(sem campanha)') as campaign,
                COUNT(*) as bookings,
                COALESCE(SUM(total), 0) as revenue
             FROM bookings
             WHERE {$where} AND (utm_source IS NOT NULL AND utm_source <> '')
             GROUP BY source, medium, campaign
             ORDER BY revenue DESC",
            $params
        );
    }

    /**
     * Passeios mais vendidos no período.
     */
    public function getTopTrips(?string $from, ?string $to, int $limit = 10): array
    {
        $params = [];
        // filtro de período sobre bookings via join
        $where = "b.status IN ('booked','partially_paid','completed')";
        if ($from) { $where .= " AND DATE(b.created_at) >= ?"; $params[] = $from; }
        if ($to) { $where .= " AND DATE(b.created_at) <= ?"; $params[] = $to; }

        return $this->db->fetchAll(
            "SELECT t.title, COUNT(bi.id) as sales, COALESCE(SUM(bi.price), 0) as revenue
             FROM booking_items bi
             INNER JOIN bookings b ON bi.booking_id = b.id
             INNER JOIN trips t ON bi.trip_id = t.id
             WHERE {$where}
             GROUP BY t.id, t.title
             ORDER BY revenue DESC
             LIMIT {$limit}",
            $params
        );
    }

    /**
     * Vendas por método de pagamento (gateway).
     */
    public function getSalesByGateway(?string $from, ?string $to): array
    {
        $params = [];
        $where = "p.status = 'completed'";
        if ($from) { $where .= " AND DATE(p.created_at) >= ?"; $params[] = $from; }
        if ($to) { $where .= " AND DATE(p.created_at) <= ?"; $params[] = $to; }

        return $this->db->fetchAll(
            "SELECT p.gateway, COUNT(*) as payments, COALESCE(SUM(p.amount), 0) as total
             FROM payments p
             WHERE {$where}
             GROUP BY p.gateway
             ORDER BY total DESC",
            $params
        );
    }
}
