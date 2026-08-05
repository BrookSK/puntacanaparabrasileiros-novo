-- PASSO 1: Atualizar categorias de viajante existentes
UPDATE traveler_categories SET name = 'Adulto', age_group = '12-85 anos', sort_order = 1 WHERE slug = 'adulto';
UPDATE traveler_categories SET name = 'Criança', slug = 'crianca', age_group = '4-11 anos', sort_order = 2 WHERE slug = 'crianca';
UPDATE traveler_categories SET name = 'Infantil', slug = 'infantil', age_group = '0-3 anos', sort_order = 3 WHERE slug = 'bebe';
DELETE FROM traveler_categories WHERE slug = 'idoso';

-- PASSO 2: Inserir Criança e Infantil se não existirem
INSERT IGNORE INTO traveler_categories (name, slug, age_group, sort_order) VALUES ('Criança', 'crianca', '4-11 anos', 2);
INSERT IGNORE INTO traveler_categories (name, slug, age_group, sort_order) VALUES ('Infantil', 'infantil', '0-3 anos', 3);

-- PASSO 3: Limpar preços antigos
DELETE FROM trip_package_categories;

-- PASSO 4: Inserir preços por faixa etária para cada passeio
-- Cada INSERT usa subquery para pegar o package_id e traveler_category_id corretos

-- Buggies + Cenote Domitai: Adulto $55, Criança $40, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'buggies-cenote-domitai' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 40.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'buggies-cenote-domitai' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'buggies-cenote-domitai' AND tc.slug = 'infantil' LIMIT 1;

-- Quadriciclos + Cenote: Adulto $65, Criança $45, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 65.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'quadriciclos-cenote' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'quadriciclos-cenote' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'quadriciclos-cenote' AND tc.slug = 'infantil' LIMIT 1;

-- Saona VIP Mano Juan: Adulto $69, Criança $49, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-vip-mano-juan-lancha' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-vip-mano-juan-lancha' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-vip-mano-juan-lancha' AND tc.slug = 'infantil' LIMIT 1;

-- La Hacienda Park: Adulto $99, Criança $69, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 99.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'la-hacienda-park' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'la-hacienda-park' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'la-hacienda-park' AND tc.slug = 'infantil' LIMIT 1;

-- Chic Cabaret: Adulto $180, Criança $120, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 180.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'chic-cabaret-restaurant' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'chic-cabaret-restaurant' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'chic-cabaret-restaurant' AND tc.slug = 'infantil' LIMIT 1;

-- Coco Bongo Front Row: Adulto $190 (somente adultos)
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 190.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'coco-bongo-front-row' AND tc.slug = 'adulto' LIMIT 1;

-- Coco Bongo Gold Member: Adulto $170 (somente adultos)
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 170.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'coco-bongo-gold-member' AND tc.slug = 'adulto' LIMIT 1;

-- Coco Bongo Open Bar: Adulto $90 (somente adultos)
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 90.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'coco-bongo-open-bar' AND tc.slug = 'adulto' LIMIT 1;

-- Nado 2 Golfinhos: Adulto $199, Criança $149, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 199.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 149.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-2-golfinhos' AND tc.slug = 'infantil' LIMIT 1;

-- Nado 1 Golfinho: Adulto $155, Criança $120, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 155.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-golfinho' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-golfinho' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'nado-e-interacao-com-golfinho' AND tc.slug = 'infantil' LIMIT 1;

-- Supreme Safari: Adulto $45, Criança $30, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'supreme-safari' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 30.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'supreme-safari' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'supreme-safari' AND tc.slug = 'infantil' LIMIT 1;

-- Samaná: Adulto $89, Criança $59, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 89.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado' AND tc.slug = 'infantil' LIMIT 1;

-- Scuba Doo: Adulto $70, Criança $50, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 70.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scuba-doo-aventura-submarina' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 50.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scuba-doo-aventura-submarina' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scuba-doo-aventura-submarina' AND tc.slug = 'infantil' LIMIT 1;

-- Pesca em Alto Mar: Adulto $70, Criança $50, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 70.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'pesca-em-alto-mar' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 50.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'pesca-em-alto-mar' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'pesca-em-alto-mar' AND tc.slug = 'infantil' LIMIT 1;

-- Party Boat: Adulto $59, Criança $39, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'festa-no-catamara-party-boat' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 39.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'festa-no-catamara-party-boat' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'festa-no-catamara-party-boat' AND tc.slug = 'infantil' LIMIT 1;

-- Seaquarium: Adulto $89, Criança $59, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 89.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'seaquarium' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'seaquarium' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'seaquarium' AND tc.slug = 'infantil' LIMIT 1;

-- Interação com Golfinho: Adulto $120, Criança $90, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 120.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'interacao-com-golfinho' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 90.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'interacao-com-golfinho' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'interacao-com-golfinho' AND tc.slug = 'infantil' LIMIT 1;

-- Parasailing: Adulto $60, Criança $45, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 60.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'parasailing' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 45.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'parasailing' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'parasailing' AND tc.slug = 'infantil' LIMIT 1;

-- Santo Domingo: Adulto $49, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'santo-domingo' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'santo-domingo' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'santo-domingo' AND tc.slug = 'infantil' LIMIT 1;

-- Scape Park: Adulto $69, Criança $49, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 69.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scape-park-cenote' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scape-park-cenote' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'scape-park-cenote' AND tc.slug = 'infantil' LIMIT 1;

-- Isla Catalina Snorkel: Adulto $55, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-snorkel' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-snorkel' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-snorkel' AND tc.slug = 'infantil' LIMIT 1;

-- Isla Catalina + Chavón: Adulto $59, Criança $40, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 59.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-altos-de-chavon' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 40.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-altos-de-chavon' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'isla-catalina-altos-de-chavon' AND tc.slug = 'infantil' LIMIT 1;

-- Saona Premium: Adulto $79, Criança $55, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 79.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 55.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-premium-brasil-lancha-ida-e-volta' AND tc.slug = 'infantil' LIMIT 1;

-- Saona Clássica: Adulto $49, Criança $35, Infantil $0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 49.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-classica' AND tc.slug = 'adulto' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 35.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-classica' AND tc.slug = 'crianca' LIMIT 1;
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price)
SELECT tp.id, tc.id, 0.00, NULL FROM trip_packages tp INNER JOIN trips t ON t.id = tp.trip_id, traveler_categories tc WHERE t.slug = 'saona-classica' AND tc.slug = 'infantil' LIMIT 1;
