<!-- Blog Post Header -->
<section class="blogpost-header">
    <div class="container">
        <div class="blogpost-header-inner">
            <a href="/blog" class="blogpost-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Voltar ao blog
            </a>

            <div class="blogpost-meta">
                <?php if ($post['category_name'] ?? null): ?>
                <a href="/blog?categoria=<?= e($post['category_slug'] ?? '') ?>" class="blogpost-cat"><?= e($post['category_name']) ?></a>
                <?php endif; ?>
                <span class="blogpost-date"><?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
            </div>

            <h1 class="blogpost-title"><?= e($post['title']) ?></h1>

            <?php if ($post['excerpt'] ?? null): ?>
            <p class="blogpost-excerpt"><?= e($post['excerpt']) ?></p>
            <?php endif; ?>

            <div class="blogpost-author">
                <div class="blogpost-author-avatar">
                    <?= strtoupper(substr($post['author_first_name'] ?? 'P', 0, 1)) ?>
                </div>
                <div class="blogpost-author-info">
                    <span class="blogpost-author-name"><?= e(($post['author_first_name'] ?? 'Punta Cana') . ' ' . ($post['author_last_name'] ?? 'para Brasileiros')) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Imagem Destaque -->
<?php if ($post['featured_image']): ?>
<div class="blogpost-cover">
    <div class="container">
        <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
    </div>
</div>
<?php endif; ?>

<!-- Conteúdo -->
<section class="blogpost-content-section">
    <div class="container">
        <article class="blogpost-content">
            <?= $post['content'] ?>
        </article>

        <!-- Compartilhar -->
        <div class="blogpost-share">
            <span>Compartilhar:</span>
            <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' - ' . url('/blog/' . $post['slug'])) ?>" target="_blank" title="WhatsApp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" title="Facebook">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" title="Twitter">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- Posts Relacionados -->
<?php if (!empty($relatedPosts)): ?>
<section class="blogpost-related">
    <div class="container">
        <h3 class="blogpost-related-heading">Leia também</h3>
        <div class="blog-posts-grid">
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
                <p>Assine e fique por dentro dos melhores roteiros para Punta Cana.</p>
            </div>
            <form class="blog-newsletter-form" id="blogNewsletterForm" onsubmit="return submitNewsletter(event)">
                <input type="email" name="newsletter_email" id="newsletterEmail" placeholder="Seu melhor e-mail" class="blog-newsletter-input" required>
                <button type="submit" class="blog-newsletter-btn">Assinar</button>
            </form>
            <p class="blog-newsletter-msg" id="newsletterMsg" style="display:none;"></p>
        </div>
    </div>
</section>
