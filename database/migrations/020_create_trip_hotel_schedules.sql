-- Migration: Criar tabelas de horários por hotel para passeios
-- Data: 2026-08-10

-- Tabela de hotéis vinculados aos passeios
CREATE TABLE IF NOT EXISTS trip_hotels (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id INT UNSIGNED NOT NULL,
    hotel_name VARCHAR(255) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_trip_id (trip_id),
    INDEX idx_hotel_name (hotel_name),
    INDEX idx_active (is_active),
    UNIQUE KEY uk_trip_hotel (trip_id, hotel_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de horários disponíveis por hotel/passeio
CREATE TABLE IF NOT EXISTS trip_hotel_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_hotel_id INT UNSIGNED NOT NULL,
    pickup_time TIME NOT NULL,
    notes VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_trip_hotel_id (trip_hotel_id),
    INDEX idx_pickup_time (pickup_time),
    INDEX idx_active (is_active),
    UNIQUE KEY uk_hotel_time (trip_hotel_id, pickup_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
