-- Migration 004: FAQs específicas por passeio
CREATE TABLE IF NOT EXISTS `trip_faqs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `trip_id` INT UNSIGNED NOT NULL,
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_trip_faqs_trip` (`trip_id`),
    CONSTRAINT `fk_trip_faqs_trip` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
