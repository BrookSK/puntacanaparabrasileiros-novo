-- =============================================================
-- ATUALIZAÇÃO: Isla Catalina e Chavón (com Snorkel)
-- Conteúdo (texto) + PREÇOS, com base na página do catálogo.
--
-- Preços: Adulto US$119 | Criança 4-11 US$59 | Menor de 4 anos: cortesia (0)
-- Categorias: 1=Adulto  2=Criança  6=Infantil
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Rode o SELECT [A] para achar o ID do passeio e o package_id.
-- 3. Troque os "?? " (trip id e package_id) nos passos [B] e [C].
-- 4. Rode os UPDATE.
-- =============================================================

-- [A] DESCUBRA OS IDs (não altera nada).
--     Mostra o passeio e o pacote de preço dele.
SELECT t.id AS trip_id, t.title, tp.id AS package_id, tp.title AS pacote
FROM trips t
LEFT JOIN trip_packages tp ON tp.trip_id = t.id
WHERE t.title LIKE '%atalina%' OR t.title LIKE '%havon%' OR t.title LIKE '%havón%';


-- =============================================================
-- [B] CONTEÚDO (TEXTO) — troque o id do WHERE pelo trip_id correto
-- =============================================================
UPDATE trips SET
    duration = 10,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Passeio de dia inteiro à Isla Catalina com mergulho de snorkel, visita aos Altos de Chavón e navegação de catamarã com festa e animação. Almoço buffet e open bar inclusos.',

    description = 'Começaremos o dia com uma visita aos Altos de Chavón, uma encantadora recriação de uma vila mediterrânea, de inspiração europeia do século XVI, construída em pedra a partir de 1976 e inaugurada em 1982, situada bem acima do rio Chavón, em La Romana, onde foram feitas cenas de vários filmes famosos, incluindo Apocalypse Now (1979), The Lost City (2022), King Kong e Rambo II (1985).\n\nEm seguida, navegue de catamarã até a impressionante e intocada Isla Catalina para desfrutar da natureza com uma fantástica experiência de mergulho com snorkel em um dos maiores recifes de coral do Caribe, com dezenas de espécies marinhas.\n\nPasse um tempo em sua esplêndida praia de areia branca, de onde você pode continuar sua exploração subaquática de corais e peixes, ou simplesmente relaxar, tomar sol e tomar uma bebida gelada. Um almoço estilo buffet e open bar estarão inclusos. Este é o puro sabor caribenho!\n\nFunciona às terças, quartas e sextas (consultar disponibilidade). Duração aproximada de 10 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Uso obrigatório de colete salva-vidas (incluso) durante todo o percurso marítimo.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia e boné ou chapéu.',

    includes = '["Transporte ida e volta","Festa e animação a bordo do catamarã","Guia turístico local: espanhol ou inglês","Almoço coletivo estilo buffet","Open bar: água, refrigerante e rum dominicano","Passeio pelos Altos de Chavón","Animação no catamarã","Mergulho e equipamento de snorkel","Visita aos Altos de Chavón"]',

    excludes = '["Fotos profissionais","Gorjetas (opcional)","Itens não especificados"]'

WHERE id = 0;  -- ??  TROQUE pelo trip_id do "Isla Catalina e Chavón" (veja passo [A])


-- =============================================================
-- [C] PREÇOS — troque o package_id pelo valor do passo [A]
--     Adulto 119 | Criança 59 | Infantil (menor de 4) 0
-- =============================================================
UPDATE trip_package_categories SET price = 119.00 WHERE package_id = 0 AND traveler_category_id = 1;  -- ?? package_id
UPDATE trip_package_categories SET price = 59.00  WHERE package_id = 0 AND traveler_category_id = 2;  -- ?? package_id
UPDATE trip_package_categories SET price = 0.00   WHERE package_id = 0 AND traveler_category_id = 6;  -- ?? package_id

-- [D] CONFERÊNCIA FINAL (opcional): troque os ids
-- SELECT id, title, short_description FROM trips WHERE id = 0;
-- SELECT package_id, traveler_category_id, price FROM trip_package_categories WHERE package_id = 0;
