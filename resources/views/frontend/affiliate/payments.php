<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'pagamentos']) ?>
        <div class="affiliate-panel-content">
            <div class="affiliate-period">
                <span class="period-badge">Todos os tempos</span>
                <button class="btn btn-primary btn-sm">Aplicar</button>
            </div>
            <table class="table">
                <thead><tr><th>ID</th><th>Montante</th><th>Data</th><th>Status</th><th></th></tr></thead>
                <tbody>
                <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= money((float)$p['amount']) ?></td>
                    <td><?= format_datetime($p['paid_at'] ?? $p['created_at']) ?></td>
                    <td><span class="badge badge-success">Pago</span></td>
                    <td><a href="#" class="text-muted" style="font-size:12px">Detalhes</a></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:30px">Nenhum pagamento realizado.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <p class="text-right text-muted" style="font-size:12px;margin-top:10px"><?= count($payments ?? []) ?> resultado<?= count($payments ?? []) !== 1 ? 's' : '' ?></p>
        </div>
    </div>
</section>
