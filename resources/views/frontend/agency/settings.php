<?php
$success = $session->getFlash('success');
?>
<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'configuracoes']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Configurações</h1>
                        <p class="aff-page-subtitle">Dados de contato e pagamento da sua agência</p>
                    </div>
                </div>

                <?php if ($success): ?>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:14px;"><?= e($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="/painel-agencia/configuracoes">
                    <?= csrf_field() ?>

                    <div class="aff-card">
                        <h3 class="aff-card-title">Dados da Agência</h3>
                        <div class="aff-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:14px;">
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Razão Social</label>
                                <input type="text" value="<?= e($agency['company_name']) ?>" class="form-control" readonly style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafb;color:#94a3b8;">
                                <small style="color:#94a3b8;">A razão social não pode ser alterada aqui. Fale com o suporte.</small>
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Nome Fantasia</label>
                                <input type="text" name="trade_name" value="<?= e($agency['trade_name'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Nome do Contato</label>
                                <input type="text" name="contact_name" value="<?= e($agency['contact_name'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">WhatsApp/Telefone</label>
                                <input type="text" name="phone" value="<?= e($agency['phone'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">E-mail</label>
                                <input type="email" name="email" value="<?= e($agency['email'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">Cidade</label>
                                <input type="text" name="city" value="<?= e($agency['city'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                            <div class="form-group">
                                <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px;">País</label>
                                <input type="text" name="country" value="<?= e($agency['country'] ?? '') ?>" class="form-control" style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;">
                            </div>
                        </div>
                    </div>

                    <div class="aff-card">
                        <h3 class="aff-card-title">Dados para Pagamento</h3>
                        <p class="aff-card-desc">Informe seus dados bancários ou chave PIX para recebimento das comissões.</p>
                        <div class="form-group">
                            <textarea name="bank_info" rows="4" class="form-control" placeholder="Ex: Banco, agência, conta, chave PIX..." style="width:100%;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;resize:vertical;"><?= e($agency['bank_info'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">Salvar Alterações</button>
                </form>
            </main>
        </div>
    </div>
</section>
