@extends('layouts.app')

@section('title', 'Wali Kelas')

@section('content')
<x-page-header title="Wali Kelas" icon="bi-person-badge-fill" />

@if($waliKelas->count() === 0)
    <x-card>
        <x-empty-state title="Belum ada penugasan wali kelas" icon="bi-person-badge" message="Anda belum ditugaskan sebagai wali kelas pada tahun ajaran aktif." />
    </x-card>
@else
    <div class="row gy-4">
        @foreach($waliKelas as $wk)
            <div class="col-md-6 col-xl-4">
                <x-card title="{{ $wk->kelas?->tingkat }} {{ $wk->kelas?->nama_kelas }}" icon="bi-building">
                    <div class="d-flex flex-column gap-2">
                        <div class="text-muted small">Tahun Ajaran {{ $wk->tahunAjaran?->tahun ?? '-' }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">{{ $wk->absensi_count }} absensi</span>
                            <span class="badge bg-info text-dark">{{ $wk->pertemuan_count }} pertemuan</span>
                            <span class="badge bg-warning text-dark">{{ $wk->penanganan_aktif_count }} penanganan aktif</span>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <x-button :href="route('guru.wali-kelas.absensi', $wk)" color="outline-primary" icon="bi-clipboard-check">Absensi Harian</x-button>
                            <x-button :href="route('guru.wali-kelas.pertemuan', $wk)" color="outline-secondary" icon="bi-calendar-event">Pertemuan</x-button>
                            <x-button :href="route('guru.wali-kelas.penanganan', $wk)" color="outline-danger" icon="bi-heart-pulse">Penanganan Siswa</x-button>
                        </div>
                    </div>
                </x-card>
            </div>
        @endforeach
    </div>
@endif
@endsection
