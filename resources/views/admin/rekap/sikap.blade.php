@extends('layouts.app')
@section('title', 'Rekap Sikap')

@section('content')
<div class="page-header"><h4><i class="bi bi-heart-fill me-2"></i> Rekap Sikap</h4></div>

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
            </div>
        </form>
    </div>
</div>

@if($kelasId && count($rekap) > 0)
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-heart-fill me-2"></i> Sikap Spiritual (KI-1) — Kelas {{ $kelasNama }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" style="font-size:0.78rem;">
                <thead style="background:var(--primary-700);color:white;">
                    <tr><th>No</th><th>NIS</th><th>Nama</th><th>Taqwa</th><th>Jujur</th><th>Disiplin</th><th>Sabar</th><th>Syukur</th><th>Tawadhu</th></tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $r)
                    <tr>
                        <td>{{ $i+1 }}</td><td>{{ $r['nis'] }}</td><td><strong>{{ $r['nama'] }}</strong></td>
                        @if($r['spiritual'])
                        <td class="text-center">{{ $r['spiritual']['taqwa'] }}</td><td class="text-center">{{ $r['spiritual']['kejujuran'] }}</td>
                        <td class="text-center">{{ $r['spiritual']['disiplin'] }}</td><td class="text-center">{{ $r['spiritual']['sabar'] }}</td>
                        <td class="text-center">{{ $r['spiritual']['syukur'] }}</td><td class="text-center">{{ $r['spiritual']['tawadhu'] }}</td>
                        @else
                        <td colspan="6" class="text-center text-muted">Belum dinilai</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-emoji-smile-fill me-2"></i> Sikap Sosial (KI-2) — Kelas {{ $kelasNama }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" style="font-size:0.78rem;">
                <thead style="background:var(--primary-700);color:white;">
                    <tr><th>No</th><th>NIS</th><th>Nama</th><th>Empati</th><th>Kerjasama</th><th>Toleransi</th><th>Percaya Diri</th><th>Komunikasi</th></tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $r)
                    <tr>
                        <td>{{ $i+1 }}</td><td>{{ $r['nis'] }}</td><td><strong>{{ $r['nama'] }}</strong></td>
                        @if($r['sosial'])
                        <td class="text-center">{{ $r['sosial']['empati'] }}</td><td class="text-center">{{ $r['sosial']['kerjasama'] }}</td>
                        <td class="text-center">{{ $r['sosial']['toleransi'] }}</td><td class="text-center">{{ $r['sosial']['percaya_diri'] }}</td>
                        <td class="text-center">{{ $r['sosial']['komunikasi'] }}</td>
                        @else
                        <td colspan="5" class="text-center text-muted">Belum dinilai</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($kelasId)
<div class="card"><div class="card-body text-center text-muted py-5">Tidak ada data sikap.</div></div>
@else
<div class="card"><div class="card-body text-center text-muted py-5">Pilih kelas untuk menampilkan rekap sikap.</div></div>
@endif
@endsection
