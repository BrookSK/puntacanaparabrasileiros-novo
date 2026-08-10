-- Migration: Dados de exemplo para horários por hotel
-- Data: 2026-08-10
-- Nota: Vincula aos passeios existentes (IDs 1-6). Ajuste conforme necessário.

-- ============================================================
-- Passeio ID 1 (primeiro passeio do sistema)
-- ============================================================
INSERT IGNORE INTO trip_hotels (trip_id, hotel_name, sort_order, is_active) VALUES
(1, 'Hotel Barceló Bávaro Palace', 1, 1),
(1, 'Hard Rock Hotel & Casino', 2, 1),
(1, 'Dreams Royal Beach', 3, 1),
(1, 'Iberostar Grand Bávaro', 4, 1),
(1, 'Secrets Royal Beach', 5, 1),
(1, 'Lopesan Costa Bávaro', 6, 1),
(1, 'Meliá Caribe Beach', 7, 1),
(1, 'RIU Republica', 8, 1);

INSERT IGNORE INTO trip_hotel_schedules (trip_hotel_id, pickup_time, is_active) VALUES
-- Barceló Bávaro Palace
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '07:20:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '08:10:00', 1),
-- Hard Rock
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Hard Rock Hotel & Casino'), '06:50:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Hard Rock Hotel & Casino'), '07:30:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Hard Rock Hotel & Casino'), '08:00:00', 1),
-- Dreams Royal Beach
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Dreams Royal Beach'), '07:00:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Dreams Royal Beach'), '07:45:00', 1),
-- Iberostar Grand Bávaro
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Iberostar Grand Bávaro'), '07:10:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Iberostar Grand Bávaro'), '08:00:00', 1),
-- Secrets Royal Beach
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Secrets Royal Beach'), '07:15:00', 1),
-- Lopesan Costa Bávaro
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Lopesan Costa Bávaro'), '06:45:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Lopesan Costa Bávaro'), '07:30:00', 1),
-- Meliá Caribe Beach
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Meliá Caribe Beach'), '07:05:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'Meliá Caribe Beach'), '07:50:00', 1),
-- RIU Republica
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'RIU Republica'), '06:55:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'RIU Republica'), '07:40:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 1 AND hotel_name = 'RIU Republica'), '08:15:00', 1);

-- ============================================================
-- Passeio ID 2 (segundo passeio)
-- ============================================================
INSERT IGNORE INTO trip_hotels (trip_id, hotel_name, sort_order, is_active) VALUES
(2, 'Hotel Barceló Bávaro Palace', 1, 1),
(2, 'Hard Rock Hotel & Casino', 2, 1),
(2, 'Paradisus Palma Real', 3, 1),
(2, 'Breathless Punta Cana', 4, 1),
(2, 'Grand Palladium', 5, 1),
(2, 'Now Onyx', 6, 1);

INSERT IGNORE INTO trip_hotel_schedules (trip_hotel_id, pickup_time, is_active) VALUES
-- Barceló (passeio 2)
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '08:00:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '08:45:00', 1),
-- Hard Rock (passeio 2)
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Hard Rock Hotel & Casino'), '07:30:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Hard Rock Hotel & Casino'), '08:15:00', 1),
-- Paradisus Palma Real
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Paradisus Palma Real'), '07:45:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Paradisus Palma Real'), '08:30:00', 1),
-- Breathless
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Breathless Punta Cana'), '08:10:00', 1),
-- Grand Palladium
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Grand Palladium'), '07:20:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Grand Palladium'), '08:00:00', 1),
-- Now Onyx
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Now Onyx'), '07:50:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 2 AND hotel_name = 'Now Onyx'), '08:20:00', 1);

-- ============================================================
-- Passeio ID 3 (terceiro passeio)
-- ============================================================
INSERT IGNORE INTO trip_hotels (trip_id, hotel_name, sort_order, is_active) VALUES
(3, 'Hotel Barceló Bávaro Palace', 1, 1),
(3, 'Sanctuary Cap Cana', 2, 1),
(3, 'Hyatt Zilara Cap Cana', 3, 1),
(3, 'Dreams Macao Beach', 4, 1),
(3, 'Majestic Elegance', 5, 1);

INSERT IGNORE INTO trip_hotel_schedules (trip_hotel_id, pickup_time, is_active) VALUES
-- Barceló (passeio 3)
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '09:00:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Hotel Barceló Bávaro Palace'), '09:30:00', 1),
-- Sanctuary Cap Cana
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Sanctuary Cap Cana'), '08:30:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Sanctuary Cap Cana'), '09:15:00', 1),
-- Hyatt Zilara
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Hyatt Zilara Cap Cana'), '08:45:00', 1),
-- Dreams Macao Beach
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Dreams Macao Beach'), '09:00:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Dreams Macao Beach'), '09:45:00', 1),
-- Majestic Elegance
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Majestic Elegance'), '08:50:00', 1),
((SELECT id FROM trip_hotels WHERE trip_id = 3 AND hotel_name = 'Majestic Elegance'), '09:20:00', 1);
