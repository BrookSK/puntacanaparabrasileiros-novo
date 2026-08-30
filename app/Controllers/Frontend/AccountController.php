<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Booking;
use App\Models\TransferBooking;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Voucher;

class AccountController extends Controller
{
    public function dashboard(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $bookingModel = new Booking();
        $recentBookings = $bookingModel->getByUser((int) $user['id'], 1, 5);

        $this->view('frontend/account/dashboard', [
            'user' => $user,
            'recentBookings' => $recentBookings,
            'pageTitle' => 'Minha Conta',
        ], 'app');
    }

    public function bookings(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $page = max(1, (int) $request->query('page', '1'));
        $bookingModel = new Booking();
        $bookings = $bookingModel->getByUser((int) $user['id'], $page, 10);

        // Buscar nomes dos serviços para cada reserva
        if (!empty($bookings['items'])) {
            foreach ($bookings['items'] as &$bk) {
                $items = $this->db->fetchAll(
                    "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
                    [(int)$bk['id']]
                );
                $transfers = $this->db->fetchAll(
                    "SELECT CONCAT(tlo.title, ' → ', tld.title) as route FROM transfer_bookings tb INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id INNER JOIN transfer_locations tld ON tb.destination_id = tld.id WHERE tb.booking_id = ?",
                    [(int)$bk['id']]
                );
                $names = array_merge(array_column($items, 'title'), array_column($transfers, 'route'));
                $bk['service_names'] = implode(', ', $names) ?: '-';
            }
            unset($bk);
        }

        $this->view('frontend/account/bookings', [
            'bookings' => $bookings,
            'pageTitle' => 'Minhas Reservas',
        ], 'app');
    }

    public function bookingDetail(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $id = (int) $request->param('id');
        $bookingModel = new Booking();
        $booking = $bookingModel->find($id);

        if (!$booking || (int) $booking['user_id'] !== (int) $user['id']) {
            $this->abort(404, 'Reserva não encontrada.');
        }

        $items = $bookingModel->getItems($id);
        $transfers = $bookingModel->getTransferBookings($id);
        $payments = $bookingModel->getPayments($id);
        $vouchers = $bookingModel->getVouchers($id);

        $this->view('frontend/account/booking-detail', [
            'booking' => $booking,
            'items' => $items,
            'transfers' => $transfers,
            'payments' => $payments,
            'vouchers' => $vouchers,
            'pageTitle' => 'Reserva ' . $booking['booking_number'],
        ], 'app');
    }

    public function transfers(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $page = max(1, (int) $request->query('page', '1'));
        $transferModel = new TransferBooking();
        $transfers = $transferModel->getByUser((int) $user['id'], $page, 10);

        $this->view('frontend/account/transfers', [
            'transfers' => $transfers,
            'pageTitle' => 'Meus Transfers',
        ], 'app');
    }

    public function wishlist(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $wishlistModel = new Wishlist();
        $items = $wishlistModel->getByUser((int) $user['id']);

        $this->view('frontend/account/wishlist', [
            'items' => $items,
            'pageTitle' => 'Lista de Desejos',
        ], 'app');
    }

    public function cancellations(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $page = max(1, (int) $request->query('page', '1'));
        $perPage = 10;

        // Contar total de reservas do usuário
        $countRow = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT b.id) as total
             FROM bookings b
             INNER JOIN booking_items bi ON b.id = bi.booking_id
             WHERE b.user_id = ?",
            [(int) $user['id']]
        );
        $totalCount = (int) ($countRow['total'] ?? 0);

        $totalPages = max(1, (int) ceil($totalCount / $perPage));
        $offset = ($page - 1) * $perPage;

        // Buscar reservas paginadas com status de cancelamento
        $bookings = $this->db->fetchAll(
            "SELECT b.*, bi.id as item_id, bi.trip_id, bi.trip_date, t.title as trip_title,
                    cr.id as cancellation_request_id, cr.status as cancellation_status,
                    cr.admin_response, cr.refund_status, cr.refund_amount, cr.reason as cancellation_reason
             FROM bookings b
             INNER JOIN booking_items bi ON b.id = bi.booking_id
             INNER JOIN trips t ON bi.trip_id = t.id
             LEFT JOIN cancellation_requests cr ON cr.booking_id = b.id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC
             LIMIT $perPage OFFSET $offset",
            [(int) $user['id']]
        );

        $this->view('frontend/account/cancellations', [
            'bookings' => $bookings,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'pageTitle' => 'Cancelamentos',
        ], 'app');
    }

    public function requestCancellation(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $bookingId = (int) $request->input('booking_id');
        $reason = trim($request->input('cancellation_reason', ''));

        // Validar motivo
        if (empty($reason)) {
            $this->flash('error', 'Por favor, informe o motivo do cancelamento.');
            $this->redirect('/minha-conta/cancelamentos');
            return;
        }

        // Verificar se o booking pertence ao usuário
        $bookingModel = new Booking();
        $booking = $bookingModel->find($bookingId);

        if (!$booking || (int) $booking['user_id'] !== (int) $user['id']) {
            $this->flash('error', 'Reserva não encontrada.');
            $this->redirect('/minha-conta/cancelamentos');
            return;
        }

        // Verificar se já foi cancelado ou solicitado
        if (in_array($booking['status'], ['cancelled', 'refunded'])) {
            $this->flash('error', 'Esta reserva já está cancelada.');
            $this->redirect('/minha-conta/cancelamentos');
            return;
        }

        // Verificar se já existe uma solicitação pendente
        $existing = $this->db->fetchOne(
            "SELECT id FROM cancellation_requests WHERE booking_id = ? AND status = 'pending'",
            [$bookingId]
        );
        if ($existing) {
            $this->flash('error', 'Já existe uma solicitação de cancelamento pendente para esta reserva.');
            $this->redirect('/minha-conta/cancelamentos');
            return;
        }

        // Criar solicitação de cancelamento
        $cancellationModel = new \App\Models\CancellationRequest();
        $cancellationModel->create([
            'booking_id' => $bookingId,
            'user_id' => (int) $user['id'],
            'reason' => $reason,
            'status' => 'pending',
            'refund_status' => 'none',
        ]);

        // Log
        $this->db->insert('activity_log', [
            'user_id' => (int) $user['id'],
            'action' => 'cancellation_requested',
            'entity_type' => 'booking',
            'entity_id' => $bookingId,
            'ip_address' => $request->ip(),
        ]);

        // Buscar nome do(s) passeio(s) da reserva
        $bookingItems = $this->db->fetchAll(
            "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [$bookingId]
        );
        $serviceName = implode(', ', array_column($bookingItems, 'title')) ?: '';

        // Notificar admin por email
        $emailService = new \App\Services\EmailService();
        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            $emailService->sendTemplate(
                $adminEmail, 'Admin',
                'Nova Solicitação de Cancelamento: ' . $booking['booking_number'],
                'cancellation',
                [
                    'emailTitle' => 'Nova Solicitação de Cancelamento',
                    'clientName' => 'Admin',
                    'emailMessage' => 'O cliente <strong>' . e($user['first_name'] . ' ' . $user['last_name']) . '</strong> solicitou o cancelamento da reserva abaixo.',
                    'bookingNumber' => $booking['booking_number'],
                    'serviceName' => $serviceName,
                    'bookingTotal' => $booking['total'] ?? 0,
                    'statusLabel' => 'Aguardando Análise',
                    'statusColor' => '#d97706',
                    'blockquoteLabel' => 'Motivo do Cliente',
                    'blockquoteText' => $reason,
                    'blockquoteColor' => '#e74c3c',
                    'blockquoteBg' => '#fef2f2',
                    'additionalMessage' => 'Acesse o painel administrativo para analisar e processar esta solicitação.',
                    'ctaUrl' => url('/admin/cancelamentos'),
                    'ctaText' => 'Ver no Painel Admin',
                ]
            );
        }

        // Notificar cliente por email
        $clientEmail = $booking['billing_email'] ?? $user['email'] ?? '';
        $clientName = $user['first_name'] ?? 'Cliente';
        if ($clientEmail) {
            $emailService->sendTemplate(
                $clientEmail, $clientName,
                'Solicitação de Cancelamento Recebida - ' . $booking['booking_number'],
                'cancellation',
                [
                    'emailTitle' => 'Solicitação Recebida',
                    'clientName' => $clientName,
                    'emailMessage' => 'Recebemos sua solicitação de cancelamento para a reserva abaixo. Nossa equipe analisará o pedido e você receberá uma resposta em breve.',
                    'bookingNumber' => $booking['booking_number'],
                    'serviceName' => $serviceName,
                    'bookingTotal' => $booking['total'] ?? 0,
                    'statusLabel' => 'Aguardando Análise',
                    'statusColor' => '#d97706',
                    'blockquoteLabel' => 'Motivo Informado',
                    'blockquoteText' => $reason,
                    'blockquoteColor' => '#3772C0',
                    'blockquoteBg' => '#eff6ff',
                    'additionalMessage' => 'Você receberá um e-mail assim que sua solicitação for processada.',
                    'ctaUrl' => url('/minha-conta/cancelamentos'),
                    'ctaText' => 'Acompanhar Solicitação',
                ]
            );
        }

        $this->flash('success', 'Solicitação de cancelamento enviada com sucesso! Você receberá uma resposta por e-mail.');
        $this->redirect('/minha-conta/cancelamentos');
    }

    public function toggleWishlist(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $tripId = (int) $request->input('trip_id');

        if (!$tripId) {
            $this->json(['error' => 'Trip ID inválido.'], 400);
            return;
        }

        $wishlistModel = new Wishlist();
        $added = $wishlistModel->toggle((int) $user['id'], $tripId);

        $this->json([
            'success' => true,
            'in_wishlist' => $added,
            'message' => $added ? 'Adicionado à lista de desejos!' : 'Removido da lista de desejos.',
        ]);
    }

    public function profile(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        // Buscar dados completos do user (sessão pode estar desatualizada)
        $userModel = new User();
        $fullUser = $userModel->find((int) $user['id']);
        unset($fullUser['password']);

        $this->view('frontend/account/profile', [
            'user' => $fullUser,
            'pageTitle' => 'Detalhes da Conta',
        ], 'app');
    }

    public function billing(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $userModel = new User();
        $fullUser = $userModel->find((int) $user['id']);

        $this->view('frontend/account/billing', [
            'user' => $fullUser,
            'pageTitle' => 'Informações de Cobrança',
        ], 'app');
    }

    public function updateBilling(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $data = $request->only(['billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone', 'billing_address', 'billing_city', 'billing_country']);

        // Salvar como dados do perfil
        $userModel = new User();
        $userModel->update((int) $user['id'], [
            'first_name' => $data['billing_first_name'] ?? $user['first_name'],
            'last_name' => $data['billing_last_name'] ?? $user['last_name'],
            'phone' => $data['billing_phone'] ?? null,
            'address' => $data['billing_address'] ?? null,
            'city' => $data['billing_city'] ?? null,
            'country' => $data['billing_country'] ?? null,
        ]);

        // Atualizar sessão
        $updatedUser = $userModel->find((int) $user['id']);
        unset($updatedUser['password']);
        $this->session->set('user', $updatedUser);

        $this->flash('success', 'Informações de cobrança atualizadas!');
        $this->redirect('/minha-conta/cobranca');
    }

    public function updateProfile(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $data = $request->only(['first_name', 'last_name', 'phone', 'country', 'address', 'city']);

        $errors = [];
        if (empty($data['first_name'])) $errors['first_name'] = 'Nome é obrigatório.';
        if (empty($data['last_name'])) $errors['last_name'] = 'Sobrenome é obrigatório.';

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->redirect('/minha-conta/perfil');
            return;
        }

        $userModel = new User();
        $userModel->update((int) $user['id'], $data);

        // Atualizar sessão
        $updatedUser = $userModel->find((int) $user['id']);
        unset($updatedUser['password']);
        $this->session->set('user', $updatedUser);

        $this->flash('success', 'Perfil atualizado com sucesso!');
        $this->redirect('/minha-conta/perfil');
    }

    public function updatePassword(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $currentPassword = $request->input('current_password', '');
        $newPassword = $request->input('new_password', '');
        $confirmPassword = $request->input('new_password_confirmation', '');

        // Verificar senha atual
        $userModel = new User();
        $fullUser = $userModel->find((int) $user['id']);
        if (!password_verify($currentPassword, $fullUser['password'])) {
            $this->flash('error', 'Senha atual incorreta.');
            $this->redirect('/minha-conta/perfil');
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->flash('error', 'Nova senha deve ter pelo menos 6 caracteres.');
            $this->redirect('/minha-conta/perfil');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->flash('error', 'As senhas não coincidem.');
            $this->redirect('/minha-conta/perfil');
            return;
        }

        $userModel->updatePassword((int) $user['id'], $newPassword);
        $this->flash('success', 'Senha alterada com sucesso!');
        $this->redirect('/minha-conta/perfil');
    }

    public function downloadVoucher(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $reference = $request->param('reference', '');

        $voucherModel = new Voucher();
        $voucher = $voucherModel->findByReference($reference);

        if (!$voucher) {
            $this->abort(404, 'Voucher não encontrado.');
        }

        // Verificar permissão (o voucher pertence a um booking do user?)
        if ($voucher['booking_id']) {
            $bookingModel = new Booking();
            $booking = $bookingModel->find((int) $voucher['booking_id']);
            if (!$booking || (int) $booking['user_id'] !== (int) $user['id']) {
                $this->abort(403, 'Acesso negado.');
            }
        }

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'Arquivo do voucher não encontrado.');
        }

        $voucherModel->incrementDownload((int) $voucher['id']);
        $response->download($filePath, 'voucher-' . $reference . '.html');
    }

    public function viewVoucherPublic(Request $request, Response $response): void
    {
        $reference = $request->param('reference', '');

        $voucherModel = new Voucher();
        $voucher = $voucherModel->findByReference($reference);

        if (!$voucher) {
            // Se não encontrar na tabela vouchers, redirecionar para confirmação
            $this->redirect('/voucher/' . $reference . '/confirmar');
            return;
        }

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            // Arquivo não encontrado, mostrar confirmação
            $this->redirect('/voucher/' . $reference . '/confirmar');
            return;
        }

        // Render the voucher HTML directly
        $voucherHtml = file_get_contents($filePath);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Voucher ' . htmlspecialchars($reference) . '</title>';
        echo '<style>body{margin:0;padding:20px;background:#f5f5f5;}.voucher-wrapper{max-width:800px;margin:0 auto;}.voucher-actions{text-align:center;margin:20px auto;max-width:800px;}.voucher-actions button,.voucher-actions a{display:inline-block;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;margin:0 8px;}.btn-print{background:#1B6F00;color:#fff;border:none;}.btn-download{background:#E4B505;color:#1C2011;border:none;}@media print{.voucher-actions{display:none;}body{padding:0;background:#fff;}}</style>';
        echo '</head><body>';
        echo '<div class="voucher-actions"><button class="btn-print" onclick="window.print()">Imprimir Voucher</button></div>';
        echo '<div class="voucher-wrapper">' . $voucherHtml . '</div>';
        echo '</body></html>';
        exit;
    }

    public function confirmVoucherPublic(Request $request, Response $response): void
    {
        $reference = $request->param('reference', '');

        $voucherModel = new Voucher();
        $voucher = $voucherModel->findByReference($reference);

        // Buscar dados do booking
        $tripName = '';
        $customerName = '';
        $date = '';
        $status = 'booked';
        $type = 'trip';
        $booking = null;

        if ($voucher) {
            $type = $voucher['type'] ?? 'trip';

            if ($type === 'trip' && $voucher['booking_id']) {
                $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$voucher['booking_id']]);
                if ($booking) {
                    $customerName = trim(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? ''));
                    $status = $booking['status'] ?? 'booked';

                    // Verificar se há pagamento aprovado — se sim, considerar confirmado
                    $payment = $this->db->fetchOne(
                        "SELECT id FROM payments WHERE booking_id = ? AND status IN ('approved', 'completed', 'paid') ORDER BY created_at DESC LIMIT 1",
                        [(int) $booking['id']]
                    );
                    if ($payment) {
                        $status = 'confirmed';
                    }
                }
                if ($voucher['booking_item_id']) {
                    $item = $this->db->fetchOne(
                        "SELECT bi.*, t.title as trip_title FROM booking_items bi LEFT JOIN trips t ON bi.trip_id = t.id WHERE bi.id = ?",
                        [$voucher['booking_item_id']]
                    );
                    if ($item) {
                        $tripName = $item['trip_title'] ?? '';
                        $date = $item['travel_date'] ?? '';
                    }
                }
            } elseif ($type === 'transfer' && $voucher['transfer_booking_id']) {
                $transfer = $this->db->fetchOne(
                    "SELECT tb.*, tlo.title as origin_title, tld.title as destination_title
                     FROM transfer_bookings tb
                     INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id
                     INNER JOIN transfer_locations tld ON tb.destination_id = tld.id
                     WHERE tb.id = ?",
                    [$voucher['transfer_booking_id']]
                );
                if ($transfer) {
                    $tripName = ($transfer['origin_title'] ?? '') . ' \u2192 ' . ($transfer['destination_title'] ?? '');
                    $customerName = $transfer['passenger_name'] ?? '';
                    $date = $transfer['pickup_date'] ?? '';
                    $status = $transfer['status'] ?? 'confirmed';
                }
                if (!$customerName && $voucher['booking_id']) {
                    $booking = $this->db->fetchOne("SELECT * FROM bookings WHERE id = ?", [$voucher['booking_id']]);
                    if ($booking) {
                        $customerName = trim(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? ''));
                        // Verificar pagamento
                        $payment = $this->db->fetchOne(
                            "SELECT id FROM payments WHERE booking_id = ? AND status IN ('approved', 'completed', 'paid') ORDER BY created_at DESC LIMIT 1",
                            [(int) $booking['id']]
                        );
                        if ($payment) {
                            $status = 'confirmed';
                        } else {
                            $status = $status ?: ($booking['status'] ?? 'booked');
                        }
                    }
                }
            }
        } else {
            // Voucher não encontrado no banco, mas tenta buscar pelo booking_number ou reference
            // Mostrar confirmação genérica com a referência
            $status = 'booked';
        }

        $this->view('frontend/voucher/confirmation', [
            'voucher' => $voucher,
            'reference' => $reference,
            'type' => $type,
            'tripName' => $tripName,
            'customerName' => $customerName,
            'date' => $date,
            'status' => $status,
            'totalAmount' => (float) ($booking['total'] ?? 0),
            'paidAmount' => (float) ($booking['paid_amount'] ?? 0),
            'dueAmount' => (float) ($booking['due_amount'] ?? 0),
            'pageTitle' => 'Confirmação de Voucher - ' . $reference,
        ], 'app');
    }

    // ==================== PAINEL DO AFILIADO ====================

    public function affiliateDashboard(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);

        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $affiliateId = (int) $affiliate['id'];

        // Buscar dados dos últimos 30 dias para o gráfico
        $visitsByDay = $this->db->fetchAll(
            "SELECT DATE(created_at) as day, COUNT(*) as total FROM affiliate_visits WHERE affiliate_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at)",
            [$affiliateId]
        );
        $commissionsByDay = $this->db->fetchAll(
            "SELECT DATE(created_at) as day, COUNT(*) as total, SUM(amount) as earnings FROM commissions WHERE affiliate_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at)",
            [$affiliateId]
        );

        // Montar arrays de 30 posições (indexadas por data)
        $visitsMap = [];
        foreach ($visitsByDay as $row) { $visitsMap[$row['day']] = (int) $row['total']; }
        $commissionsMap = [];
        $earningsMap = [];
        foreach ($commissionsByDay as $row) {
            $commissionsMap[$row['day']] = (int) $row['total'];
            $earningsMap[$row['day']] = (float) $row['earnings'];
        }

        $chartLabels = [];
        $chartVisits = [];
        $chartCommissions = [];
        $chartEarnings = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $chartLabels[] = date('d/m', strtotime($date));
            $chartVisits[] = $visitsMap[$date] ?? 0;
            $chartCommissions[] = $commissionsMap[$date] ?? 0;
            $chartEarnings[] = $earningsMap[$date] ?? 0;
        }

        $this->view('frontend/affiliate/dashboard', [
            'affiliate' => $affiliate,
            'user' => $user,
            'chartLabels' => $chartLabels,
            'chartVisits' => $chartVisits,
            'chartCommissions' => $chartCommissions,
            'chartEarnings' => $chartEarnings,
            'pageTitle' => 'Painel do Afiliado',
        ], 'app');
    }

    public function affiliateLinks(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $this->view('frontend/affiliate/links', ['affiliate' => $affiliate, 'pageTitle' => 'Links Afiliados'], 'app');
    }

    public function affiliateCommissions(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $commissionModel = new \App\Models\Commission();
        $commissions = $commissionModel->getByAffiliate((int) $affiliate['id'], 1, 50);

        // Calcular total cancelado
        $totalCancelled = $this->db->fetchColumn(
            "SELECT COALESCE(SUM(amount), 0) FROM commissions WHERE affiliate_id = ? AND status = 'rejected'",
            [(int) $affiliate['id']]
        );

        $this->view('frontend/affiliate/commissions', [
            'affiliate' => $affiliate,
            'commissions' => $commissions['items'] ?? [],
            'totalCancelled' => (float) $totalCancelled,
            'pageTitle' => 'Comissões',
        ], 'app');
    }

    public function affiliateVisits(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $affiliateId = (int) $affiliate['id'];
        $page = max(1, (int) $request->query('page', '1'));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliate_visits WHERE affiliate_id = ?", [$affiliateId]);
        $totalPages = (int) ceil($total / $perPage);

        $visits = $this->db->fetchAll(
            "SELECT * FROM affiliate_visits WHERE affiliate_id = ? ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
            [$affiliateId]
        );

        $this->view('frontend/affiliate/visits', [
            'affiliate' => $affiliate,
            'visits' => $visits,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Visitas',
        ], 'app');
    }

    public function affiliateCreatives(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $creativeModel = new \App\Models\AffiliateCreative();
        $creatives = $creativeModel->getActive();

        $this->view('frontend/affiliate/creatives', ['affiliate' => $affiliate, 'creatives' => $creatives, 'pageTitle' => 'Criativos'], 'app');
    }

    public function affiliatePayments(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $payments = $this->db->fetchAll("SELECT * FROM commissions WHERE affiliate_id = ? AND status = 'paid' ORDER BY paid_at DESC", [(int) $affiliate['id']]);

        $this->view('frontend/affiliate/payments', ['affiliate' => $affiliate, 'payments' => $payments, 'pageTitle' => 'Pagamentos'], 'app');
    }

    public function affiliateTestNotification(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $service = new \App\Services\AffiliateService();
        $result = $service->testNotification((int) $affiliate['id']);

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function affiliateSettings(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $userModel = new User();
        $fullUser = $userModel->find((int) $user['id']);
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $affiliateNotes = json_decode($affiliate['notes'] ?? '{}', true) ?: [];

        $this->view('frontend/affiliate/settings', ['affiliate' => $affiliate, 'affiliateNotes' => $affiliateNotes, 'user' => $fullUser, 'pageTitle' => 'Configurações'], 'app');
    }

    public function affiliateSettingsUpdate(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $data = $request->only(['phone', 'payment_email', 'pix', 'bank_name', 'bank_agency', 'bank_account', 'bank_account_type', 'website', 'followers_count', 'niche', 'content_type']);

        // Atualizar phone no user
        $userModel = new User();
        $userModel->update((int) $user['id'], ['phone' => $data['phone'] ?? '']);

        // Atualizar affiliate
        $notes = json_decode($affiliate['notes'] ?? '{}', true) ?: [];
        $notes['pix'] = $data['pix'] ?? '';
        $notes['bank_name'] = $data['bank_name'] ?? '';
        $notes['bank_agency'] = $data['bank_agency'] ?? '';
        $notes['bank_account'] = $data['bank_account'] ?? '';
        $notes['bank_account_type'] = $data['bank_account_type'] ?? 'corrente';
        $notes['website'] = $data['website'] ?? '';
        $notes['followers_count'] = $data['followers_count'] ?? '';
        $notes['niche'] = $data['niche'] ?? '';
        $notes['content_type'] = $data['content_type'] ?? '';

        $affiliateModel->update((int) $affiliate['id'], [
            'payment_email' => $data['payment_email'] ?? '',
            'notes' => json_encode($notes),
        ]);

        $this->flash('success', 'Configurações salvas!');
        $this->redirect('/painel-afiliado/configuracoes');
    }

    public function affiliateLanding(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $this->view('frontend/affiliate/landing', ['affiliate' => $affiliate, 'pageTitle' => 'Crie sua Landing Page'], 'app');
    }
}