/**
 * WhatsApp Chat — JavaScript Completo
 * Gerencia: contatos, mensagens, polling, envio, emoji, respostas rápidas,
 * lightbox, detalhes, briefing, etiquetas, CRM.
 */
'use strict';

// ═══════════════════════════════════════════════
// ESTADO GLOBAL
// ═══════════════════════════════════════════════
const STATE = {
    contactId: null,
    instanceId: null,
    lastMessageId: 0,
    currentTab: 'contacts',
    pollInterval: null,
    statusInterval: null,
    quickReplies: [],
    stagedFile: null,
    renderedMessageIds: new Set(),
};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const chatApp = document.getElementById('chatApp');

if (chatApp) {
    STATE.instanceId = chatApp.dataset.instanceId;
    const initContactId = chatApp.dataset.contactId;
    if (initContactId) {
        STATE.contactId = parseInt(initContactId);
    }
}

// ═══════════════════════════════════════════════
// INICIALIZAÇÃO
// ═══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    loadContacts();
    loadQuickReplies();
    setupTextarea();
    setupEmojiPicker();

    if (STATE.contactId) {
        openContact(STATE.contactId);
    }

    // Polling de contatos a cada 8 segundos
    setInterval(loadContacts, 8000);
});

// ═══════════════════════════════════════════════
// CONTATOS
// ═══════════════════════════════════════════════
async function loadContacts() {
    const search = document.getElementById('searchContacts')?.value || '';
    const assigned = document.getElementById('filterAssigned')?.value || '';
    const label = document.getElementById('filterLabel')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';

    const params = new URLSearchParams({
        tab: STATE.currentTab, search, assigned_to: assigned, label_id: label, service_status: status
    });

    try {
        const res = await fetch(`/whatsapp/contacts?${params}`);
        const json = await res.json();
        renderContacts(json.contacts || []);
        if (json.counts) {
            document.getElementById('countContacts').textContent = json.counts.contacts || 0;
            document.getElementById('countGroups').textContent = json.counts.groups || 0;
        }
    } catch (e) { console.error('loadContacts error:', e); }
}

function renderContacts(contacts) {
    const list = document.getElementById('contactList');
    if (!list) return;

    // Agrupar por status
    const groups = { em_atendimento: [], aguardando: [], novo: [], concluido: [] };
    contacts.forEach(c => {
        const s = c.service_status || 'novo';
        if (groups[s]) groups[s].push(c);
        else groups.novo.push(c);
    });

    const statusLabels = {
        em_atendimento: '🟠 Em Atendimento',
        aguardando: '🔴 Aguardando',
        novo: '🔵 Novos',
        concluido: '🟢 Concluídos',
    };

    let html = '';
    for (const [status, items] of Object.entries(groups)) {
        if (items.length === 0) continue;
        html += `<div class="wpp-status-group">${statusLabels[status]} (${items.length})</div>`;
        items.forEach(c => { html += renderContactItem(c); });
    }

    list.innerHTML = html || '<p style="padding:20px;text-align:center;color:#999;">Nenhum contato encontrado.</p>';
}

function renderContactItem(c) {
    const name = c.contact_name || c.push_name || c.phone || 'Desconhecido';
    const hasPhoto = c.profile_picture_url && c.profile_picture_url.length > 5 && c.profile_picture_url !== 'null';
    const avatar = hasPhoto
        ? `<img src="${c.profile_picture_url}" alt="">`
        : name.substring(0, 2).toUpperCase();
    const avatarClass = c.is_group ? 'wpp-avatar group' : 'wpp-avatar';
    const isActive = STATE.contactId === c.id ? 'active' : '';

    let preview = '';
    if (c.last_message_type === 'image') preview = '📷 Imagem';
    else if (c.last_message_type === 'audio') preview = '🎤 Áudio';
    else if (c.last_message_type === 'video') preview = '🎥 Vídeo';
    else if (c.last_message_type === 'document') preview = '📎 Documento';
    else if (c.last_message_type === 'sticker') preview = '🏷️ Sticker';
    else preview = truncateText(c.last_message_text || '', 40);

    const time = c.last_message_at ? formatTime(c.last_message_at) : '';
    const unread = c.unread_count > 0 ? `<span class="wpp-unread-badge">${c.unread_count}</span>` : '';

    let labels = '';
    if (c.labels && c.labels.length) {
        labels = c.labels.map(l => `<span class="wpp-label-badge" style="background:${l.color}">${l.name}</span>`).join('');
    }
    let crm = '';
    if (c.crm_info) {
        crm = `<span class="wpp-crm-badge">${c.crm_info.board_name} › ${c.crm_info.column_name}</span>`;
    }

    return `<div class="wpp-contact-item ${isActive}" onclick="openContact(${c.id})">
        <span class="wpp-status-dot ${c.service_status || 'novo'}"></span>
        <div class="${avatarClass}">${typeof avatar === 'string' && avatar.startsWith('<') ? avatar : avatar}</div>
        <div class="wpp-contact-info">
            <div class="wpp-contact-name">${escapeHtml(name)}</div>
            <div class="wpp-contact-preview">${preview}</div>
            <div class="wpp-contact-labels">${labels}${crm}</div>
        </div>
        <div class="wpp-contact-meta">
            <div class="wpp-contact-time">${time}</div>
            ${unread}
        </div>
    </div>`;
}

function switchTab(tab) {
    STATE.currentTab = tab;
    document.querySelectorAll('.wpp-tab').forEach(t => t.classList.remove('active'));
    document.querySelector(`.wpp-tab[data-tab="${tab}"]`)?.classList.add('active');
    loadContacts();
}

function filterContacts() { loadContacts(); }

// ═══════════════════════════════════════════════
// ABRIR CONTATO / MENSAGENS
// ═══════════════════════════════════════════════
async function openContact(contactId) {
    STATE.contactId = contactId;
    STATE.lastMessageId = 0;
    STATE.renderedMessageIds.clear();

    // UI
    document.getElementById('emptyChat').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';
    document.getElementById('messagesList').innerHTML = '';
    document.querySelector('.wpp-chat-wrapper')?.classList.add('chat-open');

    // Marcar ativo na lista
    document.querySelectorAll('.wpp-contact-item').forEach(el => el.classList.remove('active'));

    // Carregar detalhes do contato
    try {
        const res = await fetch(`/whatsapp/contactDetail/${contactId}`);
        const json = await res.json();
        if (json.success) {
            const c = json.contact;
            document.getElementById('chatName').textContent = c.contact_name || c.push_name || c.phone || '—';
            document.getElementById('chatPhone').textContent = c.phone || '';
            document.getElementById('chatAvatar').innerHTML = (c.profile_picture_url && c.profile_picture_url.length > 5 && c.profile_picture_url !== 'null')
                ? `<img src="${c.profile_picture_url}">` : (c.contact_name || c.push_name || 'XX').substring(0, 2).toUpperCase();
            document.getElementById('serviceStatusSelect').value = c.service_status || 'novo';

            // Preencher details panel
            const detailAvatarEl = document.getElementById('detailAvatar');
            if (detailAvatarEl) {
                const hasAvatar = c.profile_picture_url && c.profile_picture_url.length > 5 && c.profile_picture_url !== 'null';
                detailAvatarEl.innerHTML = hasAvatar
                    ? `<img src="${c.profile_picture_url}">` 
                    : `<span style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:24px;font-weight:600;color:#666;">${(c.contact_name || c.push_name || 'XX').substring(0, 2).toUpperCase()}</span>`;
            }
            document.getElementById('detailName').value = c.contact_name || '';
            document.getElementById('detailAssigned').value = c.assigned_to || '';
            document.getElementById('detailNotes').value = c.internal_notes || '';
            renderDetailLabels(c.labels || []);
        }
    } catch (e) { console.error(e); }

    // Carregar mensagens
    await loadMessages();

    // Iniciar polling
    clearInterval(STATE.pollInterval);
    clearInterval(STATE.statusInterval);
    STATE.pollInterval = setInterval(pollMessages, 4000);
    STATE.statusInterval = setInterval(pollStatuses, 10000);
}

async function loadMessages(beforeId) {
    const params = beforeId ? `?before_id=${beforeId}` : '';
    const res = await fetch(`/whatsapp/messages/${STATE.contactId}${params}`);
    const json = await res.json();

    if (json.messages && json.messages.length) {
        const container = document.getElementById('messagesList');
        const html = json.messages.map(m => renderMessage(m)).join('');

        if (beforeId) {
            container.insertAdjacentHTML('afterbegin', html);
        } else {
            container.innerHTML = html;
            scrollToBottom();
        }

        json.messages.forEach(m => {
            STATE.renderedMessageIds.add(m.id);
            if (m.id > STATE.lastMessageId) STATE.lastMessageId = m.id;
        });
    }
}

async function pollMessages() {
    if (!STATE.contactId) return;
    try {
        const res = await fetch(`/whatsapp/poll/${STATE.contactId}?after_id=${STATE.lastMessageId}`);
        const json = await res.json();

        if (json.messages && json.messages.length) {
            const container = document.getElementById('messagesList');
            json.messages.forEach(m => {
                if (!STATE.renderedMessageIds.has(m.id)) {
                    container.insertAdjacentHTML('beforeend', renderMessage(m));
                    STATE.renderedMessageIds.add(m.id);
                    if (m.id > STATE.lastMessageId) STATE.lastMessageId = m.id;
                }
            });
            scrollToBottom();
        }

        // Mensagens deletadas
        if (json.deleted_ids && json.deleted_ids.length) {
            json.deleted_ids.forEach(d => {
                const el = document.querySelector(`[data-msg-id="${d.id}"]`);
                if (el) el.innerHTML = '<em class="wpp-msg-deleted">🚫 Mensagem apagada</em>';
            });
        }
    } catch (e) {}
}

async function pollStatuses() {
    if (!STATE.contactId) return;
    try {
        const res = await fetch(`/whatsapp/messageStatuses/${STATE.contactId}`);
        const json = await res.json();
        (json.statuses || []).forEach(s => {
            const el = document.querySelector(`[data-msg-id="${s.id}"] .wpp-msg-ack`);
            if (el) el.innerHTML = ackIcon(s.ack_status);
        });
    } catch (e) {}
}

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;
}

function closeChat() {
    document.querySelector('.wpp-chat-wrapper')?.classList.remove('chat-open');
    STATE.contactId = null;
    clearInterval(STATE.pollInterval);
    clearInterval(STATE.statusInterval);
}

// ═══════════════════════════════════════════════
// RENDERIZAR MENSAGEM
// ═══════════════════════════════════════════════
function renderMessage(m) {
    if (m.is_deleted) return `<div class="wpp-msg ${m.from_me ? 'me' : 'other'}" data-msg-id="${m.id}"><em class="wpp-msg-deleted" style="opacity:.6;">Mensagem apagada</em></div>`;

    // Reações não são renderizadas como mensagens — são aplicadas como badges
    if (m.message_type === 'reaction') {
        // Tentar encontrar a mensagem original e adicionar reação
        if (m.quoted_message_id) {
            setTimeout(() => applyReaction(m.quoted_message_id, m.message_text), 50);
        }
        return ''; // Não renderizar como balão
    }

    const side = m.from_me ? 'me' : 'other';
    let content = '';

    switch (m.message_type) {
        case 'image':
            if (m.media_url) {
                content = `<div class="wpp-msg-image"><img src="${m.media_url}" onclick="openLightbox('${m.media_url}')"></div>`;
            } else {
                content = `<div class="wpp-msg-text" style="color:#999;font-style:italic;">🖼 Imagem não disponível</div>`;
            }
            if (m.message_text) content += `<div class="wpp-msg-text">${formatWhatsApp(m.message_text)}</div>`;
            break;
        case 'audio':
            content = renderAudioPlayer(m);
            break;
        case 'video':
            if (m.media_url) {
                content = `<div class="wpp-msg-video"><video src="${m.media_url}" controls></video></div>`;
            } else {
                content = `<div class="wpp-msg-text" style="color:#999;font-style:italic;">🎥 Vídeo não disponível</div>`;
            }
            if (m.message_text) content += `<div class="wpp-msg-text">${formatWhatsApp(m.message_text)}</div>`;
            break;
        case 'document':
            content = renderDocument(m);
            break;
        case 'sticker':
            if (m.media_url) {
                content = `<div class="wpp-msg-sticker"><img src="${m.media_url}"></div>`;
            } else {
                content = `<div class="wpp-msg-text" style="color:#999;font-style:italic;">🏷 Figurinha</div>`;
            }
            break;
        case 'location':
            content = `<div class="wpp-msg-text">${m.message_text || 'Localização compartilhada'}</div>`;
            break;
        default:
            content = `<div class="wpp-msg-text">${formatWhatsApp(m.message_text || '')}</div>`;
    }

    const sender = (!m.from_me && m.sender_name) ? `<div class="wpp-msg-sender">${escapeHtml(m.sender_name)}</div>` : '';
    const time = formatMessageTime(m.timestamp);
    const ack = m.from_me ? `<span class="wpp-msg-ack ${m.ack_status === 'read' ? 'read' : ''}">${ackIcon(m.ack_status)}</span>` : '';

    return `<div class="wpp-msg ${side}" data-msg-id="${m.id}" data-message-id="${m.message_id || ''}">${sender}${content}<div class="wpp-msg-time">${time} ${ack}</div></div>`;
}

function renderAudioPlayer(m) {
    if (!m.media_url) {
        let html = `<div class="wpp-msg-text" style="color:#999;font-style:italic;">Áudio não disponível</div>`;
        if (m.transcription) html += `<div class="wpp-transcription">${escapeHtml(m.transcription)}</div>`;
        return html;
    }

    // Gerar barras de waveform aleatórias (simulação visual)
    const barCount = 35;
    let bars = '';
    for (let i = 0; i < barCount; i++) {
        const h = Math.floor(Math.random() * 24) + 6;
        bars += `<div class="wave-bar" style="height:${h}px;" data-idx="${i}"></div>`;
    }

    let html = `<div class="wpp-msg-audio" data-audio-url="${m.media_url}" data-msg-id="${m.id}">
        <div class="wpp-audio-row">
            <button class="wpp-audio-play" onclick="event.stopPropagation();toggleAudio(this)">
                <svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </button>
            <div class="wpp-audio-waveform" onclick="event.stopPropagation();seekAudio(this,event)">${bars}</div>
            <span class="wpp-audio-volume"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07"/></svg></span>
        </div>
        <div class="wpp-audio-times"><span class="audio-current">0:00</span><span class="audio-duration">0:00</span></div>
    </div>`;

    if (m.transcription) {
        html += `<div class="wpp-transcription">${escapeHtml(m.transcription)}</div>`;
    } else {
        html += `<button class="wpp-transcribe-btn" onclick="event.stopPropagation();transcribeAudio(${m.id},this)">Transcrever</button>`;
    }
    return html;
}

function renderDocument(m) {
    if (!m.media_url) {
        return `<div class="wpp-msg-text" style="color:#999;font-style:italic;">Documento: ${escapeHtml(m.media_filename || 'arquivo')}</div>`;
    }
    // Ícone por tipo
    let icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>';
    let iconColor = '#64748b';
    if (m.media_mime_type && m.media_mime_type.includes('pdf')) { iconColor = '#ef4444'; }
    else if (m.media_mime_type && (m.media_mime_type.includes('excel') || m.media_mime_type.includes('spreadsheet'))) { iconColor = '#16a34a'; }
    else if (m.media_mime_type && (m.media_mime_type.includes('word') || m.media_mime_type.includes('document'))) { iconColor = '#2563eb'; }

    const fileName = m.media_filename || 'Documento';
    const mimeLabel = m.media_mime_type ? m.media_mime_type.split('/').pop().toUpperCase() : '';

    return `<div class="wpp-msg-document">
        <span class="doc-icon" style="color:${iconColor}">${icon}</span>
        <div class="doc-info">
            <strong>${escapeHtml(fileName)}</strong>
            <small>${mimeLabel}</small>
        </div>
        <div class="doc-actions">
            <a href="${m.media_url}" target="_blank">Ver</a>
            <a href="${m.media_url}" download="${escapeHtml(fileName)}">Baixar</a>
        </div>
    </div>`;
}

function ackIcon(status) {
    switch (status) {
        case 'pending': return '⏳';
        case 'sent': return '✓';
        case 'delivered': return '✓✓';
        case 'read': return '✓✓';
        case 'failed': return '❌';
        default: return '';
    }
}

// ═══════════════════════════════════════════════
// ENVIO DE MENSAGENS
// ═══════════════════════════════════════════════
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    const sign = document.getElementById('signToggle')?.checked ? '1' : '0';

    // Se tem arquivo staged, enviar como mídia
    if (STATE.stagedFile) {
        await sendMediaMessage(text);
        return;
    }

    if (!text || !STATE.contactId) return;

    input.value = '';
    input.style.height = '34px';

    try {
        await fetch('/whatsapp/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ contact_id: STATE.contactId, message: text, sign }),
        });
        pollMessages();
    } catch (e) { console.error('send error:', e); }
}

async function sendMediaMessage(caption) {
    if (!STATE.stagedFile || !STATE.contactId) return;

    const formData = new FormData();
    formData.append('file', STATE.stagedFile);
    formData.append('contact_id', STATE.contactId);
    formData.append('caption', caption || '');
    formData.append('sign', document.getElementById('signToggle')?.checked ? '1' : '0');

    cancelMedia();
    document.getElementById('messageInput').value = '';

    try {
        await fetch('/whatsapp/sendMedia', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        });
        pollMessages();
    } catch (e) { console.error('sendMedia error:', e); }
}

// ─── File staging ───
function stageFile(input) {
    const file = input.files[0];
    if (!file) return;
    STATE.stagedFile = file;

    const stage = document.getElementById('mediaStage');
    document.getElementById('mediaName').textContent = file.name;
    document.getElementById('mediaSize').textContent = formatFileSize(file.size);

    // Preview
    const preview = document.getElementById('mediaPreview');
    if (file.type.startsWith('image/')) {
        const url = URL.createObjectURL(file);
        preview.innerHTML = `<img src="${url}" style="width:32px;height:32px;object-fit:cover;border-radius:4px;">`;
    } else {
        preview.innerHTML = '📎';
    }

    stage.style.display = 'flex';
    input.value = '';
}

function cancelMedia() {
    STATE.stagedFile = null;
    document.getElementById('mediaStage').style.display = 'none';
}

// ─── Textarea setup ───
function setupTextarea() {
    const textarea = document.getElementById('messageInput');
    if (!textarea) return;

    // Auto-resize
    textarea.addEventListener('input', () => {
        textarea.style.height = '34px';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        checkQuickReply(textarea.value);
    });

    // Enter = send, Shift+Enter = newline
    textarea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
}

// ═══════════════════════════════════════════════
// RESPOSTAS RÁPIDAS
// ═══════════════════════════════════════════════
async function loadQuickReplies() {
    try {
        const res = await fetch('/whatsapp/quickReplies', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!res.ok) {
            console.error('[WhatsApp] quickReplies HTTP', res.status);
            return;
        }
        const json = await res.json();
        STATE.quickReplies = json.replies || [];
        console.log('[WhatsApp] Quick replies carregadas:', STATE.quickReplies.length);
    } catch (e) {
        console.error('[WhatsApp] Erro ao carregar respostas rápidas:', e);
    }
}

function checkQuickReply(text) {
    const dropdown = document.getElementById('quickReplyDropdown');
    if (!dropdown) return;
    if (!text.startsWith('/') || text.length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    const query = text.substring(1).toLowerCase();
    const matches = STATE.quickReplies.filter(r => r.shortcut.toLowerCase().startsWith(query));

    if (matches.length === 0) {
        dropdown.style.display = 'none';
        return;
    }

    dropdown.innerHTML = matches.map(r =>
        `<div class="wpp-qr-item" data-reply-id="${r.id}">
            <div class="qr-shortcut">/${escapeHtml(r.shortcut)}</div>
            <div class="qr-preview">${escapeHtml(truncateText(r.message || '', 60))}</div>
        </div>`
    ).join('');

    // Adicionar event listeners (evita problemas com aspas no onclick inline)
    dropdown.querySelectorAll('.wpp-qr-item').forEach(item => {
        item.addEventListener('click', () => {
            selectQuickReply(parseInt(item.dataset.replyId));
        });
    });

    dropdown.style.display = 'block';
}

function selectQuickReply(id) {
    const textarea = document.getElementById('messageInput');
    const reply = STATE.quickReplies.find(r => parseInt(r.id) === id);
    if (reply) {
        textarea.value = reply.message || '';
        textarea.style.height = '34px';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }
    document.getElementById('quickReplyDropdown').style.display = 'none';
    textarea.focus();
}

function openQuickReplies() {
    document.getElementById('modal-quick-replies').style.display = 'flex';
    renderQuickRepliesList();
}

function renderQuickRepliesList() {
    const container = document.getElementById('quickRepliesList');
    if (!STATE.quickReplies.length) {
        container.innerHTML = '<p style="color:#999;">Nenhuma resposta rápida cadastrada.</p>';
        return;
    }
    container.innerHTML = STATE.quickReplies.map(r => `
        <div style="padding:8px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;">
            <div><span class="badge badge-info">/${r.shortcut}</span> <span style="font-size:12px;margin-left:8px;">${escapeHtml(truncateText(r.message || '', 80))}</span>
            ${r.attachment_name ? `<br><small>📎 ${r.attachment_name}</small>` : ''}</div>
            <button class="btn btn-sm btn-danger-outline" onclick="deleteQuickReply(${r.id})">×</button>
        </div>
    `).join('');
}

async function saveQuickReply(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('shortcut', document.getElementById('qr-shortcut').value);
    formData.append('message', document.getElementById('qr-message').value);
    const file = document.getElementById('qr-attachment').files[0];
    if (file) formData.append('attachment', file);

    await fetch('/whatsapp/saveQuickReply', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData });
    await loadQuickReplies();
    renderQuickRepliesList();
    document.getElementById('form-quick-reply').reset();
    return false;
}

async function deleteQuickReply(id) {
    if (!confirm('Excluir esta resposta rápida?')) return;
    await fetch(`/whatsapp/deleteQuickReply/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    await loadQuickReplies();
    renderQuickRepliesList();
}

// ═══════════════════════════════════════════════
// EMOJI PICKER
// ═══════════════════════════════════════════════
function setupEmojiPicker() {
    const picker = document.getElementById('emojiPicker');
    if (!picker) return;

    const emojis = [
        '😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗',
        '😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏',
        '😒','🙄','😬','😮','😯','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱',
        '😖','😣','😞','😓','😩','😫','🥱','😤','😡','🤬','😈','👿','💀','☠️','💩','🤡',
        '👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆',
        '👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💪',
        '❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖',
        '💝','💘','🏠','🏢','🚗','✈️','🚀','⭐','🌟','💫','⚡','🔥','💯','🎉','🎊','🎁',
        '✅','❌','⚠️','🔔','📌','💬','👀','🙈','🙉','🙊','💡','📝','📎','📊','🗓️','⏰',
    ];

    picker.innerHTML = emojis.map(e => `<span onclick="insertEmoji('${e}')">${e}</span>`).join('');
}

function toggleEmojiPicker() {
    const picker = document.getElementById('emojiPicker');
    picker.style.display = picker.style.display === 'none' ? 'grid' : 'none';
}

function insertEmoji(emoji) {
    const textarea = document.getElementById('messageInput');
    const start = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, start) + emoji + textarea.value.substring(textarea.selectionEnd);
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    textarea.focus();
}

// ═══════════════════════════════════════════════
// LIGHTBOX
// ═══════════════════════════════════════════════
function openLightbox(imgUrl) {
    document.getElementById('img-lightbox-img').src = imgUrl;
    document.getElementById('img-lightbox').classList.add('open');
}

function closeLightbox(event) {
    if (event.target === document.getElementById('img-lightbox') || event.target.classList.contains('wpp-lightbox-close')) {
        document.getElementById('img-lightbox').classList.remove('open');
    }
}

// ═══════════════════════════════════════════════
// ÁUDIO PLAYER E TRANSCRIÇÃO
// ═══════════════════════════════════════════════
let currentAudio = null;
let currentAudioEl = null;

function toggleAudio(btn) {
    const container = btn.closest('.wpp-msg-audio');
    const url = container.dataset.audioUrl;

    // Se já está tocando este áudio, pausar
    if (currentAudio && currentAudioEl === container) {
        if (currentAudio.paused) {
            currentAudio.play();
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
        } else {
            currentAudio.pause();
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
        }
        return;
    }

    // Parar áudio anterior
    if (currentAudio) {
        currentAudio.pause();
        if (currentAudioEl) {
            currentAudioEl.querySelector('.wpp-audio-play').innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
            currentAudioEl.querySelectorAll('.wave-bar').forEach(b => b.classList.remove('played'));
        }
    }

    const audio = new Audio(url);
    currentAudio = audio;
    currentAudioEl = container;
    const bars = container.querySelectorAll('.wave-bar');
    const currentTimeEl = container.querySelector('.audio-current');
    const durationEl = container.querySelector('.audio-duration');

    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>';
    audio.play();

    audio.addEventListener('loadedmetadata', () => {
        durationEl.textContent = formatDuration(audio.duration);
    });

    audio.addEventListener('timeupdate', () => {
        if (audio.duration) {
            const progress = audio.currentTime / audio.duration;
            const playedCount = Math.floor(progress * bars.length);
            bars.forEach((b, i) => {
                b.classList.toggle('played', i < playedCount);
            });
            currentTimeEl.textContent = formatDuration(audio.currentTime);
        }
    });

    audio.addEventListener('ended', () => {
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
        bars.forEach(b => b.classList.remove('played'));
        currentTimeEl.textContent = '0:00';
        currentAudio = null;
        currentAudioEl = null;
    });
}

function seekAudio(waveform, event) {
    if (!currentAudio || currentAudioEl !== waveform.closest('.wpp-msg-audio')) return;
    const rect = waveform.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const percent = x / rect.width;
    currentAudio.currentTime = percent * currentAudio.duration;
}

async function transcribeAudio(msgId, btn) {
    btn.textContent = '⏳ Transcrevendo...';
    btn.disabled = true;
    try {
        const res = await fetch(`/whatsapp/transcribeAudio/${msgId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
        const json = await res.json();
        if (json.success && json.transcription) {
            btn.outerHTML = `<div class="wpp-transcription">${escapeHtml(json.transcription)}</div>`;
        } else {
            btn.textContent = '❌ Erro';
        }
    } catch (e) { btn.textContent = '❌ Erro'; }
}

// ═══════════════════════════════════════════════
// DETALHES DO CONTATO (Painel Direito)
// ═══════════════════════════════════════════════
function toggleDetails() {
    const panel = document.getElementById('wppDetails');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

async function saveContactDetails() {
    const data = {
        contact_name: document.getElementById('detailName').value,
        assigned_to: document.getElementById('detailAssigned').value || null,
        internal_notes: document.getElementById('detailNotes').value,
    };
    await fetch(`/whatsapp/updateContact/${STATE.contactId}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data),
    });
    loadContacts();
}

async function updateServiceStatus() {
    const status = document.getElementById('serviceStatusSelect').value;
    await fetch(`/whatsapp/updateServiceStatus/${STATE.contactId}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status }),
    });
    loadContacts();
}

// ─── Etiquetas ───
function renderDetailLabels(labels) {
    const container = document.getElementById('detailLabels');
    container.innerHTML = labels.map(l =>
        `<span class="wpp-label-tag" style="background:${l.color}">${l.name} <button onclick="removeLabel(${l.id})">×</button></span>`
    ).join('');
}

async function addLabel() {
    const select = document.getElementById('addLabelSelect');
    const labelId = select.value;
    if (!labelId) return;
    await fetch('/whatsapp/toggleLabel', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ contact_id: STATE.contactId, label_id: labelId, action: 'add' }),
    });
    openContact(STATE.contactId);
}

async function removeLabel(labelId) {
    await fetch('/whatsapp/toggleLabel', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ contact_id: STATE.contactId, label_id: labelId, action: 'remove' }),
    });
    openContact(STATE.contactId);
}

function openCreateLabel() { document.getElementById('modal-create-label').style.display = 'flex'; }

async function createNewLabel(e) {
    e.preventDefault();
    const name = document.getElementById('newLabelName').value;
    const color = document.getElementById('newLabelColor').value;
    await fetch('/whatsapp/createLabel', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ name, color }),
    });
    closeModal('modal-create-label');
    location.reload();
    return false;
}

// ─── CRM ───
async function loadCrmColumns() {
    const boardId = document.getElementById('crmBoardSelect').value;
    const colSelect = document.getElementById('crmColumnSelect');
    colSelect.innerHTML = '<option value="">Selecionar coluna...</option>';
    colSelect.disabled = true;
    if (!boardId) return;

    const res = await fetch('/crm/listBoards');
    const json = await res.json();
    const board = (json.boards || []).find(b => b.id == boardId);
    if (board && board.columns) {
        board.columns.forEach(c => {
            colSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
        });
        colSelect.disabled = false;
    }
}

async function addToCrm() {
    const columnId = document.getElementById('crmColumnSelect').value;
    if (!columnId) { alert('Selecione board e coluna.'); return; }
    await fetch('/whatsapp/addToCrm', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ contact_id: STATE.contactId, column_id: columnId }),
    });
    alert('Contato adicionado ao CRM!');
    loadContacts();
}

// Carregar boards no painel de detalhes
(async function loadCrmBoards() {
    try {
        const res = await fetch('/crm/listBoards');
        const json = await res.json();
        const select = document.getElementById('crmBoardSelect');
        if (select && json.boards) {
            json.boards.forEach(b => {
                select.innerHTML += `<option value="${b.id}">${b.name}</option>`;
            });
        }
    } catch (e) {}
})();

async function deleteContactPermanently() {
    if (!confirm('ATENÇÃO: Excluir este contato, todas as mensagens, etiquetas e briefing? Essa ação não pode ser desfeita.')) return;
    await fetch(`/whatsapp/deleteContact/${STATE.contactId}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    location.reload();
}

// ═══════════════════════════════════════════════
// BRIEFING COMERCIAL
// ═══════════════════════════════════════════════
async function openBriefing() {
    document.getElementById('modal-briefing').style.display = 'flex';
    const res = await fetch(`/whatsapp/getBriefing/${STATE.contactId}`);
    const json = await res.json();
    const b = json.briefing || {};
    document.getElementById('bf-need').value = b.need || '';
    document.getElementById('bf-pain').value = b.main_pain || '';
    document.getElementById('bf-solution').value = b.current_solution || '';
    document.getElementById('bf-goal').value = b.expected_goal || '';
    document.getElementById('bf-urgency').value = b.urgency || '';
    document.getElementById('bf-investment').value = b.investment_range || '';
    document.getElementById('bf-decision').value = b.decision_level || '';
    document.getElementById('bf-temperature').value = b.lead_temperature || '';
    document.getElementById('bf-next-date').value = b.next_contact_date || '';
    document.getElementById('bf-objection').value = b.main_objection || '';
    document.getElementById('bf-next-step').value = b.next_step || '';
    document.getElementById('bf-notes').value = b.notes || '';
}

async function saveBriefing(e) {
    e.preventDefault();
    const data = {
        need: document.getElementById('bf-need').value,
        main_pain: document.getElementById('bf-pain').value,
        current_solution: document.getElementById('bf-solution').value,
        expected_goal: document.getElementById('bf-goal').value,
        urgency: document.getElementById('bf-urgency').value,
        investment_range: document.getElementById('bf-investment').value,
        decision_level: document.getElementById('bf-decision').value,
        lead_temperature: document.getElementById('bf-temperature').value,
        next_contact_date: document.getElementById('bf-next-date').value,
        main_objection: document.getElementById('bf-objection').value,
        next_step: document.getElementById('bf-next-step').value,
        notes: document.getElementById('bf-notes').value,
    };
    await fetch(`/whatsapp/saveBriefing/${STATE.contactId}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data),
    });
    closeModal('modal-briefing');
    return false;
}

// ═══════════════════════════════════════════════
// NOVA CONVERSA
// ═══════════════════════════════════════════════
function openNewConversation() { document.getElementById('modal-new-conversation').style.display = 'flex'; }

async function startConversation(e) {
    e.preventDefault();
    const phone = document.getElementById('newConvPhone').value.replace(/\D/g, '');
    const name = document.getElementById('newConvName').value;

    if (phone.length < 10) { alert('Número inválido.'); return false; }

    const res = await fetch('/whatsapp/startConversation', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ phone, name }),
    });
    const json = await res.json();
    if (json.success) {
        closeModal('modal-new-conversation');
        openContact(json.contact_id);
        loadContacts();
    } else {
        alert(json.error || 'Erro ao iniciar conversa.');
    }
    return false;
}

// ═══════════════════════════════════════════════
// SINCRONIZAÇÃO
// ═══════════════════════════════════════════════
async function syncGroups() {
    const res = await fetch('/whatsapp/syncGroups', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    const json = await res.json();
    // Também sincronizar fotos
    await fetch('/whatsapp/syncPhotos', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
    alert(json.success ? `${json.updated} grupos sincronizados! Fotos atualizadas.` : (json.error || 'Erro'));
    loadContacts();
}

// ═══════════════════════════════════════════════
// REAÇÕES
// ═══════════════════════════════════════════════
function applyReaction(quotedMessageId, emoji) {
    if (!emoji) return;
    // Encontrar a mensagem no DOM pelo quoted_message_id
    // O quoted_message_id é o message_id da Evolution API, que está no data-msg-id como ID do banco
    // Precisamos buscar pelo message_id no DOM — mas temos apenas o ID do banco como data-msg-id
    // Alternativa: buscar todos os elementos e verificar
    const allMsgs = document.querySelectorAll('.wpp-msg[data-msg-id]');
    // Por enquanto, aplicar na última mensagem do mesmo remetente (heurística)
    // Melhor: guardar em um mapa e aplicar por quoted_message_id
    if (!window._reactionMap) window._reactionMap = {};
    window._reactionMap[quotedMessageId] = emoji;
    
    // Tentar aplicar no DOM (se a mensagem tem o message_id como atributo)
    const targetEl = document.querySelector(`[data-message-id="${quotedMessageId}"]`);
    if (targetEl) {
        addReactionBadge(targetEl, emoji);
    }
}

function addReactionBadge(msgEl, emoji) {
    // Remover reação existente se houver
    const existing = msgEl.querySelector('.wpp-reaction');
    if (existing) existing.remove();
    
    const badge = document.createElement('span');
    badge.className = 'wpp-reaction';
    badge.textContent = emoji;
    msgEl.style.position = 'relative';
    msgEl.appendChild(badge);
}

// ═══════════════════════════════════════════════
// UTILITÁRIOS
// ═══════════════════════════════════════════════
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatWhatsApp(text) {
    if (!text) return '';
    let html = escapeHtml(text);
    // Monospace ```text```
    html = html.replace(/```([\s\S]+?)```/g, '<code>$1</code>');
    // Bold *text*
    html = html.replace(/\*((?!\s)([^\n*]+?)(?<!\s))\*/g, '<strong>$1</strong>');
    // Italic _text_
    html = html.replace(/_((?!\s)([^\n_]+?)(?<!\s))_/g, '<em>$1</em>');
    // Strikethrough ~text~
    html = html.replace(/~((?!\s)([^\n~]+?)(?<!\s))~/g, '<del>$1</del>');
    // Mentions @number
    html = html.replace(/@(\d{10,15})/g, '<span class="wpp-mention">@$1</span>');
    // Newlines
    html = html.replace(/\n/g, '<br>');
    return html;
}

function formatTime(datetime) {
    if (!datetime) return '';
    const d = new Date(datetime);
    const now = new Date();
    const diffDays = Math.floor((now - d) / 86400000);

    if (diffDays === 0) return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    if (diffDays === 1) return 'Ontem';
    return d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
}

function formatMessageTime(datetime) {
    if (!datetime) return '';
    return new Date(datetime).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function formatDuration(seconds) {
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return `${m}:${s.toString().padStart(2, '0')}`;
}

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function truncateText(text, max) {
    if (!text) return '';
    return text.length > max ? text.substring(0, max) + '...' : text;
}

// ═══════════════════════════════════════════════
// CHAT INTERNO — Discutir com a equipe sobre este cliente
// ═══════════════════════════════════════════════
async function discussWithTeam() {
    if (!STATE.contactId) { alert('Abra uma conversa primeiro.'); return; }
    try {
        const res = await fetch('/chat-interno/openForContact', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
            body: 'contact_id=' + encodeURIComponent(STATE.contactId),
        });
        const json = await res.json();
        if (json.success && json.redirect) {
            window.location = json.redirect;
        } else {
            alert(json.error || 'Não foi possível abrir a conversa da equipe.');
        }
    } catch (e) {
        alert('Erro de conexão. Tente novamente.');
    }
}
