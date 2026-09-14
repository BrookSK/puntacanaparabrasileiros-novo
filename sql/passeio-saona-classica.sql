-- =============================================================
-- ATUALIZAÇÃO DE CONTEÚDO: Saona Clássica  (id = 72)
-- Preenche apenas campos de TEXTO. NÃO toca em nenhum preço.
--
-- COMO USAR:
-- 1. FAÇA BACKUP DO BANCO ANTES (phpMyAdmin > Exportar).
-- 2. Rode o SELECT do passo [A] para confirmar o passeio.
-- 3. Rode o UPDATE do passo [B].
-- =============================================================

-- [A] CONFERÊNCIA (não altera nada). Deve mostrar "Saona Clássica - Catamarã".
SELECT id, title FROM trips WHERE id = 72;

-- [B] ATUALIZAÇÃO DO CONTEÚDO (mira pelo id = 72)
UPDATE trips SET
    duration = 10,
    duration_unit = 'hours',
    min_pax = 1,

    short_description = 'Passeio de dia inteiro à Isla Saona combinando lancha rápida e catamarã com festa e animação. Piscina natural com estrelas-do-mar, praia com estrutura completa, almoço típico dominicano e open bar.',

    description = 'Passeio de dia inteiro (aproximadamente 10 horas) à Isla Saona. Duas experiências e duas formas de navegar em um só passeio: lancha rápida e catamarã (com animação, música e festa).\n\nFunciona todos os dias, sujeito a disponibilidade.\n\nTipo de passeio: coletivo e compartilhado, com várias nacionalidades (passeio terceirizado). Guia local em espanhol e inglês.\n\nROTEIRO DO PASSEIO\n1. Saída do hotel em Punta Cana entre 6h35 e 7h50.\n2. Viagem de aproximadamente 1h até a Marina de Bayahibe.\n3. Percurso até a Isla Saona em lancha rápida ou catamarã (a ser definido no dia do passeio). O percurso de catamarã inclui animação, música e festa.\n4. 1ª parada — Piscina Natural (tempo no local: cerca de 30 minutos): águas rasas, mornas e cristalinas, com observação respeitosa das estrelas-do-mar. Open bar: água, refrigerante e rum dominicano.\n5. 2ª parada — Playa Abanico (tempo no local: cerca de 1h30): praia com estrutura completa (banheiros e espreguiçadeiras). Almoço coletivo típico dominicano, em estilo buffet. Open bar: água, refrigerante e rum dominicano.\n6. Percurso de retorno até a Marina de Bayahibe em lancha rápida ou catamarã (embarcação inversa da utilizada na ida).\n7. Chegada na Marina de Bayahibe entre 17h00 e 17h30.\n8. Retorno ao hotel entre 18h30 e 19h30.\n\nA ordem do passeio pode ser alterada sem aviso prévio.',

    meeting_point = 'Recepção do seu hotel ou ponto de encontro especificado.',

    important_notes = 'Transporte: incluso em resorts na região de Bávaro e Punta Cana (ida e volta). Demais regiões, consultar.\nUso obrigatório de colete salva-vidas (incluso) durante todo o percurso marítimo.\nGestantes não são permitidas. Não acessível a cadeirantes.\n\nO QUE LEVAR: protetor solar, repelente, óculos de sol, toalha de praia e boné ou chapéu.',

    includes = '["Transporte terrestre","Transporte marítimo em lancha rápida e catamarã","Festa e animação no catamarã","Open bar: rum, água e refrigerante","Almoço buffet coletivo","1 praia","Piscina natural"]',

    excludes = '["Fotos profissionais","Gorjetas (opcional)","Itens não especificados"]'

WHERE id = 72;

-- [C] CONFERÊNCIA FINAL (opcional)
SELECT id, title, short_description, meeting_point, includes, excludes
FROM trips WHERE id = 72;
