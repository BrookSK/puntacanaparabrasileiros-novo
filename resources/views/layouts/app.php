<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription ?? setting('meta_description', '')) ?>">
    <title><?= e($pageTitle ?? setting('site_name', 'Punta Cana para Brasileiros')) ?> - <?= e(setting('site_name', '')) ?></title>

    <!-- Favicon -->
    <?php if (setting('site_favicon')): ?>
    <link rel="icon" href="<?= e(setting('site_favicon')) ?>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=<?= e(setting('font_primary', 'Poppins')) ?>:wght@300;400;500;600;700;800&family=Caveat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- Custom CSS from settings -->
    <?php if (setting('custom_css')): ?>
    <style><?= setting('custom_css') ?></style>
    <?php endif; ?>

    <!-- Head Scripts from settings -->
    <?= setting('head_scripts', '') ?>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body>
    <!-- Header -->
    <?= partial('header') ?>

    <!-- Flash Messages -->
    <?php if (has_flash('success')): ?>
    <div class="alert alert-success alert-dismissible">
        <span><?= e(flash('success')) ?></span>
        <button class="alert-close">&times;</button>
    </div>
    <?php endif; ?>
    <?php if (has_flash('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <span><?= e(flash('error')) ?></span>
        <button class="alert-close">&times;</button>
    </div>
    <?php endif; ?>
    <?php if (has_flash('info')): ?>
    <div class="alert alert-info alert-dismissible">
        <span><?= e(flash('info')) ?></span>
        <button class="alert-close">&times;</button>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Footer -->
    <?= partial('footer') ?>

    <!-- WhatsApp Float Button -->
    <a href="https://api.whatsapp.com/send?phone=18294582170&text=Oi%2C%20tudo%20bem%3FPara%20ajudar%20voc%C3%AA%20da%20melhor%20forma%2C%20me%20diga%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Seu%20nome%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quando%20voc%C3%AA%20vai%20chegar%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quantas%20pessoas%20s%C3%A3o%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Em%20qual%20hotel%20voc%C3%AA%20vai%20ficar%3FEstou%20aqui%20para%20te%20ajudar%20com%20o%20que%20precisar!" target="_blank" class="whatsapp-float" title="Fale conosco pelo WhatsApp">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.625-1.472A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-2.168 0-4.19-.588-5.932-1.614l-.424-.253-2.744.874.87-2.675-.278-.442A9.78 9.78 0 012.182 12c0-5.422 4.396-9.818 9.818-9.818S21.818 6.578 21.818 12s-4.396 9.818-9.818 9.818z"/></svg>
    </a>

    <!-- JavaScript -->
    <script src="<?= asset('js/app.js') ?>"></script>

    <!-- Body Scripts from settings -->
    <?= setting('body_scripts', '') ?>
</body>
</html>
