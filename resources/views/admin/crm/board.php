<div class="admin-page-header">
    <div>
        <a href="/crm" class="btn btn-outline btn-sm">← Boards</a>
        <h2 style="display:inline;margin-left:12px;"><?= e($board['name']) ?></h2>
        <?php if ($board['description']): ?>
        <small style="margin-left:8px;color:#666;"><?= e($board['description']) ?></small>
        <?php endif; ?>
    </div>
    <div>
        <button class="btn btn-outline btn-sm" onclick="document.getElementById('modal-new-column').style.display='flex'">+ Coluna</button>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-new-card').style.display='flex'">+ Card</button>
    </div>
</div>

<div class="kanban-wrapper" id="kanbanWrapper">
    <?php foreach ($board['columns'] as $col): ?>
    <div class="kanban-column" data-column-id="<?= $col['id'] ?>">
        <div class="kanban-column-header">
            <span class="kanban-dot" style="background:<?= e($col['color']) ?>"></span>
            <strong><?= e($col['name']) ?></strong>
            <span class="badge badge-sm"><?= count($col['cards']) ?></span>
            <div class="kanban-col-menu">
                <button class="btn-icon" onclick="toggleColMenu(this)">⋮</button>
                <div class="kanban-col-dropdown" style="display:none;">
                    <button onclick="renameColumn(<?= $col['id'] ?>, '<?= e($col['name']) ?>')">Renomear</button>
                    <?php if (is_superadmin()): ?>
                    <button class="text-danger" onclick="deleteColumn(<?= $col['id'] ?>)">Excluir</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="kanban-cards" data-column-id="<?= $col['id'] ?>">
            <?php foreach ($col['cards'] as $card): ?>
            <div class="kanban-card" data-card-id="<?= $card['id'] ?>" draggable="true" onclick="openCardDetail(<?= $card['id'] ?>)">
                <?php if ($card['lead_temperature'] ?? null): ?>
                <span class="temp-dot temp-<?= e($card['lead_temperature']) ?>"></span>
                <?php endif; ?>
                <div class="kanban-card-title"><?= e($card['title']) ?></div>
                <div class="kanban-card-meta">
                    <?php if ($card['label_name'] ?? null): ?>
                    <span class="badge-mini" style="background:<?= e($card['label_color']) ?>"><?= e($card['label_name']) ?></span>
                    <?php endif; ?>
                    <?php if ($card['lead_outcome'] === 'converted'): ?>
                    <span class="badge-mini badge-success">✅ Convertido</span>
                    <?php elseif ($card['lead_outcome'] === 'lost'): ?>
                    <span class="badge-mini badge-danger">❌ Perdido</span>
                    <?php endif; ?>
                    <?php if ($card['in_recovery']): ?>
                    <span class="badge-mini badge-purple">Em recuperação</span>
                    <?php endif; ?>
                </div>
                <?php if ($card['phone'] ?? $card['contact_phone'] ?? null): ?>
                <small class="kanban-card-phone"><?= e($card['phone'] ?? $card['contact_phone']) ?></small>
                <?php endif; ?>
                <?php if ($card['value']): ?>
                <small class="kanban-card-value">R$ <?= number_format((float)$card['value'], 2, ',', '.') ?></small>
                <?php endif; ?>
                <?php if ($card['assigned_name'] ?? null): ?>
                <small class="kanban-card-assigned">👤 <?= e($card['assigned_name']) ?></small>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Nova Coluna -->
<div class="modal-overlay" id="modal-new-column" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header"><h3>Nova Coluna</h3><button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button></div>
        <form onsubmit="return createColumn(event)">
            <div class="form-group"><label>Nome *</label><input type="text" id="colName" class="form-control" required></div>
            <div class="form-group"><label>Cor</label><input type="color" id="colColor" class="form-control" value="#6c757d"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Novo Card -->
<div class="modal-overlay" id="modal-new-card" style="display:none;">
    <div class="modal-box">
        <div class="modal-header"><h3>Novo Card</h3><button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button></div>
        <form onsubmit="return createCard(event)">
            <div class="form-row">
                <div class="form-group"><label>Título *</label><input type="text" id="cardTitle" class="form-control" required></div>
                <div class="form-group"><label>Telefone</label><input type="text" id="cardPhone" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Valor (R$)</label><input type="text" id="cardValue" class="form-control"></div>
                <div class="form-group"><label>Coluna</label>
                    <select id="cardColumn" class="form-control">
                        <?php foreach ($board['columns'] as $col): ?>
                        <option value="<?= $col['id'] ?>"><?= e($col['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Responsável</label>
                    <select id="cardAssigned" class="form-control">
                        <option value="">—</option>
                        <?php foreach ($teamMembers as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= e($m['first_name'] . ' ' . $m['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Etiqueta</label>
                    <select id="cardLabel" class="form-control">
                        <option value="">—</option>
                        <?php foreach ($labels as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Descrição</label><textarea id="cardDesc" class="form-control" rows="2"></textarea></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('.modal-overlay').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detalhes do Card -->
<div class="modal-overlay" id="modal-card-detail" style="display:none;">
    <div class="modal-box modal-lg">
        <div class="modal-header"><h3 id="cardDetailTitle">Detalhes do Card</h3><button class="modal-close" onclick="this.closest('.modal-overlay').style.display='none'">&times;</button></div>
        <div class="modal-body-scroll" id="cardDetailBody">
            <!-- Preenchido via JS -->
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const boardId = <?= (int)$board['id'] ?>;

// Drag and Drop
document.querySelectorAll('.kanban-card').forEach(card => {
    card.addEventListener('dragstart', e => {
        e.dataTransfer.setData('text/plain', card.dataset.cardId);
        card.classList.add('dragging');
    });
    card.addEventListener('dragend', () => card.classList.remove('dragging'));
});

document.querySelectorAll('.kanban-cards').forEach(col => {
    col.addEventListener('dragover', e => { e.preventDefault(); col.classList.add('drag-over'); });
    col.addEventListener('dragleave', () => col.classList.remove('drag-over'));
    col.addEventListener('drop', async e => {
        e.preventDefault();
        col.classList.remove('drag-over');
        const cardId = e.dataTransfer.getData('text/plain');
        const columnId = col.dataset.columnId;
        await fetch('/crm/moveCard', {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
            body: JSON.stringify({card_id: cardId, column_id: columnId, position: 0})
        });
        location.reload();
    });
});

function toggleColMenu(btn) {
    const dd = btn.nextElementSibling;
    dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
}

async function createColumn(e) {
    e.preventDefault();
    await fetch('/crm/createColumn', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({board_id: boardId, name: document.getElementById('colName').value, color: document.getElementById('colColor').value})
    });
    location.reload(); return false;
}

async function renameColumn(id, currentName) {
    const name = prompt('Novo nome:', currentName);
    if (!name) return;
    await fetch(`/crm/updateColumn/${id}`, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({name})
    });
    location.reload();
}

async function deleteColumn(id) {
    if (!confirm('Excluir esta coluna e todos os cards dentro dela?')) return;
    await fetch(`/crm/deleteColumn/${id}`, {method:'POST', headers:{'X-CSRF-TOKEN':csrfToken}});
    location.reload();
}

async function createCard(e) {
    e.preventDefault();
    await fetch('/crm/createCard', {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({
            column_id: document.getElementById('cardColumn').value,
            title: document.getElementById('cardTitle').value,
            phone: document.getElementById('cardPhone').value,
            value: document.getElementById('cardValue').value,
            assigned_to: document.getElementById('cardAssigned').value,
            label_id: document.getElementById('cardLabel').value,
            description: document.getElementById('cardDesc').value,
        })
    });
    location.reload(); return false;
}

async function openCardDetail(id) {
    const res = await fetch(`/crm/cardDetail/${id}`);
    const json = await res.json();
    if (!json.success) return;
    const c = json.card;
    const body = document.getElementById('cardDetailBody');
    document.getElementById('cardDetailTitle').textContent = c.title;

    let activitiesHtml = (c.activities || []).map(a => 
        `<div class="activity-item"><small class="activity-type">${a.activity_type}</small> <span>${a.description || ''}</span> <small>${a.user_name || 'Sistema'} · ${a.created_at}</small></div>`
    ).join('');

    body.innerHTML = `
        <div class="card-detail-grid">
            <div class="card-detail-main">
                <div class="form-group"><label>Título</label><input type="text" id="cd-title" class="form-control" value="${c.title || ''}"></div>
                <div class="form-group"><label>Descrição</label><textarea id="cd-desc" class="form-control" rows="2">${c.description || ''}</textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Telefone</label><input type="text" id="cd-phone" class="form-control" value="${c.phone || ''}"></div>
                    <div class="form-group"><label>Valor (R$)</label><input type="text" id="cd-value" class="form-control" value="${c.value || ''}"></div>
                </div>
                <div class="form-group"><label>Responsável</label><input type="text" class="form-control" value="${c.assigned_name || 'Sem responsável'}" disabled></div>
                <div class="card-detail-actions">
                    <button class="btn btn-success btn-sm" onclick="convertLead(${c.id})">✅ Converter</button>
                    <button class="btn btn-danger btn-sm" onclick="lostLead(${c.id})">❌ Perdido</button>
                    ${c.contact_id ? `<a href="/whatsapp/chat/${c.contact_id}" class="btn btn-outline btn-sm">💬 Abrir chat</a>` : ''}
                    <button class="btn btn-outline btn-sm" onclick="openFollowUp(${c.id})">⏰ Retomar</button>
                    <button class="btn btn-danger-outline btn-sm" onclick="deleteCard(${c.id})">🗑️ Excluir</button>
                </div>
                <button class="btn btn-primary btn-sm" style="margin-top:12px;" onclick="saveCard(${c.id})">Salvar alterações</button>
            </div>
            <div class="card-detail-aside">
                <h5>Atividades</h5>
                <div class="activities-list">${activitiesHtml || '<p class="text-muted">Sem atividades</p>'}</div>
                <div class="form-group" style="margin-top:12px;">
                    <textarea id="cd-note" class="form-control" rows="2" placeholder="Adicionar nota..."></textarea>
                    <button class="btn btn-sm btn-outline" style="margin-top:4px;" onclick="addNote(${c.id})">+ Nota</button>
                </div>
            </div>
        </div>`;
    document.getElementById('modal-card-detail').style.display = 'flex';
}

async function saveCard(id) {
    await fetch(`/crm/updateCard/${id}`, {
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({
            title: document.getElementById('cd-title').value,
            description: document.getElementById('cd-desc').value,
            phone: document.getElementById('cd-phone').value,
            value: document.getElementById('cd-value').value,
        })
    });
    location.reload();
}
async function addNote(cardId) {
    const desc = document.getElementById('cd-note').value;
    if (!desc) return;
    await fetch(`/crm/addNote/${cardId}`, {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({description:desc})});
    openCardDetail(cardId);
}
async function convertLead(id) { if (!confirm('Converter este lead?')) return; await fetch(`/crm/convertLead/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}}); location.reload(); }
async function lostLead(id) { if (!confirm('Marcar como perdido?')) return; await fetch(`/crm/lostLead/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}}); location.reload(); }
async function deleteCard(id) { if (!confirm('Excluir este card?')) return; await fetch(`/crm/deleteCard/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken}}); location.reload(); }

function openFollowUp(id) {
    const amount = prompt('Retomar em quantas unidades?', '3');
    if (!amount) return;
    const unit = prompt('Unidade (minutes, hours, days)?', 'days');
    fetch(`/crm/setFollowUp/${id}`, {method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({amount,unit})})
        .then(() => location.reload());
}
</script>
