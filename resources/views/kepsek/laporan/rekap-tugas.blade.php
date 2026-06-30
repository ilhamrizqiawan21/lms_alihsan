@extends('layouts.app')

@section('title', 'Rekap Tugas')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-journal-check me-2"></i> Rekap Tugas Per Kelas</h4>
</div>

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-funnel me-1"></i> Filter</div>
    <div class="card-body">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="kelas_id" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari judul tugas..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('kepsek.laporan.rekap-tugas') }}" class="btn btn-sm btn-outline-secondary w-100" title="Reset">↻</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    @forelse($tugas as $t)
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card border h-100 {{ $t->batas_waktu && $t->batas_waktu->isPast() && $t->belum_kumpul > 0 ? 'border-danger' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong style="font-size:0.9rem;">{{ \Illuminate\Support\Str::limit($t->judul, 35) }}</strong>
                    @if($t->kategori_nilai && $t->kategori_nilai !== 'NH')
                    <span class="badge bg-info ms-1" style="font-size:0.65rem;">{{ $t->kategori_nilai }}</span>
                    @endif
                </div>
                @if($t->batas_waktu)
                    @if($t->batas_waktu->isPast())
                    <span class="badge bg-danger" style="font-size:0.65rem;">Tutup</span>
                    @else
                    <span class="badge bg-success" style="font-size:0.65rem;">Aktif</span>
                    @endif
                @endif
            </div>
            <div class="card-body py-2">
                <div class="mb-1" style="font-size:0.78rem;">
                    <i class="bi bi-book text-primary me-1"></i>
                    {{ $t->kelasMapel->mataPelajaran->nama_mapel ?? '—' }}
                    <span class="text-muted mx-1">•</span>
                    {{ $t->kelasMapel->kelas->nama_kelas ?? '—' }}
                </div>
                <div class="mb-1" style="font-size:0.75rem; color:var(--gray-500);">
                    <i class="bi bi-person me-1"></i> {{ $t->kelasMapel->guru->nama_lengkap ?? '—' }}
                    @if($t->batas_waktu)
                    <span class="mx-1">•</span>
                    <i class="bi bi-clock me-1"></i> {{ $t->batas_waktu->format('d M Y H:i') }}
                    @endif
                </div>
                <hr class="my-1">
                <div class="d-flex justify-content-between text-center">
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;">{{ $t->total_siswa }}</div>
                        <small class="text-muted">Total</small>
                    </div>
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;color:var(--primary-600);">{{ $t->sudah_kumpul }}</div>
                        <small class="text-muted">Sudah</small>
                    </div>
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;color:#ef4444;">{{ $t->belum_kumpul }}</div>
                        <small class="text-muted">Belum</small>
                    </div>
                    <div>
                        <div style="font-size:1.1rem;font-weight:700;">{{ is_numeric($t->rata_nilai) ? number_format($t->rata_nilai, 1) : '—' }}</div>
                        <small class="text-muted">Rata²</small>
                    </div>
                </div>
                @if($t->total_siswa > 0)
                <div class="progress mt-2" style="height:6px;">
                    @php $pct = round(($t->sudah_kumpul / max($t->total_siswa, 1)) * 100); @endphp
                    <div class="progress-bar bg-{{ $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger') }}" style="width:{{ $pct }}%"></div>
                </div>
                <small class="text-muted" style="font-size:0.65rem;">{{ $pct }}% terkumpul</small>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><p class="text-muted text-center py-4">Tidak ada data tugas.</p></div>
    @endforelse
</div>

@if($tugas->hasPages())
<div class="d-flex justify-content-center">{{ $tugas->links() }}</div>
@endif
@endsection
