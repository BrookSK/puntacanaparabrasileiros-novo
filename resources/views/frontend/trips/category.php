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
            <!-- Sidebar com Filtros -->
            <aside class="passeios-sidebar">
                <div class="sidebar-header">
                    <h3>Critérios</h3>
                    <a href="/passeios/categoria/<?= e($category['slug']) ?>" class="sidebar-clear-all">Limpar tudo</a>
                </div>

                <form method="GET" action="/passeios/categoria/<?= e($category['slug']) ?>" id="filters-form">

                    <!-- Destino -->
                    <?php if (!empty($destinations)): ?>
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Destino
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options">
                            <?php foreach ($destinations as $dest): ?>
                            <label class="sidebar-checkbox">
                                <input type="checkbox" name="destino[]" value="<?= e($dest['slug']) ?>" <?= in_array($dest['slug'], $currentFilters['destino'] ?? []) ? 'checked' : '' ?>>
                                <span class="sidebar-checkbox-label"><?= e($dest['name']) ?></span>
                                <span class="sidebar-checkbox-count"><?= (int)$dest['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Preço -->
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Preço
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options">
                            <div class="price-range-slider">
                                <div class="range-values">
                                    <span>$<span id="price-min-val"><?= (int)($currentFilters['preco_min'] ?? $priceRange['min'] ?? 0) ?></span></span>
                                    <span>$<span id="price-max-val"><?= (int)($currentFilters['preco_max'] ?? $priceRange['max'] ?? 500) ?></span></span>
                                </div>
                                <div class="range-slider-track">
                                    <input type="range" name="preco_min" id="price-min" min="<?= (int)($priceRange['min'] ?? 0) ?>" max="<?= (int)($priceRange['max'] ?? 500) ?>" value="<?= (int)($currentFilters['preco_min'] ?? $priceRange['min'] ?? 0) ?>" step="5">
                                    <input type="range" name="preco_max" id="price-max" min="<?= (int)($priceRange['min'] ?? 0) ?>" max="<?= (int)($priceRange['max'] ?? 500) ?>" value="<?= (int)($currentFilters['preco_max'] ?? $priceRange['max'] ?? 500) ?>" step="5">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duração -->
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Duração
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options">
                            <div class="duration-range-slider">
                                <div class="range-values">
                                    <span><span id="duration-min-val"><?= (int)($currentFilters['duracao_min'] ?? 0) ?></span> dias</span>
                                    <span><span id="duration-max-val"><?= (int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1) ?></span> dia<?= ((int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1)) > 1 ? 's' : '' ?></span>
                                </div>
                                <div class="range-slider-track">
                                    <input type="range" name="duracao_min" id="duration-min" min="0" max="<?= (int)($durationRange['max'] ?? 1) ?>" value="<?= (int)($currentFilters['duracao_min'] ?? 0) ?>" step="1">
                                    <input type="range" name="duracao_max" id="duration-max" min="0" max="<?= (int)($durationRange['max'] ?? 1) ?>" value="<?= (int)($currentFilters['duracao_max'] ?? $durationRange['max'] ?? 1) ?>" step="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atividades -->
                    <?php if (!empty($activities)): ?>
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Atividades
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options filter-options-collapsible">
                            <?php foreach ($activities as $i => $act): ?>
                            <label class="sidebar-checkbox <?= $i >= 5 ? 'filter-hidden-item' : '' ?>">
                                <input type="checkbox" name="atividade[]" value="<?= e($act['slug']) ?>" <?= in_array($act['slug'], $currentFilters['atividade'] ?? []) ? 'checked' : '' ?>>
                                <span class="sidebar-checkbox-label"><?= e($act['name']) ?></span>
                                <span class="sidebar-checkbox-count"><?= (int)$act['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($activities) > 5): ?>
                            <button type="button" class="filter-show-more" data-show-more>Mostrar todos <?= count($activities) ?> ↓</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tipos de Viagem (Categorias) -->
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Tipos de Viagem
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options filter-options-collapsible">
                            <?php foreach ($categories as $i => $cat): ?>
                            <?php if ($cat['trip_count'] > 0): ?>
                            <label class="sidebar-checkbox <?= $i >= 7 ? 'filter-hidden-item' : '' ?>">
                                <input type="checkbox" name="tipo[]" value="<?= e($cat['slug']) ?>" <?= in_array($cat['slug'], $currentFilters['tipo'] ?? []) ? 'checked' : '' ?> <?= $cat['slug'] === $category['slug'] ? 'checked' : '' ?>>
                                <span class="sidebar-checkbox-label"><?= e($cat['name']) ?></span>
                                <span class="sidebar-checkbox-count"><?= (int)$cat['trip_count'] ?></span>
                            </label>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (count($categories) > 7): ?>
                            <button type="button" class="filter-show-more" data-show-more>Mostrar todos <?= count($categories) ?> ↓</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tags -->
                    <?php if (!empty($tags)): ?>
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Tags
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options filter-options-collapsible">
                            <?php foreach ($tags as $i => $tag): ?>
                            <label class="sidebar-checkbox <?= $i >= 5 ? 'filter-hidden-item' : '' ?>">
                                <input type="checkbox" name="tag[]" value="<?= e($tag['slug']) ?>" <?= in_array($tag['slug'], $currentFilters['tag'] ?? []) ? 'checked' : '' ?>>
                                <span class="sidebar-checkbox-label"><?= e($tag['name']) ?></span>
                                <span class="sidebar-checkbox-count"><?= (int)$tag['trip_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($tags) > 5): ?>
                            <button type="button" class="filter-show-more" data-show-more>Mostrar todos <?= count($tags) ?> ↓</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Datas de Início -->
                    <?php if (!empty($availableDates)): ?>
                    <div class="sidebar-filter-group" data-collapsible>
                        <h4 class="sidebar-filter-title" data-toggle-filter>
                            Datas de Início
                            <svg class="filter-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </h4>
                        <div class="sidebar-filter-options filter-options-collapsible">
                            <?php foreach ($availableDates as $i => $dateGroup): ?>
                            <label class="sidebar-checkbox <?= $i >= 5 ? 'filter-hidden-item' : '' ?>">
                                <input type="checkbox" name="data[]" value="<?= e($dateGroup['month_key']) ?>" <?= in_array($dateGroup['month_key'], $currentFilters['data'] ?? []) ? 'checked' : '' ?>>
                                <span class="sidebar-checkbox-label"><?= e($dateGroup['label']) ?></span>
                            </label>
                            <?php endforeach; ?>
                            <?php if (count($availableDates) > 5): ?>
                            <button type="button" class="filter-show-more" data-show-more>Mostrar todos <?= count($availableDates) ?> ↓</button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ordenar por -->
                    <div class="sidebar-filter-group">
                        <h4 class="sidebar-filter-title">Ordenar por</h4>
                        <select name="ordenar" class="form-control">
                            <option value="relevancia" <?= ($currentOrder ?? '') === 'relevancia' || empty($currentOrder) ? 'selected' : '' ?>>Relevância</option>
                            <option value="preco_asc" <?= ($currentOrder ?? '') === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                            <option value="preco_desc" <?= ($currentOrder ?? '') === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                            <option value="recente" <?= ($currentOrder ?? '') === 'recente' ? 'selected' : '' ?>>Mais Recente</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-filter-apply">Aplicar Filtros</button>
                </form>
            </aside>

            <!-- Conteúdo -->
            <div class="passeios-content">
                <div class="passeios-content-header">
                    <p class="passeios-count"><?= $trips['total'] ?? count($trips['items']) ?> passeio<?= ($trips['total'] ?? count($trips['items'])) > 1 ? 's' : '' ?> em <strong><?= e($category['name']) ?></strong></p>
                    <select class="sort-select-mobile" onchange="document.querySelector('[name=ordenar]').value=this.value; document.getElementById('filters-form').submit();">
                        <option value="relevancia" <?= ($currentOrder ?? '') === 'relevancia' || empty($currentOrder) ? 'selected' : '' ?>>Relevância</option>
                        <option value="preco_asc" <?= ($currentOrder ?? '') === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                        <option value="preco_desc" <?= ($currentOrder ?? '') === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                        <option value="recente" <?= ($currentOrder ?? '') === 'recente' ? 'selected' : '' ?>>Mais Recente</option>
                    </select>
                </div>

                <?php if (empty($trips['items'])): ?>
                <div class="empty-state">
                    <p>Nenhum passeio encontrado com os filtros selecionados.</p>
                    <a href="/passeios/categoria/<?= e($category['slug']) ?>" class="btn btn-primary">Limpar Filtros</a>
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

                            <?php if (!empty($trip['next_dates'])): ?>
                            <div class="passeio-card-dates">
                                <small>Próxima Partida</small>
                                <div class="date-badges">
                                    <?php foreach (array_slice($trip['next_dates'], 0, 3) as $date): ?>
                                    <span class="date-badge"><?= date('d/m/Y', strtotime($date['date'])) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

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
                    <?php
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    $baseQuery = http_build_query($queryParams);
                    ?>
                    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $baseQuery ? '&' . $baseQuery : '' ?>"
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

<!-- Disponibilidade (calendário mensal inline) -->
<?php if (!empty($trips['items'])): ?>
<div class="availability-bar">
    <div class="container">
        <span class="availability-label">Disponível durante todo o ano</span>
        <div class="availability-months">
            <?php
            $months = ['jan','fev','mar','abr','maio','jun','jul','ago','set','out','nov','dez'];
            foreach ($months as $m): ?>
            <span class="month-badge"><?= $m ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collapsible filter groups
    document.querySelectorAll('[data-toggle-filter]').forEach(function(el) {
        el.addEventListener('click', function() {
            var group = el.closest('[data-collapsible]');
            group.classList.toggle('collapsed');
        });
    });

    // Show more buttons
    document.querySelectorAll('[data-show-more]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var options = btn.closest('.filter-options-collapsible');
            options.classList.toggle('expanded');
            if (options.classList.contains('expanded')) {
                btn.textContent = 'Mostrar menos ↑';
            } else {
                btn.textContent = btn.dataset.originalText || 'Mostrar todos ↓';
            }
        });
        btn.dataset.originalText = btn.textContent;
    });

    // Price range slider
    var priceMin = document.getElementById('price-min');
    var priceMax = document.getElementById('price-max');
    var priceMinVal = document.getElementById('price-min-val');
    var priceMaxVal = document.getElementById('price-max-val');

    if (priceMin && priceMax) {
        priceMin.addEventListener('input', function() {
            if (parseInt(priceMin.value) > parseInt(priceMax.value)) {
                priceMin.value = priceMax.value;
            }
            priceMinVal.textContent = priceMin.value;
        });
        priceMax.addEventListener('input', function() {
            if (parseInt(priceMax.value) < parseInt(priceMin.value)) {
                priceMax.value = priceMin.value;
            }
            priceMaxVal.textContent = priceMax.value;
        });
    }

    // Duration range slider
    var durMin = document.getElementById('duration-min');
    var durMax = document.getElementById('duration-max');
    var durMinVal = document.getElementById('duration-min-val');
    var durMaxVal = document.getElementById('duration-max-val');

    if (durMin && durMax) {
        durMin.addEventListener('input', function() {
            if (parseInt(durMin.value) > parseInt(durMax.value)) {
                durMin.value = durMax.value;
            }
            durMinVal.textContent = durMin.value;
        });
        durMax.addEventListener('input', function() {
            if (parseInt(durMax.value) < parseInt(durMin.value)) {
                durMax.value = durMin.value;
            }
            durMaxVal.textContent = durMax.value;
        });
    }
});
</script>
