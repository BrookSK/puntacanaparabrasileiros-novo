<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripPackage;
use App\Models\Wishlist;

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
        $category = $request->query('categoria');
        $search = $request->query('busca');
        $orderBy = $request->query('ordenar', 'relevancia');
        $duration = $request->query('duracao');

        // Validar valores permitidos
        $allowedOrders = ['relevancia', 'preco_asc', 'preco_desc', 'recente', 'antigo'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'relevancia';
        }

        if ($search) {
            $trips = $this->tripModel->search($search, $page, 12);
        } elseif ($category) {
            $cat = $this->categoryModel->findBySlug($category);
            if ($cat) {
                $trips = $this->tripModel->getByCategory((int) $cat['id'], $page, 12, $orderBy);
            } else {
                $trips = $this->tripModel->getPublished($page, 12, $orderBy);
            }
        } else {
            $trips = $this->tripModel->getPublished($page, 12, $orderBy);
        }

        // Adicionar preço mínimo a cada trip
        foreach ($trips['items'] as &$trip) {
            $packages = $this->packageModel->getByTrip((int) $trip['id']);
            $trip['min_price'] = 0;
            $trip['regular_price'] = 0;
            if (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing'])) {
                // Group pricing: usar preço da primeira faixa
                $gpRules = json_decode($trip['group_pricing'], true);
                if (is_array($gpRules) && !empty($gpRules)) {
                    usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                    $trip['min_price'] = (float) $gpRules[0]['price'];
                    $trip['regular_price'] = $trip['min_price'];
                }
            } elseif (!empty($packages)) {
                $trip['min_price'] = $this->packageModel->getBasePrice((int) $packages[0]['id']);
                $trip['regular_price'] = $this->packageModel->getRegularPrice((int) $packages[0]['id']);
            }
            $trip['rating'] = $this->tripModel->getAverageRating((int) $trip['id']);
        }

        $categories = $this->categoryModel->getWithTripCount();

        // Buscar passeios em destaque (featured)
        $featuredTrips = $this->tripModel->getFeatured(6);
        foreach ($featuredTrips as &$ft) {
            $packages = $this->packageModel->getByTrip((int) $ft['id']);
            $ft['min_price'] = 0;
            $ft['regular_price'] = 0;
            if (!empty($ft['group_pricing_enabled']) && !empty($ft['group_pricing'])) {
                $gpRules = json_decode($ft['group_pricing'], true);
                if (is_array($gpRules) && !empty($gpRules)) {
                    usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                    $ft['min_price'] = (float) $gpRules[0]['price'];
                    $ft['regular_price'] = $ft['min_price'];
                }
            } elseif (!empty($packages)) {
                $ft['min_price'] = $this->packageModel->getBasePrice((int) $packages[0]['id']);
                $ft['regular_price'] = $this->packageModel->getRegularPrice((int) $packages[0]['id']);
            }
            $ft['rating'] = $this->tripModel->getAverageRating((int) $ft['id']);
        }

        $this->view('frontend/trips/index', [
            'trips' => $trips,
            'featuredTrips' => $featuredTrips,
            'categories' => $categories,
            'currentCategory' => $category,
            'currentSearch' => $search,
            'currentOrder' => $orderBy,
            'currentDuration' => $duration,
            'pageTitle' => 'Passeios em Punta Cana',
        ], 'app');
    }

    public function category(Request $request, Response $response): void
    {
        $slug = $request->param('slug', '');
        $page = max(1, (int) $request->query('page', '1'));
        $orderBy = $request->query('ordenar', 'relevancia');

        // Validar valores permitidos
        $allowedOrders = ['relevancia', 'preco_asc', 'preco_desc', 'recente'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'relevancia';
        }

        $cat = $this->categoryModel->findBySlug($slug);
        if (!$cat) {
            $this->abort(404, 'Categoria não encontrada.');
        }

        // Coletar todos os filtros da sidebar
        $priceRange = $this->tripModel->getPriceRange();
        $durationRange = $this->tripModel->getDurationRange();

        $currentFilters = [
            'destino' => array_filter((array) ($request->query('destino') ?? [])),
            'preco_min' => $request->query('preco_min'),
            'preco_max' => $request->query('preco_max'),
            'duracao_min' => $request->query('duracao_min'),
            'duracao_max' => $request->query('duracao_max'),
            'atividade' => array_filter((array) ($request->query('atividade') ?? [])),
            'tag' => array_filter((array) ($request->query('tag') ?? [])),
            'data' => array_filter((array) ($request->query('data') ?? [])),
        ];

        // Não aplicar filtro de preço se os valores são iguais ao range completo
        if ((int)($currentFilters['preco_min'] ?? 0) <= $priceRange['min']) {
            $currentFilters['preco_min'] = null;
        }
        if ((int)($currentFilters['preco_max'] ?? 0) >= $priceRange['max']) {
            $currentFilters['preco_max'] = null;
        }
        // Não aplicar filtro de duração se os valores são iguais ao range completo
        if ((int)($currentFilters['duracao_min'] ?? 0) <= $durationRange['min']) {
            $currentFilters['duracao_min'] = null;
        }
        if ((int)($currentFilters['duracao_max'] ?? 0) >= $durationRange['max']) {
            $currentFilters['duracao_max'] = null;
        }

        // Buscar passeios com filtros
        $trips = $this->tripModel->getFiltered((int) $cat['id'], $currentFilters, $orderBy, $page, 12);

        // Adicionar preço mínimo e próximas datas a cada trip
        foreach ($trips['items'] as &$trip) {
            $packages = $this->packageModel->getByTrip((int) $trip['id']);
            $trip['min_price'] = 0;
            $trip['regular_price'] = 0;
            if (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing'])) {
                $gpRules = json_decode($trip['group_pricing'], true);
                if (is_array($gpRules) && !empty($gpRules)) {
                    usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                    $trip['min_price'] = (float) $gpRules[0]['price'];
                    $trip['regular_price'] = $trip['min_price'];
                }
            } elseif (!empty($packages)) {
                $trip['min_price'] = $this->packageModel->getBasePrice((int) $packages[0]['id']);
                $trip['regular_price'] = $this->packageModel->getRegularPrice((int) $packages[0]['id']);
            }
            $trip['next_dates'] = $this->tripModel->getFixedDates((int) $trip['id'], true);
        }

        // Dados para os filtros da sidebar
        $categories = $this->categoryModel->getWithTripCount();

        // Tags e datas (requerem migration 017)
        try {
            $destinations = $this->tripModel->getTagsWithCount('destino');
            $activities = $this->tripModel->getTagsWithCount('atividade');
            $tags = $this->tripModel->getTagsWithCount('tag');
            $availableDates = $this->tripModel->getAvailableMonths();
        } catch (\Throwable $e) {
            $destinations = [];
            $activities = [];
            $tags = [];
            $availableDates = [];
        }

        // Mapeamento categoria → tag que deve estar pré-selecionada
        $categoryTagMap = [
            'familia' => 'adequado-para-criancas',
            'aventura' => 'amigo-da-natureza',
            'noturno' => 'vida-noturna',
            'romantico' => 'romantico',
        ];
        $lockedTag = $categoryTagMap[$cat['slug']] ?? null;

        // Se tem tag travada, garantir que está nos filtros
        if ($lockedTag && !in_array($lockedTag, $currentFilters['tag'])) {
            $currentFilters['tag'][] = $lockedTag;
        }

        $this->view('frontend/trips/category', [
            'trips' => $trips,
            'category' => $cat,
            'categories' => $categories,
            'destinations' => $destinations,
            'activities' => $activities,
            'tags' => $tags,
            'availableDates' => $availableDates,
            'priceRange' => $priceRange,
            'durationRange' => $durationRange,
            'currentFilters' => $currentFilters,
            'currentOrder' => $orderBy,
            'lockedTag' => $lockedTag,
            'pageTitle' => $cat['name'] . ' - Passeios em Punta Cana',
        ], 'app');
    }

    public function show(Request $request, Response $response): void
    {
        $slug = $request->param('slug', '');
        $trip = $this->tripModel->findBySlug($slug);

        if (!$trip || $trip['status'] !== 'published') {
            $this->abort(404, 'Passeio não encontrado.');
        }

        // Incrementar views
        $this->tripModel->incrementViews((int) $trip['id']);

        // Dados completos
        $tripId = (int) $trip['id'];
        $packages = $this->packageModel->getByTrip($tripId);
        foreach ($packages as &$pkg) {
            $pkg['categories'] = $this->packageModel->getCategories((int) $pkg['id']);
            $pkg['base_price'] = $this->packageModel->getBasePrice((int) $pkg['id']);
        }

        // Se group pricing ativo, sobrescrever base_price com preço da 1ª faixa
        if (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing'])) {
            $gpRules = json_decode($trip['group_pricing'], true);
            if (is_array($gpRules) && !empty($gpRules)) {
                usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                $gpFirstPrice = (float) $gpRules[0]['price'];
                foreach ($packages as &$pkg) {
                    $pkg['base_price'] = $gpFirstPrice;
                }
            }
        }

        $categories = $this->tripModel->getCategories($tripId);
        $itinerary = $this->tripModel->getItinerary($tripId);
        $extraServices = $this->tripModel->getExtraServices($tripId);
        $fixedDates = $this->tripModel->getFixedDates($tripId);
        $reviews = $this->tripModel->getReviews($tripId);
        $rating = $this->tripModel->getAverageRating($tripId);
        $relatedTrips = $this->tripModel->getRelated($tripId);

        // FAQs do passeio (primeiro tenta do campo JSON, depois da tabela)
        $tripFaqs = [];
        if (!empty($trip['faqs'])) {
            $tripFaqs = json_decode($trip['faqs'], true) ?: [];
        }
        if (empty($tripFaqs)) {
            $tripFaqs = $this->db->fetchAll(
                "SELECT * FROM trip_faqs WHERE trip_id = ? ORDER BY sort_order ASC",
                [$tripId]
            );
        }

        // Verificar wishlist
        $inWishlist = false;
        $user = $this->currentUser();
        if ($user) {
            $wishlistModel = new Wishlist();
            $inWishlist = $wishlistModel->isInWishlist((int) $user['id'], $tripId);
        }

        // Gallery
        $gallery = $trip['gallery'] ? json_decode($trip['gallery'], true) : [];
        $includes = $trip['includes'] ? json_decode($trip['includes'], true) : [];
        $excludes = $trip['excludes'] ? json_decode($trip['excludes'], true) : [];

        $this->view('frontend/trips/show', [
            'trip' => $trip,
            'packages' => $packages,
            'categories' => $categories,
            'itinerary' => $itinerary,
            'extraServices' => $extraServices,
            'fixedDates' => $fixedDates,
            'reviews' => $reviews,
            'rating' => $rating,
            'relatedTrips' => $relatedTrips,
            'tripFaqs' => $tripFaqs,
            'inWishlist' => $inWishlist,
            'gallery' => $gallery,
            'includes' => $includes,
            'excludes' => $excludes,
            'pageTitle' => $trip['meta_title'] ?: $trip['title'],
            'metaDescription' => $trip['meta_description'] ?: $trip['short_description'],
        ], 'app');
    }
}
