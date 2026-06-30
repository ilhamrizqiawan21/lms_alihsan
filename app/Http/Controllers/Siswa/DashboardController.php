<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\Siswa;
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
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $statistik = $this->statistikService->dashboardSiswa($siswa->id);

        $kelasMapel = KelasMapel::with(['mataPelajaran', 'guru', 'tahunAjaran'])
            ->where('kelas_id', $siswa->kelas_id)
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        $pengumuman = Pengumuman::with('creator')
            ->where(function ($q) {
                $q->where('target', 'semua')
                  ->orWhere('target', 'siswa');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $absensiTerbaru = Absensi::with('kelasMapel.mataPelajaran')
            ->where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact('statistik', 'kelasMapel', 'pengumuman', 'notifikasi', 'absensiTerbaru', 'siswa'));
    }
}
