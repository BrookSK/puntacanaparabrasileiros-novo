<?php
declare(strict_types=1);

/**
 * Configurações gerais da aplicação.
 */
return [
    'name' => 'Punta Cana para Brasileiros',
    'debug' => true, // Alterar para false em produção
    'url' => 'http://localhost',
    'timezone' => 'America/Santo_Domingo',
    'locale' => 'pt_BR',

    // Paths
    'views_path' => BASE_PATH . '/resources/views',
    'storage_path' => BASE_PATH . '/storage',
    'uploads_path' => BASE_PATH . '/public/uploads',
    'vouchers_path' => BASE_PATH . '/public/uploads/vouchers',

    // Sessão
    'session_lifetime' => 604800, // 7 dias em segundos
    'cart_expiration' => 604800, // 7 dias

    // Upload
    'max_upload_size' => 10 * 1024 * 1024, // 10MB
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    'allowed_file_types' => ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'],
];
