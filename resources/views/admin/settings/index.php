<form method="POST" action="/admin/configuracoes" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Tabs -->
    <div class="settings-tabs">
        <button type="button" class="tab-btn active" data-tab="general">Geral</button>
        <button type="button" class="tab-btn" data-tab="email">SMTP / Email</button>
        <button type="button" class="tab-btn" data-tab="payments">Pagamentos</button>
        <button type="button" class="tab-btn" data-tab="whatsapp">WhatsApp</button>
        <button type="button" class="tab-btn" data-tab="vouchers">Vouchers</button>
        <button type="button" class="tab-btn" data-tab="affiliates">Afiliados</button>
        <button type="button" class="tab-btn" data-tab="seo">SEO</button>
        <button type="button" class="tab-btn" data-tab="appearance">Aparência</button>
    </div>

    <!-- Geral -->
    <div class="tab-content active" id="tab-general">
        <div class="form-group"><label>Nome do Site</label><input type="text" name="site_name" class="form-control" value="<?= e($settings['general']['site_name']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>URL do Site</label><input type="url" name="site_url" class="form-control" value="<?= e($settings['general']['site_url']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Email do Administrador</label><input type="email" name="admin_email" class="form-control" value="<?= e($settings['general']['admin_email']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Logo</label><input type="file" name="site_logo" class="form-control" accept="image/*"></div>
        <div class="form-group"><label>Moeda</label><input type="text" name="currency" class="form-control" value="<?= e($settings['general']['currency']['setting_value'] ?? 'USD') ?>"></div>
        <div class="form-group"><label>Símbolo</label><input type="text" name="currency_symbol" class="form-control" value="<?= e($settings['general']['currency_symbol']['setting_value'] ?? '$') ?>"></div>
        <div class="form-group"><label>Fuso Horário</label><input type="text" name="timezone" class="form-control" value="<?= e($settings['general']['timezone']['setting_value'] ?? 'America/Santo_Domingo') ?>"></div>
    </div>

    <!-- SMTP -->
    <div class="tab-content" id="tab-email">
        <div class="form-row">
            <div class="form-group"><label>Host SMTP</label><input type="text" name="smtp_host" class="form-control" value="<?= e($settings['email']['smtp_host']['setting_value'] ?? '') ?>"></div>
            <div class="form-group"><label>Porta</label><input type="number" name="smtp_port" class="form-control" value="<?= e($settings['email']['smtp_port']['setting_value'] ?? '587') ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Usuário</label><input type="text" name="smtp_username" class="form-control" value="<?= e($settings['email']['smtp_username']['setting_value'] ?? '') ?>"></div>
            <div class="form-group"><label>Senha</label><input type="password" name="smtp_password" class="form-control" value="<?= e($settings['email']['smtp_password']['setting_value'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>Encryption</label>
            <select name="smtp_encryption" class="form-control">
                <option value="tls" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                <option value="ssl" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                <option value="none" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'none' ? 'selected' : '' ?>>Nenhuma</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Email Remetente</label><input type="email" name="mail_from_email" class="form-control" value="<?= e($settings['email']['mail_from_email']['setting_value'] ?? '') ?>"></div>
            <div class="form-group"><label>Nome Remetente</label><input type="text" name="mail_from_name" class="form-control" value="<?= e($settings['email']['mail_from_name']['setting_value'] ?? '') ?>"></div>
        </div>
        <hr>
        <h4>Teste de Email</h4>
        <div class="form-row">
            <div class="form-group"><input type="email" id="testEmail" class="form-control" placeholder="Email para teste"></div>
            <button type="button" onclick="sendTestEmail()" class="btn btn-outline">Enviar Teste</button>
        </div>
    </div>

    <!-- Pagamentos -->
    <div class="tab-content" id="tab-payments">
        <h4>PayPal Express</h4>
        <div class="form-group"><label><input type="checkbox" name="paypal_enabled" value="1" <?= ($settings['payments']['paypal_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar PayPal</label></div>
        <div class="form-group"><label>Client ID</label><input type="text" name="paypal_client_id" class="form-control" value="<?= e($settings['payments']['paypal_client_id']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Secret Key</label><input type="password" name="paypal_secret" class="form-control" value="<?= e($settings['payments']['paypal_secret']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Modo</label><select name="paypal_mode" class="form-control"><option value="sandbox" <?= ($settings['payments']['paypal_mode']['setting_value'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option><option value="production" <?= ($settings['payments']['paypal_mode']['setting_value'] ?? '') === 'production' ? 'selected' : '' ?>>Produção</option></select></div>

        <hr><h4>Stripe</h4>
        <div class="form-group"><label><input type="checkbox" name="stripe_enabled" value="1" <?= ($settings['payments']['stripe_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Stripe</label></div>
        <div class="form-group"><label>Publishable Key</label><input type="text" name="stripe_publishable_key" class="form-control" value="<?= e($settings['payments']['stripe_publishable_key']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Secret Key</label><input type="password" name="stripe_secret_key" class="form-control" value="<?= e($settings['payments']['stripe_secret_key']['setting_value'] ?? '') ?>"></div>

        <hr><h4>Pagamento Parcial</h4>
        <div class="form-group"><label><input type="checkbox" name="partial_payment_enabled" value="1" <?= ($settings['payments']['partial_payment_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Pagamento Parcial</label></div>
        <div class="form-group"><label>Percentual de Depósito (%)</label><input type="number" name="partial_payment_percent" class="form-control" value="<?= e($settings['payments']['partial_payment_percent']['setting_value'] ?? '50') ?>" min="1" max="99"></div>
    </div>

    <!-- WhatsApp -->
    <div class="tab-content" id="tab-whatsapp">
        <div class="form-group"><label><input type="checkbox" name="whatsapp_enabled" value="1" <?= ($settings['whatsapp']['whatsapp_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Notificações WhatsApp</label></div>
        <div class="form-group"><label>URL do Webhook</label><input type="url" name="whatsapp_webhook_url" class="form-control" value="<?= e($settings['whatsapp']['whatsapp_webhook_url']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Template - Passeio</label><textarea name="whatsapp_trip_template" class="form-control" rows="5"><?= e($settings['whatsapp']['whatsapp_trip_template']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Template - Transfer</label><textarea name="whatsapp_transfer_template" class="form-control" rows="5"><?= e($settings['whatsapp']['whatsapp_transfer_template']['setting_value'] ?? '') ?></textarea></div>
    </div>

    <!-- Vouchers -->
    <div class="tab-content" id="tab-vouchers">
        <div class="form-group"><label>Logo do Voucher</label><input type="file" name="voucher_logo" class="form-control" accept="image/*"></div>
        <div class="form-group"><label>Texto de Rodapé</label><textarea name="voucher_footer_text" class="form-control" rows="2"><?= e($settings['vouchers']['voucher_footer_text']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Instruções Padrão</label><textarea name="voucher_instructions" class="form-control" rows="3"><?= e($settings['vouchers']['voucher_instructions']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Limpar vouchers após (dias)</label><input type="number" name="voucher_cleanup_days" class="form-control" value="<?= e($settings['vouchers']['voucher_cleanup_days']['setting_value'] ?? '90') ?>"></div>
    </div>

    <!-- Afiliados -->
    <div class="tab-content" id="tab-affiliates">
        <div class="form-group"><label><input type="checkbox" name="affiliate_enabled" value="1" <?= ($settings['affiliates']['affiliate_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Programa de Afiliados</label></div>
        <div class="form-group"><label>Comissão Padrão (%)</label><input type="number" name="affiliate_default_rate" class="form-control" value="<?= e($settings['affiliates']['affiliate_default_rate']['setting_value'] ?? '20') ?>" step="0.01"></div>
        <div class="form-group"><label>Duração do Cookie (dias)</label><input type="number" name="affiliate_cookie_days" class="form-control" value="<?= e($settings['affiliates']['affiliate_cookie_days']['setting_value'] ?? '30') ?>"></div>
        <div class="form-group"><label><input type="checkbox" name="affiliate_auto_approve" value="1" <?= ($settings['affiliates']['affiliate_auto_approve']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Auto-aprovar afiliados</label></div>
    </div>

    <!-- SEO -->
    <div class="tab-content" id="tab-seo">
        <div class="form-group"><label>Meta Title Padrão</label><input type="text" name="meta_title" class="form-control" value="<?= e($settings['seo']['meta_title']['setting_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Meta Description Padrão</label><textarea name="meta_description" class="form-control" rows="2"><?= e($settings['seo']['meta_description']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Google Analytics ID</label><input type="text" name="google_analytics_id" class="form-control" value="<?= e($settings['seo']['google_analytics_id']['setting_value'] ?? '') ?>" placeholder="G-XXXXXXXXXX"></div>
        <div class="form-group"><label>Scripts do &lt;head&gt;</label><textarea name="head_scripts" class="form-control" rows="4"><?= e($settings['seo']['head_scripts']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Scripts do &lt;/body&gt;</label><textarea name="body_scripts" class="form-control" rows="4"><?= e($settings['seo']['body_scripts']['setting_value'] ?? '') ?></textarea></div>
    </div>

    <!-- Aparência -->
    <div class="tab-content" id="tab-appearance">
        <div class="form-row">
            <div class="form-group"><label>Cor Primária</label><input type="color" name="color_primary" class="form-control form-control-color" value="<?= e($settings['appearance']['color_primary']['setting_value'] ?? '#0077b6') ?>"></div>
            <div class="form-group"><label>Cor Secundária</label><input type="color" name="color_secondary" class="form-control form-control-color" value="<?= e($settings['appearance']['color_secondary']['setting_value'] ?? '#00b4d8') ?>"></div>
            <div class="form-group"><label>Cor Accent</label><input type="color" name="color_accent" class="form-control form-control-color" value="<?= e($settings['appearance']['color_accent']['setting_value'] ?? '#f77f00') ?>"></div>
        </div>
        <div class="form-group"><label>Fonte Principal</label><input type="text" name="font_primary" class="form-control" value="<?= e($settings['appearance']['font_primary']['setting_value'] ?? 'Poppins') ?>"></div>
        <div class="form-group"><label>CSS Customizado</label><textarea name="custom_css" class="form-control" rows="5"><?= e($settings['appearance']['custom_css']['setting_value'] ?? '') ?></textarea></div>
        <div class="form-group"><label>WhatsApp (número para botão flutuante)</label><input type="text" name="whatsapp_float_number" class="form-control" value="<?= e($settings['appearance']['whatsapp_float_number']['setting_value'] ?? '') ?>" placeholder="5511999999999"></div>
        <div class="form-group"><label>Texto do Botão WhatsApp</label><input type="text" name="whatsapp_float_text" class="form-control" value="<?= e($settings['appearance']['whatsapp_float_text']['setting_value'] ?? 'Fale conosco!') ?>"></div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">Salvar Configurações</button>
    </div>
</form>

<script>
function sendTestEmail() {
    const email = document.getElementById('testEmail').value;
    if (!email) { alert('Informe um email'); return; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/configuracoes/email-teste';
    form.innerHTML = '<input name="_token" value="<?= e(csrf_token()) ?>"><input name="test_email" value="'+email+'">';
    document.body.appendChild(form);
    form.submit();
}
</script>
