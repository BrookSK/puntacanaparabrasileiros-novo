-- Migração: Aurora — Agente de IA para atendimento no WhatsApp
-- Data: 2026-08-13
-- Descrição: Insere as settings do agente de IA "Aurora", responsável pelo primeiro
--            atendimento automático via WhatsApp (identifica interesse, consulta passeios,
--            responde ao cliente e movimenta o lead no CRM).
--
-- Regras de negócio (refletidas no comportamento do AuroraService):
--   - Aurora atende APENAS até um atendente humano assumir o contato (assigned_to).
--   - Toggle geral liga/desliga tudo.
--   - Aurora NÃO fecha vendas nem inventa preços: informa, sugere e aciona um humano.
--
-- Como aplicar: execute este arquivo no phpMyAdmin (ou via CLI mysql) no banco do site.

-- ─────────────────────────────────────────────
-- Settings da Aurora (chave/valor)
-- aurora_enabled          : liga/desliga o agente de IA (0/1)
-- aurora_openai_api_key   : chave de API da OpenAI (sk-...)
-- aurora_model            : modelo do chat (padrão gpt-4o-mini)
-- aurora_system_prompt    : instruções de personalidade/comportamento da Aurora
-- aurora_crm_board_id      : ID do board do CRM onde os leads serão criados/movidos (vazio = primeiro board ativo)
-- aurora_max_replies      : nº máximo de respostas automáticas por contato antes de forçar handoff humano
-- aurora_history_limit    : nº de mensagens recentes enviadas como contexto para a IA
-- ─────────────────────────────────────────────
-- Inserimos apenas se a chave ainda não existir. autoload=1 para leitura via setting().
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`, `autoload`)
SELECT * FROM (
    SELECT 'aurora_enabled' AS k, '0' AS v, 'aurora' AS g, 'boolean' AS t, 1 AS a UNION ALL
    SELECT 'aurora_openai_api_key', '', 'aurora', 'text', 1 UNION ALL
    SELECT 'aurora_model', 'gpt-4o-mini', 'aurora', 'text', 1 UNION ALL
    SELECT 'aurora_crm_board_id', '', 'aurora', 'text', 1 UNION ALL
    SELECT 'aurora_max_replies', '8', 'aurora', 'text', 1 UNION ALL
    SELECT 'aurora_history_limit', '10', 'aurora', 'text', 1 UNION ALL
    SELECT 'aurora_system_prompt',
        'Você é a Aurora, assistente virtual de uma agência de turismo especializada em passeios em Punta Cana (República Dominicana) para brasileiros. Seu papel é fazer o PRIMEIRO atendimento pelo WhatsApp de forma calorosa, simpática e objetiva, em português do Brasil.\n\nO que você DEVE fazer:\n- Cumprimentar o cliente e entender o que ele procura (qual passeio, quantas pessoas, datas, interesses).\n- Sugerir passeios com base na lista de passeios disponíveis que será fornecida no contexto. Cite nomes reais dos passeios.\n- Tirar dúvidas gerais sobre os passeios (o que inclui, duração, tipo de experiência) com base nas informações fornecidas.\n- Ser breve: respostas curtas, no máximo 2 a 4 frases, como uma conversa real de WhatsApp.\n\nO que você NÃO PODE fazer (regras rígidas):\n- NUNCA invente preços, datas de disponibilidade, promoções ou informações que não estejam no contexto fornecido. Se não souber o preço ou a disponibilidade, diga que um consultor vai confirmar.\n- NUNCA feche a venda nem confirme reserva. Quando o cliente quiser fechar, contratar, pagar ou saber preço/disponibilidade exata, avise gentilmente que vai chamar um consultor humano para dar sequência.\n- Não peça dados de cartão, senha ou informações sensíveis.\n\nQuando perceber intenção clara de compra (cliente quer fechar, pagar, saber preço final ou disponibilidade de data específica), encerre sua mensagem sinalizando que um consultor humano dará continuidade.',
        'aurora', 'textarea', 1
) AS novo
WHERE NOT EXISTS (
    SELECT 1 FROM `settings` s WHERE s.`setting_key` = novo.k
);
