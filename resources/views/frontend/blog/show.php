<section class="section section-blog-single">
    <div class="container">
        <div class="blog-single-wrapper">
            <!-- Voltar -->
            <a href="/blog" class="blog-back-link">&larr; Voltar para o blog</a>

            <!-- Header do post -->
            <div class="blog-single-header">
                <div class="blog-single-meta-top">
                    <?php if ($post['category_name'] ?? null): ?>
                    <span class="blog-single-category" style="background: <?= e($post['category_color'] ?? '#6b7280') ?>">
                        <?= e($post['category_name']) ?>
                    </span>
                    <?php endif; ?>
                    <span class="blog-single-date">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= format_date($post['published_at'] ?? $post['created_at']) ?>
                    </span>
                </div>

                <h1 class="blog-single-title"><?= e($post['title']) ?></h1>

                <?php if ($post['excerpt'] ?? null): ?>
                <p class="blog-single-excerpt"><?= e($post['excerpt']) ?></p>
                <?php endif; ?>

                <div class="blog-single-author-row">
                    <div class="blog-single-author">
                        <div class="blog-single-avatar">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <span class="blog-single-author-name"><?= e(($post['author_first_name'] ?? 'Punta Cana') . ' ' . ($post['author_last_name'] ?? 'para Brasileiros')) ?></span>
                    </div>
                    <div class="blog-single-share">
                        <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' - ' . url('/blog/' . $post['slug'])) ?>" target="_blank" class="share-icon" title="WhatsApp">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" class="share-icon" title="Facebook">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(url('/blog/' . $post['slug'])) ?>" target="_blank" class="share-icon" title="LinkedIn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Imagem destaque -->
            <?php if ($post['featured_image']): ?>
            <div class="blog-single-featured-img">
                <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
            </div>
            <?php endif; ?>

            <!-- Conteúdo -->
            <div class="blog-single-body">
                <?= $post['content'] ?>
            </div>

            <!-- Posts Relacionados -->
            <?php if (!empty($relatedPosts)): ?>
            <div class="blog-related">
                <h3>Posts Relacionados</h3>
                <div class="blog-grid-home">
                    <?php foreach ($relatedPosts as $rp): ?>
                    <?php if (($rp['slug'] ?? '') !== ($post['slug'] ?? '')): ?>
                    <div class="blog-card">
                        <a href="/blog/<?= e($rp['slug']) ?>" class="blog-card-image">
                            <img src="<?= e($rp['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="" loading="lazy">
                            <?php if ($rp['category_name'] ?? null): ?>
                            <span class="blog-card-category" style="background: <?= e($rp['category_color'] ?? '#3772C0') ?>"><?= e(strtoupper($rp['category_name'])) ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="blog-card-body">
                            <h3 class="blog-card-title"><a href="/blog/<?= e($rp['slug']) ?>"><?= e($rp['title']) ?></a></h3>
                            <a href="/blog/<?= e($rp['slug']) ?>" class="blog-card-readmore">LER MAIS &raquo;</a>
                            <div class="blog-card-meta">
                                <span><?= e(($rp['author_first_name'] ?? 'Admin') . ' ' . ($rp['author_last_name'] ?? '')) ?></span>
                                <span>&middot;</span>
                                <span><?= format_date($rp['published_at'] ?? $rp['created_at'] ?? '') ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Newsletter -->
            <div class="blog-single-newsletter">
                <div class="blog-newsletter-card">
                    <h3>Assine nossa Newsletter</h3>
                    <p>Receba as melhores dicas e ofertas exclusivas para sua viagem a Punta Cana.</p>
                    <form id="blogNewsletterForm" onsubmit="return submitNewsletter(event)">
                        <label>Seu melhor e-mail: <span class="required">*</span></label>
                        <input type="email" id="newsletterEmail" class="form-control" placeholder="" required>
                        <button type="submit" class="btn-newsletter-full">Assinar</button>
                        <p class="newsletter-privacy-text">Respeitamos sua privacidade. Você pode cancelar a qualquer momento.</p>
                    </form>
                    <p class="newsletter-msg" id="newsletterMsg" style="display:none;font-size:13px;margin-top:8px;text-align:center"></p>
                </div>
            </div>
        </div>
    </div>
</section>
