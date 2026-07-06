<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\PengumpulanFile;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $this->ensureTugasBelongsToKelasMapel($tugas, $kelasMapel);

        $pengumpulan = PengumpulanTugas::with(['siswa.user', 'files'])
            ->where('tugas_id', $tugas->id)
            ->get();

        return view('guru.tugas.pengumpulan', compact('kelasMapel', 'tugas', 'pengumpulan'));
    }

    public function nilai(Request $request, KelasMapel $kelasMapel, Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        $this->authorize('mengajar', $kelasMapel);
        $this->ensureTugasBelongsToKelasMapel($tugas, $kelasMapel);
        $this->ensurePengumpulanBelongsToTugas($pengumpulan, $tugas);

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

    public function downloadFile(KelasMapel $kelasMapel, Tugas $tugas, PengumpulanFile $file)
    {
        $this->authorize('mengajar', $kelasMapel);
        $this->ensureTugasBelongsToKelasMapel($tugas, $kelasMapel);
        $file->loadMissing('pengumpulan');
        abort_unless($file->pengumpulan, 404);
        $this->ensurePengumpulanBelongsToTugas($file->pengumpulan, $tugas);

        return $this->downloadPengumpulanPath($file->file_path, $file->file_name);
    }

    public function downloadLegacyFile(KelasMapel $kelasMapel, Tugas $tugas, PengumpulanTugas $pengumpulan)
    {
        $this->authorize('mengajar', $kelasMapel);
        $this->ensureTugasBelongsToKelasMapel($tugas, $kelasMapel);
        $this->ensurePengumpulanBelongsToTugas($pengumpulan, $tugas);

        return $this->downloadPengumpulanPath($pengumpulan->file_upload, basename((string) $pengumpulan->file_upload));
    }

    public function destroy(Tugas $tugas)
    {
        $kelasMapel = $tugas->kelasMapel;
        $this->authorize('mengajar', $kelasMapel);

        // Hapus semua pengumpulan terkait beserta file
        $pengumpulanList = PengumpulanTugas::with('files')
            ->where('tugas_id', $tugas->id)
            ->get();
        foreach ($pengumpulanList as $p) {
            if ($p->file_upload) {
                $this->deletePengumpulanPath($p->file_upload);
            }
            // Hapus file di pengumpulan_files
            foreach ($p->files as $file) {
                $this->deletePengumpulanPath($file->file_path);
                $file->delete();
            }
            $p->delete();
        }

        $tugas->delete();

        return redirect()->route('guru.tugas.list', $kelasMapel)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    private function ensureTugasBelongsToKelasMapel(Tugas $tugas, KelasMapel $kelasMapel): void
    {
        abort_unless((int) $tugas->kelas_mapel_id === (int) $kelasMapel->id, 403);
    }

    private function ensurePengumpulanBelongsToTugas(PengumpulanTugas $pengumpulan, Tugas $tugas): void
    {
        abort_unless((int) $pengumpulan->tugas_id === (int) $tugas->id, 403);
    }

    private function downloadPengumpulanPath(?string $path, string $downloadName)
    {
        abort_unless($path, 404);

        $disk = Storage::disk('local');
        if (!$disk->exists($path)) {
            $disk = Storage::disk('public');
        }
        abort_unless($disk->exists($path), 404);

        return response()->download($disk->path($path), $downloadName);
    }

    private function deletePengumpulanPath(?string $path): void
    {
        if (!$path) {
            return;
        }

        Storage::disk('local')->delete($path);
        Storage::disk('public')->delete($path);
    }
}
