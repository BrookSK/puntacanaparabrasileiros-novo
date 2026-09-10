<?php
/**
 * PÁGINA TEMPORÁRIA DE DIAGNÓSTICO DE UPLOAD.
 * Acesse em: https://SEU-DOMINIO/diagnostico-upload.php
 * REMOVA este arquivo depois de resolver o problema.
 */
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
$uploadsDir = __DIR__ . '/uploads';

echo "=== DIAGNÓSTICO DE UPLOAD ===\n\n";

echo "[1] Limites do PHP (o que importa pro bug de max_input_vars)\n";
echo "  SAPI (como o PHP roda): " . php_sapi_name() . "\n";
echo "  max_input_vars     = " . ini_get('max_input_vars') . "   (precisa ser > 1000)\n";
echo "  post_max_size      = " . ini_get('post_max_size') . "\n";
echo "  upload_max_filesize= " . ini_get('upload_max_filesize') . "\n";
echo "  max_file_uploads   = " . ini_get('max_file_uploads') . "\n";
echo "  file_uploads       = " . ini_get('file_uploads') . "\n\n";

echo "[2] Pasta de uploads\n";
echo "  Caminho: {$uploadsDir}\n";
echo "  Existe?     " . (is_dir($uploadsDir) ? 'SIM' : 'NÃO') . "\n";
echo "  Gravável?   " . (is_writable($uploadsDir) ? 'SIM' : 'NÃO') . "\n";
$testFile = $uploadsDir . '/_teste_gravacao_' . uniqid() . '.txt';
$canWrite = @file_put_contents($testFile, 'teste');
if ($canWrite !== false) {
    echo "  Teste de escrita: OK (consegui criar e vou apagar)\n";
    @unlink($testFile);
} else {
    echo "  Teste de escrita: FALHOU (não consegui criar arquivo)\n";
}
echo "\n";

echo "[3] Arquivos de passeio (trip-*) já gravados no disco\n";
$tripFiles = glob($uploadsDir . '/trip-*');
if ($tripFiles) {
    echo "  Encontrados " . count($tripFiles) . " arquivo(s):\n";
    foreach ($tripFiles as $f) {
        echo "    - " . basename($f) . " (" . round(filesize($f) / 1024) . " KB)\n";
    }
} else {
    echo "  NENHUM arquivo trip-* no disco. (nenhum upload de passeio gravou ainda)\n";
}
echo "\n";

echo "[4] Log de diagnóstico (últimas 40 linhas)\n";
$logFile = $root . '/storage/upload-debug.log';
if (is_file($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    $lines = array_slice($lines, -40);
    echo "  " . implode("\n  ", $lines) . "\n";
} else {
    echo "  Log ainda não foi gerado (nenhum save de passeio passou pelo código novo).\n";
}
echo "\n";

echo "[5] Galeria dos passeios no banco vs arquivos no disco\n";
try {
    $appConfig = require $root . '/config/app.php';
    spl_autoload_register(function ($class) use ($root) {
        $map = ['Core\\' => $root . '/core/', 'App\\Models\\' => $root . '/app/Models/'];
        foreach ($map as $p => $d) {
            if (str_starts_with($class, $p)) {
                $file = $d . str_replace('\\', '/', substr($class, strlen($p))) . '.php';
                if (is_file($file)) require_once $file;
            }
        }
    });
    $db = \Core\Database::getInstance();
    $rows = $db->fetchAll("SELECT id, title, featured_image, gallery FROM trips ORDER BY id DESC LIMIT 20");
    foreach ($rows as $r) {
        echo "  Passeio #{$r['id']} - {$r['title']}\n";
        $fi = $r['featured_image'] ?? '';
        if ($fi) {
            $path = __DIR__ . $fi;
            echo "    featured_image: {$fi} -> " . (is_file($path) ? 'EXISTE' : 'ARQUIVO NÃO EXISTE') . "\n";
        }
        $gal = !empty($r['gallery']) ? json_decode($r['gallery'], true) : [];
        if (is_array($gal) && $gal) {
            foreach ($gal as $g) {
                $path = __DIR__ . $g;
                echo "    galeria: {$g} -> " . (is_file($path) ? 'EXISTE' : 'ARQUIVO NÃO EXISTE') . "\n";
            }
        }
    }
} catch (\Throwable $e) {
    echo "  Não consegui ler o banco: " . $e->getMessage() . "\n";
}

echo "\n=== FIM ===\n";
