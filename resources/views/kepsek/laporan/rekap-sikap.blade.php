@extends('layouts.app')

@section('title', 'Rekap Sikap')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-heart-fill me-2"></i> Rekap Sikap Spiritual & Sosial</h4>
</div>

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-funnel me-1"></i> Filter</div>
    <div class="card-body">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            @if($kelasId)
            <div class="col-md-2">
                <a href="{{ route('kepsek.laporan.rekap-sikap') }}" class="btn btn-sm btn-outline-secondary w-100">↻ Reset</a>
            </div>
            @endif
        </form>
    </div>
</div>

{{-- Sikap Sosial --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i> Sikap Sosial (KI-2)</span>
        <span class="badge bg-secondary">{{ $sikapSosial->count() }} siswa</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th class="d-none d-md-table-cell">Kelas</th>
                        <th>Empati</th>
                        <th>Kerja Sama</th>
                        <th>Toleransi</th>
                        <th>Percaya Diri</th>
                        <th>Komunikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!blank($sikapSosial))
                        @foreach($sikapSosial as $i => $s)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $s['siswa']->user->nama_lengkap ?? $s['siswa']->nis ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ $s['siswa']->kelas->nama_kelas ?? '—' }}</td>
                        @foreach(['empati','kerjasama','toleransi','percaya_diri','komunikasi'] as $aspek)
                        @php $v = $s[$aspek] ?? 0; @endphp
                        <td class="text-center">
                            <span class="badge bg-{{ $v >= 4 ? 'success' : ($v >= 3 ? 'warning text-dark' : 'danger') }}">
                                {{ $v }}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                        @endforeach
                    @else
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data sikap sosial.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Sikap Spiritual --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-star-fill me-2"></i> Sikap Spiritual (KI-1)</span>
        <span class="badge bg-secondary">{{ $sikapSpiritual->count() }} siswa</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th class="d-none d-md-table-cell">Kelas</th>
                        <th>Taqwa</th>
                        <th>Kejujuran</th>
                        <th>Disiplin</th>
                        <th>Sabar</th>
                        <th>Syukur</th>
                        <th>Tawadhu</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!blank($sikapSpiritual))
                        @foreach($sikapSpiritual as $i => $s)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $s['siswa']->user->nama_lengkap ?? $s['siswa']->nis ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ $s['siswa']->kelas->nama_kelas ?? '—' }}</td>
                        @foreach(['taqwa','kejujuran','disiplin','sabar','syukur','tawadhu'] as $aspek)
                        @php $v = $s[$aspek] ?? 0; @endphp
                        <td class="text-center">
                            <span class="badge bg-{{ $v >= 4 ? 'success' : ($v >= 3 ? 'warning text-dark' : 'danger') }}">
                                {{ $v }}
                            </span>
                        </td>
                        @endforeach
                    </tr>
                        @endforeach
                    @else
                    <tr><td colspan="9" class="text-center text-muted py-3">Belum ada data sikap spiritual.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Ringkasan Statistik --}}
@if($sikapSosial->count() > 0 || $sikapSpiritual->count() > 0)
<div class="card">
    <div class="card-header"><i class="bi bi-info-circle me-2"></i> Ringkasan</div>
    <div class="card-body">
        <div class="row">
            @if($sikapSosial->count() > 0)
            <div class="col-md-6">
                <strong>Sikap Sosial — Rata-rata Semua Siswa:</strong>
                <div class="d-flex gap-3 mt-2 flex-wrap">
                    @foreach(['empati','kerjasama','toleransi','percaya_diri','komunikasi'] as $a)
                    @php $avg = round($sikapSosial->avg($a), 1); @endphp
                    <div class="text-center" style="min-width:80px;">
                        <div style="font-size:1.2rem;font-weight:700;">{{ $avg }}</div>
                        <small class="text-muted">{{ ucfirst(str_replace('_',' ',$a)) }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if($sikapSpiritual->count() > 0)
            <div class="col-md-6">
                <strong>Sikap Spiritual — Rata-rata Semua Siswa:</strong>
                <div class="d-flex gap-3 mt-2 flex-wrap">
                    @foreach(['taqwa','kejujuran','disiplin','sabar','syukur','tawadhu'] as $a)
                    @php $avg = round($sikapSpiritual->avg($a), 1); @endphp
                    <div class="text-center" style="min-width:80px;">
                        <div style="font-size:1.2rem;font-weight:700;">{{ $avg }}</div>
                        <small class="text-muted">{{ ucfirst($a) }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection
