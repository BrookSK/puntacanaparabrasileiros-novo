<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\AuditLog;

/**
 * Controller para visualização dos logs de auditoria.
 * Apenas superadmin tem acesso.
 */
class AuditLogsController extends Controller
{
    private AuditLog $auditLogModel;

    public function __construct()
    {
        parent::__construct();
        $this->auditLogModel = new AuditLog();
    }

    /**
     * Lista os logs de auditoria com filtros e paginação.
     * GET /admin/audit-logs
     */
    public function index(Request $request, Response $response): void
    {
        // Apenas superadmin pode acessar
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Apenas o superadmin pode acessar os logs de auditoria.');
            $this->redirect('/admin');
            return;
        }

        // Parâmetros de filtro
        $page = max(1, (int) $request->query('page', '1'));
        $perPage = 50;
        $userId = $request->query('user_id');
        $action = $request->query('action');
        $entityType = $request->query('entity_type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('busca');

        // Converter userId para int se fornecido
        $userIdInt = ($userId !== null && $userId !== '') ? (int) $userId : null;

        // Buscar logs filtrados
        $logs = $this->auditLogModel->listFiltered(
            $page,
            $perPage,
            $userIdInt,
            $action,
            $entityType,
            $dateFrom,
            $dateTo,
            $search
        );

        // Buscar opções para filtros
        $distinctUsers = $this->auditLogModel->getDistinctUsers();
        $distinctActions = $this->auditLogModel->getDistinctActions();
        $distinctEntityTypes = $this->auditLogModel->getDistinctEntityTypes();

        // Estatísticas rápidas (opcional, para cabeçalho)
        $stats = $this->auditLogModel->getStats($dateFrom, $dateTo);

        $this->view('admin/audit-logs/index', [
            'logs' => $logs,
            'distinctUsers' => $distinctUsers,
            'distinctActions' => $distinctActions,
            'distinctEntityTypes' => $distinctEntityTypes,
            'stats' => $stats,
            'filters' => [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'busca' => $search,
            ],
            'pageTitle' => 'Logs de Auditoria',
        ], 'admin');
    }

    /**
     * Visualiza detalhes de um log específico (AJAX ou modal).
     * GET /admin/audit-logs/{id}
     */
    public function show(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->json(['error' => 'Acesso negado.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $log = $this->auditLogModel->find($id);

        if (!$log) {
            $this->json(['error' => 'Log não encontrado.'], 404);
            return;
        }

        // Decodificar changes se existir
        if (!empty($log['changes'])) {
            $log['changes_decoded'] = json_decode($log['changes'], true);
        }

        // Traduzir valores
        $log['action_label'] = AuditLog::translateAction($log['action']);
        $log['entity_type_label'] = AuditLog::translateEntityType($log['entity_type']);
        $log['result_label'] = AuditLog::translateResult($log['result']);

        $this->json(['log' => $log]);
    }

    /**
     * Exporta logs filtrados para CSV.
     * GET /admin/audit-logs/export
     */
    public function export(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Acesso negado.');
            $this->redirect('/admin/audit-logs');
            return;
        }

        // Mesmos filtros da listagem
        $userId = $request->query('user_id');
        $action = $request->query('action');
        $entityType = $request->query('entity_type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('busca');

        $userIdInt = ($userId !== null && $userId !== '') ? (int) $userId : null;

        // Buscar todos os logs filtrados (limite maior para exportação)
        $logs = $this->auditLogModel->listFiltered(
            1,
            10000, // Limite de exportação
            $userIdInt,
            $action,
            $entityType,
            $dateFrom,
            $dateTo,
            $search
        );

        // Gerar CSV
        $filename = 'audit-logs-' . date('Y-m-d-His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 no Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // Cabeçalho
        fputcsv($output, [
            'ID',
            'Data/Hora',
            'Usuário',
            'Email',
            'Ação',
            'Tipo de Entidade',
            'ID Entidade',
            'Nome Entidade',
            'Descrição',
            'Resultado',
            'IP',
        ], ';');

        // Dados
        foreach ($logs['items'] as $log) {
            fputcsv($output, [
                $log['id'],
                $log['created_at'],
                $log['user_name'] ?? '-',
                $log['user_email'] ?? '-',
                AuditLog::translateAction($log['action']),
                AuditLog::translateEntityType($log['entity_type']),
                $log['entity_id'] ?? '-',
                $log['entity_name'] ?? '-',
                $log['description'] ?? '-',
                AuditLog::translateResult($log['result']),
                $log['ip_address'] ?? '-',
            ], ';');
        }

        fclose($output);
        exit;
    }
}
