-- Migration 003: Criar tabela de inscritos na newsletter
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `name` VARCHAR(200) DEFAULT NULL,
    `status` ENUM('active','unsubscribed','bounced') NOT NULL DEFAULT 'active',
    `source` VARCHAR(50) DEFAULT 'blog_sidebar',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `subscribed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_newsletter_email` (`email`),
    KEY `idx_newsletter_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de campanhas de email
CREATE TABLE IF NOT EXISTS `newsletter_campaigns` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `subject` VARCHAR(255) NOT NULL,
    `body` LONGTEXT NOT NULL,
    `status` ENUM('draft','sending','sent','failed') NOT NULL DEFAULT 'draft',
    `recipients_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `sent_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `failed_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `sent_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_nc_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
