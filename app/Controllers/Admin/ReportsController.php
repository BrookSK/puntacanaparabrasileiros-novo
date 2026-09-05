<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Booking;

/**
 * Relatórios do painel admin.
 * Dashboard com país, cidade, volume de vendas, origem e campanhas de tráfego pago.
 */
class ReportsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
    }

    public function index(Request $request, Response $response): void
    {
        $bookingModel = new Booking();

        // Período — padrão: últimos 30 dias
        $from = $request->query('from', date('Y-m-d', strtotime('-30 days')));
        $to = $request->query('to', date('Y-m-d'));

        // Validar formato de data
        if (!\DateTime::createFromFormat('Y-m-d', $from)) $from = date('Y-m-d', strtotime('-30 days'));
        if (!\DateTime::createFromFormat('Y-m-d', $to)) $to = date('Y-m-d');

        $summary = $bookingModel->getReportSummary($from, $to);
        $byCountry = $bookingModel->getSalesByCountry($from, $to);
        $byCity = $bookingModel->getSalesByCity($from, $to);
        $timeline = $bookingModel->getSalesTimeline($from, $to);
        $byOrigin = $bookingModel->getSalesByOrigin($from, $to);
        $byCampaign = $bookingModel->getSalesByCampaign($from, $to);
        $topTrips = $bookingModel->getTopTrips($from, $to);
        $byGateway = $bookingModel->getSalesByGateway($from, $to);

        $this->view('admin/reports/index', [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'byCountry' => $byCountry,
            'byCity' => $byCity,
            'timeline' => $timeline,
            'byOrigin' => $byOrigin,
            'byCampaign' => $byCampaign,
            'topTrips' => $topTrips,
            'byGateway' => $byGateway,
            'pageTitle' => 'Relatórios',
        ], 'admin');
    }
}
