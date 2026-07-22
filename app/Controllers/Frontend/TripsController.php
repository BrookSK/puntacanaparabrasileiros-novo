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
        $orderBy = $request->query('ordenar', 'sort_order ASC, created_at DESC');

        // Mapear ordenação
        $orderMap = [
            'preco_asc' => 'sort_order ASC',
            'preco_desc' => 'sort_order DESC',
            'popular' => 'bookings_count DESC',
            'recente' => 'created_at DESC',
            'nome' => 'title ASC',
        ];
        $order = $orderMap[$orderBy] ?? 'sort_order ASC, created_at DESC';

        if ($search) {
            $trips = $this->tripModel->search($search, $page, 12);
        } elseif ($category) {
            $cat = $this->categoryModel->findBySlug($category);
            if ($cat) {
                $trips = $this->tripModel->getByCategory((int) $cat['id'], $page, 12);
            } else {
                $trips = $this->tripModel->getPublished($page, 12, $order);
            }
        } else {
            $trips = $this->tripModel->getPublished($page, 12, $order);
        }

        // Adicionar preço mínimo a cada trip
        foreach ($trips['items'] as &$trip) {
            $packages = $this->packageModel->getByTrip((int) $trip['id']);
            $trip['min_price'] = 0;
            $trip['regular_price'] = 0;
            if (!empty($packages)) {
                $trip['min_price'] = $this->packageModel->getBasePrice((int) $packages[0]['id']);
                $trip['regular_price'] = $this->packageModel->getRegularPrice((int) $packages[0]['id']);
            }
            $trip['rating'] = $this->tripModel->getAverageRating((int) $trip['id']);
        }

        $categories = $this->categoryModel->getWithTripCount();

        $this->view('frontend/trips/index', [
            'trips' => $trips,
            'categories' => $categories,
            'currentCategory' => $category,
            'currentSearch' => $search,
            'currentOrder' => $orderBy,
            'pageTitle' => 'Passeios em Punta Cana',
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

        $categories = $this->tripModel->getCategories($tripId);
        $itinerary = $this->tripModel->getItinerary($tripId);
        $extraServices = $this->tripModel->getExtraServices($tripId);
        $fixedDates = $this->tripModel->getFixedDates($tripId);
        $reviews = $this->tripModel->getReviews($tripId);
        $rating = $this->tripModel->getAverageRating($tripId);
        $relatedTrips = $this->tripModel->getRelated($tripId);

        // FAQs do passeio
        $tripFaqs = $this->db->fetchAll(
            "SELECT * FROM trip_faqs WHERE trip_id = ? ORDER BY sort_order ASC",
            [$tripId]
        );

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
