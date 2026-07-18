@extends('layouts.app')

@section('title', 'Wali Kelas')

@section('content')
<x-page-header title="Wali Kelas" icon="bi-person-badge-fill" />

@php
    $wk = $waliKelas instanceof \Illuminate\Support\Collection ? $waliKelas->first() : $waliKelas;
@endphp

@if(!$wk)
    <x-card>
        <x-empty-state title="Belum ada penugasan wali kelas" icon="bi-person-badge" message="Anda belum ditugaskan sebagai wali kelas pada tahun ajaran aktif." />
    </x-card>
@else
    <div class="row gy-4">
        <div class="col-12 col-xl-8">
            <x-card title="Kelas Wali {{ $wk->kelas?->tingkat }} {{ $wk->kelas?->nama_kelas }}" icon="bi-building">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="text-muted small">Tahun Ajaran</div>
                        <div class="fw-semibold">{{ $wk->tahunAjaran?->tahun ?? '-' }}</div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">{{ $wk->absensi_count }} absensi</span>
                        <span class="badge bg-info text-dark">{{ $wk->pertemuan_count }} pertemuan</span>
                        <span class="badge bg-warning text-dark">{{ $wk->penanganan_aktif_count }} penanganan aktif</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4 d-grid">
                            <x-button :href="route('guru.wali-kelas.absensi', $wk)" color="outline-primary" icon="bi-clipboard-check">Absensi Harian</x-button>
                        </div>
                        <div class="col-md-4 d-grid">
                            <x-button :href="route('guru.wali-kelas.pertemuan', $wk)" color="outline-secondary" icon="bi-calendar-event">Pertemuan</x-button>
                        </div>
                        <div class="col-md-4 d-grid">
                            <x-button :href="route('guru.wali-kelas.penanganan', $wk)" color="outline-danger" icon="bi-heart-pulse">Penanganan Siswa</x-button>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
@endif
@endsection
