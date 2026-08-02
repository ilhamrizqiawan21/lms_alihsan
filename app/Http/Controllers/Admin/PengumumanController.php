<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            $guruKelasIds = KelasMapel::where('guru_id', Auth::id())->pluck('kelas_id')->unique()->values();

            $query->where(function ($query) use ($guruKelasIds) {
                $query->whereIn('target', ['semua', 'guru'])
                    ->orWhere('created_by', Auth::id())
                    ->orWhere(function ($query) use ($guruKelasIds) {
                        $query->where('target', 'kelas_mapel')
                            ->where(function ($query) use ($guruKelasIds) {
                                $query->whereIn('kelas_mapel_id', KelasMapel::where('guru_id', Auth::id())->select('id'));

                                foreach ($guruKelasIds as $kelasId) {
                                    $query->orWhere('target_kelas', 'like', '%"' . $kelasId . '"%');
                                }
                            });
                    });
            });
        }

        if (Auth::user()->role?->nama_role === 'kepala_sekolah') {
            $query->where(function ($query) {
                $query->whereIn('target', ['semua', 'guru'])
                    ->orWhere('created_by', Auth::id());
            });
        }

        $pengumuman = $query->paginate(15);
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->when(Auth::user()->isGuru(), fn ($query) => $query->where('guru_id', Auth::id()))
            ->orderBy('kelas_id')
            ->get();
        $targetKelasOptions = Auth::user()->isGuru()
            ? $kelasMapel->pluck('kelas')->filter()->unique('id')->sortBy(fn (Kelas $kelas) => $kelas->tingkat . ' ' . $kelas->nama_kelas)->values()
            : $kelas;
        $routePrefix = $this->routePrefix();

        return view('admin.pengumuman.index', compact('pengumuman', 'kelas', 'kelasMapel', 'targetKelasOptions', 'routePrefix'));
    }

    public function show(Pengumuman $pengumuman)
    {
        $role = Auth::user()->role?->nama_role;

        abort_unless($this->canView($pengumuman, $role), 403);

        $pengumuman->loadMissing(['creator', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran']);
        $targetKelasLabels = Kelas::whereIn('id', $pengumuman->targetKelasIds())
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get()
            ->map(fn (Kelas $kelas) => trim($kelas->tingkat . ' ' . $kelas->nama_kelas));
        $routePrefix = $this->routePrefix();

        return view('admin.pengumuman.show', compact('pengumuman', 'targetKelasLabels', 'routePrefix'));
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
            'target_kelas_ids' => 'nullable|required_if:target,kelas_mapel|array',
            'target_kelas_ids.*' => 'integer|exists:kelas,id',
        ]);

        if ($validated['target'] === 'kelas_mapel') {
            $targetKelasIds = collect($validated['target_kelas_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($targetKelasIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'target_kelas_ids' => 'Pilih minimal satu kelas tujuan.',
                ]);
            }

            if (Auth::user()->isGuru()) {
                $allowedKelasIds = KelasMapel::where('guru_id', Auth::id())
                    ->whereIn('kelas_id', $targetKelasIds)
                    ->pluck('kelas_id')
                    ->unique()
                    ->values();

                abort_unless($targetKelasIds->diff($allowedKelasIds)->isEmpty(), 403);
            }

            $kelasMapelId = KelasMapel::whereIn('kelas_id', $targetKelasIds)->value('id');

            $validated['target_kelas'] = $targetKelasIds->map(fn ($id) => (string) $id)->values()->toJson();
            $validated['kelas_mapel_id'] = $kelasMapelId;
        } else {
            $validated['kelas_mapel_id'] = null;
            $validated['target_kelas'] = null;
        }

        unset($validated['target_kelas_ids']);
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
        $role = Auth::user()->role?->nama_role;
        $allowedTargets = match ($role) {
            'guru' => ['kelas_mapel'],
            'admin', 'kepala_sekolah' => ['semua', 'guru', 'siswa', 'kelas_mapel'],
            default => [],
        };

        abort_unless($allowedTargets !== [], 403);
        abort_unless($role === 'admin' || (int) $pengumuman->created_by === (int) Auth::id(), 403);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'target' => ['required', Rule::in($allowedTargets)],
            'target_kelas_ids' => 'nullable|required_if:target,kelas_mapel|array',
            'target_kelas_ids.*' => 'integer|exists:kelas,id',
        ]);

        if ($validated['target'] === 'kelas_mapel') {
            $targetKelasIds = collect($validated['target_kelas_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($targetKelasIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'target_kelas_ids' => 'Pilih minimal satu kelas tujuan.',
                ]);
            }

            if (Auth::user()->isGuru()) {
                $allowedKelasIds = KelasMapel::where('guru_id', Auth::id())
                    ->whereIn('kelas_id', $targetKelasIds)
                    ->pluck('kelas_id')
                    ->unique()
                    ->values();

                abort_unless($targetKelasIds->diff($allowedKelasIds)->isEmpty(), 403);
            }

            $validated['target_kelas'] = $targetKelasIds->map(fn ($id) => (string) $id)->values()->toJson();
            $validated['kelas_mapel_id'] = KelasMapel::whereIn('kelas_id', $targetKelasIds)->value('id');
        } else {
            $validated['target_kelas'] = null;
            $validated['kelas_mapel_id'] = null;
        }

        unset($validated['target_kelas_ids']);

        $pengumuman->update($validated);

        return redirect()->route($this->routePrefix() . '.index')
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

            $targetKelasIds = $pengumuman->targetKelasIds();
            if ($pengumuman->target === 'kelas_mapel' && $targetKelasIds !== []) {
                return KelasMapel::whereIn('kelas_id', $targetKelasIds)
                    ->where('guru_id', Auth::id())
                    ->exists();
            }

            return $pengumuman->target === 'kelas_mapel'
                && KelasMapel::whereKey($pengumuman->kelas_mapel_id)
                    ->where('guru_id', Auth::id())
                    ->exists();
        }

        return false;
    }
}
