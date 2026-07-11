@extends('layouts.app')
@section('title', 'Progress Saya')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-graph-up-arrow me-2"></i> Progress Belajar</h4>
    <p class="text-muted">{{ auth()->user()->nama_lengkap }} — {{ $siswa->kelas?->tingkat }} {{ $siswa->kelas?->nama_kelas }} — TA {{ $taAktif?->tahun }} Semester {{ $semester == '1' ? 'Ganjil' : 'Genap' }}</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-star-fill"></i></div>
        <div><div class="stat-label">Rata-rata Nilai (GPA)</div><div class="stat-number">{{ $gpa ? number_format($gpa, 2) : '-' }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
        <div>
            <div class="stat-label">Kehadiran {{ date('F') }}</div>
            <div class="stat-number">{{ $persenHadir }}%</div>
            <div class="progress mt-1"><div class="progress-bar" style="width:{{ $persenHadir }}%"></div></div>
            <small class="text-muted">H:{{ $hadir }} S:{{ $sakit }} I:{{ $izin }} A:{{ $alpha }}</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-clipboard-check-fill"></i></div>
        <div>
            <div class="stat-label">Penyelesaian Tugas</div>
            <div class="stat-number">{{ $persenTugas }}%</div>
            <div class="progress mt-1"><div class="progress-bar" style="width:{{ $persenTugas }}%"></div></div>
            <small class="text-muted">{{ $selesai }} dari {{ $totalTugas }} tugas</small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-2"></i> Nilai per Mata Pelajaran</div>
            <div class="card-body">
                @if($subjectScores->isNotEmpty())
                <canvas id="subjectChart" height="250"></canvas>
                @else
                <p class="text-muted text-center py-4">Belum ada data nilai.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i> Detail Nilai</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Mata Pelajaran</th><th class="text-center">Rata-rata</th><th class="text-center">Status</th></tr></thead>
                    <tbody>
                        @forelse($subjectScores as $sc)
                        <tr>
                            <td>{{ $sc['nama_mapel'] }}</td>
                            <td class="text-center fw-bold" style="color:{{ ($sc['rata'] ?? 0) >= 75 ? '#16a34a' : '#ef4444' }};">
                                {{ $sc['rata'] !== null ? number_format($sc['rata'], 2) : '-' }}
                            </td>
                            <td class="text-center">
                                @if($sc['rata'] === null) <span class="badge bg-secondary">-</span>
                                @elseif($sc['rata'] >= 92) <span class="badge bg-success">A</span>
                                @elseif($sc['rata'] >= 83) <span class="badge bg-info">B</span>
                                @elseif($sc['rata'] >= 75) <span class="badge bg-warning">C</span>
                                @else <span class="badge bg-danger">D</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">Belum ada nilai</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($subjectScores->isNotEmpty())
<script>
window.addEventListener('load', () => {
    window.renderChart('subjectChart', {
        type: 'bar',
        data: {
            labels: @json($subjectScores->pluck('nama_mapel')),
            datasets: [{
                label: 'Rata-rata',
                data: @json($subjectScores->pluck('rata')->map(fn($v) => $v ?? 0)),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderColor: '#16a34a',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endif
@endpush
