<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Services\StatistikService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected StatistikService $statistikService;

    public function __construct(StatistikService $statistikService)
    {
        $this->statistikService = $statistikService;
    }

    public function index()
    {
        //fitur fitur dalam dashboard
        $guruId = Auth::id();
        $statistik = $this->statistikService->dashboardGuru($guruId);

        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', $guruId)
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        $pengumuman = Pengumuman::with('creator')
            ->where(function ($q) {
                $q->where('target', 'semua')
                  ->orWhere('target', 'guru');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifikasi = Notifikasi::where('user_id', $guruId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $unreadNotifCount = Notifikasi::where('user_id', $guruId)
            ->where('is_read', false)
            ->count();

        return view('guru.dashboard', compact('statistik', 'kelasMapel', 'pengumuman', 'notifikasi', 'unreadNotifCount'));
    }
}
