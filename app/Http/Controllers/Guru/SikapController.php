<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\SikapSosial;
use App\Models\SikapSpiritual;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SikapController extends Controller
{
    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', Auth::id())
            ->aktif()
            ->get();

        return view('guru.sikap.index', compact('kelasMapel'));
    }
    //Input nilai sikap  siswa
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

        $sikapSosial = SikapSosial::where('kelas_mapel_id', $kelasMapel->id)
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->get()
            ->keyBy('siswa_id');

        $sikapSpiritual = SikapSpiritual::where('kelas_mapel_id', $kelasMapel->id)
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.sikap.input', compact('kelasMapel', 'siswa', 'sikapSosial', 'sikapSpiritual', 'tahunAjaran', 'semester'));
    }
    //Simpan nilai sikap siswa
    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $request->validate([
            'semester' => 'required|in:1,2',
            'sosial' => 'required|array',
            'sosial.*.empati' => 'nullable|integer|min:1|max:5',
            'sosial.*.kerjasama' => 'nullable|integer|min:1|max:5',
            'sosial.*.toleransi' => 'nullable|integer|min:1|max:5',
            'sosial.*.percaya_diri' => 'nullable|integer|min:1|max:5',
            'sosial.*.komunikasi' => 'nullable|integer|min:1|max:5',
            'spiritual' => 'required|array',
            'spiritual.*.taqwa' => 'nullable|integer|min:1|max:5',
            'spiritual.*.kejujuran' => 'nullable|integer|min:1|max:5',
            'spiritual.*.disiplin' => 'nullable|integer|min:1|max:5',
            'spiritual.*.sabar' => 'nullable|integer|min:1|max:5',
            'spiritual.*.syukur' => 'nullable|integer|min:1|max:5',
            'spiritual.*.tawadhu' => 'nullable|integer|min:1|max:5',
        ]);

        $tahunAjaran = TahunAjaran::getAktif();
        if (!$tahunAjaran) {
            return back()
                ->withInput()
                ->with('error', 'Tahun ajaran aktif belum diatur.');
        }

        $validSiswaIds = Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->pluck('id')
            ->map(fn($id) => (string) $id);

        $submittedSiswaIds = collect(array_keys($request->input('sosial', [])))
            ->merge(array_keys($request->input('spiritual', [])))
            ->unique();

        if ($submittedSiswaIds->diff($validSiswaIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'sosial' => 'Data sikap berisi siswa yang tidak termasuk kelas ini.',
            ]);
        }

        foreach ($request->input('sosial', []) as $siswaId => $data) {
            SikapSosial::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'kelas_mapel_id' => $kelasMapel->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'semester' => $request->semester,
                ],
                $data
            );
        }

        foreach ($request->input('spiritual', []) as $siswaId => $data) {
            SikapSpiritual::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'kelas_mapel_id' => $kelasMapel->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'semester' => $request->semester,
                ],
                $data
            );
        }

        return redirect()->route('guru.sikap.input', $kelasMapel)
            ->with('success', 'Nilai sikap berhasil disimpan.');
    }
    //Bahan untuk merekap nilai sikap siswa per kelas dan mata pelajaran yang diampu oleh guru
    public function rekap(Request $request)
    {
        $request->validate([
            'semester' => 'nullable|in:1,2',
            'kelas_mapel_id' => 'nullable|integer',
        ]);

        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', Auth::id())
            ->aktif()
            ->get();

        $tahunAjaran = TahunAjaran::getAktif();
        $semester = $request->input('semester', \App\Models\Pengaturan::getValue('semester_aktif', '1'));

        $kmId = $request->input('kelas_mapel_id');

        // Sikap Sosial
        $soFields = ['empati','kerjasama','toleransi','percaya_diri','komunikasi'];
        $sosialQuery = SikapSosial::with(['siswa.user', 'siswa.kelas', 'kelasMapel.mataPelajaran'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->whereHas('kelasMapel', fn($q) => $q->where('guru_id', Auth::id())->aktif($semester));

        if ($kmId) $sosialQuery->where('kelas_mapel_id', $kmId);

        $sikapSosial = $sosialQuery->get()->groupBy('siswa_id')->map(function ($records) use ($soFields) {
            $first = $records->first();
            $avg = [];
            foreach ($soFields as $f) $avg[$f] = round($records->avg($f), 1);
            $avg['rata'] = round(array_sum($avg) / count($soFields), 1);
            return ['siswa' => $first->siswa] + $avg;
        })->values();

        // Sikap Spiritual
        $spFields = ['taqwa','kejujuran','disiplin','sabar','syukur','tawadhu'];
        $spiritualQuery = SikapSpiritual::with(['siswa.user', 'siswa.kelas', 'kelasMapel.mataPelajaran'])
            ->where('tahun_ajaran_id', $tahunAjaran?->id)
            ->where('semester', $semester)
            ->whereHas('kelasMapel', fn($q) => $q->where('guru_id', Auth::id())->aktif($semester));

        if ($kmId) $spiritualQuery->where('kelas_mapel_id', $kmId);

        $sikapSpiritual = $spiritualQuery->get()->groupBy('siswa_id')->map(function ($records) use ($spFields) {
            $first = $records->first();
            $avg = [];
            foreach ($spFields as $f) $avg[$f] = round($records->avg($f), 1);
            $avg['rata'] = round(array_sum($avg) / count($spFields), 1);
            return ['siswa' => $first->siswa] + $avg;
        })->values();

        return view('guru.rekap-sikap', compact('sikapSosial', 'sikapSpiritual', 'kelasMapel', 'semester'));
    }
}
