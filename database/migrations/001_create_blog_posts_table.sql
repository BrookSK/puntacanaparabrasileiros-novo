-- Migration 001: Criar tabela de posts do blog
-- Executar após schema.sql

CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `color` VARCHAR(7) DEFAULT '#3772C0',
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_blog_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `author_id` INT UNSIGNED DEFAULT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_blog_posts_slug` (`slug`),
    KEY `idx_bp_category` (`category_id`),
    KEY `idx_bp_author` (`author_id`),
    KEY `idx_bp_status` (`status`),
    KEY `idx_bp_published` (`published_at`),
    CONSTRAINT `fk_bp_category` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_bp_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categorias padrão
INSERT INTO `blog_categories` (`name`, `slug`, `color`) VALUES
('Sem Categoria', 'sem-categoria', '#6b7280'),
('Geral', 'geral', '#3772C0'),
('Passeio', 'passeio', '#E4B505'),
('Transfer', 'transfer', '#1B6F00'),
('Dicas', 'dicas', '#e74c3c');
