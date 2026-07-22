<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TransferLocation;

class TransferController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $locationModel = new TransferLocation();
        $locations = $locationModel->getActive();

        $vehicleModel = new \App\Models\TransferVehicle();
        $vehicles = $vehicleModel->getActive();

        $this->view('frontend/transfers/search', [
            'locations' => $locations,
            'vehicles' => $vehicles,
            'pageTitle' => 'Transfers em Punta Cana',
            'metaDescription' => 'Reserve seu transfer do aeroporto para o hotel em Punta Cana. Serviço privado ou compartilhado.',
        ], 'app');
    }
}
