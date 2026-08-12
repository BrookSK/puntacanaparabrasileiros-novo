<!-- Blog Hero -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-content">
            <span class="blog-hero-badge">Blog</span>
            <h1>Dicas & Roteiros</h1>
            <p>Tudo o que você precisa saber para aproveitar o melhor de Punta Cana. Guias práticos, roteiros e experiências reais.</p>
        </div>
    </div>
</section>

<!-- Post em Destaque -->
<?php if ($featuredPost): ?>
<section class="section blog-destaque-section">
    <div class="container">
        <div class="blog-destaque-card">
            <a href="/blog/<?= e($featuredPost['slug']) ?>" class="blog-destaque-img">
                <img src="<?= e($featuredPost['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($featuredPost['title']) ?>" loading="lazy">
                <div class="blog-destaque-overlay"></div>
            </a>
            <div class="blog-destaque-body">
                <?php if ($featuredPost['category_name'] ?? null): ?>
                <span class="blog-destaque-cat" style="background: <?= e($featuredPost['category_color'] ?? 'var(--text-green)') ?>">
                    <?= e($featuredPost['category_name']) ?>
                </span>
                <?php endif; ?>
                <h2 class="blog-destaque-title">
                    <a href="/blog/<?= e($featuredPost['slug']) ?>"><?= e($featuredPost['title']) ?></a>
                </h2>
                <p class="blog-destaque-excerpt"><?= e(truncate($featuredPost['excerpt'] ?? $featuredPost['content'] ?? '', 180)) ?></p>
                <div class="blog-destaque-meta">
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?= e(($featuredPost['author_first_name'] ?? 'Punta Cana') . ' ' . ($featuredPost['author_last_name'] ?? 'para Brasileiros')) ?>
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= format_date($featuredPost['published_at'] ?? $featuredPost['created_at']) ?>
                    </span>
                </div>
                <a href="/blog/<?= e($featuredPost['slug']) ?>" class="blog-destaque-btn">
                    Ler artigo completo
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Filtro de Categorias -->
<?php if (!empty($categories)): ?>
<section class="blog-categorias-section">
    <div class="container">
        <div class="blog-categorias-pills">
            <a href="/blog" class="blog-pill <?= empty($currentCategory) ? 'active' : '' ?>">Todos</a>
            <?php foreach ($categories as $cat): ?>
            <a href="/blog?categoria=<?= e($cat['slug']) ?>" class="blog-pill <?= ($currentCategory ?? '') === $cat['slug'] ? 'active' : '' ?>">
                <?= e($cat['name']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Grid de Posts -->
<section class="section blog-posts-section">
    <div class="container">
        <?php if (!empty($posts['items'])): ?>
        <div class="blog-posts-grid">
            <?php foreach ($posts['items'] as $post): ?>
            <article class="blog-post-card">
                <a href="/blog/<?= e($post['slug']) ?>" class="blog-post-card-img">
                    <img src="<?= e($post['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                    <?php if ($post['category_name'] ?? null): ?>
                    <span class="blog-post-card-cat" style="background: <?= e($post['category_color'] ?? 'var(--text-green)') ?>">
                        <?= e($post['category_name']) ?>
                    </span>
                    <?php endif; ?>
                </a>
                <div class="blog-post-card-body">
                    <div class="blog-post-card-meta">
                        <span><?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
                        <span>&middot;</span>
                        <span><?= e(($post['author_first_name'] ?? 'Admin') . ' ' . ($post['author_last_name'] ?? '')) ?></span>
                    </div>
                    <h3 class="blog-post-card-title">
                        <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                    </h3>
                    <p class="blog-post-card-excerpt"><?= e(truncate($post['excerpt'] ?? $post['content'] ?? '', 120)) ?></p>
                    <a href="/blog/<?= e($post['slug']) ?>" class="blog-post-card-link">
                        Ler mais
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($posts['total_pages'] > 1): ?>
        <nav class="pagination">
            <?php for ($i = 1; $i <= $posts['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>&categoria=<?= e($currentCategory ?? '') ?>"
               class="page-link <?= $i === $posts['current_page'] ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state" style="text-align:center;padding:60px 20px;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:16px;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <p style="color:var(--gray);font-size:16px;">Nenhum post publicado ainda.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Newsletter CTA -->
<section class="blog-newsletter-section">
    <div class="container">
        <div class="blog-newsletter-card">
            <div class="blog-newsletter-content">
                <h3>Receba nossas dicas exclusivas</h3>
                <p>Assine nossa newsletter e fique por dentro dos melhores roteiros e ofertas para Punta Cana.</p>
            </div>
            <form class="blog-newsletter-form" id="blogNewsletterForm" onsubmit="return submitNewsletter(event)">
                <input type="email" name="newsletter_email" id="newsletterEmail" placeholder="Seu melhor e-mail" class="blog-newsletter-input" required>
                <button type="submit" class="blog-newsletter-btn">Assinar</button>
            </form>
            <p class="blog-newsletter-msg" id="newsletterMsg" style="display:none;"></p>
        </div>
    </div>
</section>
