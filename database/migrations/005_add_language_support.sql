-- Migration 005: Suporte multilíngue básico
-- O idioma ativo é controlado por sessão (parâmetro ?lang=pt/en/es)
-- Adicionar setting de idiomas disponíveis
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('available_languages', 'pt,en,es', 'general', 'text')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
