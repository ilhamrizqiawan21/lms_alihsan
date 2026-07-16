<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Services\StatistikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
            ->aktif()
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

        return Inertia::render('Guru/Dashboard', [
            'statistik' => $statistik,
            'kelasMapel' => $kelasMapel->map(fn (KelasMapel $item) => [
                'id' => $item->id,
                'kelas' => $item->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $item->mataPelajaran?->nama_mapel ?? '-',
                'semester' => $item->semester === '1' ? 'Ganjil' : 'Genap',
            ])->values(),
            'pengumuman' => $pengumuman->map(fn (Pengumuman $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->format('d M Y') : null,
            ])->values(),
            'notifikasi' => $notifikasi->map(fn (Notifikasi $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'pesan' => $item->pesan,
                'is_read' => (bool) $item->is_read,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->diffForHumans() : null,
            ])->values(),
            'unreadNotifCount' => $unreadNotifCount,
        ]);
    }
}
