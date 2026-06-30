@extends('layouts.app')
@section('title', 'Rekap Tugas')

@section('content')
<div class="page-header"><h4><i class="bi bi-journal-check me-2"></i> Rekap Tugas</h4></div>

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
                @if($kelasId && count($tugasList) > 0)
                <a href="{{ route('admin.export.tugas.excel', request()->only(['kelas_id', 'semester'])) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
                <a href="{{ route('admin.export.tugas.pdf', request()->only(['kelas_id', 'semester'])) }}" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($kelasId && count($tugasList) > 0)
<div class="card">
    <div class="card-header"><i class="bi bi-table me-2"></i> Kelas {{ $kelasNama }} — Semester {{ $semester == '1' ? 'Ganjil' : 'Genap' }} {{ $taAktif?->tahun }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:var(--primary-700);color:white;">
                    <tr><th>Judul Tugas</th><th>Mapel</th><th>Guru</th><th>Deadline</th><th>Kategori</th><th class="text-center">Sudah Kumpul</th><th class="text-center">Total</th><th class="text-center">%</th></tr>
                </thead>
                <tbody>
                    @foreach($tugasList as $t)
                    <tr>
                        <td><strong>{{ $t->judul }}</strong></td>
                        <td>{{ $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                        <td>{{ $t->kelasMapel?->guru?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $t->batas_waktu ? \Carbon\Carbon::parse($t->batas_waktu)->format('d M Y') : '-' }}</td>
                        <td><span class="badge bg-secondary">{{ $t->kategori_nilai ?? 'NH' }}</span></td>
                        <td class="text-center"><strong style="color:var(--primary-600);">{{ $t->sudah_kumpul ?? 0 }}</strong></td>
                        <td class="text-center">{{ $t->total_siswa ?? 0 }}</td>
                        <td class="text-center fw-bold" style="color:{{ ($t->total_siswa > 0 ? round(($t->sudah_kumpul / $t->total_siswa) * 100) : 0) >= 75 ? '#16a34a' : '#ef4444' }};">
                            {{ $t->total_siswa > 0 ? round(($t->sudah_kumpul / $t->total_siswa) * 100) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@elseif($kelasId)
<div class="card"><div class="card-body text-center text-muted py-5">Tidak ada tugas untuk filter ini.</div></div>
@else
<div class="card"><div class="card-body text-center text-muted py-5">Pilih kelas untuk menampilkan rekap tugas.</div></div>
@endif
@endsection
