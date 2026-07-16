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
use Illuminate\Support\Str;
use Inertia\Inertia;

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
            ->aktif()
            ->get();

        return Inertia::render('Guru/Tugas/Index', [
            'kelasMapel' => $kelasMapel->map(fn (KelasMapel $item) => [
                'id' => $item->id,
                'kelas' => $item->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $item->mataPelajaran?->nama_mapel ?? '-',
                'initials' => strtoupper(substr($item->mataPelajaran?->nama_mapel ?? 'MP', 0, 2)),
                'semester' => $item->semester,
                'href' => route('guru.tugas.list', $item),
            ])->values(),
        ]);
    }

    public function list(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $tugas = Tugas::where('kelas_mapel_id', $kelasMapel->id)
            ->withCount(['pengumpulan as sudah_mengumpulkan' => function ($q) use ($kelasMapel) {
                $q->whereIn('status', ['sudah', 'terlambat', 'dinilai'])
                    ->whereHas('siswa', fn ($siswa) => $siswa
                        ->where('kelas_id', $kelasMapel->kelas_id)
                        ->where('status', 'aktif'));
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSiswa = \App\Models\Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->count();

        return Inertia::render('Guru/Tugas/List', [
            'kelasMapel' => [
                'id' => $kelasMapel->id,
                'kelas' => $kelasMapel->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $kelasMapel->mataPelajaran?->nama_mapel ?? '-',
                'store_url' => route('guru.tugas.store', $kelasMapel),
            ],
            'tugas' => $tugas->map(fn (Tugas $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'deskripsi' => Str::limit((string) $item->deskripsi, 80),
                'batas_waktu' => $item->batas_waktu?->format('d M Y H:i'),
                'sudah_mengumpulkan' => $item->sudah_mengumpulkan ?? 0,
                'pengumpulan_url' => route('guru.tugas.pengumpulan', [$kelasMapel, $item]),
                'delete_url' => route('guru.tugas.destroy', $item),
            ])->values(),
            'totalSiswa' => $totalSiswa,
        ]);
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

        return Inertia::render('Guru/Tugas/Pengumpulan', [
            'kelasMapel' => [
                'id' => $kelasMapel->id,
                'kelas' => $kelasMapel->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $kelasMapel->mataPelajaran?->nama_mapel ?? '-',
                'back_url' => route('guru.tugas.list', $kelasMapel),
            ],
            'tugas' => [
                'id' => $tugas->id,
                'judul' => $tugas->judul,
                'batas_waktu' => $tugas->batas_waktu?->format('d/m/Y H:i'),
            ],
            'pengumpulan' => $pengumpulan->map(function (PengumpulanTugas $item) use ($kelasMapel, $tugas) {
                return [
                    'id' => $item->id,
                    'siswa' => $item->siswa?->user?->nama_lengkap ?? '-',
                    'status' => $item->status,
                    'tanggal_kumpul' => $item->tanggal_kumpul?->format('d/m/Y H:i'),
                    'teks_jawaban' => $item->teks_jawaban,
                    'catatan' => $item->catatan,
                    'nilai' => $item->nilai,
                    'nilai_url' => route('guru.tugas.nilai', [$kelasMapel, $tugas, $item]),
                    'legacy_file_url' => $item->file_upload ? route('guru.tugas.pengumpulan.download', [$kelasMapel, $tugas, $item]) : null,
                    'files' => $item->files->map(fn (PengumpulanFile $file) => [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        'url' => route('guru.tugas.file.download', [$kelasMapel, $tugas, $file]),
                    ])->values(),
                ];
            })->values(),
        ]);
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
            'status' => 'dinilai',
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

        if ($tugas->pengumpulan()->exists()) {
            return back()->with('error', 'Tugas tidak dapat dihapus karena sudah memiliki pengumpulan siswa.');
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
