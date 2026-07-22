-- Migration 002: Adicionar settings de Instagram
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('instagram_enabled', '1', 'instagram', 'boolean'),
('instagram_access_token', '', 'instagram', 'text'),
('instagram_username', 'puntacanaparabrasileiros', 'instagram', 'text'),
('instagram_post_count', '5', 'instagram', 'number')
ON DUPLICATE KEY UPDATE setting_key = setting_key;
