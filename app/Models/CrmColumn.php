<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para colunas do kanban CRM.
 */
class CrmColumn extends Model
{
    protected string $table = 'crm_columns';
    protected array $fillable = ['board_id', 'name', 'color', 'label_id', 'status', 'position'];

    /**
     * Lista colunas de um board.
     */
    public function getByBoard(int $boardId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE board_id = ? ORDER BY position ASC",
            [$boardId]
        );
    }

    /**
     * Próxima posição disponível.
     */
    public function nextPosition(int $boardId): int
    {
        $max = $this->db->fetchColumn(
            "SELECT COALESCE(MAX(position), -1) FROM {$this->table} WHERE board_id = ?",
            [$boardId]
        );
        return (int) $max + 1;
    }

    /**
     * Primeira coluna do board (fallback para follow-ups).
     */
    public function getFirst(int $boardId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE board_id = ? ORDER BY position ASC LIMIT 1",
            [$boardId]
        );
    }
}
