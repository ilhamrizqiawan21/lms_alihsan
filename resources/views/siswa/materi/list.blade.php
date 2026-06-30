@extends('layouts.app')
@section('title', 'Materi: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? ''))

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-text-fill me-2"></i> {{ $kelasMapel->mataPelajaran?->nama_mapel }}</h4>
    <p class="text-muted">Guru: {{ $kelasMapel->guru?->nama_lengkap }}</p>
    <a href="{{ route('siswa.materi.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

@if($materi->count() > 0)
<div class="row">
    @foreach($materi as $m)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-bold">{{ $m->judul }}</h5>
                <p class="text-muted" style="font-size:0.85rem;">{{ $m->deskripsi ?: 'Tidak ada deskripsi' }}</p>
                <small class="text-muted">{{ $m->created_at ? \Carbon\Carbon::parse($m->created_at)->format('d M Y') : '' }}</small>
            </div>
            @if($m->file_materi)
            <div class="card-footer bg-transparent">
                <a href="{{ asset('storage/'.$m->file_materi) }}" class="btn btn-sm btn-success" target="_blank"><i class="bi bi-download me-1"></i> Download</a>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card"><div class="card-body text-center text-muted py-5">Belum ada materi.</div></div>
@endif
@endsection
