<?php
$dayNames = ['0' => 'Dom', '1' => 'Seg', '2' => 'Ter', '3' => 'Qua', '4' => 'Qui', '5' => 'Sex', '6' => 'Sáb'];
$dayFull = ['0' => 'Domingo', '1' => 'Segunda', '2' => 'Terça', '3' => 'Quarta', '4' => 'Quinta', '5' => 'Sexta', '6' => 'Sábado'];
$tripId = (int) $trip['id'];
?>

<div class="card-header">
    <div>
        <h2>Dias e Horários — <?= e($trip['title']) ?></h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Controle quando este passeio pode ser vendido e quais horários o cliente vê.</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="/admin/passeios/<?= $tripId ?>/precos" class="btn btn-outline">Preços</a>
        <a href="/admin/passeios/<?= $tripId ?>/editar" class="btn btn-outline">&larr; Voltar ao Passeio</a>
    </div>
</div>

<?php if (!$ready): ?>
<div class="admin-card" style="background:#fef2f2;border:1px solid #fecaca;">
    <p style="margin:0;color:#991b1b;"><strong>Atenção:</strong> o banco de dados ainda não tem as tabelas desta tela (migration <code>036_trip_availability_and_transfer_lead.sql</code>). Até ela ser executada, nada aqui é salvo.</p>
</div>
<?php endif; ?>

<form method="POST" action="/admin/passeios/<?= $tripId ?>/disponibilidade" class="admin-form" id="availabilityForm">
    <?= csrf_field() ?>

    <!-- 1. Dias fechados -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="14" x2="15" y2="20"/><line x1="15" y1="14" x2="9" y2="20"/></svg></div>
            <div><h3>1. Dias da semana em que o passeio NÃO funciona</h3><p class="admin-card-subtitle">Marque os dias fechados. O cliente não consegue escolher esses dias no calendário.</p></div>
        </div>
        <div class="av-days">
            <?php foreach ($dayFull as $k => $name): ?>
            <label class="av-day av-day-closed">
                <input type="checkbox" name="closed_weekdays[]" value="<?= $k ?>" <?= in_array((int)$k, $closedWeekdays, true) ? 'checked' : '' ?>>
                <span><?= $name ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <small class="form-hint">Exemplo: Coco Bongo não abre às segundas → marque "Segunda". Nada marcado = funciona todos os dias.</small>
    </div>

    <!-- 2. Exceções por data -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><h3>2. Datas especiais (fechar ou abrir excepcionalmente)</h3><p class="admin-card-subtitle">Vale por cima da regra dos dias da semana.</p></div>
        </div>
        <div id="exceptionsList">
            <?php foreach ($exceptions as $i => $ex): ?>
            <div class="av-row card-inner">
                <div class="form-row" style="grid-template-columns: 1.3fr 1fr 1fr 2fr auto; gap:12px; align-items:end;">
                    <div class="form-group" style="margin:0"><label>O que fazer</label>
                        <select name="exceptions[<?= $i ?>][type]" class="form-control">
                            <option value="closed" <?= $ex['exception_type'] === 'closed' ? 'selected' : '' ?>>Fechar (não vender)</option>
                            <option value="open" <?= $ex['exception_type'] === 'open' ? 'selected' : '' ?>>Abrir excepcionalmente</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0"><label>De</label><input type="date" name="exceptions[<?= $i ?>][date_start]" value="<?= e($ex['date_start']) ?>" class="form-control" required></div>
                    <div class="form-group" style="margin:0"><label>Até (opcional)</label><input type="date" name="exceptions[<?= $i ?>][date_end]" value="<?= e($ex['date_end']) ?>" class="form-control"></div>
                    <div class="form-group" style="margin:0"><label>Observação (só para vocês)</label><input type="text" name="exceptions[<?= $i ?>][note]" value="<?= e($ex['note'] ?? '') ?>" class="form-control" placeholder="Ex: Fornecedor avisou que está fechado"></div>
                    <button type="button" class="btn btn-sm btn-danger av-remove">Remover</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline" id="addException" style="margin-top:10px;">+ Adicionar data especial</button>
        <small class="form-hint" style="display:block;margin-top:8px;">Exemplos: Coco Bongo abre na segunda 28/12/2026 → "Abrir excepcionalmente", De 28/12/2026. Fechado no Natal → "Fechar", De 24/12 Até 25/12. Datas que já passaram somem sozinhas da lista.</small>
    </div>

    <!-- 3. Horários de saída -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div><h3>3. Horários de saída do passeio</h3><p class="admin-card-subtitle">O cliente só vê os horários do dia que ele escolheu.</p></div>
        </div>
        <div class="av-help">
            <p><strong>Como funciona:</strong></p>
            <ul>
                <li><strong>Nenhum horário cadastrado</strong> → o cliente escolhe só a data (sem escolher horário).</li>
                <li><strong>1 horário</strong> (Saona, Santo Domingo, noturnos) → é escolhido automaticamente, o cliente não precisa clicar.</li>
                <li><strong>2 ou mais horários</strong> (Golfinhos, Buggy, Party Boat) → o cliente escolhe um.</li>
                <li>Desmarque os dias em que aquele horário não acontece. Ex.: Golfinhos na piscina sem o 1º horário → desmarque todos os dias do 1º horário (ou apague a linha).</li>
                <li>O horário de busca no hotel continua vindo de <a href="/admin/passeios/<?= $tripId ?>/editar">Horários por Hotel</a>. Cadastre os horários de busca de cada hotel <strong>na mesma ordem</strong> das saídas (1º, 2º, 3º) que o site mostra só a busca da saída escolhida.</li>
            </ul>
        </div>
        <div id="timesList">
            <?php foreach ($departureTimes as $i => $t): ?>
            <div class="av-row card-inner">
                <div class="form-row" style="grid-template-columns: 120px 1.2fr 3fr auto; gap:12px; align-items:end;">
                    <div class="form-group" style="margin:0"><label>Horário</label><input type="time" name="times[<?= $i ?>][time]" value="<?= e($t['time']) ?>" class="form-control" required></div>
                    <div class="form-group" style="margin:0"><label>Nome (opcional)</label><input type="text" name="times[<?= $i ?>][label]" value="<?= e($t['label'] ?? '') ?>" class="form-control" placeholder="Ex: Manhã"></div>
                    <div class="form-group" style="margin:0"><label>Dias em que este horário existe</label>
                        <div class="av-days av-days-sm">
                            <?php foreach ($dayNames as $k => $n): ?>
                            <label class="av-day"><input type="checkbox" name="times[<?= $i ?>][weekdays][]" value="<?= $k ?>" <?= in_array((int)$k, $t['weekday_list'], true) ? 'checked' : '' ?>><span><?= $n ?></span></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger av-remove">Remover</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline" id="addTime" style="margin-top:10px;">+ Adicionar horário</button>
    </div>

    <div class="form-actions" style="border-top:none;padding-top:0;">
        <button type="submit" class="btn btn-primary">Salvar dias e horários</button>
        <a href="/admin/passeios" class="btn btn-outline">Cancelar</a>
    </div>
</form>

<style>
.av-days { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:8px; }
.av-day { display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; cursor:pointer; font-size:13px; user-select:none; background:#fff; }
.av-day input { margin:0; }
.av-day:has(input:checked) { border-color:#1B6F00; background:#f0fdf4; color:#14532d; font-weight:600; }
.av-day-closed:has(input:checked) { border-color:#dc2626; background:#fef2f2; color:#991b1b; }
.av-days-sm .av-day { padding:6px 9px; font-size:12px; }
.av-row { margin-bottom:10px; }
.av-help { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px; margin-bottom:14px; font-size:13px; color:#334155; }
.av-help p { margin:0 0 6px; }
.av-help ul { margin:0; padding-left:18px; }
.av-help li { margin-bottom:4px; }
@media (max-width: 900px) { #availabilityForm .form-row { grid-template-columns: 1fr !important; } }
</style>

<script>
(function () {
    var dayNames = <?= json_encode($dayNames, JSON_UNESCAPED_UNICODE) ?>;
    var exIdx = <?= count($exceptions) ?> + 100;
    var tIdx = <?= count($departureTimes) ?> + 100;

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('av-remove')) {
            e.target.closest('.av-row').remove();
        }
    });

    document.getElementById('addException').addEventListener('click', function () {
        var i = exIdx++;
        var div = document.createElement('div');
        div.className = 'av-row card-inner';
        div.innerHTML =
            '<div class="form-row" style="grid-template-columns: 1.3fr 1fr 1fr 2fr auto; gap:12px; align-items:end;">' +
            '<div class="form-group" style="margin:0"><label>O que fazer</label><select name="exceptions[' + i + '][type]" class="form-control"><option value="closed">Fechar (não vender)</option><option value="open">Abrir excepcionalmente</option></select></div>' +
            '<div class="form-group" style="margin:0"><label>De</label><input type="date" name="exceptions[' + i + '][date_start]" class="form-control" required></div>' +
            '<div class="form-group" style="margin:0"><label>Até (opcional)</label><input type="date" name="exceptions[' + i + '][date_end]" class="form-control"></div>' +
            '<div class="form-group" style="margin:0"><label>Observação (só para vocês)</label><input type="text" name="exceptions[' + i + '][note]" class="form-control" placeholder="Ex: Fornecedor avisou que está fechado"></div>' +
            '<button type="button" class="btn btn-sm btn-danger av-remove">Remover</button></div>';
        document.getElementById('exceptionsList').appendChild(div);
    });

    document.getElementById('addTime').addEventListener('click', function () {
        var i = tIdx++;
        var days = '';
        Object.keys(dayNames).forEach(function (k) {
            days += '<label class="av-day"><input type="checkbox" name="times[' + i + '][weekdays][]" value="' + k + '" checked><span>' + dayNames[k] + '</span></label>';
        });
        var div = document.createElement('div');
        div.className = 'av-row card-inner';
        div.innerHTML =
            '<div class="form-row" style="grid-template-columns: 120px 1.2fr 3fr auto; gap:12px; align-items:end;">' +
            '<div class="form-group" style="margin:0"><label>Horário</label><input type="time" name="times[' + i + '][time]" class="form-control" required></div>' +
            '<div class="form-group" style="margin:0"><label>Nome (opcional)</label><input type="text" name="times[' + i + '][label]" class="form-control" placeholder="Ex: Manhã"></div>' +
            '<div class="form-group" style="margin:0"><label>Dias em que este horário existe</label><div class="av-days av-days-sm">' + days + '</div></div>' +
            '<button type="button" class="btn btn-sm btn-danger av-remove">Remover</button></div>';
        document.getElementById('timesList').appendChild(div);
    });
})();
</script>
