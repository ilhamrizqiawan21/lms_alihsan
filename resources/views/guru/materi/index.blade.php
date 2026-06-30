@extends('layouts.app')

@section('title', 'Materi')

@section('content')
<div class="page-header"><h4><i class="bi bi-file-earmark-text-fill me-2"></i> Materi Pembelajaran</h4></div>

@if($kelasMapel->count() == 0)
<div class="card"><div class="card-body text-center text-muted py-5">Anda belum memiliki penugasan mengajar semester ini.</div></div>
@else
<div class="card">
    <div class="card-header"><i class="bi bi-book me-2"></i> Pilih Kelas & Mata Pelajaran</div>
    <div class="card-body">
        <div class="row">
            @foreach($kelasMapel as $km)
            <div class="col-md-4 mb-3">
                <a href="{{ route('guru.materi.list', $km) }}" class="text-decoration-none">
                    <div class="card border h-100 hover-shadow" style="transition:all 0.2s;">
                        <div class="card-body text-center">
                            <div style="font-size:2rem;color:var(--primary-500);">{{ strtoupper(substr($km->mataPelajaran?->nama_mapel ?? 'MP', 0, 2)) }}</div>
                            <strong>{{ $km->mataPelajaran?->nama_mapel ?? '-' }}</strong>
                            <div class="text-muted" style="font-size:0.8rem;">{{ $km->kelas?->nama_kelas ?? '-' }} — Sem. {{ $km->semester }}</div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
