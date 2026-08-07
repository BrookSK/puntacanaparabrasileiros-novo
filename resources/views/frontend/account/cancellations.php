<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Cancelamentos</h2>
        <?php if (empty($bookings)): ?>
        <div class="empty-state"><p>Nenhuma reserva encontrada.</p><a href="/passeios" class="btn btn-primary">Ver Passeios</a></div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Número</th><th>Serviço</th><th>Data</th><th>Total</th><th>Pago</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= e($b['booking_number'] ?? '#' . (int)$b['id']) ?></td>
                <td style="font-size:13px;color:#374151;max-width:200px;"><?= e($b['trip_title']) ?></td>
                <td><?= $b['trip_date'] ? format_date($b['trip_date']) : format_date($b['created_at']) ?></td>
                <td><?= money((float)($b['total'] ?? 0)) ?></td>
                <td><?= money((float)($b['paid_amount'] ?? 0)) ?></td>
                <td><span class="badge badge-<?= booking_status_class($b['status']) ?>"><?= booking_status_label($b['status']) ?></span></td>
                <td>
                    <?php if (in_array($b['status'], ['booked', 'pending', 'partially_paid']) && empty($b['cancellation_request_id'])): ?>
                    <button type="button" class="btn btn-sm btn-danger" onclick="openCancelModal(<?= (int)$b['id'] ?>, '<?= e($b['booking_number'] ?? '#' . (int)$b['id']) ?>')">Cancelar</button>

                    <?php elseif (!empty($b['cancellation_request_id'])): ?>
                        <?php if ($b['cancellation_status'] === 'pending'): ?>
                        <span class="badge badge-warning" style="cursor:pointer" onclick="openDetailModal(this)" data-status="pending" data-reason="<?= e($b['cancellation_reason'] ?? '') ?>">Aguardando</span>

                        <?php elseif ($b['cancellation_status'] === 'approved'): ?>
                        <span class="badge badge-success" style="cursor:pointer" onclick="openDetailModal(this)" data-status="approved" data-reason="<?= e($b['cancellation_reason'] ?? '') ?>" data-response="<?= e($b['admin_response'] ?? '') ?>" data-refund="<?= e($b['refund_status'] ?? 'none') ?>" data-refund-amount="<?= number_format((float)($b['refund_amount'] ?? 0), 2, '.', '') ?>">Aprovado</span>

                        <?php elseif ($b['cancellation_status'] === 'rejected'): ?>
                        <span class="badge badge-danger" style="cursor:pointer" onclick="openDetailModal(this)" data-status="rejected" data-reason="<?= e($b['cancellation_reason'] ?? '') ?>" data-response="<?= e($b['admin_response'] ?? '') ?>">Negado</span>
                        <?php endif; ?>

                    <?php elseif ($b['status'] === 'cancelled'): ?>
                    <span class="badge badge-danger">Cancelado</span>
                    <?php elseif ($b['status'] === 'refunded'): ?>
                    <span class="badge badge-info">Reembolsado</span>
                    <?php else: ?>
                    <span class="btn btn-sm btn-outline" style="pointer-events:none;opacity:.6;">Indisponível</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
        <nav class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Solicitar Cancelamento -->
<div id="cancelModal" class="modal-overlay modal-hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Solicitar Cancelamento</h3>
            <button type="button" class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>
        <form method="POST" action="/minha-conta/cancelamentos/solicitar" id="cancelForm">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" id="cancelBookingId" value="">
            <div class="modal-body">
                <p class="modal-subtitle">Reserva: <strong id="cancelBookingNumber"></strong></p>
                <div class="form-group">
                    <label for="cancellation_reason">Motivo do cancelamento <span style="color:#e74c3c">*</span></label>
                    <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="4" required placeholder="Informe o motivo pelo qual deseja cancelar esta reserva..."></textarea>
                </div>
                <p style="font-size:12px;color:#636e72;margin-top:12px;">Após o envio, sua solicitação será analisada pela equipe. Você receberá uma resposta por e-mail.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeCancelModal()">Voltar</button>
                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Detalhes do Cancelamento -->
<div id="detailModal" class="modal-overlay modal-hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Detalhes do Cancelamento</h3>
            <button type="button" class="modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="detailContent"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeDetailModal()">Fechar</button>
        </div>
    </div>
</div>

<script>
// Modal de solicitar cancelamento
function openCancelModal(bookingId, bookingNumber) {
    document.getElementById('cancelBookingId').value = bookingId;
    document.getElementById('cancelBookingNumber').textContent = bookingNumber;
    document.getElementById('cancellation_reason').value = '';
    document.getElementById('cancelModal').classList.remove('modal-hidden');
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('modal-hidden');
    document.body.style.overflow = '';
}
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});

// Modal de detalhes
function openDetailModal(el) {
    var status = el.getAttribute('data-status');
    var reason = el.getAttribute('data-reason');
    var response = el.getAttribute('data-response') || '';
    var refund = el.getAttribute('data-refund') || 'none';
    var refundAmount = el.getAttribute('data-refund-amount') || '0.00';

    var html = '';

    var statusLabel = {pending: 'Aguardando Análise', approved: 'Aprovado', rejected: 'Não Autorizado'};
    var statusColor = {pending: '#d97706', approved: '#1B6F00', rejected: '#dc2626'};
    html += '<div style="margin-bottom:16px;"><strong>Status:</strong> <span style="color:' + statusColor[status] + ';font-weight:600;">' + statusLabel[status] + '</span></div>';

    html += '<div style="margin-bottom:16px;"><strong>Seu motivo:</strong><div style="margin-top:6px;padding:12px 16px;background:#f8f9fa;border-radius:8px;font-size:13px;white-space:pre-line;line-height:1.5;">' + escapeHtml(reason) + '</div></div>';

    if (status === 'approved' && response) {
        html += '<div style="margin-bottom:16px;"><strong>Resposta da equipe:</strong><div style="margin-top:6px;padding:12px 16px;background:#f0fdf4;border-left:4px solid #1B6F00;border-radius:6px;font-size:13px;white-space:pre-line;line-height:1.5;">' + escapeHtml(response) + '</div></div>';
    } else if (status === 'rejected' && response) {
        html += '<div style="margin-bottom:16px;"><strong>Motivo da recusa:</strong><div style="margin-top:6px;padding:12px 16px;background:#fef2f2;border-left:4px solid #dc2626;border-radius:6px;font-size:13px;white-space:pre-line;line-height:1.5;">' + escapeHtml(response) + '</div></div>';
    } else if (status === 'pending') {
        html += '<div style="padding:12px 16px;background:#fffbeb;border-radius:8px;font-size:13px;color:#92400e;line-height:1.5;">Sua solicitação está sendo analisada pela equipe. Você receberá uma resposta por e-mail.</div>';
    }

    if (status === 'approved' && refund === 'refunded') {
        html += '<div style="margin-top:16px;padding:12px 16px;background:#eff6ff;border-radius:8px;font-size:13px;color:#1e40af;"><strong>Reembolso processado:</strong> $' + refundAmount + '</div>';
    } else if (status === 'approved' && refund === 'none') {
        html += '<div style="margin-top:16px;padding:12px 16px;background:#fffbeb;border-radius:8px;font-size:13px;color:#92400e;">Cancelamento aprovado. Reembolso em processamento.</div>';
    }

    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('detailModal').classList.remove('modal-hidden');
    document.body.style.overflow = 'hidden';
}
function closeDetailModal() {
    document.getElementById('detailModal').classList.add('modal-hidden');
    document.body.style.overflow = '';
}
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});

function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
        closeDetailModal();
    }
});
</script>
