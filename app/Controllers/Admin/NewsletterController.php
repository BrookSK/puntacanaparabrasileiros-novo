<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\NewsletterSubscriber;
use App\Services\EmailService;

class NewsletterController extends Controller
{
    private NewsletterSubscriber $subscriberModel;

    public function __construct()
    {
        parent::__construct();
        $this->subscriberModel = new NewsletterSubscriber();
    }

    /**
     * Listagem de inscritos.
     */
    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status');

        $subscribers = $this->subscriberModel->getAll($page, 30, $status);
        $activeCount = $this->subscriberModel->getActiveCount();
        $totalCount = $this->subscriberModel->count();

        $this->view('admin/newsletter/index', [
            'subscribers' => $subscribers,
            'activeCount' => $activeCount,
            'totalCount' => $totalCount,
            'currentStatus' => $status,
            'pageTitle' => 'Newsletter - Inscritos',
        ], 'admin');
    }

    /**
     * Excluir inscrito.
     */
    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->subscriberModel->delete($id);
        $this->flash('success', 'Inscrito removido.');
        $this->redirect('/admin/newsletter');
    }

    /**
     * Exportar inscritos ativos como CSV.
     */
    public function export(Request $request, Response $response): void
    {
        $subscribers = $this->subscriberModel->exportActive();

        $filename = 'newsletter-inscritos-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Email', 'Nome', 'Data de Inscrição']);
        foreach ($subscribers as $sub) {
            fputcsv($output, [$sub['email'], $sub['name'] ?? '', $sub['subscribed_at']]);
        }
        fclose($output);
        exit;
    }

    /**
     * Página de criar campanha.
     */
    public function createCampaign(Request $request, Response $response): void
    {
        $activeCount = $this->subscriberModel->getActiveCount();
        $campaigns = $this->db->fetchAll("SELECT * FROM newsletter_campaigns ORDER BY created_at DESC LIMIT 20");

        $this->view('admin/newsletter/campaign', [
            'activeCount' => $activeCount,
            'campaigns' => $campaigns,
            'pageTitle' => 'Newsletter - Campanhas',
        ], 'admin');
    }

    /**
     * Enviar campanha para todos os inscritos ativos.
     */
    public function sendCampaign(Request $request, Response $response): void
    {
        $subject = trim($request->input('subject', ''));
        $body = $request->input('body', '');

        if (!$subject || !$body) {
            $this->flash('error', 'Assunto e conteúdo são obrigatórios.');
            $this->redirect('/admin/newsletter/campanhas');
            return;
        }

        $subscribers = $this->subscriberModel->getActive();
        $recipientsCount = count($subscribers);

        // Criar registro da campanha
        $campaignId = $this->db->insert('newsletter_campaigns', [
            'subject' => $subject,
            'body' => $body,
            'status' => 'sending',
            'recipients_count' => $recipientsCount,
        ]);

        // Enviar emails
        $emailService = new EmailService();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($subscribers as $sub) {
            $personalizedBody = str_replace(
                ['{email}', '{name}'],
                [$sub['email'], $sub['name'] ?? 'Viajante'],
                $body
            );

            $sent = $emailService->send(
                $sub['email'],
                $sub['name'] ?? '',
                $subject,
                $personalizedBody
            );

            if ($sent) {
                $sentCount++;
            } else {
                $failedCount++;
            }
        }

        // Atualizar campanha
        $this->db->update('newsletter_campaigns', [
            'status' => 'sent',
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'sent_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$campaignId]);

        $this->flash('success', "Campanha enviada! {$sentCount} enviados, {$failedCount} falharam.");
        $this->redirect('/admin/newsletter/campanhas');
    }
}
