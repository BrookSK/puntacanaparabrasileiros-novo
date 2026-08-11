<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'WhatsApp') ?> - PCB</title>
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/whatsapp.css') ?>?v=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="current-user-id" content="<?= e(current_user()['id'] ?? '') ?>">
    <meta name="current-user-name" content="<?= e(current_user()['first_name'] ?? '') ?>">
</head>
<body class="wpp-body">
    <?= $content ?>
    <script src="<?= asset('js/whatsapp.js') ?>?v=1.0"></script>
</body>
</html>
