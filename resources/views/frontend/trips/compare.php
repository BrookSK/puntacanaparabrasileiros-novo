<section class="section" style="padding-top:32px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:28px;">
            <h1 style="font-size:1.8rem;margin-bottom:8px;">Comparar Passeios</h1>
            <p style="color:#636e72;font-size:15px;">Selecione até 4 passeios e veja lado a lado o que cada um oferece.</p>
        </div>

        <!-- Seletor de passeios -->
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin-bottom:28px;">
            <form method="GET" action="/comparar-passeios" id="compareForm">
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                    <?php $sel = $selectedIds[$i] ?? ''; ?>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#475569;display:block;margin-bottom:4px;">Passeio <?= $i + 1 ?></label>
                        <select name="trip_<?= $i ?>" class="form-control compare-select" style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;">
                            <option value="">— Selecione —</option>
                            <?php foreach ($allTrips as $t): ?>
                            <option value="<?= (int)$t['id'] ?>" <?= (int)$sel === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endfor; ?>
                </div>
                <div style="text-align:center;margin-top:16px;">
                    <button type="submit" class="btn btn-primary" style="padding:12px 32px;">Comparar</button>
                </div>
            </form>
        </div>

        <?php if (empty($compareTrips)): ?>
        <div style="text-align:center;padding:40px 20px;color:#94a3b8;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:12px;opacity:0.4;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <p style="margin:0;font-size:15px;">Selecione ao menos um passeio acima para começar a comparação.</p>
        </div>
        <?php else: ?>

        <!-- Tabela comparativa -->
        <div style="overflow-x:auto;">
        <table class="compare-table" style="width:100%;border-collapse:collapse;min-width:<?= 200 + count($compareTrips) * 220 ?>px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:14px;background:#f8fafc;border-bottom:2px solid #e2e8f0;width:200px;position:sticky;left:0;"></th>
                    <?php foreach ($compareTrips as $trip): ?>
                    <th style="padding:14px;background:#f8fafc;border-bottom:2px solid #e2e8f0;text-align:center;vertical-align:top;">
                        <?php if (!empty($trip['featured_image'])): ?>
                        <img src="<?= e($trip['featured_image']) ?>" alt="<?= e($trip['title']) ?>" style="width:100%;max-width:180px;height:110px;object-fit:cover;border-radius:8px;margin-bottom:8px;">
                        <?php endif; ?>
                        <div style="font-size:14px;font-weight:700;color:#1e293b;line-height:1.3;"><?= e($trip['title']) ?></div>
                        <a href="/passeios/<?= e($trip['slug']) ?>" style="font-size:12px;color:#3b82f6;text-decoration:none;">Ver passeio →</a>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Preço -->
                <tr>
                    <td style="padding:12px 14px;font-weight:600;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;">💰 Preço a partir de</td>
                    <?php foreach ($compareTrips as $trip): ?>
                    <td style="padding:12px 14px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:16px;font-weight:700;color:#059669;">
                        <?= $trip['min_price'] > 0 ? money((float)$trip['min_price']) : 'Consultar' ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Duração -->
                <tr>
                    <td style="padding:12px 14px;font-weight:600;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;">⏱️ Duração</td>
                    <?php foreach ($compareTrips as $trip): ?>
                    <td style="padding:12px 14px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:14px;">
                        <?= !empty($trip['duration']) ? e($trip['duration']) . ' ' . (($trip['duration_unit'] ?? 'hours') === 'hours' ? 'horas' : 'dias') : '—' ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Dificuldade -->
                <tr>
                    <td style="padding:12px 14px;font-weight:600;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;">⚡ Dificuldade</td>
                    <?php
                    $diffMap = ['easy' => 'Fácil', 'moderate' => 'Moderado', 'hard' => 'Difícil'];
                    foreach ($compareTrips as $trip):
                    ?>
                    <td style="padding:12px 14px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:14px;">
                        <?= $diffMap[$trip['difficulty'] ?? ''] ?? '—' ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Avaliação -->
                <tr>
                    <td style="padding:12px 14px;font-weight:600;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;">⭐ Avaliação</td>
                    <?php foreach ($compareTrips as $trip): ?>
                    <td style="padding:12px 14px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:14px;">
                        <?= $trip['rating'] > 0 ? number_format((float)$trip['rating'], 1) . ' / 5' : 'Novo' ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <!-- Passageiros -->
                <tr>
                    <td style="padding:12px 14px;font-weight:600;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;">👥 Passageiros</td>
                    <?php foreach ($compareTrips as $trip): ?>
                    <td style="padding:12px 14px;text-align:center;border-bottom:1px solid #f1f5f9;font-size:14px;">
                        <?php
                        $minP = (int)($trip['min_pax'] ?? 0);
                        $maxP = (int)($trip['max_pax'] ?? 0);
                        if ($minP && $maxP) echo $minP . ' a ' . $maxP;
                        elseif ($minP) echo 'A partir de ' . $minP;
                        else echo '—';
                        ?>
                    </td>
                    <?php endforeach; ?>
                </tr>

                <!-- Cabeçalho: O que inclui -->
                <tr>
                    <td colspan="<?= count($compareTrips) + 1 ?>" style="padding:14px;background:#f0fdf4;font-weight:700;color:#166534;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">✔️ O que inclui</td>
                </tr>
                <?php if (empty($allIncludeItems)): ?>
                <tr>
                    <td colspan="<?= count($compareTrips) + 1 ?>" style="padding:12px 14px;color:#94a3b8;font-size:13px;text-align:center;">Nenhum item de inclusão cadastrado nos passeios selecionados.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($allIncludeItems as $key => $itemLabel): ?>
                <tr>
                    <td style="padding:10px 14px;color:#475569;font-size:13px;border-bottom:1px solid #f1f5f9;background:#fff;position:sticky;left:0;"><?= e($itemLabel) ?></td>
                    <?php foreach ($compareTrips as $trip): ?>
                    <?php
                    $has = false;
                    foreach ($trip['includes_arr'] as $inc) {
                        if (mb_strtolower(trim($inc)) === $key) { $has = true; break; }
                    }
                    ?>
                    <td style="padding:10px 14px;text-align:center;border-bottom:1px solid #f1f5f9;">
                        <?php if ($has): ?>
                        <span style="color:#059669;font-size:18px;font-weight:700;">✔</span>
                        <?php else: ?>
                        <span style="color:#e2e8f0;font-size:18px;">✖</span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Submeter automaticamente ao mudar seleção
document.querySelectorAll('.compare-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
        // Coletar valores únicos não-vazios
        var ids = [];
        document.querySelectorAll('.compare-select').forEach(function(s) {
            if (s.value && ids.indexOf(s.value) === -1) ids.push(s.value);
        });
        window.location.href = '/comparar-passeios?ids=' + ids.join(',');
    });
});
</script>
