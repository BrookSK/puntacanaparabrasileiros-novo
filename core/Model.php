<?php
declare(strict_types=1);

namespace Core;

/**
 * Model base — fornece métodos CRUD genéricos.
 * Cada model define sua tabela e campos fillable.
 */
abstract class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca um registro pelo ID.
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /**
     * Busca um registro por condição.
     */
    public function findWhere(string $column, mixed $value): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1",
            [$value]
        );
    }

    /**
     * Busca todos os registros (com limit/offset opcionais).
     */
    public function all(string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY {$orderBy}";
        $params = [];

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params = [$limit, $offset];
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Busca registros com condição WHERE.
     */
    public function where(string $where, array $params = [], string $orderBy = 'id DESC', ?int $limit = null, int $offset = 0): array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE {$where} ORDER BY {$orderBy}";

        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Conta registros com condição.
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        return $this->db->count($this->table, $where, $params);
    }

    /**
     * Cria um novo registro. Filtra apenas campos fillable.
     */
    public function create(array $data): int
    {
        $filtered = $this->filterFillable($data);
        return $this->db->insert($this->table, $filtered);
    }

    /**
     * Atualiza um registro pelo ID. Filtra apenas campos fillable.
     */
    public function update(int $id, array $data): int
    {
        $filtered = $this->filterFillable($data);
        return $this->db->update(
            $this->table,
            $filtered,
            "`{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /**
     * Deleta um registro pelo ID.
     */
    public function delete(int $id): int
    {
        return $this->db->delete(
            $this->table,
            "`{$this->primaryKey}` = ?",
            [$id]
        );
    }

    /**
     * Verifica se um registro existe.
     */
    public function exists(string $column, mixed $value, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `{$column}` = ?";
        $params = [$value];

        if ($excludeId !== null) {
            $sql .= " AND `{$this->primaryKey}` != ?";
            $params[] = $excludeId;
        }

        return (int) $this->db->fetchColumn($sql, $params) > 0;
    }

    /**
     * Paginação simples.
     */
    public function paginate(int $page = 1, int $perPage = 15, string $where = '1=1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $total = $this->count($where, $params);
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $items = $this->where($where, $params, $orderBy, $perPage, $offset);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ];
    }

    /**
     * Filtra dados mantendo apenas campos fillable.
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Retorna o nome da tabela.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
