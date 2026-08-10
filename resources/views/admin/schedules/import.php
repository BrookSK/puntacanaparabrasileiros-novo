<div class="card-header">
    <h2>Importar Horários: <?= e($trip['title']) ?></h2>
    <a href="/admin/passeios/<?= (int) $trip['id'] ?>/editar" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para o Passeio
    </a>
</div>

<div style="max-width:780px;">
    <form method="POST" action="/admin/passeios/<?= (int) $trip['id'] ?>/horarios/importar" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <div>
                    <h3>Upload da Planilha</h3>
                    <p class="admin-card-subtitle">Selecione um arquivo .xlsx ou .csv</p>
                </div>
            </div>

            <div class="form-group">
                <label>Arquivo <span class="required">*</span></label>
                <input type="file" name="schedule_file" class="form-control" accept=".xlsx,.csv,.txt" required>
                <small style="color:#94a3b8;font-size:11px;">Formatos: .xlsx, .csv — Máximo: 5MB</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="clear_existing" value="1" checked>
                    Substituir dados existentes (remove hotéis/horários atuais antes de importar)
                </label>
            </div>

            <div style="display:flex;gap:10px;align-items:center;">
                <button type="submit" class="btn btn-primary">Importar</button>
                <a href="/admin/passeios/<?= (int) $trip['id'] ?>/editar" class="btn btn-outline">Cancelar</a>
                <a href="/storage/templates/horarios_modelo.csv" download class="btn btn-sm btn-outline" style="margin-left:auto;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Baixar Modelo
                </a>
            </div>
        </div>
    </form>

    <!-- Instruções -->
    <div class="admin-card" style="margin-top:20px;">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div>
                <h3>Formato Esperado</h3>
                <p class="admin-card-subtitle">A planilha deve seguir este formato</p>
            </div>
        </div>

        <table class="table" style="font-size:13px;margin-bottom:16px;">
            <thead>
                <tr>
                    <th>Hotel</th>
                    <th>Horário 1</th>
                    <th>Horário 2</th>
                    <th>Horário 3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hotel Barceló Bávaro Palace</td>
                    <td><code>07:20</code></td>
                    <td><code>08:10</code></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Hard Rock Hotel & Casino</td>
                    <td><code>06:50</code></td>
                    <td><code>07:30</code></td>
                    <td><code>08:00</code></td>
                </tr>
                <tr>
                    <td>Dreams Royal Beach</td>
                    <td><code>09:00</code></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div style="font-size:12px;color:#64748b;line-height:1.8;">
            <strong>Dicas:</strong>
            <ul style="margin:4px 0 0;padding-left:18px;">
                <li>Primeira linha pode ser cabeçalho (detectado automaticamente)</li>
                <li>Horários no formato HH:MM</li>
                <li>Linhas repetidas do mesmo hotel são agrupadas</li>
                <li>Células vazias são ignoradas</li>
            </ul>
        </div>
    </div>
</div>
