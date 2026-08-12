<!-- Blog Post Hero -->
<section class="blog-post-hero">
    <div class="container">
        <a href="/blog" class="blog-post-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para o blog
        </a>
        <div class="blog-post-hero-inner">
            <div class="blog-post-hero-meta">
                <?php if ($post['category_name'] ?? null): ?>
                <span class="blog-post-hero-cat" style="background: <?= e($post['category_color'] ?? 'var(--text-green)') ?>">
                    <?= e($post['category_name']) ?>
                </span>
                <?php endif; ?>
                <span class="blog-post-hero-date">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= format_date($post['published_at'] ?? $post['created_at']) ?>
                </span>
            </div>
            <h1 class="blog-post-hero-title"><?= e($post['title']) ?></h1>
            <?php if ($post['excerpt'] ?? null): ?>
            <p class="blog-post-hero-excerpt"><?= e($post['excerpt']) ?></p>
            <?php endif; ?>
            <div class="blog-post-hero-author">
                <div class="blog-post-hero-avatar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#1B6F00"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <span><?= e(($post['author_first_name'] ?? 'Punta Cana') . ' ' . ($post['author_last_name'] ?? 'para Brasileiros')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Imagem Destaque -->
<?php if ($post['featured_image']): ?>
<section class="blog-post-img-section">
    <div class="container">
        <div class="blog-post-featured-img">
            <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Conteúdo do Post -->
<section class="blog-post-content-section">
    <div class="container">
        <div class="blog-post-content-wrapper">
            <!-- Share lateral -->
            <aside class="blog-post-share-sidebar">
                <span class="blog-post-share-label">Compartilhar</span>
                <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' - ' . url('/blog/' . $post['slug'])) ?>" target="_blank" class="blog-post-share-btn" title="WhatsApp" style="background:#25d366;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" class="blog-post-share-btn" title="Facebook" style="background:#1877f2;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" class="blog-post-share-btn" title="LinkedIn" style="background:#0077b5;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
                </a>
            </aside>

            <!-- Conteúdo -->
            <article class="blog-post-body">
                <?= $post['content'] ?>
            </article>
        </div>
    </div>
</section>

<!-- Posts Relacionados -->
<?php if (!empty($relatedPosts)): ?>
<section class="blog-post-related-section">
    <div class="container">
        <h3 class="blog-post-related-title">Você também pode gostar</h3>
        <div class="blog-posts-grid" style="max-width:100%;">
            <?php foreach ($relatedPosts as $rp): ?>
            <?php if (($rp['slug'] ?? '') !== ($post['slug'] ?? '')): ?>
            <article class="blog-post-card">
                <a href="/blog/<?= e($rp['slug']) ?>" class="blog-post-card-img">
                    <img src="<?= e($rp['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($rp['title']) ?>" loading="lazy">
                    <?php if ($rp['category_name'] ?? null): ?>
                    <span class="blog-post-card-cat" style="background: <?= e($rp['category_color'] ?? 'var(--text-green)') ?>">
                        <?= e($rp['category_name']) ?>
                    </span>
                    <?php endif; ?>
                </a>
                <div class="blog-post-card-body">
                    <div class="blog-post-card-meta">
                        <span><?= format_date($rp['published_at'] ?? $rp['created_at'] ?? '') ?></span>
                        <span>&middot;</span>
                        <span><?= e(($rp['author_first_name'] ?? 'Admin') . ' ' . ($rp['author_last_name'] ?? '')) ?></span>
                    </div>
                    <h3 class="blog-post-card-title">
                        <a href="/blog/<?= e($rp['slug']) ?>"><?= e($rp['title']) ?></a>
                    </h3>
                    <a href="/blog/<?= e($rp['slug']) ?>" class="blog-post-card-link">
                        Ler mais
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter -->
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
