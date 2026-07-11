@extends('layouts.app')

@section('title', 'Laporan Wali Kelas')

@section('content')
<x-page-header title="Laporan Wali Kelas" icon="bi-person-badge-fill" />

@php
    $waliKelasRows = $waliKelas instanceof \Illuminate\Pagination\AbstractPaginator ? collect($waliKelas->items()) : collect($waliKelas ?? []);
@endphp

<x-card title="Daftar Wali Kelas Aktif" icon="bi-list-ul" body-class="p-0">
    <x-table-wrapper>
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Absensi</th>
                    <th>Pertemuan</th>
                    <th>Penanganan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($waliKelasRows->isNotEmpty())
                    @foreach($waliKelasRows as $wk)
                        <tr>
                            <td><strong>{{ $wk->kelas?->tingkat }} {{ $wk->kelas?->nama_kelas }}</strong></td>
                            <td>{{ $wk->guru?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $wk->tahunAjaran?->tahun ?? '-' }}</td>
                            <td>{{ $wk->absensi_count }}</td>
                            <td>{{ $wk->pertemuan_count }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $wk->penanganan_aktif_count }} aktif</span> <span class="text-muted small">/ {{ $wk->penanganan_siswa_count }} total</span></td>
                            <td><x-button :href="route('kepsek.laporan.wali-kelas.show', $wk)" color="outline-primary" icon="bi-eye">Detail</x-button></td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada wali kelas aktif.</td></tr>
                @endif
            </tbody>
        </table>
    </x-table-wrapper>
    @if($waliKelas->hasPages())
        <x-slot:footer>{{ $waliKelas->links() }}</x-slot:footer>
    @endif
</x-card>
@endsection
