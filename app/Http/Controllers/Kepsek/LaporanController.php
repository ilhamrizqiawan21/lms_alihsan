<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\NilaiAkhir;
use App\Models\Pengaturan;
use App\Models\PengumpulanTugas;
use App\Models\SikapSosial;
use App\Models\SikapSpiritual;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// Laporan Nilai, Sikap, Tugas, dan Absensi untuk Kepala Sekolah
class LaporanController extends Controller
{
    protected AbsensiService $absensiService;

    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    public function absensi(Request $request)
    {
        $kelas = Kelas::all();
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'guru'])
            ->aktif()
            ->get();

        $query = Absensi::with(['siswa.user', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran'])
            ->whereHas('kelasMapel', fn($q) => $q->aktif());

        if ($request->filled('kelas_mapel_id')) {
            $query->where('kelas_mapel_id', $request->kelas_mapel_id);
        }
        if ($request->filled('tanggal_awal')) {
            $query->where('tanggal', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $absensi = $query->orderBy('tanggal', 'desc')->paginate(30);

        return view('kepsek.laporan.absensi', compact('absensi', 'kelasMapel', 'kelas'));
    }

    public function nilai(Request $request)
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $taAktif = TahunAjaran::getAktif();
        $semester = $request->input('semester', Pengaturan::getValue('semester_aktif', '1'));

        $query = NilaiAkhir::with(['siswa.user', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran', 'tahunAjaran'])
            ->where('tahun_ajaran_id', $taAktif?->id)
            ->where('semester', $semester);

        if ($request->filled('kelas_id')) {
            $query->whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('mapel_id')) {
            $query->whereHas('kelasMapel', fn($q) => $q->where('mapel_id', $request->mapel_id));
        }
        $nilai = $query->orderBy('rata_akhir', 'desc')->paginate(30);

        return view('kepsek.laporan.nilai', compact('nilai', 'kelas', 'mapel', 'semester', 'taAktif'));
    }

    public function rekapAbsensi()
    {
        $kelas = Kelas::withCount(['siswa' => fn($q) => $q->where('status', 'aktif')])->get();
        $rekap = [];

        foreach ($kelas as $k) {
            $total = Absensi::whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $k->id)->aktif())
                ->count();
            $hadir = Absensi::whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $k->id)->aktif())
                ->where('status', 'hadir')
                ->count();

            $rekap[] = [
                'kelas' => $k,
                'total_absensi' => $total,
                'total_hadir' => $hadir,
                'persen' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            ];
        }

        return view('kepsek.laporan.rekap-absensi', compact('rekap'));
    }

    public function rekapTugas(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $query = Tugas::with([
            'kelasMapel.kelas',
            'kelasMapel.mataPelajaran',
            'kelasMapel.guru',
            'pengumpulan.siswa.user',
        ])->whereHas('kelasMapel', fn($q) => $q->aktif());

        if ($request->filled('kelas_id')) {
            $query->whereHas('kelasMapel', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('judul', 'like', "%{$s}%");
        }

        $tugas = $query->orderBy('batas_waktu', 'desc')->paginate(20);

        // Hitung statistik per tugas
        foreach ($tugas as $t) {
            $t->total_siswa = $t->pengumpulan->count();
            $t->sudah_kumpul = $t->pengumpulan->where('status', 'sudah')->count();
            $t->belum_kumpul = $t->pengumpulan->where('status', 'belum')->count();
            $t->rata_nilai = $t->pengumpulan->whereNotNull('nilai')->avg('nilai');
        }

        return view('kepsek.laporan.rekap-tugas', compact('tugas', 'kelas'));
    }

    public function rekapSikap(Request $request)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $kelasId = $request->input('kelas_id');
        $taAktif = TahunAjaran::getAktif();
        $semester = Pengaturan::getValue('semester_aktif', '1');

        // Sikap Sosial
        $sosialQuery = SikapSosial::with(['siswa.user', 'siswa.kelas', 'kelasMapel.mataPelajaran'])
            ->where('tahun_ajaran_id', $taAktif?->id)
            ->where('semester', $semester);

        if ($kelasId) {
            $sosialQuery->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId));
        }

        $sikapSosial = $sosialQuery->get()->groupBy('siswa_id')->map(function ($records) {
            $first = $records->first();
            return [
                'siswa' => $first->siswa,
                'mapel_count' => $records->count(),
                'empati' => round($records->avg('empati'), 1),
                'kerjasama' => round($records->avg('kerjasama'), 1),
                'toleransi' => round($records->avg('toleransi'), 1),
                'percaya_diri' => round($records->avg('percaya_diri'), 1),
                'komunikasi' => round($records->avg('komunikasi'), 1),
            ];
        })->values();

        // Sikap Spiritual
        $spiritualQuery = SikapSpiritual::with(['siswa.user', 'siswa.kelas', 'kelasMapel.mataPelajaran'])
            ->where('tahun_ajaran_id', $taAktif?->id)
            ->where('semester', $semester);

        if ($kelasId) {
            $spiritualQuery->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelasId));
        }

        $sikapSpiritual = $spiritualQuery->get()->groupBy('siswa_id')->map(function ($records) {
            $first = $records->first();
            return [
                'siswa' => $first->siswa,
                'mapel_count' => $records->count(),
                'taqwa' => round($records->avg('taqwa'), 1),
                'kejujuran' => round($records->avg('kejujuran'), 1),
                'disiplin' => round($records->avg('disiplin'), 1),
                'sabar' => round($records->avg('sabar'), 1),
                'syukur' => round($records->avg('syukur'), 1),
                'tawadhu' => round($records->avg('tawadhu'), 1),
            ];
        })->values();

        return view('kepsek.laporan.rekap-sikap', compact('sikapSosial', 'sikapSpiritual', 'kelas', 'kelasId', 'semester', 'taAktif'));
    }
}
