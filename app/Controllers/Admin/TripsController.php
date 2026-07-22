<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripPackage;

class TripsController extends Controller
{
    private Trip $tripModel;
    private TripCategory $categoryModel;
    private TripPackage $packageModel;

    public function __construct()
    {
        parent::__construct();
        $this->tripModel = new Trip();
        $this->categoryModel = new TripCategory();
        $this->packageModel = new TripPackage();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status');
        $search = $request->query('busca');

        if ($search) {
            $trips = $this->tripModel->paginate($page, 20, "title LIKE ?", ['%' . $search . '%'], 'created_at DESC');
        } elseif ($status) {
            $trips = $this->tripModel->paginate($page, 20, "status = ?", [$status], 'created_at DESC');
        } else {
            $trips = $this->tripModel->paginate($page, 20, '1=1', [], 'sort_order ASC, created_at DESC');
        }

        $this->view('admin/trips/index', [
            'trips' => $trips,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'pageTitle' => 'Gerenciar Passeios',
        ], 'admin');
    }

    public function create(Request $request, Response $response): void
    {
        $categories = $this->categoryModel->getAll();
        $travelerCategories = $this->db->fetchAll("SELECT * FROM traveler_categories ORDER BY sort_order ASC");

        $this->view('admin/trips/form', [
            'trip' => null,
            'categories' => $categories,
            'travelerCategories' => $travelerCategories,
            'pageTitle' => 'Novo Passeio',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $request->only([
            'title', 'description', 'short_description', 'duration', 'duration_unit',
            'difficulty', 'min_pax', 'max_pax', 'meeting_point', 'important_notes',
            'partial_payment_enabled', 'partial_payment_percent',
            'group_discount_enabled', 'group_discount_rules',
            'meta_title', 'meta_description', 'sort_order', 'featured', 'status',
        ]);

        $data['slug'] = $this->tripModel->generateSlug($data['title']);
        $data['partial_payment_enabled'] = isset($data['partial_payment_enabled']) ? 1 : 0;
        $data['group_discount_enabled'] = isset($data['group_discount_enabled']) ? 1 : 0;
        $data['featured'] = isset($data['featured']) ? 1 : 0;

        // Includes/Excludes como JSON
        $includes = $request->input('includes', []);
        $excludes = $request->input('excludes', []);
        $data['includes'] = !empty($includes) ? json_encode(array_filter($includes)) : null;
        $data['excludes'] = !empty($excludes) ? json_encode(array_filter($excludes)) : null;

        // Upload de imagem
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        // Gallery
        $gallery = $request->input('gallery_images', []);
        $data['gallery'] = !empty($gallery) ? json_encode($gallery) : null;

        $tripId = $this->tripModel->create($data);

        // Categorias
        $categoryIds = $request->input('categories', []);
        $this->tripModel->syncCategories($tripId, $categoryIds);

        // Pacotes
        $this->savePackages($tripId, $request);

        // Itinerário
        $this->saveItinerary($tripId, $request);

        // Serviços extras
        $this->saveExtraServices($tripId, $request);

        $this->flash('success', 'Passeio criado com sucesso!');
        $this->redirect('/admin/passeios/' . $tripId . '/editar');
    }

    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $trip = $this->tripModel->find($id);
        if (!$trip) $this->abort(404);

        $categories = $this->categoryModel->getAll();
        $tripCategories = $this->tripModel->getCategories($id);
        $packages = $this->packageModel->getByTrip($id);
        foreach ($packages as &$pkg) {
            $pkg['categories'] = $this->packageModel->getCategories((int) $pkg['id']);
        }
        $itinerary = $this->tripModel->getItinerary($id);
        $extraServices = $this->tripModel->getExtraServices($id);
        $fixedDates = $this->tripModel->getFixedDates($id, false);
        $travelerCategories = $this->db->fetchAll("SELECT * FROM traveler_categories ORDER BY sort_order ASC");

        $this->view('admin/trips/form', [
            'trip' => $trip,
            'categories' => $categories,
            'tripCategories' => array_column($tripCategories, 'id'),
            'packages' => $packages,
            'itinerary' => $itinerary,
            'extraServices' => $extraServices,
            'fixedDates' => $fixedDates,
            'travelerCategories' => $travelerCategories,
            'pageTitle' => 'Editar: ' . $trip['title'],
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $trip = $this->tripModel->find($id);
        if (!$trip) $this->abort(404);

        $data = $request->only([
            'title', 'description', 'short_description', 'duration', 'duration_unit',
            'difficulty', 'min_pax', 'max_pax', 'meeting_point', 'important_notes',
            'partial_payment_enabled', 'partial_payment_percent',
            'group_discount_enabled', 'group_discount_rules',
            'meta_title', 'meta_description', 'sort_order', 'featured', 'status',
        ]);

        if ($data['title'] !== $trip['title']) {
            $data['slug'] = $this->tripModel->generateSlug($data['title'], $id);
        }

        $data['partial_payment_enabled'] = isset($data['partial_payment_enabled']) ? 1 : 0;
        $data['group_discount_enabled'] = isset($data['group_discount_enabled']) ? 1 : 0;
        $data['featured'] = isset($data['featured']) ? 1 : 0;

        $includes = $request->input('includes', []);
        $excludes = $request->input('excludes', []);
        $data['includes'] = !empty($includes) ? json_encode(array_filter($includes)) : null;
        $data['excludes'] = !empty($excludes) ? json_encode(array_filter($excludes)) : null;

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        $gallery = $request->input('gallery_images', []);
        if (!empty($gallery)) {
            $data['gallery'] = json_encode($gallery);
        }

        $this->tripModel->update($id, $data);

        // Categorias
        $categoryIds = $request->input('categories', []);
        $this->tripModel->syncCategories($id, $categoryIds);

        // Pacotes
        $this->savePackages($id, $request);

        // Itinerário
        $this->saveItinerary($id, $request);

        // Serviços extras
        $this->saveExtraServices($id, $request);

        // Datas fixas
        $this->saveFixedDates($id, $request);

        $this->flash('success', 'Passeio atualizado com sucesso!');
        $this->redirect('/admin/passeios/' . $id . '/editar');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->tripModel->delete($id);
        $this->flash('success', 'Passeio excluído.');
        $this->redirect('/admin/passeios');
    }

    public function pricing(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $trip = $this->tripModel->find($id);
        if (!$trip) $this->abort(404);

        $packages = $this->packageModel->getByTrip($id);
        foreach ($packages as &$pkg) {
            $pkg['categories'] = $this->packageModel->getCategories((int) $pkg['id']);
            $pkg['day_pricing'] = $this->db->fetchAll(
                "SELECT * FROM trip_day_pricing WHERE package_id = ? ORDER BY rule_type, day_key",
                [(int) $pkg['id']]
            );
        }

        $travelerCategories = $this->db->fetchAll("SELECT * FROM traveler_categories ORDER BY sort_order ASC");

        $this->view('admin/trips/pricing', [
            'trip' => $trip,
            'packages' => $packages,
            'travelerCategories' => $travelerCategories,
            'pageTitle' => 'Preços por Dia: ' . $trip['title'],
        ], 'admin');
    }

    public function savePricing(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $rules = $request->input('pricing_rules', []);

        // Limpar regras antigas e inserir novas
        $packages = $this->packageModel->getByTrip($id);
        foreach ($packages as $pkg) {
            $this->db->delete('trip_day_pricing', 'package_id = ?', [(int) $pkg['id']]);
        }

        foreach ($rules as $rule) {
            if (empty($rule['package_id']) || empty($rule['category_id']) || empty($rule['price'])) continue;
            $this->db->insert('trip_day_pricing', [
                'package_id' => (int) $rule['package_id'],
                'traveler_category_id' => (int) $rule['category_id'],
                'rule_type' => $rule['rule_type'] ?? 'weekday',
                'day_key' => $rule['day_key'] ?? '0',
                'price' => (float) $rule['price'],
                'sale_price' => !empty($rule['sale_price']) ? (float) $rule['sale_price'] : null,
                'label' => $rule['label'] ?? null,
                'active' => 1,
            ]);
        }

        $this->flash('success', 'Regras de preço salvas com sucesso!');
        $this->redirect('/admin/passeios/' . $id . '/precos');
    }

    private function savePackages(int $tripId, Request $request): void
    {
        $packages = $request->input('packages', []);
        // Remover pacotes antigos
        $this->db->delete('trip_packages', 'trip_id = ?', [$tripId]);

        foreach ($packages as $i => $pkg) {
            if (empty($pkg['title'])) continue;
            $packageId = $this->db->insert('trip_packages', [
                'trip_id' => $tripId,
                'title' => $pkg['title'],
                'description' => $pkg['description'] ?? null,
                'sort_order' => $i,
                'status' => 1,
            ]);

            // Categorias de preço do pacote
            if (!empty($pkg['categories'])) {
                $pkgModel = new TripPackage();
                $pkgModel->syncCategories($packageId, $pkg['categories']);
            }
        }
    }

    private function saveItinerary(int $tripId, Request $request): void
    {
        $items = $request->input('itinerary', []);
        $this->db->delete('trip_itinerary', 'trip_id = ?', [$tripId]);
        foreach ($items as $i => $item) {
            if (empty($item['title'])) continue;
            $this->db->insert('trip_itinerary', [
                'trip_id' => $tripId,
                'day_number' => (int) ($item['day_number'] ?? ($i + 1)),
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'sort_order' => $i,
            ]);
        }
    }

    private function saveExtraServices(int $tripId, Request $request): void
    {
        $services = $request->input('extra_services', []);
        $this->db->delete('trip_extra_services', 'trip_id = ?', [$tripId]);
        foreach ($services as $i => $svc) {
            if (empty($svc['name'])) continue;
            $this->db->insert('trip_extra_services', [
                'trip_id' => $tripId,
                'name' => $svc['name'],
                'description' => $svc['description'] ?? null,
                'price' => (float) ($svc['price'] ?? 0),
                'price_type' => $svc['price_type'] ?? 'per_person',
                'required' => isset($svc['required']) ? 1 : 0,
                'sort_order' => $i,
            ]);
        }
    }

    private function saveFixedDates(int $tripId, Request $request): void
    {
        $dates = $request->input('fixed_dates', []);
        $this->db->delete('trip_fixed_dates', 'trip_id = ?', [$tripId]);
        foreach ($dates as $fd) {
            if (empty($fd['date'])) continue;
            $this->db->insert('trip_fixed_dates', [
                'trip_id' => $tripId,
                'date' => $fd['date'],
                'time' => $fd['time'] ?? null,
                'max_pax' => !empty($fd['max_pax']) ? (int) $fd['max_pax'] : null,
                'status' => $fd['status'] ?? 'available',
            ]);
        }
    }

    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        if ($file['size'] > 10 * 1024 * 1024) return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'trip-' . uniqid() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }
}
