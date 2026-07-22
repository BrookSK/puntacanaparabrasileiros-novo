<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TransferVehicle;
use App\Models\TransferLocation;
use App\Models\TransferBooking;

class TransfersController extends Controller
{
    private TransferVehicle $vehicleModel;
    private TransferLocation $locationModel;

    public function __construct()
    {
        parent::__construct();
        $this->vehicleModel = new TransferVehicle();
        $this->locationModel = new TransferLocation();
    }

    public function index(Request $request, Response $response): void
    {
        $this->redirect('/admin/transfers/veiculos');
    }

    public function vehicles(Request $request, Response $response): void
    {
        $vehicles = $this->vehicleModel->all('sort_order ASC');
        $this->view('admin/transfers/vehicles', [
            'vehicles' => $vehicles,
            'pageTitle' => 'Veículos de Transfer',
        ], 'admin');
    }

    public function createVehicle(Request $request, Response $response): void
    {
        $locations = $this->locationModel->getActive();
        $this->view('admin/transfers/vehicle-form', [
            'vehicle' => null,
            'locations' => $locations,
            'routes' => [],
            'pageTitle' => 'Novo Veículo',
        ], 'admin');
    }

    public function storeVehicle(Request $request, Response $response): void
    {
        $data = $request->only([
            'title', 'description', 'vehicle_type', 'max_passengers',
            'max_adults', 'max_children', 'max_infants', 'max_luggage', 'sort_order', 'status',
        ]);

        $data['slug'] = $this->vehicleModel->generateSlug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $vehicleId = $this->vehicleModel->create($data);
        $this->saveRoutes($vehicleId, $request);

        $this->flash('success', 'Veículo criado com sucesso!');
        $this->redirect('/admin/transfers/veiculos/' . $vehicleId . '/editar');
    }

    public function editVehicle(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) $this->abort(404);

        $locations = $this->locationModel->getActive();
        $routes = $this->vehicleModel->getRoutes($id);
        foreach ($routes as &$route) {
            $route['tariffs'] = $this->vehicleModel->getTariffs((int) $route['id']);
        }

        $this->view('admin/transfers/vehicle-form', [
            'vehicle' => $vehicle,
            'locations' => $locations,
            'routes' => $routes,
            'pageTitle' => 'Editar: ' . $vehicle['title'],
        ], 'admin');
    }

    public function updateVehicle(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $vehicle = $this->vehicleModel->find($id);
        if (!$vehicle) $this->abort(404);

        $data = $request->only([
            'title', 'description', 'vehicle_type', 'max_passengers',
            'max_adults', 'max_children', 'max_infants', 'max_luggage', 'sort_order', 'status',
        ]);

        if ($data['title'] !== $vehicle['title']) {
            $data['slug'] = $this->vehicleModel->generateSlug($data['title'], $id);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $this->vehicleModel->update($id, $data);
        $this->saveRoutes($id, $request);

        $this->flash('success', 'Veículo atualizado!');
        $this->redirect('/admin/transfers/veiculos/' . $id . '/editar');
    }

    public function locations(Request $request, Response $response): void
    {
        $locations = $this->locationModel->all('sort_order ASC, title ASC');
        $this->view('admin/transfers/locations', [
            'locations' => $locations,
            'pageTitle' => 'Locais de Transfer',
        ], 'admin');
    }

    public function storeLocation(Request $request, Response $response): void
    {
        $data = $request->only(['title', 'address', 'latitude', 'longitude', 'location_type', 'sort_order']);
        $data['slug'] = mb_strtolower(preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($data['title'])));
        $data['status'] = 1;
        $this->locationModel->create($data);
        $this->flash('success', 'Local criado!');
        $this->redirect('/admin/transfers/locais');
    }

    public function updateLocation(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $data = $request->only(['title', 'address', 'latitude', 'longitude', 'location_type', 'sort_order', 'status']);
        $this->locationModel->update($id, $data);
        $this->flash('success', 'Local atualizado!');
        $this->redirect('/admin/transfers/locais');
    }

    public function bookings(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $transferModel = new TransferBooking();
        $bookings = $transferModel->paginate($page, 20, '1=1', [], 'created_at DESC');

        $this->view('admin/transfers/bookings', [
            'bookings' => $bookings,
            'pageTitle' => 'Reservas de Transfer',
        ], 'admin');
    }

    private function saveRoutes(int $vehicleId, Request $request): void
    {
        $routes = $request->input('routes', []);
        // Limpar rotas e tarifas antigas
        $existingRoutes = $this->db->fetchAll("SELECT id FROM transfer_routes WHERE vehicle_id = ?", [$vehicleId]);
        foreach ($existingRoutes as $er) {
            $this->db->delete('transfer_tariffs', 'route_id = ?', [(int) $er['id']]);
        }
        $this->db->delete('transfer_routes', 'vehicle_id = ?', [$vehicleId]);

        foreach ($routes as $route) {
            if (empty($route['origin_id']) || empty($route['destination_id'])) continue;
            $routeId = $this->db->insert('transfer_routes', [
                'vehicle_id' => $vehicleId,
                'origin_id' => (int) $route['origin_id'],
                'destination_id' => (int) $route['destination_id'],
                'base_price' => (float) ($route['base_price'] ?? 0),
                'duration' => !empty($route['duration']) ? (int) $route['duration'] : null,
                'status' => 1,
            ]);

            // Tarifas por faixa
            if (!empty($route['tariffs'])) {
                foreach ($route['tariffs'] as $tariff) {
                    if (empty($tariff['price'])) continue;
                    $this->db->insert('transfer_tariffs', [
                        'route_id' => $routeId,
                        'service_type' => $tariff['service_type'] ?? 'private',
                        'min_pax' => (int) ($tariff['min_pax'] ?? 1),
                        'max_pax' => (int) ($tariff['max_pax'] ?? 10),
                        'price' => (float) $tariff['price'],
                    ]);
                }
            }
        }
    }

    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'vehicle-' . uniqid() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }
}
