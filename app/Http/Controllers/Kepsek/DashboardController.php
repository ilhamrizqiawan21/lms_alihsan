<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\LogLogin;
use App\Models\NilaiAkhir;
use App\Models\Pengumuman;
use App\Services\StatistikService;
use Illuminate\Support\Facades\DB;
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

        return view('kepsek.dashboard', compact(
            'statistik', 'absensiMingguan', 'rataNilaiPerMapel',
            'pengumuman', 'loginTerbaru'
        ));
    }
}
