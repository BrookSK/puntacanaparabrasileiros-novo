<!-- Hero da Categoria -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1><?= e($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
            <p><?= e($category['description']) ?></p>
            <?php else: ?>
            <p>Explore os melhores passeios de <?= e($category['name']) ?> em Punta Cana.</p>
            <?php endif; ?>
            <a href="/passeios" class="btn btn-secondary">← Voltar para Todos os Passeios</a>
        </div>
    </div>
</section>

<!-- Listagem da Categoria -->
<section class="section section-todos-passeios">
    <div class="container">
        <div class="passeios-layout">
            <!-- Sidebar -->
            <aside class="passeios-sidebar">
                <div class="sidebar-header">
                    <h3>Categorias</h3>
                    <a href="/passeios" class="sidebar-clear">Ver todas</a>
                </div>

                <div class="sidebar-filter-options">
                    <?php foreach ($categories as $cat): ?>
                    <?php if ($cat['trip_count'] > 0): ?>
                    <a href="/passeios/categoria/<?= e($cat['slug']) ?>" class="sidebar-cat-link <?= $cat['slug'] === $category['slug'] ? 'active' : '' ?>">
                        <span><?= e($cat['name']) ?></span>
                        <span class="sidebar-checkbox-count"><?= (int)$cat['trip_count'] ?></span>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="sidebar-filter-group" style="margin-top:20px;padding-top:16px;border-top:1px solid #f0f0f0;">
                    <h4 class="sidebar-filter-title">Ordenar por</h4>
                    <form method="GET" action="/passeios/categoria/<?= e($category['slug']) ?>">
                        <select name="ordenar" class="form-control" onchange="this.form.submit()">
                            <option value="">Relevância</option>
                            <option value="preco_asc" <?= ($currentOrder ?? '') === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                            <option value="preco_desc" <?= ($currentOrder ?? '') === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                            <option value="recente" <?= ($currentOrder ?? '') === 'recente' ? 'selected' : '' ?>>Mais Recente</option>
                        </select>
                    </form>
                </div>
            </aside>

            <!-- Conteúdo -->
            <div class="passeios-content">
                <div class="passeios-content-header">
                    <p class="passeios-count"><?= $trips['total'] ?? count($trips['items']) ?> passeio<?= ($trips['total'] ?? count($trips['items'])) > 1 ? 's' : '' ?> em <strong><?= e($category['name']) ?></strong></p>
                </div>

                <?php if (empty($trips['items'])): ?>
                <div class="empty-state">
                    <p>Nenhum passeio encontrado nesta categoria.</p>
                    <a href="/passeios" class="btn btn-primary">Ver Todos os Passeios</a>
                </div>
                <?php else: ?>
                <div class="passeios-list">
                    <?php foreach ($trips['items'] as $trip): ?>
                    <div class="passeio-card-horizontal">
                        <a href="/passeios/<?= e($trip['slug']) ?>" class="passeio-card-img">
                            <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                            <?php if (!empty($trip['featured'])): ?>
                            <span class="passeio-card-badge">Destaque</span>
                            <?php endif; ?>
                        </a>
                        <div class="passeio-card-body">
                            <div class="passeio-card-top">
                                <h3 class="passeio-card-title">
                                    <a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a>
                                </h3>
                                <div class="passeio-card-price">
                                    <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                                    <span class="price-from"><?= money($trip['regular_price']) ?></span>
                                    <?php endif; ?>
                                    <span class="price-current"><?= money($trip['min_price'] ?? 0) ?></span>
                                </div>
                            </div>
                            <div class="passeio-card-meta">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    Punta Cana
                                </span>
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?= e($trip['duration'] ?? '') ?> <?= ($trip['duration_unit'] ?? 'hours') === 'hours' ? 'Horas' : 'Dias' ?>
                                </span>
                            </div>
                            <p class="passeio-card-desc"><?= e(truncate($trip['short_description'] ?? '', 160)) ?></p>
                            <div class="passeio-card-footer">
                                <a href="/passeios/<?= e($trip['slug']) ?>" class="btn btn-primary btn-sm">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($trips['total_pages'] > 1): ?>
                <nav class="pagination">
                    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?>&ordenar=<?= e($currentOrder ?? '') ?>"
                       class="page-link <?= $i === $trips['current_page'] ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
