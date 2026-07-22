<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TransferVehicle;
use App\Models\TransferLocation;

class TransferSearchController extends Controller
{
    public function search(Request $request, Response $response): void
    {
        $originId = (int) $request->input('origin_id');
        $destinationId = (int) $request->input('destination_id');
        $adults = (int) $request->input('adults', '1');
        $children = (int) $request->input('children', '0');
        $infants = (int) $request->input('infants', '0');
        $serviceType = $request->input('service_type', 'private');
        $date = $request->input('date', '');
        $time = $request->input('time', '');

        // Validação
        if (!$originId || !$destinationId) {
            $this->json(['error' => 'Origem e destino são obrigatórios.'], 400);
            return;
        }

        if ($originId === $destinationId) {
            $this->json(['error' => 'Origem e destino devem ser diferentes.'], 400);
            return;
        }

        $totalPax = $adults + $children + $infants;
        if ($totalPax < 1) {
            $this->json(['error' => 'Pelo menos 1 passageiro é necessário.'], 400);
            return;
        }

        // Buscar veículos disponíveis
        $vehicleModel = new TransferVehicle();
        $vehicles = $vehicleModel->searchAvailable($originId, $destinationId, $totalPax, $serviceType);

        // Buscar nomes dos locais
        $locationModel = new TransferLocation();
        $origin = $locationModel->find($originId);
        $destination = $locationModel->find($destinationId);

        $results = [];
        foreach ($vehicles as $vehicle) {
            $results[] = [
                'id' => $vehicle['id'],
                'title' => $vehicle['title'],
                'description' => $vehicle['description'],
                'image' => $vehicle['image'],
                'vehicle_type' => $vehicle['vehicle_type'],
                'max_passengers' => (int) $vehicle['max_passengers'],
                'max_luggage' => (int) $vehicle['max_luggage'],
                'price' => $vehicle['calculated_price'],
                'duration' => (int) ($vehicle['duration'] ?? 0),
                'route_id' => (int) $vehicle['route_id'],
            ];
        }

        $this->json([
            'success' => true,
            'origin' => $origin['title'] ?? '',
            'destination' => $destination['title'] ?? '',
            'total_pax' => $totalPax,
            'service_type' => $serviceType,
            'results' => $results,
            'count' => count($results),
        ]);
    }
}
