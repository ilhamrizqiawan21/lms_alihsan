<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelasMapel;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PengumumanController extends Controller
{
    /**
     * Tampilkan daftar pengumuman.
     */
    public function index()
    {
        $query = Pengumuman::with(['creator', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran'])
            ->orderBy('created_at', 'desc')
            ;

        if (Auth::user()->isGuru()) {
            $query->where(function ($query) {
                $query->whereIn('target', ['semua', 'guru'])
                    ->orWhere('created_by', Auth::id());
            });
        }

        $pengumuman = $query->paginate(15);
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->when(Auth::user()->isGuru(), fn ($query) => $query->where('guru_id', Auth::id()))
            ->orderBy('kelas_id')
            ->get();
        $routePrefix = $this->routePrefix();

        return view('admin.pengumuman.index', compact('pengumuman', 'kelasMapel', 'routePrefix'));
    }

    public function show(Pengumuman $pengumuman)
    {
        $role = Auth::user()->role?->nama_role;

        abort_unless($this->canView($pengumuman, $role), 403);

        $pengumuman->loadMissing(['creator', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran']);
        $routePrefix = $this->routePrefix();

        return view('admin.pengumuman.show', compact('pengumuman', 'routePrefix'));
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
        $role = Auth::user()->role?->nama_role;
        $allowedTargets = match ($role) {
            'guru' => ['kelas_mapel'],
            'admin', 'kepala_sekolah' => ['semua', 'guru', 'siswa', 'kelas_mapel'],
            default => [],
        };

        abort_unless($allowedTargets !== [], 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'target' => ['required', Rule::in($allowedTargets)],
            'target_kelas' => 'nullable|string',
            'kelas_mapel_id' => 'nullable|required_if:target,kelas_mapel|exists:kelas_mapel,id',
        ]);

        if ($validated['target'] === 'kelas_mapel') {
            $kelasMapelQuery = KelasMapel::whereKey($validated['kelas_mapel_id']);
            if (Auth::user()->isGuru()) {
                $kelasMapelQuery->where('guru_id', Auth::id());
            }

            abort_unless($kelasMapelQuery->exists(), 403);
        } else {
            $validated['kelas_mapel_id'] = null;
            $validated['target_kelas'] = null;
        }

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

    private function canView(Pengumuman $pengumuman, ?string $role): bool
    {
        if ($role === 'admin') {
            return true;
        }

        if ($role === 'kepala_sekolah') {
            return in_array($pengumuman->target, ['semua', 'guru'], true) || (int) $pengumuman->created_by === (int) Auth::id();
        }

        if ($role === 'guru') {
            if (in_array($pengumuman->target, ['semua', 'guru'], true) || (int) $pengumuman->created_by === (int) Auth::id()) {
                return true;
            }

            return $pengumuman->target === 'kelas_mapel'
                && KelasMapel::whereKey($pengumuman->kelas_mapel_id)
                    ->where('guru_id', Auth::id())
                    ->exists();
        }

        return false;
    }
}
