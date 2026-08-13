<div class="card-header">
    <h2>Criar Reserva Manual</h2>
    <a href="/admin/reservas" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Reservas
    </a>
</div>

<form method="POST" action="/admin/reservas/criar" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Esquerda: Formulário -->
        <div>
            <!-- Dados do Cliente -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <h3>Dados do Cliente</h3>
                        <p class="admin-card-subtitle">Informações de contato do cliente</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Nome <span class="required">*</span></label>
                        <input type="text" name="billing_first_name" class="form-control" placeholder="Ex: João" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Sobrenome <span class="required">*</span></label>
                        <input type="text" name="billing_last_name" class="form-control" placeholder="Ex: Silva" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>E-mail <span class="required">*</span></label>
                        <input type="email" name="billing_email" class="form-control" placeholder="cliente@email.com" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Telefone / WhatsApp</label>
                        <input type="tel" name="billing_phone" class="form-control" placeholder="+55 11 99999-9999" data-phone-country>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>País</label>
                        <select name="billing_country" class="form-control">
                            <option value="BR">Brasil</option>
                            <option value="US">Estados Unidos</option>
                            <option value="PT">Portugal</option>
                            <option value="DO">República Dominicana</option>
                            <option value="AR">Argentina</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Detalhes da Reserva -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div>
                        <h3>Detalhes da Reserva</h3>
                        <p class="admin-card-subtitle">Valor e informações adicionais</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Valor Total (USD) <span class="required">*</span></label>
                        <input type="number" step="0.01" name="total" class="form-control" required min="0" placeholder="0.00">
                        <small class="form-hint">Valor em dólares americanos (USD).</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Observações Internas</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Informações adicionais, como detalhes do passeio, data, número de pessoas, etc."></textarea>
                    <small class="form-hint">Visível apenas para administradores.</small>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Resumo -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <h3>Resumo da Reserva</h3>
                </div>

                <div class="summary-card-body">
                    <div class="summary-row">
                        <span class="summary-row-label">Status</span>
                        <span class="badge badge-success badge-lg">Confirmado</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Pagamento</span>
                        <span class="summary-row-value">Manual</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Moeda</span>
                        <span class="summary-row-value">USD ($)</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-row-label">Criado por</span>
                        <span class="summary-row-value"><?= e(current_user()['first_name'] ?? 'Admin') ?></span>
                    </div>
                </div>

                <div class="summary-card-note">
                    <div class="summary-note-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <div>
                        <strong>Como funciona?</strong>
                        <p>A reserva será criada com status <strong>Confirmado</strong> e o pagamento será registrado automaticamente como completo. Nenhum e-mail será enviado ao cliente.</p>
                    </div>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Criar Reserva
                    </button>
                    <a href="/admin/reservas" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
