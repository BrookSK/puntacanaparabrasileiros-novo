-- Migração: Módulo Equipe (carrossel "Nossa equipe" na página Sobre Nós)
-- Data: 2026-09-10
-- Descrição: Cria a tabela de membros da equipe exibidos no carrossel da
--            página Sobre Nós. Cada membro tem foto, nome, cargo e apresentação.
--            Apenas membros com status 'published' aparecem no site.
--
-- Como aplicar: execute este arquivo no phpMyAdmin no banco do site.

CREATE TABLE IF NOT EXISTS `team_members` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `role` VARCHAR(150) DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `status` ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_team_status` (`status`),
    KEY `idx_team_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- Seed dos três primeiros membros já conhecidos.
-- A foto fica em branco até ser enviada pelo painel (Admin > Equipe).
-- Os três nascem como 'published'. Os slides 4, 5 e 6 NÃO são criados
-- aqui: basta cadastrá-los no painel quando o conteúdo chegar.
-- ─────────────────────────────────────────────
INSERT INTO `team_members` (`name`, `role`, `photo`, `bio`, `sort_order`, `status`) VALUES
('Anna Amélia', 'Cofundadora', NULL, 'Brasileira, naturalizada dominicana e apaixonada por pessoas e por esse destino incrível. Cuida de cada detalhe com muito carinho, garantindo que cada brasileiro viva experiências autênticas, seguras e inesquecíveis em Punta Cana.', 1, 'published'),
('Danilo Ramos', 'Cofundador', NULL, 'Dominicano que viveu no Brasil por cinco anos, fala português e se apaixonou pela nossa alegria. Conhece Punta Cana como poucos e trabalha para que cada viagem seja tranquila, bem organizada e cheia de bons momentos.', 2, 'published'),
('Aurora Costa', 'Assistente virtual', NULL, 'Nossa assistente virtual, sempre pronta para tirar dúvidas e ajudar você a planejar a viagem dos sonhos. Atendimento ágil, em português e disponível para orientar cada passo da sua experiência em Punta Cana.', 3, 'published');
