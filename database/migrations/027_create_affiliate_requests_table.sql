-- Migration: Criar tabela de solicitações de afiliação (separada de users)
-- Data: 2026-08-10

CREATE TABLE IF NOT EXISTS affiliate_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    username VARCHAR(100) NULL,
    password_hash VARCHAR(255) NOT NULL,
    pix VARCHAR(255) NULL,
    payment_email VARCHAR(255) NULL,
    website VARCHAR(500) NULL,
    followers_count VARCHAR(50) NULL,
    niche VARCHAR(100) NULL,
    content_type VARCHAR(100) NULL,
    promotion_strategy TEXT NULL,
    how_found VARCHAR(255) NULL,
    social_links TEXT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    approved_at DATETIME NULL,
    rejected_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
