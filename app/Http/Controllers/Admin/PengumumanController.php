<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Tampilkan daftar pengumuman.
     */
    public function index()
    {
        $pengumuman = Pengumuman::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $routePrefix = $this->routePrefix();

        return view('admin.pengumuman.index', compact('pengumuman', 'routePrefix'));
    }

    /**
     * Form tambah pengumuman.
     */
    public function create()
    {
        return view('admin.pengumuman.create');
    }

    /**
     * Simpan pengumuman.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'target' => 'required|in:semua,guru,siswa,kelas_mapel',
            'target_kelas' => 'nullable|string',
            'kelas_mapel_id' => 'nullable|exists:kelas_mapel,id',
        ]);

        $validated['created_by'] = Auth::id();

        Pengumuman::create($validated);

        return redirect()->route($this->routePrefix() . '.index')
            ->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /**
     * Form edit pengumuman.
     */
    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Update pengumuman.
     */
    public function update(Request $request, Pengumuman $pengumuman)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'target' => 'required|in:semua,guru,siswa,kelas_mapel',
            'target_kelas' => 'nullable|string',
            'kelas_mapel_id' => 'nullable|exists:kelas_mapel,id',
        ]);

        $pengumuman->update($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $role = Auth::user()->role?->nama_role;
        if ($role !== 'admin' && (int) $pengumuman->created_by !== (int) Auth::id()) {
            abort(403);
        }

        $pengumuman->delete();
        return redirect()->route($this->routePrefix() . '.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function routePrefix(): string
    {
        return match (Auth::user()->role?->nama_role) {
            'guru' => 'guru.pengumuman',
            'kepala_sekolah' => 'kepsek.pengumuman',
            default => 'admin.pengumuman',
        };
    }
}
