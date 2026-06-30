<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Materi;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $kelasMapel = KelasMapel::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        return view('siswa.materi.index', compact('kelasMapel'));
    }

    public function list(KelasMapel $kelasMapel)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa || $siswa->kelas_id !== $kelasMapel->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        $materi = Materi::where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.materi.list', compact('kelasMapel', 'materi'));
    }

    public function download(KelasMapel $kelasMapel, Materi $materi)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa || $siswa->kelas_id !== $kelasMapel->kelas_id) {
            abort(403);
        }

        $path = storage_path('app/public/' . $materi->file_materi);
        if (!file_exists($path)) {
            return back()->with('error', 'File materi tidak ditemukan.');
        }

        return response()->download($path, $materi->judul . '_' . basename($materi->file_materi));
    }
}
