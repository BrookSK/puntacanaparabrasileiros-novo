<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para boards (CRMs) do módulo CRM.
 */
class CrmBoard extends Model
{
    protected string $table = 'crm_boards';
    protected array $fillable = ['name', 'description', 'created_by', 'is_active'];

    /**
     * Lista boards ativos com contagem de cards.
     */
    public function listActive(): array
    {
        return $this->db->fetchAll(
            "SELECT b.*, 
                u.first_name as creator_name,
                (SELECT COUNT(*) FROM crm_cards cc 
                 INNER JOIN crm_columns col ON col.id = cc.column_id 
                 WHERE col.board_id = b.id) as card_count
             FROM {$this->table} b
             LEFT JOIN users u ON u.id = b.created_by
             WHERE b.is_active = 1
             ORDER BY b.created_at DESC"
        );
    }

    /**
     * Busca board com colunas e cards.
     */
    public function findWithColumns(int $id): ?array
    {
        $board = $this->find($id);
        if (!$board || !$board['is_active']) return null;

        $board['columns'] = $this->db->fetchAll(
            "SELECT * FROM crm_columns WHERE board_id = ? ORDER BY position ASC",
            [$id]
        );

        // Cards por coluna
        foreach ($board['columns'] as &$column) {
            $column['cards'] = $this->db->fetchAll(
                "SELECT cc.*, 
                    u.first_name as assigned_name,
                    wc.contact_name, wc.phone as contact_phone,
                    wl.name as label_name, wl.color as label_color,
                    cb.lead_temperature
                 FROM crm_cards cc
                 LEFT JOIN users u ON u.id = cc.assigned_to
                 LEFT JOIN whatsapp_contacts wc ON wc.id = cc.contact_id
                 LEFT JOIN whatsapp_labels wl ON wl.id = cc.label_id
                 LEFT JOIN commercial_briefings cb ON cb.contact_id = cc.contact_id
                 WHERE cc.column_id = ?
                 ORDER BY cc.position ASC",
                [(int) $column['id']]
            );
        }

        return $board;
    }

    /**
     * Cria board com colunas padrão.
     */
    public function createWithDefaults(array $data): int
    {
        $boardId = $this->create($data);

        $defaultColumns = [
            ['name' => 'Novo Lead', 'color' => '#1565c0', 'position' => 0],
            ['name' => 'Contato Feito', 'color' => '#e65100', 'position' => 1],
            ['name' => 'Em Negociação', 'color' => '#7b1fa2', 'position' => 2],
            ['name' => 'Fechado', 'color' => '#2e7d32', 'position' => 3],
            ['name' => 'Perdido', 'color' => '#c62828', 'position' => 4],
        ];

        foreach ($defaultColumns as $col) {
            $this->db->insert('crm_columns', array_merge($col, ['board_id' => $boardId]));
        }

        return $boardId;
    }

    /**
     * Soft-delete do board.
     */
    public function softDelete(int $id): void
    {
        $this->db->update($this->table, ['is_active' => 0], 'id = ?', [$id]);
    }

    /**
     * Lista todos os boards com suas colunas (para dropdowns).
     */
    public function listWithColumns(): array
    {
        $boards = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name ASC"
        );

        foreach ($boards as &$board) {
            $board['columns'] = $this->db->fetchAll(
                "SELECT id, name, color FROM crm_columns WHERE board_id = ? ORDER BY position ASC",
                [(int) $board['id']]
            );
        }

        return $boards;
    }
}
