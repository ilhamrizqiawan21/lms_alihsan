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
                    @if(!blank(($statPerKelas ?? [])))
                        @foreach(($statPerKelas ?? []) as $stat)
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
                        @endforeach
                    @else
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.addEventListener('load', () => {
    @if(isset($chartLabels))
    window.renderChart('siswaChart', {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Jumlah Siswa',
                data: @json($chartData),
                backgroundColor: '#198754'
            }]
        }
    });
    @endif
    @if(isset($nilaiLabels))
    window.renderChart('nilaiChart', {
        type: 'bar',
        data: {
            labels: @json($nilaiLabels),
            datasets: [{
                label: 'Rata-rata Nilai',
                data: @json($nilaiData),
                backgroundColor: '#0d6efd'
            }]
        },
        options: {
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
    @endif
});
</script>
@endpush
