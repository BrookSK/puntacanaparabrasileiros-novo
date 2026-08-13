<section class="aff-panel">
    <div class="container">
        <div class="aff-layout">
            <?= partial('affiliate-nav', ['active' => 'visitas']) ?>

            <main class="aff-main">
                <div class="aff-page-header">
                    <div>
                        <h1 class="aff-page-title">Visitas</h1>
                        <p class="aff-page-subtitle">Rastreamento de todas as visitas geradas pelos seus links</p>
                    </div>
                    <div class="aff-header-stat">
                        <span class="aff-header-stat-value"><?= number_format((int)($affiliate['total_visits'] ?? 0)) ?></span>
                        <span class="aff-header-stat-label">Total de visitas</span>
                    </div>
                </div>

                <!-- Table -->
                <div class="aff-card">
                    <div class="aff-table-header">
                        <h3 class="aff-card-title">Histórico de Visitas</h3>
                        <span class="aff-table-count"><?= $total ?? 0 ?> registro<?= ($total ?? 0) !== 1 ? 's' : '' ?></span>
                    </div>
                    <div class="aff-table-wrap">
                        <table class="aff-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Página Visitada</th>
                                    <th>Link Usado</th>
                                    <th>Converteu</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($visits)): ?>
                                <?php foreach ($visits as $v): ?>
                                <?php
                                    // Converter URL em nome amigável
                                    $pageUrl = $v['page_url'] ?? '/';
                                    $pagePath = strtok($pageUrl, '?'); // remove query string
                                    $pageName = 'Home';
                                    if ($pagePath === '/' || $pagePath === '') {
                                        $pageName = 'Home';
                                    } elseif (str_starts_with($pagePath, '/passeios/')) {
                                        $slug = basename($pagePath);
                                        $pageName = 'Passeio: ' . ucwords(str_replace('-', ' ', $slug));
                                    } elseif ($pagePath === '/passeios') {
                                        $pageName = 'Página de Passeios';
                                    } elseif (str_starts_with($pagePath, '/transfers') || str_starts_with($pagePath, '/transfer')) {
                                        $pageName = 'Transfer';
                                    } elseif (str_starts_with($pagePath, '/blog/')) {
                                        $slug = basename($pagePath);
                                        $pageName = 'Blog: ' . ucwords(str_replace('-', ' ', $slug));
                                    } elseif ($pagePath === '/blog') {
                                        $pageName = 'Blog';
                                    } elseif ($pagePath === '/sobre-nos') {
                                        $pageName = 'Sobre Nós';
                                    } elseif ($pagePath === '/contato') {
                                        $pageName = 'Contato';
                                    } else {
                                        $pageName = ucwords(str_replace(['-', '/'], [' ', ' / '], ltrim($pagePath, '/')));
                                    }
                                ?>
                                <tr>
                                    <td class="aff-td-id">#<?= (int)$v['id'] ?></td>
                                    <td><strong><?= e($pageName) ?></strong><br><small style="color:var(--gray);font-size:11px;"><?= e($pagePath) ?></small></td>
                                    <td class="aff-td-url"><span class="aff-url-text"><?= e($v['referrer'] ?: 'Link direto') ?></span></td>
                                    <td><?php if (!empty($v['converted'])): ?><span style="color:var(--text-green);font-weight:600;">Sim</span><?php else: ?><span style="color:var(--gray);">Não</span><?php endif; ?></td>
                                    <td><?= format_datetime($v['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="aff-table-empty">Nenhuma visita registrada ainda.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <?php if (($totalPages ?? 1) > 1): ?>
                    <nav class="pagination" style="margin-top:20px;">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </nav>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</section>
