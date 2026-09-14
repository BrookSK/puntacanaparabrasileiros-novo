-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: Coco Bongo
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço/pacote.
--
-- ATENÇÃO: pode existir MAIS DE UM "Coco Bongo" no sistema (um por
-- pacote: Pista Regular, Pista Premium, Gold Member, Front Row).
-- Rode o SELECT do passo [A] para ver quantos são e seus ids.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Rode o SELECT [A] e me diga quantos Coco Bongo existem e os ids.
-- 3. No passo [B], troque o id do WHERE pelo id correto.
--    (Se houver vários passeios separados, avise que eu faço um
--     UPDATE para cada, com a descrição do pacote certo.)
-- =============================================================

-- [A] DESCUBRA OS IDs (não altera nada).
SELECT id, title FROM trips WHERE title LIKE '%oco Bongo%' ORDER BY id;

-- [B] ATUALIZAÇÃO DO CONTEÚDO (CONFIRME o id antes de rodar!)
UPDATE trips SET
    duration = 5,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'A casa de show mais famosa de Punta Cana e do Caribe. Shows inspirados na Broadway e em Las Vegas, performances circenses, balada e muita música. Duração aproximada de 5 horas.',

    description = 'O Coco Bongo é a casa de show mais famosa de Punta Cana e do Caribe, referência em diversão, espetáculos, shows, performances circenses, balada, boa música e gente bonita. Não faltam motivos para incluí-la na sua lista de passeios noturnos.\n\nOs shows são inspirados em grandes produções da Broadway e de Las Vegas, além de homenagens a estrelas da música e do cinema como O Máscara, Moulin Rouge, Chicago, Batman, Homem-Aranha, Madonna, Michael Jackson e The Beatles, entre outros, recriados de forma impressionante, combinando talento artístico, vídeos e recursos tecnológicos de última geração.\n\nFunciona de terça a domingo (sujeito a limitação de capacidade). Duração aproximada de 5 horas.\n\nPACOTES DISPONÍVEIS (consultar valores)\n- Pacote 1 — Pista Regular: transporte, pista, 1 snack surpresa e open bar de bebidas nacionais.\n- Pacote 2 — Pista Premium: transporte, pista, 1 snack surpresa, open bar de bebidas nacionais e open bar de bebidas premium.\n- Pacote 3 — Gold Member: transporte, camarote inferior frontal e lateral, entrada preferencial, 1 snack surpresa, open bar de bebidas nacionais e premium, e assentos reservados em área preferencial.\n- Pacote 4 — Front Row: transporte, camarote superior frontal e lateral, entrada preferencial, 1 snack surpresa, open bar de bebidas nacionais e premium, serviço personalizado e assentos reservados em área preferencial.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Entrada proibida para menores de 18 anos.\nGestantes são permitidas mediante assinatura de termo de responsabilidade.\nNão acessível a cadeirantes.',

    includes = '["Transporte","Entrada no Coco Bongo","1 snack surpresa","Open bar de bebidas nacionais","Show com performances e espetáculos"]',

    excludes = '["Gorjetas (opcional)","Itens não especificados","Bebidas premium (dependendo do pacote escolhido)"]'

WHERE id = 0;  -- ??  TROQUE pelo id do Coco Bongo (veja o passo [A])

-- [C] CONFERÊNCIA FINAL (opcional)
-- SELECT id, title, short_description, includes FROM trips WHERE id = 0;
