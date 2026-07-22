<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\PricingService;

class PricingController extends Controller
{
    public function getDayPrices(Request $request, Response $response): void
    {
        $packageId = (int) $request->input('package_id');
        $date = $request->input('date', '');

        if (!$packageId || !$date) {
            $this->json(['error' => 'Pacote e data são obrigatórios.'], 400);
            return;
        }

        // Validar formato de data
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj) {
            $this->json(['error' => 'Formato de data inválido. Use YYYY-MM-DD.'], 400);
            return;
        }

        $pricingService = new PricingService();
        $prices = $pricingService->getPriceForDate($packageId, $date);

        $this->json([
            'success' => true,
            'package_id' => $packageId,
            'date' => $date,
            'prices' => $prices,
        ]);
    }
}
