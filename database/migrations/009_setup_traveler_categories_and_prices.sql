-- Migration 009: Configurar categorias de viajante e preços por faixa etária
-- Atualiza categorias para: Adulto (12-85), Criança (4-11), Infantil (0-3)
-- Insere preços para todos os passeios

-- ============================================================
-- 1. ATUALIZAR CATEGORIAS DE VIAJANTE
-- ============================================================
UPDATE traveler_categories SET name = 'Adulto', age_group = '12-85 anos', sort_order = 1 WHERE slug = 'adulto';
UPDATE traveler_categories SET name = 'Criança', age_group = '4-11 anos', sort_order = 2 WHERE slug = 'crianca';
UPDATE traveler_categories SET name = 'Infantil', age_group = '0-3 anos', sort_order = 3 WHERE slug = 'bebe';
DELETE FROM traveler_categories WHERE slug = 'idoso';

-- Caso as categorias não existam, inserir
INSERT IGNORE INTO traveler_categories (name, slug, age_group, sort_order) VALUES
('Adulto', 'adulto', '12-85 anos', 1),
('Criança', 'crianca', '4-11 anos', 2),
('Infantil', 'infantil', '0-3 anos', 3);

-- Se o slug 'bebe' foi atualizado, garantir que exista 'infantil'
UPDATE traveler_categories SET slug = 'infantil', name = 'Infantil', age_group = '0-3 anos' WHERE slug = 'bebe';

-- ============================================================
-- 2. LIMPAR PREÇOS ANTIGOS E INSERIR NOVOS
-- ============================================================
DELETE FROM trip_package_categories;

-- Inserir preços para cada passeio usando subqueries
-- Formato: (package_id do passeio, traveler_category_id, price, sale_price)

-- Obter IDs das categorias
SET @adulto_id = (SELECT id FROM traveler_categories WHERE slug = 'adulto' LIMIT 1);
SET @crianca_id = (SELECT id FROM traveler_categories WHERE slug = 'crianca' LIMIT 1);
SET @infantil_id = (SELECT id FROM traveler_categories WHERE slug = 'infantil' LIMIT 1);

-- 1. Buggies + Cenote Domitai: Adulto $55, Criança $40, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'buggies-cenote-domitai' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 40.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'buggies-cenote-domitai' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'buggies-cenote-domitai' LIMIT 1;

-- 2. Quadriciclos + Cenote: Adulto $65, Criança $45, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 65.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'quadriciclos-cenote' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'quadriciclos-cenote' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'quadriciclos-cenote' LIMIT 1;

-- 3. Saona VIP Mano Juan - Lancha: Adulto $69, Criança $49, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-vip-mano-juan-lancha' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-vip-mano-juan-lancha' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-vip-mano-juan-lancha' LIMIT 1;

-- 4. La Hacienda Park: Adulto $99, Criança $69, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 99.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'la-hacienda-park' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'la-hacienda-park' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'la-hacienda-park' LIMIT 1;

-- 5. Chic Cabaret & Restaurant: Adulto $180, Criança $120, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 180.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'chic-cabaret-restaurant' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'chic-cabaret-restaurant' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'chic-cabaret-restaurant' LIMIT 1;

-- 6. Coco Bongo - Front Row: Adulto $190, Criança N/A, Infantil N/A
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 190.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'coco-bongo-front-row' LIMIT 1;

-- 7. Coco Bongo - Gold Member: Adulto $170, Criança N/A, Infantil N/A
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 170.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'coco-bongo-gold-member' LIMIT 1;

-- 8. Coco Bongo - Open Bar: Adulto $90, Criança N/A, Infantil N/A
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 90.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'coco-bongo-open-bar' LIMIT 1;

-- 9. Nado e interação com 2 Golfinhos: Adulto $199, Criança $149, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 199.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 149.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' LIMIT 1;

-- 10. Nado e interação com 1 Golfinho: Adulto $155, Criança $120, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 155.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-golfinho' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-golfinho' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'nado-e-interacao-com-golfinho' LIMIT 1;

-- 11. Supreme Safari: Adulto $45, Criança $30, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'supreme-safari' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 30.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'supreme-safari' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'supreme-safari' LIMIT 1;

-- 12. Samaná: Adulto $89, Criança $59, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 89.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' LIMIT 1;

-- 13. Scuba Doo: Adulto $70, Criança $50, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 70.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scuba-doo-aventura-submarina' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 50.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scuba-doo-aventura-submarina' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scuba-doo-aventura-submarina' LIMIT 1;

-- 14. Pesca em Alto Mar: Adulto $70, Criança $50, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 70.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'pesca-em-alto-mar' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 50.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'pesca-em-alto-mar' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'pesca-em-alto-mar' LIMIT 1;

-- 15. Festa no Catamarã (Party Boat): Adulto $59, Criança $39, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'festa-no-catamara-party-boat' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 39.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'festa-no-catamara-party-boat' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'festa-no-catamara-party-boat' LIMIT 1;

-- 16. Seaquarium: Adulto $89, Criança $59, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 89.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'seaquarium' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'seaquarium' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'seaquarium' LIMIT 1;

-- 17. Interação com Golfinho: Adulto $120, Criança $90, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'interacao-com-golfinho' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 90.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'interacao-com-golfinho' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'interacao-com-golfinho' LIMIT 1;

-- 18. Parasailing: Adulto $60, Criança $45, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 60.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'parasailing' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'parasailing' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'parasailing' LIMIT 1;

-- 19. Santo Domingo: Adulto $49, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'santo-domingo' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'santo-domingo' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'santo-domingo' LIMIT 1;

-- 20. Scape Park + Cenote: Adulto $69, Criança $49, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scape-park-cenote' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scape-park-cenote' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'scape-park-cenote' LIMIT 1;

-- 21. Isla Catalina com Snorkel: Adulto $55, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-snorkel' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-snorkel' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-snorkel' LIMIT 1;

-- 22. Isla Catalina + Altos de Chavón: Adulto $59, Criança $40, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-altos-de-chavon' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 40.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-altos-de-chavon' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'isla-catalina-altos-de-chavon' LIMIT 1;

-- 23. Saona Premium Brasil - Lancha: Adulto $79, Criança $55, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 79.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' LIMIT 1;

-- 24. Saona Clássica – Catamarã: Adulto $49, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @adulto_id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-classica' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @crianca_id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-classica' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, @infantil_id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id WHERE t.slug = 'saona-classica' LIMIT 1;
