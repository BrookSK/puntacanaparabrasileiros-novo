-- ============================================================
-- PUNTA CANA PARA BRASILEIROS - Schema SQL Completo
-- Sistema de Reservas de Passeios e Transfers
-- Banco: MySQL 8.0+ | Engine: InnoDB | Charset: utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE';

-- ============================================================
-- TABELA: users
-- Usuários do sistema (clientes, admin, afiliados)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `country` VARCHAR(5) DEFAULT NULL,
    `address` VARCHAR(500) DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `role` ENUM('superadmin','admin','editor','affiliate','customer') NOT NULL DEFAULT 'customer',
    `status` ENUM('active','inactive','banned') NOT NULL DEFAULT 'active',
    `email_verified_at` DATETIME DEFAULT NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `last_login_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: password_resets
-- Tokens para recuperação de senha
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_password_resets_email` (`email`),
    KEY `idx_password_resets_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: sessions
-- Sessões ativas do sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(128) NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sessions_user_id` (`user_id`),
    KEY `idx_sessions_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: settings
-- Configurações do sistema (gerenciadas via admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(191) NOT NULL,
    `setting_value` LONGTEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `setting_type` ENUM('text','textarea','number','boolean','json','file','color','select') NOT NULL DEFAULT 'text',
    `autoload` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_settings_key` (`setting_key`),
    KEY `idx_settings_group` (`setting_group`),
    KEY `idx_settings_autoload` (`autoload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_categories
-- Categorias de passeios (hierárquica)
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) NOT NULL,
    `slug` VARCHAR(191) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_trip_categories_slug` (`slug`),
    KEY `idx_trip_categories_parent` (`parent_id`),
    CONSTRAINT `fk_trip_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `trip_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trips
-- Passeios/Tours (entidade principal)
-- ============================================================
CREATE TABLE IF NOT EXISTS `trips` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` LONGTEXT DEFAULT NULL,
    `short_description` TEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `gallery` JSON DEFAULT NULL,
    `duration` VARCHAR(50) DEFAULT NULL,
    `duration_unit` ENUM('hours','days') NOT NULL DEFAULT 'hours',
    `difficulty` ENUM('easy','moderate','hard') DEFAULT 'easy',
    `min_pax` INT UNSIGNED NOT NULL DEFAULT 1,
    `max_pax` INT UNSIGNED DEFAULT NULL,
    `includes` JSON DEFAULT NULL,
    `excludes` JSON DEFAULT NULL,
    `map_latitude` DECIMAL(10,8) DEFAULT NULL,
    `map_longitude` DECIMAL(11,8) DEFAULT NULL,
    `map_embed` TEXT DEFAULT NULL,
    `weather_info` JSON DEFAULT NULL,
    `meeting_point` TEXT DEFAULT NULL,
    `important_notes` TEXT DEFAULT NULL,
    `partial_payment_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `partial_payment_percent` DECIMAL(5,2) DEFAULT NULL,
    `group_discount_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `group_discount_rules` JSON DEFAULT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('published','draft','disabled') NOT NULL DEFAULT 'draft',
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `bookings_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_trips_slug` (`slug`),
    KEY `idx_trips_status` (`status`),
    KEY `idx_trips_featured` (`featured`),
    KEY `idx_trips_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_category_relations
-- Relação N:N entre trips e categorias
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_category_relations` (
    `trip_id` INT UNSIGNED NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`trip_id`, `category_id`),
    CONSTRAINT `fk_tcr_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tcr_category` FOREIGN KEY (`category_id`) REFERENCES `trip_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: traveler_categories
-- Categorias de viajante (Adulto, Criança, Bebê, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `traveler_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `age_group` VARCHAR(50) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_traveler_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_packages
-- Pacotes de preço por trip
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_packages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_trip_packages_trip` (`trip_id`),
    CONSTRAINT `fk_trip_packages_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_package_categories
-- Preços por categoria de viajante dentro de cada pacote
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_package_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `package_id` INT UNSIGNED NOT NULL,
    `traveler_category_id` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `sale_price` DECIMAL(10,2) DEFAULT NULL,
    `min_pax` INT UNSIGNED NOT NULL DEFAULT 0,
    `max_pax` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_tpc_package` (`package_id`),
    KEY `idx_tpc_traveler` (`traveler_category_id`),
    CONSTRAINT `fk_tpc_package` FOREIGN KEY (`package_id`) REFERENCES `trip_packages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tpc_traveler` FOREIGN KEY (`traveler_category_id`) REFERENCES `traveler_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_day_pricing
-- Preço dinâmico por dia (dia da semana, feriado, data, mês, anual)
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_day_pricing` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `package_id` INT UNSIGNED NOT NULL,
    `traveler_category_id` INT UNSIGNED NOT NULL,
    `rule_type` ENUM('weekday','holiday','specific','monthly','annual') NOT NULL,
    `day_key` VARCHAR(50) NOT NULL COMMENT 'weekday: 0-6 | holiday: DD/MM | specific: DD/MM/YYYY | monthly: 1-31 | annual: 1-12',
    `price` DECIMAL(10,2) NOT NULL,
    `sale_price` DECIMAL(10,2) DEFAULT NULL,
    `label` VARCHAR(100) DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tdp_package` (`package_id`),
    KEY `idx_tdp_category` (`traveler_category_id`),
    KEY `idx_tdp_rule` (`rule_type`, `day_key`),
    CONSTRAINT `fk_tdp_package` FOREIGN KEY (`package_id`) REFERENCES `trip_packages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tdp_traveler` FOREIGN KEY (`traveler_category_id`) REFERENCES `traveler_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_fixed_dates
-- Datas fixas de saída por trip
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_fixed_dates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME DEFAULT NULL,
    `max_pax` INT UNSIGNED DEFAULT NULL,
    `booked_pax` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('available','full','cancelled') NOT NULL DEFAULT 'available',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tfd_trip` (`trip_id`),
    KEY `idx_tfd_date` (`date`),
    CONSTRAINT `fk_tfd_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_itinerary
-- Itinerário multi-dia/etapa
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_itinerary` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `day_number` INT UNSIGNED NOT NULL DEFAULT 1,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_ti_trip` (`trip_id`),
    CONSTRAINT `fk_ti_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_extra_services
-- Serviços extras add-on por trip
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_extra_services` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `price_type` ENUM('per_person','per_group','fixed') NOT NULL DEFAULT 'per_person',
    `required` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_tes_trip` (`trip_id`),
    CONSTRAINT `fk_tes_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: trip_reviews
-- Avaliações e reviews de passeios
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_reviews` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `author_name` VARCHAR(100) DEFAULT NULL,
    `author_email` VARCHAR(255) DEFAULT NULL,
    `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `title` VARCHAR(255) DEFAULT NULL,
    `comment` TEXT NOT NULL,
    `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tr_trip` (`trip_id`),
    KEY `idx_tr_user` (`user_id`),
    KEY `idx_tr_status` (`status`),
    CONSTRAINT `fk_tr_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: transfer_locations
-- Locais/pontos de embarque e desembarque
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfer_locations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `address` TEXT DEFAULT NULL,
    `latitude` DECIMAL(10,8) DEFAULT NULL,
    `longitude` DECIMAL(11,8) DEFAULT NULL,
    `location_type` ENUM('airport','hotel','resort','city','other') NOT NULL DEFAULT 'other',
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_transfer_locations_slug` (`slug`),
    KEY `idx_tl_type` (`location_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: transfer_vehicles
-- Veículos disponíveis para transfer
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfer_vehicles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `vehicle_type` VARCHAR(100) DEFAULT NULL,
    `max_passengers` INT UNSIGNED NOT NULL DEFAULT 4,
    `max_adults` INT UNSIGNED NOT NULL DEFAULT 4,
    `max_children` INT UNSIGNED NOT NULL DEFAULT 2,
    `max_infants` INT UNSIGNED NOT NULL DEFAULT 1,
    `max_luggage` INT UNSIGNED NOT NULL DEFAULT 4,
    `amenities` JSON DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_transfer_vehicles_slug` (`slug`),
    KEY `idx_tv_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: transfer_routes
-- Rotas por veículo (origem → destino)
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfer_routes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `vehicle_id` INT UNSIGNED NOT NULL,
    `origin_id` INT UNSIGNED NOT NULL,
    `destination_id` INT UNSIGNED NOT NULL,
    `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `duration` INT UNSIGNED DEFAULT NULL COMMENT 'Duração em minutos',
    `distance_km` DECIMAL(8,2) DEFAULT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_troute_vehicle` (`vehicle_id`),
    KEY `idx_troute_origin` (`origin_id`),
    KEY `idx_troute_dest` (`destination_id`),
    UNIQUE KEY `uk_troute_combo` (`vehicle_id`, `origin_id`, `destination_id`),
    CONSTRAINT `fk_troute_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `transfer_vehicles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_troute_origin` FOREIGN KEY (`origin_id`) REFERENCES `transfer_locations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_troute_dest` FOREIGN KEY (`destination_id`) REFERENCES `transfer_locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: transfer_tariffs
-- Tarifas por faixa de passageiros por rota
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfer_tariffs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `route_id` INT UNSIGNED NOT NULL,
    `service_type` ENUM('private','shared') NOT NULL DEFAULT 'private',
    `min_pax` INT UNSIGNED NOT NULL DEFAULT 1,
    `max_pax` INT UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ttariff_route` (`route_id`),
    KEY `idx_ttariff_service` (`service_type`),
    CONSTRAINT `fk_ttariff_route` FOREIGN KEY (`route_id`) REFERENCES `transfer_routes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: bookings
-- Reservas/pedidos principais
-- ============================================================
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `booking_number` VARCHAR(50) NOT NULL,
    `status` ENUM('pending','booked','partially_paid','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `paid_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `due_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_mode` ENUM('full','partial') NOT NULL DEFAULT 'full',
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `billing_first_name` VARCHAR(100) NOT NULL,
    `billing_last_name` VARCHAR(100) NOT NULL,
    `billing_email` VARCHAR(255) NOT NULL,
    `billing_phone` VARCHAR(50) DEFAULT NULL,
    `billing_address` VARCHAR(500) DEFAULT NULL,
    `billing_city` VARCHAR(100) DEFAULT NULL,
    `billing_country` VARCHAR(5) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `admin_notes` TEXT DEFAULT NULL,
    `affiliate_id` INT UNSIGNED DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_bookings_number` (`booking_number`),
    KEY `idx_bookings_user` (`user_id`),
    KEY `idx_bookings_status` (`status`),
    KEY `idx_bookings_date` (`created_at`),
    KEY `idx_bookings_affiliate` (`affiliate_id`),
    CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: booking_items
-- Itens (trips) dentro de uma reserva
-- ============================================================
CREATE TABLE IF NOT EXISTS `booking_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id` INT UNSIGNED NOT NULL,
    `trip_id` INT UNSIGNED NOT NULL,
    `package_id` INT UNSIGNED DEFAULT NULL,
    `trip_date` DATE NOT NULL,
    `trip_time` TIME DEFAULT NULL,
    `pax` JSON NOT NULL COMMENT '{"category_id": quantity, ...}',
    `extra_services` JSON DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `partial_price` DECIMAL(10,2) DEFAULT NULL,
    `group_discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_bi_booking` (`booking_id`),
    KEY `idx_bi_trip` (`trip_id`),
    CONSTRAINT `fk_bi_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bi_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: booking_travelers
-- Dados dos viajantes por item de reserva
-- ============================================================
CREATE TABLE IF NOT EXISTS `booking_travelers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_item_id` INT UNSIGNED NOT NULL,
    `full_name` VARCHAR(200) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `age_group` VARCHAR(50) DEFAULT NULL,
    `traveler_category_id` INT UNSIGNED DEFAULT NULL,
    `extra_data` JSON DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_bt_item` (`booking_item_id`),
    CONSTRAINT `fk_bt_item` FOREIGN KEY (`booking_item_id`) REFERENCES `booking_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: transfer_bookings
-- Reservas individuais de transfer
-- ============================================================
CREATE TABLE IF NOT EXISTS `transfer_bookings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `group_id` VARCHAR(50) DEFAULT NULL COMMENT 'Agrupa ida+volta',
    `vehicle_id` INT UNSIGNED NOT NULL,
    `origin_id` INT UNSIGNED NOT NULL,
    `destination_id` INT UNSIGNED NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `type` ENUM('arrival','departure') NOT NULL,
    `service_type` ENUM('private','shared') NOT NULL DEFAULT 'private',
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `adults` INT UNSIGNED NOT NULL DEFAULT 1,
    `children` INT UNSIGNED NOT NULL DEFAULT 0,
    `infants` INT UNSIGNED NOT NULL DEFAULT 0,
    `customer_name` VARCHAR(200) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(50) DEFAULT NULL,
    `passengers` JSON DEFAULT NULL,
    `flight_number` VARCHAR(50) DEFAULT NULL,
    `flight_time` TIME DEFAULT NULL,
    `status` ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_tb_booking` (`booking_id`),
    KEY `idx_tb_vehicle` (`vehicle_id`),
    KEY `idx_tb_date` (`date`),
    KEY `idx_tb_status` (`status`),
    KEY `idx_tb_group` (`group_id`),
    CONSTRAINT `fk_tb_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tb_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `transfer_vehicles` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tb_origin` FOREIGN KEY (`origin_id`) REFERENCES `transfer_locations` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tb_dest` FOREIGN KEY (`destination_id`) REFERENCES `transfer_locations` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: payments
-- Pagamentos registrados (pode ter múltiplos por booking)
-- ============================================================
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id` INT UNSIGNED NOT NULL,
    `gateway` ENUM('paypal','stripe','manual','free') NOT NULL,
    `transaction_id` VARCHAR(255) DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status` ENUM('pending','processing','completed','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
    `type` ENUM('full','partial','remaining') NOT NULL DEFAULT 'full',
    `gateway_response` JSON DEFAULT NULL,
    `payer_email` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_payments_booking` (`booking_id`),
    KEY `idx_payments_gateway` (`gateway`),
    KEY `idx_payments_status` (`status`),
    KEY `idx_payments_transaction` (`transaction_id`),
    CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: cart_sessions
-- Carrinho persistido (sessão + banco)
-- ============================================================
CREATE TABLE IF NOT EXISTS `cart_sessions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `session_id` VARCHAR(128) NOT NULL,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `cart_data` JSON NOT NULL,
    `transfer_data` JSON DEFAULT NULL,
    `expires_at` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_cart_session` (`session_id`),
    KEY `idx_cart_user` (`user_id`),
    KEY `idx_cart_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: vouchers
-- Vouchers gerados (trips e transfers)
-- ============================================================
CREATE TABLE IF NOT EXISTS `vouchers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `booking_item_id` INT UNSIGNED DEFAULT NULL,
    `transfer_booking_id` INT UNSIGNED DEFAULT NULL,
    `reference_code` VARCHAR(50) NOT NULL,
    `type` ENUM('trip','transfer') NOT NULL DEFAULT 'trip',
    `file_path` VARCHAR(255) DEFAULT NULL,
    `email_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `whatsapp_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_vouchers_reference` (`reference_code`),
    KEY `idx_vouchers_booking` (`booking_id`),
    KEY `idx_vouchers_transfer` (`transfer_booking_id`),
    CONSTRAINT `fk_vouchers_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_vouchers_item` FOREIGN KEY (`booking_item_id`) REFERENCES `booking_items` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_vouchers_transfer` FOREIGN KEY (`transfer_booking_id`) REFERENCES `transfer_bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: affiliates
-- Dados do programa de afiliados
-- ============================================================
CREATE TABLE IF NOT EXISTS `affiliates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `status` ENUM('pending','active','inactive','rejected') NOT NULL DEFAULT 'pending',
    `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 20.00,
    `cookie_days` INT UNSIGNED NOT NULL DEFAULT 30,
    `payment_email` VARCHAR(255) DEFAULT NULL,
    `payment_method` ENUM('manual','stripe') NOT NULL DEFAULT 'manual',
    `total_visits` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_referrals` INT UNSIGNED NOT NULL DEFAULT 0,
    `total_sales` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_earnings` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_paid` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_affiliates_user` (`user_id`),
    KEY `idx_affiliates_status` (`status`),
    CONSTRAINT `fk_affiliates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: affiliate_visits
-- Visitas rastreadas via link de afiliado
-- ============================================================
CREATE TABLE IF NOT EXISTS `affiliate_visits` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` INT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `referrer` TEXT DEFAULT NULL,
    `page_url` TEXT DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `converted` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_av_affiliate` (`affiliate_id`),
    KEY `idx_av_date` (`created_at`),
    CONSTRAINT `fk_av_affiliate` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: commissions
-- Comissões de afiliados
-- ============================================================
CREATE TABLE IF NOT EXISTS `commissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` INT UNSIGNED NOT NULL,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `rate` DECIMAL(5,2) NOT NULL,
    `base_amount` DECIMAL(10,2) NOT NULL COMMENT 'Valor base da venda',
    `status` ENUM('pending','approved','paid','rejected') NOT NULL DEFAULT 'pending',
    `paid_at` DATETIME DEFAULT NULL,
    `payout_reference` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comm_affiliate` (`affiliate_id`),
    KEY `idx_comm_booking` (`booking_id`),
    KEY `idx_comm_status` (`status`),
    CONSTRAINT `fk_comm_affiliate` FOREIGN KEY (`affiliate_id`) REFERENCES `affiliates` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comm_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: wishlists
-- Lista de desejos dos clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS `wishlists` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `trip_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_wishlist_user_trip` (`user_id`, `trip_id`),
    CONSTRAINT `fk_wl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_wl_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: pages
-- Páginas estáticas do site (termos, sobre, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `template` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: activity_log
-- Log de atividades do sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) DEFAULT NULL,
    `entity_id` INT UNSIGNED DEFAULT NULL,
    `details` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_al_user` (`user_id`),
    KEY `idx_al_action` (`action`),
    KEY `idx_al_entity` (`entity_type`, `entity_id`),
    KEY `idx_al_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: email_log
-- Log de emails enviados
-- ============================================================
CREATE TABLE IF NOT EXISTS `email_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `to_email` VARCHAR(255) NOT NULL,
    `to_name` VARCHAR(200) DEFAULT NULL,
    `subject` VARCHAR(500) NOT NULL,
    `body` LONGTEXT DEFAULT NULL,
    `attachments` JSON DEFAULT NULL,
    `status` ENUM('sent','failed','queued') NOT NULL DEFAULT 'queued',
    `error_message` TEXT DEFAULT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
    `sent_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_el_email` (`to_email`),
    KEY `idx_el_status` (`status`),
    KEY `idx_el_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: voucher_log
-- Log de vouchers gerados e enviados
-- ============================================================
CREATE TABLE IF NOT EXISTS `voucher_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_id` INT UNSIGNED DEFAULT NULL,
    `reference_code` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `trip_name` VARCHAR(255) DEFAULT NULL,
    `file_path` VARCHAR(255) DEFAULT NULL,
    `email_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `whatsapp_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_vl_booking` (`booking_id`),
    KEY `idx_vl_reference` (`reference_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: migrations
-- Controle de migrations executadas
-- ============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_migrations_name` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: rate_limits
-- Controle de rate limiting
-- ============================================================
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `identifier` VARCHAR(255) NOT NULL COMMENT 'IP ou user_id',
    `action` VARCHAR(100) NOT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `last_attempt_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `blocked_until` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_rate_limits` (`identifier`, `action`),
    KEY `idx_rl_blocked` (`blocked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: coupons (cupons de desconto - funcionalidade extra)
-- ============================================================
CREATE TABLE IF NOT EXISTS `coupons` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `type` ENUM('percentage','fixed') NOT NULL DEFAULT 'percentage',
    `value` DECIMAL(10,2) NOT NULL,
    `min_order` DECIMAL(10,2) DEFAULT NULL,
    `max_discount` DECIMAL(10,2) DEFAULT NULL,
    `usage_limit` INT UNSIGNED DEFAULT NULL,
    `used_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `applicable_trips` JSON DEFAULT NULL COMMENT 'null = todos',
    `starts_at` DATETIME DEFAULT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_coupons_code` (`code`),
    KEY `idx_coupons_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
