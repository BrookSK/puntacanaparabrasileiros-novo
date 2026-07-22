<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'comissoes']) ?>
        <div class="affiliate-panel-content">
            <div class="affiliate-period">
                <span class="period-badge">Todos os tempos</span>
                <button class="btn btn-primary btn-sm">Aplicar</button>
            </div>
            <table class="table">
                <thead><tr><th>ID</th><th>Montante</th><th>Referência</th><th>Tipo</th><th>Data</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (!empty($commissions)): ?>
                <?php foreach ($commissions as $c): ?>
                <tr>
                    <td><?= (int)$c['id'] ?></td>
                    <td><?= money((float)$c['amount']) ?></td>
                    <td><?= e($c['booking_id'] ?? '') ?></td>
                    <td>Venda</td>
                    <td><?= format_datetime($c['created_at']) ?></td>
                    <td><span class="badge badge-<?= $c['status'] === 'paid' ? 'success' : ($c['status'] === 'approved' ? 'info' : 'warning') ?>"><?= $c['status'] === 'paid' ? 'Pago' : ($c['status'] === 'approved' ? 'Aprovado' : 'Pendente') ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted" style="padding:30px">Nenhuma comissão ainda.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <p class="text-right text-muted" style="font-size:12px;margin-top:10px"><?= count($commissions ?? []) ?> resultados</p>
        </div>
    </div>
</section>
