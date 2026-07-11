@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')
@section('page_title', 'Dashboard Kepala Sekolah')

@section('content')
<x-page-header
    title="Dashboard Kepala Sekolah"
    icon="bi-speedometer2"
    subtitle="Pantau ringkasan sekolah, absensi, dan pengumuman terbaru."
/>

<div class="stats-grid">
    <x-stat-card label="Total Siswa" :value="$statistik['total_siswa'] ?? 0" icon="bi-people-fill" />
    <x-stat-card label="Total Guru" :value="$statistik['total_guru'] ?? 0" icon="bi-person-workspace" />
    <x-stat-card label="Total Kelas" :value="$statistik['total_kelas'] ?? 0" icon="bi-building" />
    <x-stat-card label="Mata Pelajaran" :value="$statistik['total_mapel'] ?? 0" icon="bi-book-fill" />
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-clipboard-check-fill me-1"></i> Statistik Absensi (7 Hari Terakhir)</div>
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
                            <tr><td colspan="2" class="text-center text-muted py-3">Belum ada pengumuman</td></tr>
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
<script>
    window.addEventListener('load', () => {
        const absensiMingguan = @json($absensiMingguan);
        window.renderChart('absensiChart', {
            type: 'bar',
            data: {
                labels: absensiMingguan.map(item => item.tanggal),
                datasets: [
                    { label: 'Hadir', data: absensiMingguan.map(item => item.hadir), backgroundColor: '#198754' },
                    { label: 'Sakit', data: absensiMingguan.map(item => item.sakit), backgroundColor: '#ffc107' },
                    { label: 'Izin', data: absensiMingguan.map(item => item.izin), backgroundColor: '#0d6efd' },
                    { label: 'Alpa', data: absensiMingguan.map(item => item.alpha), backgroundColor: '#dc3545' }
                ]
            }
        });
    });
</script>
@endpush
