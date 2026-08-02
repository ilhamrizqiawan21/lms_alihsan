@extends('layouts.app')

@section('title', 'Pengumuman')
@section('page_title', 'Pengumuman')

@php
    $isGuru = auth()->user()->isGuru();
    $oldTargetKelasIds = collect(old('target_kelas_ids', []))->map(fn ($id) => (int) $id)->all();
    $kelasById = $kelas->keyBy('id');
@endphp

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Buat Pengumuman</div>
            <div class="card-body">
                <form action="{{ route($routePrefix . '.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul') }}" required>
                        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="isi" class="form-control @error('isi') is-invalid @enderror"
                                  rows="4" required>{{ old('isi') }}</textarea>
                        @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <select name="target" class="form-select @error('target') is-invalid @enderror">
                            @if(!$isGuru)
                                <option value="semua" @selected(old('target', 'semua') === 'semua')>Semua</option>
                                <option value="guru" @selected(old('target') === 'guru')>Guru</option>
                                <option value="siswa" @selected(old('target') === 'siswa')>Siswa</option>
                            @endif
                            <option value="kelas_mapel" @selected(old('target', $isGuru ? 'kelas_mapel' : null) === 'kelas_mapel')>Kelas Tertentu</option>
                        </select>
                        @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3" data-target-kelas-wrapper>
                        <label class="form-label">Target Kelas <small class="text-muted">(pilih satu atau beberapa kelas)</small></label>
                        <div class="target-kelas-checklist @error('target_kelas_ids') is-invalid @enderror @error('target_kelas_ids.*') is-invalid @enderror">
                            @forelse($targetKelasOptions as $kelasOption)
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="target_kelas_ids[]"
                                        value="{{ $kelasOption->id }}"
                                        id="target-kelas-{{ $kelasOption->id }}"
                                        @checked(in_array((int) $kelasOption->id, $oldTargetKelasIds, true))
                                    >
                                    <label class="form-check-label" for="target-kelas-{{ $kelasOption->id }}">
                                        {{ $kelasOption->tingkat }} {{ $kelasOption->nama_kelas }}
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted small">Belum ada kelas yang tersedia.</div>
                            @endforelse
                        </div>
                        @error('target_kelas_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @error('target_kelas_ids.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-send"></i> Kirim</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone-fill me-1"></i> Daftar Pengumuman</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Target</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank($pengumuman))
                                @foreach($pengumuman as $p)
                            <tr>
                                <td>{{ $p->judul }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $p->target }}</span>
                                    @if($p->target === 'kelas_mapel')
                                        @php
                                            $targetLabels = collect($p->targetKelasIds())
                                                ->map(fn ($id) => $kelasById->get($id))
                                                ->filter()
                                                ->map(fn ($kelasItem) => trim($kelasItem->tingkat . ' ' . $kelasItem->nama_kelas));
                                        @endphp
                                        <div class="small text-muted mt-1">
                                            {{ $targetLabels->isNotEmpty() ? $targetLabels->join(', ') : (($p->kelasMapel?->kelas?->nama_kelas ?? '-') . ' — ' . ($p->kelasMapel?->mataPelajaran?->nama_mapel ?? '-')) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <a href="{{ route($routePrefix . '.show', $p) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    @if(auth()->user()->isAdmin() || $p->created_by === auth()->id())
                                    <form action="{{ route($routePrefix . '.destroy', $p) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-confirm="Hapus pengumuman?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                                @endforeach
                            @else
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pengumuman</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .target-kelas-checklist {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.375rem;
        display: grid;
        gap: 0.35rem 1rem;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        max-height: 180px;
        overflow-y: auto;
        padding: 0.75rem;
    }

    .target-kelas-checklist.is-invalid {
        border-color: var(--bs-danger);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const targetSelect = document.querySelector('select[name="target"]');
        const targetKelasWrapper = document.querySelector('[data-target-kelas-wrapper]');

        if (!targetSelect || !targetKelasWrapper) {
            return;
        }

        const syncTargetKelasVisibility = () => {
            targetKelasWrapper.classList.toggle('d-none', targetSelect.value !== 'kelas_mapel');
        };

        targetSelect.addEventListener('change', syncTargetKelasVisibility);
        syncTargetKelasVisibility();
    });
</script>
@endpush
