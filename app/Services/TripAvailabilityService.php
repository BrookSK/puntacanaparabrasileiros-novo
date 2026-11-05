<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;

/**
 * Regras de funcionamento de cada passeio:
 *  - dias da semana fechados (trips.closed_weekdays);
 *  - exceções por data/período (trip_date_exceptions: closed/open);
 *  - horários de saída por dia da semana (trip_departure_times).
 *
 * Usado pelo site (calendário/horários do modal de reserva) e no servidor
 * (carrinho e checkout) para impedir reservas em datas/horários indisponíveis.
 * Se a migration 036 ainda não rodou, tudo funciona como antes (sem bloqueios).
 */
class TripAvailabilityService
{
    private Database $db;
    private static array $tableCache = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function isReady(): bool
    {
        return $this->tableExists('trip_departure_times') && $this->tableExists('trip_date_exceptions');
    }

    /** Dias da semana fechados do passeio (inteiros 0-6). */
    public function getClosedWeekdays(int $tripId): array
    {
        try {
            $csv = (string) ($this->db->fetchColumn("SELECT closed_weekdays FROM trips WHERE id = ?", [$tripId]) ?? '');
        } catch (\Throwable $e) {
            return [];
        }
        return self::parseWeekdays($csv);
    }

    /** Horários de saída cadastrados (ativos), ordenados. */
    public function getDepartureTimes(int $tripId): array
    {
        if (!$this->tableExists('trip_departure_times')) return [];
        $rows = $this->db->fetchAll(
            "SELECT * FROM trip_departure_times WHERE trip_id = ? AND is_active = 1 ORDER BY departure_time ASC, sort_order ASC, id ASC",
            [$tripId]
        );
        foreach ($rows as &$r) {
            $r['time'] = substr((string) $r['departure_time'], 0, 5);
            $r['weekday_list'] = self::parseWeekdays((string) $r['weekdays']);
        }
        return $rows;
    }

    /** Exceções futuras (e em andamento) do passeio. */
    public function getExceptions(int $tripId, bool $onlyFuture = true): array
    {
        if (!$this->tableExists('trip_date_exceptions')) return [];
        $sql = "SELECT * FROM trip_date_exceptions WHERE trip_id = ?";
        if ($onlyFuture) $sql .= " AND date_end >= CURDATE()";
        $sql .= " ORDER BY date_start ASC";
        return $this->db->fetchAll($sql, [$tripId]);
    }

    /**
     * A data está aberta para venda?
     * Ordem: exceção "fechado" > exceção "aberto" > dia da semana fechado > aberto.
     */
    public function isDateOpen(int $tripId, string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$d) return false;
        $ymd = $d->format('Y-m-d');

        $hasOpen = false;
        foreach ($this->getExceptions($tripId, false) as $ex) {
            if ($ymd < $ex['date_start'] || $ymd > $ex['date_end']) continue;
            if ($ex['exception_type'] === 'closed') return false;
            if ($ex['exception_type'] === 'open') $hasOpen = true;
        }
        if ($hasOpen) return true;

        return !in_array((int) $d->format('w'), $this->getClosedWeekdays($tripId), true);
    }

    /** Horários de saída válidos para a data (HH:MM => label). */
    public function getDepartureTimesForDate(int $tripId, string $date): array
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$d) return [];
        $w = (int) $d->format('w');
        $out = [];
        foreach ($this->getDepartureTimes($tripId) as $t) {
            if (in_array($w, $t['weekday_list'], true)) {
                $out[] = ['id' => (int) $t['id'], 'time' => $t['time'], 'label' => (string) ($t['label'] ?? '')];
            }
        }
        return $out;
    }

    /**
     * Valida data/horário para uma reserva. Retorna null se ok, ou a mensagem de erro.
     */
    public function validate(int $tripId, string $date, ?string $time): ?string
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$d) return 'Data inválida.';
        if ($d->format('Y-m-d') < date('Y-m-d')) return 'Não é possível reservar uma data que já passou.';

        if (!$this->isDateOpen($tripId, $date)) {
            return 'Este passeio não está disponível em ' . $d->format('d/m/Y') . '. Escolha outra data.';
        }

        $allTimes = $this->getDepartureTimes($tripId);
        if (empty($allTimes)) return null; // passeio sem horários cadastrados: só a data

        $dayTimes = $this->getDepartureTimesForDate($tripId, $date);
        if (empty($dayTimes)) {
            return 'Não há horários de saída para este passeio em ' . $d->format('d/m/Y') . '. Escolha outra data.';
        }

        $time = self::normalizeTime($time);
        if (count($dayTimes) === 1 && $time === null) return null; // único horário: escolhido automaticamente
        if ($time === null) return 'Selecione o horário do passeio.';

        foreach ($dayTimes as $t) {
            if ($t['time'] === $time) return null;
        }
        return 'O horário ' . $time . ' não está disponível em ' . $d->format('d/m/Y') . '. Escolha outro horário.';
    }

    /** Dados para o calendário/horários do modal de reserva (JSON no front). */
    public function getFrontendConfig(int $tripId): array
    {
        $exceptions = array_map(static fn($ex) => [
            'type' => $ex['exception_type'],
            'start' => $ex['date_start'],
            'end' => $ex['date_end'],
        ], $this->getExceptions($tripId, true));

        $times = array_map(static fn($t) => [
            'id' => (int) $t['id'],
            'time' => $t['time'],
            'label' => (string) ($t['label'] ?? ''),
            'weekdays' => $t['weekday_list'],
        ], $this->getDepartureTimes($tripId));

        return [
            'closed_weekdays' => $this->getClosedWeekdays($tripId),
            'exceptions' => $exceptions,
            'departure_times' => $times,
        ];
    }

    public static function parseWeekdays(string $csv): array
    {
        $out = [];
        foreach (explode(',', $csv) as $p) {
            $p = trim($p);
            if ($p !== '' && ctype_digit($p) && (int) $p >= 0 && (int) $p <= 6) $out[] = (int) $p;
        }
        $out = array_values(array_unique($out));
        sort($out);
        return $out;
    }

    public static function normalizeTime(?string $value): ?string
    {
        $value = trim((string) ($value ?? ''));
        if ($value === '') return null;
        if (preg_match('/^(\d{1,2}):(\d{2})/', $value, $m)) {
            return sprintf('%02d:%s', (int) $m[1], $m[2]);
        }
        return null;
    }

    private function tableExists(string $table): bool
    {
        if (array_key_exists($table, self::$tableCache)) return self::$tableCache[$table];
        try {
            $this->db->fetchColumn("SELECT 1 FROM `{$table}` LIMIT 1");
            return self::$tableCache[$table] = true;
        } catch (\Throwable $e) {
            return self::$tableCache[$table] = false;
        }
    }
}
