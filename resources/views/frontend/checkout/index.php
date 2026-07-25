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
                                <label>WhatsApp *</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="+55 11 99999-9999">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>País *</label>
                                <select name="country" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <option value="BR" selected>Brasil</option>
                                    <option value="US">Estados Unidos</option>
                                    <option value="AR">Argentina</option>
                                    <option value="CO">Colômbia</option>
                                    <option value="CL">Chile</option>
                                    <option value="PT">Portugal</option>
                                    <option value="DO">República Dominicana</option>
                                    <option value="MX">México</option>
                                    <option value="UY">Uruguai</option>
                                    <option value="PY">Paraguai</option>
                                    <option value="OTHER">Outro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cidade *</label>
                                <input type="text" name="city" class="form-control" required placeholder="Sua cidade">
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="checkout-section" id="addressSection">
                        <h3>Endereço</h3>
                        <div class="form-group">
                            <label>Endereço <span class="address-required-label"></span></label>
                            <input type="text" name="address" class="form-control address-field" placeholder="Rua, número, complemento">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Estado / Província <span class="address-required-label"></span></label>
                                <input type="text" name="state" class="form-control address-field" placeholder="Ex: São Paulo">
                            </div>
                            <div class="form-group">
                                <label>CEP / Código Postal <span class="address-required-label"></span></label>
                                <input type="text" name="zip_code" class="form-control address-field" placeholder="Ex: 01001-000">
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="checkout-section">
                        <h3>Observações</h3>
                        <div class="form-group">
                            <textarea name="notes" class="form-control" rows="3" placeholder="Informações adicionais, restrições alimentares, necessidades especiais, pedidos..."></textarea>
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
                            <p class="partial-note">O restante deve ser pago antes da data do passeio/transfer.</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Termos -->
                    <div class="checkout-section checkout-terms">
                        <label class="terms-checkbox">
                            <input type="checkbox" id="termsCheck" required>
                            <span>Marque a caixa para confirmar que você leu e concorda com nossos <a href="/termos-e-condicoes" target="_blank">termos e condições</a> e <a href="/politicas-de-privacidade" target="_blank">política de privacidade</a>.</span>
                        </label>
                    </div>

                    <!-- Botão -->
                    <div id="paymentContainer" class="checkout-submit">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-block btn-lg">
                            Confirmar e Pagar <?= money($cart['grand_total']) ?>
                        </button>
                    </div>

                    <!-- PayPal Container -->
                    <div id="paypalButtonContainer" style="display:none; margin-top: 16px;"></div>
                </form>
            </div>

            <!-- Resumo lateral -->
            <aside class="checkout-summary">
                <div class="summary-card">
                    <h3>Resumo da Reserva</h3>

                    <?php if (!empty($cart['trips'])): ?>
                    <div class="summary-group">
                        <h4 class="summary-group-title">Passeios</h4>
                        <?php foreach ($cart['trips'] as $item): ?>
                        <div class="summary-product">
                            <div class="summary-product-info">
                                <strong><?= e($item['trip_title']) ?></strong>
                                <span class="summary-product-meta">
                                    <?= format_date($item['date']) ?>
                                    <?php if (!empty($item['time'])): ?> às <?= e($item['time']) ?><?php endif; ?>
                                </span>
                                <?php if (!empty($item['package_title'])): ?>
                                <span class="summary-product-meta">Pacote: <?= e($item['package_title']) ?></span>
                                <?php endif; ?>
                                <span class="summary-product-meta"><?= (int)$item['total_pax'] ?> passageiro(s)</span>
                            </div>
                            <div class="summary-product-price"><?= money($item['total']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($cart['transfers'])): ?>
                    <div class="summary-group">
                        <h4 class="summary-group-title">Transfers</h4>
                        <?php foreach ($cart['transfers'] as $item): ?>
                        <div class="summary-product">
                            <div class="summary-product-info">
                                <strong><?= e($item['vehicle_title']) ?></strong>
                                <span class="summary-product-meta">
                                    <?= e($item['origin_title']) ?> &rarr; <?= e($item['destination_title']) ?>
                                </span>
                                <span class="summary-product-meta">
                                    <?= format_date($item['date']) ?> às <?= e($item['time']) ?>
                                    | <?= e($item['type'] === 'arrival' ? 'Chegada' : 'Partida') ?>
                                </span>
                                <span class="summary-product-meta">
                                    <?= (int)$item['adults'] + (int)$item['children'] + (int)$item['infants'] ?> passageiro(s)
                                </span>
                            </div>
                            <div class="summary-product-price"><?= money((float)$item['price']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="summary-totals">
                        <?php if ($cart['trip_total'] > 0): ?>
                        <div class="summary-row"><span>Subtotal Passeios:</span><span><?= money($cart['trip_total']) ?></span></div>
                        <?php endif; ?>
                        <?php if ($cart['transfer_total'] > 0): ?>
                        <div class="summary-row"><span>Subtotal Transfers:</span><span><?= money($cart['transfer_total']) ?></span></div>
                        <?php endif; ?>
                        <div class="summary-row summary-total">
                            <span>Total:</span>
                            <span id="checkoutTotal"><?= money($cart['grand_total']) ?></span>
                        </div>
                        <?php if ($partialEnabled): ?>
                        <div class="summary-row summary-partial" id="partialRow" style="display:none;">
                            <span>Pagamento agora (<?= (int)$partialPercent ?>%):</span>
                            <span id="partialAmount"><?= money($cart['grand_total'] * $partialPercent / 100) ?></span>
                        </div>
                        <?php endif; ?>
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
    csrfToken: '<?= e(csrf_token()) ?>',
    stripeActive: <?= !empty($stripePublishableKey) ? 'true' : 'false' ?>,
    paypalActive: <?= !empty($paypalClientId) ? 'true' : 'false' ?>
};

// Endereço obrigatório dependendo do gateway
document.addEventListener('DOMContentLoaded', function() {
    const gatewayRadios = document.querySelectorAll('input[name="gateway"]');
    const addressFields = document.querySelectorAll('.address-field');
    const addressLabels = document.querySelectorAll('.address-required-label');

    function updateAddressRequired() {
        const selected = document.querySelector('input[name="gateway"]:checked');
        // Stripe exige endereço, PayPal não
        const requireAddress = selected && selected.value === 'stripe';

        addressFields.forEach(field => {
            if (requireAddress) {
                field.setAttribute('required', 'required');
            } else {
                field.removeAttribute('required');
            }
        });
        addressLabels.forEach(label => {
            label.textContent = requireAddress ? '*' : '(opcional)';
        });
    }

    gatewayRadios.forEach(radio => {
        radio.addEventListener('change', updateAddressRequired);
    });
    updateAddressRequired();

    // Partial payment toggle
    const partialCheck = document.getElementById('partialCheck');
    const partialRow = document.getElementById('partialRow');
    if (partialCheck && partialRow) {
        partialCheck.addEventListener('change', function() {
            partialRow.style.display = this.checked ? 'flex' : 'none';
        });
    }
});
</script>
<?php if ($paypalClientId): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= e($paypalClientId) ?>&currency=USD"></script>
<?php endif; ?>
<?php if ($stripePublishableKey): ?>
<script src="https://js.stripe.com/v3/"></script>
<?php endif; ?>
