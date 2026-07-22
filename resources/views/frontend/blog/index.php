<!-- Blog Hero -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Nosso Blog</h1>
            <p>Dicas, roteiros e informações úteis para planejar sua viagem perfeita para Punta Cana</p>
        </div>
    </div>
</section>

<!-- Post em Destaque -->
<?php if ($featuredPost): ?>
<section class="section section-blog-featured">
    <div class="container">
        <div class="blog-featured-card">
            <a href="/blog/<?= e($featuredPost['slug']) ?>" class="blog-featured-image">
                <img src="<?= e($featuredPost['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($featuredPost['title']) ?>" loading="lazy">
            </a>
            <div class="blog-featured-content">
                <?php if ($featuredPost['category_name'] ?? null): ?>
                <span class="blog-featured-category" style="color: <?= e($featuredPost['category_color'] ?? '#3772C0') ?>; border-color: <?= e($featuredPost['category_color'] ?? '#3772C0') ?>">
                    <?= e($featuredPost['category_name']) ?>
                </span>
                <?php endif; ?>
                <h2 class="blog-featured-title">
                    <a href="/blog/<?= e($featuredPost['slug']) ?>"><?= e($featuredPost['title']) ?></a>
                </h2>
                <p class="blog-featured-excerpt"><?= e(truncate($featuredPost['excerpt'] ?? $featuredPost['content'] ?? '', 200)) ?></p>
                <div class="blog-featured-meta">
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <?= e(($featuredPost['author_first_name'] ?? 'Punta Cana') . ' ' . ($featuredPost['author_last_name'] ?? 'para Brasileiros')) ?>
                    </span>
                    <span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= format_date($featuredPost['published_at'] ?? $featuredPost['created_at']) ?>
                    </span>
                </div>
                <a href="/blog/<?= e($featuredPost['slug']) ?>" class="btn btn-accent">Ler artigo completo</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Listagem de Posts com Sidebar -->
<section class="section section-blog-list">
    <div class="container">
        <div class="blog-list-layout">
            <!-- Posts Grid (esquerda) -->
            <div class="blog-list-main">
                <h3 class="blog-list-heading">Posts Recentes</h3>
                <?php if (!empty($posts['items'])): ?>
                <div class="blog-grid-2col">
                    <?php foreach ($posts['items'] as $post): ?>
                    <div class="blog-card">
                        <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-image">
                            <img src="<?= e($post['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                            <?php if ($post['category_name'] ?? null): ?>
                            <span class="blog-card-category" style="background: <?= e($post['category_color'] ?? '#3772C0') ?>">
                                <?= e(strtoupper($post['category_name'])) ?>
                            </span>
                            <?php endif; ?>
                        </a>
                        <div class="blog-card-body">
                            <h3 class="blog-card-title">
                                <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                            </h3>
                            <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-readmore">LER MAIS &raquo;</a>
                            <div class="blog-card-meta">
                                <span><?= e(($post['author_first_name'] ?? 'Admin') . ' ' . ($post['author_last_name'] ?? '')) ?></span>
                                <span>&middot;</span>
                                <span><?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
                            </div>
                        </div>
                    </div>
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
                <div class="empty-state">
                    <p>Nenhum post publicado ainda.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar (direita) -->
            <aside class="blog-sidebar">
                <!-- Busca -->
                <div class="blog-sidebar-widget">
                    <h4>Buscar</h4>
                    <form action="/blog" method="GET" class="blog-search-form">
                        <div class="blog-search-input-wrap">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input type="text" name="busca" placeholder="Buscar no blog..." class="blog-search-input">
                        </div>
                    </form>
                </div>

                <!-- Categorias -->
                <div class="blog-sidebar-widget">
                    <h4>Categorias</h4>
                    <ul class="blog-sidebar-list">
                        <?php foreach ($categories as $cat): ?>
                        <li><a href="/blog?categoria=<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Tags Populares -->
                <div class="blog-sidebar-widget">
                    <h4>Tags Populares</h4>
                    <ul class="blog-sidebar-list">
                        <li><a href="/blog?busca=scuba+doo">Scuba Doo</a></li>
                        <li><a href="/blog?busca=coco+bongo">Coco Bongo</a></li>
                        <li><a href="/blog?busca=buggies">Buggies</a></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="blog-sidebar-widget blog-sidebar-newsletter">
                    <h4>Assine nossa Newsletter</h4>
                    <p>Receba as melhores dicas e ofertas exclusivas para sua viagem a Punta Cana.</p>
                    <form class="newsletter-form" id="newsletterForm" onsubmit="return submitNewsletter(event)">
                        <label>Seu melhor e-mail: <span class="required">*</span></label>
                        <input type="email" name="newsletter_email" id="newsletterEmail" placeholder="" class="form-control" required>
                        <button type="submit" class="btn-newsletter">Assinar</button>
                        <p class="newsletter-privacy">Respeitamos sua privacidade. Você pode cancelar a qualquer momento.</p>
                        <p class="newsletter-msg" id="newsletterMsg" style="display:none;font-size:13px;margin-top:8px;"></p>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>
