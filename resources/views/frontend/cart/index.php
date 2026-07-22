<section class="page-header">
    <div class="container">
        <h1>Carrinho</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($cart['trips']) && empty($cart['transfers'])): ?>
        <div class="empty-state">
            <h3>Seu carrinho está vazio</h3>
            <p>Explore nossos passeios e transfers para começar a planejar sua viagem!</p>
            <a href="/passeios" class="btn btn-primary">Ver Passeios</a>
            <a href="/transfers" class="btn btn-outline">Ver Transfers</a>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <!-- Trips -->
                <?php if (!empty($cart['trips'])): ?>
                <h3>Passeios</h3>
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
                <h3>Transfers</h3>
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
                    <a href="/checkout" class="btn btn-primary btn-block btn-lg">Ir para Checkout</a>
                    <a href="/passeios" class="btn btn-outline btn-block">Continuar Comprando</a>
                </div>
            </aside>
        </div>
        <?php endif; ?>
    </div>
</section>
