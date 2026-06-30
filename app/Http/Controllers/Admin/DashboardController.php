<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatistikService;
use App\Models\LogLogin;
use App\Models\Pengumuman;

class DashboardController extends Controller
{
    protected StatistikService $statistikService;

    public function __construct(StatistikService $statistikService)
    {
        $this->statistikService = $statistikService;
    }

    /**
     * Tampilkan dashboard admin.
     */
    public function index()
    {
        $statistik = $this->statistikService->dashboardAdmin();
        $loginTerbaru = LogLogin::with('user')
            ->orderBy('login_time', 'desc')
            ->take(10)
            ->get();
        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('statistik', 'loginTerbaru', 'pengumuman'));
    }
}
