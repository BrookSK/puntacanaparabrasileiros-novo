<section class="passeios-hero"><div class="container"><div class="passeios-hero-content"><h1>Painel do Afiliado</h1></div></div></section>
<section class="section section-affiliate-panel">
    <div class="container">
        <?= partial('affiliate-nav', ['active' => 'visitas']) ?>
        <div class="affiliate-panel-content">
            <div class="affiliate-period">
                <span class="period-badge">Todos os tempos</span>
                <button class="btn btn-primary btn-sm">Aplicar</button>
            </div>
            <table class="table">
                <thead><tr><th>ID</th><th>Landing URL</th><th>URL referenciador</th><th>Data</th></tr></thead>
                <tbody>
                <?php if (!empty($visits)): ?>
                <?php foreach ($visits as $v): ?>
                <tr>
                    <td><?= (int)$v['id'] ?></td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis"><?= e($v['page_url'] ?? '') ?></td>
                    <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis"><?= e($v['referrer'] ?? '') ?></td>
                    <td><?= format_datetime($v['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:30px">Nenhuma visita registrada.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <p class="text-right text-muted" style="font-size:12px;margin-top:10px"><?= count($visits ?? []) ?> resultados</p>
        </div>
    </div>
</section>
