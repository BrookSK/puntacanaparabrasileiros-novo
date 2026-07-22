-- ============================================================
-- PUNTA CANA PARA BRASILEIROS - Seeds (Dados Iniciais)
-- Executar APÓS schema.sql
-- ============================================================

SET NAMES utf8mb4;

-- ============================================================
-- USUÁRIO SUPERADMIN PADRÃO
-- Senha: Admin@123 (bcrypt hash)
-- ============================================================
INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `role`, `status`, `email_verified_at`) VALUES
('Admin', 'Sistema', 'admin@puntacanaparabrasileiros.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', 'active', NOW());

-- ============================================================
-- CATEGORIAS DE VIAJANTE PADRÃO
-- ============================================================
INSERT INTO `traveler_categories` (`name`, `slug`, `age_group`, `sort_order`) VALUES
('Adulto', 'adulto', '+12 anos', 1),
('Criança', 'crianca', '2-11 anos', 2),
('Bebê', 'bebe', '0-1 ano', 3),
('Idoso', 'idoso', '+65 anos', 4);

-- ============================================================
-- CATEGORIAS DE PASSEIOS
-- ============================================================
INSERT INTO `trip_categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Passeios de Barco', 'passeios-de-barco', 'Catamaran, lanchas e passeios marítimos', 1),
('Aventura', 'aventura', 'Tirolesa, buggy, quadriciclo e mais', 2),
('Cultural', 'cultural', 'Visitas culturais e históricas', 3),
('Natureza', 'natureza', 'Cenotes, praias paradisíacas e vida selvagem', 4),
('Noturno', 'noturno', 'Festas, shows e vida noturna', 5),
('Família', 'familia', 'Passeios ideais para famílias com crianças', 6),
('Romântico', 'romantico', 'Experiências para casais', 7),
('Esportes Aquáticos', 'esportes-aquaticos', 'Mergulho, snorkel, jet ski', 8);

-- ============================================================
-- LOCAIS DE TRANSFER PADRÃO (Punta Cana e região)
-- ============================================================
INSERT INTO `transfer_locations` (`title`, `slug`, `address`, `location_type`, `sort_order`) VALUES
('Aeroporto Internacional de Punta Cana (PUJ)', 'aeroporto-punta-cana', 'Punta Cana International Airport, Punta Cana, RD', 'airport', 1),
('Aeroporto La Romana (LRM)', 'aeroporto-la-romana', 'Aeropuerto Internacional de La Romana, La Romana, RD', 'airport', 2),
('Bávaro', 'bavaro', 'Bávaro, Punta Cana, RD', 'city', 3),
('Cap Cana', 'cap-cana', 'Cap Cana, Punta Cana, RD', 'resort', 4),
('Hard Rock Hotel', 'hard-rock-hotel', 'Hard Rock Hotel & Casino Punta Cana', 'hotel', 5),
('Zona Hotelera Bávaro', 'zona-hotelera-bavaro', 'Zona Hotelera, Bávaro, Punta Cana, RD', 'hotel', 6),
('Uvero Alto', 'uvero-alto', 'Uvero Alto, Punta Cana, RD', 'resort', 7),
('Macao', 'macao', 'Macao, Punta Cana, RD', 'city', 8),
('La Romana', 'la-romana', 'La Romana, RD', 'city', 9),
('Bayahibe', 'bayahibe', 'Bayahibe, La Romana, RD', 'city', 10),
('Santo Domingo', 'santo-domingo', 'Santo Domingo, RD', 'city', 11),
('Juan Dolio', 'juan-dolio', 'Juan Dolio, San Pedro de Macorís, RD', 'city', 12);

-- ============================================================
-- CONFIGURAÇÕES PADRÃO DO SISTEMA (settings)
-- ============================================================

-- Geral
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('site_name', 'Punta Cana para Brasileiros', 'general', 'text'),
('site_url', 'https://puntacanaparabrasileiros.com', 'general', 'text'),
('admin_email', 'admin@puntacanaparabrasileiros.com', 'general', 'text'),
('site_logo', '', 'general', 'file'),
('site_favicon', '', 'general', 'file'),
('currency', 'USD', 'general', 'text'),
('currency_symbol', '$', 'general', 'text'),
('date_format', 'd/m/Y', 'general', 'text'),
('timezone', 'America/Santo_Domingo', 'general', 'text'),
('default_language', 'pt-BR', 'general', 'select');

-- SMTP / Email
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('smtp_host', '', 'email', 'text'),
('smtp_port', '587', 'email', 'number'),
('smtp_username', '', 'email', 'text'),
('smtp_password', '', 'email', 'text'),
('smtp_encryption', 'tls', 'email', 'select'),
('mail_from_email', 'noreply@puntacanaparabrasileiros.com', 'email', 'text'),
('mail_from_name', 'Punta Cana para Brasileiros', 'email', 'text');

-- Pagamentos
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('paypal_enabled', '0', 'payments', 'boolean'),
('paypal_client_id', '', 'payments', 'text'),
('paypal_secret', '', 'payments', 'text'),
('paypal_mode', 'sandbox', 'payments', 'select'),
('stripe_enabled', '0', 'payments', 'boolean'),
('stripe_publishable_key', '', 'payments', 'text'),
('stripe_secret_key', '', 'payments', 'text'),
('stripe_mode', 'test', 'payments', 'select'),
('partial_payment_enabled', '0', 'payments', 'boolean'),
('partial_payment_percent', '50', 'payments', 'number');

-- WhatsApp
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('whatsapp_enabled', '0', 'whatsapp', 'boolean'),
('whatsapp_webhook_url', 'https://api.lrvweb.com.br/api/webhooks/028f76ff-75bf-46eb-a2c9-afeff5e718b8', 'whatsapp', 'text'),
('whatsapp_trip_template', '🎫 SEUS VOUCHERS DE PASSEIO 🎫\n\nOlá {customer_name}!\n\nSua reserva foi confirmada:\n📍 {trip_name}\n📅 {trip_date}\n⏰ {trip_time}\n👥 {pax_info}\n💰 Total: {total}\n\nCódigo: {reference}\n\nBoa viagem! 🌴', 'whatsapp', 'textarea'),
('whatsapp_transfer_template', '🚗 SEU TRANSFER CONFIRMADO 🚗\n\nOlá {customer_name}!\n\n🚗 {vehicle_name}\n📍 {origin} → {destination}\n📅 {date}\n⏰ {time}\n👥 {pax_info}\n\nCódigo: {reference}\n\nBoa viagem! 🌴', 'whatsapp', 'textarea');

-- Vouchers
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('voucher_logo', '', 'vouchers', 'file'),
('voucher_footer_text', 'Punta Cana para Brasileiros - Sua melhor experiência no Caribe!', 'vouchers', 'textarea'),
('voucher_instructions', 'Apresente este voucher impresso ou no celular no ponto de encontro. Chegue com 15 minutos de antecedência.', 'vouchers', 'textarea'),
('voucher_cleanup_days', '90', 'vouchers', 'number');

-- Afiliados
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('affiliate_enabled', '0', 'affiliates', 'boolean'),
('affiliate_default_rate', '20', 'affiliates', 'number'),
('affiliate_cookie_days', '30', 'affiliates', 'number'),
('affiliate_auto_approve', '0', 'affiliates', 'boolean'),
('affiliate_payment_method', 'manual', 'affiliates', 'select');

-- SEO
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('meta_title', 'Punta Cana para Brasileiros - Passeios e Transfers', 'seo', 'text'),
('meta_description', 'Os melhores passeios e transfers em Punta Cana para brasileiros. Reserve online com os melhores preços!', 'seo', 'textarea'),
('google_analytics_id', '', 'seo', 'text'),
('head_scripts', '', 'seo', 'textarea'),
('body_scripts', '', 'seo', 'textarea');

-- Aparência
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`, `setting_type`) VALUES
('color_primary', '#0077b6', 'appearance', 'color'),
('color_secondary', '#00b4d8', 'appearance', 'color'),
('color_accent', '#f77f00', 'appearance', 'color'),
('font_primary', 'Poppins', 'appearance', 'text'),
('custom_css', '', 'appearance', 'textarea'),
('whatsapp_float_number', '', 'appearance', 'text'),
('whatsapp_float_text', 'Fale conosco!', 'appearance', 'text');
