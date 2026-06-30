<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\PengumpulanTugas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $taAktif = TahunAjaran::getAktif();

        $tugas = Tugas::with(['kelasMapel.mataPelajaran', 'pengumpulan' => function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        }])
            ->whereHas('kelasMapel', function ($q) use ($siswa, $taAktif) {
                $q->where('kelas_id', $siswa->kelas_id);
                if ($taAktif) {
                    $q->where('tahun_ajaran_id', $taAktif->id);
                }
            })
            ->orderBy('batas_waktu', 'desc')
            ->get();

        return view('siswa.tugas.index', compact('tugas', 'siswa'));
    }

    public function show(Tugas $tugas)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa || $siswa->kelas_id !== $tugas->kelasMapel->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $pengumpulan = PengumpulanTugas::with('files')
            ->where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswa->id)
            ->first();

        return view('siswa.tugas.show', compact('tugas', 'pengumpulan', 'siswa'));
    }

    public function store(Request $request, Tugas $tugas)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa || $siswa->kelas_id !== $tugas->kelasMapel->kelas_id) {
            abort(403);
        }

        $validated = $request->validate([
            'file_upload' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg,zip,rar|max:20480',
            'teks_jawaban' => 'nullable|string|max:5000',
        ]);

        $filePath = null;
        if ($request->hasFile('file_upload')) {
            $filePath = $request->file('file_upload')
                ->store('tugas/' . $tugas->id . '/' . $siswa->id, 'public');
        }

        PengumpulanTugas::updateOrCreate(
            [
                'tugas_id' => $tugas->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'status' => 'sudah',
                'file_upload' => $filePath,
                'teks_jawaban' => $validated['teks_jawaban'],
                'tanggal_kumpul' => now(),
            ]
        );

        $guruId = $tugas->kelasMapel->guru_id;
        $notifikasiService = app(\App\Services\NotifikasiService::class);
        $notifikasiService->notifikasiUser(
            $guruId,
            'kumpul_tugas',
            'Siswa mengumpulkan tugas',
            "{$user->nama_lengkap} telah mengumpulkan tugas '{$tugas->judul}'.",
            route('guru.tugas.pengumpulan', [$tugas->kelas_mapel_id, $tugas->id])
        );

        return redirect()->route('siswa.tugas.show', $tugas)
            ->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
