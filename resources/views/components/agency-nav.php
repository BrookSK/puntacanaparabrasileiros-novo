<style>
.aff-panel{padding:40px 0 60px;background:#f8fafb;min-height:80vh}
.aff-layout{display:grid;grid-template-columns:260px 1fr;gap:32px;align-items:start}
.aff-sidebar{background:#fff;border-radius:12px;border:1px solid #e8ecf0;overflow:hidden;position:sticky;top:90px}
.aff-sidebar-profile{padding:24px 20px;border-bottom:1px solid #f0f2f5;display:flex;align-items:center;gap:12px}
.aff-avatar{width:42px;height:42px;border-radius:50%;background:#1B6F00;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0}
.aff-profile-info{display:flex;flex-direction:column}
.aff-profile-name{font-size:14px;font-weight:600;color:#1C2011;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}
.aff-profile-role{font-size:11px;color:#636e72;text-transform:uppercase;letter-spacing:.5px}
.aff-nav{padding:12px 0}
.aff-nav-link{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13px;color:#636e72;text-decoration:none;transition:all .15s;border-left:3px solid transparent}
.aff-nav-link:hover{color:#1C2011;background:#f8fafb}
.aff-nav-link.active{color:#1B6F00;background:rgba(27,111,0,.04);border-left-color:#1B6F00;font-weight:600}
.aff-nav-link svg{flex-shrink:0}
.aff-sidebar-footer{padding:12px 0;border-top:1px solid #f0f2f5}
.aff-nav-logout{color:#e74c3c}
.aff-main{min-width:0}
.aff-page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap}
.aff-page-title{font-size:24px;font-weight:700;color:#1C2011;margin-bottom:4px}
.aff-page-subtitle{font-size:14px;color:#636e72}
.aff-period-selector{display:flex;align-items:center;gap:10px}
.aff-period-badge{display:inline-block;padding:6px 14px;background:rgba(27,111,0,.08);color:#1B6F00;border-radius:20px;font-size:12px;font-weight:600}
.aff-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px}
.aff-stats-grid--4{grid-template-columns:repeat(4,1fr)}
.aff-stat-card{background:#fff;border:1px solid #e8ecf0;border-radius:12px;padding:20px;display:flex;flex-direction:column;gap:12px;transition:box-shadow .2s}
.aff-stat-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.06)}
.aff-stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center}
.aff-stat-icon--blue{background:rgba(59,130,246,.1);color:#3b82f6}
.aff-stat-icon--orange{background:rgba(245,158,11,.1);color:#f59e0b}
.aff-stat-icon--green{background:rgba(16,185,129,.1);color:#10b981}
.aff-stat-icon--gray{background:rgba(100,116,139,.1);color:#64748b}
.aff-stat-content{flex:1}
.aff-stat-value{display:block;font-size:28px;font-weight:700;color:#1C2011;line-height:1.2}
.aff-stat-label{display:block;font-size:12px;color:#636e72;margin-top:2px}
.aff-stat-action{font-size:12px;color:#1B6F00;font-weight:500;text-decoration:none}
.aff-card{background:#fff;border:1px solid #e8ecf0;border-radius:12px;padding:24px;margin-bottom:20px}
.aff-card-title{font-size:16px;font-weight:700;color:#1C2011;margin:0 0 6px}
.aff-card-desc{font-size:14px;color:#636e72;line-height:1.6;margin-bottom:18px}
.aff-chart-card{background:#fff;border:1px solid #e8ecf0;border-radius:12px;padding:24px;margin-bottom:20px}
.aff-chart-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px}
.aff-chart-legend{display:flex;gap:16px}
.aff-legend-item{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#636e72}
.aff-legend-dot{width:8px;height:8px;border-radius:50%}
.aff-chart-body{height:220px}
.aff-bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.aff-mini-stats{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.aff-mini-stat-value{display:block;font-size:20px;font-weight:700;color:#1C2011;margin-bottom:2px}
.aff-mini-stat-label{font-size:11px;color:#636e72;text-transform:uppercase}
.aff-program-details{display:flex;flex-direction:column;gap:14px}
.aff-program-item{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5f5f5}
.aff-program-item:last-child{border-bottom:none}
.aff-program-label{font-size:13px;color:#636e72}
.aff-program-value{font-size:14px;font-weight:600;color:#1C2011}
.aff-link-copy-box{display:flex;gap:8px;align-items:center}
.aff-link-input{flex:1;background:#f8fafb;font-size:13px;font-family:monospace;padding:11px 14px;border:1px solid #e2e8f0;border-radius:8px;color:#334155}
.aff-copy-btn{white-space:nowrap;display:inline-flex;align-items:center;gap:6px}
.aff-table-wrap{overflow-x:auto}
.aff-table{width:100%;border-collapse:collapse;font-size:13px}
.aff-table th{text-align:left;padding:10px 12px;font-size:11px;font-weight:600;color:#636e72;text-transform:uppercase;letter-spacing:.3px;border-bottom:2px solid #f0f2f5;background:#fafbfc}
.aff-table td{padding:12px;border-bottom:1px solid #f5f7fa;color:#1C2011}
.aff-table tr:hover td{background:#fafcfe}
.aff-table-empty{text-align:center;color:#636e72;padding:40px 12px!important;font-size:14px}
.aff-td-amount{font-weight:700;color:#1C2011}
.btn-copied{background:#10b981!important;color:#fff!important}
@media(max-width:992px){.aff-layout{grid-template-columns:1fr}.aff-sidebar{position:static}.aff-nav{display:flex;overflow-x:auto;padding:8px 12px;gap:0}.aff-nav-link{border-left:none;border-bottom:2px solid transparent;padding:8px 14px;white-space:nowrap;font-size:12px}.aff-nav-link.active{border-left-color:transparent;border-bottom-color:#1B6F00}.aff-sidebar-profile{display:none}.aff-sidebar-footer{display:none}}
@media(max-width:768px){.aff-stats-grid,.aff-stats-grid--4{grid-template-columns:1fr 1fr}.aff-bottom-grid{grid-template-columns:1fr}.aff-page-header{flex-direction:column}.aff-link-copy-box{flex-direction:column}.aff-link-copy-box .aff-copy-btn{width:100%;justify-content:center}.aff-panel{padding:20px 0 40px}}
</style>
<aside class="aff-sidebar">
    <div class="aff-sidebar-profile">
        <div class="aff-avatar">
            <?php
            $navName = ($agency['trade_name'] ?? '') ?: ($agency['company_name'] ?? 'A');
            echo strtoupper(mb_substr($navName, 0, 2));
            ?>
        </div>
        <div class="aff-profile-info">
            <span class="aff-profile-name"><?= e($agency['trade_name'] ?: $agency['company_name']) ?></span>
            <span class="aff-profile-role">Agência</span>
        </div>
    </div>
    <nav class="aff-nav">
        <a href="/painel-agencia" class="aff-nav-link <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="/painel-agencia/link" class="aff-nav-link <?= ($active ?? '') === 'link' ? 'active' : '' ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            <span>Link de Indicação</span>
        </a>
        <a href="/painel-agencia/comissoes" class="aff-nav-link <?= ($active ?? '') === 'comissoes' ? 'active' : '' ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            <span>Comissões</span>
        </a>
        <a href="/painel-agencia/pagamentos" class="aff-nav-link <?= ($active ?? '') === 'pagamentos' ? 'active' : '' ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Pagamentos</span>
        </a>
        <a href="/painel-agencia/configuracoes" class="aff-nav-link <?= ($active ?? '') === 'configuracoes' ? 'active' : '' ?>">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
            <span>Configurações</span>
        </a>
    </nav>
    <div class="aff-sidebar-footer">
        <a href="/logout" class="aff-nav-link aff-nav-logout">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span>Sair</span>
        </a>
    </div>
</aside>
