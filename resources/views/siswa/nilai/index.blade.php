@extends('layouts.app')
@section('title', 'Nilai Saya')

@section('content')
<div class="page-header"><h4><i class="bi bi-bar-chart-fill me-2"></i> Nilai Saya</h4></div>

@if($nilaiList->isEmpty())
<div class="card"><div class="card-body text-center text-muted py-5">Belum ada data nilai.</div></div>
@else
@foreach($nilaiList as $periode => $nilai)
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-calendar3 me-2"></i> {{ $periode }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:var(--primary-100);">
                    <tr>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">SUM1</th><th class="text-center">SUM2</th><th class="text-center">SUM3</th><th class="text-center">SUM4</th>
                        <th class="text-center">Harian</th><th class="text-center">STS</th><th class="text-center">SAS</th><th class="text-center">SAT</th>
                        <th class="text-center" style="background:var(--primary-500);color:white;">Rata²</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($nilai as $n)
                    <tr>
                        <td><strong>{{ $n->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</strong></td>
                        <td class="text-center">{{ $n->sum1 ?? '-' }}</td>
                        <td class="text-center">{{ $n->sum2 ?? '-' }}</td>
                        <td class="text-center">{{ $n->sum3 ?? '-' }}</td>
                        <td class="text-center">{{ $n->sum4 ?? '-' }}</td>
                        <td class="text-center">{{ $n->nilai_harian ?? '-' }}</td>
                        <td class="text-center">{{ $n->sts ?? '-' }}</td>
                        <td class="text-center">{{ $n->sas ?? '-' }}</td>
                        <td class="text-center">{{ $n->sat ?? '-' }}</td>
                        <td class="text-center fw-bold" style="color:{{ ($n->rata_akhir ?? 0) >= 75 ? '#16a34a' : '#ef4444' }};">
                            {{ $n->rata_akhir ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
