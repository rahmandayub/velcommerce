<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(Request $request): Response
    {
        $range = $request->string('range', '30d')->toString();

        if (! in_array($range, ['7d', '30d', '90d', '12m'], true)) {
            $range = '30d';
        }

        return Inertia::render('admin/dashboard', [
            'range' => $range,
            'kpis' => $this->dashboardService->kpis(),
            'chartData' => $this->dashboardService->salesChart($range),
            'topProducts' => $this->dashboardService->topProducts(5),
            'lowStock' => $this->dashboardService->lowStock(
                (int) config('shop.low_stock_threshold', 5)
            ),
        ]);
    }
}
