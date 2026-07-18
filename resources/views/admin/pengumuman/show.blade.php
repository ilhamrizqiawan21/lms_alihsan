@extends('layouts.app')

@section('title', 'Detail Pengumuman')
@section('page_title', 'Detail Pengumuman')

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-megaphone-fill me-1"></i> Pengumuman</span>
                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <h4 class="mb-2">{{ $pengumuman->judul }}</h4>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-info">{{ $pengumuman->target }}</span>
                    @if($pengumuman->target === 'kelas_mapel')
                        <span class="badge bg-secondary">
                            {{ $pengumuman->kelasMapel?->kelas?->nama_kelas ?? '-' }} - {{ $pengumuman->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}
                        </span>
                    @endif
                </div>
                <div class="text-muted small mb-4">
                    Dibuat oleh {{ $pengumuman->creator?->nama_lengkap ?? '-' }}
                    pada {{ $pengumuman->created_at ? \Carbon\Carbon::parse($pengumuman->created_at)->format('d M Y H:i') : '-' }}
                </div>
                <div class="pengumuman-content">
                    {!! nl2br(e($pengumuman->isi)) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .pengumuman-content {
        color: var(--text-body);
        line-height: 1.7;
        white-space: normal;
    }
</style>
@endpush
