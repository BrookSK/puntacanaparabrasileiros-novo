<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;
use App\Models\AuditLog;

/**
 * Serviço centralizado para registro de logs de auditoria.
 * Fornece métodos convenientes para registrar diferentes tipos de ações.
 */
class AuditService
{
    private static ?AuditService $instance = null;
    private Database $db;
    private AuditLog $model;

    /**
     * Campos que nunca devem ser logados (dados sensíveis).
     */
    private const SENSITIVE_FIELDS = [
        'password', 'password_confirmation', 'senha', 'token', 'secret',
        'api_key', 'private_key', 'credit_card', 'cvv', 'card_number',
        '_token', 'csrf_token', 'remember_token', 'refresh_token',
    ];

    private function __construct()
    {
        $this->db = Database::getInstance();
        $this->model = new AuditLog();
    }

    /**
     * Obtém a instância singleton do serviço.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Registra uma ação de auditoria.
     *
     * @param string $action Tipo de ação (create, update, delete, login, etc.)
     * @param string|null $entityType Tipo de entidade afetada
     * @param int|null $entityId ID do registro afetado
     * @param string|null $entityName Nome/identificador legível do registro
     * @param string|null $description Descrição detalhada da ação
     * @param array|null $changes Dados alterados (sem dados sensíveis)
     * @param string $result Resultado da ação (success, failure, warning)
     * @param array|null $user Dados do usuário (se não fornecido, usa sessão atual)
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $entityName = null,
        ?string $description = null,
        ?array $changes = null,
        string $result = 'success',
        ?array $user = null
    ): bool {
        try {
            // Obter dados do usuário atual se não fornecido
            if ($user === null) {
                $user = $this->getCurrentUser();
            }

            // Filtrar dados sensíveis das changes
            if ($changes !== null) {
                $changes = $this->filterSensitiveData($changes);
            }

            // Preparar dados para inserção
            $data = [
                'user_id' => $user['id'] ?? null,
                'user_name' => $this->formatUserName($user),
                'user_email' => $user['email'] ?? null,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'entity_name' => $entityName ? mb_substr($entityName, 0, 255) : null,
                'description' => $description,
                'changes' => $changes !== null ? json_encode($changes, JSON_UNESCAPED_UNICODE) : null,
                'result' => $result,
                'ip_address' => $this->getIpAddress(),
                'user_agent' => $this->getUserAgent(),
            ];

            $this->db->insert('audit_logs', $data);
            return true;
        } catch (\Throwable $e) {
            // Falha silenciosa para não afetar a operação principal
            error_log('AuditService error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra criação de registro.
     */
    public function logCreate(
        string $entityType,
        int $entityId,
        ?string $entityName = null,
        ?array $data = null,
        ?string $description = null
    ): bool {
        return $this->log(
            'create',
            $entityType,
            $entityId,
            $entityName,
            $description ?? "Criou {$this->translateEntityType($entityType)}" . ($entityName ? ": {$entityName}" : ''),
            $data ? ['created' => $data] : null
        );
    }

    /**
     * Registra atualização de registro.
     */
    public function logUpdate(
        string $entityType,
        int $entityId,
        ?string $entityName = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?string $description = null
    ): bool {
        $changes = null;
        if ($oldData !== null && $newData !== null) {
            $changes = $this->computeChanges($oldData, $newData);
            if (empty($changes)) {
                return true; // Nada mudou, não registra log
            }
        }

        return $this->log(
            'update',
            $entityType,
            $entityId,
            $entityName,
            $description ?? "Atualizou {$this->translateEntityType($entityType)}" . ($entityName ? ": {$entityName}" : ''),
            $changes
        );
    }

    /**
     * Registra exclusão de registro.
     */
    public function logDelete(
        string $entityType,
        int $entityId,
        ?string $entityName = null,
        ?array $deletedData = null,
        ?string $description = null
    ): bool {
        return $this->log(
            'delete',
            $entityType,
            $entityId,
            $entityName,
            $description ?? "Excluiu {$this->translateEntityType($entityType)}" . ($entityName ? ": {$entityName}" : ''),
            $deletedData ? ['deleted' => $deletedData] : null
        );
    }

    /**
     * Registra tentativa de login bem-sucedida.
     */
    public function logLogin(array $user): bool
    {
        return $this->log(
            'login',
            'user',
            (int) $user['id'],
            ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
            'Login realizado com sucesso',
            null,
            'success',
            $user
        );
    }

    /**
     * Registra tentativa de login falha.
     */
    public function logLoginFailed(string $email, string $reason = 'Credenciais inválidas'): bool
    {
        return $this->log(
            'login_failed',
            'user',
            null,
            $email,
            "Tentativa de login falhou: {$reason}",
            ['email' => $email],
            'failure',
            ['email' => $email] // Usuário não autenticado
        );
    }

    /**
     * Registra alteração de status.
     */
    public function logStatusChange(
        string $entityType,
        int $entityId,
        ?string $entityName,
        string $oldStatus,
        string $newStatus,
        ?string $description = null
    ): bool {
        return $this->log(
            'status_change',
            $entityType,
            $entityId,
            $entityName,
            $description ?? "Alterou status de {$this->translateEntityType($entityType)}: {$oldStatus} → {$newStatus}",
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    /**
     * Registra atualização de configurações.
     */
    public function logSettingsUpdate(array $changes, ?string $description = null): bool
    {
        return $this->log(
            'settings_update',
            'setting',
            null,
            'Configurações do Sistema',
            $description ?? 'Atualizou configurações do sistema',
            ['updated_settings' => array_keys($changes)]
        );
    }

    /**
     * Registra uma ação genérica.
     */
    public function logAction(
        string $action,
        ?string $entityType,
        ?int $entityId,
        ?string $entityName,
        string $description,
        ?array $extra = null,
        string $result = 'success'
    ): bool {
        return $this->log($action, $entityType, $entityId, $entityName, $description, $extra, $result);
    }

    /**
     * Obtém o usuário atual da sessão.
     */
    private function getCurrentUser(): ?array
    {
        try {
            $app = App::getInstance();
            $session = $app->getSession();
            return $session->get('user');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Formata o nome completo do usuário.
     */
    private function formatUserName(?array $user): ?string
    {
        if ($user === null) return null;
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        return $name !== '' ? $name : ($user['email'] ?? null);
    }

    /**
     * Obtém o endereço IP do cliente.
     */
    private function getIpAddress(): ?string
    {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Pega apenas o primeiro IP se houver múltiplos
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }
        return null;
    }

    /**
     * Obtém o User Agent do navegador.
     */
    private function getUserAgent(): ?string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        return $ua ? mb_substr($ua, 0, 512) : null;
    }

    /**
     * Remove dados sensíveis de um array.
     */
    private function filterSensitiveData(array $data): array
    {
        $filtered = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            
            // Verifica se é um campo sensível
            $isSensitive = false;
            foreach (self::SENSITIVE_FIELDS as $sensitiveField) {
                if (strpos($lowerKey, $sensitiveField) !== false) {
                    $isSensitive = true;
                    break;
                }
            }

            if ($isSensitive) {
                $filtered[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $filtered[$key] = $this->filterSensitiveData($value);
            } else {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Computa as diferenças entre dados antigos e novos.
     */
    private function computeChanges(array $oldData, array $newData): array
    {
        $changes = [];
        
        // Campos que mudaram
        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;
            
            // Ignora campos sensíveis
            $lowerKey = strtolower($key);
            foreach (self::SENSITIVE_FIELDS as $sensitiveField) {
                if (strpos($lowerKey, $sensitiveField) !== false) {
                    continue 2;
                }
            }

            // Compara valores (convertendo para string para comparação)
            if ((string) $oldValue !== (string) $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Traduz tipo de entidade para português.
     */
    private function translateEntityType(string $entityType): string
    {
        return AuditLog::translateEntityType($entityType);
    }
}
