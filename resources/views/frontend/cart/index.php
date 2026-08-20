<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Carrinho</h1>
            <p>Revise seus itens antes de finalizar a compra.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($cart['trips']) && empty($cart['transfers'])): ?>
        <div class="empty-state">
            <h3>Seu carrinho está vazio</h3>
            <p>Explore nossos passeios e transfers para começar a planejar sua viagem!</p>
            <div class="empty-state-actions">
                <a href="/passeios" class="btn btn-primary">Ver Passeios</a>
                <a href="/transfers" class="btn btn-outline">Ver Transfers</a>
            </div>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <!-- Trips -->
                <?php if (!empty($cart['trips'])): ?>
                <h3 class="cart-section-title">Passeios</h3>
                <?php foreach ($cart['trips'] as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="<?= e($item['trip_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="">
                    </div>
                    <div class="cart-item-info">
                        <h4><a href="/passeios/<?= e($item['trip_slug']) ?>"><?= e($item['trip_title']) ?></a></h4>
                        <p class="cart-item-meta">
                            Data: <?= format_date($item['date']) ?>
                            <?php if ($item['time']): ?> | Horário: <?= e($item['time']) ?><?php endif; ?>
                            | <?= (int)$item['total_pax'] ?> passageiro(s)
                        </p>
                        <?php if ($item['package_title']): ?>
                        <p class="cart-item-package">Pacote: <?= e($item['package_title']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-price">
                        <span class="price"><?= money($item['total']) ?></span>
                        <form method="POST" action="/carrinho/remover" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="item_id" value="<?= e($item['id']) ?>">
                            <input type="hidden" name="type" value="trip">
                            <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- Transfers -->
                <?php if (!empty($cart['transfers'])): ?>
                <h3 class="cart-section-title">Transfers</h3>
                <?php foreach ($cart['transfers'] as $transfer): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="<?= e($transfer['vehicle_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="">
                    </div>
                    <div class="cart-item-info">
                        <h4><?= e($transfer['vehicle_title']) ?></h4>
                        <p class="cart-item-meta">
                            <?= e($transfer['origin_title']) ?> &rarr; <?= e($transfer['destination_title']) ?><br>
                            <?= format_date($transfer['date']) ?> às <?= e($transfer['time']) ?>
                            | <?= e($transfer['type'] === 'arrival' ? 'Chegada' : 'Partida') ?>
                            | <?= (int)$transfer['adults'] + (int)$transfer['children'] + (int)$transfer['infants'] ?> passageiro(s)
                        </p>
                    </div>
                    <div class="cart-item-price">
                        <span class="price"><?= money((float)$transfer['price']) ?></span>
                        <form method="POST" action="/carrinho/remover" class="inline-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="item_id" value="<?= e($transfer['id']) ?>">
                            <input type="hidden" name="type" value="transfer">
                            <input type="hidden" name="group_id" value="<?= e($transfer['group_id'] ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Resumo -->
            <aside class="cart-summary">
                <div class="summary-card">
                    <h3>Resumo do Pedido</h3>
                    <?php if ($cart['trip_total'] > 0): ?>
                    <div class="summary-row"><span>Passeios:</span><span><?= money($cart['trip_total']) ?></span></div>
                    <?php endif; ?>
                    <?php if ($cart['transfer_total'] > 0): ?>
                    <div class="summary-row"><span>Transfers:</span><span><?= money($cart['transfer_total']) ?></span></div>
                    <?php endif; ?>
                    <div class="summary-row summary-total"><span>Total:</span><span><?= money($cart['grand_total']) ?></span></div>
                    <div class="summary-actions">
                        <a href="/checkout" class="btn btn-primary btn-block">Ir para Checkout</a>
                        <a href="/passeios" class="btn btn-outline btn-block">Continuar Comprando</a>
                    </div>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (has_flash('warning')): ?>
<!-- Pop-up de aviso de mudança de preço -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var msg = <?= json_encode(flash('warning')) ?>;
    if (!msg) return;
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:99999;display:flex;align-items:center;justify-content:center;padding:20px;';
    var modal = document.createElement('div');
    modal.style.cssText = 'background:#fff;border-radius:12px;padding:32px 28px 24px;max-width:460px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;animation:modalFadeIn 0.3s ease;';
    modal.innerHTML = '<div style="margin-bottom:16px;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>'
        + '<h3 style="margin:0 0 12px;font-size:1.25rem;color:#1a1a1a;font-weight:600;">Preço Atualizado</h3>'
        + '<p style="margin:0 0 24px;font-size:0.95rem;color:#555;line-height:1.5;">' + msg + '</p>'
        + '<button id="priceChangeOkBtn" style="background:#1B6F00;color:#fff;border:none;padding:12px 40px;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;">OK, entendi</button>';
    overlay.appendChild(modal);
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    document.getElementById('priceChangeOkBtn').addEventListener('click', function() {
        overlay.remove();
        document.body.style.overflow = '';
    });
});
</script>
<?php endif; ?>