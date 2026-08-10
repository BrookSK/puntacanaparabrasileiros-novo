<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">&larr; Voltar</a>
    </div>
</div>

<div class="import-container">
    <div class="import-info">
        <h3>Importar Horários para: <?= e($trip['title']) ?></h3>
        <p class="text-muted">Faça upload de uma planilha Excel (.xlsx) ou CSV com os hotéis e horários de pickup.</p>
        <a href="/storage/templates/horarios_modelo.csv" download class="btn btn-sm btn-outline" style="margin-top: 0.5rem;">
            &#x2B07; Baixar Planilha Modelo (.csv)
        </a>
    </div>

    <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/importar" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-section">
            <div class="form-group">
                <label for="schedule_file" class="form-label">Arquivo da Planilha *</label>
                <input type="file" 
                       id="schedule_file" 
                       name="schedule_file" 
                       class="form-control" 
                       accept=".xlsx,.csv,.txt"
                       required>
                <small class="form-help">Formatos aceitos: .xlsx, .csv — Tamanho máximo: 5MB</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="clear_existing" value="1" checked>
                    <strong>Substituir dados existentes</strong>
                    <br><small class="text-muted">Se marcado, remove todos os hotéis/horários atuais antes de importar. Se desmarcado, adiciona aos existentes.</small>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Importar Planilha</button>
            <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>

    <div class="import-instructions">
        <h4>Formato Esperado da Planilha</h4>
        <p>A planilha deve ter o seguinte formato (a primeira coluna é o nome do hotel, as colunas seguintes são os horários):</p>

        <div class="example-table">
            <table class="admin-table">
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
                        <td>07:20</td>
                        <td>08:10</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Hard Rock Hotel</td>
                        <td>06:50</td>
                        <td>07:30</td>
                        <td>08:00</td>
                    </tr>
                    <tr>
                        <td>Dreams Royal Beach</td>
                        <td>09:00</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="format-notes">
            <h5>Observações:</h5>
            <ul>
                <li>A primeira linha pode ser um cabeçalho (será detectado automaticamente)</li>
                <li>Horários no formato <code>HH:MM</code> (ex: 07:20, 14:30)</li>
                <li>Se houver linhas repetidas do mesmo hotel, os horários serão agrupados</li>
                <li>Células vazias são ignoradas</li>
                <li>O sistema detecta automaticamente se o Excel armazena horários como número decimal</li>
            </ul>
        </div>

        <div class="format-alternative">
            <h5>Formato Alternativo (duas colunas):</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Hotel</th>
                        <th>Horário</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hotel Barceló</td>
                        <td>07:20</td>
                    </tr>
                    <tr>
                        <td>Hotel Barceló</td>
                        <td>08:10</td>
                    </tr>
                    <tr>
                        <td>Hard Rock Hotel</td>
                        <td>06:50</td>
                    </tr>
                </tbody>
            </table>
            <small class="text-muted">Neste formato, hotéis repetidos terão seus horários agrupados automaticamente.</small>
        </div>
    </div>
</div>

<style>
.import-container {
    max-width: 800px;
}
.import-info {
    margin-bottom: 2rem;
}
.import-info h3 {
    margin-bottom: 0.5rem;
}
.import-instructions {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}
.import-instructions h4 {
    margin-bottom: 0.75rem;
    color: #1e293b;
}
.import-instructions h5 {
    margin: 1.5rem 0 0.5rem;
    color: #334155;
}
.example-table {
    margin: 1rem 0;
    overflow-x: auto;
}
.example-table table {
    font-size: 0.875rem;
}
.format-notes ul {
    padding-left: 1.25rem;
    color: #475569;
}
.format-notes li {
    margin-bottom: 0.375rem;
}
.format-notes code {
    background: #f1f5f9;
    padding: 0.125rem 0.375rem;
    border-radius: 3px;
    font-size: 0.8rem;
}
.format-alternative {
    margin-top: 1.5rem;
}
.format-alternative table {
    font-size: 0.875rem;
    max-width: 350px;
}
</style>
