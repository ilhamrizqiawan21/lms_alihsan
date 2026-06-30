<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\NilaiAkhir;
use App\Models\Notifikasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    protected NilaiService $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', Auth::id())
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        return view('guru.nilai.index', compact('kelasMapel'));
    }
    //Input nilai
    public function input(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $tahunAjaran = TahunAjaran::getAktif();
        $semester = \App\Models\Pengaturan::getValue('semester_aktif', '1');

        $siswa = Siswa::with('user')
            ->where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->orderBy('nis')
            ->get();

        $nilaiList = NilaiAkhir::where('kelas_mapel_id', $kelasMapel->id)
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.nilai.input', compact('kelasMapel', 'siswa', 'nilaiList', 'tahunAjaran', 'semester'));
    }
    //Simpan nilai
    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $request->validate([
            'semester' => 'required|in:1,2',
            'nilai' => 'required|array',
            'nilai.*.sum1' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sum2' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sum3' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sum4' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.sat' => 'nullable|numeric|min:0|max:100',
        ]);

        $tahunAjaran = TahunAjaran::getAktif();

        $siswas = Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->get()
            ->keyBy('id');

        foreach ($request->nilai as $siswaId => $data) {
            $this->nilaiService->simpanNilai([
                'siswa_id' => $siswaId,
                'kelas_mapel_id' => $kelasMapel->id,
                'tahun_ajaran_id' => $tahunAjaran?->id,
                'semester' => $request->semester,
                'sum1' => $data['sum1'] ?? null,
                'sum2' => $data['sum2'] ?? null,
                'sum3' => $data['sum3'] ?? null,
                'sum4' => $data['sum4'] ?? null,
                'sts' => $data['sts'] ?? null,
                'sas' => $data['sas'] ?? null,
                'sat' => $data['sat'] ?? null,
            ]);

            // Kirim notifikasi ke siswa
            $siswa = $siswas->get((int) $siswaId);
            if ($siswa && $siswa->user_id) {
                Notifikasi::create([
                    'user_id' => $siswa->user_id,
                    'tipe' => 'nilai_baru',
                    'judul' => 'Nilai Diperbarui',
                    'pesan' => "Nilai {$kelasMapel->mataPelajaran?->nama_mapel} semester {$request->semester} telah diinput.",
                    'link' => route('siswa.nilai.index'),
                ]);
            }
        }

        return redirect()->route('guru.nilai.input', $kelasMapel)
            ->with('success', 'Nilai berhasil disimpan.');
    }
    //Bahan untuk merekap nilai siswa per kelas dan mata pelajaran yang diampu oleh guru
    public function rekap(Request $request)
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', Auth::id())
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        $tahunAjaran = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $query = NilaiAkhir::with(['siswa.user', 'siswa.kelas', 'kelasMapel.mataPelajaran'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->whereHas('kelasMapel', fn($q) => $q->where('guru_id', Auth::id()));

        if ($request->filled('kelas_mapel_id')) {
            $query->where('kelas_mapel_id', $request->kelas_mapel_id);
        }

        $nilai = $query->orderBy('rata_akhir', 'desc')->paginate(30);

        return view('guru.rekap-nilai', compact('nilai', 'kelasMapel', 'semester'));
    }
}
