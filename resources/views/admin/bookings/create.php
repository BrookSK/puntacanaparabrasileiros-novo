<div class="card-header">
    <h2>Criar Reserva Manual</h2>
    <a href="/admin/reservas" class="btn btn-sm btn-outline">&larr; Voltar</a>
</div>

<div class="admin-card">
    <form method="POST" action="/admin/reservas/criar" class="admin-form">
        <?= csrf_field() ?>

        <fieldset class="form-section">
            <legend>Dados do Cliente</legend>
            <div class="form-row">
                <div class="form-group col-6">
                    <label>Nome *</label>
                    <input type="text" name="billing_first_name" class="form-control" required>
                </div>
                <div class="form-group col-6">
                    <label>Sobrenome *</label>
                    <input type="text" name="billing_last_name" class="form-control" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-4">
                    <label>E-mail *</label>
                    <input type="email" name="billing_email" class="form-control" required>
                </div>
                <div class="form-group col-4">
                    <label>Telefone</label>
                    <input type="text" name="billing_phone" class="form-control">
                </div>
                <div class="form-group col-4">
                    <label>País</label>
                    <input type="text" name="billing_country" class="form-control" value="BR">
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Valor</legend>
            <div class="form-row">
                <div class="form-group col-4">
                    <label>Total (USD) *</label>
                    <input type="number" step="0.01" name="total" class="form-control" required min="0">
                </div>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Observações</legend>
            <div class="form-group">
                <textarea name="notes" class="form-control" rows="3" placeholder="Notas internas sobre esta reserva..."></textarea>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Criar Reserva</button>
            <a href="/admin/reservas" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
