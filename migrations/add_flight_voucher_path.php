<?php
/**
 * Migração: Adicionar coluna flight_voucher_path na tabela bookings
 * Execute via: php migrations/add_flight_voucher_path.php
 * Ou acesse via navegador (remova depois).
 */

require_once __DIR__ . '/../public/index.php';

// Só roda se chamado diretamente
$db = \Core\Database::getInstance();

try {
    // Verificar se a coluna já existe
    $columns = $db->fetchAll("SHOW COLUMNS FROM bookings LIKE 'flight_voucher_path'");
    if (empty($columns)) {
        $db->query("ALTER TABLE bookings ADD COLUMN flight_voucher_path VARCHAR(500) DEFAULT NULL AFTER notes");
        echo "✅ Coluna 'flight_voucher_path' adicionada com sucesso à tabela 'bookings'.\n";
    } else {
        echo "ℹ️ Coluna 'flight_voucher_path' já existe.\n";
    }

    // Também criar tabela de notificações de preço se não existir
    $db->query("CREATE TABLE IF NOT EXISTS price_change_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        trip_title VARCHAR(255) NOT NULL,
        old_price DECIMAL(10,2) NOT NULL,
        new_price DECIMAL(10,2) NOT NULL,
        notified_emails TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Tabela 'price_change_notifications' verificada/criada.\n";

} catch (\Throwable $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
