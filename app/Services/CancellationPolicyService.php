<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use App\Models\CancellationRule;

/**
 * Aplica as regras de cancelamento configuradas pelo admin para calcular
 * o reembolso sugerido de uma reserva, com base na antecedência (dias/horas)
 * em relação à data da viagem.
 */
class CancellationPolicyService
{
    private Database $db;
    private CancellationRule $ruleModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ruleModel = new CancellationRule();
    }

    /**
     * Data/hora mais próxima da viagem dentro de um booking (passeio ou transfer).
     * Retorna 'Y-m-d H:i:s' ou null se não houver.
     */
    public function getEarliestTravelDateTime(int $bookingId): ?string
    {
        // Passeios: booking_items.trip_date (+ trip_time se houver)
        $tripDate = $this->db->fetchColumn(
            "SELECT MIN(
                        CASE
                            WHEN trip_time IS NOT NULL AND trip_time <> '00:00:00'
                            THEN TIMESTAMP(trip_date, trip_time)
                            ELSE TIMESTAMP(trip_date, '00:00:00')
                        END
                    )
             FROM booking_items
             WHERE booking_id = ? AND trip_date IS NOT NULL",
            [$bookingId]
        );

        // Transfers: transfer_bookings.date (+ time)
        $transferDate = $this->db->fetchColumn(
            "SELECT MIN(
                        CASE
                            WHEN time IS NOT NULL AND time <> '00:00:00'
                            THEN TIMESTAMP(date, time)
                            ELSE TIMESTAMP(date, '00:00:00')
                        END
                    )
             FROM transfer_bookings
             WHERE booking_id = ? AND date IS NOT NULL",
            [$bookingId]
        );

        $candidates = array_filter([$tripDate ?: null, $transferDate ?: null]);
        if (empty($candidates)) {
            return null;
        }
        // Menor data (mais próxima)
        usort($candidates, static fn($a, $b) => strtotime($a) <=> strtotime($b));
        return $candidates[0];
    }

    /**
     * Calcula o reembolso sugerido para um booking.
     *
     * @return array{
     *   has_rules: bool,
     *   travel_at: string|null,
     *   hours_until: float|null,
     *   rule: array|null,
     *   refund_amount: float,
     *   refund_percentage: float|null,
     *   label: string
     * }
     */
    public function evaluate(int $bookingId, float $paidAmount): array
    {
        $result = [
            'has_rules' => false,
            'travel_at' => null,
            'hours_until' => null,
            'rule' => null,
            'refund_amount' => 0.0,
            'refund_percentage' => null,
            'label' => 'Sem regra aplicável',
        ];

        $rules = $this->ruleModel->getActiveByHoursDesc();
        $result['has_rules'] = !empty($rules);

        $travelAt = $this->getEarliestTravelDateTime($bookingId);
        $result['travel_at'] = $travelAt;

        // Sem regras cadastradas ou sem data de viagem: não há sugestão automática.
        if (empty($rules) || !$travelAt) {
            return $result;
        }

        $hoursUntil = (strtotime($travelAt) - time()) / 3600;
        $result['hours_until'] = round($hoursUntil, 1);

        // Escolhe a faixa de MAIOR antecedência que o cliente ainda atende.
        // (rules já vem ordenado da maior para a menor antecedência)
        $applied = null;
        foreach ($rules as $rule) {
            if ($hoursUntil >= (float) $rule['threshold_hours']) {
                $applied = $rule;
                break;
            }
        }

        // Nenhuma faixa atendida = cancelamento muito próximo/ausência = sem reembolso.
        if (!$applied) {
            $result['refund_amount'] = 0.0;
            $result['refund_percentage'] = 0.0;
            $result['label'] = 'Fora do prazo das regras — sem reembolso';
            return $result;
        }

        // Calcula o valor conforme o tipo
        if ($applied['refund_type'] === 'percentage') {
            $percent = (float) $applied['refund_value'];
            $percent = max(0, min(100, $percent));
            $amount = round($paidAmount * ($percent / 100), 2);
            $result['refund_percentage'] = $percent;
        } else {
            $amount = min((float) $applied['refund_value'], $paidAmount);
            $amount = round(max(0, $amount), 2);
            $result['refund_percentage'] = $paidAmount > 0 ? round(($amount / $paidAmount) * 100, 2) : null;
        }

        $result['rule'] = $applied;
        $result['refund_amount'] = $amount;
        $result['label'] = $applied['label'] ?: $this->describeRule($applied);
        return $result;
    }

    private function describeRule(array $rule): string
    {
        $unit = $rule['time_unit'] === 'days' ? 'dia(s)' : 'hora(s)';
        $val = (int) $rule['time_value'];
        if ($rule['refund_type'] === 'percentage') {
            $refund = rtrim(rtrim(number_format((float) $rule['refund_value'], 2), '0'), '.') . '%';
        } else {
            $refund = money((float) $rule['refund_value']);
        }
        return "A partir de {$val} {$unit} antes: {$refund}";
    }
}
