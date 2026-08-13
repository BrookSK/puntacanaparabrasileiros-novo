<div style="margin-bottom:24px;">
    <a href="/admin/cancelamentos" class="btn btn-outline btn-sm">&larr; Voltar para lista</a>
</div>

<?php
$statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
$statusLabels = ['pending' => 'Aguardando Análise', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado'];
$st = $cancellation['status'] ?? 'pending';

$bkStatusColors = ['pending' => 'warning', 'booked' => 'success', 'partially_paid' => 'info', 'completed' => 'success', 'cancelled' => 'danger', 'refunded' => 'secondary'];
$bkStatusLabels = ['pending' => 'Pendente', 'booked' => 'Confirmado', 'partially_paid' => 'Parc. Pago', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
$bst = $booking['status'] ?? 'pending';
?>

<!-- Grid principal -->
<div class="admin-grid-2">
    <!-- Coluna esquerda: Solicitação + Ações -->
    <div>
        <!-- Card da Solicitação -->
        <div class="admin-card summary-card">
            <div class="summary-card-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <h3>Solicitação de Cancelamento</h3>
            </div>
            <div class="summary-card-body">
                <div class="summary-row">
                    <span class="summary-row-label">Status</span>
                    <span class="summary-row-value"><span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Data da Solicitação</span>
                    <span class="summary-row-value"><?= date('d/m/Y \à\s H:i', strtotime($cancellation['created_at'])) ?></span>
                </div>
                <?php if (!empty($cancellation['processed_at'])): ?>
                <div class="summary-row">
                    <span class="summary-row-label">Processado em</span>
                    <span class="summary-row-value"><?= date('d/m/Y \à\s H:i', strtotime($cancellation['processed_at'])) ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span class="summary-row-label">Reembolso</span>
                    <span class="summary-row-value">
                        <?php if ($cancellation['refund_status'] === 'refunded'): ?>
                        <span class="badge badge-info">$<?= number_format((float)($cancellation['refund_amount'] ?? 0), 2) ?></span>
                        <?php elseif ($cancellation['status'] === 'approved' && $cancellation['refund_status'] === 'none'): ?>
                        <span class="badge badge-warning">Pendente</span>
                        <?php else: ?>
                        <span style="color:#94a3b8;">—</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>

            <!-- Motivo do cliente -->
            <div class="summary-card-note">
                <svg class="summary-note-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                <div>
                    <strong>Motivo informado pelo cliente:</strong>
                    <p><?= nl2br(e($cancellation['reason'] ?? '')) ?></p>
                </div>
            </div>

            <?php if (!empty($cancellation['admin_response'])): ?>
            <div style="margin:0 26px 22px;padding:20px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;display:flex;gap:14px;align-items:flex-start;">
                <svg style="color:#16a34a;flex-shrink:0;margin-top:2px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                <div>
                    <strong style="font-size:13px;color:#166534;display:block;margin-bottom:8px;">Resposta enviada ao cliente:</strong>
                    <p style="font-size:13px;color:#475569;line-height:1.7;margin:0;"><?= nl2br(e($cancellation['admin_response'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($cancellation['refund_status'] === 'refunded'): ?>
            <div style="margin:0 26px 22px;padding:20px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;display:flex;gap:14px;align-items:flex-start;">
                <svg style="color:#2563eb;flex-shrink:0;margin-top:2px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                <div>
                    <strong style="font-size:13px;color:#1e40af;display:block;margin-bottom:8px;">Reembolso processado</strong>
                    <p style="font-size:13px;color:#475569;line-height:1.7;margin:0;">
                        Valor: <strong>$<?= number_format((float)($cancellation['refund_amount'] ?? 0), 2) ?></strong>
                        <?php if (!empty($cancellation['refunded_at'])): ?>
                        <br>Data: <?= date('d/m/Y \à\s H:i', strtotime($cancellation['refunded_at'])) ?>
                        <?php endif; ?>
                        <?php if (!empty($cancellation['refund_notes'])): ?>
                        <br>Obs: <?= e($cancellation['refund_notes']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Card de Ações -->
        <?php if ($cancellation['status'] === 'pending'): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <h3>Processar Solicitação</h3>
                    <p class="admin-card-subtitle">Aprovar ou rejeitar este pedido de cancelamento</p>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label for="admin_response" style="font-weight:600;margin-bottom:8px;display:block;">Resposta ao cliente</label>
                <p style="font-size:12px;color:#94a3b8;margin-bottom:8px;">Obrigatório para rejeição. Opcional para aprovação.</p>
                <textarea id="admin_response" class="form-control" rows="3" placeholder="Escreva uma mensagem para o cliente..." style="resize:vertical;"></textarea>
            </div>

            <div style="display:flex;gap:12px;">
                <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/aprovar" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="admin_response" class="admin-response-field" value="">
                    <button type="submit" class="btn btn-primary" onclick="this.form.querySelector('.admin-response-field').value = document.getElementById('admin_response').value">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/></svg>
                            Aprovar Cancelamento
                        </span>
                    </button>
                </form>
                <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/rejeitar" style="display:inline" onsubmit="if(!document.getElementById('admin_response').value.trim()){alert('Informe o motivo da rejeição ao cliente.');return false;} this.querySelector('.admin-response-field').value=document.getElementById('admin_response').value">
                    <?= csrf_field() ?>
                    <input type="hidden" name="admin_response" class="admin-response-field" value="">
                    <button type="submit" class="btn btn-danger">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Rejeitar
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <?php elseif ($cancellation['status'] === 'approved' && $cancellation['refund_status'] === 'none'): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                </div>
                <div>
                    <h3>Processar Reembolso</h3>
                    <p class="admin-card-subtitle">Informe o valor e confirme o reembolso ao cliente</p>
                </div>
            </div>

            <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/reembolsar">
                <?= csrf_field() ?>
                <div class="form-row" style="margin-bottom:16px;">
                    <div class="form-group">
                        <label for="refund_amount" style="font-weight:600;">Valor do Reembolso (USD)</label>
                        <input type="number" step="0.01" min="0.01" name="refund_amount" id="refund_amount" class="form-control" value="<?= number_format((float)($booking['paid_amount'] ?? 0), 2, '.', '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="refund_notes" style="font-weight:600;">Observações (opcional)</label>
                        <input type="text" name="refund_notes" id="refund_notes" class="form-control" placeholder="Ex: Reembolso via PIX, estorno no cartão...">
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    <button type="submit" class="btn btn-primary">
                        <span style="display:inline-flex;align-items:center;gap:6px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Confirmar Reembolso
                        </span>
                    </button>
                    <span style="font-size:12px;color:#94a3b8;">O cliente será notificado por e-mail automaticamente.</span>
                </div>
            </form>
        </div>

        <?php elseif ($cancellation['status'] === 'approved' && $cancellation['refund_status'] === 'refunded'): ?>
        <div class="admin-card" style="background:#f0fdf4;border-color:#bbf7d0;">
            <p style="display:flex;align-items:center;gap:10px;color:#166534;font-weight:600;margin:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Cancelamento aprovado e reembolso processado com sucesso.
            </p>
        </div>

        <?php elseif ($cancellation['status'] === 'rejected'): ?>
        <div class="admin-card" style="background:#fef2f2;border-color:#fecaca;">
            <p style="display:flex;align-items:center;gap:10px;color:#991b1b;font-weight:600;margin:0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Solicitação rejeitada. O cliente foi notificado por e-mail.
            </p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Coluna direita: Dados da Reserva -->
    <div>
        <div class="admin-card summary-card">
            <div class="summary-card-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <h3>Dados da Reserva</h3>
            </div>
            <div class="summary-card-body">
                <div class="summary-row">
                    <span class="summary-row-label">Nº Reserva</span>
                    <span class="summary-row-value"><a href="/admin/reservas/<?= (int)$booking['id'] ?>" style="color:#2563eb;"><?= e($booking['booking_number'] ?? '-') ?></a></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Status</span>
                    <span class="summary-row-value"><span class="badge badge-<?= $bkStatusColors[$bst] ?? 'secondary' ?>"><?= $bkStatusLabels[$bst] ?? $bst ?></span></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Total</span>
                    <span class="summary-row-value" style="font-size:16px;">$<?= number_format((float)($booking['total'] ?? 0), 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Valor Pago</span>
                    <span class="summary-row-value" style="font-size:16px;color:#16a34a;">$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Card do Cliente -->
        <div class="admin-card summary-card" style="margin-top:20px;">
            <div class="summary-card-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <h3>Cliente</h3>
            </div>
            <div class="summary-card-body">
                <div class="summary-row">
                    <span class="summary-row-label">Nome</span>
                    <span class="summary-row-value"><?= e(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">E-mail</span>
                    <span class="summary-row-value" style="font-size:13px;"><?= e($client['email'] ?? '') ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Telefone</span>
                    <span class="summary-row-value"><?= phone_with_flag($booking['billing_phone'] ?? '') ?></span>
                </div>
            </div>
        </div>

        <!-- Serviços -->
        <?php if (!empty($items)): ?>
        <div class="admin-card" style="margin-top:20px;">
            <div class="admin-card-header" style="margin-bottom:16px;padding-bottom:14px;">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <h3>Serviços da Reserva</h3>
                </div>
            </div>
            <?php foreach ($items as $item): ?>
            <div class="card-inner" style="display:flex;align-items:center;gap:12px;">
                <?php if (!empty($item['featured_image'])): ?>
                <img src="<?= e($item['featured_image']) ?>" style="width:48px;height:48px;object-fit:cover;border-radius:8px;" alt="">
                <?php endif; ?>
                <div>
                    <strong style="font-size:14px;color:#1e293b;"><?= e($item['trip_title'] ?? '') ?></strong>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
