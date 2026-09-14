-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: 3 passeios de GOLFINHOS
--   1) Interação com 1 Golfinho
--   2) Interação e Nado com 1 Golfinho
--   3) Interação e Nado com 2 Golfinhos
--
-- Informações gerais IGUAIS nos três; o que muda é a descrição do
-- programa e a lista "INCLUI" (atividades de cada um).
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço/pacote.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Rode o SELECT do passo [A]. Anote o id de CADA um dos 3 passeios.
-- 3. Nos passos [B], [C] e [D], troque o id do WHERE pelo id correto
--    de cada passeio (os "?? " marcam onde trocar).
-- 4. Rode os três UPDATE.
-- =============================================================

-- [A] DESCUBRA OS IDs (não altera nada).
SELECT id, title FROM trips WHERE title LIKE '%olfinh%' ORDER BY id;


-- =============================================================
-- [B] INTERAÇÃO COM 1 GOLFINHO  (sem nado)
--     Troque o id abaixo (?? ) pelo id desse passeio.
-- =============================================================
UPDATE trips SET
    duration = 4,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Interação com 1 golfinho em santuário natural, com instruções de treinadores. Meio período, com transporte incluso a partir de Bávaro e Punta Cana. Não inclui nado.',

    description = 'Passeio de meio período (aproximadamente 4 horas) de interação com 1 golfinho em santuário natural. Funciona todos os dias, em 3 horários (consultar), sujeito a disponibilidade.\n\nEste programa é de INTERAÇÃO (não inclui nado) e contempla: aperto de mão, beijo e abraço, dança, orquestra e carinho na barriga.\n\nTipo de passeio: coletivo e compartilhado, com várias nacionalidades (passeio terceirizado). Guia local em espanhol e inglês.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel até o santuário natural dos golfinhos.\n2. Instruções de segurança com os treinadores.\n3. Interação guiada com o golfinho (conforme programa escolhido).\n4. Seleção e aquisição de fotos (conforme preços do local).\n5. Retorno ao hotel.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta).\nUso obrigatório de colete salva-vidas (incluso) durante toda a atividade na água.\nGestantes: até 12 semanas, permitidas apenas na interação; não permitidas no nado.\nNão acessível a cadeirantes.\n\nINFORMAÇÕES GERAIS:\nNão é permitido o uso de equipamentos pessoais para fotos e vídeos. O local conta com fotógrafos profissionais exclusivos para comercialização de fotos.\nCrianças devem saber nadar e estar acompanhadas por um adulto.\nAtividade sujeita a restrição de idade e altura (consultar).\n\nO QUE LEVAR: protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',

    includes = '["Transporte terrestre (ida e volta)","Instruções de segurança com treinadores","Interação com 1 golfinho","Aperto de mão","Beijo e abraço","Dança","Orquestra","Carinho na barriga"]',

    excludes = '["Fotos profissionais","Alimentos e bebidas","Gorjetas (opcional)","Nado com golfinho (não incluso neste programa)","Itens não especificados no mar ou na piscina"]'

WHERE id = 65;  -- ??  TROQUE pelo id do "Interação com 1 Golfinho"


-- =============================================================
-- [C] INTERAÇÃO E NADO COM 1 GOLFINHO
--     Troque o id abaixo (?? ) pelo id desse passeio.
-- =============================================================
UPDATE trips SET
    duration = 4,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Interação e nado com 1 golfinho em santuário natural, com instruções de treinadores. Meio período, com transporte incluso a partir de Bávaro e Punta Cana.',

    description = 'Passeio de meio período (aproximadamente 4 horas) de interação e nado com 1 golfinho em santuário natural. Funciona todos os dias, em 3 horários (consultar), sujeito a disponibilidade.\n\nEste programa contempla: aperto de mão, beijo e abraço, dança, orquestra, carinho na barriga, empurrão, reboque dorsal e nado com 1 golfinho.\n\nTipo de passeio: coletivo e compartilhado, com várias nacionalidades (passeio terceirizado). Guia local em espanhol e inglês.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel até o santuário natural dos golfinhos.\n2. Instruções de segurança com os treinadores.\n3. Interação guiada e nado com o golfinho (conforme programa escolhido).\n4. Seleção e aquisição de fotos (conforme preços do local).\n5. Retorno ao hotel.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta).\nUso obrigatório de colete salva-vidas (incluso) durante toda a atividade na água.\nGestantes: até 12 semanas, permitidas apenas na interação; não permitidas no nado.\nNão acessível a cadeirantes.\n\nINFORMAÇÕES GERAIS:\nNão é permitido o uso de equipamentos pessoais para fotos e vídeos. O local conta com fotógrafos profissionais exclusivos para comercialização de fotos.\nCrianças devem saber nadar e estar acompanhadas por um adulto.\nAtividade sujeita a restrição de idade e altura (consultar).\n\nO QUE LEVAR: protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',

    includes = '["Transporte terrestre (ida e volta)","Instruções de segurança com treinadores","Interação com golfinho","Aperto de mão","Beijo e abraço","Dança","Orquestra","Carinho na barriga","Empurrão","Reboque dorsal","Nado com 1 golfinho"]',

    excludes = '["Fotos profissionais","Alimentos e bebidas","Gorjetas (opcional)","Itens não especificados no mar ou na piscina"]'

WHERE id = 0;  -- ??  TROQUE pelo id do "Interação e Nado com 1 Golfinho"


-- =============================================================
-- [D] INTERAÇÃO E NADO COM 2 GOLFINHOS
--     Troque o id abaixo (?? ) pelo id desse passeio.
-- =============================================================
UPDATE trips SET
    duration = 4,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Interação e nado com 2 golfinhos em santuário natural, com instruções de treinadores. Meio período, com transporte incluso a partir de Bávaro e Punta Cana.',

    description = 'Passeio de meio período (aproximadamente 4 horas) de interação e nado com 2 golfinhos em santuário natural. Funciona todos os dias, em 3 horários (consultar), sujeito a disponibilidade.\n\nEste programa é o mais completo e contempla: aperto de mão, beijo e abraço, dança, orquestra, carinho na barriga, empurrão, reboque dorsal, nado com 1 golfinho, nado com 2 golfinhos, passeio dorsal, peck e shake.\n\nTipo de passeio: coletivo e compartilhado, com várias nacionalidades (passeio terceirizado). Guia local em espanhol e inglês.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel até o santuário natural dos golfinhos.\n2. Instruções de segurança com os treinadores.\n3. Interação guiada e nado com os golfinhos (conforme programa escolhido).\n4. Seleção e aquisição de fotos (conforme preços do local).\n5. Retorno ao hotel.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta).\nUso obrigatório de colete salva-vidas (incluso) durante toda a atividade na água.\nGestantes: até 12 semanas, permitidas apenas na interação; não permitidas no nado.\nNão acessível a cadeirantes.\n\nINFORMAÇÕES GERAIS:\nNão é permitido o uso de equipamentos pessoais para fotos e vídeos. O local conta com fotógrafos profissionais exclusivos para comercialização de fotos.\nCrianças devem saber nadar e estar acompanhadas por um adulto.\nAtividade sujeita a restrição de idade e altura (consultar).\n\nO QUE LEVAR: protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',

    includes = '["Transporte terrestre (ida e volta)","Instruções de segurança com treinadores","Interação com golfinhos","Aperto de mão","Beijo e abraço","Dança","Orquestra","Carinho na barriga","Empurrão","Reboque dorsal","Nado com 1 golfinho","Nado com 2 golfinhos","Passeio dorsal","Peck","Shake"]',

    excludes = '["Fotos profissionais","Alimentos e bebidas","Gorjetas (opcional)","Itens não especificados no mar ou na piscina"]'

WHERE id = 0;  -- ??  TROQUE pelo id do "Interação e Nado com 2 Golfinhos"


-- [E] CONFERÊNCIA FINAL (opcional): troque os ids pelos corretos.
-- SELECT id, title, short_description, includes FROM trips WHERE id IN (65, 0, 0);
