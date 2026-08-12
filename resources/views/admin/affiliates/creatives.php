<div class="card-header">
    <div class="header-actions">
        <a href="/admin/afiliados" class="btn btn-outline">&larr; Afiliados</a>
        <a href="/admin/afiliados/comissoes" class="btn btn-outline">Comissões</a>
    </div>
</div>

<!-- Formulário de Upload -->
<div class="admin-card">
    <h3>Enviar Novo Criativo</h3>
    <p class="admin-card-subtitle" style="margin-bottom:20px">Faça upload de banners e imagens promocionais que os afiliados poderão usar nas redes sociais.</p>

    <form method="POST" action="/admin/afiliados/criativos/criar" enctype="multipart/form-data" class="admin-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-3">
                <label>Título <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Ex: Banner Promo Verão 2026" required>
            </div>
            <div class="form-group col-2">
                <label>Tipo</label>
                <select name="type" class="form-control">
                    <option value="post">Post (Feed)</option>
                    <option value="story">Story</option>
                    <option value="banner">Banner</option>
                    <option value="video">Vídeo</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            <div class="form-group col-3">
                <label>Descrição (opcional)</label>
                <input type="text" name="description" class="form-control" placeholder="Breve descrição do material">
            </div>
            <div class="form-group col-2">
                <label>Imagem <span class="required">*</span></label>
                <div class="file-upload-area">
                    <input type="file" name="image" id="creativeImage" class="file-input-hidden" accept="image/*,video/*,.pdf,.doc,.docx,.psd,.ai,.zip" required>
                    <label for="creativeImage" class="file-upload-label">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span id="creativeFileName">Escolher arquivo</span>
                    </label>
                    <small class="form-hint">Imagem, vídeo, PDF ou outros formatos.</small>
                </div>
            </div>
            <div class="form-group col-2" style="display:flex;align-items:flex-end;">
                <button type="submit" class="btn btn-primary">Enviar Criativo</button>
            </div>
        </div>
    </form>
</div>

<!-- Lista de Criativos -->
<div class="admin-card">
    <h3 style="margin-bottom:20px">Criativos Disponíveis (<?= count($creatives) ?>)</h3>

    <?php if (empty($creatives)): ?>
    <p style="text-align:center;padding:30px;color:#94a3b8;">Nenhum criativo enviado ainda.</p>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));gap:20px;">
        <?php foreach ($creatives as $creative): ?>
        <div style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;">
            <div style="height:160px;overflow:hidden;background:#f8fafc;">
                <img src="<?= e($creative['image_url']) ?>" alt="<?= e($creative['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div style="padding:14px;">
                <strong style="font-size:13px;color:#1e293b;display:block;margin-bottom:4px;"><?= e($creative['title']) ?></strong>
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                    <span class="badge badge-info"><?= e(ucfirst($creative['type'] ?? 'post')) ?></span>
                    <?php if (!empty($creative['dimensions'])): ?>
                    <span style="font-size:11px;color:#94a3b8;"><?= e($creative['dimensions']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($creative['description'])): ?>
                <p style="font-size:12px;color:#64748b;margin-bottom:10px;"><?= e($creative['description']) ?></p>
                <?php endif; ?>
                <div style="display:flex;gap:8px;">
                    <a href="<?= e($creative['image_url']) ?>" target="_blank" class="btn btn-sm btn-outline">Ver</a>
                    <form method="POST" action="/admin/afiliados/criativos/<?= (int)$creative['id'] ?>/excluir" style="display:inline" onsubmit="return confirm('Excluir este criativo?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('creativeImage')?.addEventListener('change', function() {
    var label = document.getElementById('creativeFileName');
    if (this.files && this.files[0]) {
        label.textContent = this.files[0].name;
    } else {
        label.textContent = 'Escolher imagem';
    }
});
</script>
