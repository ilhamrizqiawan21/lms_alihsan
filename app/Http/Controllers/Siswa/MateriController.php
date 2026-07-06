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
    //Daftar materi yang sudah di input guru
    public function list(KelasMapel $kelasMapel)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        $this->ensureKelasMapelAktifUntukSiswa($kelasMapel, $siswa);

        $materi = Materi::where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.materi.list', compact('kelasMapel', 'materi'));
    }
    //Unduh materi
    public function download(KelasMapel $kelasMapel, Materi $materi)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        $this->ensureKelasMapelAktifUntukSiswa($kelasMapel, $siswa);

        $this->ensureMateriBelongsToKelasMapel($materi, $kelasMapel);

        $path = storage_path('app/public/' . $materi->file_path);
        if (!file_exists($path)) {
            return back()->with('error', 'File materi tidak ditemukan.');
        }

        return response()->download($path, $materi->judul . '_' . basename($materi->file_path));
    }

    private function ensureMateriBelongsToKelasMapel(Materi $materi, KelasMapel $kelasMapel): void
    {
        abort_unless((int) $materi->kelas_mapel_id === (int) $kelasMapel->id, 403);
    }

    private function ensureKelasMapelAktifUntukSiswa(KelasMapel $kelasMapel, ?Siswa $siswa): void
    {
        $kelasMapelAktif = $kelasMapel->exists
            ? $kelasMapel->tahunAjaran()->where('is_active', true)->exists()
            : true;

        abort_unless(
            $siswa
            && (int) $siswa->kelas_id === (int) $kelasMapel->kelas_id
            && $kelasMapelAktif,
            403,
            'Anda tidak memiliki akses ke materi ini.'
        );
    }
}
