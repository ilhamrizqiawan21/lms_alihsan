<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KelasMapelController extends Controller
{
    /**
     * Tampilkan daftar kelas_mapel (pengaturan mengajar).
     */
    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran', 'guru', 'tahunAjaran'])
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('kelas_id')
            ->orderBy('semester')
            ->paginate(20);

        $waliKelas = WaliKelas::with(['kelas', 'guru', 'tahunAjaran'])
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('kelas_id')
            ->paginate(20, ['*'], 'wali_page');

        $kelas = Kelas::all();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $guru = User::whereHas('role', fn($q) => $q->where('nama_role', 'guru'))
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.kelas-mapel.index', compact('kelasMapel', 'waliKelas', 'kelas', 'mapel', 'guru', 'tahunAjaran'));
    }

    /**
     * Form tambah.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $guru = User::whereHas('role', fn($q) => $q->where('nama_role', 'guru'))
            ->where('is_active', true)
            ->orderBy('nama_lengkap')
            ->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.kelas-mapel.create', compact('kelas', 'mapel', 'guru', 'tahunAjaran'));
    }

    /**
     * Simpan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'semester' => 'required|in:1,2',
            'pertemuan_per_minggu' => 'required|integer|min:1|max:6',
        ]);

        $isGuru = User::whereKey($validated['guru_id'])
            ->whereHas('role', fn($q) => $q->where('nama_role', 'guru'))
            ->where('is_active', true)
            ->exists();

        if (!$isGuru) {
            throw ValidationException::withMessages([
                'guru_id' => 'Guru yang dipilih tidak aktif atau tidak valid.',
            ]);
        }

        // Cek unique constraint
        $exists = KelasMapel::where([
            'kelas_id' => $validated['kelas_id'],
            'mapel_id' => $validated['mapel_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'semester' => $validated['semester'],
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Kombinasi kelas, mapel, tahun ajaran, dan semester sudah ada.')
                ->withInput();
        }

        KelasMapel::create($validated);

        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', 'Pengaturan kelas-mapel berhasil ditambahkan.');
    }

    /**
     * Hapus.
     */
    public function destroy(KelasMapel $kelasMapel)
    {
        $hasData = $kelasMapel->materi()->exists()
            || $kelasMapel->tugas()->exists()
            || $kelasMapel->absensi()->exists()
            || $kelasMapel->nilaiAkhir()->exists()
            || $kelasMapel->sikapSosial()->exists()
            || $kelasMapel->sikapSpiritual()->exists()
            || $kelasMapel->chatMessages()->exists();

        if ($hasData) {
            return back()->with('error', 'Pengajaran tidak dapat dihapus karena sudah memiliki data materi, tugas, absensi, nilai, sikap, atau chat.');
        }

        $kelasMapel->delete();
        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', 'Pengaturan kelas-mapel berhasil dihapus.');
    }

    public function storeWaliKelas(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $isGuru = User::whereKey($validated['guru_id'])
            ->whereHas('role', fn($q) => $q->where('nama_role', 'guru'))
            ->where('is_active', true)
            ->exists();

        if (!$isGuru) {
            throw ValidationException::withMessages([
                'guru_id' => 'Guru wali kelas yang dipilih tidak aktif atau tidak valid.',
            ]);
        }

        $exists = WaliKelas::where([
            'kelas_id' => $validated['kelas_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Kelas ini sudah memiliki wali kelas pada tahun ajaran tersebut.')
                ->withInput();
        }

        WaliKelas::create($validated);

        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', 'Penugasan wali kelas berhasil ditambahkan.');
    }

    public function destroyWaliKelas(WaliKelas $waliKelas)
    {
        $hasData = $waliKelas->absensi()->exists()
            || $waliKelas->pertemuan()->exists()
            || $waliKelas->penangananSiswa()->exists();

        if ($hasData) {
            return back()->with('error', 'Penugasan wali kelas tidak dapat dihapus karena sudah memiliki data absensi, pertemuan, atau penanganan siswa.');
        }

        $waliKelas->delete();

        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', 'Penugasan wali kelas berhasil dihapus.');
    }
}
