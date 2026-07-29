<form method="POST" action="/admin/configuracoes" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Tabs -->
    <div class="settings-tabs">
        <button type="button" class="settings-tab active" data-tab="general">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
            Geral
        </button>
        <button type="button" class="settings-tab" data-tab="email">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            SMTP / Email
        </button>
        <button type="button" class="settings-tab" data-tab="payments">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Pagamentos
        </button>
        <button type="button" class="settings-tab" data-tab="whatsapp">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
            WhatsApp
        </button>
        <button type="button" class="settings-tab" data-tab="vouchers">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/></svg>
            Vouchers
        </button>
        <button type="button" class="settings-tab" data-tab="affiliates">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Afiliados
        </button>
        <button type="button" class="settings-tab" data-tab="seo">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            SEO
        </button>
        <button type="button" class="settings-tab" data-tab="appearance">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>
            Aparência
        </button>
    </div>

    <!-- Tab: Geral -->
    <div class="settings-panel active" id="tab-general">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                </div>
                <div>
                    <h3>Configurações Gerais</h3>
                    <p class="admin-card-subtitle">Informações básicas do site</p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Nome do Site</label><input type="text" name="site_name" class="form-control" value="<?= e($settings['general']['site_name']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>URL do Site</label><input type="url" name="site_url" class="form-control" value="<?= e($settings['general']['site_url']['setting_value'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Email do Administrador</label><input type="email" name="admin_email" class="form-control" value="<?= e($settings['general']['admin_email']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Fuso Horário</label><input type="text" name="timezone" class="form-control" value="<?= e($settings['general']['timezone']['setting_value'] ?? 'America/Santo_Domingo') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Moeda</label><input type="text" name="currency" class="form-control" value="<?= e($settings['general']['currency']['setting_value'] ?? 'USD') ?>"></div>
                <div class="form-group col-6"><label>Símbolo</label><input type="text" name="currency_symbol" class="form-control" value="<?= e($settings['general']['currency_symbol']['setting_value'] ?? '$') ?>"></div>
            </div>
            <div class="form-group">
                <label>Logo</label>
                <div class="file-upload-area">
                    <input type="file" name="site_logo" id="siteLogo" class="file-input-hidden" accept="image/*">
                    <label for="siteLogo" class="file-upload-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <span>Escolher imagem</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: SMTP / Email -->
    <div class="settings-panel" id="tab-email">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <h3>Configurações de E-mail</h3>
                    <p class="admin-card-subtitle">SMTP e envio de mensagens</p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Host SMTP</label><input type="text" name="smtp_host" class="form-control" value="<?= e($settings['email']['smtp_host']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Porta</label><input type="number" name="smtp_port" class="form-control" value="<?= e($settings['email']['smtp_port']['setting_value'] ?? '587') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Usuário</label><input type="text" name="smtp_username" class="form-control" value="<?= e($settings['email']['smtp_username']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Senha</label><input type="password" name="smtp_password" class="form-control" value="<?= e($settings['email']['smtp_password']['setting_value'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Encryption</label>
                    <select name="smtp_encryption" class="form-control">
                        <option value="tls" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="none" <?= ($settings['email']['smtp_encryption']['setting_value'] ?? '') === 'none' ? 'selected' : '' ?>>Nenhuma</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Email Remetente</label><input type="email" name="mail_from_email" class="form-control" value="<?= e($settings['email']['mail_from_email']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Nome Remetente</label><input type="text" name="mail_from_name" class="form-control" value="<?= e($settings['email']['mail_from_name']['setting_value'] ?? '') ?>"></div>
            </div>

            <div style="margin-top:28px;padding-top:20px;border-top:1px solid #e5e7eb;">
                <h4 style="font-size:14px;font-weight:600;margin-bottom:12px;">Testar Email</h4>
                <div style="display:flex;gap:10px;align-items:center;">
                    <input type="email" id="testEmailInput" class="form-control" placeholder="Digite o email para teste..." style="max-width:300px;">
                    <button type="button" class="btn btn-outline" onclick="sendTestEmail()">Enviar Teste</button>
                </div>
                <small style="display:block;margin-top:8px;color:#6b7280;">Salve as configurações antes de testar.</small>
            </div>
        </div>
    </div>

    <!-- Tab: Pagamentos -->
    <div class="settings-panel" id="tab-payments">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-orange">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div><h3>Pagamentos</h3><p class="admin-card-subtitle">Gateways e configurações de cobrança</p></div>
            </div>
            <h4 class="settings-section-title">PayPal</h4>
            <div class="form-group"><label><input type="checkbox" name="paypal_enabled" value="1" <?= ($settings['payments']['paypal_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar PayPal</label></div>
            <div class="form-row">
                <div class="form-group col-6"><label>Client ID</label><input type="text" name="paypal_client_id" class="form-control" value="<?= e($settings['payments']['paypal_client_id']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Secret Key</label><input type="password" name="paypal_secret" class="form-control" value="<?= e($settings['payments']['paypal_secret']['setting_value'] ?? '') ?>"></div>
            </div>
            <div class="form-group"><label>Modo</label><select name="paypal_mode" class="form-control" style="max-width:200px;"><option value="sandbox" <?= ($settings['payments']['paypal_mode']['setting_value'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option><option value="production" <?= ($settings['payments']['paypal_mode']['setting_value'] ?? '') === 'production' ? 'selected' : '' ?>>Produção</option></select></div>

            <h4 class="settings-section-title">Stripe</h4>
            <div class="form-group"><label><input type="checkbox" name="stripe_enabled" value="1" <?= ($settings['payments']['stripe_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Stripe</label></div>
            <div class="form-row">
                <div class="form-group col-6"><label>Publishable Key</label><input type="text" name="stripe_publishable_key" class="form-control" value="<?= e($settings['payments']['stripe_publishable_key']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Secret Key</label><input type="password" name="stripe_secret_key" class="form-control" value="<?= e($settings['payments']['stripe_secret_key']['setting_value'] ?? '') ?>"></div>
            </div>

            <h4 class="settings-section-title">PIX (PagBank)</h4>
            <div class="form-group"><label><input type="checkbox" name="pagbank_enabled" value="1" <?= ($settings['payments']['pagbank_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar PIX via PagBank</label></div>
            <div class="form-row">
                <div class="form-group col-6"><label>Token da API</label><input type="password" name="pagbank_token" class="form-control" value="<?= e($settings['payments']['pagbank_token']['setting_value'] ?? '') ?>"></div>
                <div class="form-group col-6"><label>Modo</label><select name="pagbank_mode" class="form-control"><option value="sandbox" <?= ($settings['payments']['pagbank_mode']['setting_value'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option><option value="production" <?= ($settings['payments']['pagbank_mode']['setting_value'] ?? '') === 'production' ? 'selected' : '' ?>>Produção</option></select></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Taxa USD → BRL</label><input type="number" step="0.01" name="pagbank_usd_brl_rate" class="form-control" value="<?= e($settings['payments']['pagbank_usd_brl_rate']['setting_value'] ?? '5.50') ?>" style="max-width:120px;"></div>
            </div>

            <h4 class="settings-section-title">Pagamento Parcial</h4>
            <div class="form-group"><label><input type="checkbox" name="partial_payment_enabled" value="1" <?= ($settings['payments']['partial_payment_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Pagamento Parcial</label></div>
            <div class="form-group"><label>Percentual de Depósito (%)</label><input type="number" name="partial_payment_percent" class="form-control" value="<?= e($settings['payments']['partial_payment_percent']['setting_value'] ?? '50') ?>" min="1" max="99" style="max-width:120px;"></div>
        </div>
    </div>

    <!-- Tab: WhatsApp -->
    <div class="settings-panel" id="tab-whatsapp">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                </div>
                <div><h3>WhatsApp</h3><p class="admin-card-subtitle">Notificações via WhatsApp</p></div>
            </div>
            <div class="form-group"><label><input type="checkbox" name="whatsapp_enabled" value="1" <?= ($settings['whatsapp']['whatsapp_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Notificações WhatsApp</label></div>
            <div class="form-group"><label>URL do Webhook</label><input type="url" name="whatsapp_webhook_url" class="form-control" value="<?= e($settings['whatsapp']['whatsapp_webhook_url']['setting_value'] ?? '') ?>"></div>
            <div class="form-group"><label>Template - Passeio</label><textarea name="whatsapp_trip_template" class="form-control" rows="4"><?= e($settings['whatsapp']['whatsapp_trip_template']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Template - Transfer</label><textarea name="whatsapp_transfer_template" class="form-control" rows="4"><?= e($settings['whatsapp']['whatsapp_transfer_template']['setting_value'] ?? '') ?></textarea></div>
        </div>
    </div>

    <!-- Tab: Vouchers -->
    <div class="settings-panel" id="tab-vouchers">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/></svg>
                </div>
                <div><h3>Vouchers</h3><p class="admin-card-subtitle">Configurações de geração de vouchers</p></div>
            </div>
            <div class="form-group"><label>Logo do Voucher</label>
                <div class="file-upload-area">
                    <input type="file" name="voucher_logo" id="voucherLogo" class="file-input-hidden" accept="image/*">
                    <label for="voucherLogo" class="file-upload-label"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span>Escolher imagem</span></label>
                </div>
            </div>
            <div class="form-group"><label>Texto de Rodapé</label><textarea name="voucher_footer_text" class="form-control" rows="2"><?= e($settings['vouchers']['voucher_footer_text']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Instruções Padrão</label><textarea name="voucher_instructions" class="form-control" rows="3"><?= e($settings['vouchers']['voucher_instructions']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Limpar vouchers após (dias)</label><input type="number" name="voucher_cleanup_days" class="form-control" value="<?= e($settings['vouchers']['voucher_cleanup_days']['setting_value'] ?? '90') ?>" style="max-width:120px;"></div>
        </div>
    </div>

    <!-- Tab: Afiliados -->
    <div class="settings-panel" id="tab-affiliates">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-orange">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
                </div>
                <div><h3>Programa de Afiliados</h3><p class="admin-card-subtitle">Comissões e configurações</p></div>
            </div>
            <div class="form-group"><label><input type="checkbox" name="affiliate_enabled" value="1" <?= ($settings['affiliates']['affiliate_enabled']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Ativar Programa de Afiliados</label></div>
            <div class="form-row">
                <div class="form-group col-6"><label>Comissão Padrão (%)</label><input type="number" name="affiliate_default_rate" class="form-control" value="<?= e($settings['affiliates']['affiliate_default_rate']['setting_value'] ?? '20') ?>" step="0.01"></div>
                <div class="form-group col-6"><label>Duração do Cookie (dias)</label><input type="number" name="affiliate_cookie_days" class="form-control" value="<?= e($settings['affiliates']['affiliate_cookie_days']['setting_value'] ?? '30') ?>"></div>
            </div>
            <div class="form-group"><label><input type="checkbox" name="affiliate_auto_approve" value="1" <?= ($settings['affiliates']['affiliate_auto_approve']['setting_value'] ?? '') === '1' ? 'checked' : '' ?>> Auto-aprovar novos afiliados</label></div>
        </div>
    </div>

    <!-- Tab: SEO -->
    <div class="settings-panel" id="tab-seo">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <div><h3>SEO e Analytics</h3><p class="admin-card-subtitle">Meta tags e scripts de rastreamento</p></div>
            </div>
            <div class="form-group"><label>Meta Title Padrão</label><input type="text" name="meta_title" class="form-control" value="<?= e($settings['seo']['meta_title']['setting_value'] ?? '') ?>"></div>
            <div class="form-group"><label>Meta Description Padrão</label><textarea name="meta_description" class="form-control" rows="2"><?= e($settings['seo']['meta_description']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Google Analytics ID</label><input type="text" name="google_analytics_id" class="form-control" value="<?= e($settings['seo']['google_analytics_id']['setting_value'] ?? '') ?>" placeholder="G-XXXXXXXXXX"></div>
            <div class="form-group"><label>Scripts do &lt;head&gt;</label><textarea name="head_scripts" class="form-control" rows="3"><?= e($settings['seo']['head_scripts']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Scripts do &lt;/body&gt;</label><textarea name="body_scripts" class="form-control" rows="3"><?= e($settings['seo']['body_scripts']['setting_value'] ?? '') ?></textarea></div>
        </div>
    </div>

    <!-- Tab: Aparência -->
    <div class="settings-panel" id="tab-appearance">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>
                </div>
                <div><h3>Aparência</h3><p class="admin-card-subtitle">Cores, fontes e customizações visuais</p></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Cor Primária</label><input type="color" name="color_primary" class="form-control form-control-color" value="<?= e($settings['appearance']['color_primary']['setting_value'] ?? '#0077b6') ?>"></div>
                <div class="form-group col-6"><label>Cor Secundária</label><input type="color" name="color_secondary" class="form-control form-control-color" value="<?= e($settings['appearance']['color_secondary']['setting_value'] ?? '#00b4d8') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-6"><label>Cor Accent</label><input type="color" name="color_accent" class="form-control form-control-color" value="<?= e($settings['appearance']['color_accent']['setting_value'] ?? '#f77f00') ?>"></div>
                <div class="form-group col-6"><label>Fonte Principal</label><input type="text" name="font_primary" class="form-control" value="<?= e($settings['appearance']['font_primary']['setting_value'] ?? 'Poppins') ?>"></div>
            </div>
            <div class="form-group"><label>CSS Customizado</label><textarea name="custom_css" class="form-control" rows="5"><?= e($settings['appearance']['custom_css']['setting_value'] ?? '') ?></textarea></div>
            <div class="form-row">
                <div class="form-group col-6"><label>WhatsApp (número botão flutuante)</label><input type="text" name="whatsapp_float_number" class="form-control" value="<?= e($settings['appearance']['whatsapp_float_number']['setting_value'] ?? '') ?>" placeholder="5511999999999"></div>
                <div class="form-group col-6"><label>Texto do Botão WhatsApp</label><input type="text" name="whatsapp_float_text" class="form-control" value="<?= e($settings['appearance']['whatsapp_float_text']['setting_value'] ?? 'Fale conosco!') ?>"></div>
            </div>
        </div>
    </div>

    <!-- Botão Salvar -->
    <div class="settings-save-bar">
        <button type="submit" class="btn btn-primary btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Salvar Configurações
        </button>
    </div>
</form>

<script>
// Settings tabs
document.querySelectorAll('.settings-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
});

function sendTestEmail() {
    const email = document.getElementById('testEmailInput').value;
    if (!email) { alert('Informe um email para teste'); return; }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/configuracoes/email-teste';
    form.innerHTML = '<input name="_token" value="<?= e(csrf_token()) ?>"><input name="test_email" value="'+email+'">';
    document.body.appendChild(form);
    form.submit();
}
</script>
