-- Migração: Adicionar campos de informações do passeio na tabela trips
-- - ideal_for: lista de perfis "Ideal para" (chips), em JSON.
-- - departure_time_info: texto livre de saída do hotel (ex.: "Entre 7h00 e 8h00").
-- - return_time_info: texto livre de retorno ao hotel (ex.: "Entre 18h00 e 19h00").
-- - availability_info: texto livre de disponibilidade (ex.: "Sujeita a quórum, capacidade e clima").
-- Data: 2026-09-10

ALTER TABLE trips ADD COLUMN ideal_for JSON DEFAULT NULL AFTER excludes;
ALTER TABLE trips ADD COLUMN departure_time_info VARCHAR(255) DEFAULT NULL AFTER meeting_point;
ALTER TABLE trips ADD COLUMN return_time_info VARCHAR(255) DEFAULT NULL AFTER departure_time_info;
ALTER TABLE trips ADD COLUMN availability_info VARCHAR(255) DEFAULT NULL AFTER return_time_info;
