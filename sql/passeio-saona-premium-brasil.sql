-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: Saona Premium Brasil
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Confira o passo [A] abaixo para achar o passeio certo.
-- 3. Rode o UPDATE do passo [B].
-- =============================================================

-- -------------------------------------------------------------
-- [A] CONFERÊNCIA: veja se o passeio existe com esse título.
--     Rode este SELECT primeiro. Ele NÃO altera nada.
--     Anote o "id" que aparecer (você pode usar ele no lugar do
--     título, se preferir — é mais seguro).
-- -------------------------------------------------------------
SELECT id, title FROM trips WHERE title LIKE '%Saona%';

-- -------------------------------------------------------------
-- [B] ATUALIZAÇÃO DO CONTEÚDO
--     Se o título no banco for exatamente "Saona Premium Brasil",
--     pode rodar como está. Se for outro, troque o valor do
--     WHERE (ou use: WHERE id = <numero_do_id_do_passo_A>).
-- -------------------------------------------------------------
UPDATE trips SET
    duration = 10,
    duration_unit = 'hours',
    min_pax = 1,
    max_pax = 24,

    short_description = 'Passeio de dia inteiro à Isla Saona, exclusivo para brasileiros. Duas praias paradisíacas, piscina natural com estrelas-do-mar, almoço típico dominicano e open bar premium. Guia em português durante todo o passeio.',

    description = 'Passeio privado de dia inteiro (aproximadamente 10 horas) à Isla Saona, exclusivo para brasileiros — máximo de 24 pessoas por lancha (grupos maiores, consultar). Idealizado e operado exclusivamente pela Punta Cana para Brasileiros.\n\nFunciona às terças, sextas e domingos, podendo haver alteração conforme o clima. Sujeito a formação de quórum ou limitação de capacidade.\n\nGuia turístico em português durante todo o passeio.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel entre 7h00 e 8h00. Viagem de aproximadamente 1h até a Marina de Bayahibe.\n2. Percurso até a Isla Saona em lancha rápida: cerca de 35 minutos navegando pelo Mar do Caribe.\n3. 1ª parada — Playa El Toro / Flamenco (cerca de 2 horas): praia virgem e sem estrutura, ambiente tranquilo e exclusivo. Petiscos. Open bar: água, refrigerante, cerveja Heineken, cerveja Presidente e rum dominicano.\n4. 2ª parada — Playa Abanico (cerca de 2 horas): praia com estrutura completa (banheiros e espreguiçadeiras). Almoço típico dominicano, exclusivo para o grupo, em estilo buffet à vontade. Open bar: água, refrigerante, rum dominicano, cerveja Heineken, Presidente e Hollandia.\n5. 3ª parada — Piscina Natural da Playa Palmilla (cerca de 35 minutos): águas rasas, mornas e cristalinas. Observação respeitosa das estrelas-do-mar. Open bar: água, refrigerante, cerveja Heineken, cerveja Presidente e rum dominicano. Brinde com espumante.\n6. Percurso de volta até a Marina de Bayahibe em lancha rápida: cerca de 15 minutos. Chegada na Marina entre 16h00 e 17h00.\n7. Chegada ao hotel entre 18h00 e 19h00.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta). Demais regiões, consultar.\nUso obrigatório de colete salva-vidas (incluso) durante todo o percurso de lancha.\nNão é permitido consumo de bebidas alcoólicas durante o percurso de lancha.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia, boné ou chapéu e itens pessoais.',

    includes = '["Guia em português","Transporte terrestre (ida e volta)","Transporte marítimo em lancha rápida","2 praias + piscina natural e brinde com espumante","Petiscos na primeira praia","Almoço privado na segunda praia (salada fria, pão, massa quente e fria, arroz, peixe, carne de porco, frango e frutas)","Open bar premium nas paradas (cerveja Heineken, cerveja Presidente, refrigerante, suco e rum premium dominicano)"]',

    excludes = '["Fotos profissionais","Itens pessoais","Gorjetas (opcional)"]'

WHERE title = 'Saona Premium Brasil';

-- -------------------------------------------------------------
-- [C] CONFERÊNCIA FINAL (opcional): rode para ver o resultado.
-- -------------------------------------------------------------
SELECT id, title, short_description, meeting_point, includes, excludes
FROM trips WHERE title = 'Saona Premium Brasil';
