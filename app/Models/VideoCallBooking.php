<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class VideoCallBooking extends Model
{
    protected string $table = 'videocall_bookings';
    protected array $fillable = [
        'trip_id', 'customer_name', 'email', 'phone', 'scheduled_at',
        'duration_minutes', 'meeting_link', 'status', 'reminder_sent',
        'notes', 'admin_notes',
    ];

    /**
     * Lista paginada com dados do passeio associado (para o painel admin).
     */
    public function getWithTrip(int $page = 1, int $perPage = 20, ?string $status = null): array
    {
        $where = '1=1';
        $params = [];
        if ($status !== null && $status !== '') {
            $where = 'v.status = ?';
            $params[] = $status;
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM videocall_bookings v WHERE {$where}",
            $params
        );
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT v.*, t.title AS trip_title, t.slug AS trip_slug
             FROM videocall_bookings v
             LEFT JOIN trips t ON v.trip_id = t.id
             WHERE {$where}
             ORDER BY v.scheduled_at DESC
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

    /**
     * Verifica se já existe agendamento ativo no mesmo horário.
     */
    public function slotTaken(string $scheduledAt): bool
    {
        $row = $this->db->fetchColumn(
            "SELECT id FROM videocall_bookings
             WHERE scheduled_at = ? AND status IN ('pending','confirmed') LIMIT 1",
            [$scheduledAt]
        );
        return (bool) $row;
    }

    /**
     * Horários já ocupados (pending/confirmed) a partir de agora.
     * Retorna array de strings 'Y-m-d H:i:s'.
     */
    public function bookedSlotsFrom(string $fromDateTime): array
    {
        $rows = $this->db->fetchAll(
            "SELECT scheduled_at FROM videocall_bookings
             WHERE scheduled_at >= ? AND status IN ('pending','confirmed')",
            [$fromDateTime]
        );
        return array_map(static fn($r) => $r['scheduled_at'], $rows);
    }

    /**
     * Agendamentos que precisam de lembrete: acontecem dentro da janela
     * (agora .. agora + $windowMinutes), ainda não tiveram lembrete enviado
     * e estão ativos.
     */
    public function dueForReminder(int $windowMinutes = 60): array
    {
        $now = date('Y-m-d H:i:s');
        $limit = date('Y-m-d H:i:s', time() + $windowMinutes * 60);
        return $this->db->fetchAll(
            "SELECT v.*, t.title AS trip_title
             FROM videocall_bookings v
             LEFT JOIN trips t ON v.trip_id = t.id
             WHERE v.reminder_sent = 0
               AND v.status IN ('pending','confirmed')
               AND v.scheduled_at BETWEEN ? AND ?
             ORDER BY v.scheduled_at ASC",
            [$now, $limit]
        );
    }

    public function markReminderSent(int $id): void
    {
        $this->db->update($this->table, ['reminder_sent' => 1], 'id = ?', [$id]);
    }

    public function setStatus(int $id, string $status): void
    {
        $this->db->update($this->table, ['status' => $status], 'id = ?', [$id]);
    }
}
