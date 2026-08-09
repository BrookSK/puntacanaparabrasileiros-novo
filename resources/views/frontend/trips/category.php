<!-- Hero da Categoria -->
<section class="cat-hero">
    <div class="container">
        <h1 class="cat-hero-title"><?= e($category['name']) ?></h1>
        <?php if (!empty($category['description'])): ?>
        <p class="cat-hero-desc"><?= e($category['description']) ?></p>
        <?php endif; ?>
        <a href="/passeios" class="cat-hero-back">← Voltar para Todos os Passeios</a>
    </div>
</section>

<!-- Listagem -->
<section class="cat-listing">
    <div class="container">

        <!-- Header de resultados -->
        <div class="cat-results-header">
            <p class="cat-results-count">
                <strong><?= $trips['total'] ?? count($trips['items']) ?></strong> passeio<?= ($trips['total'] ?? count($trips['items'])) > 1 ? 's' : '' ?> em <strong><?= e($category['name']) ?></strong>
            </p>
        </div>

        <div class="cat-grid">
            <!-- Sidebar Filtros -->
            <aside class="cat-filters">
                <form method="GET" action="/passeios/categoria/<?= e($category['slug']) ?>" id="filters-form">

                    <div class="cat-filters-header">
                        <span class="cat-filters-label">Filtros</span>
                        <a href="/passeios/categoria/<?= e($category['slug']) ?>" class="cat-filters-clear">Limpar tudo</a>
                    </div>

                    <!-- Destino -->
                    <?php if (!empty($destinations)): ?>
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Destino</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body">
                            <?php foreach ($destinations as $dest): ?>
                            <label class="filter-option">
                                <input type="checkbox" name="destino[]" value="<?= e($dest['slug']) ?>" <?= in_array($dest['slug'], $currentFilters['destino'] ?? []) ? 'checked' : '' ?>>
                                <span class="filter-option-check"></span>
                                <span class="filter-option-label"><?= e($dest['name']) ?></span>
                                <span class="filter-option-count"><?= (int)$dest['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Preço -->
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Preço</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body">
                            <div class="filter-range">
                                <div class="filter-range-labels">
                                    <span class="filter-range-val">$<span id="price-min-val"><?= (int)($currentFilters['preco_min'] ?? $priceRange['min'] ?? 0) ?></span></span>
                                    <span class="filter-range-val">$<span id="price-max-val"><?= (int)($currentFilters['preco_max'] ?? $priceRange['max'] ?? 500) ?></span></span>
                                </div>
                                <div class="filter-range-track">
                                    <input type="range" name="preco_min" id="price-min" min="<?= (int)($priceRange['min'] ?? 0) ?>" max="<?= (int)($priceRange['max'] ?? 500) ?>" value="<?= (int)($currentFilters['preco_min'] ?? $priceRange['min'] ?? 0) ?>" step="5">
                                    <input type="range" name="preco_max" id="price-max" min="<?= (int)($priceRange['min'] ?? 0) ?>" max="<?= (int)($priceRange['max'] ?? 500) ?>" value="<?= (int)($currentFilters['preco_max'] ?? $priceRange['max'] ?? 500) ?>" step="5">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duração -->
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Duração</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body">
                            <div class="filter-range">
                                <div class="filter-range-labels">
                                    <span class="filter-range-val"><span id="duration-min-val"><?= (int)($currentFilters['duracao_min'] ?? 0) ?></span> dias</span>
                                    <span class="filter-range-val"><span id="duration-max-val"><?= (int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1) ?></span> dia<?= ((int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1)) > 1 ? 's' : '' ?></span>
                                </div>
                                <div class="filter-range-track">
                                    <input type="range" name="duracao_min" id="duration-min" min="0" max="<?= (int)($durationRange['max'] ?? 1) ?>" value="<?= (int)($currentFilters['duracao_min'] ?? 0) ?>" step="1">
                                    <input type="range" name="duracao_max" id="duration-max" min="0" max="<?= (int)($durationRange['max'] ?? 1) ?>" value="<?= (int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1) ?>" step="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atividades -->
                    <?php if (!empty($activities)): ?>
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Atividades</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body filter-block-expandable">
                            <?php foreach ($activities as $i => $act): ?>
                            <label class="filter-option <?= $i >= 5 ? 'filter-option-hidden' : '' ?>">
                                <input type="checkbox" name="atividade[]" value="<?= e($act['slug']) ?>" <?= in_array($act['slug'], $currentFilters['atividade'] ?? []) ? 'checked' : '' ?>>
                                <span class="filter-option-check"></span>
                                <span class="filter-option-label"><?= e($act['name']) ?></span>
                                <span class="filter-option-count"><?= (int)$act['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($activities) > 5): ?>
                            <button type="button" class="filter-expand-btn" data-expand>
                                <span class="expand-text">Mostrar todos (<?= count($activities) ?>)</span>
                                <svg class="expand-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tipos de Viagem -->
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Tipos de Viagem</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body filter-block-expandable">
                            <?php $catIdx = 0; foreach ($categories as $cat): ?>
                            <?php if ($cat['trip_count'] > 0): ?>
                            <label class="filter-option <?= $catIdx >= 7 ? 'filter-option-hidden' : '' ?>">
                                <input type="checkbox" name="tipo[]" value="<?= e($cat['slug']) ?>" <?= in_array($cat['slug'], $currentFilters['tipo'] ?? []) ? 'checked' : '' ?> <?= $cat['slug'] === $category['slug'] ? 'checked' : '' ?>>
                                <span class="filter-option-check"></span>
                                <span class="filter-option-label"><?= e($cat['name']) ?></span>
                                <span class="filter-option-count"><?= (int)$cat['trip_count'] ?></span>
                            </label>
                            <?php $catIdx++; endif; ?>
                            <?php endforeach; ?>
                            <?php if ($catIdx > 7): ?>
                            <button type="button" class="filter-expand-btn" data-expand>
                                <span class="expand-text">Mostrar todos (<?= $catIdx ?>)</span>
                                <svg class="expand-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Tags</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body filter-block-expandable">
                            <?php foreach ($tags as $i => $tag): ?>
                            <label class="filter-option <?= $i >= 5 ? 'filter-option-hidden' : '' ?>">
                                <input type="checkbox" name="tag[]" value="<?= e($tag['slug']) ?>" <?= in_array($tag['slug'], $currentFilters['tag'] ?? []) ? 'checked' : '' ?>>
                                <span class="filter-option-check"></span>
                                <span class="filter-option-label"><?= e($tag['name']) ?></span>
                                <span class="filter-option-count"><?= (int)$tag['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($tags) > 5): ?>
                            <button type="button" class="filter-expand-btn" data-expand>
                                <span class="expand-text">Mostrar todos (<?= count($tags) ?>)</span>
                                <svg class="expand-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Datas de Início -->
                    <?php if (!empty($availableDates)): ?>
                    <div class="filter-block" data-collapsible>
                        <button type="button" class="filter-block-title" data-toggle-filter>
                            <span>Datas de Início</span>
                            <svg class="filter-arrow" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="filter-block-body filter-block-expandable">
                            <?php foreach ($availableDates as $i => $dateGroup): ?>
                            <label class="filter-option <?= $i >= 5 ? 'filter-option-hidden' : '' ?>">
                                <input type="checkbox" name="data[]" value="<?= e($dateGroup['month_key']) ?>" <?= in_array($dateGroup['month_key'], $currentFilters['data'] ?? []) ? 'checked' : '' ?>>
                                <span class="filter-option-check"></span>
                                <span class="filter-option-label"><?= e($dateGroup['label']) ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($availableDates) > 5): ?>
                            <button type="button" class="filter-expand-btn" data-expand>
                                <span class="expand-text">Mostrar todos (<?= count($availableDates) ?>)</span>
                                <svg class="expand-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ordenar -->
                    <div class="filter-block filter-block-order">
                        <label class="filter-block-title filter-block-title-static">Ordenar por</label>
                        <select name="ordenar" class="filter-select">
                            <option value="relevancia" <?= ($currentOrder ?? '') === 'relevancia' || empty($currentOrder) ? 'selected' : '' ?>>Relevância</option>
                            <option value="preco_asc" <?= ($currentOrder ?? '') === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                            <option value="preco_desc" <?= ($currentOrder ?? '') === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                            <option value="recente" <?= ($currentOrder ?? '') === 'recente' ? 'selected' : '' ?>>Mais Recente</option>
                        </select>
                    </div>

                    <button type="submit" class="filter-apply-btn">Aplicar Filtros</button>
                </form>
            </aside>

            <!-- Lista de Passeios -->
            <div class="cat-content">
                <?php if (empty($trips['items'])): ?>
                <div class="cat-empty">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <p>Nenhum passeio encontrado com os filtros selecionados.</p>
                    <a href="/passeios/categoria/<?= e($category['slug']) ?>" class="btn btn-primary">Limpar Filtros</a>
                </div>
                <?php else: ?>
                <div class="cat-trips-list">
                    <?php foreach ($trips['items'] as $trip): ?>
                    <article class="trip-card">
                        <a href="/passeios/<?= e($trip['slug']) ?>" class="trip-card-image">
                            <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                            <?php if (!empty($trip['featured'])): ?>
                            <span class="trip-card-badge">Destaque</span>
                            <?php endif; ?>
                        </a>
                        <div class="trip-card-content">
                            <div class="trip-card-header">
                                <h3 class="trip-card-name">
                                    <a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a>
                                </h3>
                                <div class="trip-card-pricing">
                                    <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                                    <span class="trip-card-price-old"><?= money($trip['regular_price']) ?></span>
                                    <?php endif; ?>
                                    <span class="trip-card-price"><?= money($trip['min_price'] ?? 0) ?></span>
                                </div>
                            </div>
                            <div class="trip-card-meta">
                                <span class="trip-card-meta-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    Punta Cana
                                </span>
                                <span class="trip-card-meta-item">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?= e($trip['duration'] ?? '') ?> <?= ($trip['duration_unit'] ?? 'hours') === 'hours' ? 'Horas' : 'Dias' ?>
                                </span>
                            </div>
                            <p class="trip-card-desc"><?= e(truncate($trip['short_description'] ?? '', 140)) ?></p>
                            <div class="trip-card-footer">
                                <a href="/passeios/<?= e($trip['slug']) ?>" class="trip-card-btn">Ver Detalhes</a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <?php if ($trips['total_pages'] > 1): ?>
                <nav class="cat-pagination">
                    <?php
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $baseQuery = http_build_query($queryParams);
                    ?>
                    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $baseQuery ? '&' . $baseQuery : '' ?>"
                       class="cat-pagination-link <?= $i === $trips['current_page'] ? 'active' : '' ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collapse/Expand filter blocks
    document.querySelectorAll('[data-toggle-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            btn.closest('[data-collapsible]').classList.toggle('collapsed');
        });
    });

    // Show more / Show less
    document.querySelectorAll('[data-expand]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var body = btn.closest('.filter-block-expandable');
            var isExpanded = body.classList.toggle('expanded');
            var text = btn.querySelector('.expand-text');
            if (isExpanded) {
                text.textContent = 'Mostrar menos';
                btn.classList.add('is-expanded');
            } else {
                text.textContent = btn.dataset.originalText || text.textContent;
                btn.classList.remove('is-expanded');
            }
        });
        btn.dataset.originalText = btn.querySelector('.expand-text').textContent;
    });

    // Price range
    var pMin = document.getElementById('price-min');
    var pMax = document.getElementById('price-max');
    var pMinV = document.getElementById('price-min-val');
    var pMaxV = document.getElementById('price-max-val');
    if (pMin && pMax) {
        pMin.addEventListener('input', function() {
            if (+pMin.value > +pMax.value) pMin.value = pMax.value;
            pMinV.textContent = pMin.value;
        });
        pMax.addEventListener('input', function() {
            if (+pMax.value < +pMin.value) pMax.value = pMin.value;
            pMaxV.textContent = pMax.value;
        });
    }

    // Duration range
    var dMin = document.getElementById('duration-min');
    var dMax = document.getElementById('duration-max');
    var dMinV = document.getElementById('duration-min-val');
    var dMaxV = document.getElementById('duration-max-val');
    if (dMin && dMax) {
        dMin.addEventListener('input', function() {
            if (+dMin.value > +dMax.value) dMin.value = dMax.value;
            dMinV.textContent = dMin.value;
        });
        dMax.addEventListener('input', function() {
            if (+dMax.value < +dMin.value) dMax.value = dMin.value;
            dMaxV.textContent = dMax.value;
        });
    }
});
</script>
