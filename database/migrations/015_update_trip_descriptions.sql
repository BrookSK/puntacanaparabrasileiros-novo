-- Migration: Atualizar descrições dos passeios com conteúdo completo do site antigo
-- Data: 2026-08-08

-- Buggies + Cenote Domitai
UPDATE trips SET short_description = 'Prepare-se para uma aventura emocionante sobre quatro rodas pelas estradas e trilhas da região de Macao, em Punta Cana! Com paisagens de tirar o fôlego, lama, cavernas escondidas e elementos culturais. O passeio oferece uma experiência vibrante para quem busca adrenalina e diversão. Você poderá nadar em um cenote de águas cristalinas e visitar a Vila Taína para uma imersão na cultura local.

A ordem do passeio poderá ser alterada sem aviso prévio.

Gestantes não são permitidas.

O que levar:

• Roupas e calçados confortáveis
• Roupa de praia
• Óculos de sol e bandana
• Protetor solar
• Repelente
• Roupa de banho
• Toalha de banho
• Dinheiro para fotos'
WHERE slug = 'buggies-cenote-domitai';

-- Destaques do Buggies + Cenote Domitai
DELETE FROM trip_itinerary WHERE trip_id = (SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1);

INSERT INTO trip_itinerary (trip_id, day_number, title, description, sort_order) VALUES
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Conduza o veículo escolhido em uma caravana guiada', NULL, 1),
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Trilha em meio à natureza com emoção e lama', NULL, 2),
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Nade no Cenote de nossa Caverna Indígena Iguanabona', NULL, 3),
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Visite a Vila Taíno, recriada historicamente, e assista ao show interativo dos indígenas no Parque Domitai', NULL, 4),
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Experimente café, chocolate, charuto, chá e a famosa mamajuana', NULL, 5),
((SELECT id FROM trips WHERE slug = 'buggies-cenote-domitai' LIMIT 1), 1, 'Gestantes não são permitidas', NULL, 6);
