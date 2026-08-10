<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">&larr; Voltar</a>
    </div>
</div>

<div style="max-width:820px;">
    <!-- Info do passeio -->
    <div style="margin-bottom:2rem;padding:1.25rem;background:var(--bg-card, #1e293b);border-radius:10px;border:1px solid var(--border, rgba(255,255,255,0.06));">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0ea5e9;">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <h3 style="margin:0;font-size:1.1rem;color:var(--text-primary, #f1f5f9);">Importar Horários</h3>
        </div>
        <p style="margin:0;font-size:0.9rem;color:var(--text-muted, #94a3b8);">
            Passeio: <strong style="color:var(--text-primary, #f1f5f9);"><?= e($trip['title']) ?></strong>
        </p>
    </div>

    <!-- Formulário de upload -->
    <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/importar" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div style="margin-bottom:1.5rem;">
            <label for="schedule_file" class="form-label">Arquivo da Planilha</label>
            <div class="upload-area" id="uploadArea">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted, #94a3b8);margin-bottom:0.5rem;">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                <p class="upload-area__text">Arraste o arquivo aqui ou <strong>clique para selecionar</strong></p>
                <p class="upload-area__hint">.xlsx ou .csv — Máx. 5MB</p>
                <input type="file" 
                       id="schedule_file" 
                       name="schedule_file" 
                       accept=".xlsx,.csv,.txt"
                       required
                       style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                <span class="upload-area__filename" id="fileName"></span>
            </div>
        </div>

        <div style="margin-bottom:2rem;">
            <label class="checkbox-label" style="display:flex;align-items:flex-start;gap:0.5rem;cursor:pointer;">
                <input type="checkbox" name="clear_existing" value="1" checked style="margin-top:3px;">
                <div>
                    <strong style="color:var(--text-primary, #f1f5f9);">Substituir dados existentes</strong>
                    <br><span style="font-size:0.8rem;color:var(--text-muted, #94a3b8);">Remove todos os hotéis/horários atuais antes de importar. Se desmarcado, adiciona aos existentes.</span>
                </div>
            </label>
        </div>

        <div style="display:flex;gap:0.75rem;align-items:center;">
            <button type="submit" class="btn btn-primary">Importar Planilha</button>
            <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">Cancelar</a>
            <a href="/storage/templates/horarios_modelo.csv" download class="btn btn-sm btn-outline" style="margin-left:auto;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Baixar Modelo CSV
            </a>
        </div>
    </form>

    <!-- Instruções -->
    <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--border, rgba(255,255,255,0.06));">
        <h4 style="margin-bottom:1rem;color:var(--text-primary, #f1f5f9);font-size:1rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;opacity:0.7;">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            Formato Esperado
        </h4>
        
        <div class="format-example">
            <p style="font-size:0.85rem;color:var(--text-muted, #94a3b8);margin-bottom:0.75rem;">
                A primeira coluna é o nome do hotel. As colunas seguintes são os horários de pickup:
            </p>
            <table class="table" style="font-size:0.82rem;">
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
                        <td><code style="color:#38bdf8;">07:20</code></td>
                        <td><code style="color:#38bdf8;">08:10</code></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Hard Rock Hotel & Casino</td>
                        <td><code style="color:#38bdf8;">06:50</code></td>
                        <td><code style="color:#38bdf8;">07:30</code></td>
                        <td><code style="color:#38bdf8;">08:00</code></td>
                    </tr>
                    <tr>
                        <td>Dreams Royal Beach</td>
                        <td><code style="color:#38bdf8;">09:00</code></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top:1.25rem;padding:1rem;background:rgba(14,165,233,0.06);border:1px solid rgba(14,165,233,0.12);border-radius:8px;">
            <p style="margin:0 0 0.5rem;font-size:0.85rem;color:var(--text-primary, #f1f5f9);font-weight:600;">Dicas:</p>
            <ul style="margin:0;padding-left:1.25rem;font-size:0.82rem;color:var(--text-muted, #94a3b8);line-height:1.7;">
                <li>A primeira linha pode ser cabeçalho (detectado automaticamente)</li>
                <li>Horários no formato <code style="color:#38bdf8;">HH:MM</code></li>
                <li>Linhas repetidas do mesmo hotel são agrupadas</li>
                <li>Células vazias são ignoradas</li>
                <li>Suporta valores decimais do Excel (ex: 0.305 = 07:20)</li>
            </ul>
        </div>
    </div>
</div>

<style>
.upload-area {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    border: 2px dashed var(--border, rgba(255,255,255,0.12));
    border-radius: 10px;
    background: var(--bg-card, #1e293b);
    text-align: center;
    transition: border-color 0.2s, background 0.2s;
    cursor: pointer;
}
.upload-area:hover,
.upload-area.dragover {
    border-color: #0ea5e9;
    background: rgba(14, 165, 233, 0.04);
}
.upload-area__text {
    margin: 0;
    font-size: 0.9rem;
    color: var(--text-primary, #f1f5f9);
}
.upload-area__hint {
    margin: 0.25rem 0 0;
    font-size: 0.8rem;
    color: var(--text-muted, #94a3b8);
}
.upload-area__filename {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #0ea5e9;
    font-weight: 600;
}
.format-example code {
    background: rgba(14, 165, 233, 0.1);
    padding: 0.1rem 0.35rem;
    border-radius: 3px;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.8rem;
}
</style>

<script>
// Nome do arquivo selecionado
document.getElementById('schedule_file').addEventListener('change', function() {
    const name = this.files[0] ? this.files[0].name : '';
    document.getElementById('fileName').textContent = name;
});

// Drag & drop visual
const area = document.getElementById('uploadArea');
['dragenter', 'dragover'].forEach(e => {
    area.addEventListener(e, () => area.classList.add('dragover'));
});
['dragleave', 'drop'].forEach(e => {
    area.addEventListener(e, () => area.classList.remove('dragover'));
});
</script>
