-- Migração: Adicionar rastreamento UTM na tabela bookings
-- Data: 2026-08-13
-- Descrição: Permite relatórios de campanhas de tráfego pago (Meta Ads, Google Ads, etc.)

ALTER TABLE bookings ADD COLUMN utm_source VARCHAR(150) DEFAULT NULL AFTER ip_address;
ALTER TABLE bookings ADD COLUMN utm_medium VARCHAR(150) DEFAULT NULL AFTER utm_source;
ALTER TABLE bookings ADD COLUMN utm_campaign VARCHAR(200) DEFAULT NULL AFTER utm_medium;
ALTER TABLE bookings ADD COLUMN utm_term VARCHAR(200) DEFAULT NULL AFTER utm_campaign;
ALTER TABLE bookings ADD COLUMN utm_content VARCHAR(200) DEFAULT NULL AFTER utm_term;
ALTER TABLE bookings ADD COLUMN referrer VARCHAR(500) DEFAULT NULL AFTER utm_content;

CREATE INDEX idx_bookings_utm_source ON bookings (utm_source);
CREATE INDEX idx_bookings_utm_campaign ON bookings (utm_campaign);
