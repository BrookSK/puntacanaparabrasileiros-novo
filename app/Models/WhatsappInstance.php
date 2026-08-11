<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para instâncias WhatsApp (conexões via Evolution API).
 */
class WhatsappInstance extends Model
{
    protected string $table = 'whatsapp_instances';
    protected array $fillable = [
        'instance_name', 'display_name', 'api_url', 'api_key',
        'owner_phone', 'user_id', 'connection_status', 'qr_code', 'is_default',
    ];

    /**
     * Busca a instância padrão.
     */
    public function getDefault(): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE is_default = 1 LIMIT 1"
        );
    }

    /**
     * Define uma instância como padrão (remove padrão das outras).
     */
    public function setAsDefault(int $id): void
    {
        $this->db->query("UPDATE {$this->table} SET is_default = 0 WHERE is_default = 1");
        $this->db->update($this->table, ['is_default' => 1], 'id = ?', [$id]);
    }

    /**
     * Busca instância do usuário (lógica de getUserInstance).
     * 1. Vinculada ao usuário
     * 2. Padrão sem vínculo
     * 3. Qualquer sem vínculo
     */
    public function getUserInstance(int $userId): ?array
    {
        // 1. Vinculada ao usuário
        $instance = $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE user_id = ? AND connection_status = 'open' LIMIT 1",
            [$userId]
        );
        if ($instance) return $instance;

        // 2. Padrão sem vínculo
        $instance = $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE is_default = 1 AND user_id IS NULL LIMIT 1"
        );
        if ($instance) return $instance;

        // 3. Qualquer sem vínculo
        $instance = $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE user_id IS NULL LIMIT 1"
        );

        return $instance;
    }

    /**
     * Busca todas as instâncias com dados do usuário vinculado.
     */
    public function allWithUser(): array
    {
        return $this->db->fetchAll(
            "SELECT wi.*, u.first_name as user_name, u.last_name as user_last_name
             FROM {$this->table} wi
             LEFT JOIN users u ON u.id = wi.user_id
             ORDER BY wi.is_default DESC, wi.created_at ASC"
        );
    }

    /**
     * Atualiza o status de conexão.
     */
    public function updateStatus(int $id, string $status, ?string $qrCode = null): void
    {
        $data = ['connection_status' => $status];
        if ($qrCode !== null) {
            $data['qr_code'] = $qrCode;
        }
        if ($status === 'open') {
            $data['qr_code'] = null; // Limpa QR ao conectar
        }
        $this->db->update($this->table, $data, 'id = ?', [$id]);
    }
}
