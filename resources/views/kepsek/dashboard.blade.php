@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')
@section('page_title', 'Dashboard Kepala Sekolah')

@section('content')
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-green">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-number">{{ $totalSiswa ?? 0 }}</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-blue">
            <div class="icon"><i class="bi bi-person-workspace"></i></div>
            <div class="stat-number">{{ $totalGuru ?? 0 }}</div>
            <div class="stat-label">Total Guru</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-orange">
            <div class="icon"><i class="bi bi-building"></i></div>
            <div class="stat-number">{{ $totalKelas ?? 0 }}</div>
            <div class="stat-label">Total Kelas</div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card bg-purple">
            <div class="icon"><i class="bi bi-book-fill"></i></div>
            <div class="stat-number">{{ $totalMapel ?? 0 }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-clipboard-check-fill me-1"></i> Statistik Absensi (Bulan Ini)</div>
            <div class="card-body">
                <canvas id="absensiChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone-fill me-1"></i> Pengumuman Terbaru</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Judul</th><th>Tanggal</th></tr>
                        </thead>
                        <tbody>
                            @forelse($pengumuman ?? [] as $p)
                            <tr>
                                <td>{{ $p->judul }}</td>
                                <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('absensiChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        {{ $statHadir ?? 0 }},
                        {{ $statSakit ?? 0 }},
                        {{ $statIzin ?? 0 }},
                        {{ $statAlpa ?? 0 }}
                    ],
                    backgroundColor: ['#198754', '#ffc107', '#0d6efd', '#dc3545']
                }]
            }
        });
    }
</script>
@endpush
