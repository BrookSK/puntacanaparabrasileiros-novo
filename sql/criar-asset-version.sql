-- Registra a chave de versão global dos assets (CSS/JS).
-- Opcional: o sistema funciona sem esta linha (default = "1"),
-- mas registrá-la deixa o valor visível/consistente no banco.
-- O botão "Limpar Cache" no admin incrementa este valor automaticamente.

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('asset_version', '1', 'general', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;
