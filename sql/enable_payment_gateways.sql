-- Habilitar gateways de pagamento
-- Execute este SQL no banco puntacana_novo_db

-- PayPal (habilitar)
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('paypal_enabled', '1', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = '1';

-- Stripe (habilitar)
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('stripe_enabled', '1', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = '1';

-- PagBank/PIX (habilitar)
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('pagbank_enabled', '1', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = '1';

-- Configuracoes PagBank
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('pagbank_token', '', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('pagbank_mode', 'sandbox', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('pagbank_usd_brl_rate', '5.50', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

-- Configuracoes Stripe (placeholders)
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('stripe_publishable_key', '', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('stripe_secret_key', '', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

-- Configuracoes PayPal (placeholders)
INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('paypal_client_id', '', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('paypal_secret', '', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;

INSERT INTO settings (setting_key, setting_value, setting_group, autoload)
VALUES ('paypal_mode', 'sandbox', 'payments', 1)
ON DUPLICATE KEY UPDATE setting_value = setting_value;
