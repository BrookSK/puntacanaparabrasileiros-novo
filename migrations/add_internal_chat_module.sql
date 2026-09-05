-- Migração: Chat Interno entre atendentes/equipe
-- Data: 2026-08-13
-- Descrição: Conversas internas (diretas ou em grupo) entre membros da equipe,
--            com possibilidade de vincular a um cliente (whatsapp_contact) ou reserva.
--
-- Como aplicar: execute este arquivo no phpMyAdmin no banco do site.

-- ─────────────────────────────────────────────
-- Conversas
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `internal_chat_conversations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` ENUM('direct','group') NOT NULL DEFAULT 'direct',
    `title` VARCHAR(200) DEFAULT NULL COMMENT 'Nome do grupo (para type=group)',
    `created_by` INT UNSIGNED DEFAULT NULL,
    `related_contact_id` INT UNSIGNED DEFAULT NULL COMMENT 'Cliente (whatsapp_contacts) sobre o qual a conversa é',
    `related_booking_id` INT UNSIGNED DEFAULT NULL COMMENT 'Reserva vinculada (opcional)',
    `last_message_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ic_type` (`type`),
    KEY `idx_ic_contact` (`related_contact_id`),
    KEY `idx_ic_last` (`last_message_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Participantes de cada conversa
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `internal_chat_participants` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conversation_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `role` ENUM('member','admin') NOT NULL DEFAULT 'member',
    `last_read_message_id` INT UNSIGNED NOT NULL DEFAULT 0,
    `joined_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_conv_user` (`conversation_id`, `user_id`),
    KEY `idx_icp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Mensagens
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `internal_chat_messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conversation_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL COMMENT 'Autor da mensagem (NULL = sistema)',
    `body` TEXT DEFAULT NULL,
    `message_type` ENUM('text','system') NOT NULL DEFAULT 'text',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_icm_conv` (`conversation_id`, `id`),
    KEY `idx_icm_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Obs.: sem FOREIGN KEY (validação na aplicação), padrão dos módulos recentes.
