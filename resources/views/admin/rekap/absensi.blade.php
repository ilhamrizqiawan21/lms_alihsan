@extends('layouts.app')
@section('title', 'Rekap Absensi')

@php
$bulanIndo = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$statusIcon = ['hadir'=>'<span class="badge badge-hadir">H</span>','sakit'=>'<span class="badge badge-sakit">S</span>','izin'=>'<span class="badge badge-izin">I</span>','alpha'=>'<span class="badge badge-alpha">A</span>'];
@endphp

@section('content')
<div class="page-header">
    <h4><i class="bi bi-clipboard-check-fill me-2"></i> Rekap Absensi</h4>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>{{ $k->tingkat }} {{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Tampilkan</button>
                @if($kelasId && count($rekap) > 0)
                <a href="{{ route('admin.export.absensi.excel', request()->only(['kelas_id', 'bulan'])) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
                <a href="{{ route('admin.export.absensi.pdf', request()->only(['kelas_id', 'bulan'])) }}" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
                <a href="#" class="btn btn-outline-secondary" onclick="window.print();return false;"><i class="bi bi-printer me-1"></i> Cetak</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($kelasId && count($rekap) > 0)
<div class="card">
    <div class="card-header">
        <i class="bi bi-table me-2"></i> Kelas {{ $kelasNama }} — {{ $bulanIndo[(int)substr($bulan,5,2)] }} {{ substr($bulan,0,4) }}
        <span class="ms-3">
            <span class="badge badge-hadir">H = Hadir</span>
            <span class="badge badge-sakit ms-1">S = Sakit</span>
            <span class="badge badge-izin ms-1">I = Izin</span>
            <span class="badge badge-alpha ms-1">A = Alpha</span>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:70vh;overflow-y:auto;">
            <table class="table table-bordered table-hover mb-0" style="font-size:0.78rem;white-space:nowrap;">
                <thead style="position:sticky;top:0;z-index:2;">
                    <tr style="background:var(--primary-700);color:white;">
                        <th style="text-align:center;width:35px;">No</th>
                        <th style="width:60px;">NIS</th>
                        <th style="min-width:160px;">Nama</th>
                        @foreach($tanggalList as $tgl)
                        <th style="text-align:center;width:38px;font-size:0.65rem;">{{ date('d', strtotime($tgl)) }}</th>
                        @endforeach
                        <th style="text-align:center;width:32px;background:#dcfce7;color:#166534;">H</th>
                        <th style="text-align:center;width:32px;background:#fef3c7;color:#92400e;">S</th>
                        <th style="text-align:center;width:32px;background:#dbeafe;color:#1e40af;">I</th>
                        <th style="text-align:center;width:32px;background:#fee2e2;color:#991b1b;">A</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekap as $i => $r)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $r['nis'] }}</td>
                        <td><strong>{{ $r['nama'] }}</strong></td>
                        @foreach($tanggalList as $tgl)
                        <td class="text-center p-0" style="font-size:0.7rem;">
                            @php $st = $r['absensi'][$tgl] ?? null; @endphp
                            @if($st === 'hadir') <span style="color:#16a34a;">H</span>
                            @elseif($st === 'sakit') <span style="color:#d97706;">S</span>
                            @elseif($st === 'izin') <span style="color:#3b82f6;">I</span>
                            @elseif($st === 'alpha') <span style="color:#ef4444;">A</span>
                            @else <span style="color:#d1d5db;">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="text-center fw-bold" style="color:#16a34a;">{{ $r['hadir'] }}</td>
                        <td class="text-center" style="color:#d97706;">{{ $r['sakit'] }}</td>
                        <td class="text-center" style="color:#3b82f6;">{{ $r['izin'] }}</td>
                        <td class="text-center fw-bold" style="color:#ef4444;">{{ $r['alpha'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($kelasId)
<div class="card"><div class="card-body text-center text-muted py-5">Tidak ada data absensi untuk kelas dan bulan ini.</div></div>
@else
<div class="card"><div class="card-body text-center text-muted py-5"><i class="bi bi-arrow-up-circle me-2"></i>Pilih kelas dan bulan untuk menampilkan rekap absensi.</div></div>
@endif
@endsection
