@extends('layouts.app')

@section('title', 'Statistik')
@section('page_title', 'Statistik')

@section('content')
<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-people-fill me-1"></i> Jumlah Siswa Per Kelas</div>
            <div class="card-body">
                <canvas id="siswaChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart-fill me-1"></i> Rata-rata Nilai Per Mapel</div>
            <div class="card-body">
                <canvas id="nilaiChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-clipboard-check-fill me-1"></i> Statistik Absensi</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Kelas</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statPerKelas ?? [] as $stat)
                    <tr>
                        <td><strong>{{ $stat['kelas'] }}</strong></td>
                        <td>{{ $stat['hadir'] }}</td>
                        <td>{{ $stat['sakit'] }}</td>
                        <td>{{ $stat['izin'] }}</td>
                        <td>{{ $stat['alpa'] }}</td>
                        <td>
                            @php $total = $stat['hadir'] + $stat['sakit'] + $stat['izin'] + $stat['alpa']; @endphp
                            @if($total > 0)
                                <span class="badge bg-{{ ($stat['hadir']/$total) >= 0.8 ? 'success' : 'warning' }}">
                                    {{ round(($stat['hadir']/$total)*100, 1) }}%
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(isset($chartLabels))
    new Chart(document.getElementById('siswaChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Jumlah Siswa',
                data: {!! json_encode($chartData) !!},
                backgroundColor: '#198754'
            }]
        }
    });
    @endif
    @if(isset($nilaiLabels))
    new Chart(document.getElementById('nilaiChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($nilaiLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($nilaiData) !!},
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
    @endif
</script>
@endpush
