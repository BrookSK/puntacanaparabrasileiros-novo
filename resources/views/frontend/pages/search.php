<!-- Hero Pesquisa -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Pesquisar</h1>
            <p>Encontre passeios, experiências e artigos do blog</p>
        </div>
    </div>
</section>

<!-- Campo de Busca -->
<section class="section section-search-page">
    <div class="container">
        <div class="search-page-box">
            <form action="/pesquisa" method="GET" class="search-page-form">
                <div class="search-page-input-wrap">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="q" value="<?= e($query) ?>" placeholder="Digite o que você procura..." class="search-page-input" autofocus>
                    <button type="submit" class="search-page-btn">Buscar</button>
                </div>
            </form>

            <?php if (!$query): ?>
            <!-- Sugestões quando não tem busca -->
            <div class="search-suggestions">
                <p class="search-suggestions-title">Sugestões populares:</p>
                <div class="search-tags">
                    <a href="/pesquisa?q=Saona" class="search-tag">Isla Saona</a>
                    <a href="/pesquisa?q=Catamaran" class="search-tag">Catamaran</a>
                    <a href="/pesquisa?q=Buggy" class="search-tag">Buggies</a>
                    <a href="/pesquisa?q=Golfinho" class="search-tag">Golfinhos</a>
                    <a href="/pesquisa?q=Transfer" class="search-tag">Transfer</a>
                    <a href="/pesquisa?q=Coco+Bongo" class="search-tag">Coco Bongo</a>
                    <a href="/pesquisa?q=Snorkel" class="search-tag">Snorkel</a>
                    <a href="/pesquisa?q=Santo+Domingo" class="search-tag">Santo Domingo</a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($query): ?>
        <!-- Resultados -->
        <div class="search-results">

            <!-- Resultados de Passeios -->
            <?php if (!empty($results['trips'])): ?>
            <div class="search-results-section">
                <div class="search-results-header">
                    <h3>Passeios e Experiências</h3>
                    <span class="search-results-count"><?= count($results['trips']) ?> resultado<?= count($results['trips']) > 1 ? 's' : '' ?></span>
                </div>
                <div class="destaque-trips-grid">
                    <?php foreach ($results['trips'] as $trip): ?>
                    <div class="destaque-trip-card">
                        <a href="/passeios/<?= e($trip['slug']) ?>" class="destaque-trip-image">
                            <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                        </a>
                        <div class="destaque-trip-body">
                            <h3 class="destaque-trip-title">
                                <a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a>
                            </h3>
                            <p class="destaque-trip-desc"><?= e(truncate($trip['short_description'] ?? '', 80)) ?></p>
                            <div class="destaque-trip-footer">
                                <div class="destaque-trip-meta">
                                    <span class="meta-location">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Punta Cana
                                    </span>
                                    <span class="meta-duration">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?= e($trip['duration'] ?? '') ?> <?= ($trip['duration_unit'] ?? 'hours') === 'hours' ? 'Horas' : 'Dias' ?>
                                    </span>
                                </div>
                                <span class="destaque-trip-price"><?= money($trip['min_price'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="/passeios?busca=<?= e(urlencode($query)) ?>" class="search-view-all">Ver todos os passeios &rarr;</a>
            </div>
            <?php endif; ?>

            <!-- Resultados do Blog -->
            <?php if (!empty($results['blog'])): ?>
            <div class="search-results-section">
                <div class="search-results-header">
                    <h3>Artigos do Blog</h3>
                    <span class="search-results-count"><?= count($results['blog']) ?> resultado<?= count($results['blog']) > 1 ? 's' : '' ?></span>
                </div>
                <div class="search-blog-list">
                    <?php foreach ($results['blog'] as $post): ?>
                    <a href="/blog/<?= e($post['slug']) ?>" class="search-blog-item">
                        <div class="search-blog-img">
                            <img src="<?= e($post['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="" loading="lazy">
                        </div>
                        <div class="search-blog-info">
                            <h4><?= e($post['title']) ?></h4>
                            <p><?= e(truncate($post['excerpt'] ?? '', 100)) ?></p>
                            <span class="search-blog-date"><?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Nenhum resultado -->
            <?php if (empty($results['trips']) && empty($results['blog'])): ?>
            <div class="search-no-results">
                <div class="search-no-results-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="8" x2="14" y2="14"/><line x1="14" y1="8" x2="8" y2="14"/></svg>
                </div>
                <h3>Nenhum resultado encontrado</h3>
                <p>Não encontramos nada para "<strong><?= e($query) ?></strong>". Tente buscar com outras palavras.</p>
                <div class="search-suggestions" style="margin-top:24px;">
                    <p class="search-suggestions-title">Sugestões:</p>
                    <div class="search-tags">
                        <a href="/pesquisa?q=Saona" class="search-tag">Isla Saona</a>
                        <a href="/pesquisa?q=Catamaran" class="search-tag">Catamaran</a>
                        <a href="/pesquisa?q=Transfer" class="search-tag">Transfer</a>
                        <a href="/pesquisa?q=Coco+Bongo" class="search-tag">Coco Bongo</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <?php endif; ?>
    </div>
</section>
