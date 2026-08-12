<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription ?? '') ?>">
    <title><?= e($pageTitle ?? 'Catálogo de Experiências') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="<?= asset('css/catalog.css') ?>?v=1.2">
</head>
<body>
    <?= $content ?>

    <!-- Google Translate -->
    <div id="google_translate_element" style="display:none;"></div>
    <script>
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'pt',
            includedLanguages: 'pt,en,es',
            autoDisplay: false
        }, 'google_translate_element');
    }

    function translatePage(lang) {
        if (lang === 'pt') {
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=.' + location.hostname;
            location.reload();
            return;
        }
        var langCode = '/pt/' + lang;
        document.cookie = 'googtrans=' + langCode + '; path=/;';
        document.cookie = 'googtrans=' + langCode + '; path=/; domain=.' + location.hostname;
        location.reload();
    }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <style>
    .goog-te-banner-frame { display: none !important; }
    body { top: 0 !important; }
    .goog-te-gadget { display: none !important; }
    .skiptranslate { display: none !important; }
    </style>
</body>
</html>
