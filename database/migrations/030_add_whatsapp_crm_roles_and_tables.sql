-- Migration: Adicionar novos roles ao sistema e criar tabelas CRM
-- Data: 2026-08-11
-- Descrição: Adiciona roles attendant, whatsapp_agent, comercial à tabela users,
--            campo commission_percent, e cria tabelas completas do módulo CRM.

-- ─────────────────────────────────────────────
-- ATUALIZAR ENUM DE ROLES NA TABELA USERS
-- ─────────────────────────────────────────────
ALTER TABLE users 
MODIFY COLUMN `role` ENUM('superadmin','admin','editor','affiliate','customer','attendant','whatsapp_agent','comercial') 
NOT NULL DEFAULT 'customer';

-- Adicionar campo de comissão para comerciais
ALTER TABLE users ADD COLUMN IF NOT EXISTS commission_percent DECIMAL(5,2) DEFAULT 0 
COMMENT 'Percentual de comissão para usuários com role comercial';

-- ─────────────────────────────────────────────
-- TABELAS CRM
-- ─────────────────────────────────────────────

-- Boards (CRMs independentes)
CREATE TABLE IF NOT EXISTS crm_boards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL COMMENT 'Nome do board',
    description TEXT DEFAULT NULL COMMENT 'Descrição opcional',
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem criou',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Soft delete (0=inativo)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Colunas do Kanban
CREATE TABLE IF NOT EXISTS crm_columns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_id INT NOT NULL COMMENT 'Board ao qual pertence',
    name VARCHAR(100) NOT NULL COMMENT 'Nome da coluna',
    color VARCHAR(7) DEFAULT '#6c757d' COMMENT 'Cor hexadecimal',
    label_id INT DEFAULT NULL COMMENT 'Etiqueta WhatsApp vinculada (opcional)',
    status ENUM('novo','em_atendimento','aguardando','concluido','perdido') DEFAULT NULL COMMENT 'Status vinculado à coluna',
    position INT DEFAULT 0 COMMENT 'Ordem de exibição',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES crm_boards(id) ON DELETE CASCADE,
    FOREIGN KEY (label_id) REFERENCES whatsapp_labels(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cards (Leads/Oportunidades)
CREATE TABLE IF NOT EXISTS crm_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    column_id INT NOT NULL COMMENT 'Coluna atual no kanban',
    contact_id INT DEFAULT NULL COMMENT 'Contato WhatsApp vinculado',
    title VARCHAR(200) NOT NULL COMMENT 'Título/Nome do lead',
    description TEXT DEFAULT NULL COMMENT 'Descrição do card',
    phone VARCHAR(20) DEFAULT NULL COMMENT 'Telefone do lead',
    value DECIMAL(10,2) DEFAULT NULL COMMENT 'Valor monetário (R$)',
    label_id INT DEFAULT NULL COMMENT 'Etiqueta do card',
    status ENUM('novo','em_atendimento','aguardando','concluido','perdido') DEFAULT NULL COMMENT 'Status do lead',
    lead_outcome ENUM('open','converted','lost') DEFAULT 'open' COMMENT 'Resultado: aberto/convertido/perdido',
    outcome_at DATETIME DEFAULT NULL COMMENT 'Data da conversão ou perda',
    converted_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem converteu o lead',
    follow_up_at DATETIME DEFAULT NULL COMMENT 'Data agendada de retomada',
    follow_up_column_id INT DEFAULT NULL COMMENT 'Coluna destino ao retomar',
    in_recovery TINYINT(1) DEFAULT 0 COMMENT 'Se está em recuperação (retomado automaticamente)',
    position INT DEFAULT 0 COMMENT 'Ordem dentro da coluna',
    assigned_to INT UNSIGNED DEFAULT NULL COMMENT 'Responsável pelo card',
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem criou',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_column (column_id),
    KEY idx_contact (contact_id),
    KEY idx_outcome (lead_outcome),
    KEY idx_followup (follow_up_at),
    FOREIGN KEY (column_id) REFERENCES crm_columns(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES whatsapp_contacts(id) ON DELETE SET NULL,
    FOREIGN KEY (label_id) REFERENCES whatsapp_labels(id) ON DELETE SET NULL,
    FOREIGN KEY (follow_up_column_id) REFERENCES crm_columns(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (converted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Atividades/Histórico dos Cards
CREATE TABLE IF NOT EXISTS crm_card_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_id INT NOT NULL COMMENT 'Card ao qual pertence',
    user_id INT UNSIGNED DEFAULT NULL COMMENT 'Quem registrou',
    activity_type ENUM('note','move','create','assign','label','convert','lost','followup') DEFAULT 'note' COMMENT 'Tipo da atividade',
    description TEXT DEFAULT NULL COMMENT 'Descrição/conteúdo da atividade',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (card_id) REFERENCES crm_cards(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Briefing Comercial
CREATE TABLE IF NOT EXISTS commercial_briefings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL UNIQUE COMMENT 'Contato WhatsApp vinculado (1:1)',
    need TEXT DEFAULT NULL COMMENT 'Necessidade do lead',
    main_pain TEXT DEFAULT NULL COMMENT 'Principal dor/problema',
    current_solution TEXT DEFAULT NULL COMMENT 'Solução atual utilizada',
    expected_goal TEXT DEFAULT NULL COMMENT 'Objetivo esperado',
    urgency VARCHAR(50) DEFAULT NULL COMMENT 'Urgência: Baixa, Média, Alta, Urgente',
    investment_range VARCHAR(100) DEFAULT NULL COMMENT 'Faixa de investimento (R$)',
    decision_level VARCHAR(100) DEFAULT NULL COMMENT 'Nível de decisão: Decisor, Influenciador, etc.',
    lead_temperature ENUM('frio','morno','quente') DEFAULT NULL COMMENT 'Temperatura do lead',
    main_objection TEXT DEFAULT NULL COMMENT 'Principal objeção',
    next_step TEXT DEFAULT NULL COMMENT 'Próximo passo combinado',
    next_contact_date DATE DEFAULT NULL COMMENT 'Data do próximo contato',
    notes TEXT DEFAULT NULL COMMENT 'Observações importantes',
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem criou',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES whatsapp_contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
