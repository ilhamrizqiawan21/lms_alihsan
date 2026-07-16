<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KelasController extends Controller
{
    /**
     * Tampilkan daftar kelas.
     */
    public function index()
    {
        $kelas = Kelas::withCount(['siswa' => function ($q) {
            $q->where('status', 'aktif');
        }])->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return Inertia::render('Admin/Kelas/Index', [
            'kelas' => $kelas,
        ]);
    }

    /**
     * Simpan kelas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tingkat' => 'required|in:VII,VIII,IX',
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Update kelas.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'tingkat' => 'required|in:VII,VIII,IX',
            'nama_kelas' => 'required|string|max:20|unique:kelas,nama_kelas,' . $kelas->id,
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Hapus kelas.
     */
    public function destroy(Kelas $kelas)
    {
        if ($kelas->siswa()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki data siswa. Pindahkan atau arsipkan siswa terlebih dahulu.');
        }

        if ($kelas->kelasMapel()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki pengaturan pengajaran. Hapus pengaturan pengajarannya terlebih dahulu.');
        }

        $kelas->delete();
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Tampilkan daftar siswa dalam kelas.
     */
    public function siswa(Kelas $kelas)
    {
        $siswa = $kelas->siswa()->with('user')->where('status', 'aktif')->get();
        return view('admin.kelas.siswa', compact('kelas', 'siswa'));
    }
}
