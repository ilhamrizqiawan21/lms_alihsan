<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\NilaiAkhir;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    /**
     * Halaman statistik lengkap.
     */
    public function index()
    {
        // Statistik siswa per kelas
        $siswaPerKelas = Kelas::withCount(['siswa' => fn($q) => $q->where('status', 'aktif')])
            ->orderBy('tingkat')
            ->get();

        // Statistik guru
        $totalGuru = User::whereHas('role', fn($query) => $query->where('nama_role', 'guru'))->count();

        // Statistik absensi per bulan (6 bulan terakhir)
        $absensiBulanan = Absensi::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
            DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
            DB::raw("COUNT(*) as total")
        )
            ->where('tanggal', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Distribusi nilai
        $distribusiNilai = [
            'sangat_baik' => NilaiAkhir::where('rata_akhir', '>=', 92)->count(),
            'baik' => NilaiAkhir::whereBetween('rata_akhir', [83, 91.99])->count(),
            'cukup' => NilaiAkhir::whereBetween('rata_akhir', [75, 82.99])->count(),
            'kurang' => NilaiAkhir::where('rata_akhir', '<', 75)->count(),
        ];

        return view('kepsek.statistik.index', compact(
            'siswaPerKelas', 'totalGuru', 'absensiBulanan', 'distribusiNilai'
        ));
    }
}
