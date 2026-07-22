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
        $summary = $this->cartService->getSummary();

        $this->view('frontend/cart/index', [
            'cart' => $summary,
            'pageTitle' => 'Carrinho',
        ], 'app');
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
