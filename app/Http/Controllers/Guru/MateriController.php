<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
            ->where('guru_id', Auth::id())
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        return view('guru.materi.index', compact('kelasMapel'));
    }
    //Daftar materi yang diupload oleh guru untuk kelas dan mata pelajaran tertentu
    public function list(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $materi = Materi::where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.materi.list', compact('kelasMapel', 'materi'));
    }
    //Menyimpan materi baru
    public function store(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'file_materi' => 'required|file|mimes:png,jpg,jpeg,pdf|max:20480',
        ]);

        $file = $request->file('file_materi');
        $path = $file->store('materi/' . $kelasMapel->id, 'public');

        Materi::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'file_materi' => $path,
        ]);

        return redirect()->route('guru.materi.list', $kelasMapel)
            ->with('success', 'Materi berhasil diupload.');
    }
    //Menghapus materi yang sudah diupload oleh guru untuk kelas dan mata pelajaran tertentu
    public function destroy(KelasMapel $kelasMapel, Materi $materi)
    {
        $this->authorize('mengajar', $kelasMapel);

        if ($materi->file_materi) {
            Storage::disk('public')->delete($materi->file_materi);
        }

        $materi->delete();

        return redirect()->route('guru.materi.list', $kelasMapel)
            ->with('success', 'Materi berhasil dihapus.');
    }
}
