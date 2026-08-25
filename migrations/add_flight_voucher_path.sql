-- Migração: Adicionar coluna flight_voucher_path na tabela bookings
-- Data: 2026-08-20

ALTER TABLE bookings ADD COLUMN flight_voucher_path VARCHAR(500) DEFAULT NULL AFTER notes;

-- Tabela de notificações de mudança de preço
CREATE TABLE IF NOT EXISTS price_change_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    trip_title VARCHAR(255) NOT NULL,
    old_price DECIMAL(10,2) NOT NULL,
    new_price DECIMAL(10,2) NOT NULL,
    notified_emails TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
