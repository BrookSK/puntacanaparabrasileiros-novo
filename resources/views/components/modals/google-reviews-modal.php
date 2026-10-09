<?php
/**
 * Modal de avaliações reais do Google (item 5 da validação).
 * Espera a variável $googleReviews (array ou null), passada pela view do passeio.
 * Estrutura: ['rating'=>float,'count'=>int,'url'=>?string,'reviews'=>array,'source'=>'google'|'manual'].
 */
$gr = $googleReviews ?? null;
if (empty($gr)) return;

$grRating = (float) ($gr['rating'] ?? 0);
$grCount = (int) ($gr['count'] ?? 0);
$grUrl = $gr['url'] ?? null;
$grReviews = is_array($gr['reviews'] ?? null) ? $gr['reviews'] : [];
?>
<div class="greviews-modal-overlay" id="googleReviewsModal" aria-hidden="true">
    <div class="greviews-modal" role="dialog" aria-modal="true" aria-labelledby="greviewsTitle">
        <button type="button" class="greviews-modal-close" id="googleReviewsClose" aria-label="Fechar">&times;</button>

        <div class="greviews-head">
            <div class="greviews-logo">
                <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.65l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0012 23z"/><path fill="#FBBC05" d="M5.84 14.11a6.6 6.6 0 010-4.22V7.05H2.18a11 11 0 000 9.9l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1A11 11 0 002.18 7.05l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/></svg>
                <span>Avaliações do Google</span>
            </div>
            <div class="greviews-summary">
                <?php if ($grRating > 0): ?>
                <span class="greviews-score"><?= number_format($grRating, 1, ',', '') ?></span>
                <span class="greviews-stars" aria-label="<?= number_format($grRating, 1, ',', '') ?> de 5">
                    <?php for ($s = 1; $s <= 5; $s++): ?><?= $s <= round($grRating) ? '&#9733;' : '&#9734;' ?><?php endfor; ?>
                </span>
                <?php endif; ?>
                <?php if ($grCount > 0): ?>
                <span class="greviews-count"><?= $grCount ?> avaliaç<?= $grCount > 1 ? 'ões' : 'ão' ?></span>
                <?php endif; ?>
            </div>
            <h3 class="greviews-title" id="greviewsTitle">Avaliações reais de clientes no Google</h3>
        </div>

        <div class="greviews-body">
            <?php if (!empty($grReviews)): ?>
            <ul class="greviews-list">
                <?php foreach ($grReviews as $rv): ?>
                <li class="greviews-item">
                    <div class="greviews-item-head">
                        <?php if (!empty($rv['profile_photo'])): ?>
                        <img class="greviews-avatar" src="<?= e($rv['profile_photo']) ?>" alt="" loading="lazy" referrerpolicy="no-referrer">
                        <?php else: ?>
                        <span class="greviews-avatar greviews-avatar--letter"><?= e(mb_substr((string) ($rv['author'] ?? 'C'), 0, 1)) ?></span>
                        <?php endif; ?>
                        <div class="greviews-item-meta">
                            <strong class="greviews-author"><?= e($rv['author'] ?? 'Cliente do Google') ?></strong>
                            <span class="greviews-item-stars">
                                <?php $rr = (int) ($rv['rating'] ?? 5); for ($s = 1; $s <= 5; $s++): ?><?= $s <= $rr ? '&#9733;' : '&#9734;' ?><?php endfor; ?>
                                <?php if (!empty($rv['relative_time'])): ?><span class="greviews-when"><?= e($rv['relative_time']) ?></span><?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($rv['text'])): ?>
                    <p class="greviews-text"><?= nl2br(e($rv['text'])) ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="greviews-empty">As avaliações individuais ficam disponíveis diretamente no Google. Clique no botão abaixo para ver todas.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($grUrl)): ?>
        <div class="greviews-foot">
            <a href="<?= e($grUrl) ?>" target="_blank" rel="noopener" class="greviews-all-btn">
                Ver todas as avaliações do Google
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function(){
    var openBtn = document.getElementById('btnGoogleReviews');
    var overlay = document.getElementById('googleReviewsModal');
    var closeBtn = document.getElementById('googleReviewsClose');
    if (!openBtn || !overlay) return;

    function open(){ overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
    function close(){ overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }

    openBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', function(e){ if (e.target === overlay) close(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') close(); });
})();
</script>
