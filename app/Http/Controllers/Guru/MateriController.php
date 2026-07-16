<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MateriController extends Controller
{
    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', Auth::id())
            ->aktif()
            ->get();

        return Inertia::render('Guru/Materi/Index', [
            'kelasMapel' => $kelasMapel->map(fn (KelasMapel $item) => [
                'id' => $item->id,
                'kelas' => $item->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $item->mataPelajaran?->nama_mapel ?? '-',
                'initials' => strtoupper(substr($item->mataPelajaran?->nama_mapel ?? 'MP', 0, 2)),
                'semester' => $item->semester,
                'href' => route('guru.materi.list', $item),
            ])->values(),
        ]);
    }
    //Daftar materi yang diupload oleh guru untuk kelas dan mata pelajaran tertentu
    public function list(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $materi = Materi::where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Guru/Materi/List', [
            'kelasMapel' => [
                'id' => $kelasMapel->id,
                'kelas' => $kelasMapel->kelas?->nama_kelas ?? '-',
                'mata_pelajaran' => $kelasMapel->mataPelajaran?->nama_mapel ?? '-',
                'store_url' => route('guru.materi.store', $kelasMapel),
                'back_url' => route('guru.materi.index'),
            ],
            'materi' => $materi->map(fn (Materi $item) => [
                'id' => $item->id,
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'deskripsi_ringkas' => Str::limit((string) $item->deskripsi, 60),
                'tanggal' => $item->created_at?->format('d M Y') ?? '-',
                'download_url' => $item->file_path ? route('guru.materi.download', [$kelasMapel, $item]) : null,
                'delete_url' => route('guru.materi.destroy', [$kelasMapel, $item]),
            ])->values(),
        ]);
    }
    //Menyimpan materi baru
    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'required|file|mimes:jpg,jpeg,pdf|extensions:jpg,jpeg,pdf|max:5120',
        ]);

        $file = $request->file('file_materi');
        $path = $file->store('materi/' . $kelasMapel->id, 'public');

        Materi::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'file_path' => $path,
        ]);

        return redirect()->route('guru.materi.list', $kelasMapel)
            ->with('success', 'Materi berhasil diupload.');
    }
    //Menghapus materi yang sudah diupload oleh guru untuk kelas dan mata pelajaran tertentu
    public function download(KelasMapel $kelasMapel, Materi $materi)
    {
        $this->authorize('mengajar', $kelasMapel);
        $this->ensureMateriBelongsToKelasMapel($materi, $kelasMapel);

        $disk = Storage::disk('public');
        if (!$materi->file_path || !$disk->exists($materi->file_path)) {
            return back()->with('error', 'File materi tidak ditemukan.');
        }

        return response()->download($disk->path($materi->file_path), $materi->judul . '_' . basename($materi->file_path));
    }

    //Menghapus materi yang sudah diupload oleh guru untuk kelas dan mata pelajaran tertentu
    public function destroy(KelasMapel $kelasMapel, Materi $materi)
    {
        $this->authorize('mengajar', $kelasMapel);
        $this->ensureMateriBelongsToKelasMapel($materi, $kelasMapel);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return redirect()->route('guru.materi.list', $kelasMapel)
            ->with('success', 'Materi berhasil dihapus.');
    }

    private function ensureMateriBelongsToKelasMapel(Materi $materi, KelasMapel $kelasMapel): void
    {
        abort_unless((int) $materi->kelas_mapel_id === (int) $kelasMapel->id, 403);
    }
}
