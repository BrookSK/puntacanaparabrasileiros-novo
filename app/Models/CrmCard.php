<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para cards (leads/oportunidades) do CRM.
 */
class CrmCard extends Model
{
    protected string $table = 'crm_cards';
    protected array $fillable = [
        'column_id', 'contact_id', 'title', 'description', 'phone',
        'value', 'label_id', 'status', 'lead_outcome', 'outcome_at',
        'converted_by', 'follow_up_at', 'follow_up_column_id',
        'in_recovery', 'position', 'assigned_to', 'created_by',
    ];

    /**
     * Busca card com detalhes completos.
     */
    public function findWithDetails(int $id): ?array
    {
        $card = $this->db->fetchOne(
            "SELECT cc.*, 
                col.name as column_name, col.color as column_color,
                b.id as board_id, b.name as board_name,
                u.first_name as assigned_name,
                cu.first_name as creator_name,
                conv.first_name as converter_name,
                wc.contact_name, wc.phone as contact_phone, wc.remote_jid,
                wl.name as label_name, wl.color as label_color,
                cb.lead_temperature, cb.investment_range,
                cb.urgency, cb.decision_level
             FROM {$this->table} cc
             INNER JOIN crm_columns col ON col.id = cc.column_id
             INNER JOIN crm_boards b ON b.id = col.board_id
             LEFT JOIN users u ON u.id = cc.assigned_to
             LEFT JOIN users cu ON cu.id = cc.created_by
             LEFT JOIN users conv ON conv.id = cc.converted_by
             LEFT JOIN whatsapp_contacts wc ON wc.id = cc.contact_id
             LEFT JOIN whatsapp_labels wl ON wl.id = cc.label_id
             LEFT JOIN commercial_briefings cb ON cb.contact_id = cc.contact_id
             WHERE cc.id = ?",
            [$id]
        );

        if (!$card) return null;

        // Atividades
        $card['activities'] = $this->db->fetchAll(
            "SELECT a.*, u.first_name as user_name
             FROM crm_card_activities a
             LEFT JOIN users u ON u.id = a.user_id
             WHERE a.card_id = ?
             ORDER BY a.created_at DESC",
            [$id]
        );

        return $card;
    }

    /**
     * Move card para outra coluna.
     */
    public function moveToColumn(int $cardId, int $columnId, int $position = 0): void
    {
        $this->db->update($this->table, [
            'column_id' => $columnId,
            'position' => $position,
        ], 'id = ?', [$cardId]);
    }

    /**
     * Converte lead (marca como converted).
     */
    public function convertLead(int $id, int $userId): void
    {
        $this->db->update($this->table, [
            'lead_outcome' => 'converted',
            'outcome_at' => date('Y-m-d H:i:s'),
            'converted_by' => $userId,
        ], 'id = ?', [$id]);

        $this->addActivity($id, $userId, 'convert', 'Lead convertido');
    }

    /**
     * Marca lead como perdido.
     */
    public function markAsLost(int $id, int $userId): void
    {
        $this->db->update($this->table, [
            'lead_outcome' => 'lost',
            'outcome_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        $this->addActivity($id, $userId, 'lost', 'Lead perdido');
    }

    /**
     * Agenda follow-up.
     */
    public function setFollowUp(int $id, string $followUpAt, ?int $columnId, int $userId): void
    {
        $this->db->update($this->table, [
            'follow_up_at' => $followUpAt,
            'follow_up_column_id' => $columnId,
        ], 'id = ?', [$id]);

        $dateFormatted = date('d/m/Y H:i', strtotime($followUpAt));
        $this->addActivity($id, $userId, 'followup', "Retomada agendada para {$dateFormatted}");
    }

    /**
     * Processa follow-ups vencidos (move cards para coluna alvo).
     */
    public function processFollowUps(): int
    {
        $cards = $this->db->fetchAll(
            "SELECT cc.*, col.board_id
             FROM {$this->table} cc
             INNER JOIN crm_columns col ON col.id = cc.column_id
             WHERE cc.follow_up_at <= NOW() 
             AND cc.follow_up_at IS NOT NULL
             AND cc.lead_outcome = 'open'"
        );

        $count = 0;
        foreach ($cards as $card) {
            $targetColumnId = $card['follow_up_column_id'];

            // Fallback: primeira coluna do board
            if (!$targetColumnId) {
                $firstCol = $this->db->fetchOne(
                    "SELECT id FROM crm_columns WHERE board_id = ? ORDER BY position ASC LIMIT 1",
                    [(int) $card['board_id']]
                );
                $targetColumnId = $firstCol ? (int) $firstCol['id'] : (int) $card['column_id'];
            }

            $this->db->update($this->table, [
                'column_id' => $targetColumnId,
                'in_recovery' => 1,
                'follow_up_at' => null,
                'follow_up_column_id' => null,
            ], 'id = ?', [(int) $card['id']]);

            $colName = $this->db->fetchColumn(
                "SELECT name FROM crm_columns WHERE id = ?",
                [$targetColumnId]
            );
            $this->addActivity((int) $card['id'], null, 'move',
                "Retomada automática — movido para {$colName} (Em recuperação)"
            );
            $count++;
        }

        return $count;
    }

    /**
     * Adiciona atividade ao card.
     */
    public function addActivity(int $cardId, ?int $userId, string $type, string $description): void
    {
        $this->db->insert('crm_card_activities', [
            'card_id' => $cardId,
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
        ]);
    }

    /**
     * Dashboard: contadores de leads.
     */
    public function getDashboardStats(): array
    {
        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table}");
        $withLabel = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE label_id IS NOT NULL");
        $open = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE lead_outcome = 'open'");
        $converted = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE lead_outcome = 'converted'");
        $lost = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM {$this->table} WHERE lead_outcome = 'lost'");

        $valueTotal = (float) $this->db->fetchColumn("SELECT COALESCE(SUM(value), 0) FROM {$this->table}");
        $valueConverted = (float) $this->db->fetchColumn("SELECT COALESCE(SUM(value), 0) FROM {$this->table} WHERE lead_outcome = 'converted'");
        $valueLost = (float) $this->db->fetchColumn("SELECT COALESCE(SUM(value), 0) FROM {$this->table} WHERE lead_outcome = 'lost'");
        $valueRecovery = (float) $this->db->fetchColumn("SELECT COALESCE(SUM(value), 0) FROM {$this->table} WHERE in_recovery = 1 OR follow_up_at IS NOT NULL");
        $ticketMedio = $converted > 0 ? $valueConverted / $converted : 0;

        return compact('total', 'withLabel', 'open', 'converted', 'lost',
            'valueTotal', 'valueConverted', 'valueLost', 'valueRecovery', 'ticketMedio');
    }

    /**
     * Dashboard: evolução dos últimos 6 meses.
     */
    public function getMonthlyEvolution(): array
    {
        return $this->db->fetchAll(
            "SELECT 
                DATE_FORMAT(outcome_at, '%Y-%m') as month,
                SUM(CASE WHEN lead_outcome = 'converted' THEN 1 ELSE 0 END) as converted,
                SUM(CASE WHEN lead_outcome = 'lost' THEN 1 ELSE 0 END) as lost
             FROM {$this->table}
             WHERE outcome_at IS NOT NULL
             AND outcome_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(outcome_at, '%Y-%m')
             ORDER BY month ASC"
        );
    }

    /**
     * Comissões: leads convertidos por usuário comercial.
     */
    public function getCommissions(?int $userId = null, ?string $month = null): array
    {
        $where = "cc.lead_outcome = 'converted'";
        $params = [];

        if ($userId) {
            $where .= " AND cc.converted_by = ?";
            $params[] = $userId;
        }
        if ($month) {
            $where .= " AND DATE_FORMAT(cc.outcome_at, '%Y-%m') = ?";
            $params[] = $month;
        }

        return $this->db->fetchAll(
            "SELECT u.id as user_id, u.first_name, u.last_name, u.commission_percent,
                COUNT(cc.id) as leads_count,
                COALESCE(SUM(cc.value), 0) as total_value,
                COALESCE(SUM(cc.value), 0) * u.commission_percent / 100 as commission_value
             FROM users u
             INNER JOIN {$this->table} cc ON cc.converted_by = u.id
             WHERE u.role = 'comercial' AND {$where}
             GROUP BY u.id
             ORDER BY commission_value DESC",
            $params
        );
    }
}
