-- Migration: Corrigir e popular relações entre passeios e categorias
-- Data: 2026-08-09
-- Problema: A tabela trip_category_relations estava vazia, fazendo com que
-- nenhum passeio aparecesse nas páginas de categoria.
-- Esta migration limpa e recria TODAS as associações corretamente.

-- Categorias existentes (da migration 006):
-- aventura, passeios-de-barco, natureza, familia, noturno, cultural, romantico, esportes-aquaticos

START TRANSACTION;

-- Limpar relações existentes para evitar duplicatas
DELETE FROM trip_category_relations;

-- =====================================================
-- AVENTURA
-- Buggies, Quadriciclos, La Hacienda, Scape Park, Supreme Safari, Parasailing
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'aventura'
AND t.slug IN (
    'buggies-cenote-domitai',
    'quadriciclos-cenote',
    'la-hacienda-park',
    'scape-park-cenote',
    'supreme-safari',
    'parasailing'
);

-- =====================================================
-- PASSEIOS DE BARCO
-- Saona (todas), Isla Catalina (todas), Festa no Catamarã, Pesca em Alto Mar
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'passeios-de-barco'
AND t.slug IN (
    'saona-vip-mano-juan-lancha',
    'saona-premium-brasil-lancha-ida-e-volta',
    'saona-classica',
    'isla-catalina-snorkel',
    'isla-catalina-altos-de-chavon',
    'festa-no-catamara-party-boat',
    'pesca-em-alto-mar'
);

-- =====================================================
-- NATUREZA
-- Cenotes, Golfinhos, Seaquarium, Scuba Doo, Isla Catalina
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'natureza'
AND t.slug IN (
    'buggies-cenote-domitai',
    'quadriciclos-cenote',
    'nado-e-interacao-com-2-golfinhos',
    'nado-e-interacao-com-golfinho',
    'interacao-com-golfinho',
    'seaquarium',
    'scuba-doo-aventura-submarina',
    'isla-catalina-snorkel',
    'isla-catalina-altos-de-chavon',
    'la-hacienda-park'
);

-- =====================================================
-- FAMÍLIA
-- Passeios adequados para crianças (sem restrição de idade severa)
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'familia'
AND t.slug IN (
    'saona-classica',
    'saona-premium-brasil-lancha-ida-e-volta',
    'saona-vip-mano-juan-lancha',
    'isla-catalina-snorkel',
    'isla-catalina-altos-de-chavon',
    'interacao-com-golfinho',
    'nado-e-interacao-com-golfinho',
    'nado-e-interacao-com-2-golfinhos',
    'santo-domingo',
    'supreme-safari',
    'seaquarium'
);

-- =====================================================
-- NOTURNO
-- Coco Bongo (todas), Chic Cabaret
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'noturno'
AND t.slug IN (
    'coco-bongo-front-row',
    'coco-bongo-gold-member',
    'coco-bongo-open-bar',
    'chic-cabaret-restaurant'
);

-- =====================================================
-- CULTURAL
-- Santo Domingo, La Hacienda, Isla Catalina + Altos de Chavón, Samaná
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'cultural'
AND t.slug IN (
    'santo-domingo',
    'la-hacienda-park',
    'isla-catalina-altos-de-chavon',
    'samana-playa-rincon-city-tour-panoramico-cayo-levantado'
);

-- =====================================================
-- ROMÂNTICO
-- Saona VIP, Saona Premium, Chic Cabaret, Festa no Catamarã
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'romantico'
AND t.slug IN (
    'saona-vip-mano-juan-lancha',
    'saona-premium-brasil-lancha-ida-e-volta',
    'chic-cabaret-restaurant',
    'festa-no-catamara-party-boat'
);

-- =====================================================
-- ESPORTES AQUÁTICOS
-- Snorkel, Nado com golfinhos, Parasailing, Scuba Doo, Pesca, Seaquarium
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'esportes-aquaticos'
AND t.slug IN (
    'isla-catalina-snorkel',
    'isla-catalina-altos-de-chavon',
    'nado-e-interacao-com-2-golfinhos',
    'nado-e-interacao-com-golfinho',
    'interacao-com-golfinho',
    'parasailing',
    'scuba-doo-aventura-submarina',
    'pesca-em-alto-mar',
    'seaquarium',
    'festa-no-catamara-party-boat'
);

-- =====================================================
-- VERIFICAÇÃO: Passeios que ficaram sem categoria (fallback para aventura)
-- =====================================================
INSERT INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'aventura'
AND t.status = 'published'
AND t.id NOT IN (SELECT trip_id FROM trip_category_relations);

COMMIT;
