-- ============================================================
-- Sistema de Log de Auditoria
-- Registra ações administrativas importantes do sistema
-- ============================================================

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL COMMENT 'ID do usuário que realizou a ação (NULL para ações anônimas)',
    `user_name` VARCHAR(255) NULL COMMENT 'Nome do usuário no momento da ação (snapshot)',
    `user_email` VARCHAR(255) NULL COMMENT 'Email do usuário no momento da ação (snapshot)',
    `action` VARCHAR(100) NOT NULL COMMENT 'Tipo de ação: create, update, delete, login, logout, etc.',
    `entity_type` VARCHAR(100) NULL COMMENT 'Tipo de entidade afetada: trip, booking, user, setting, etc.',
    `entity_id` INT UNSIGNED NULL COMMENT 'ID do registro afetado',
    `entity_name` VARCHAR(255) NULL COMMENT 'Nome/identificador legível do registro afetado',
    `description` TEXT NULL COMMENT 'Descrição detalhada da ação',
    `changes` JSON NULL COMMENT 'Dados alterados em formato JSON (sem dados sensíveis)',
    `result` ENUM('success', 'failure', 'warning') DEFAULT 'success' COMMENT 'Resultado da ação',
    `ip_address` VARCHAR(45) NULL COMMENT 'Endereço IP do usuário',
    `user_agent` VARCHAR(512) NULL COMMENT 'User agent do navegador',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_entity_type` (`entity_type`),
    INDEX `idx_entity_id` (`entity_id`),
    INDEX `idx_result` (`result`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_user_action_date` (`user_id`, `action`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentário da tabela
ALTER TABLE `audit_logs` COMMENT = 'Registra logs de auditoria para rastrear ações no sistema administrativo';
