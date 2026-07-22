<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Login') ?> - <?= e(setting('site_name', 'Punta Cana para Brasileiros')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <?php if (setting('site_logo')): ?>
                    <img src="<?= e(setting('site_logo')) ?>" alt="<?= e(setting('site_name', '')) ?>">
                <?php else: ?>
                    <h2><?= e(setting('site_name', 'Punta Cana para Brasileiros')) ?></h2>
                <?php endif; ?>
            </div>

            <!-- Flash Messages -->
            <?php if (has_flash('success')): ?>
            <div class="alert alert-success"><?= e(flash('success')) ?></div>
            <?php endif; ?>
            <?php if (has_flash('error')): ?>
            <div class="alert alert-danger"><?= e(flash('error')) ?></div>
            <?php endif; ?>

            <?= $content ?>
        </div>

        <div class="auth-footer">
            <a href="/">Voltar para o site</a>
        </div>
    </div>
</body>
</html>
