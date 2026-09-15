-- =============================================================
-- CATÁLOGO OUTUBRO 2026 — CONTEÚDO (TEXTO) DE VÁRIOS PASSEIOS
-- Só campos de TEXTO em `trips`. NÃO toca em preço (ver arquivo
-- separado de preços). Cada bloco mira o id correto do passeio.
--
-- FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- =============================================================


-- ============ Isla Catalina com Snorkel (id 69) ============
UPDATE trips SET
    duration = 10, duration_unit = 'hours', min_pax = 1,
    short_description = 'Passeio de dia inteiro à Isla Catalina com mergulho de snorkel em um dos maiores recifes de coral do Caribe. Navegação de catamarã com festa, almoço buffet e open bar.',
    description = 'Começamos o dia navegando de catamarã até a impressionante e intocada Isla Catalina para desfrutar da natureza com uma fantástica experiência de mergulho com snorkel em um dos maiores recifes de coral do Caribe e República Dominicana, com dezenas de espécies marinhas.\n\nPasse um tempo em sua esplêndida praia de areia branca, onde você pode continuar sua exploração subaquática de corais e peixes, ou simplesmente relaxar, tomar sol e uma bebida gelada. Será servido um almoço tipo buffet de comida dominicana e o bar estará aberto com bebidas nacionais. Este é o puro sabor caribenho!\n\nFunciona às terças e quintas (consultar disponibilidade). Duração aproximada de 10 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Gestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte ida e volta","Guia turístico local: espanhol ou inglês","Festa e animação a bordo do catamarã","Almoço coletivo estilo buffet","Open bar: água, refrigerante e rum dominicano","Animação no catamarã","Mergulho e equipamento de snorkel"]',
    excludes = '["Fotos profissionais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 69;


-- ============ Isla Catalina + Altos de Chavon (id 70) ============
UPDATE trips SET
    duration = 10, duration_unit = 'hours', min_pax = 1,
    short_description = 'Passeio de dia inteiro à Isla Catalina com snorkel, visita aos Altos de Chavón e navegação de catamarã com festa. Almoço buffet e open bar inclusos.',
    description = 'Começaremos o dia com uma visita aos Altos de Chavón, uma encantadora recriação de uma vila mediterrânea, de inspiração europeia do século XVI, construída em pedra a partir de 1976 e inaugurada em 1982, situada bem acima do rio Chavón, em La Romana, onde foram feitas cenas de vários filmes famosos, incluindo Apocalypse Now (1979), The Lost City (2022), King Kong e Rambo II (1985).\n\nEm seguida, navegue de catamarã até a impressionante e intocada Isla Catalina para desfrutar da natureza com uma fantástica experiência de mergulho com snorkel em um dos maiores recifes de coral do Caribe, com dezenas de espécies marinhas.\n\nPasse um tempo em sua esplêndida praia de areia branca, de onde você pode continuar sua exploração subaquática de corais e peixes, ou simplesmente relaxar, tomar sol e tomar uma bebida gelada. Um almoço estilo buffet e open bar estarão inclusos. Este é o puro sabor caribenho!\n\nDuração aproximada de 10 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Uso obrigatório de colete salva-vidas (incluso) durante todo o percurso marítimo.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte ida e volta","Festa e animação a bordo do catamarã","Guia turístico local: espanhol ou inglês","Almoço coletivo estilo buffet","Open bar: água, refrigerante e rum dominicano","Passeio pelos Altos de Chavón","Animação no catamarã","Mergulho e equipamento de snorkel"]',
    excludes = '["Fotos profissionais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 70;


-- ============ Santo Domingo (City Tour) (id 67) ============
UPDATE trips SET
    duration = 10, duration_unit = 'hours', min_pax = 1,
    short_description = 'City tour de dia inteiro à cidade mais antiga do Novo Mundo. Caverna Los Tres Ojos, Zona Colonial, Catedral, Calle Las Damas e Faro a Colón. Almoço típico incluso.',
    description = 'Visite a cidade mais antiga do Novo Mundo, onde Cristóvão Colombo estabeleceu seu primeiro assentamento permanente após a descoberta das Américas, há mais de 500 anos. Junte-se a nós para absorver esta história extraordinária e rica.\n\nDesça dentro da milenar caverna indígena Los Tres Ojos, depois visite a primeira Catedral do novo mundo, chamada Santa Maria La Menor, o Alcázar, casa do filho de Colombo, Diego Colombo, a Calle Las Damas e uma seleção de outros monumentos e edifícios coloniais.\n\nVocê também terá uma vista panorâmica do enorme Faro a Colón, do centro da cidade e do Palácio Presidencial. Também há tempo para fazer compras em um colorido mercado de souvenirs, além de desfrutar de um saboroso almoço típico dominicano, estilo buffet, em um restaurante local.\n\nFunciona de terça a domingo (exceto em feriados nacionais). Duração aproximada de 10 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'RESTRIÇÕES: para entrar na catedral, é obrigatório usar roupas que cubram os ombros e as coxas. Caso não esteja vestido adequadamente, haverá tecidos disponíveis no local para cobrir o corpo ao visitar o monumento religioso.\n\nO QUE LEVAR E VESTIR: protetor solar e repelente, óculos de sol e chapéu ou boné, calçados e roupas confortáveis, dinheiro para compras e roupas apropriadas para visita a santuários religiosos.',
    includes = '["Transporte","Guia turístico local em espanhol ou inglês","Ingresso dos monumentos","Almoço típico dominicano estilo buffet, água e refrigerante","Parque Los 3 Ojos","Zona Colonial","Calle Las Damas","Vista panorâmica do Faro a Colón"]',
    excludes = '["Alimentação e bebidas","Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 67;


-- ============ Scape Park + Cenote (Blue Hole) (id 68) ============
UPDATE trips SET
    duration = 7, duration_unit = 'hours', min_pax = 1,
    short_description = 'Parque de aventuras com tirolesa, cavernas subterrâneas, Cenote Blue Hole, cachoeira, trilha e interação com animais. Diversão e adrenalina em Punta Cana.',
    description = 'Este mundo de sensações oferece desde aventuras emocionantes e cheias de adrenalina até experiências suaves e culturais. Deixe seus sentidos escaparem enquanto pratica tirolesa em um penhasco, explorando cavernas subterrâneas.\n\nÉ um parque cheio de sensações e aventuras emocionantes com muita adrenalina, experiências educativas e culturais. Não perca a oportunidade de descobrir os segredos que este mundo guarda!\n\nAlgumas das atividades incluídas: tirolesa, zip-line, Cenote Blue Hole, cachoeira, caverna, trilha, interação com macacos, aves e répteis.\n\nFunciona todos os dias. Duração aproximada de 7 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Gestantes não são permitidas. Não acessível a cadeirantes.\nNão são permitidos alimentos e bebidas trazidos de fora, bastões de selfie e drones no parque.\n\nO QUE LEVAR: cadeado (aluguel disponível no local), calçados aquáticos (obrigatório em algumas atividades), protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte ida e volta","Guia turístico local: espanhol ou inglês"]',
    excludes = '["Cadeado","Calçados aquáticos","Alimentação e bebidas","Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 68;


-- ============ Parasailing (id 66) ============
UPDATE trips SET
    duration = 1, duration_unit = 'hours', min_pax = 1,
    short_description = 'Voe sobre as praias de Bávaro em Punta Cana. Puxado por barco, você fica suspenso no céu por 8 a 10 minutos, com vista do mar cristalino e dos resorts.',
    description = 'Se você já sonhou em voar, chegou a hora desse sonho se tornar realidade!\n\nVenha voar sobre as deslumbrantes praias de Bávaro, em Punta Cana! No parasailing, você será puxado pelo nosso barco enquanto fica suspenso no céu por 8 a 10 minutos, vivendo uma experiência única e cheia de sensações incríveis. Não perca esta oportunidade exclusiva de apreciar, do alto, o mar cristalino, os coqueiros e os diversos resorts da Praia de Bávaro.\n\nFunciona todos os dias, a partir das 10h (a depender das condições do vento). Duração aproximada de 1 hora.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'RESTRIÇÕES: crianças menores de 6 anos não são permitidas. Não nos responsabilizamos por danos ou perdas de objetos ou equipamentos pessoais de registro de imagens durante a prática da atividade.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta)","Guia turístico local: espanhol ou inglês","Equipamento para a prática do esporte","Água"]',
    excludes = '["Alimentação e bebidas","Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 66;


-- ============ Scuba Doo (com Snorkel) (id 61) ============
UPDATE trips SET
    duration = 4, duration_unit = 'hours', min_pax = 1,
    short_description = 'Aventura de mergulho em moto subaquática pelo Oceano Atlântico. Sem experiência prévia; acompanhado por mergulhadores profissionais. Inclui navegação em barco com fundo de cristal e snorkel.',
    description = 'O Scuba Doo é uma aventura de mergulho que leva você a explorar algumas das áreas mais bonitas e preservadas do Oceano Atlântico. A bordo de uma de nossas motos subaquáticas, você vive a experiência de um verdadeiro mergulhador no fundo do mar. Durante o passeio, é possível observar de perto peixes coloridos, corais e até naufrágios.\n\nNão é necessário ter experiência prévia em mergulho ou pilotagem de scooter, pois o Scuba Doo foi projetado para ser seguro e fácil de usar por crianças a partir de 8 anos até adultos acima de 70 anos. É possível até utilizá-lo com óculos de grau. A aventura acontece a uma profundidade de 3 metros (9 pés) e é acompanhada o tempo todo por mergulhadores profissionais (Dive Masters).\n\nDurante a navegação até o local da atividade, admire o fundo do mar com uma visão privilegiada através do casco de cristal do barco.\n\nFunciona de segunda a sábado (sujeito a disponibilidade). Duração aproximada de 4 horas.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Gestantes não são permitidas. Não acessível a cadeirantes.\nCrianças menores de 7 anos não são permitidas.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte (ida e volta)","Navegação em barco com fundo de cristal","Mergulho com snorkel","Guia turístico local: espanhol ou inglês","Equipamento para a prática do esporte"]',
    excludes = '["Alimentação e bebidas","Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 61;


-- ============ Seaquarium (id 64) ============
UPDATE trips SET
    duration = 4, duration_unit = 'hours', min_pax = 1,
    short_description = 'Caminhada subaquática com capacete, avistando golfinhos, leão-marinho, tubarões e arraias. Não precisa saber nadar. Inclui piscina natural e festa a bordo do catamarã.',
    description = 'Esta excursão é uma forma fácil e divertida de explorar o mundo subaquático através da Caminhada Subaquática. Com um capacete conectado a uma entrada de ar na superfície, você caminha pelo fundo do mar, podendo até usar óculos de grau. Durante o passeio, você terá a oportunidade de avistar golfinhos, receber um beijo carinhoso de um leão-marinho e também poderá nadar com tubarões e arraias, além de apreciar recifes e uma grande variedade de peixes tropicais.\n\nNão é preciso saber nadar nem ter certificação, pois mergulhadores profissionais (Dive Masters) acompanham toda a atividade subaquática. A experiência também inclui um momento relaxante em uma piscina natural, com água na altura da cintura, em Punta Cana. Para encerrar, uma animada festa a bordo com petiscos, open bar de drinks, música e dança conduzida pela nossa equipe de animação.\n\nFunciona de segunda a sábado (sujeito a disponibilidade). Duração aproximada de 4 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'RESTRIÇÕES: gestantes podem participar de todas as atividades, exceto da Caminhada Subaquática. Não acessível a cadeirantes. Crianças só poderão realizar a atividade subaquática caso o capacete se ajuste perfeitamente.\n\nO QUE LEVAR: roupa de praia, protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta)","Festa e animação a bordo do catamarã","Petiscos","Open bar: água e drinks nacionais alcoólicos e não alcoólicos durante a festa a bordo do catamarã","Guia turístico local: espanhol ou inglês","Equipamento para a prática do esporte"]',
    excludes = '["Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 64;


-- ============ Festa no Catamara (Party Boat) (id 63) ============
UPDATE trips SET
    duration = 5, duration_unit = 'hours', min_pax = 1,
    short_description = 'Festa a bordo de catamarã pela costa de Punta Cana, com snorkel em recife de coral, open bar e parada na piscina natural de Bávaro.',
    description = 'Aproveite uma incrível festa a bordo de um catamarã pela costa de Punta Cana. Durante o passeio, faça snorkel em um recife de coral repleto de peixes coloridos e diferentes espécies marinhas, apreciando de perto a beleza do mundo subaquático. Depois, aproveite o open bar enquanto seguimos até a piscina natural de Bávaro-Punta Cana, onde você poderá saborear bons drinks com a água na altura da cintura, mesmo a centenas de metros da praia.\n\nEmbora seja uma atividade coletiva, também é uma excelente opção para celebrações privadas, como despedidas de solteiro, casamentos, aniversários, bodas e outros momentos especiais com amigos e familiares.\n\nFunciona todos os dias, em 3 horários (consultar). Sujeito a disponibilidade. Duração aproximada de 5 horas.\n\nImportante: se você estiver hospedado em resorts de Uvero Alto, Punta Cana ou Cap Cana, é necessário um grupo mínimo de 6 pessoas para que possamos oferecer transporte gratuito.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Gestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: roupa de praia, protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta)","Festa e animação a bordo do catamarã","Petiscos","Open bar: água e drinks nacionais alcoólicos e não alcoólicos","Guia turístico local: espanhol ou inglês","Equipamento para a prática do snorkel"]',
    excludes = '["Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 63;


-- ============ Pesca em Alto Mar (id 62) ============
UPDATE trips SET
    duration = 4, duration_unit = 'hours', min_pax = 1,
    short_description = 'Pesca de corrico em alto mar na região de Punta Cana. Marlins Brancos e Azuis são os principais alvos. Barco com capitão e tripulação, equipamentos profissionais e bebidas inclusos.',
    description = 'A região de Punta Cana é uma das áreas de pesca mais populares da República Dominicana e do Caribe, recebendo seu torneio anual de pesca em Cabeza de Toro e na Marina de Cap Cana. Os Marlins Brancos e Azuis são os principais alvos, podendo chegar a mais de 300 kg. Agora é a sua vez de tentar a captura!\n\nNossos barcos têm capacidade para 6, 7 ou 8 pescadores, além de 4 tripulantes. Esta é uma pesca de corrico, não de fundo — a técnica ideal para capturar os grandes peixes. Utilizamos equipamentos profissionais de pesca, iscas de qualidade e coletes salva-vidas. Embora tenhamos sucesso em 95% das partidas, lembre-se: trata-se de pesca, não de captura garantida. Não há garantia de que você pegará algum peixe.\n\nCALENDÁRIO DE PESCA\nAno todo: Mahi-mahi, Wahoo, Peixe Voador, Marlin Branco, Marlin Azul, entre outros.\nJaneiro, fevereiro e março: Mahi-mahi (75%), Wahoo (70%), peixes voadores (65%).\nAbril, maio e junho: Espadim Branco (80%), Espadim Azul (85%), Mahi-mahi (80%).\nJulho, agosto e setembro: Blue Marlin (70%), Mahi-mahi (85%), Wahoo (75%), Peixe voador (75%).\nOutubro, novembro e dezembro: Mahi-mahi (85%), Dourado (85%), Wahoo (80%).\n\nFunciona todos os dias, em 2 horários (consultar horário e disponibilidade). Duração aproximada de 4 horas.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'Gestantes não são permitidas. Não acessível a cadeirantes.\nCrianças menores de 10 anos não são permitidas.\n\nO QUE LEVAR: roupa de praia, protetor solar e repelente, óculos de sol, toalha de praia e boné ou chapéu.',
    includes = '["Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta)","Guia turístico local: espanhol ou inglês","Barco com GPS, capitão, operador de bordo, rádio comunicador e equipamentos exigidos pela marinha","Material de pesca, iscas e licença de pesca","Bebidas: água, rum e refrigerante"]',
    excludes = '["Alimentação","Fotos profissionais","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 62;


-- ============ Chic Cabaret e Restaurant (id 53) ============
UPDATE trips SET
    duration = 5, duration_unit = 'hours', min_pax = 1,
    short_description = 'Experiência que une gastronomia, música e emoção. Menu gourmet de 7 pratos e show com dançarinos, acrobatas e cantores, do rock ao pop, em uma noite inesquecível em Punta Cana.',
    description = 'Luzes, câmera, sabores e emoção em um só lugar! Chegou a hora do Chic Cabaret & Restaurant.\n\nPrepare-se para viver uma experiência completa, onde gastronomia, música e emoção se encontram. Brinde ao som das melhores músicas dos últimos tempos, delicie-se com um menu gourmet de 7 pratos e deixe-se envolver por dançarinos, acrobatas e cantores que dão vida a um show empolgante e cheio de energia.\n\nDurante o espetáculo, você será transportado pelos momentos mais marcantes da história musical — dos anos 50 até os dias atuais —, com ritmos latinos vibrantes, performances impressionantes e uma atmosfera sofisticada. Cada prato é um espetáculo à parte, cada música, uma viagem no tempo.\n\nPermita-se ser surpreendido por essa combinação única de sabores, arte e emoção — uma noite inesquecível em Punta Cana.\n\nSe você é vegano ou vegetariano, não se preocupe: temos opções para todos os gostos. Basta nos informar no momento da reserva.\n\nFunciona às terças, quintas, sextas e sábados. Sujeito à limitação de capacidade. Duração aproximada de 5 horas.\n\nROTEIRO\n- Check-in e Pré Party com open bar: 19h30\n- Jantar com open bar e show temático: das 20h20 às 23h\n- After Party com open bar: das 23h às 00h30',
    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',
    important_notes = 'RESTRIÇÕES E ORIENTAÇÕES: vestimenta elegante casual. Obrigatório uso de sapatos fechados. Chinelos não são permitidos. Em caso de usar shorts, deve utilizar sapatos sociais. NÃO permitido para menores de 18 anos.',
    includes = '["Transporte incluso em resorts na região de Bávaro e Punta Cana (ida e volta)","Pré Party","Jantar Surf & Turf de 7 pratos, incluindo entradas, prato principal e sobremesa","Open bar de bebidas premium e bar mixológico com coquetéis especiais","Show com 8 vozes, elenco de artistas e banda ao longo da noite"]',
    excludes = '["Garrafas de bebidas","Itens pessoais","Gorjetas (opcional)","Itens não especificados"]'
WHERE id = 53;
