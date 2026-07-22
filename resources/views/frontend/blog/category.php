<!-- Hero Categoria -->
<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content" style="text-align:left">
            <h1><?= e($category['name']) ?></h1>
            <p>Explore o melhor de Punta Cana com dicas imperdíveis de lugares, experiências e atrações.</p>
            <nav class="breadcrumb">
                <a href="/blog">Blog</a> <span>&rsaquo;</span> <span><?= e($category['name']) ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Listagem -->
<section class="section section-blog-list">
    <div class="container">
        <h3 class="blog-list-heading">Artigos sobre <?= e($category['name']) ?></h3>

        <?php if (!empty($posts['items'])): ?>
        <div class="blog-grid-home">
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
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $posts['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty-state">
            <p>Nenhum artigo nesta categoria ainda.</p>
            <a href="/blog" class="btn btn-outline">Ver todos os posts</a>
        </div>
        <?php endif; ?>
    </div>
</section>
