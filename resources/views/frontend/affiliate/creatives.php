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
                                    <?php
                                    $fileUrl = $creative['image_url'] ?? '';
                                    $ext = strtolower(pathinfo($fileUrl, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    $isVideo = in_array($ext, ['mp4', 'webm', 'mov']);
                                    $isPdf = $ext === 'pdf';
                                    ?>
                                    <?php if ($isImage): ?>
                                    <img src="<?= e($fileUrl) ?>" alt="<?= e($creative['title'] ?? 'Criativo') ?>" loading="lazy">
                                    <?php elseif ($isVideo): ?>
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#1e293b;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                    <?php elseif ($isPdf): ?>
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#fef2f2;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <?php else: ?>
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f1f5f9;">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="aff-creative-info">
                                    <span class="aff-creative-title"><?= e($creative['title'] ?? 'Material Promocional') ?></span>
                                    <div class="aff-creative-actions">
                                        <a href="<?= e($fileUrl) ?>" download class="btn btn-sm btn-primary" title="Baixar">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:3px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                            Baixar
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline" onclick="copyAffLink(this)" data-url="<?= e(setting('site_url', 'https://puntacanaparabrasileiros.com')) ?>/?ref=<?= (int)($affiliate['id'] ?? 1) ?>">Copiar Link</button>
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
