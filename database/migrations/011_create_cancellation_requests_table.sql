-- Migration: Criar tabela de solicitações de cancelamento
-- Data: 2026-08-07

CREATE TABLE IF NOT EXISTS cancellation_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_response TEXT NULL,
    refund_status ENUM('none', 'refunded', 'partial_refund') NOT NULL DEFAULT 'none',
    refund_amount DECIMAL(10,2) NULL DEFAULT NULL,
    refund_notes TEXT NULL,
    processed_by INT UNSIGNED NULL,
    processed_at DATETIME NULL,
    refunded_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_booking_id (booking_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
