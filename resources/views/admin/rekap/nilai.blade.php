@extends('layouts.app')
@section('title', 'Rekap Nilai')

@section('content')
<div class="page-header"><h4><i class="bi bi-bar-chart-fill me-2"></i> Rekap Nilai</h4></div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih --</option>
                    @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->tingkat }} {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="1" {{ $semester == '1' ? 'selected' : '' }}>Ganjil</option>
                    <option value="2" {{ $semester == '2' ? 'selected' : '' }}>Genap</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary"><i class="bi bi-search me-1"></i> Tampilkan</button>
                @if($kelasId && count($rekap) > 0)
                <a href="{{ route('admin.export.nilai.excel', request()->only(['kelas_id', 'semester'])) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
                <a href="{{ route('admin.export.nilai.pdf', request()->only(['kelas_id', 'semester'])) }}" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
                <a href="#" class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer me-1"></i> Cetak</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($kelasId && count($rekap) > 0)
<div class="card">
    <div class="card-header"><i class="bi bi-table me-2"></i> Kelas {{ $kelasNama }} — Semester {{ $semester == '1' ? 'Ganjil' : 'Genap' }} {{ $taAktif?->tahun }}</div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:70vh;overflow-y:auto;">
            <table class="table table-bordered table-hover mb-0" style="font-size:0.78rem;white-space:nowrap;">
                <thead style="position:sticky;top:0;z-index:2;">
                    <tr style="background:var(--primary-700);color:white;">
                        <th style="width:35px;">No</th><th style="width:60px;">NIS</th><th style="min-width:160px;">Nama</th>
                        @foreach($mapelList as $mp)<th style="text-align:center;width:55px;font-size:0.65rem;">{{ \Illuminate\Support\Str::limit($mp->nama_mapel, 8) }}</th>@endforeach
                        <th style="text-align:center;width:55px;background:var(--primary-600);">Rata²</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $r)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td><td>{{ $r['nis'] }}</td><td><strong>{{ $r['nama'] }}</strong></td>
                        @foreach($mapelList as $mp)
                        <td class="text-center fw-bold" style="color:{{ ($r['nilai'][$mp->id] ?? 0) >= 75 ? '#16a34a' : '#ef4444' }};">
                            {{ $r['nilai'][$mp->id] !== null ? $r['nilai'][$mp->id] : '-' }}
                        </td>
                        @endforeach
                        <td class="text-center fw-bold" style="background:var(--primary-50);color:var(--primary-700);">
                            {{ $r['rata'] !== null ? $r['rata'] : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($kelasId)
<div class="card"><div class="card-body text-center text-muted py-5">Tidak ada data nilai untuk filter ini.</div></div>
@else
<div class="card"><div class="card-body text-center text-muted py-5">Pilih kelas untuk menampilkan rekap nilai.</div></div>
@endif
@endsection
