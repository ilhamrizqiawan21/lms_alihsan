@extends('layouts.app')

@section('title', 'Pertemuan Wali Kelas')

@section('content')
<x-page-header title="Pertemuan Wali Kelas" icon="bi-calendar-event">
    <x-badge color="primary">{{ $waliKelas->kelas?->tingkat }} {{ $waliKelas->kelas?->nama_kelas }}</x-badge>
</x-page-header>

@php
    $pertemuanRows = $pertemuan instanceof \Illuminate\Pagination\AbstractPaginator ? collect($pertemuan->items()) : collect($pertemuan ?? []);
@endphp

<div class="row gy-4">
    <div class="col-lg-4">
        <x-card title="Tambah Pertemuan" icon="bi-plus-circle">
            <form action="{{ route('guru.wali-kelas.pertemuan.store', $waliKelas) }}" method="POST">
                @csrf
                <x-form.input type="date" name="tanggal" label="Hari/Tanggal" required />
                <x-form.input name="topik" label="Topik" maxlength="200" required />
                <x-form.textarea name="hasil" label="Hasil" rows="5" required />
                <x-button type="submit" color="success" icon="bi-save" class="w-100">Simpan</x-button>
            </form>
        </x-card>
    </div>
    <div class="col-lg-8">
        <x-card title="Daftar Pertemuan" icon="bi-list-ul" body-class="p-0">
            <x-table-wrapper>
                <table class="table table-hover mb-0">
                    <thead><tr><th>Tanggal</th><th>Topik</th><th>Hasil</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @if($pertemuanRows->isNotEmpty())
                            @foreach($pertemuanRows as $p)
                                <tr>
                                    <td>{{ $p->tanggal?->format('d/m/Y') }}</td>
                                    <td><strong>{{ $p->topik }}</strong></td>
                                    <td>{{ $p->hasil }}</td>
                                    <td>
                                        <form action="{{ route('guru.wali-kelas.pertemuan.destroy', [$waliKelas, $p]) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" data-confirm="Hapus pertemuan ini?"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pertemuan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-table-wrapper>
            @if($pertemuan->hasPages())
                <x-slot:footer>{{ $pertemuan->links() }}</x-slot:footer>
            @endif
        </x-card>
    </div>
</div>
@endsection
