-- Migration: Criar tabelas de tags para passeios
-- Data: 2026-08-09
-- Cobre: Destino, Atividades, Tags (filtros da barra lateral)

START TRANSACTION;

-- =====================================================
-- TABELA: trip_tags
-- Tags com tipo (destino, atividade, tag) para filtros da sidebar
-- =====================================================
CREATE TABLE IF NOT EXISTS `trip_tags` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `type` ENUM('destino','atividade','tag') NOT NULL DEFAULT 'tag',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_trip_tags_slug_type` (`slug`, `type`),
    KEY `idx_trip_tags_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABELA: trip_tag_relations
-- Relação N:N entre trips e tags
-- =====================================================
CREATE TABLE IF NOT EXISTS `trip_tag_relations` (
    `trip_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`trip_id`, `tag_id`),
    CONSTRAINT `fk_ttr_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ttr_tag` FOREIGN KEY (`tag_id`) REFERENCES `trip_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- POPULAR: Destinos
-- =====================================================
INSERT INTO trip_tags (name, slug, type, sort_order) VALUES
('Punta Cana', 'punta-cana', 'destino', 1),
('Bayahibe', 'bayahibe', 'destino', 2),
('Samaná', 'samana', 'destino', 3),
('Santo Domingo', 'santo-domingo', 'destino', 4);

-- =====================================================
-- POPULAR: Atividades
-- =====================================================
INSERT INTO trip_tags (name, slug, type, sort_order) VALUES
('Snorkel', 'snorkel', 'atividade', 1),
('Mergulho', 'mergulho', 'atividade', 2),
('Aventura', 'aventura', 'atividade', 3),
('Degustação de produtos locais', 'degustacao', 'atividade', 4),
('Dirigir pelas trilhas de Macao', 'trilhas-macao', 'atividade', 5),
('Ilhas', 'ilhas', 'atividade', 6),
('Nado com golfinhos', 'nado-golfinhos', 'atividade', 7),
('Show e espetáculo', 'show-espetaculo', 'atividade', 8),
('Pesca esportiva', 'pesca-esportiva', 'atividade', 9),
('Voo panorâmico', 'voo-panoramico', 'atividade', 10);

-- =====================================================
-- POPULAR: Tags
-- =====================================================
INSERT INTO trip_tags (name, slug, type, sort_order) VALUES
('Adequado para crianças', 'adequado-para-criancas', 'tag', 1),
('Amigo da Natureza', 'amigo-da-natureza', 'tag', 2),
('Baladas', 'baladas', 'tag', 3),
('Passeio em Grupo', 'passeio-em-grupo', 'tag', 4),
('Somente adultos', 'somente-adultos', 'tag', 5),
('Vida Noturna', 'vida-noturna', 'tag', 6),
('Romântico', 'romantico', 'tag', 7);

-- =====================================================
-- ASSOCIAR: Destinos aos passeios
-- =====================================================

-- Punta Cana (maioria)
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'punta-cana' AND tt.type = 'destino'
AND t.slug IN (
    'buggies-cenote-domitai', 'quadriciclos-cenote', 'la-hacienda-park',
    'chic-cabaret-restaurant', 'coco-bongo-front-row', 'coco-bongo-gold-member',
    'coco-bongo-open-bar', 'nado-e-interacao-com-2-golfinhos', 'nado-e-interacao-com-golfinho',
    'interacao-com-golfinho', 'supreme-safari', 'scuba-doo-aventura-submarina',
    'pesca-em-alto-mar', 'festa-no-catamara-party-boat', 'seaquarium',
    'parasailing', 'scape-park-cenote', 'saona-classica',
    'saona-premium-brasil-lancha-ida-e-volta'
);

-- Bayahibe
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'bayahibe' AND tt.type = 'destino'
AND t.slug IN (
    'saona-vip-mano-juan-lancha', 'saona-classica',
    'saona-premium-brasil-lancha-ida-e-volta',
    'isla-catalina-snorkel', 'isla-catalina-altos-de-chavon'
);

-- Samaná
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'samana' AND tt.type = 'destino'
AND t.slug = 'samana-playa-rincon-city-tour-panoramico-cayo-levantado';

-- Santo Domingo
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'santo-domingo' AND tt.type = 'destino'
AND t.slug = 'santo-domingo';

-- =====================================================
-- ASSOCIAR: Atividades aos passeios
-- =====================================================

-- Snorkel
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'snorkel' AND tt.type = 'atividade'
AND t.slug IN ('isla-catalina-snorkel', 'isla-catalina-altos-de-chavon', 'seaquarium', 'scuba-doo-aventura-submarina');

-- Mergulho
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'mergulho' AND tt.type = 'atividade'
AND t.slug IN ('scuba-doo-aventura-submarina', 'seaquarium');

-- Aventura
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'aventura' AND tt.type = 'atividade'
AND t.slug IN ('buggies-cenote-domitai', 'quadriciclos-cenote', 'la-hacienda-park', 'scape-park-cenote', 'supreme-safari', 'parasailing');

-- Degustação
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'degustacao' AND tt.type = 'atividade'
AND t.slug IN ('buggies-cenote-domitai', 'quadriciclos-cenote', 'supreme-safari', 'la-hacienda-park');

-- Dirigir pelas trilhas de Macao
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'trilhas-macao' AND tt.type = 'atividade'
AND t.slug IN ('buggies-cenote-domitai', 'quadriciclos-cenote');

-- Ilhas
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'ilhas' AND tt.type = 'atividade'
AND t.slug IN ('isla-catalina-snorkel', 'isla-catalina-altos-de-chavon', 'saona-vip-mano-juan-lancha', 'saona-premium-brasil-lancha-ida-e-volta', 'saona-classica', 'samana-playa-rincon-city-tour-panoramico-cayo-levantado');

-- Nado com golfinhos
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'nado-golfinhos' AND tt.type = 'atividade'
AND t.slug IN ('nado-e-interacao-com-2-golfinhos', 'nado-e-interacao-com-golfinho', 'interacao-com-golfinho');

-- Show e espetáculo
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'show-espetaculo' AND tt.type = 'atividade'
AND t.slug IN ('coco-bongo-front-row', 'coco-bongo-gold-member', 'coco-bongo-open-bar', 'chic-cabaret-restaurant');

-- Pesca esportiva
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'pesca-esportiva' AND tt.type = 'atividade'
AND t.slug = 'pesca-em-alto-mar';

-- Voo panorâmico
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'voo-panoramico' AND tt.type = 'atividade'
AND t.slug = 'parasailing';

-- =====================================================
-- ASSOCIAR: Tags aos passeios
-- =====================================================

-- Adequado para crianças
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'adequado-para-criancas' AND tt.type = 'tag'
AND t.slug IN (
    'saona-classica', 'saona-premium-brasil-lancha-ida-e-volta', 'saona-vip-mano-juan-lancha',
    'isla-catalina-snorkel', 'isla-catalina-altos-de-chavon', 'interacao-com-golfinho',
    'nado-e-interacao-com-golfinho', 'nado-e-interacao-com-2-golfinhos',
    'santo-domingo', 'supreme-safari', 'seaquarium'
);

-- Amigo da Natureza
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'amigo-da-natureza' AND tt.type = 'tag'
AND t.slug IN (
    'buggies-cenote-domitai', 'quadriciclos-cenote', 'la-hacienda-park',
    'scape-park-cenote', 'isla-catalina-snorkel', 'isla-catalina-altos-de-chavon',
    'nado-e-interacao-com-2-golfinhos', 'nado-e-interacao-com-golfinho',
    'interacao-com-golfinho', 'seaquarium', 'scuba-doo-aventura-submarina',
    'samana-playa-rincon-city-tour-panoramico-cayo-levantado', 'supreme-safari',
    'saona-classica', 'saona-premium-brasil-lancha-ida-e-volta', 'saona-vip-mano-juan-lancha'
);

-- Baladas
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'baladas' AND tt.type = 'tag'
AND t.slug IN ('coco-bongo-front-row', 'coco-bongo-gold-member', 'coco-bongo-open-bar', 'festa-no-catamara-party-boat');

-- Passeio em Grupo
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'passeio-em-grupo' AND tt.type = 'tag'
AND t.slug IN (
    'saona-classica', 'saona-premium-brasil-lancha-ida-e-volta', 'supreme-safari',
    'festa-no-catamara-party-boat', 'samana-playa-rincon-city-tour-panoramico-cayo-levantado',
    'santo-domingo', 'isla-catalina-snorkel', 'isla-catalina-altos-de-chavon',
    'buggies-cenote-domitai', 'quadriciclos-cenote', 'la-hacienda-park',
    'scape-park-cenote', 'saona-vip-mano-juan-lancha'
);

-- Somente adultos
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'somente-adultos' AND tt.type = 'tag'
AND t.slug IN ('coco-bongo-front-row', 'coco-bongo-gold-member', 'coco-bongo-open-bar', 'chic-cabaret-restaurant');

-- Vida Noturna
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'vida-noturna' AND tt.type = 'tag'
AND t.slug IN ('coco-bongo-front-row', 'coco-bongo-gold-member', 'coco-bongo-open-bar', 'chic-cabaret-restaurant');

-- Romântico
INSERT INTO trip_tag_relations (trip_id, tag_id)
SELECT t.id, tt.id FROM trips t, trip_tags tt
WHERE tt.slug = 'romantico' AND tt.type = 'tag'
AND t.slug IN ('saona-vip-mano-juan-lancha', 'saona-premium-brasil-lancha-ida-e-volta', 'chic-cabaret-restaurant', 'festa-no-catamara-party-boat');

COMMIT;
