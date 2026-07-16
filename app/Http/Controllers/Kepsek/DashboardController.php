<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LogLogin;
use App\Models\NilaiAkhir;
use App\Models\Pengumuman;
use App\Services\StatistikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
//DashboardController untuk kepala sekolah, menampilkan statistik, absensi, nilai rata-rata, pengumuman, dan login terbaru
class DashboardController extends Controller
{
    protected StatistikService $statistikService;

    public function __construct(StatistikService $statistikService)
    {
        $this->statistikService = $statistikService;
    }

    /**
     * Dashboard kepala sekolah.
     */
    public function index()
    {
        $statistik = $this->statistikService->dashboardKepsek();

        // Grafik absensi 7 hari terakhir
        $absensiMingguan = Absensi::select(
            DB::raw('DATE(tanggal) as tanggal'),
            DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
            DB::raw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
            DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin"),
            DB::raw("SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha")
        )
            ->where('tanggal', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Rata-rata nilai per mata pelajaran
        $rataNilaiPerMapel = NilaiAkhir::select(
            'mata_pelajaran.nama_mapel',
            DB::raw('AVG(nilai_akhir.rata_akhir) as rata_rata')
        )
            ->join('kelas_mapel', 'nilai_akhir.kelas_mapel_id', '=', 'kelas_mapel.id')
            ->join('mata_pelajaran', 'kelas_mapel.mapel_id', '=', 'mata_pelajaran.id')
            ->groupBy('mata_pelajaran.nama_mapel')
            ->orderBy('rata_rata', 'desc')
            ->get();

        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $loginTerbaru = LogLogin::with('user')
            ->orderBy('login_time', 'desc')
            ->take(10)
            ->get();

        return Inertia::render('Kepsek/Dashboard', [
            'statistik' => [
                'total_siswa' => $statistik['total_siswa'] ?? 0,
                'total_guru' => $statistik['total_guru'] ?? 0,
                'total_kelas' => $statistik['total_kelas'] ?? 0,
                'total_mapel' => $statistik['total_mapel'] ?? 0,
            ],
            'absensiMingguan' => $absensiMingguan->map(fn ($item) => [
                'tanggal' => $item->tanggal,
                'hadir' => (int) $item->hadir,
                'sakit' => (int) $item->sakit,
                'izin' => (int) $item->izin,
                'alpha' => (int) $item->alpha,
            ]),
            'rataNilaiPerMapel' => $rataNilaiPerMapel->map(fn ($item) => [
                'nama_mapel' => $item->nama_mapel,
                'rata_rata' => round((float) $item->rata_rata, 2),
            ]),
            'pengumuman' => $pengumuman->map(fn (Pengumuman $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'created_at' => $item->created_at ? Carbon::parse($item->created_at)->format('d/m/Y') : '-',
            ]),
            'loginTerbaru' => $loginTerbaru->map(fn (LogLogin $item) => [
                'id' => $item->id,
                'nama_lengkap' => $item->nama_lengkap,
                'role' => $item->role,
                'login_time' => $item->login_time ? Carbon::parse($item->login_time)->diffForHumans() : '-',
                'ip_address' => $item->ip_address,
            ]),
        ]);
    }
}
