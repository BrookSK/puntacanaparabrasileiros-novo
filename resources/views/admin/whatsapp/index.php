<div class="admin-page-header">
    <h2>WhatsApp — Instâncias</h2>
    <button class="btn btn-primary" onclick="openNewInstanceModal()">+ Nova Instância</button>
</div>

<div class="instances-grid">
    <?php if (empty($instances)): ?>
    <div class="empty-state">
        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
        <p>Nenhuma instância configurada.</p>
        <button class="btn btn-primary" onclick="openNewInstanceModal()">Criar primeira instância</button>
    </div>
    <?php else: ?>
    <?php foreach ($instances as $inst): ?>
    <div class="instance-card" id="instance-<?= $inst['id'] ?>">
        <div class="instance-header">
            <div class="instance-name">
                <strong><?= e($inst['display_name'] ?: $inst['instance_name']) ?></strong>
                <small class="instance-technical"><?= e($inst['instance_name']) ?></small>
            </div>
            <div class="instance-badges">
                <?php
                $statusClass = match($inst['connection_status']) {
                    'open', 'connected' => 'badge-success',
                    'connecting' => 'badge-warning',
                    default => 'badge-secondary',
                };
                $statusLabel = match($inst['connection_status']) {
                    'open', 'connected' => 'Conectado',
                    'connecting' => 'Conectando...',
                    default => 'Desconectado',
                };
                ?>
                <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                <?php if ($inst['is_default']): ?>
                <span class="badge badge-info">Padrão</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="instance-info">
            <?php if ($inst['owner_phone']): ?>
            <p><small>📱 <?= e($inst['owner_phone']) ?></small></p>
            <?php endif; ?>
            <p><small>👤 <?= $inst['user_name'] ? e($inst['user_name'] . ' ' . $inst['user_last_name']) : 'Sem usuário vinculado' ?></small></p>
        </div>

        <div class="instance-qr" id="qr-<?= $inst['id'] ?>" style="display:none;">
            <img src="" alt="QR Code" id="qr-img-<?= $inst['id'] ?>">
        </div>

        <div class="instance-actions">
            <?php if ($inst['connection_status'] !== 'open' && $inst['connection_status'] !== 'connected'): ?>
            <button class="btn btn-sm btn-success" onclick="connectInstance(<?= $inst['id'] ?>)">Conectar</button>
            <?php endif; ?>

            <?php if ($inst['connection_status'] === 'open' || $inst['connection_status'] === 'connected'): ?>
            <?php if (is_superadmin()): ?>
            <button class="btn btn-sm btn-danger" onclick="disconnectInstance(<?= $inst['id'] ?>)">Desconectar</button>
            <?php endif; ?>
            <?php endif; ?>

            <button class="btn btn-sm btn-outline" onclick="refreshStatus(<?= $inst['id'] ?>)" title="Atualizar status">↻</button>

            <?php if (is_superadmin()): ?>
            <button class="btn btn-sm btn-outline" onclick="editInstance(<?= $inst['id'] ?>, '<?= e($inst['display_name']) ?>', '<?= e($inst['api_url']) ?>', '<?= e($inst['api_key']) ?>', '<?= e($inst['user_id'] ?? '') ?>')" title="Editar">✏️</button>
            <?php if (!$inst['is_default']): ?>
            <button class="btn btn-sm btn-outline" onclick="setDefault(<?= $inst['id'] ?>)" title="Definir como padrão">⭐</button>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline" onclick="registerWebhook(<?= $inst['id'] ?>)" title="Re-registrar webhook">🔗</button>
            <button class="btn btn-sm btn-danger-outline" onclick="deleteInstance(<?= $inst['id'] ?>)" title="Excluir">🗑️</button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Nova Instância -->
<div class="modal-overlay" id="modal-new-instance" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Nova Instância</h3>
            <button class="modal-close" onclick="closeModal('modal-new-instance')">&times;</button>
        </div>
        <form id="form-new-instance" onsubmit="return createInstance(event)">
            <div class="form-group">
                <label>Nome da instância *</label>
                <input type="text" name="instance_name" class="form-control" placeholder="minha-empresa (sem espaços)" required pattern="[a-z0-9\-]+">
                <small>Minúsculas, sem espaços ou caracteres especiais</small>
            </div>
            <div class="form-group">
                <label>Nome de exibição</label>
                <input type="text" name="display_name" class="form-control" placeholder="WhatsApp Empresa">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="use_default_credentials" value="1" checked onchange="toggleCredentials(this)">
                    Usar URL e API Key da instância padrão
                </label>
            </div>
            <div id="custom-credentials" style="display:none;">
                <div class="form-group">
                    <label>URL da Evolution API</label>
                    <input type="url" name="api_url" class="form-control" placeholder="https://evo.exemplo.com">
                </div>
                <div class="form-group">
                    <label>API Key</label>
                    <input type="text" name="api_key" class="form-control" placeholder="sua-api-key">
                </div>
            </div>
            <div class="form-group">
                <label>Vincular ao usuário (opcional)</label>
                <select name="user_id" class="form-control">
                    <option value="">Sem vínculo (disponível para todos)</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= e($u['first_name'] . ' ' . $u['last_name']) ?> (<?= e($u['role']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-new-instance')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Instância</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Instância -->
<div class="modal-overlay" id="modal-edit-instance" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Editar Instância</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-instance')">&times;</button>
        </div>
        <form id="form-edit-instance" onsubmit="return updateInstance(event)">
            <input type="hidden" name="id" id="edit-instance-id">
            <div class="form-group">
                <label>Nome de exibição</label>
                <input type="text" name="display_name" id="edit-display-name" class="form-control">
            </div>
            <div class="form-group">
                <label>URL da Evolution API</label>
                <input type="url" name="api_url" id="edit-api-url" class="form-control">
            </div>
            <div class="form-group">
                <label>API Key</label>
                <input type="text" name="api_key" id="edit-api-key" class="form-control">
            </div>
            <div class="form-group">
                <label>Vincular ao usuário</label>
                <select name="user_id" id="edit-user-id" class="form-control">
                    <option value="">Sem vínculo</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= e($u['first_name'] . ' ' . $u['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-instance')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function openNewInstanceModal() {
    document.getElementById('modal-new-instance').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
function toggleCredentials(cb) {
    document.getElementById('custom-credentials').style.display = cb.checked ? 'none' : 'block';
}

async function createInstance(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    const data = Object.fromEntries(form);
    data.use_default_credentials = data.use_default_credentials ? '1' : '0';
    const res = await fetch('/whatsapp/createInstance', {
        method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify(data)
    });
    const json = await res.json();
    if (json.success) { location.reload(); } else { alert(json.error || 'Erro'); }
    return false;
}

async function connectInstance(id) {
    const res = await fetch(`/whatsapp/connect/${id}`);
    const json = await res.json();
    if (json.qrcode) {
        document.getElementById(`qr-${id}`).style.display = 'block';
        document.getElementById(`qr-img-${id}`).src = 'data:image/png;base64,' + json.qrcode;
    } else if (json.connected) {
        location.reload();
    } else { alert(json.error || 'Erro ao conectar'); }
}

async function disconnectInstance(id) {
    if (!confirm('Desconectar esta instância?')) return;
    await fetch(`/whatsapp/disconnect/${id}`, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}});
    location.reload();
}

async function refreshStatus(id) {
    const res = await fetch(`/whatsapp/status/${id}`);
    const json = await res.json();
    location.reload();
}

async function setDefault(id) {
    await fetch(`/whatsapp/setDefault/${id}`, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}});
    location.reload();
}

function editInstance(id, name, url, key, userId) {
    document.getElementById('edit-instance-id').value = id;
    document.getElementById('edit-display-name').value = name;
    document.getElementById('edit-api-url').value = url;
    document.getElementById('edit-api-key').value = key;
    document.getElementById('edit-user-id').value = userId || '';
    document.getElementById('modal-edit-instance').style.display = 'flex';
}

async function updateInstance(e) {
    e.preventDefault();
    const form = new FormData(e.target);
    const id = form.get('id');
    const data = Object.fromEntries(form);
    delete data.id;
    await fetch(`/whatsapp/updateInstance/${id}`, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify(data)
    });
    location.reload();
    return false;
}

async function deleteInstance(id) {
    if (!confirm('Excluir esta instância permanentemente? Todos os contatos e mensagens serão removidos.')) return;
    await fetch(`/whatsapp/deleteInstance/${id}`, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}});
    location.reload();
}

async function registerWebhook(id) {
    const res = await fetch(`/whatsapp/registerWebhookEvents/${id}`, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}});
    const json = await res.json();
    alert(json.success ? 'Webhook registrado!' : 'Erro ao registrar webhook.');
}
</script>
