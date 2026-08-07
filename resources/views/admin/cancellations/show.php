<div class="page-header">
    <a href="/admin/cancelamentos" class="btn btn-outline btn-sm">&larr; Voltar</a>
</div>

<div class="admin-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:20px;">
    <!-- Info da Solicitação -->
    <div class="card">
        <div class="card-header"><h4>Solicitação de Cancelamento #<?= (int)$cancellation['id'] ?></h4></div>
        <div class="card-body">
            <table class="detail-table">
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>
                        <?php
                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                        $statusLabels = ['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado'];
                        $st = $cancellation['status'] ?? 'pending';
                        ?>
                        <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Data da Solicitação:</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($cancellation['created_at'])) ?></td>
                </tr>
                <tr>
                    <td><strong>Motivo do Cliente:</strong></td>
                    <td style="white-space:pre-line;"><?= e($cancellation['reason'] ?? '') ?></td>
                </tr>
                <?php if (!empty($cancellation['admin_response'])): ?>
                <tr>
                    <td><strong>Resposta Admin:</strong></td>
                    <td style="white-space:pre-line;"><?= e($cancellation['admin_response']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($cancellation['processed_at'])): ?>
                <tr>
                    <td><strong>Processado em:</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($cancellation['processed_at'])) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><strong>Reembolso:</strong></td>
                    <td>
                        <?php if ($cancellation['refund_status'] === 'refunded'): ?>
                        <span class="badge badge-info">Reembolsado: $<?= number_format((float)($cancellation['refund_amount'] ?? 0), 2) ?></span>
                        <?php if (!empty($cancellation['refunded_at'])): ?>
                        <br><small style="color:#636e72;">em <?= date('d/m/Y H:i', strtotime($cancellation['refunded_at'])) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($cancellation['refund_notes'])): ?>
                        <br><small style="color:#636e72;">Obs: <?= e($cancellation['refund_notes']) ?></small>
                        <?php endif; ?>
                        <?php else: ?>
                        <span style="color:#aaa;">Nenhum</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Info do Booking/Cliente -->
    <div class="card">
        <div class="card-header"><h4>Dados da Reserva</h4></div>
        <div class="card-body">
            <table class="detail-table">
                <tr>
                    <td><strong>Nº Reserva:</strong></td>
                    <td><a href="/admin/reservas/<?= (int)$booking['id'] ?>"><?= e($booking['booking_number'] ?? '-') ?></a></td>
                </tr>
                <tr>
                    <td><strong>Cliente:</strong></td>
                    <td><?= e(($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '')) ?></td>
                </tr>
                <tr>
                    <td><strong>E-mail:</strong></td>
                    <td><?= e($client['email'] ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Telefone:</strong></td>
                    <td><?= e($booking['billing_phone'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td><strong>Total da Reserva:</strong></td>
                    <td>$<?= number_format((float)($booking['total'] ?? 0), 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Valor Pago:</strong></td>
                    <td>$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Status da Reserva:</strong></td>
                    <td>
                        <?php
                        $bkStatusColors = ['pending' => 'warning', 'booked' => 'success', 'partially_paid' => 'info', 'completed' => 'success', 'cancelled' => 'danger', 'refunded' => 'secondary'];
                        $bkStatusLabels = ['pending' => 'Pendente', 'booked' => 'Confirmado', 'partially_paid' => 'Parc. Pago', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
                        $bst = $booking['status'] ?? 'pending';
                        ?>
                        <span class="badge badge-<?= $bkStatusColors[$bst] ?? 'secondary' ?>"><?= $bkStatusLabels[$bst] ?? $bst ?></span>
                    </td>
                </tr>
                <?php if (!empty($items)): ?>
                <tr>
                    <td><strong>Serviços:</strong></td>
                    <td>
                        <?php foreach ($items as $item): ?>
                        <div style="font-size:13px;margin-bottom:4px;">• <?= e($item['trip_title'] ?? '') ?></div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- Ações -->
<div style="margin-top:24px;">
    <?php if ($cancellation['status'] === 'pending'): ?>
    <!-- Aprovar ou Rejeitar -->
    <div class="card">
        <div class="card-header"><h4>Processar Solicitação</h4></div>
        <div class="card-body">
            <div class="form-group">
                <label for="admin_response">Resposta ao cliente (obrigatório para rejeição, opcional para aprovação):</label>
                <textarea id="admin_response" class="form-control" rows="3" placeholder="Escreva uma mensagem para o cliente..."></textarea>
            </div>
            <div style="display:flex;gap:12px;margin-top:16px;">
                <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/aprovar" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="admin_response" class="admin-response-field" value="">
                    <button type="submit" class="btn btn-primary" onclick="this.form.querySelector('.admin-response-field').value = document.getElementById('admin_response').value">Aprovar Cancelamento</button>
                </form>
                <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/rejeitar" style="display:inline" onsubmit="if(!document.getElementById('admin_response').value.trim()){alert('Informe o motivo da rejeição.');return false;} this.querySelector('.admin-response-field').value=document.getElementById('admin_response').value">
                    <?= csrf_field() ?>
                    <input type="hidden" name="admin_response" class="admin-response-field" value="">
                    <button type="submit" class="btn btn-danger">Rejeitar Cancelamento</button>
                </form>
            </div>
        </div>
    </div>

    <?php elseif ($cancellation['status'] === 'approved' && $cancellation['refund_status'] === 'none'): ?>
    <!-- Reembolsar -->
    <div class="card">
        <div class="card-header"><h4>Processar Reembolso</h4></div>
        <div class="card-body">
            <form method="POST" action="/admin/cancelamentos/<?= (int)$cancellation['id'] ?>/reembolsar">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="refund_amount">Valor do Reembolso (USD)</label>
                        <input type="number" step="0.01" min="0.01" name="refund_amount" id="refund_amount" class="form-control" value="<?= number_format((float)($booking['paid_amount'] ?? 0), 2, '.', '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="refund_notes">Observações (opcional)</label>
                        <input type="text" name="refund_notes" id="refund_notes" class="form-control" placeholder="Ex: Reembolso via PIX, estorno no cartão...">
                    </div>
                </div>
                <div style="display:flex;gap:12px;margin-top:12px;">
                    <button type="submit" class="btn btn-primary">Confirmar Reembolso</button>
                    <span style="font-size:13px;color:#636e72;align-self:center;">O cliente será notificado por e-mail.</span>
                </div>
            </form>
        </div>
    </div>

    <?php elseif ($cancellation['status'] === 'approved' && $cancellation['refund_status'] === 'refunded'): ?>
    <div class="alert alert-success">Cancelamento aprovado e reembolso processado.</div>

    <?php elseif ($cancellation['status'] === 'rejected'): ?>
    <div class="alert alert-info">Esta solicitação foi rejeitada. O cliente foi notificado.</div>
    <?php endif; ?>
</div>
