@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Rekap Absensi Per Kelas</h4>
</div>

<div class="row">
    @forelse($rekap as $r)
    <div class="col-md-6 col-lg-4 mb-3">
        <div class="card border h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $r['kelas']->nama_kelas }}</strong>
                <span class="badge bg-secondary">{{ $r['kelas']->siswa_count ?? 0 }} siswa</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-check-circle-fill text-success me-1"></i> Total Hadir</span>
                    <strong>{{ $r['total_hadir'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><i class="bi bi-bar-chart-fill text-primary me-1"></i> Total Absensi</span>
                    <strong>{{ $r['total_absensi'] }}</strong>
                </div>
                <div class="progress" style="height:20px;">
                    @php $pct = $r['persen']; $color = $pct >= 90 ? 'bg-success' : ($pct >= 75 ? 'bg-warning' : 'bg-danger'); @endphp
                    <div class="progress-bar {{ $color }}" style="width:{{ $pct }}%;">
                        {{ $pct }}%
                    </div>
                </div>
                <small class="text-muted mt-1 d-block">Persentase kehadiran</small>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><p class="text-muted text-center py-4">Tidak ada data rekap absensi.</p></div>
    @endforelse
</div>
@endsection
