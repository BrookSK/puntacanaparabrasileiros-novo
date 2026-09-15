-- =============================================================
-- CATÁLOGO OUTUBRO 2026 — PREÇOS
-- Regra: Adulto (cat 1) = maior | Criança (cat 2) = menor |
--        Infantil (cat 6) = 0 (cortesia).
--
-- Método: para cada pacote, APAGA as categorias e REINSERE com os
-- valores do PDF. É o MESMO comportamento do painel (syncCategories:
-- delete + insert), então é seguro e deixa o preço exatamente como
-- o catálogo manda, sem depender do que já existia.
--
-- IMPORTANTE:
-- - FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- - Rode este arquivo TODO de uma vez (cada passeio é um bloco
--   delete+insert independente).
--
-- Categorias: 1=Adulto  2=Criança(4-11)  6=Infantil(cortesia)
-- Mapa package_id -> passeio:
--   36 Chic | 44 Scuba Doo | 45 Pesca | 46 Party Boat | 47 Seaquarium
--   49 Parasailing | 50 Santo Domingo | 51 Scape Park
--   52 Catalina Snorkel | 53 Catalina Chavón
-- =============================================================


-- ===== Isla Catalina com Snorkel (pkg 52): adulto 95 / criança 55 / infantil 0 =====
DELETE FROM trip_package_categories WHERE package_id = 52;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(52, 1, 95.00, NULL, 0, NULL),
(52, 2, 55.00, NULL, 0, NULL),
(52, 6, 0.00,  NULL, 0, NULL);

-- ===== Isla Catalina + Altos de Chavon (pkg 53): adulto 119 / criança 59 / infantil 0 =====
DELETE FROM trip_package_categories WHERE package_id = 53;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(53, 1, 119.00, NULL, 0, NULL),
(53, 2, 59.00,  NULL, 0, NULL),
(53, 6, 0.00,   NULL, 0, NULL);

-- ===== Santo Domingo (pkg 50): adulto 85 / criança 49 / infantil 0 =====
DELETE FROM trip_package_categories WHERE package_id = 50;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(50, 1, 85.00, NULL, 0, NULL),
(50, 2, 49.00, NULL, 0, NULL),
(50, 6, 0.00,  NULL, 0, NULL);

-- ===== Scape Park + Cenote (pkg 51): adulto 129 / criança 69 / infantil 0 =====
DELETE FROM trip_package_categories WHERE package_id = 51;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(51, 1, 129.00, NULL, 0, NULL),
(51, 2, 69.00,  NULL, 0, NULL),
(51, 6, 0.00,   NULL, 0, NULL);

-- ===== Parasailing (pkg 49): adulto 70 / criança 6-11 60 (menor de 6 não pode; sem infantil) =====
DELETE FROM trip_package_categories WHERE package_id = 49;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(49, 1, 70.00, NULL, 0, NULL),
(49, 2, 60.00, NULL, 0, NULL);

-- ===== Scuba Doo (pkg 44): adulto 60 / criança 7-11 50 (menor de 7 não pode; sem infantil) =====
DELETE FROM trip_package_categories WHERE package_id = 44;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(44, 1, 60.00, NULL, 0, NULL),
(44, 2, 50.00, NULL, 0, NULL);

-- ===== Seaquarium (pkg 47): adulto 125 / criança 3-8 89 / infantil (menor de 3) 0 =====
DELETE FROM trip_package_categories WHERE package_id = 47;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(47, 1, 125.00, NULL, 0, NULL),
(47, 2, 89.00,  NULL, 0, NULL),
(47, 6, 0.00,   NULL, 0, NULL);

-- ===== Festa no Catamara / Party Boat (pkg 46): adulto 59 / criança 39 / infantil 0 =====
DELETE FROM trip_package_categories WHERE package_id = 46;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(46, 1, 59.00, NULL, 0, NULL),
(46, 2, 39.00, NULL, 0, NULL),
(46, 6, 0.00,  NULL, 0, NULL);

-- ===== Pesca em Alto Mar (pkg 45): adulto 115 / criança 4-10 60 (só acompanhar; menor de 4 não pode) =====
DELETE FROM trip_package_categories WHERE package_id = 45;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(45, 1, 115.00, NULL, 0, NULL),
(45, 2, 60.00,  NULL, 0, NULL);

-- ===== Chic Cabaret (pkg 36): 180 por pessoa (só adulto) =====
DELETE FROM trip_package_categories WHERE package_id = 36;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(36, 1, 180.00, NULL, 0, NULL);


-- [CONFERÊNCIA] (opcional) rode para ver o resultado
-- SELECT tp.trip_id, tpc.package_id, tpc.traveler_category_id, tpc.price
-- FROM trip_package_categories tpc
-- JOIN trip_packages tp ON tp.id = tpc.package_id
-- WHERE tpc.package_id IN (36,44,45,46,47,49,50,51,52,53)
-- ORDER BY tp.trip_id, tpc.traveler_category_id;
