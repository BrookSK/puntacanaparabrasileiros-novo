-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: Saona VIP – Mano Juan  (id = 51)
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Rode o SELECT do passo [A] para confirmar o passeio.
-- 3. Rode o UPDATE do passo [B].
-- =============================================================

-- [A] CONFERÊNCIA (não altera nada). Deve mostrar "Saona VIP Mano Juan - Lancha".
SELECT id, title FROM trips WHERE id = 51;

-- [B] ATUALIZAÇÃO DO CONTEÚDO (mira pelo id = 51)
UPDATE trips SET
    duration = 10,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Passeio de dia inteiro à Isla Saona em lancha rápida: 3 praias, piscina natural com estrelas-do-mar, Santuário das Tartarugas e visita ao povoado de Mano Juan. Almoço típico dominicano e open bar.',

    description = 'Passeio de dia inteiro (aproximadamente 10 horas) à Isla Saona em lancha rápida, com ida e volta: 3 praias + piscina natural.\n\nFunciona às segundas, quartas e sábados, podendo haver alteração conforme o clima. Sujeito a formação de quórum ou limitação de capacidade.\n\nTipo de passeio: coletivo e compartilhado, com várias nacionalidades (passeio terceirizado). Guia local em espanhol e inglês.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel entre 6h35 e 7h50. Viagem de aproximadamente 1h até a Marina de Bayahibe.\n2. Percurso até a 1ª parada — Piscina Natural: cerca de 25 minutos navegando em lancha rápida (tempo no local: cerca de 30 minutos). Observação respeitosa das estrelas-do-mar.\n3. Percurso até a 2ª parada na Isla Saona — Playa El Toro / Flamenco: cerca de 15 minutos em lancha rápida (tempo no local: cerca de 45 minutos). Praia virgem e sem estrutura, ambiente tranquilo e exclusivo.\n4. Percurso até a 3ª parada na Isla Saona — Playa Mano Juan e Santuário das Tartarugas: cerca de 10 minutos em lancha rápida (tempo no local: cerca de 1h30). Praia com estrutura completa (banheiros e espreguiçadeiras). Passeio pelo povoado de Mano Juan, onde vivem cerca de 70 famílias locais. Visita ao Santuário de Tartarugas Laúd (tartarugas verdes). Almoço coletivo típico dominicano, em estilo buffet. Open bar: água, refrigerante e rum dominicano.\n5. Percurso até a 4ª parada na Isla Saona — Playa Abanico: cerca de 10 minutos em lancha rápida (tempo no local: cerca de 45 minutos). Praia com estrutura completa (banheiros e espreguiçadeiras).\n6. Percurso de volta até a Marina de Bayahibe em lancha rápida: cerca de 30 minutos pelo Mar do Caribe. Chegada na Marina entre 16h00 e 17h00.\n7. Chegada ao hotel entre 18h00 e 19h00.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta). Demais regiões, consultar.\nUso obrigatório de colete salva-vidas (incluso) durante todo o percurso marítimo.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia, boné ou chapéu e itens pessoais.',

    includes = '["Transporte terrestre em ônibus","Transporte marítimo em lancha rápida (ida e volta)","Open bar: rum, água e refrigerante","Almoço buffet coletivo (salada fria, pão, massa quente e fria, arroz, peixe, carne de porco, frango e frutas)","Piscina natural","3 praias: El Toro/Flamenco, Mano Juan e Abanico","Visita ao Santuário das Tartarugas","Visita ao povoado de Mano Juan"]',

    excludes = '["Fotos profissionais","Itens pessoais","Gorjetas (opcional)"]'

WHERE id = 51;

-- [C] CONFERÊNCIA FINAL (opcional)
SELECT id, title, short_description, meeting_point, includes, excludes
FROM trips WHERE id = 51;
