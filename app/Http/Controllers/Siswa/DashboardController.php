<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KelasMapel;
use App\Models\Materi;
use App\Models\Notifikasi;
use App\Models\PengumpulanTugas;
use App\Models\Pengumuman;
use App\Models\Siswa;
use App\Models\Tugas;
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
            ->aktif()
            ->get();

        $kelasMapelIds = $kelasMapel->pluck('id');

        $totalTugas = Tugas::whereIn('kelas_mapel_id', $kelasMapelIds)->count();
        $tugasSelesai = PengumpulanTugas::where('siswa_id', $siswa->id)
            ->where('status', 'sudah')
            ->whereHas('tugas', fn($q) => $q->whereIn('kelas_mapel_id', $kelasMapelIds))
            ->count();
        $tugasBelum = max($totalTugas - $tugasSelesai, 0);
        $totalMateri = Materi::whereIn('kelas_mapel_id', $kelasMapelIds)->count();

        $tugasTerbaru = Tugas::with([
            'kelasMapel.mataPelajaran',
            'pengumpulan' => fn($q) => $q->where('siswa_id', $siswa->id),
        ])
            ->whereIn('kelas_mapel_id', $kelasMapelIds)
            ->orderBy('batas_waktu', 'asc')
            ->take(5)
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
            ->whereHas('kelasMapel', fn($q) => $q->aktif())
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact(
            'statistik',
            'kelasMapel',
            'pengumuman',
            'notifikasi',
            'absensiTerbaru',
            'siswa',
            'totalTugas',
            'tugasSelesai',
            'tugasBelum',
            'totalMateri',
            'tugasTerbaru'
        ));
    }
}
