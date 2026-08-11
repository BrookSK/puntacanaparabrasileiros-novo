-- Migration: Criar tabelas do sistema de afiliados (se não existirem)
-- Data: 2026-08-10

CREATE TABLE IF NOT EXISTS affiliates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    status ENUM('pending', 'active', 'rejected', 'suspended') NOT NULL DEFAULT 'pending',
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    cookie_days INT NOT NULL DEFAULT 30,
    payment_email VARCHAR(255) NULL,
    payment_method VARCHAR(50) NULL DEFAULT 'pix',
    notes TEXT NULL,
    total_visits INT UNSIGNED NOT NULL DEFAULT 0,
    total_referrals INT UNSIGNED NOT NULL DEFAULT 0,
    total_sales DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_earnings DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_paid DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    UNIQUE KEY uk_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS affiliate_visits (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    ip_address VARCHAR(45) NULL,
    referrer TEXT NULL,
    page_url VARCHAR(500) NULL,
    user_agent TEXT NULL,
    converted TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_affiliate_id (affiliate_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    affiliate_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    base_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'paid', 'rejected') NOT NULL DEFAULT 'pending',
    payout_reference VARCHAR(255) NULL,
    paid_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_affiliate_id (affiliate_id),
    INDEX idx_status (status),
    INDEX idx_booking_id (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
