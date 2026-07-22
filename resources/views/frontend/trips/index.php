<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Passeios e Experiências</h1>
            <p>Descubra experiências únicas e memoráveis em Punta Cana. Do relaxamento ao aprendizado, crie memórias que durarão para sempre.</p>
            <a href="#passeios-grid" class="btn btn-secondary">Explorar Todas as Experiências</a>
        </div>
    </div>
</section>

<!-- Categorias de Experiências -->
<section class="section section-categorias-passeios">
    <div class="container">
        <h2 class="section-title">Todos os Tipos de Experiências</h2>
        <div class="categorias-cards-grid">
            <a href="/passeios?categoria=passeios-de-barco" class="categoria-card-lg">
                <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/3.png" alt="Praia e Barco" loading="lazy">
                <div class="categoria-card-overlay">
                    <span class="categoria-card-name">Praia e Barco &rarr;</span>
                </div>
            </a>
            <a href="/passeios?categoria=familia" class="categoria-card-lg">
                <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/4.png" alt="Adequado para crianças" loading="lazy">
                <div class="categoria-card-overlay">
                    <span class="categoria-card-name">Adequado para crianças &rarr;</span>
                </div>
            </a>
            <a href="/passeios?categoria=cultural" class="categoria-card-lg">
                <img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0044-990x490.jpg" alt="Passeio pela cidade" loading="lazy">
                <div class="categoria-card-overlay">
                    <span class="categoria-card-name">Passeio pela cidade &rarr;</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Experiências em Destaque -->
<section class="section section-destaque-passeios">
    <div class="container">
        <h2 class="section-title">Experiências em Destaque</h2>

        <?php if (!empty($featuredTrips)): ?>
        <div class="destaque-trips-grid">
            <?php foreach ($featuredTrips as $trip): ?>
            <div class="destaque-trip-card">
                <a href="/passeios/<?= e($trip['slug']) ?>" class="destaque-trip-image">
                    <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                    <button class="ft-card-fav" title="Favoritar">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    </button>
                    <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                    <?php $discount = round(100 - ($trip['min_price'] / $trip['regular_price'] * 100)); ?>
                    <span class="ft-card-discount"><?= $discount ?>% Off</span>
                    <?php endif; ?>
                </a>
                <div class="destaque-trip-body">
                    <h3 class="destaque-trip-title">
                        <a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a>
                    </h3>
                    <p class="destaque-trip-desc"><?= e(truncate($trip['short_description'] ?? '', 120)) ?></p>
                    <div class="destaque-trip-footer">
                        <div class="destaque-trip-meta">
                            <span class="meta-location">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                Punta Cana
                            </span>
                            <span class="meta-duration">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= e($trip['duration'] ?? '4') ?> <?= ($trip['duration_unit'] ?? 'hours') === 'hours' ? 'Horas' : 'Dias' ?>
                            </span>
                        </div>
                        <span class="destaque-trip-price"><?= money($trip['min_price'] ?? 0) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="empty-state-text">Em breve teremos experiências em destaque para você!</p>
        <?php endif; ?>

        <div class="section-cta">
            <a href="#passeios-grid" class="btn-ver-todos">Ver Todos os Passeios &rarr;</a>
        </div>
    </div>
</section>

<!-- Listagem Completa de Passeios -->
<section class="section section-todos-passeios" id="passeios-grid">
    <div class="container">
        <h2 class="section-title">Todas as Experiências</h2>

        <!-- Filtros inline -->
        <div class="passeios-filtros-bar">
            <form method="GET" action="/passeios" class="filtros-inline">
                <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar passeio..." class="filtro-input">
                <select name="categoria" class="filtro-select-inline">
                    <option value="">Todas categorias</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['slug']) ?>" <?= ($currentCategory ?? '') === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="ordenar" class="filtro-select-inline">
                    <option value="">Ordenar por</option>
                    <option value="preco_asc" <?= ($currentOrder ?? '') === 'preco_asc' ? 'selected' : '' ?>>Menor Preço</option>
                    <option value="preco_desc" <?= ($currentOrder ?? '') === 'preco_desc' ? 'selected' : '' ?>>Maior Preço</option>
                    <option value="popular" <?= ($currentOrder ?? '') === 'popular' ? 'selected' : '' ?>>Mais Popular</option>
                    <option value="recente" <?= ($currentOrder ?? '') === 'recente' ? 'selected' : '' ?>>Mais Recente</option>
                </select>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
        </div>

        <?php if (empty($trips['items'])): ?>
        <div class="empty-state">
            <p>Nenhum passeio encontrado.</p>
            <a href="/passeios" class="btn btn-outline">Limpar filtros</a>
        </div>
        <?php else: ?>
        <div class="destaque-trips-grid">
            <?php foreach ($trips['items'] as $trip): ?>
            <div class="destaque-trip-card">
                <a href="/passeios/<?= e($trip['slug']) ?>" class="destaque-trip-image">
                    <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                    <button class="ft-card-fav" title="Favoritar">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    </button>
                    <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                    <?php $discount = round(100 - ($trip['min_price'] / $trip['regular_price'] * 100)); ?>
                    <span class="ft-card-discount"><?= $discount ?>% Off</span>
                    <?php endif; ?>
                </a>
                <div class="destaque-trip-body">
                    <h3 class="destaque-trip-title">
                        <a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a>
                    </h3>
                    <p class="destaque-trip-desc"><?= e(truncate($trip['short_description'] ?? '', 120)) ?></p>
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

        <!-- Paginação -->
        <?php if ($trips['total_pages'] > 1): ?>
        <nav class="pagination">
            <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>&categoria=<?= e($currentCategory ?? '') ?>&busca=<?= e($currentSearch ?? '') ?>&ordenar=<?= e($currentOrder ?? '') ?>"
               class="page-link <?= $i === $trips['current_page'] ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Seção Depoimentos -->
<section class="section section-depoimentos section-depoimentos-alt">
    <div class="container">
        <h2 class="section-title">O Que Dizem Nossos Clientes</h2>

        <div class="depoimentos-slider" id="depoimentosSliderPasseios">
            <div class="depoimentos-track" id="depoimentosTrackPasseios">
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Carlos Eduardo</strong><span>Rio de Janeiro, RJ</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Segunda vez que viajo com a PuntaCanaBR e mais uma vez superou as expectativas. Recomendo para quem busca tranquilidade e bom preço."</p>
                </div>

                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Mariana Silva</strong><span>Belo Horizonte, MG</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                    <p class="depoimento-text">"Amei a experiência, principalmente os passeios exclusivos. O único ponto a melhorar seria o tempo de transfer do aeroporto ao hotel."</p>
                </div>

                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Ana Beatriz</strong><span>São Paulo, SP</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Viagem perfeita! Atendimento em português do início ao fim, hotel excelente e passeios bem organizados. Já estou planejando voltar no próximo ano."</p>
                </div>

                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Fernanda Oliveira</strong><span>Curitiba, PR</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Tudo perfeito do início ao fim. O passeio à Isla Saona foi inesquecível. Equipe super atenciosa e dedicada!"</p>
                </div>

                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Roberto Santos</strong><span>Brasília, DF</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                    <p class="depoimento-text">"Ótimo custo-benefício. O transfer foi pontual e o motorista muito simpático. Voltarei com certeza."</p>
                </div>

                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar"><svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></div>
                        <div class="depoimento-info"><strong>Juliana Costa</strong><span>Salvador, BA</span></div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Melhor decisão que tomei foi reservar por aqui. Comunicação rápida pelo WhatsApp e sem surpresas desagradáveis."</p>
                </div>
            </div>

            <div class="depoimentos-dots" id="depoimentosDotsPasseios">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
        </div>
    </div>
</section>
