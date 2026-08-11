-- Migration: Criar tabelas do módulo WhatsApp
-- Data: 2026-08-11
-- Descrição: Cria todas as tabelas necessárias para o módulo WhatsApp (instâncias, contatos, mensagens, etiquetas, respostas rápidas)

-- ─────────────────────────────────────────────
-- INSTÂNCIAS (conexões WhatsApp via Evolution API)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_instances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instance_name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome técnico (sem espaços)',
    display_name VARCHAR(150) DEFAULT NULL COMMENT 'Nome de exibição amigável',
    api_url VARCHAR(500) NOT NULL COMMENT 'URL da Evolution API',
    api_key VARCHAR(500) NOT NULL COMMENT 'Chave de autenticação da API',
    owner_phone VARCHAR(20) DEFAULT NULL COMMENT 'Telefone do proprietário da instância',
    user_id INT UNSIGNED DEFAULT NULL COMMENT 'Usuário vinculado (só ele vê contatos desta instância)',
    connection_status ENUM('open','connected','close','connecting') DEFAULT 'close' COMMENT 'Status da conexão',
    qr_code TEXT DEFAULT NULL COMMENT 'QR Code base64 para conexão',
    is_default TINYINT(1) DEFAULT 0 COMMENT 'Se é a instância padrão (apenas uma por vez)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- CONTATOS (individuais e grupos)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instance_id INT NOT NULL COMMENT 'Instância à qual pertence',
    remote_jid VARCHAR(100) NOT NULL COMMENT 'JID do WhatsApp (numero@s.whatsapp.net ou grupo@g.us)',
    phone VARCHAR(20) DEFAULT NULL COMMENT 'Número de telefone (apenas dígitos)',
    contact_name VARCHAR(200) DEFAULT NULL COMMENT 'Nome definido pelo atendente (prioridade na exibição)',
    push_name VARCHAR(200) DEFAULT NULL COMMENT 'Nome do perfil do WhatsApp',
    profile_picture_url TEXT DEFAULT NULL COMMENT 'URL da foto de perfil',
    is_group TINYINT(1) DEFAULT 0 COMMENT 'Se é grupo (1) ou contato individual (0)',
    internal_notes TEXT DEFAULT NULL COMMENT 'Observações internas do atendente',
    assigned_to INT UNSIGNED DEFAULT NULL COMMENT 'Atendente responsável pelo contato',
    service_status ENUM('novo','em_atendimento','aguardando','concluido') DEFAULT 'novo' COMMENT 'Status do atendimento',
    last_message_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Data/hora da última mensagem',
    is_archived TINYINT(1) DEFAULT 0 COMMENT 'Se o contato está arquivado',
    unread_count INT DEFAULT 0 COMMENT 'Quantidade de mensagens não lidas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_instance_jid (instance_id, remote_jid),
    KEY idx_phone (phone),
    KEY idx_assigned (assigned_to),
    KEY idx_status (service_status),
    KEY idx_last_message (last_message_at DESC),
    FOREIGN KEY (instance_id) REFERENCES whatsapp_instances(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- MENSAGENS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instance_id INT NOT NULL COMMENT 'Instância de origem',
    contact_id INT NOT NULL COMMENT 'Contato associado',
    remote_jid VARCHAR(100) NOT NULL COMMENT 'JID do destinatário/remetente',
    message_id VARCHAR(100) DEFAULT NULL COMMENT 'ID único da mensagem no WhatsApp',
    from_me TINYINT(1) DEFAULT 0 COMMENT 'Se foi enviada por nós (1) ou recebida (0)',
    message_type ENUM('text','image','audio','video','document','sticker','location','contact','reaction','poll','list','unknown') DEFAULT 'text' COMMENT 'Tipo da mensagem',
    message_text TEXT DEFAULT NULL COMMENT 'Conteúdo de texto da mensagem',
    transcription TEXT DEFAULT NULL COMMENT 'Transcrição de áudio (via Whisper)',
    media_url TEXT DEFAULT NULL COMMENT 'URL/caminho do arquivo de mídia',
    media_mime_type VARCHAR(100) DEFAULT NULL COMMENT 'MIME type da mídia',
    media_filename VARCHAR(255) DEFAULT NULL COMMENT 'Nome original do arquivo',
    quoted_message_id VARCHAR(100) DEFAULT NULL COMMENT 'ID da mensagem citada/respondida',
    sender_name VARCHAR(200) DEFAULT NULL COMMENT 'Nome de quem enviou (relevante em grupos)',
    participant_jid VARCHAR(100) DEFAULT NULL COMMENT 'JID do participante em grupos',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Momento da mensagem',
    is_read TINYINT(1) DEFAULT 0 COMMENT 'Se foi lida pelo atendente',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Se a mensagem foi apagada pelo remetente',
    ack_status ENUM('pending','sent','delivered','read','failed') DEFAULT NULL COMMENT 'Status de entrega',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_message_id (instance_id, message_id),
    KEY idx_contact (contact_id),
    KEY idx_jid_time (remote_jid, timestamp),
    KEY idx_timestamp (timestamp DESC),
    FOREIGN KEY (instance_id) REFERENCES whatsapp_instances(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES whatsapp_contacts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- ETIQUETAS (labels para organização de contatos)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_labels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Nome da etiqueta',
    color VARCHAR(7) DEFAULT '#6c757d' COMMENT 'Cor hexadecimal',
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem criou a etiqueta',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- RELAÇÃO CONTATO ↔ ETIQUETA (N:N)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_contact_labels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    label_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_contact_label (contact_id, label_id),
    FOREIGN KEY (contact_id) REFERENCES whatsapp_contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (label_id) REFERENCES whatsapp_labels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- RESPOSTAS RÁPIDAS
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS whatsapp_quick_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shortcut VARCHAR(100) NOT NULL UNIQUE COMMENT 'Atalho sem a /, minúsculo, sem espaços',
    message TEXT DEFAULT NULL COMMENT 'Texto da resposta rápida',
    attachment_path TEXT DEFAULT NULL COMMENT 'Caminho do arquivo anexado (relativo)',
    attachment_name VARCHAR(255) DEFAULT NULL COMMENT 'Nome original do arquivo anexado',
    attachment_mime VARCHAR(100) DEFAULT NULL COMMENT 'MIME type do anexo',
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Quem criou',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- SETTINGS DO WHATSAPP (inserir se não existem)
-- ─────────────────────────────────────────────
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group, setting_type, autoload) VALUES
    ('whatsapp_group_notify_enabled', '0', 'whatsapp', 'boolean', 1),
    ('whatsapp_default_group_jid', '', 'whatsapp', 'text', 1),
    ('openai_api_key', '', 'integrations', 'text', 1),
    ('evolution_api_url', '', 'whatsapp', 'text', 1),
    ('evolution_api_key', '', 'whatsapp', 'text', 1),
    ('evolution_instance_name', '', 'whatsapp', 'text', 1);

-- ─────────────────────────────────────────────
-- ADICIONAR CAMPOS AO USERS (se não existem)
-- ─────────────────────────────────────────────
-- Nota: Estes ALTER TABLE podem falhar se as colunas já existirem.
-- Execute separadamente se necessário.

ALTER TABLE users ADD COLUMN IF NOT EXISTS commission_percent DECIMAL(5,2) DEFAULT 0 COMMENT 'Percentual de comissão para comerciais';
