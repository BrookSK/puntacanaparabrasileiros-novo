<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <!-- Header da wishlist -->
        <div class="wishlist-header">
            <span class="wishlist-count"><?= count($items) ?> items in wishlist</span>
            <?php if (!empty($items)): ?>
            <form method="POST" action="/minha-conta/wishlist/limpar" class="inline-form">
                <?= csrf_field() ?>
                <button type="submit" class="wishlist-clear-btn">Remover Tudo</button>
            </form>
            <?php endif; ?>
        </div>

        <?php if (empty($items)): ?>
        <div class="empty-state">
            <p>Sua lista de desejos está vazia.</p>
            <a href="/passeios" class="btn btn-primary">Explorar Passeios</a>
        </div>
        <?php else: ?>
        <div class="wishlist-grid">
            <?php foreach ($items as $item): ?>
            <div class="wishlist-card">
                <!-- Imagem -->
                <a href="/passeios/<?= e($item['slug']) ?>" class="wishlist-card-image">
                    <img src="<?= e($item['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                </a>

                <!-- Conteúdo -->
                <div class="wishlist-card-body">
                    <h3 class="wishlist-card-title">
                        <a href="/passeios/<?= e($item['slug']) ?>"><?= e($item['title']) ?></a>
                    </h3>

                    <div class="wishlist-card-meta">
                        <span class="meta-location">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Punta Cana
                        </span>
                        <span class="meta-duration">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?= e($item['duration'] ?? '4') ?> Horas
                        </span>
                    </div>

                    <!-- Preço + Desconto -->
                    <div class="wishlist-card-price">
                        <?php
                        $minPrice = 0;
                        $regularPrice = 0;
                        $packageModel = new \App\Models\TripPackage();
                        $packages = $packageModel->getByTrip((int) $item['trip_id']);
                        if (!empty($packages)) {
                            $minPrice = $packageModel->getBasePrice((int) $packages[0]['id']);
                            $regularPrice = $packageModel->getRegularPrice((int) $packages[0]['id']);
                        }
                        $hasDiscount = $regularPrice > $minPrice && $minPrice > 0;
                        $discountPercent = $hasDiscount ? round(100 - ($minPrice / $regularPrice * 100)) : 0;
                        ?>
                        <?php if ($hasDiscount): ?>
                        <span class="wishlist-discount-badge"><?= $discountPercent ?>% Off</span>
                        <?php endif; ?>
                        <span class="wishlist-price"><?= money($minPrice) ?></span>
                        <?php if ($hasDiscount): ?>
                        <span class="wishlist-price-old"><?= money($regularPrice) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Descrição -->
                    <p class="wishlist-card-desc"><?= e(truncate($item['short_description'] ?? '', 120)) ?></p>

                    <!-- Botão Ver Detalhes -->
                    <a href="/passeios/<?= e($item['slug']) ?>" class="btn-wishlist-details">VER DETALHES</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
