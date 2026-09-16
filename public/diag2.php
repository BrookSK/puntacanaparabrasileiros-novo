<?php
/**
 * DIAGNÓSTICO 2 — foco em imagens de TRANSFER e passeios.
 * Acesse: [endereço-do-site]/diag2.php
 * REMOVA este arquivo depois de resolver.
 */
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
if (!defined('BASE_PATH')) define('BASE_PATH', $root);

echo "=== DIAGNÓSTICO 2 (TRANSFERS) ===\n\n";

// Autoloader mínimo
spl_autoload_register(function ($class) use ($root) {
    $map = ['Core\\' => $root . '/core/', 'App\\Models\\' => $root . '/app/Models/'];
    foreach ($map as $p => $d) {
        if (str_starts_with($class, $p)) {
            $file = $d . str_replace('\\', '/', substr($class, strlen($p))) . '.php';
            if (is_file($file)) require_once $file;
        }
    }
});

try {
    require $root . '/config/app.php';
} catch (\Throwable $e) {}

try {
    $db = \Core\Database::getInstance();

    echo "--- TRANSFERS (transfer_vehicles) ---\n";
    $vs = $db->fetchAll("SELECT id, title, image FROM transfer_vehicles ORDER BY id");
    foreach ($vs as $v) {
        $img = trim((string)($v['image'] ?? ''));
        echo "Veículo #{$v['id']} - {$v['title']}\n";
        if ($img === '') {
            echo "   (SEM imagem no banco)\n";
            continue;
        }
        echo "   valor no banco: '{$img}'\n";
        // Testa o caminho relativo a /public
        $rel = ltrim($img, '/');
        $path1 = __DIR__ . '/' . $rel;                 // ex public/uploads/x.png
        $path2 = __DIR__ . $img;                        // ex public + /uploads/x.png
        $exists = is_file($path1) || is_file($path2);
        echo "   arquivo no disco: " . ($exists ? 'EXISTE' : 'NAO EXISTE') . "\n";
    }

    echo "\n--- Arquivos vehicle-* na pasta uploads ---\n";
    $files = glob(__DIR__ . '/uploads/vehicle-*');
    if ($files) {
        foreach ($files as $f) echo "   " . basename($f) . " (" . round(filesize($f)/1024) . " KB)\n";
    } else {
        echo "   NENHUM arquivo vehicle-* encontrado no disco.\n";
    }

} catch (\Throwable $e) {
    echo "ERRO ao ler o banco: " . $e->getMessage() . "\n";
}

echo "\n=== FIM ===\n";
