-- Migration: Campos da página de passeios (validação do cliente)
-- Data: 2026-10-09
-- Cópia nomeada da database/migrations/035_add_trip_page_fields.sql (o projeto
-- mantém as duas convenções de pasta). Idempotente via "IF NOT EXISTS".
--
-- Item 1 — Detalhes do passeio (cards de destaque abaixo do título), independentes do roteiro.
ALTER TABLE trips ADD COLUMN IF NOT EXISTS detail_highlights JSON DEFAULT NULL AFTER ideal_for;

-- Item 2 — Faixas de horário (De/Até) de saída e chegada do hotel.
ALTER TABLE trips ADD COLUMN IF NOT EXISTS departure_time_start TIME DEFAULT NULL AFTER availability_info;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS departure_time_end TIME DEFAULT NULL AFTER departure_time_start;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS return_time_start TIME DEFAULT NULL AFTER departure_time_end;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS return_time_end TIME DEFAULT NULL AFTER return_time_start;

-- Item 4 — "O que levar" (lista individual por passeio).
ALTER TABLE trips ADD COLUMN IF NOT EXISTS what_to_bring JSON DEFAULT NULL AFTER excludes;

-- Item 5 — Avaliações do Google (Place ID + valores manuais de fallback).
ALTER TABLE trips ADD COLUMN IF NOT EXISTS google_place_id VARCHAR(255) DEFAULT NULL;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS google_rating DECIMAL(2,1) DEFAULT NULL;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS google_reviews_count INT UNSIGNED DEFAULT NULL;
ALTER TABLE trips ADD COLUMN IF NOT EXISTS google_reviews_url VARCHAR(500) DEFAULT NULL;

-- Item 2 — Horário dedicado por etapa do roteiro.
ALTER TABLE trip_itinerary ADD COLUMN IF NOT EXISTS step_time TIME DEFAULT NULL AFTER description;
