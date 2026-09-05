<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Agency extends Model
{
    protected string $table = 'agencies';
    protected array $fillable = [
        'company_name', 'trade_name', 'cnpj', 'contact_name', 'email', 'phone',
        'address', 'city', 'country', 'bank_info', 'ref_code', 'commission_rate',
        'status', 'total_sales', 'total_commission', 'total_paid', 'notes',
    ];

    public function findByRefCode(string $refCode): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM agencies WHERE ref_code = ? LIMIT 1",
            [trim($refCode)]
        );
    }

    /**
     * Lista paginada, com busca opcional por nome/CNPJ/código.
     */
    public function getAllPaginated(int $page = 1, int $perPage = 20, string $search = '', ?string $status = null): array
    {
        $where = '1=1';
        $params = [];

        if ($search !== '') {
            $where .= " AND (company_name LIKE ? OR trade_name LIKE ? OR cnpj LIKE ? OR ref_code LIKE ?)";
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if ($status !== null && $status !== '') {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM agencies WHERE {$where}", $params);
        $offset = ($page - 1) * $perPage;

        $items = $this->db->fetchAll(
            "SELECT * FROM agencies WHERE {$where} ORDER BY company_name ASC LIMIT " . (int) $perPage . " OFFSET " . (int) $offset,
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
     * Gera um código de indicação único (ex: AG-XXXXXX).
     */
    public function generateRefCode(): string
    {
        do {
            $code = 'AG-' . strtoupper(bin2hex(random_bytes(3)));
        } while ($this->db->fetchColumn("SELECT id FROM agencies WHERE ref_code = ? LIMIT 1", [$code]));
        return $code;
    }

    public function refCodeExists(string $refCode, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM agencies WHERE ref_code = ?";
        $params = [trim($refCode)];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        return (bool) $this->db->fetchColumn($sql . " LIMIT 1", $params);
    }

    /**
     * Atualiza os agregados após uma venda.
     */
    public function updateStats(int $id, float $saleAmount, float $commission): void
    {
        $this->db->query(
            "UPDATE agencies SET
                total_sales = total_sales + ?,
                total_commission = total_commission + ?
             WHERE id = ?",
            [$saleAmount, $commission, $id]
        );
    }
}
