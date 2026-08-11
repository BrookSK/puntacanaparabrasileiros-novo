<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'criativos']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Criativos</h1>
                        <p class="aff-page-subtitle">Materiais promocionais para divulgar em suas redes sociais</p>
                    </div>
                </div>

                <div class="aff-card">
                    <div class="aff-card-header">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--text-green)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <h3 class="aff-card-title">Materiais Disponíveis</h3>
                    </div>
                    <p class="aff-card-desc">Use estes criativos para promover os passeios em Punta Cana. Todos os materiais já incluem seu link de afiliado.</p>

                    <div class="aff-creatives-grid">
                        <?php if (!empty($creatives ?? [])): ?>
                            <?php foreach ($creatives as $creative): ?>
                            <div class="aff-creative-card">
                                <div class="aff-creative-img">
                                    <img src="<?= e($creative['image_url'] ?? asset('images/placeholder.jpg')) ?>" alt="<?= e($creative['title'] ?? 'Criativo') ?>" loading="lazy">
                                </div>
                                <div class="aff-creative-info">
                                    <span class="aff-creative-title"><?= e($creative['title'] ?? 'Material Promocional') ?></span>
                                    <div class="aff-creative-actions">
                                        <a href="<?= e($creative['image_url'] ?? '#') ?>" target="_blank" class="btn btn-sm btn-outline">Ver</a>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="copyAffLink(this)" data-url="<?= e(setting('site_url', 'https://puntacanaparabrasileiros.com')) ?>/?ref=<?= (int)($affiliate['id'] ?? 1) ?>">Copiar Link</button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="aff-empty-state">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <p>Novos criativos serão disponibilizados em breve.</p>
                                <span>Enquanto isso, use seu link de afiliado para divulgar os passeios.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</section>

<script>
function copyAffLink(btn) {
    var url = btn.getAttribute('data-url');
    navigator.clipboard.writeText(url).then(function() {
        var orig = btn.textContent;
        btn.textContent = 'Copiado!';
        setTimeout(function() { btn.textContent = orig; }, 2000);
    });
}
</script>
