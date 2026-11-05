-- Migration: Disponibilidade por passeio (dias fechados, exceções por data,
-- horários de saída por dia da semana) e antecedência de busca dos transfers.
-- Data: 2026-11-05
--
-- Idempotente: pode rodar mais de uma vez sem erro.

-- Dias da semana em que o passeio NÃO funciona (CSV de 0 a 6; 0 = domingo).
-- Ex.: '1' = fechado às segundas. NULL/vazio = funciona todos os dias.
ALTER TABLE trips ADD COLUMN IF NOT EXISTS closed_weekdays VARCHAR(20) DEFAULT NULL;

-- Horários de saída do passeio. Cada horário vale para os dias da semana marcados.
-- Passeio sem nenhum horário cadastrado = cliente não escolhe horário (só a data).
CREATE TABLE IF NOT EXISTS trip_departure_times (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id INT UNSIGNED NOT NULL,
    departure_time TIME NOT NULL,
    label VARCHAR(100) NULL,
    weekdays VARCHAR(20) NOT NULL DEFAULT '0,1,2,3,4,5,6',
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tdt_trip (trip_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exceções por data (ou período):
--   closed = fechar o passeio nessas datas (feriado, fornecedor fechado etc.)
--   open   = abrir excepcionalmente num dia da semana que normalmente é fechado
CREATE TABLE IF NOT EXISTS trip_date_exceptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    trip_id INT UNSIGNED NOT NULL,
    exception_type VARCHAR(10) NOT NULL DEFAULT 'closed',
    date_start DATE NOT NULL,
    date_end DATE NOT NULL,
    note VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tde_trip (trip_id),
    INDEX idx_tde_dates (date_start, date_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Antecedência (em minutos) da busca no hotel antes do voo, por local.
-- NULL = usa o padrão geral (configuração transfer_pickup_lead_minutes, 240 = 4h).
ALTER TABLE transfer_locations ADD COLUMN IF NOT EXISTS pickup_lead_minutes INT UNSIGNED DEFAULT NULL;
