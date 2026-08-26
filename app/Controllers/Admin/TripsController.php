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
            'youtube_url',
            'meta_title', 'meta_description', 'sort_order', 'featured', 'status',
        ]);

        $data['slug'] = $this->tripModel->generateSlug($data['title']);
        $data['partial_payment_enabled'] = isset($data['partial_payment_enabled']) ? 1 : 0;
        $data['group_discount_enabled'] = isset($data['group_discount_enabled']) ? 1 : 0;
        $data['featured'] = isset($data['featured']) ? 1 : 0;

        // FAQs como JSON
        $faqs = $request->input('faqs', []);
        $faqs = array_filter($faqs, fn($f) => !empty($f['question']));
        $data['faqs'] = !empty($faqs) ? json_encode(array_values($faqs)) : null;

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
        $gallery = $this->processGalleryUploads($request);
        $data['gallery'] = !empty($gallery) ? json_encode($gallery) : null;

        // Documentos extras
        $documents = $this->processDocumentUploads($request);
        $data['documents'] = !empty($documents) ? json_encode($documents) : null;

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

        // Hotéis e horários de pickup
        $tripHotelModel = new \App\Models\TripHotel();
        $tripHotels = $tripHotelModel->getByTripWithCount($id);
        foreach ($tripHotels as &$th) {
            $th['schedules'] = $tripHotelModel->getSchedules((int) $th['id']);
        }

        $this->view('admin/trips/form', [
            'trip' => $trip,
            'categories' => $categories,
            'tripCategories' => array_column($tripCategories, 'id'),
            'packages' => $packages,
            'itinerary' => $itinerary,
            'extraServices' => $extraServices,
            'fixedDates' => $fixedDates,
            'travelerCategories' => $travelerCategories,
            'tripHotels' => $tripHotels,
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

        $gallery = $this->processGalleryUploads($request);
        $data['gallery'] = !empty($gallery) ? json_encode($gallery) : null;

        // Documentos extras
        $documents = $this->processDocumentUploads($request);
        $data['documents'] = !empty($documents) ? json_encode($documents) : null;

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

        // Guardar preços antigos para detectar mudanças
        $oldPrices = [];
        foreach ($packages as $pkg) {
            $oldPrices[(int)$pkg['id']] = $this->packageModel->getBasePrice((int)$pkg['id']);
        }

        foreach ($rules as $rule) {
            if (empty($rule['package_id']) || empty($rule['category_id']) || empty($rule['price'])) continue;

            $packageId = (int) $rule['package_id'];
            $categoryId = (int) $rule['category_id'];
            $price = (float) $rule['price'];
            $salePrice = !empty($rule['sale_price']) ? (float) $rule['sale_price'] : null;

            // Inserir regra dinâmica
            $this->db->insert('trip_day_pricing', [
                'package_id' => $packageId,
                'traveler_category_id' => $categoryId,
                'rule_type' => $rule['rule_type'] ?? 'weekday',
                'day_key' => $rule['day_key'] ?? '0',
                'price' => $price,
                'sale_price' => $salePrice,
                'label' => $rule['label'] ?? null,
                'active' => 1,
            ]);

            // TAMBÉM atualizar o preço base na trip_package_categories
            $existing = $this->db->fetchOne(
                "SELECT id FROM trip_package_categories WHERE package_id = ? AND traveler_category_id = ?",
                [$packageId, $categoryId]
            );
            if ($existing) {
                $this->db->update('trip_package_categories', [
                    'price' => $price,
                    'sale_price' => $salePrice,
                ], 'id = ?', [(int) $existing['id']]);
            } else {
                // Criar se não existe
                $this->db->insert('trip_package_categories', [
                    'package_id' => $packageId,
                    'traveler_category_id' => $categoryId,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'min_pax' => 1,
                    'max_pax' => null,
                ]);
            }
        }

        // Verificar se o preço mudou e notificar clientes
        $newPrices = [];
        foreach ($packages as $pkg) {
            $newPrices[(int)$pkg['id']] = $this->packageModel->getBasePrice((int)$pkg['id']);
        }

        $trip = $this->tripModel->find($id);
        $priceChanged = false;
        foreach ($oldPrices as $pkgId => $oldPrice) {
            $newPrice = $newPrices[$pkgId] ?? $oldPrice;
            if (abs($newPrice - $oldPrice) > 0.01) {
                $priceChanged = true;
                break;
            }
        }

        // Se preço mudou, notificar clientes que têm este passeio no carrinho
        if ($priceChanged && $trip) {
            $this->notifyClientsOfPriceChange($id, $trip['title'], $oldPrices, $newPrices);
        }

        $this->flash('success', 'Regras de preço salvas com sucesso!');
        $this->redirect('/admin/passeios/' . $id . '/precos');
    }

    /**
     * Notifica clientes logados que possuem o passeio no carrinho sobre mudança de preço.
     * Registra a mudança no banco para que a notificação seja enviada quando o cliente acessar o carrinho.
     */
    private function notifyClientsOfPriceChange(int $tripId, string $tripTitle, array $oldPrices, array $newPrices): void
    {
        $oldPrice = reset($oldPrices);
        $newPrice = reset($newPrices);

        // Registrar mudança de preço no banco para notificação futura
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS price_change_notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                trip_id INT NOT NULL,
                trip_title VARCHAR(255) NOT NULL,
                old_price DECIMAL(10,2) NOT NULL,
                new_price DECIMAL(10,2) NOT NULL,
                notified_emails TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        );

        $this->db->insert('price_change_notifications', [
            'trip_id' => $tripId,
            'trip_title' => $tripTitle,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'notified_emails' => '',
        ]);

        // Notificar o admin imediatamente
        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            try {
                $emailService = new \App\Services\EmailService();
                $html = '<h3>⚠️ Preço alterado: ' . htmlspecialchars($tripTitle) . '</h3>';
                $html .= '<p>O preço foi alterado no painel admin.</p>';
                $html .= '<p>Preço anterior: <s>$' . number_format($oldPrice, 2) . '</s></p>';
                $html .= '<p>Novo preço: <strong style="color:#1B6F00;">$' . number_format($newPrice, 2) . '</strong></p>';
                $html .= '<p>Clientes com este passeio no carrinho serão notificados automaticamente ao acessarem o site.</p>';
                $emailService->send($adminEmail, 'Admin', 'Preço alterado: ' . $tripTitle, $html);
            } catch (\Throwable $e) {}
        }

        // Notificar admin por WhatsApp
        try {
            $evolutionApi = new \App\Services\EvolutionApi();
            $msg = "⚠️ *Preço alterado*\n\n";
            $msg .= "Passeio: *{$tripTitle}*\n";
            $msg .= "Preço anterior: \$" . number_format($oldPrice, 2) . "\n";
            $msg .= "Novo preço: \$" . number_format($newPrice, 2) . "\n\n";
            $msg .= "Clientes com este passeio no carrinho serão notificados ao acessar o site.";
            $evolutionApi->sendText('18294582170', $msg);
        } catch (\Throwable $e) {}
    }

    private function savePackages(int $tripId, Request $request): void
    {
        $packages = $request->input('packages', []);
        $existingPackages = $this->db->fetchAll("SELECT * FROM trip_packages WHERE trip_id = ? ORDER BY sort_order ASC", [$tripId]);
        $processedIds = [];

        foreach ($packages as $i => $pkg) {
            if (empty($pkg['title'])) continue;

            // Verificar se já existe um pacote com esse título
            $existingPkg = null;
            foreach ($existingPackages as $ep) {
                if ($ep['title'] === $pkg['title'] && !in_array((int)$ep['id'], $processedIds)) {
                    $existingPkg = $ep;
                    break;
                }
            }

            // Se não encontrou por título, tentar por posição (mesmo index)
            if (!$existingPkg && isset($existingPackages[$i]) && !in_array((int)$existingPackages[$i]['id'], $processedIds)) {
                $existingPkg = $existingPackages[$i];
            }

            if ($existingPkg) {
                // Atualizar pacote existente — NÃO toca nos preços
                $packageId = (int) $existingPkg['id'];
                $this->db->update('trip_packages', [
                    'title' => $pkg['title'],
                    'description' => $pkg['description'] ?? null,
                    'sort_order' => $i,
                ], 'id = ?', [$packageId]);
                $processedIds[] = $packageId;
            } else {
                // Criar novo pacote
                $packageId = $this->db->insert('trip_packages', [
                    'trip_id' => $tripId,
                    'title' => $pkg['title'],
                    'description' => $pkg['description'] ?? null,
                    'sort_order' => $i,
                    'status' => 1,
                ]);
                $processedIds[] = $packageId;
            }
        }

        // Remover pacotes que foram deletados pelo admin no form
        foreach ($existingPackages as $ep) {
            if (!in_array((int)$ep['id'], $processedIds)) {
                $this->db->delete('trip_package_categories', 'package_id = ?', [(int)$ep['id']]);
                $this->db->delete('trip_packages', 'id = ?', [(int)$ep['id']]);
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
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml', 'image/bmp', 'image/avif'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        if ($file['size'] > 10 * 1024 * 1024) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'trip-' . uniqid() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }

    /**
     * Processa uploads de galeria (múltiplos arquivos).
     */
    private function processGalleryUploads(Request $request): array
    {
        $urls = [];

        // Imagens existentes que foram mantidas pelo admin
        $existingImages = $request->input('gallery_existing', []);
        foreach ($existingImages as $img) {
            $img = trim($img);
            if (!empty($img)) {
                $urls[] = $img;
            }
        }

        // Arquivos enviados por upload
        if (isset($_FILES['gallery_files'])) {
            $files = $_FILES['gallery_files'];
            $count = is_array($files['name']) ? count($files['name']) : 0;
            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                $uploaded = $this->uploadImage([
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ]);
                if ($uploaded) {
                    $urls[] = $uploaded;
                }
            }
        }

        return $urls;
    }

    /**
     * Processa uploads de documentos extras.
     */
    private function processDocumentUploads(Request $request): array
    {
        $documents = [];

        // Documentos existentes mantidos pelo admin
        $existingDocs = $request->input('docs_existing', []);
        foreach ($existingDocs as $docJson) {
            $doc = json_decode($docJson, true);
            if ($doc && !empty($doc['path'])) {
                $documents[] = $doc;
            }
        }

        // Novos documentos enviados por upload
        if (isset($_FILES['doc_files'])) {
            $files = $_FILES['doc_files'];
            $count = is_array($files['name']) ? count($files['name']) : 0;
            $allowedTypes = [
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                if ($files['size'][$i] > 10 * 1024 * 1024) continue;
                if (!in_array($files['type'][$i], $allowedTypes)) continue;

                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $filename = 'doc-' . uniqid() . '.' . $ext;
                $destination = BASE_PATH . '/public/uploads/' . $filename;
                move_uploaded_file($files['tmp_name'][$i], $destination);

                $documents[] = [
                    'name' => $files['name'][$i],
                    'path' => '/uploads/' . $filename,
                    'type' => $ext,
                    'size' => $files['size'][$i],
                ];
            }
        }

        return $documents;
    }
}
