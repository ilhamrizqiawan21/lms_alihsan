<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    protected NotifikasiService $notifikasiService;

    public function __construct(NotifikasiService $notifikasiService)
    {
        $this->notifikasiService = $notifikasiService;
    }

    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', Auth::id())
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        return view('guru.tugas.index', compact('kelasMapel'));
    }

    public function list(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $tugas = Tugas::where('kelas_mapel_id', $kelasMapel->id)
            ->withCount(['pengumpulan as sudah_mengumpulkan' => function ($q) {
                $q->where('status', 'sudah');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSiswa = \App\Models\Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->count();

        return view('guru.tugas.list', compact('kelasMapel', 'tugas', 'totalSiswa'));
    }

    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'batas_waktu' => 'required|date|after:now',
        ]);

        $tugas = Tugas::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'batas_waktu' => $validated['batas_waktu'],
        ]);

        $this->notifikasiService->notifikasiKelasMapel(
            $kelasMapel->id,
            'tugas_baru',
            'Tugas Baru',
            "Tugas '{$tugas->judul}' telah diberikan.",
            route('siswa.tugas.show', $tugas->id)
        );

        return redirect()->route('guru.tugas.list', $kelasMapel)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function pengumpulan(KelasMapel $kelasMapel, Tugas $tugas)
    {
        $this->authorize('mengajar', $kelasMapel);

        $pengumpulan = PengumpulanTugas::with(['siswa.user', 'files'])
            ->where('tugas_id', $tugas->id)
            ->get();

        return view('guru.tugas.pengumpulan', compact('kelasMapel', 'tugas', 'pengumpulan'));
    }

    public function nilai(Request $request, KelasMapel $kelasMapel, Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pengumpulan->update([
            'nilai' => $validated['nilai'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return back()->with('success', 'Nilai berhasil diberikan.');
    }
}
