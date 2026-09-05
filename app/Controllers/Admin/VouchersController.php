<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Services\EmailService;

class VouchersController extends Controller
{
    private Voucher $voucherModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->voucherModel = new Voucher();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $vouchers = $this->voucherModel->getAllWithDetails($page, 20);

        $this->view('admin/vouchers/index', [
            'vouchers' => $vouchers,
            'pageTitle' => 'Gerenciar Vouchers',
        ], 'admin');
    }

    public function show(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'Arquivo do voucher não encontrado.');
        }

        // Servir o HTML do voucher para visualização
        $html = file_get_contents($filePath);

        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        $response->setBody($html);
        $response->send();
    }

    public function download(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'Arquivo não encontrado.');
        }

        $html = file_get_contents($filePath);

        // Gerar nome amigável para o PDF
        $type = ($voucher['type'] ?? 'trip') === 'transfer' ? 'Transfer' : 'Passeio';
        $downloadName = 'Voucher-' . $type . '-' . ($voucher['reference_code'] ?? 'download') . '.pdf';

        // Tentar gerar PDF com dompdf se disponível
        if (class_exists('\\Dompdf\\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Cache-Control: no-cache, must-revalidate');
            echo $dompdf->output();
            exit;
        }

        // Fallback sem dompdf: servir página que converte e baixa PDF via JS (html2pdf.js)
        $encodedHtml = base64_encode($html);
        $jsPage = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Gerando PDF...</title>'
            . '<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.2/html2pdf.bundle.min.js"></script>'
            . '</head><body>'
            . '<div style="display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;">'
            . '<p style="font-size:1.1rem;color:#333;">Gerando PDF do voucher, aguarde...</p></div>'
            . '<div id="voucherContent" style="position:absolute;left:-9999px;top:0;"></div>'
            . '<script>'
            . 'var htmlContent = atob("' . $encodedHtml . '");'
            . 'var container = document.getElementById("voucherContent");'
            . 'container.innerHTML = htmlContent;'
            . 'var opt = { margin: 5, filename: "' . addslashes($downloadName) . '", image: { type: "jpeg", quality: 0.98 },'
            . ' html2canvas: { scale: 2, useCORS: true }, jsPDF: { unit: "mm", format: "a4", orientation: "portrait" } };'
            . 'html2pdf().set(opt).from(container).save().then(function(){ setTimeout(function(){ window.close(); }, 1500); });'
            . '</script></body></html>';

        header('Content-Type: text/html; charset=utf-8');
        echo $jsPage;
        exit;
    }

    public function send(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $email = $request->input('email');
        if (!$email && $voucher['booking_id']) {
            $booking = $this->db->fetchOne("SELECT billing_email FROM bookings WHERE id = ?", [(int) $voucher['booking_id']]);
            $email = $booking['billing_email'] ?? null;
        }

        if (!$email) {
            $this->flash('error', 'Email não encontrado.');
            $this->redirect('/admin/vouchers');
            return;
        }

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        $emailService = new EmailService();
        $sent = $emailService->send(
            $email, 'Cliente',
            'Seu Voucher - Punta Cana para Brasileiros',
            '<p>Segue seu voucher em anexo.</p>',
            file_exists($filePath) ? [$filePath] : []
        );

        if ($sent) {
            $this->voucherModel->markEmailSent($id);
            $this->flash('success', 'Voucher enviado para ' . $email);
        } else {
            $this->flash('error', 'Falha ao enviar email.');
        }

        $this->redirect('/admin/vouchers');
    }

    /**
     * Regenera o QR code de todos os vouchers existentes para apontar para /confirmar.
     */
    public function regenerateQrCodes(Request $request, Response $response): void
    {
        $vouchersPath = BASE_PATH . '/public/uploads/vouchers/';
        $siteUrl = rtrim($this->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/');
        $vouchers = $this->db->fetchAll("SELECT id, reference_code, file_path FROM vouchers");
        $count = 0;

        foreach ($vouchers as $v) {
            $filePath = $vouchersPath . $v['file_path'];
            if (!file_exists($filePath)) continue;

            $html = file_get_contents($filePath);
            $confirmUrl = $siteUrl . '/voucher/' . $v['reference_code'] . '/confirmar';
            $newQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($confirmUrl);

            // Substituir o QR antigo pelo novo
            $html = preg_replace(
                '/(<img[^>]*class="[^"]*"[^>]*src=")[^"]*qrserver\.com[^"]*("[^>]*>)/i',
                '${1}' . htmlspecialchars($newQrUrl) . '${2}',
                $html
            );
            // Tentar também o padrão sem class
            $html = preg_replace(
                '/(src=")https:\/\/api\.qrserver\.com\/v1\/create-qr-code\/[^"]*(")/i',
                '${1}' . htmlspecialchars($newQrUrl) . '${2}',
                $html
            );

            file_put_contents($filePath, $html);
            $count++;
        }

        $this->flash('success', $count . ' vouchers atualizados com novo QR code.');
        $this->redirect('/admin/vouchers');
    }
}
