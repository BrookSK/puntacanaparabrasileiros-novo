<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('agency-nav', ['agency' => $agency, 'active' => 'visitas']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Visitas</h1>
                        <p class="aff-page-subtitle">Acessos gerados pelo seu link de indicação</p>
                    </div>
                    <div class="aff-header-stat" style="text-align:right;">
                        <span style="display:block;font-size:28px;font-weight:700;color:#1C2011;"><?= number_format((int)($total ?? 0)) ?></span>
                        <span style="font-size:12px;color:#636e72;">Total de visitas</span>
                    </div>
                </div>

                <div class="aff-card">
                    <h3 class="aff-card-title">Histórico de Visitas</h3>
                    <div class="aff-table-wrap" style="margin-top:14px;">
                        <table class="aff-table">
                            <thead>
                                <tr><th>ID</th><th>Página visitada</th><th>Origem</th><th>Data</th></tr>
                            </thead>
                            <tbody>
                                <?php if (empty($visits)): ?>
                                <tr><td colspan="4" class="aff-table-empty">Nenhuma visita registrada ainda. Compartilhe seu link para começar a receber acessos.</td></tr>
                                <?php else: ?>
                                <?php foreach ($visits as $v): ?>
                                <?php
                                    $pageUrl = $v['page_url'] ?? '/';
                                    $pagePath = strtok($pageUrl, '?');
                                    $pageName = 'Home';
                                    if ($pagePath === '/' || $pagePath === '') {
                                        $pageName = 'Home';
                                    } elseif (str_starts_with($pagePath, '/passeios/')) {
                                        $pageName = 'Passeio: ' . ucwords(str_replace('-', ' ', basename($pagePath)));
                                    } elseif ($pagePath === '/passeios') {
                                        $pageName = 'Página de Passeios';
                                    } elseif (str_starts_with($pagePath, '/transfer')) {
                                        $pageName = 'Transfer';
                                    } elseif (str_starts_with($pagePath, '/blog/')) {
                                        $pageName = 'Blog: ' . ucwords(str_replace('-', ' ', basename($pagePath)));
                                    } elseif ($pagePath === '/blog') {
                                        $pageName = 'Blog';
                                    } else {
                                        $pageName = ucwords(str_replace(['-', '/'], [' ', ' / '], ltrim($pagePath, '/')));
                                    }
                                ?>
                                <tr>
                                    <td class="aff-td-id">#<?= (int)$v['id'] ?></td>
                                    <td><strong><?= e($pageName) ?></strong><br><small style="color:#94a3b8;font-size:11px;"><?= e($pagePath) ?></small></td>
                                    <td><?= e($v['referrer'] ?: 'Link direto') ?></td>
                                    <td style="color:#636e72;"><?= date('d/m/Y H:i', strtotime($v['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (($totalPages ?? 1) > 1): ?>
                    <?php
                        $cur = (int)($currentPage ?? 1);
                        $tp = (int)$totalPages;
                        $window = 2;
                        $start = max(1, $cur - $window);
                        $end = min($tp, $cur + $window);
                    ?>
                    <nav class="pagination" style="margin-top:20px;display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                        <?php if ($cur > 1): ?><a href="?page=<?= $cur - 1 ?>" class="page-link">&laquo;</a><?php endif; ?>
                        <?php if ($start > 1): ?>
                            <a href="?page=1" class="page-link">1</a>
                            <?php if ($start > 2): ?><span style="color:#94a3b8;">…</span><?php endif; ?>
                        <?php endif; ?>
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= $i === $cur ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($end < $tp): ?>
                            <?php if ($end < $tp - 1): ?><span style="color:#94a3b8;">…</span><?php endif; ?>
                            <a href="?page=<?= $tp ?>" class="page-link"><?= $tp ?></a>
                        <?php endif; ?>
                        <?php if ($cur < $tp): ?><a href="?page=<?= $cur + 1 ?>" class="page-link">&raquo;</a><?php endif; ?>
                    </nav>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</section>
