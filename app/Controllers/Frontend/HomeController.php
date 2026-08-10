<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripPackage;
use App\Models\TransferVehicle;
use App\Models\BlogPost;
use App\Services\AffiliateService;
use App\Services\InstagramService;

class HomeController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        // Rastrear afiliado se houver parâmetro ref
        $ref = $request->query('ref');
        if ($ref) {
            $affiliateService = new AffiliateService();
            $affiliateService->trackVisit(
                (int) $ref,
                $request->ip(),
                $request->header('referer'),
                $request->uri(),
                $request->userAgent()
            );
        }

        $tripModel = new Trip();
        $categoryModel = new TripCategory();
        $packageModel = new TripPackage();

        // Passeios em destaque
        $featuredTrips = $tripModel->getFeatured(6);
        foreach ($featuredTrips as &$trip) {
            $packages = $packageModel->getByTrip((int) $trip['id']);
            $trip['min_price'] = 0;
            if (!empty($packages)) {
                $trip['min_price'] = $packageModel->getBasePrice((int) $packages[0]['id']);
            }
            $trip['rating'] = $tripModel->getAverageRating((int) $trip['id']);
        }

        // Categorias com contagem
        $categories = $categoryModel->getWithTripCount();

        // Veículos de transfer em destaque (3 primeiros ativos)
        $vehicleModel = new TransferVehicle();
        $featuredVehicles = $vehicleModel->getActive();
        $featuredVehicles = array_slice($featuredVehicles, 0, 3);

        // Posts do blog (3 últimos publicados)
        $blogModel = new BlogPost();
        $latestPosts = $blogModel->getLatest(3);

        // Instagram Feed
        $instagramService = new InstagramService();
        $instagramPosts = $instagramService->getLatestPosts();
        $instagramUsername = $instagramService->getUsername();

        $this->view('frontend/home', [
            'featuredTrips' => $featuredTrips,
            'categories' => $categories,
            'featuredVehicles' => $featuredVehicles,
            'latestPosts' => $latestPosts,
            'instagramPosts' => $instagramPosts,
            'instagramUsername' => $instagramUsername,
            'pageTitle' => $this->setting('meta_title', 'Punta Cana para Brasileiros'),
            'metaDescription' => $this->setting('meta_description', ''),
        ], 'app');
    }
}
