<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Coupon extends Model
{
    protected string $table = 'coupons';
    protected array $fillable = [
        'code', 'description', 'type', 'value', 'affiliate_id',
        'min_order', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'active',
    ];

    /**
     * Busca um cupom pelo código (case-insensitive).
     */
    public function findByCode(string $code): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM coupons WHERE UPPER(code) = UPPER(?) LIMIT 1",
            [trim($code)]
        );
    }

    /**
     * Incrementa o contador de usos.
     */
    public function incrementUsage(int $id): void
    {
        $this->db->query("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$id]);
    }

    /**
     * Verifica se um código já existe (para validação no admin).
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT id FROM coupons WHERE UPPER(code) = UPPER(?)";
        $params = [trim($code)];
        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        return (bool) $this->db->fetchColumn($sql . " LIMIT 1", $params);
    }

    /**
     * Lista os cupons com o nome do afiliado vinculado (para o admin).
     */
    public function getAllWithAffiliate(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, u.first_name, u.last_name
             FROM coupons c
             LEFT JOIN affiliates a ON c.affiliate_id = a.id
             LEFT JOIN users u ON a.user_id = u.id
             ORDER BY c.created_at DESC"
        );
    }
}
