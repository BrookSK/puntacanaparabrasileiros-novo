<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Checkout</h1>
            <p>Preencha seus dados para finalizar a reserva.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="checkout-layout">
            <!-- Formulário -->
            <div class="checkout-form">
                <form id="checkoutForm">
                    <input type="hidden" name="_token" value="<?= e(csrf_token()) ?>">

                    <!-- Dados Pessoais -->
                    <div class="checkout-section">
                        <h3>Dados Pessoais</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nome *</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="Seu nome">
                            </div>
                            <div class="form-group">
                                <label>Sobrenome *</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Seu sobrenome">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" required placeholder="seuemail@exemplo.com">
                            </div>
                            <div class="form-group">
                                <label>Telefone/WhatsApp *</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="+55 11 99999-9999">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>País *</label>
                                <select name="country" class="form-control" required>
                                    <option value="BR" selected>Brasil</option>
                                    <option value="US">Estados Unidos</option>
                                    <option value="AR">Argentina</option>
                                    <option value="CO">Colômbia</option>
                                    <option value="CL">Chile</option>
                                    <option value="PT">Portugal</option>
                                    <option value="DO">República Dominicana</option>
                                    <option value="OTHER">Outro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cidade</label>
                                <input type="text" name="city" class="form-control" placeholder="Sua cidade">
                            </div>
                        </div>
                    </div>

                    <!-- Dados da Viagem -->
                    <div class="checkout-section">
                        <h3>Dados da Viagem</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Hotel / Resort</label>
                                <input type="text" name="hotel" class="form-control" placeholder="Nome do hotel que vai ficar">
                            </div>
                            <div class="form-group">
                                <label>Número do Voo (chegada)</label>
                                <input type="text" name="flight_number" class="form-control" placeholder="Ex: LA8170">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Data de Chegada</label>
                                <input type="date" name="arrival_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Data de Partida</label>
                                <input type="date" name="departure_date" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Observações</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Informações adicionais, restrições alimentares, necessidades especiais..."></textarea>
                        </div>
                    </div>

                    <!-- Método de Pagamento -->
                    <div class="checkout-section">
                        <h3>Forma de Pagamento</h3>
                        <?php foreach ($gateways as $gw): ?>
                        <label class="payment-option">
                            <input type="radio" name="gateway" value="<?= e($gw['id']) ?>" <?= $gw['id'] === 'paypal' ? 'checked' : '' ?>>
                            <span class="payment-label">
                                <strong><?= e($gw['name']) ?></strong>
                                <small><?= e($gw['description']) ?></small>
                            </span>
                        </label>
                        <?php endforeach; ?>

                        <?php if ($partialEnabled): ?>
                        <div class="partial-payment-option">
                            <label>
                                <input type="checkbox" name="payment_mode" value="partial" id="partialCheck">
                                Pagar apenas <?= (int)$partialPercent ?>% agora (depósito de <?= money($cart['grand_total'] * $partialPercent / 100) ?>)
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Termos -->
                    <div class="checkout-section">
                        <label class="terms-checkbox">
                            <input type="checkbox" id="termsCheck" required>
                            Li e aceito os <a href="/termos-e-condicoes" target="_blank">termos e condições</a> e as <a href="/politicas-de-cancelamento" target="_blank">políticas de cancelamento</a>
                        </label>
                    </div>

                    <!-- Botão -->
                    <div id="paymentContainer">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-block btn-lg">
                            Confirmar e Pagar <?= money($cart['grand_total']) ?>
                        </button>
                    </div>

                    <!-- PayPal Container -->
                    <div id="paypalButtonContainer" style="display:none;"></div>
                </form>
            </div>

            <!-- Resumo lateral -->
            <aside class="checkout-summary">
                <div class="summary-card">
                    <h3>Resumo do Pedido</h3>
                    <?php foreach ($cart['trips'] as $item): ?>
                    <div class="summary-item">
                        <span><?= e(truncate($item['trip_title'], 30)) ?></span>
                        <span><?= money($item['total']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php foreach ($cart['transfers'] as $item): ?>
                    <div class="summary-item">
                        <span>Transfer: <?= e(truncate($item['vehicle_title'], 25)) ?></span>
                        <span><?= money((float)$item['price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span id="checkoutTotal"><?= money($cart['grand_total']) ?></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- Loading Overlay -->
<div class="checkout-loading" id="checkoutLoading" style="display:none;">
    <div class="loading-content">
        <div class="spinner"></div>
        <p>Processando seu pagamento...</p>
    </div>
</div>

<script>
const CHECKOUT_CONFIG = {
    paypalClientId: '<?= e($paypalClientId) ?>',
    stripePublishableKey: '<?= e($stripePublishableKey) ?>',
    total: <?= $cart['grand_total'] ?>,
    partialPercent: <?= $partialPercent ?? 50 ?>,
    csrfToken: '<?= e(csrf_token()) ?>'
};
</script>
<?php if ($paypalClientId): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= e($paypalClientId) ?>&currency=USD"></script>
<?php endif; ?>
<?php if ($stripePublishableKey): ?>
<script src="https://js.stripe.com/v3/"></script>
<?php endif; ?>
