-- Migração: Criar tabela trip_composition_packages
-- Data: 2026-08-13
-- Descrição: Pacotes de composição por passeio (combinação pessoas + unidades + preço)
-- Permite configurar preços por combinação de passageiros e veículos/equipamentos/unidades

CREATE TABLE IF NOT EXISTS trip_composition_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    label VARCHAR(255) NOT NULL COMMENT 'Nome/descrição do pacote. Ex: 2 pessoas em 1 buggy',
    pax INT NOT NULL COMMENT 'Quantidade de pessoas nesta composição',
    units INT NOT NULL DEFAULT 1 COMMENT 'Quantidade de veículos/equipamentos/unidades',
    unit_label VARCHAR(100) DEFAULT NULL COMMENT 'Nome da unidade. Ex: Buggy, Quadriciclo, Equipamento',
    pax_per_unit INT DEFAULT NULL COMMENT 'Pessoas por unidade (opcional, informativo)',
    price DECIMAL(10,2) NOT NULL COMMENT 'Preço total desta composição',
    sort_order INT NOT NULL DEFAULT 0,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_trip_id (trip_id),
    INDEX idx_trip_pax (trip_id, pax),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Flag no trip para ativar modo composição
ALTER TABLE trips ADD COLUMN composition_pricing_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER group_pricing;
