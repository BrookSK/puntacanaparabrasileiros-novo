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
    <div class="modal-box modal-xl">
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
    const b = c.briefing || {};
    const body = document.getElementById('cardDetailBody');
    document.getElementById('cardDetailTitle').textContent = c.title;

    // Status badge
    let statusBadge = '';
    if (c.lead_outcome === 'converted') statusBadge = '<span class="badge badge-success">Convertido</span>';
    else if (c.lead_outcome === 'lost') statusBadge = '<span class="badge badge-danger">Perdido</span>';
    else statusBadge = '<span class="badge badge-info">Em aberto</span>';

    // Atividades
    let activitiesHtml = (c.activities || []).slice(0, 15).map(a => 
        `<div class="cd-activity">
            <div class="cd-activity-dot"></div>
            <div class="cd-activity-content">
                <span class="cd-activity-desc">${a.description || ''}</span>
                <span class="cd-activity-meta">${a.user_name || 'Sistema'} &middot; ${formatDateShort(a.created_at)}</span>
            </div>
        </div>`
    ).join('');

    body.innerHTML = `
        <div class="cd-layout-3col">
            <!-- Coluna 1: Info + Editar -->
            <div class="cd-col">
                <div class="cd-section">
                    <h6>Informações</h6>
                    <div class="cd-info-row"><span class="cd-label">Status</span>${statusBadge}</div>
                    <div class="cd-info-row"><span class="cd-label">Coluna</span><span class="cd-value"><span class="cd-dot" style="background:${c.column_color || '#999'}"></span>${c.column_name || ''}</span></div>
                    ${c.assigned_name ? `<div class="cd-info-row"><span class="cd-label">Responsável</span><span class="cd-value">${c.assigned_name}</span></div>` : ''}
                    ${c.phone ? `<div class="cd-info-row"><span class="cd-label">Telefone</span><span class="cd-value">${c.phone}</span></div>` : ''}
                    ${c.value ? `<div class="cd-info-row"><span class="cd-label">Valor</span><span class="cd-value cd-value-money">R$ ${parseFloat(c.value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</span></div>` : ''}
                </div>

                <div class="cd-section cd-edit-section">
                    <h6>Editar</h6>
                    <div class="cd-field"><label>Título</label><input type="text" id="cd-title" class="form-control" value="${c.title || ''}"></div>
                    <div class="cd-form-row2">
                        <div class="cd-field"><label>Telefone</label><input type="text" id="cd-phone" class="form-control" value="${c.phone || ''}"></div>
                        <div class="cd-field"><label>Valor (R$)</label><input type="text" id="cd-value" class="form-control" value="${c.value || ''}"></div>
                    </div>
                    <div class="cd-field"><label>Descrição</label><textarea id="cd-desc" class="form-control" rows="2">${c.description || ''}</textarea></div>
                    <button class="btn btn-primary btn-sm" onclick="saveCard(${c.id})">Salvar</button>
                </div>

                <div class="cd-section">
                    <h6>Ações</h6>
                    <div class="cd-actions-grid">
                        <button class="cd-action-btn cd-action-convert" onclick="convertLead(${c.id})"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Converter</button>
                        <button class="cd-action-btn cd-action-lost" onclick="lostLead(${c.id})"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Perdido</button>
                        ${c.contact_id ? `<a href="/whatsapp/chat/${c.contact_id}" class="cd-action-btn cd-action-chat"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg> Chat</a>` : ''}
                        <button class="cd-action-btn cd-action-followup" onclick="openFollowUp(${c.id})"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Retomar</button>
                        <button class="cd-action-btn cd-action-delete" onclick="deleteCard(${c.id})"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg> Excluir</button>
                    </div>
                </div>
            </div>

            <!-- Coluna 2: Briefing -->
            <div class="cd-col">
                <div class="cd-section">
                    <h6>Briefing Comercial</h6>
                    ${c.contact_id ? `
                    <div class="cd-briefing-grid">
                        <div class="cd-bf-item"><span class="cd-bf-label">Necessidade</span><span class="cd-bf-value">${b.need || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Dor/Problema</span><span class="cd-bf-value">${b.main_pain || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Solução atual</span><span class="cd-bf-value">${b.current_solution || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Objetivo</span><span class="cd-bf-value">${b.expected_goal || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Urgência</span><span class="cd-bf-value">${b.urgency || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Investimento</span><span class="cd-bf-value">${b.investment_range || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Decisor</span><span class="cd-bf-value">${b.decision_level || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Temperatura</span><span class="cd-bf-value cd-temp-${b.lead_temperature || ''}">${b.lead_temperature || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Objeção</span><span class="cd-bf-value">${b.main_objection || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Próximo passo</span><span class="cd-bf-value">${b.next_step || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Próx. contato</span><span class="cd-bf-value">${b.next_contact_date || '—'}</span></div>
                        <div class="cd-bf-item"><span class="cd-bf-label">Observações</span><span class="cd-bf-value">${b.notes || '—'}</span></div>
                    </div>
                    ` : '<p class="cd-empty">Sem contato WhatsApp vinculado. Briefing disponível apenas para cards com contato.</p>'}
                </div>
            </div>

            <!-- Coluna 3: Atividades -->
            <div class="cd-col">
                <div class="cd-section">
                    <h6>Atividades</h6>
                    <div class="cd-activities">${activitiesHtml || '<p class="cd-empty">Nenhuma atividade</p>'}</div>
                </div>
                <div class="cd-note-section">
                    <textarea id="cd-note" class="form-control" rows="2" placeholder="Escrever nota..."></textarea>
                    <button class="btn btn-sm btn-outline" onclick="addNote(${c.id})">Adicionar nota</button>
                </div>
            </div>
        </div>`;
    document.getElementById('modal-card-detail').style.display = 'flex';
}

function formatDateShort(dt) {
    if (!dt) return '';
    const d = new Date(dt);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'});
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
