-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: Buggy e Quadriciclo (passeios separados)
--   id 49 = Buggies + Cenote Domitai
--   id 50 = Quadriciclo + Cenote
--
-- Mesma rota/regras nos dois; a descrição só muda o veículo.
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço/pacote.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Confira o passo [A].
-- 3. Rode os dois UPDATE ([B] buggy id 49, [C] quadriciclo id 50).
-- =============================================================

-- [A] CONFERÊNCIA (não altera nada).
SELECT id, title FROM trips WHERE id IN (49, 50);


-- =============================================================
-- [B] BUGGIES + CENOTE DOMITAI  (id = 49)
-- =============================================================
UPDATE trips SET
    duration = 4,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Aventura de buggy pelas estradas de Macao, em Punta Cana. Trilhas e lama, banho em cenote dentro de caverna, Vila Taíno com show indígena e degustação de produtos típicos dominicanos.',

    description = 'Prepare-se para explorar as estradas de Macao, em Punta Cana, dirigindo um buggy com facilidade e muita diversão. Aproveite as paisagens deslumbrantes, percorra trilhas e lama, descubra e nade em uma caverna de águas azuis escondida no caminho e experimente produtos típicos dominicanos. Essa aventura vai render memórias inesquecíveis da sua viagem a Punta Cana.\n\nFunciona todos os dias, em 3 horários (consultar). Sujeito a disponibilidade. Duração aproximada de 4 horas.\n\nROTA\n- Conduza o buggy em uma caravana guiada.\n- Nade no cenote da nossa Caverna Indígena Iguanabona, com animação de DJ no cenote.\n- Visite a Vila Taíno, recriada historicamente, e assista ao show interativo dos indígenas no Parque Domitai.\n- Deguste café, chocolate, charuto, chá e a famosa mamajuana.\n\nA ordem do passeio poderá ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'RESTRIÇÕES:\nCrianças são permitidas somente a partir dos 6 anos de idade e devem estar acompanhadas pelos pais.\nNão acessível a cadeirantes.\nNão adequado para pessoas com dores e doenças nas costas e na coluna.\nGestantes não são permitidas.\n\nO QUE LEVAR: roupas e calçados confortáveis, protetor solar e repelente, lenço ou bandana, óculos de sol, toalha de praia, boné ou chapéu e dinheiro para fotos e despesas extras.',

    includes = '["Transporte ida e volta","Capacete","Guia turístico local"]',

    excludes = '["Fotos profissionais","Alimentação e bebidas","Itens não especificados"]'

WHERE id = 49;


-- =============================================================
-- [C] QUADRICICLO + CENOTE  (id = 50)
-- =============================================================
UPDATE trips SET
    duration = 4,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Aventura de quadriciclo pelas estradas de Macao, em Punta Cana. Trilhas e lama, banho em cenote dentro de caverna, Vila Taíno com show indígena e degustação de produtos típicos dominicanos.',

    description = 'Prepare-se para explorar as estradas de Macao, em Punta Cana, dirigindo um quadriciclo com facilidade e muita diversão. Aproveite as paisagens deslumbrantes, percorra trilhas e lama, descubra e nade em uma caverna de águas azuis escondida no caminho e experimente produtos típicos dominicanos. Essa aventura vai render memórias inesquecíveis da sua viagem a Punta Cana.\n\nFunciona todos os dias, em 3 horários (consultar). Sujeito a disponibilidade. Duração aproximada de 4 horas.\n\nROTA\n- Conduza o quadriciclo em uma caravana guiada.\n- Nade no cenote da nossa Caverna Indígena Iguanabona, com animação de DJ no cenote.\n- Visite a Vila Taíno, recriada historicamente, e assista ao show interativo dos indígenas no Parque Domitai.\n- Deguste café, chocolate, charuto, chá e a famosa mamajuana.\n\nA ordem do passeio poderá ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'RESTRIÇÕES:\nCrianças são permitidas somente a partir dos 6 anos de idade e devem estar acompanhadas pelos pais.\nNão acessível a cadeirantes.\nNão adequado para pessoas com dores e doenças nas costas e na coluna.\nGestantes não são permitidas.\n\nO QUE LEVAR: roupas e calçados confortáveis, protetor solar e repelente, lenço ou bandana, óculos de sol, toalha de praia, boné ou chapéu e dinheiro para fotos e despesas extras.',

    includes = '["Transporte ida e volta","Capacete","Guia turístico local"]',

    excludes = '["Fotos profissionais","Alimentação e bebidas","Itens não especificados"]'

WHERE id = 50;

-- [D] CONFERÊNCIA FINAL (opcional)
-- SELECT id, title, short_description, includes FROM trips WHERE id IN (49, 50);
