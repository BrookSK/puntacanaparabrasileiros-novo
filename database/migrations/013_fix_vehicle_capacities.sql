-- Migration: Corrigir capacidades dos veículos de transfer
-- Data: 2026-08-07
-- O site antigo tinha valores genéricos (59 pax) para todos os veículos.
-- Esta migration corrige com os valores reais de cada tipo de veículo.

-- Corrigir Ônibus Compartilhado
UPDATE transfer_vehicles
SET max_passengers = 45,
    max_adults = 45,
    max_children = 10,
    max_infants = 5,
    max_luggage = 45,
    description = 'Viaje com conforto e economia em um ônibus climatizado, com embarques regulares e motoristas experientes. Ideal para quem busca praticidade em Punta Cana.'
WHERE title LIKE '%nibus%' OR title LIKE '%Compartilhado%'
LIMIT 1;

-- Corrigir Van Privativa
UPDATE transfer_vehicles
SET max_passengers = 8,
    max_adults = 8,
    max_children = 4,
    max_infants = 2,
    max_luggage = 6,
    description = 'Tenha mais conforto e privacidade com nosso transfer exclusivo em van. Perfeito para famílias ou pequenos grupos, com ar-condicionado e horários flexíveis.'
WHERE title LIKE '%Van%' AND title NOT LIKE '%Adaptada%'
LIMIT 1;

-- Corrigir Van Adaptada / Acessível
UPDATE transfer_vehicles
SET max_passengers = 7,
    max_adults = 7,
    max_children = 3,
    max_infants = 2,
    max_luggage = 5,
    description = 'Viaje com segurança e acessibilidade em nossa van adaptada com rampa para cadeirantes. Espaço amplo e motorista preparado para um trajeto tranquilo.'
WHERE title LIKE '%Adaptada%' OR title LIKE '%Acess%'
LIMIT 1;
