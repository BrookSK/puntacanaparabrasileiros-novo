<div class="card-header">
    <h2>Criar Reserva Manual</h2>
    <a href="/admin/reservas" class="btn btn-sm btn-outline">&larr; Voltar</a>
</div>

<form method="POST" action="/admin/reservas/criar" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Esquerda: Formulário -->
        <div>
            <!-- Dados do Cliente -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <h3>Dados do Cliente</h3>
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
                        <input type="text" name="billing_phone" class="form-control" placeholder="+55 11 99999-9999">
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
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <h3>Detalhes da Reserva</h3>
                </div>

                <div class="form-row">
                    <div class="form-group col-4">
                        <label>Valor Total (USD) <span class="required">*</span></label>
                        <div class="input-prefix-wrapper">
                            <span class="input-prefix">$</span>
                            <input type="number" step="0.01" name="total" class="form-control input-with-prefix" required min="0" placeholder="0.00">
                        </div>
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
            <div class="admin-card admin-card-sticky">
                <div class="admin-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <h3>Resumo</h3>
                </div>

                <div class="booking-summary-info">
                    <div class="summary-item">
                        <span class="summary-label">Status:</span>
                        <span class="badge badge-success">Confirmado</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Pagamento:</span>
                        <span>Manual (Registrado como pago)</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Moeda:</span>
                        <span>USD (Dólar Americano)</span>
                    </div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <p>A reserva será criada com status <strong>Confirmado</strong> e o pagamento será registrado automaticamente como <strong>completo</strong>.</p>
                </div>

                <div class="summary-divider"></div>

                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Criar Reserva
                </button>
                <a href="/admin/reservas" class="btn btn-outline btn-block" style="margin-top:8px;">Cancelar</a>
            </div>
        </div>
    </div>
</form>
