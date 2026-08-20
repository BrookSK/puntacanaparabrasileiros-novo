<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\CartService;
use App\Services\PricingService;
use App\Models\Trip;
use App\Models\TripPackage;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
    }

    public function index(Request $request, Response $response): void
    {
        $this->cartService->cleanExpired();

        // Verificar se preços mudaram e atualizar
        $priceChanges = $this->checkAndUpdatePrices();

        $summary = $this->cartService->getSummary();

        if (!empty($priceChanges)) {
            $msg = 'Atenção: o preço de alguns itens foi atualizado.';
            foreach ($priceChanges as $change) {
                $msg .= ' ' . $change['title'] . ': de $' . number_format($change['old_price'], 2) . ' para $' . number_format($change['new_price'], 2) . '.';
            }
            $this->flash('warning', $msg);

            // Notificar admin
            $this->notifyAdminPriceChange($priceChanges);
        }

        $this->view('frontend/cart/index', [
            'cart' => $summary,
            'pageTitle' => 'Carrinho',
        ], 'app');
    }

    /**
     * Verifica e atualiza preços dos itens no carrinho.
     */
    private function checkAndUpdatePrices(): array
    {
        $changes = [];
        $pricingService = new PricingService();
        $trips = $this->cartService->getTrips();
        $updated = false;

        foreach ($trips as $idx => $item) {
            $packageId = (int) ($item['package_id'] ?? 0);
            $date = $item['date'] ?? '';
            $pax = $item['pax'] ?? [];
            $extras = $item['extra_services'] ?? [];

            if (!$packageId || !$date || empty($pax)) continue;

            try {
                $calculation = $pricingService->calculateItemTotal($packageId, $date, $pax, $extras);
                $newTotal = (float) $calculation['total'];
                $oldTotal = (float) ($item['total'] ?? 0);

                if (abs($newTotal - $oldTotal) > 0.01) {
                    $changes[] = [
                        'type' => 'trip',
                        'title' => $item['trip_title'] ?? 'Passeio',
                        'old_price' => $oldTotal,
                        'new_price' => $newTotal,
                        'date' => $date,
                    ];

                    $trips[$idx]['total'] = $newTotal;
                    $trips[$idx]['subtotal'] = $calculation['subtotal'];
                    $trips[$idx]['breakdown'] = $calculation['breakdown'];
                    $trips[$idx]['extras_total'] = $calculation['extras_total'];
                    $trips[$idx]['group_discount'] = $calculation['group_discount'];
                    $updated = true;
                }
            } catch (\Throwable $e) {}
        }

        if ($updated) {
            $this->session->set('cart_items', $trips);
        }

        return $changes;
    }

    /**
     * Notifica admin sobre mudança de preço.
     */
    private function notifyAdminPriceChange(array $changes): void
    {
        $user = $this->currentUser();
        $customerName = $user ? ($user['first_name'] . ' ' . ($user['last_name'] ?? '')) : 'Visitante';
        $customerEmail = $user['email'] ?? 'não identificado';

        // WhatsApp
        $msg = "⚠️ ALERTA: Preço alterado no carrinho\n\n";
        $msg .= "Cliente: {$customerName} ({$customerEmail})\n\n";
        foreach ($changes as $change) {
            $msg .= "• {$change['title']} ({$change['date']})\n";
            $msg .= "  Antes: \$" . number_format($change['old_price'], 2) . "\n";
            $msg .= "  Agora: \$" . number_format($change['new_price'], 2) . "\n\n";
        }

        try {
            $evolutionApi = new \App\Services\EvolutionApi();
            $evolutionApi->sendText('18294582170', $msg);
        } catch (\Throwable $e) {}

        // Email
        try {
            $adminEmail = \Core\App::getInstance()->setting('admin_email', '');
            if ($adminEmail) {
                $emailService = new \App\Services\EmailService();
                $html = '<h3>⚠️ Preço alterado no carrinho de cliente</h3>';
                $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($customerName) . ' (' . htmlspecialchars($customerEmail) . ')</p>';
                $html .= '<ul>';
                foreach ($changes as $change) {
                    $html .= '<li><strong>' . htmlspecialchars($change['title']) . '</strong> (' . htmlspecialchars($change['date']) . '): de $' . number_format($change['old_price'], 2) . ' para <strong>$' . number_format($change['new_price'], 2) . '</strong></li>';
                }
                $html .= '</ul>';
                $emailService->send($adminEmail, 'Admin', 'Alerta: Preço alterado no carrinho', $html);
            }
        } catch (\Throwable $e) {}
    }

    public function add(Request $request, Response $response): void
    {
        $tripId = (int) $request->input('trip_id');
        $packageId = (int) $request->input('package_id');
        $date = $request->input('date', '');
        $time = $request->input('time');
        $pax = $request->input('pax', []); // ['category_id' => quantity]
        $extraServiceIds = $request->input('extra_services', []);

        // Validação básica
        if (!$tripId || !$packageId || !$date || empty($pax)) {
            if ($request->expectsJson()) {
                $this->json(['error' => 'Dados incompletos.'], 400);
                return;
            }
            $this->flash('error', 'Dados incompletos para adicionar ao carrinho.');
            $this->back();
            return;
        }

        // Buscar dados do trip e calcular preço no servidor
        $tripModel = new Trip();
        $trip = $tripModel->find($tripId);
        if (!$trip) {
            $this->flash('error', 'Passeio não encontrado.');
            $this->back();
            return;
        }

        $pricingService = new PricingService();
        $calculation = $pricingService->calculateItemTotal($packageId, $date, $pax, $extraServiceIds);

        // Buscar nome do pacote
        $packageModel = new TripPackage();
        $package = $packageModel->find($packageId);

        $cartItem = [
            'trip_id' => $tripId,
            'trip_title' => $trip['title'],
            'trip_slug' => $trip['slug'],
            'trip_image' => $trip['featured_image'],
            'package_id' => $packageId,
            'package_title' => $package['title'] ?? '',
            'date' => $date,
            'time' => $time,
            'hotel_id' => $request->input('hotel_id', ''),
            'hotel_name' => $request->input('hotel_name', ''),
            'pickup_time' => $request->input('pickup_time', ''),
            'pax' => $pax,
            'extra_services' => $extraServiceIds,
            'breakdown' => $calculation['breakdown'],
            'subtotal' => $calculation['subtotal'],
            'extras_total' => $calculation['extras_total'],
            'group_discount' => $calculation['group_discount'],
            'total' => $calculation['total'],
            'total_pax' => $calculation['total_pax'],
        ];

        $this->cartService->addTrip($cartItem);

        if ($request->expectsJson()) {
            $this->json([
                'success' => true,
                'message' => 'Passeio adicionado ao carrinho!',
                'cart_count' => $this->cartService->getItemCount(),
            ]);
            return;
        }

        $this->flash('success', 'Passeio adicionado ao carrinho!');

        // Se veio com redirect=checkout, vai direto
        if ($request->input('redirect') === 'checkout') {
            $this->redirect('/checkout');
            return;
        }

        $this->redirect('/carrinho');
    }

    public function remove(Request $request, Response $response): void
    {
        $itemId = $request->input('item_id', '');
        $type = $request->input('type', 'trip');

        if ($type === 'transfer') {
            $groupId = $request->input('group_id');
            if ($groupId) {
                $this->cartService->removeTransferGroup($groupId);
            } else {
                $this->cartService->removeTransfer($itemId);
            }
        } else {
            $this->cartService->removeTrip($itemId);
        }

        if ($request->expectsJson()) {
            $this->json([
                'success' => true,
                'cart_count' => $this->cartService->getItemCount(),
                'grand_total' => $this->cartService->getGrandTotal(),
            ]);
            return;
        }

        $this->flash('success', 'Item removido do carrinho.');
        $this->redirect('/carrinho');
    }

    public function clear(Request $request, Response $response): void
    {
        $this->cartService->clearAll();

        if ($request->expectsJson()) {
            $this->json(['success' => true]);
            return;
        }

        $this->flash('success', 'Carrinho limpo.');
        $this->redirect('/carrinho');
    }
}
