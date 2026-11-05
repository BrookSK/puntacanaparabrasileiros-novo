<!-- Página legal com texto editado no painel (Admin → Políticas e Câmbio) -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1><?= e($heading) ?></h1>
        </div>
    </div>
</section>

<section class="section section-legal-content">
    <div class="container">
        <div class="legal-content">
            <?= $contentHtml /* já escapado por simple_text_to_html() */ ?>

            <?php if (!empty($showCancelCta)): ?>
            <div class="legal-cta">
                <a href="/minha-conta/cancelamentos" class="btn btn-primary">Cancelamentos</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
