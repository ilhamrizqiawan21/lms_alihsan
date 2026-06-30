<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

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

        $kelas = Kelas::all();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $guru = User::where('role_id', 2)->orderBy('nama_lengkap')->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();

        return view('admin.kelas-mapel.index', compact('kelasMapel', 'kelas', 'mapel', 'guru', 'tahunAjaran'));
    }

    /**
     * Form tambah.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $mapel = MataPelajaran::orderBy('urutan')->get();
        $guru = User::where('role_id', 2)->orderBy('nama_lengkap')->get();
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
        ]);

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
        $kelasMapel->delete();
        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', 'Pengaturan kelas-mapel berhasil dihapus.');
    }
}
