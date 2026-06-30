<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MataPelajaranController extends Controller
{
    /**
     * Tampilkan daftar mata pelajaran.
     */
    public function index()
    {
        $mapel = MataPelajaran::orderBy('urutan')->get();
        return view('admin.mata-pelajaran.index', compact('mapel'));
    }

    /**
     * Simpan mapel baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:mata_pelajaran,kode',
            'nama_mapel' => 'required|string|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Update mapel.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|unique:mata_pelajaran,kode,' . $mataPelajaran->id,
            'nama_mapel' => 'required|string|max:100',
            'urutan' => 'nullable|integer|min:0',
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Hapus mapel.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
