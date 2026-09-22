<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        $data = $this->dashboardService->getDashboardData($request->only([
            'periode',
            'program_studi',
        ]));
        $data['isAdmin'] = $request->user()?->hasRole('admin') ?? false;

        return view('admin.dashboard.index', $data);
    }
}
