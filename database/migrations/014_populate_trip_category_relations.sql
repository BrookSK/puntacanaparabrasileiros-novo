-- Migration: Popular relações entre passeios e categorias
-- Data: 2026-08-08
-- Associa os passeios existentes às categorias baseado no conteúdo

-- Aventura (slug: aventura) → Buggies, Quadriciclos
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'aventura'
AND (t.slug LIKE '%buggi%' OR t.slug LIKE '%quadri%' OR t.slug LIKE '%zipline%' OR t.slug LIKE '%tirolesa%');

-- Passeios de Barco (slug: passeios-de-barco) → Saona, Catamaran, Lancha
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'passeios-de-barco'
AND (t.slug LIKE '%saona%' OR t.slug LIKE '%catamaran%' OR t.slug LIKE '%lancha%' OR t.slug LIKE '%barco%' OR t.slug LIKE '%premium-brasil%');

-- Natureza (slug: natureza) → Cenote, Golfinhos, Nado
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'natureza'
AND (t.slug LIKE '%cenote%' OR t.slug LIKE '%golfinho%' OR t.slug LIKE '%nado%' OR t.slug LIKE '%interacao%');

-- Família (slug: familia) → Todos que são family-friendly
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'familia'
AND (t.slug LIKE '%buggi%' OR t.slug LIKE '%quadri%' OR t.slug LIKE '%saona%' OR t.slug LIKE '%golfinho%' OR t.slug LIKE '%catamaran%');

-- Noturno (slug: noturno) → Coco Bongo, Chic Cabaret
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'noturno'
AND (t.slug LIKE '%coco-bongo%' OR t.slug LIKE '%chic%' OR t.slug LIKE '%cabaret%');

-- Cultural (slug: cultural) → Santo Domingo, Hacienda
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'cultural'
AND (t.slug LIKE '%santo-domingo%' OR t.slug LIKE '%hacienda%' OR t.slug LIKE '%fazenda%');

-- Romântico (slug: romantico) → Saona VIP, Catamaran
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'romantico'
AND (t.slug LIKE '%vip%' OR t.slug LIKE '%premium%' OR t.slug LIKE '%sunset%');

-- Esportes Aquáticos (slug: esportes-aquaticos) → Snorkel, Nado, Golfinhos
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'esportes-aquaticos'
AND (t.slug LIKE '%snorkel%' OR t.slug LIKE '%golfinho%' OR t.slug LIKE '%nado%' OR t.slug LIKE '%interacao%' OR t.slug LIKE '%mergulho%');

-- Fallback: passeios sem categoria recebem "Aventura" como default
INSERT IGNORE INTO trip_category_relations (trip_id, category_id)
SELECT t.id, tc.id FROM trips t, trip_categories tc
WHERE tc.slug = 'aventura'
AND t.id NOT IN (SELECT trip_id FROM trip_category_relations)
AND t.status = 'published';
