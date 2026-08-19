<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\NilaiAkhir;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatistikController extends Controller
{
    /**
     * Halaman statistik lengkap untuk monitoring sekolah.
     */
    public function index()
    {
        $siswaPerKelas = Kelas::withCount(['siswa' => fn($q) => $q->where('status', 'aktif')])
            ->orderBy('tingkat')
            ->get();

        $totalGuru = User::whereHas('role', fn($query) => $query->where('nama_role', 'guru'))->count();
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalKelas = Kelas::count();

        $absensiBulanan = Absensi::select(
            DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"),
            DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
            DB::raw("COUNT(*) as total")
        )
            ->where('tanggal', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $distribusiNilai = [
            'sangat_baik' => NilaiAkhir::where('rata_akhir', '>=', 92)->count(),
            'baik' => NilaiAkhir::whereBetween('rata_akhir', [83, 91.99])->count(),
            'cukup' => NilaiAkhir::whereBetween('rata_akhir', [75, 82.99])->count(),
            'kurang' => NilaiAkhir::where('rata_akhir', '<', 75)->count(),
        ];

        $totalTugas = Tugas::whereHas('kelasMapel', fn($q) => $q->aktif())->count();
        $totalPengumpulan = PengumpulanTugas::whereHas('tugas.kelasMapel', fn($q) => $q->aktif())->count();
        $totalDinilai = PengumpulanTugas::whereHas('tugas.kelasMapel', fn($q) => $q->aktif())
            ->whereNotNull('nilai')
            ->count();
        $rataNilaiTugas = PengumpulanTugas::whereHas('tugas.kelasMapel', fn($q) => $q->aktif())
            ->whereNotNull('nilai')
            ->avg('nilai');

        return Inertia::render('Kepsek/Statistik/Index', [
            'siswaPerKelas' => $siswaPerKelas->map(fn (Kelas $kelas) => [
                'id' => $kelas->id,
                'label' => trim(($kelas->tingkat ?? '') . ' ' . ($kelas->nama_kelas ?? '')) ?: '-',
                'jumlah' => (int) $kelas->siswa_count,
            ]),
            'totalGuru' => $totalGuru,
            'totalSiswa' => $totalSiswa,
            'totalKelas' => $totalKelas,
            'absensiBulanan' => $absensiBulanan->map(fn ($item) => [
                'bulan' => $item->bulan,
                'hadir' => (int) $item->hadir,
                'total' => (int) $item->total,
                'persentase' => (int) $item->total > 0 ? round(((int) $item->hadir / (int) $item->total) * 100, 1) : 0,
            ]),
            'distribusiNilai' => [
                ['label' => 'Sangat Baik', 'value' => $distribusiNilai['sangat_baik'], 'color' => '#198754'],
                ['label' => 'Baik', 'value' => $distribusiNilai['baik'], 'color' => '#0d6efd'],
                ['label' => 'Cukup', 'value' => $distribusiNilai['cukup'], 'color' => '#ffc107'],
                ['label' => 'Kurang', 'value' => $distribusiNilai['kurang'], 'color' => '#dc3545'],
            ],
            'pembelajaran' => [
                'total_tugas' => $totalTugas,
                'total_pengumpulan' => $totalPengumpulan,
                'total_dinilai' => $totalDinilai,
                'persentase_dinilai' => $totalPengumpulan > 0 ? round(($totalDinilai / $totalPengumpulan) * 100, 1) : 0,
                'rata_nilai_tugas' => $rataNilaiTugas !== null ? round((float) $rataNilaiTugas, 2) : null,
            ],
        ]);
    }
}
