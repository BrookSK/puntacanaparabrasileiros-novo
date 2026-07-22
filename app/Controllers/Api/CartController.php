<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\CartService;
use App\Models\TransferVehicle;
use App\Models\TransferLocation;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
    }

    public function addTransfer(Request $request, Response $response): void
    {
        $vehicleId = (int) $request->input('vehicle_id');
        $originId = (int) $request->input('origin_id');
        $destinationId = (int) $request->input('destination_id');
        $date = $request->input('date', '');
        $time = $request->input('time', '');
        $type = $request->input('type', 'arrival'); // arrival or departure
        $serviceType = $request->input('service_type', 'private');
        $adults = (int) $request->input('adults', '1');
        $children = (int) $request->input('children', '0');
        $infants = (int) $request->input('infants', '0');
        $groupId = $request->input('group_id');
        $flightNumber = $request->input('flight_number');
        $flightTime = $request->input('flight_time');

        // Validação
        if (!$vehicleId || !$originId || !$destinationId || !$date || !$time) {
            $this->json(['error' => 'Dados incompletos.'], 400);
            return;
        }

        // Recalcular preço no servidor
        $vehicleModel = new TransferVehicle();
        $totalPax = $adults + $children + $infants;

        // Buscar route_id
        $route = $this->db->fetchOne(
            "SELECT id FROM transfer_routes WHERE vehicle_id = ? AND origin_id = ? AND destination_id = ?",
            [$vehicleId, $originId, $destinationId]
        );

        if (!$route) {
            // Tentar rota inversa
            $route = $this->db->fetchOne(
                "SELECT id FROM transfer_routes WHERE vehicle_id = ? AND origin_id = ? AND destination_id = ?",
                [$vehicleId, $destinationId, $originId]
            );
        }

        if (!$route) {
            $this->json(['error' => 'Rota não encontrada.'], 400);
            return;
        }

        $price = $vehicleModel->calculatePrice((int) $route['id'], $totalPax, $serviceType);
        if ($price === null) {
            $this->json(['error' => 'Preço não disponível para esta configuração.'], 400);
            return;
        }

        // Buscar nomes
        $vehicle = $vehicleModel->find($vehicleId);
        $locationModel = new TransferLocation();
        $origin = $locationModel->find($originId);
        $destination = $locationModel->find($destinationId);

        $transferItem = [
            'vehicle_id' => $vehicleId,
            'vehicle_title' => $vehicle['title'] ?? '',
            'vehicle_image' => $vehicle['image'] ?? '',
            'origin_id' => $originId,
            'origin_title' => $origin['title'] ?? '',
            'destination_id' => $destinationId,
            'destination_title' => $destination['title'] ?? '',
            'date' => $date,
            'time' => $time,
            'type' => $type,
            'service_type' => $serviceType,
            'adults' => $adults,
            'children' => $children,
            'infants' => $infants,
            'price' => $price,
            'group_id' => $groupId ?? uniqid('tg_'),
            'flight_number' => $flightNumber,
            'flight_time' => $flightTime,
        ];

        $this->cartService->addTransfer($transferItem);

        $this->json([
            'success' => true,
            'message' => 'Transfer adicionado ao carrinho!',
            'cart_count' => $this->cartService->getItemCount(),
            'transfer_total' => $this->cartService->getTransferTotal(),
        ]);
    }

    public function removeTransfer(Request $request, Response $response): void
    {
        $itemId = $request->input('item_id', '');
        $groupId = $request->input('group_id');

        if ($groupId) {
            $this->cartService->removeTransferGroup($groupId);
        } else {
            $this->cartService->removeTransfer($itemId);
        }

        $this->json([
            'success' => true,
            'cart_count' => $this->cartService->getItemCount(),
            'grand_total' => $this->cartService->getGrandTotal(),
        ]);
    }

    public function count(Request $request, Response $response): void
    {
        $this->json([
            'count' => $this->cartService->getItemCount(),
            'total' => $this->cartService->getGrandTotal(),
        ]);
    }

    /**
     * Inscrição na newsletter via AJAX.
     */
    public function newsletterSubscribe(Request $request, Response $response): void
    {
        $email = trim($request->input('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['error' => 'Informe um email válido.'], 400);
            return;
        }

        $model = new \App\Models\NewsletterSubscriber();
        $subscribed = $model->subscribe($email, null, 'blog_sidebar', $request->ip());

        if ($subscribed) {
            $this->json(['success' => true, 'message' => 'Inscrição realizada com sucesso!']);
        } else {
            $this->json(['success' => true, 'message' => 'Este email já está inscrito.']);
        }
    }
}
