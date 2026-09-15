-- =============================================================
-- El Dorado Water Park (trip 75) + Dominicania (trip 76)
-- TEXTO + PREÇO. Estes dois passeios NÃO tinham pacote de preço,
-- então cada bloco CRIA o pacote e depois insere os preços.
--
-- FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- Rode cada bloco (El Dorado e Dominicania) DE UMA VEZ, pois o
-- INSERT de preço usa LAST_INSERT_ID() do pacote recém-criado.
--
-- Categorias: 1=Adulto  2=Criança  6=Infantil(cortesia)
-- =============================================================


-- #############################################################
-- EL DORADO WATER PARK  (trip_id 75)
-- #############################################################

-- 1) Texto
UPDATE trips SET
    duration = 8, duration_unit = 'hours', min_pax = 1,
    short_description = 'O maior parque aquático do Caribe. Dia inteiro de diversão para toda a família: piscina de ondas, tirolesa, tobogãs, rio lento, cenotes naturais e muito mais.',
    description = 'Diversão, adrenalina e momentos inesquecíveis para toda a família!\n\nParque aquático para todas as idades. Ideal para famílias com crianças, casais, grupos de amigos e amantes de aventura. Guia local em espanhol e inglês e suporte em português via WhatsApp da Punta Cana para Brasileiros.\n\nFunciona de terça a domingo. Sujeito a alterações operacionais e condições climáticas. Duração de dia inteiro (aproximadamente 8 horas).\n\nROTEIRO DO PASSEIO\n1. Saída do hotel em Punta Cana entre 08h00 e 09h00.\n2. Chegada ao El Dorado Water Park e acesso ao parque.\n3. Tempo livre para aproveitar todas as atrações: piscina com ondas e praia artificial, tirolesa, plataforma de salto em cenote, rio lento (Lazy River), caiaque em lago natural, tobogãs para toda a família (adultos e crianças), cenote natural Cañón Turquesa e espaços de descanso.\n4. Tempo livre para almoço (não incluído).\n5. Saída do parque no final da tarde (16h30).\n6. Retorno ao hotel em Punta Cana.\n\nA programação poderá sofrer alterações sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta).\nGestantes são permitidas no parque, mas possuem restrições em algumas atividades.\nCrianças têm restrições de acordo com a atividade, peso, altura e idade.\n\nO QUE LEVAR: roupa de banho, toalha, protetor solar, óculos de sol, boné ou chapéu, chinelo ou calçado aquático e dinheiro ou cartão para despesas extras.',
    includes = '["Transporte ida e volta","Entrada no El Dorado Water Park","Acesso ilimitado às atrações do parque","Áreas de descanso, vestiário e banheiro","Equipamentos para as atividades","Áreas infantis","Praia artificial","Espreguiçadeiras e guarda-sol nas áreas comuns"]',
    excludes = '["Cama balinesa","Toalhas","Alimentação e bebidas","Lockers (armários)","Fotos profissionais","Itens pessoais","Itens não especificados"]'
WHERE id = 75;

-- 2) Cria o pacote (não existia)
INSERT INTO trip_packages (trip_id, title, description, sort_order, status)
VALUES (75, 'Padrao', NULL, 0, 1);

-- 3) Preço do pacote recém-criado: adulto 129 / criança 4-11 69 / infantil (menor de 3) 0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(LAST_INSERT_ID(), 1, 129.00, NULL, 0, NULL),
(LAST_INSERT_ID(), 2, 69.00,  NULL, 0, NULL),
(LAST_INSERT_ID(), 6, 0.00,   NULL, 0, NULL);


-- #############################################################
-- DOMINICANIA  (trip_id 76)
-- Preço base = Explorer US$79. Os 3 pacotes (Explorer/Insider/VIP)
-- estão descritos no texto. Criança até 12 = cortesia.
-- #############################################################

-- 1) Texto
UPDATE trips SET
    duration = 4, duration_unit = 'hours', min_pax = 1,
    short_description = 'Experiência cultural imersiva e inesquecível: 14 experiências na cultura dominicana, com história, música, arte, esportes, gastronomia e degustações. Guias locais e transporte incluso.',
    description = 'Viva a verdadeira Cultura Dominicana! Uma experiência cultural imersiva e inesquecível.\n\nTipo de passeio: cultural e gastronômico — 14 experiências imersivas na cultura dominicana (crianças de até 12 anos não pagam). Guias locais em espanhol ou inglês disponíveis durante cada experiência. Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta); demais regiões, consultar.\n\nFunciona todos os dias, exceto terça-feira. Duração aproximada de 4h30.\n\nCOMO FUNCIONA O PASSEIO\n1. Saída do resort ou ponto de encontro no horário informado.\n2. Chegada ao Dominicania: recepção e início da experiência cultural.\n3. Experiências imersivas: 14 experiências culturais que contam a história, a música, artes, esportes, riquezas, gastronomia e as demais tradições dominicanas.\n4. Gastronomia e degustações: degustações, bebidas e pratos típicos de acordo com o pacote escolhido.\n5. Retorno: após aproximadamente 4h30 de experiência, retorno ao resort ou ponto de encontro.\n\nPACOTES DISPONÍVEIS (consultar valores)\n- EXPLORER (US$79): acesso às experiências, degustação de cacau e chocolate, degustação de rum premium, café dominicano, cerveja e snack (empanada de mandioca + fruta + doce).\n- INSIDER (US$97): todas as experiências, degustação de cacau e chocolate, degustação de 4 rótulos de rum premium, café dominicano, cerveja, prato típico dominicano, água ou refrigerante e guia local (espanhol ou inglês).\n- VIP (US$150): todas as experiências, degustação de cacau e chocolate, degustação de 4 rótulos de rum premium, café dominicano, cerveja, experiência culinária ancestral com 3 tempos, snack (empanada de mandioca + fruta + doce), bebida assinatura à base de rum, degustação de charuto, trufa de chocolate, souvenir surpresa e guia local (espanhol ou inglês).\n\nCrianças de até 12 anos: cortesia.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta). Demais regiões, consultar.\nCrianças de até 12 anos não pagam (cortesia).'
WHERE id = 76;

-- 2) Cria o pacote (não existia)
INSERT INTO trip_packages (trip_id, title, description, sort_order, status)
VALUES (76, 'Explorer', NULL, 0, 1);

-- 3) Preço do pacote recém-criado: adulto 79 (Explorer) / criança até 12 = 0
INSERT INTO trip_package_categories (package_id, traveler_category_id, price, sale_price, min_pax, max_pax) VALUES
(LAST_INSERT_ID(), 1, 79.00, NULL, 0, NULL),
(LAST_INSERT_ID(), 2, 0.00,  NULL, 0, NULL);


-- [CONFERÊNCIA] (opcional)
-- SELECT t.id, t.title, tp.id AS package_id, tpc.traveler_category_id, tpc.price
-- FROM trips t
-- JOIN trip_packages tp ON tp.trip_id = t.id
-- JOIN trip_package_categories tpc ON tpc.package_id = tp.id
-- WHERE t.id IN (75, 76)
-- ORDER BY t.id, tpc.traveler_category_id;
