<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class CancellationRule extends Model
{
    protected string $table = 'cancellation_rules';
    protected array $fillable = [
        'label', 'time_unit', 'time_value', 'refund_type', 'refund_value', 'sort_order', 'active',
    ];

    /**
     * Todas as regras (para o admin), ordenadas.
     */
    public function getAllOrdered(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM cancellation_rules ORDER BY sort_order ASC, id ASC"
        );
    }

    /**
     * Regras ativas, já convertidas para horas de antecedência, ordenadas da MAIOR
     * antecedência para a menor (para escolher a melhor faixa que o cliente atende).
     */
    public function getActiveByHoursDesc(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM cancellation_rules WHERE active = 1"
        );
        foreach ($rows as &$r) {
            $r['threshold_hours'] = $r['time_unit'] === 'days'
                ? ((int) $r['time_value']) * 24
                : (int) $r['time_value'];
        }
        unset($r);

        usort($rows, static fn($a, $b) => $b['threshold_hours'] <=> $a['threshold_hours']);
        return $rows;
    }
}
