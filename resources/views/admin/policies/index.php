<div class="card-header">
    <div>
        <h2>Políticas e Câmbio</h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Câmbio do dólar, política de pagamento, cancelamento e páginas legais do site.</p>
    </div>
</div>

<form method="POST" action="/admin/politicas" class="admin-form">
    <?= csrf_field() ?>

    <!-- Câmbio -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg></div>
            <div><h3>Taxa de câmbio do dólar</h3><p class="admin-card-subtitle">Usada para converter o valor em dólar para reais no pagamento via Pix.</p></div>
        </div>
        <div class="form-row" style="grid-template-columns: 220px 1fr; gap:16px; align-items:end;">
            <div class="form-group" style="margin:0">
                <label>US$ 1 = R$</label>
                <input type="text" inputmode="decimal" name="exchange_rate" value="<?= e(number_format((float)$exchangeRate, 2, ',', '')) ?>" class="form-control" placeholder="6,00" style="font-size:18px;font-weight:700;">
            </div>
            <p class="text-muted" style="margin:0;font-size:13px;">Exemplo: digite <strong>6,00</strong> para US$ 1 = R$ 6,00. Vale para os próximos pagamentos (os já gerados não mudam).</p>
        </div>
    </div>

    <!-- Transfer -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/></svg></div>
            <div><h3>Transfer de saída (hotel → aeroporto)</h3><p class="admin-card-subtitle">Quantas horas antes do voo o motorista busca o cliente.</p></div>
        </div>
        <div class="form-row" style="grid-template-columns: 220px 1fr; gap:16px; align-items:end;">
            <div class="form-group" style="margin:0">
                <label>Horas antes do voo (padrão)</label>
                <input type="text" inputmode="decimal" name="lead_hours" value="<?= e(rtrim(rtrim(number_format($leadMinutes / 60, 2, ',', ''), '0'), ',')) ?>" class="form-control" placeholder="4">
            </div>
            <p class="text-muted" style="margin:0;font-size:13px;">Padrão: <strong>4</strong> horas. Para hotéis longe (fora de Bávaro/Punta Cana) ou outro aeroporto, coloque um tempo maior no próprio local em <a href="/admin/transfers/locais">Transfers → Locais</a> (campo "Busca antes do voo").</p>
        </div>
    </div>

    <!-- Texto simples: ajuda -->
    <div class="admin-card" style="background:#f8fafc;">
        <p style="margin:0 0 6px;font-size:13px;color:#334155;"><strong>Como escrever os textos abaixo (não precisa saber programação):</strong></p>
        <ul style="margin:0;padding-left:18px;font-size:13px;color:#334155;">
            <li>Linha começando com <code># </code> vira <strong>título</strong>. Com <code>## </code> vira subtítulo.</li>
            <li>Linha começando com <code>- </code> vira <strong>item de lista</strong>.</li>
            <li>Coloque palavras entre <code>**dois asteriscos**</code> para deixar em <strong>negrito</strong>.</li>
            <li>Deixe uma linha em branco para separar parágrafos.</li>
        </ul>
    </div>

    <!-- Pagamento -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div>
            <div><h3>Política de pagamento ("Como reservar e pagar")</h3><p class="admin-card-subtitle">Aparece na página de todos os passeios.</p></div>
        </div>
        <textarea name="payment_text" class="form-control" rows="6"><?= e($paymentText) ?></textarea>
        <small class="form-hint"><code>{sinal}</code> vira o % de sinal do passeio (ex.: 50) e <code>{restante}</code> vira o que falta (ex.: 50). O % de sinal de cada passeio fica no cadastro do passeio.</small>
    </div>

    <!-- Cancelamento resumo -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
            <div><h3>Política de cancelamento (resumo na página do passeio)</h3><p class="admin-card-subtitle">Aparece no quadro "Política de cancelamento" de todos os passeios.</p></div>
        </div>
        <textarea name="cancellation_summary" class="form-control" rows="8"><?= e($cancellationSummary) ?></textarea>
        <small class="form-hint">As porcentagens que o sistema usa para calcular reembolso ficam em <a href="/admin/cancelamentos/regras">Cancelamentos → Regras</a>. Se mudar aqui, mude lá também.</small>
    </div>

    <!-- Páginas -->
    <?php foreach ($pages as $key => $p): ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
            <div><h3><?= e($p['title']) ?></h3><p class="admin-card-subtitle"><a href="<?= e($p['url']) ?>" target="_blank">Ver página no site ↗</a> <?= $p['is_custom'] ? '· <strong style="color:#1B6F00;">texto editado por vocês</strong>' : '· texto original' ?></p></div>
        </div>
        <textarea name="<?= e($key) ?>" class="form-control" rows="14"><?= e($p['value']) ?></textarea>
        <?php if ($p['is_custom']): ?>
        <label class="checkbox-label" style="margin-top:8px;"><input type="checkbox" name="restore_<?= e($key) ?>" value="1"> Voltar para o texto original</label>
        <?php endif; ?>
        <?php if ($key === 'policy_cancellation_page'): ?>
        <small class="form-hint" style="display:block;">O botão "Cancelamentos" (para o cliente pedir o cancelamento) continua aparecendo no fim da página.</small>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <div class="form-actions" style="border-top:none;padding-top:0;">
        <button type="submit" class="btn btn-primary">Salvar políticas e câmbio</button>
    </div>
</form>
