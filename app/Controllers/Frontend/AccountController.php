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
        $bookingModel = new Booking();

        // Buscar todas as reservas do usuário com seus itens
        $bookings = $this->db->fetchAll(
            "SELECT b.*, bi.id as item_id, bi.trip_id, bi.trip_date, t.title as trip_title
             FROM bookings b
             INNER JOIN booking_items bi ON b.id = bi.booking_id
             INNER JOIN trips t ON bi.trip_id = t.id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC",
            [(int) $user['id']]
        );

        $this->view('frontend/account/cancellations', [
            'bookings' => $bookings,
            'pageTitle' => 'Cancelamentos',
        ], 'app');
    }

    public function requestCancellation(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $bookingId = (int) $request->input('booking_id');

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

        // Atualizar status para "cancellation_requested"
        $bookingModel->updateStatus($bookingId, 'cancelled');

        // Log
        $this->db->insert('activity_log', [
            'user_id' => (int) $user['id'],
            'action' => 'cancellation_requested',
            'entity_type' => 'booking',
            'entity_id' => $bookingId,
            'ip_address' => $request->ip(),
        ]);

        // Notificar admin
        $emailService = new \App\Services\EmailService();
        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            $emailService->send(
                $adminEmail, 'Admin',
                'Cancelamento Solicitado: ' . $booking['booking_number'],
                '<p>O cliente <strong>' . e($user['first_name'] . ' ' . $user['last_name']) . '</strong> solicitou o cancelamento da reserva <strong>' . e($booking['booking_number']) . '</strong>.</p>'
            );
        }

        $this->flash('success', 'Cancelamento solicitado com sucesso. Você receberá uma confirmação por email.');
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
}

    // ==================== PAINEL DO AFILIADO ====================

    public function affiliateDashboard(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);

        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $this->view('frontend/affiliate/dashboard', [
            'affiliate' => $affiliate,
            'user' => $user,
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

        $this->view('frontend/affiliate/commissions', ['affiliate' => $affiliate, 'commissions' => $commissions['items'] ?? [], 'pageTitle' => 'Comissões'], 'app');
    }

    public function affiliateVisits(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $visits = $this->db->fetchAll("SELECT * FROM affiliate_visits WHERE affiliate_id = ? ORDER BY created_at DESC LIMIT 50", [(int) $affiliate['id']]);

        $this->view('frontend/affiliate/visits', ['affiliate' => $affiliate, 'visits' => $visits, 'pageTitle' => 'Visitas'], 'app');
    }

    public function affiliateCreatives(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $affiliateModel = new \App\Models\Affiliate();
        $affiliate = $affiliateModel->findByUser((int) $user['id']);
        if (!$affiliate) { $this->redirect('/programa-de-afiliados'); return; }

        $this->view('frontend/affiliate/creatives', ['affiliate' => $affiliate, 'pageTitle' => 'Criativos'], 'app');
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

        $data = $request->only(['phone', 'payment_email', 'pix', 'website', 'followers_count', 'niche', 'content_type']);

        // Atualizar phone no user
        $userModel = new User();
        $userModel->update((int) $user['id'], ['phone' => $data['phone'] ?? '']);

        // Atualizar affiliate
        $notes = json_decode($affiliate['notes'] ?? '{}', true) ?: [];
        $notes['pix'] = $data['pix'] ?? '';
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
