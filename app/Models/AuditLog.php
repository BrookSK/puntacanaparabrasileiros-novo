<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para gerenciamento de logs de auditoria.
 * Registra ações administrativas importantes do sistema.
 */
class AuditLog extends Model
{
    protected string $table = 'audit_logs';
    protected array $fillable = [
        'user_id', 'user_name', 'user_email', 'action', 'entity_type',
        'entity_id', 'entity_name', 'description', 'changes', 'result',
        'ip_address', 'user_agent',
    ];

    /**
     * Ações disponíveis para registro.
     */
    public const ACTIONS = [
        'create' => 'Criação',
        'update' => 'Atualização',
        'delete' => 'Exclusão',
        'login' => 'Login',
        'login_failed' => 'Tentativa de Login',
        'logout' => 'Logout',
        'password_reset' => 'Reset de Senha',
        'status_change' => 'Alteração de Status',
        'approve' => 'Aprovação',
        'reject' => 'Rejeição',
        'payment' => 'Pagamento',
        'refund' => 'Reembolso',
        'settings_update' => 'Configurações',
        'export' => 'Exportação',
        'import' => 'Importação',
        'send_email' => 'Envio de Email',
        'send_voucher' => 'Envio de Voucher',
        'impersonate' => 'Personificação',
        'cache_clear' => 'Limpeza de Cache',
    ];

    /**
     * Tipos de entidades do sistema.
     */
    public const ENTITY_TYPES = [
        'user' => 'Usuário',
        'booking' => 'Reserva',
        'trip' => 'Passeio',
        'category' => 'Categoria',
        'transfer' => 'Transfer',
        'transfer_vehicle' => 'Veículo de Transfer',
        'transfer_location' => 'Local de Transfer',
        'coupon' => 'Cupom',
        'voucher' => 'Voucher',
        'affiliate' => 'Afiliado',
        'agency' => 'Agência',
        'setting' => 'Configuração',
        'newsletter' => 'Newsletter',
        'cancellation' => 'Cancelamento',
        'schedule' => 'Horário',
        'payment' => 'Pagamento',
        'system' => 'Sistema',
    ];

    /**
     * Resultados possíveis da ação.
     */
    public const RESULTS = [
        'success' => 'Sucesso',
        'failure' => 'Falha',
        'warning' => 'Aviso',
    ];

    /**
     * Lista paginada de logs com filtros.
     */
    public function listFiltered(
        int $page = 1,
        int $perPage = 50,
        ?int $userId = null,
        ?string $action = null,
        ?string $entityType = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $search = null
    ): array {
        $where = '1=1';
        $params = [];

        if ($userId !== null) {
            $where .= ' AND user_id = ?';
            $params[] = $userId;
        }

        if ($action !== null && $action !== '') {
            $where .= ' AND action = ?';
            $params[] = $action;
        }

        if ($entityType !== null && $entityType !== '') {
            $where .= ' AND entity_type = ?';
            $params[] = $entityType;
        }

        if ($dateFrom !== null && $dateFrom !== '') {
            $where .= ' AND DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }

        if ($dateTo !== null && $dateTo !== '') {
            $where .= ' AND DATE(created_at) <= ?';
            $params[] = $dateTo;
        }

        if ($search !== null && $search !== '') {
            $searchTerm = '%' . $search . '%';
            $where .= ' AND (user_name LIKE ? OR user_email LIKE ? OR entity_name LIKE ? OR description LIKE ?)';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        return $this->paginate($page, $perPage, $where, $params, 'created_at DESC');
    }

    /**
     * Obtém lista de usuários distintos que têm logs (para filtro).
     */
    public function getDistinctUsers(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT user_id, user_name, user_email 
             FROM {$this->table} 
             WHERE user_id IS NOT NULL 
             ORDER BY user_name ASC"
        );
    }

    /**
     * Obtém lista de ações distintas registradas (para filtro).
     */
    public function getDistinctActions(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT action FROM {$this->table} ORDER BY action ASC"
        );
    }

    /**
     * Obtém lista de tipos de entidade distintos (para filtro).
     */
    public function getDistinctEntityTypes(): array
    {
        return $this->db->fetchAll(
            "SELECT DISTINCT entity_type FROM {$this->table} WHERE entity_type IS NOT NULL ORDER BY entity_type ASC"
        );
    }

    /**
     * Obtém logs recentes de um usuário específico.
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->where('user_id = ?', [$userId], 'created_at DESC', $limit);
    }

    /**
     * Obtém logs de uma entidade específica.
     */
    public function getByEntity(string $entityType, int $entityId, int $limit = 50): array
    {
        return $this->where(
            'entity_type = ? AND entity_id = ?',
            [$entityType, $entityId],
            'created_at DESC',
            $limit
        );
    }

    /**
     * Obtém estatísticas de logs por período.
     */
    public function getStats(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $where = '1=1';
        $params = [];

        if ($dateFrom !== null && $dateFrom !== '') {
            $where .= ' AND DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }

        if ($dateTo !== null && $dateTo !== '') {
            $where .= ' AND DATE(created_at) <= ?';
            $params[] = $dateTo;
        }

        $total = $this->count($where, $params);

        $byAction = $this->db->fetchAll(
            "SELECT action, COUNT(*) as count FROM {$this->table} WHERE {$where} GROUP BY action ORDER BY count DESC",
            $params
        );

        $byResult = $this->db->fetchAll(
            "SELECT result, COUNT(*) as count FROM {$this->table} WHERE {$where} GROUP BY result ORDER BY count DESC",
            $params
        );

        $byUser = $this->db->fetchAll(
            "SELECT user_id, user_name, COUNT(*) as count FROM {$this->table} WHERE {$where} AND user_id IS NOT NULL GROUP BY user_id, user_name ORDER BY count DESC LIMIT 10",
            $params
        );

        return [
            'total' => $total,
            'by_action' => $byAction,
            'by_result' => $byResult,
            'by_user' => $byUser,
        ];
    }

    /**
     * Limpa logs antigos (para manutenção, se necessário).
     */
    public function cleanOldLogs(int $daysToKeep = 365): int
    {
        $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        return $this->db->delete($this->table, 'DATE(created_at) < ?', [$cutoffDate]);
    }

    /**
     * Traduz o nome da ação para português.
     */
    public static function translateAction(string $action): string
    {
        return self::ACTIONS[$action] ?? ucfirst($action);
    }

    /**
     * Traduz o tipo de entidade para português.
     */
    public static function translateEntityType(?string $entityType): string
    {
        if ($entityType === null) return '-';
        return self::ENTITY_TYPES[$entityType] ?? ucfirst($entityType);
    }

    /**
     * Traduz o resultado para português.
     */
    public static function translateResult(string $result): string
    {
        return self::RESULTS[$result] ?? ucfirst($result);
    }
}
