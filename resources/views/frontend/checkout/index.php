<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Checkout</h1>
            <p>Preencha seus dados para finalizar a reserva.</p>
        </div>
    </div>
</section>

<!-- Stepper de Progresso -->
<section style="padding:30px 0 10px;">
    <div class="container">
        <div id="checkoutStepper" style="display:flex;align-items:center;justify-content:center;gap:0;flex-wrap:nowrap;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:50%;border:2px solid #0077b6;display:flex;align-items:center;justify-content:center;background:#fff;color:#0077b6;flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size:13px;font-weight:500;color:#6b7280;white-space:nowrap;">Selecione uma data</span>
            </div>
            <div style="width:50px;height:2px;background:#0077b6;margin:0 12px;flex-shrink:0;"></div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:50%;border:2px solid #0077b6;display:flex;align-items:center;justify-content:center;background:#fff;color:#0077b6;flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span style="font-size:13px;font-weight:500;color:#6b7280;white-space:nowrap;">Viajantes</span>
            </div>
            <div id="stepperLine3" style="width:50px;height:2px;background:#0077b6;margin:0 12px;flex-shrink:0;"></div>
            <div id="stepperStep3" style="display:flex;align-items:center;gap:10px;">
                <div id="stepperCircle3" style="width:36px;height:36px;border-radius:50%;border:2px solid #0077b6;display:flex;align-items:center;justify-content:center;background:#0077b6;color:#fff;font-size:13px;font-weight:600;flex-shrink:0;">3</div>
                <span id="stepperLabel3" style="font-size:13px;font-weight:600;color:#1f2937;white-space:nowrap;">Detalhes De Cobrança</span>
            </div>
            <div id="stepperLine4" style="width:50px;height:2px;background:#e5e7eb;margin:0 12px;flex-shrink:0;"></div>
            <div id="stepperStep4" style="display:flex;align-items:center;gap:10px;">
                <div id="stepperCircle4" style="width:36px;height:36px;border-radius:50%;border:2px solid #d1d5db;display:flex;align-items:center;justify-content:center;background:#fff;color:#9ca3af;font-size:13px;font-weight:600;flex-shrink:0;">4</div>
                <span id="stepperLabel4" style="font-size:13px;font-weight:500;color:#9ca3af;white-space:nowrap;">Pagamento</span>
            </div>
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

                    <!-- Step 3: Detalhes de Cobrança -->
                    <div id="checkoutStep3">

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
                                <input type="tel" name="phone" class="form-control" required placeholder="+55 11 99999-9999" data-phone-country>
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
                            <label>Rua / Avenida <span class="address-required-label"></span></label>
                            <input type="text" name="address_street" class="form-control address-field" placeholder="Ex: Rua das Flores, 123">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Complemento <span class="address-required-label"></span></label>
                                <input type="text" name="address_complement" class="form-control" placeholder="Apto, bloco, sala...">
                            </div>
                            <div class="form-group">
                                <label>Bairro <span class="address-required-label"></span></label>
                                <input type="text" name="address_neighborhood" class="form-control address-field" placeholder="Seu bairro">
                            </div>
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

                    <!-- Botão ir para pagamento -->
                    <div class="checkout-section checkout-next-step" id="checkoutStep3Actions">
                        <button type="button" class="btn btn-primary btn-block btn-lg" id="goToPaymentBtn">
                            Continuar para Pagamento
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:8px;"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>
                    </div><!-- end step3 -->

                    <!-- Step 4: Pagamento (oculto inicialmente) -->
                    <div id="checkoutStep4" style="display:none;">

                    <!-- Método de Pagamento -->
                    <div class="checkout-section">
                        <h3>Forma de Pagamento</h3>
                        <?php if ($checkoutOnlineEnabled ?? true): ?>
                        <div class="payment-methods">
                            <?php foreach ($gateways as $gw): ?>
                            <label class="payment-option <?= $gw['id'] === ($gateways[0]['id'] ?? '') ? 'active' : '' ?>">
                                <input type="radio" name="gateway" value="<?= e($gw['id']) ?>" <?= $gw['id'] === ($gateways[0]['id'] ?? '') ? 'checked' : '' ?>>
                                <div class="payment-option-icon">
                                    <?php if ($gw['icon'] === 'paypal'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#003087"><path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797H9.603c-.536 0-.99.394-1.073.926l-.002.012-.894 5.7-.002.012c-.058.37-.348.646-.72.646z"/></svg>
                                    <?php elseif ($gw['icon'] === 'card'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    <?php elseif ($gw['icon'] === 'pix'): ?>
                                    <img src="<?= asset('images/pix-logo.png') ?>" alt="PIX" width="24" height="24" style="object-fit:contain;">
                                    <?php elseif ($gw['icon'] === 'simulate'): ?>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    <?php endif; ?>
                                </div>
                                <div class="payment-option-info">
                                    <strong><?= e($gw['name']) ?></strong>
                                    <small><?= e($gw['description']) ?></small>
                                </div>
                                <div class="payment-option-check">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (($checkoutWhatsappEnabled ?? true) && ($checkoutOnlineEnabled ?? true)): ?>
                        <!-- Divisor "ou" entre pagamento online e WhatsApp -->
                        <div style="display:flex;align-items:center;gap:12px;margin:24px 0 16px;">
                            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                            <span style="font-size:0.8rem;color:#94a3b8;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">ou</span>
                            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($checkoutWhatsappEnabled ?? true): ?>
                        <?php
                        // Montar mensagem detalhada para WhatsApp
                        $whatsappMsg = "Olá! Estou no checkout e gostaria de finalizar minha reserva com ajuda.\n\n";
                        $whatsappMsg .= "📋 *MINHA RESERVA:*\n\n";

                        if (!empty($cart['trips'])) {
                            $whatsappMsg .= "🎯 *PASSEIOS:*\n";
                            foreach ($cart['trips'] as $tripItem) {
                                $whatsappMsg .= "• " . ($tripItem['trip_title'] ?? 'Passeio') . "\n";
                                $whatsappMsg .= "  📅 Data: " . format_date($tripItem['date'] ?? '') . (!empty($tripItem['time']) ? " às " . $tripItem['time'] : '') . "\n";
                                $whatsappMsg .= "  👥 " . (int)($tripItem['total_pax'] ?? 1) . " passageiro(s)\n";
                                $whatsappMsg .= "  💵 " . money((float)($tripItem['total'] ?? 0)) . " USD\n\n";
                            }
                        }

                        if (!empty($cart['transfers'])) {
                            $whatsappMsg .= "🚐 *TRANSFERS:*\n";
                            foreach ($cart['transfers'] as $transferItem) {
                                $whatsappMsg .= "• " . ($transferItem['origin_title'] ?? '') . " → " . ($transferItem['destination_title'] ?? '') . "\n";
                                $whatsappMsg .= "  🚗 " . ($transferItem['vehicle_title'] ?? 'Transfer') . "\n";
                                $whatsappMsg .= "  📅 " . format_date($transferItem['date'] ?? '') . " às " . ($transferItem['time'] ?? '') . "\n";
                                $pax = (int)($transferItem['adults'] ?? 0) + (int)($transferItem['children'] ?? 0) + (int)($transferItem['infants'] ?? 0);
                                $whatsappMsg .= "  👥 " . $pax . " passageiro(s)\n";
                                $whatsappMsg .= "  💵 " . money((float)($transferItem['price'] ?? 0)) . " USD\n\n";
                            }
                        }

                        $whatsappMsg .= "💰 *Total: \$" . number_format($cart['grand_total'], 2) . " USD*\n\n";
                        $whatsappMsg .= "Pode me ajudar a finalizar?";
                        ?>
                        <a href="https://api.whatsapp.com/send?phone=18294582170&text=<?= urlencode($whatsappMsg) ?>" target="_blank" rel="noopener" class="whatsapp-checkout-btn" style="display:flex;align-items:center;gap:14px;padding:16px 20px;background:#fff;border:2px solid #25D366;border-radius:10px;text-decoration:none;transition:all 0.2s ease;cursor:pointer;">
                            <div style="width:44px;height:44px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.625-1.472A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-2.168 0-4.19-.588-5.932-1.614l-.424-.253-2.744.874.87-2.675-.278-.442A9.78 9.78 0 012.182 12c0-5.422 4.396-9.818 9.818-9.818S21.818 6.578 21.818 12s-4.396 9.818-9.818 9.818z"/></svg>
                            </div>
                            <div style="flex:1;">
                                <strong style="font-size:0.95rem;color:#1f2937;display:block;">Finalizar pelo WhatsApp</strong>
                                <span style="font-size:0.8rem;color:#6b7280;margin-top:2px;display:block;">Tire suas dúvidas e finalize com um atendente</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#25D366" stroke-width="2.5" style="flex-shrink:0;"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        <style>
                            .whatsapp-checkout-btn:hover { background: #f0fdf4 !important; border-color: #16a34a !important; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,211,102,0.15); }
                        </style>
                        <?php endif; ?>

                        <!-- CPF para PIX -->
                        <div class="pix-cpf-field" id="pixCpfField" style="display:none;">
                            <div class="form-group">
                                <label>CPF <span class="text-muted">(obrigatório para PIX)</span></label>
                                <input type="text" name="cpf" id="cpfInput" class="form-control" placeholder="000.000.000-00" maxlength="14">
                            </div>
                        </div>

                        <!-- Pagamento parcial obrigatório -->
                        <div class="partial-payment-info" style="margin-top:16px;padding:14px 18px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;">
                            <input type="hidden" name="payment_mode" value="partial" id="partialCheck">
                            <p style="font-size:13px;color:#0369a1;margin:0;font-weight:600;">
                                💳 Pague apenas <?= (int)$partialPercent ?>% agora: <strong><?= money($partialAmount ?? ($cart['grand_total'] * $partialPercent / 100)) ?></strong>
                            </p>
                            <p style="font-size:12px;color:#64748b;margin:4px 0 0;">O restante (<?= money($cart['grand_total'] - ($partialAmount ?? ($cart['grand_total'] * $partialPercent / 100))) ?>) deve ser pago antes da data do passeio/transfer.</p>
                        </div>
                    </div>

                    </div><!-- end checkoutStep4 -->

                    <!-- Termos -->
                    <div class="checkout-section checkout-terms" id="checkoutTerms" style="display:none;">
                        <label class="terms-checkbox">
                            <input type="checkbox" id="termsCheck" required>
                            <span>Marque a caixa para confirmar que você leu e concorda com nossos <a href="/termos-e-condicoes" target="_blank">termos e condições</a> e <a href="/politicas-de-privacidade" target="_blank">política de privacidade</a>.</span>
                        </label>
                    </div>

                    <!-- Botão -->
                    <div id="paymentContainer" class="checkout-submit" style="display:none;">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-block btn-lg">
                            Confirmar e Pagar <?= money($partialAmount ?? ($cart['grand_total'] * $partialPercent / 100)) ?>
                        </button>
                    </div>

                    <!-- PayPal Container -->
                    <div id="paypalButtonContainer" style="display:none; margin-top: 16px;"></div>

                    <!-- PIX QR Code Container -->
                    <div id="pixContainer" style="display:none; margin-top: 16px;">
                        <div class="pix-qr-card">
                            <div style="margin-bottom:16px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>
                            </div>
                            <h4>Pague com PIX</h4>
                            <p style="font-size:13px;color:#6b7280;margin-bottom:20px;">Escaneie o QR Code abaixo ou copie o código para pagar</p>
                            <div class="pix-qr-image" id="pixQrImage"></div>
                            <p id="pixAmountBRL" style="font-size:20px;font-weight:700;color:#1f2937;margin:16px 0 4px;"></p>
                            <div class="pix-copy-paste">
                                <label>Código PIX (copia e cola):</label>
                                <div class="pix-code-wrapper">
                                    <input type="text" id="pixCodeText" class="form-control" readonly>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="copyPixCode()">Copiar</button>
                                </div>
                            </div>
                            <p class="pix-expiration">Expira em <span id="pixTimer">30:00</span> minutos</p>
                            <p class="pix-status" id="pixStatus">Aguardando pagamento...</p>
                        </div>
                    </div>
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
                                <span class="summary-product-code">Cód. Viagem: <span>WTE-<?= 8000 + (int)$item['trip_id'] ?></span></span>
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
                        <div class="summary-row summary-partial" id="partialRow" style="display:flex;">
                            <span>Pagamento agora (<?= (int)$partialPercent ?>%):</span>
                            <span id="partialAmount"><strong><?= money($partialAmount ?? ($cart['grand_total'] * $partialPercent / 100)) ?></strong></span>
                        </div>
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
    const pixCpfField = document.getElementById('pixCpfField');
    const paymentOptions = document.querySelectorAll('.payment-option');

    function updateAddressRequired() {
        const selected = document.querySelector('input[name="gateway"]:checked');
        const gateway = selected ? selected.value : '';

        // Stripe exige endereço
        const requireAddress = gateway === 'stripe';
        addressFields.forEach(field => {
            if (requireAddress) { field.setAttribute('required', 'required'); }
            else { field.removeAttribute('required'); }
        });
        addressLabels.forEach(label => {
            label.textContent = requireAddress ? '*' : '(opcional)';
        });

        // PIX exige CPF
        if (pixCpfField) {
            pixCpfField.style.display = gateway === 'pix' ? 'block' : 'none';
            const cpfInput = document.getElementById('cpfInput');
            if (cpfInput) {
                if (gateway === 'pix') { cpfInput.setAttribute('required', 'required'); }
                else { cpfInput.removeAttribute('required'); }
            }
        }

        // Atualizar visual das opções
        paymentOptions.forEach(opt => opt.classList.remove('active'));
        if (selected) selected.closest('.payment-option')?.classList.add('active');
    }

    gatewayRadios.forEach(radio => {
        radio.addEventListener('change', updateAddressRequired);
    });
    updateAddressRequired();

    // CPF mask
    const cpfInput = document.getElementById('cpfInput');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 11) v = v.slice(0, 11);
            if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
            else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            e.target.value = v;
        });
    }

    // Partial payment toggle
    const partialCheck = document.getElementById('partialCheck');
    const partialRow = document.getElementById('partialRow');
    if (partialCheck && partialRow) {
        partialCheck.addEventListener('change', function() {
            partialRow.style.display = this.checked ? 'flex' : 'none';
        });
    }
});

// Copy PIX code
function copyPixCode() {
    const codeInput = document.getElementById('pixCodeText');
    if (codeInput) {
        codeInput.select();
        document.execCommand('copy');
        alert('Código PIX copiado!');
    }
}

// Step navigation: Step 3 -> Step 4
document.getElementById('goToPaymentBtn')?.addEventListener('click', function() {
    // Validar campos obrigatórios do step 3
    const step3 = document.getElementById('checkoutStep3');
    const requiredFields = step3.querySelectorAll('[required]');
    let valid = true;
    requiredFields.forEach(f => { if (!f.value.trim()) { f.focus(); valid = false; } });
    if (!valid) { alert('Preencha todos os campos obrigatórios.'); return; }

    // Esconder step 3, mostrar step 4
    step3.style.display = 'none';
    document.getElementById('checkoutStep3Actions').style.display = 'none';
    document.getElementById('checkoutStep4').style.display = 'block';
    document.getElementById('checkoutTerms').style.display = 'block';
    document.getElementById('paymentContainer').style.display = 'block';

    // Atualizar stepper: step 3 -> completed, step 4 -> active
    const circle3 = document.getElementById('stepperCircle3');
    const label3 = document.getElementById('stepperLabel3');
    const line4 = document.getElementById('stepperLine4');
    const circle4 = document.getElementById('stepperCircle4');
    const label4 = document.getElementById('stepperLabel4');

    // Step 3 completed
    circle3.style.background = '#fff';
    circle3.style.color = '#0077b6';
    circle3.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
    label3.style.fontWeight = '500';
    label3.style.color = '#6b7280';

    // Line 4 active
    line4.style.background = '#0077b6';

    // Step 4 active
    circle4.style.border = '2px solid #0077b6';
    circle4.style.background = '#0077b6';
    circle4.style.color = '#fff';
    label4.style.fontWeight = '600';
    label4.style.color = '#1f2937';

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Scroll stepper para o passo ativo no mobile
    var stepperEl = document.getElementById('checkoutStepper');
    var activeStep = document.getElementById('stepperStep4');
    if (stepperEl && activeStep && window.innerWidth <= 576) {
        setTimeout(function() {
            activeStep.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });
        }, 300);
    }
});
</script>
<?php if ($paypalClientId): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?= e($paypalClientId) ?>&currency=USD"></script>
<?php endif; ?>
<?php if ($stripePublishableKey): ?>
<script src="https://js.stripe.com/v3/"></script>
<?php endif; ?>
