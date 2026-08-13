<?php
/**
 * Script para instalar dependências no servidor.
 * Execute via SSH: php install-deps.php
 * Ou acesse via navegador (remova depois de usar).
 */

// Verificar se Composer está disponível
$composerPhar = __DIR__ . '/composer.phar';

// Se composer.phar não existe, baixar
if (!file_exists($composerPhar)) {
    echo "Baixando Composer...\n";
    $installerUrl = 'https://getcomposer.org/installer';
    $installer = file_get_contents($installerUrl);
    if ($installer === false) {
        die("Erro ao baixar o Composer installer.\n");
    }
    file_put_contents(__DIR__ . '/composer-setup.php', $installer);
    
    // Executar installer
    $output = [];
    exec('php ' . escapeshellarg(__DIR__ . '/composer-setup.php') . ' --install-dir=' . escapeshellarg(__DIR__), $output, $exitCode);
    unlink(__DIR__ . '/composer-setup.php');
    
    if ($exitCode !== 0 || !file_exists($composerPhar)) {
        die("Erro ao instalar Composer. Saída:\n" . implode("\n", $output) . "\n");
    }
    echo "Composer instalado com sucesso.\n";
}

// Executar composer install
echo "Instalando dependências...\n";
$output = [];
exec('php ' . escapeshellarg($composerPhar) . ' install --no-dev --optimize-autoloader --working-dir=' . escapeshellarg(__DIR__), $output, $exitCode);

echo implode("\n", $output) . "\n";

if ($exitCode === 0) {
    echo "\n✅ Dependências instaladas com sucesso!\n";
    echo "Agora o download de vouchers em PDF está funcionando.\n";
} else {
    echo "\n❌ Erro ao instalar dependências. Código: {$exitCode}\n";
}
