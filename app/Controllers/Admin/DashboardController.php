<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Booking;
use App\Models\TransferBooking;

class DashboardController extends Controller
{
    public function index(Request $request, Response $response): void
    {
        $bookingModel = new Booking();

        $stats = [
            'today_bookings' => $bookingModel->getTodayCount(),
            'pending_bookings' => $bookingModel->getPendingCount(),
            'month_revenue' => $bookingModel->getMonthRevenue(),
            'total_bookings' => $bookingModel->count(),
        ];

        $recentBookings = $bookingModel->getRecent(10);
        $chartData = $bookingModel->getStats30Days();

        $transferModel = new TransferBooking();
        $recentTransfers = $transferModel->getRecent(5);

        $this->view('admin/dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentTransfers' => $recentTransfers,
            'chartData' => $chartData,
            'pageTitle' => 'Dashboard',
        ], 'admin');
    }
}
