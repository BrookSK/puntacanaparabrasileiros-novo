-- Migração: Adicionar campos de informações do passeio na tabela trips
-- - ideal_for: lista de perfis "Ideal para" (chips), em JSON.
-- - departure_time_info: texto livre de saída do hotel (ex.: "Entre 7h00 e 8h00").
-- - return_time_info: texto livre de retorno ao hotel (ex.: "Entre 18h00 e 19h00").
-- - availability_info: texto livre de disponibilidade (ex.: "Sujeita a quórum, capacidade e clima").
-- Data: 2026-09-10
--
-- Idempotente: usa "IF NOT EXISTS" para poder rodar de novo sem erro de coluna duplicada.

ALTER TABLE trips ADD COLUMN IF NOT EXISTS ideal_for JSON DEFAULT NULL AFTER excludes;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS departure_time_info VARCHAR(255) DEFAULT NULL AFTER meeting_point;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS return_time_info VARCHAR(255) DEFAULT NULL AFTER departure_time_info;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS availability_info VARCHAR(255) DEFAULT NULL AFTER return_time_info;
