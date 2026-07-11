@extends('layouts.app')

@section('title', 'Penanganan Siswa')

@section('content')
<x-page-header title="Penanganan Siswa" icon="bi-heart-pulse">
    <x-badge color="primary">{{ $waliKelas->kelas?->tingkat }} {{ $waliKelas->kelas?->nama_kelas }}</x-badge>
</x-page-header>

@php
    $penangananRows = $penanganan instanceof \Illuminate\Pagination\AbstractPaginator ? collect($penanganan->items()) : collect($penanganan ?? []);
@endphp

<div class="row gy-4">
    <div class="col-lg-4">
        <x-card title="Tambah Penanganan" icon="bi-plus-circle">
            <form action="{{ route('guru.wali-kelas.penanganan.store', $waliKelas) }}" method="POST">
                @csrf
                <x-form.select name="siswa_id" label="Siswa" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}" @selected(old('siswa_id') == $s->id)>{{ $s->nis }} - {{ $s->user?->nama_lengkap }}</option>
                    @endforeach
                </x-form.select>
                <x-form.input name="kondisi" label="Kondisi" maxlength="200" required />
                <x-form.textarea name="deskripsi" label="Deskripsi" rows="3" />
                <x-form.textarea name="tindak_lanjut" label="Tindak Lanjut" rows="3" />
                <x-form.textarea name="hasil" label="Hasil" rows="3" />
                <x-form.select name="status" label="Status" :selected="old('status', 'baru')" required>
                    <option value="baru" @selected(old('status', 'baru') === 'baru')>Baru</option>
                    <option value="proses" @selected(old('status', 'baru') === 'proses')>Proses</option>
                    <option value="selesai" @selected(old('status', 'baru') === 'selesai')>Selesai</option>
                </x-form.select>
                <x-button type="submit" color="success" icon="bi-save" class="w-100">Simpan</x-button>
            </form>
        </x-card>
    </div>
    <div class="col-lg-8">
        <x-card title="Daftar Penanganan" icon="bi-list-ul" body-class="p-0">
            <x-table-wrapper>
                <table class="table table-hover mb-0">
                    <thead><tr><th>Siswa</th><th>Kondisi</th><th>Tindak Lanjut</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @if($penangananRows->isNotEmpty())
                            @foreach($penangananRows as $p)
                                <tr>
                                    <td><strong>{{ $p->siswa?->user?->nama_lengkap ?? '-' }}</strong><div class="small text-muted">{{ $p->siswa?->nis }}</div></td>
                                    <td>{{ $p->kondisi }}<div class="small text-muted">{{ $p->deskripsi }}</div></td>
                                    <td>{{ $p->tindak_lanjut }}<div class="small text-muted">{{ $p->hasil }}</div></td>
                                    <td><span class="badge bg-{{ $p->status === 'selesai' ? 'success' : ($p->status === 'proses' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($p->status) }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#edit-penanganan-{{ $p->id }}"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('guru.wali-kelas.penanganan.destroy', [$waliKelas, $p]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" data-confirm="Hapus penanganan siswa ini?"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="edit-penanganan-{{ $p->id }}">
                                    <td colspan="5">
                                        <form action="{{ route('guru.wali-kelas.penanganan.update', [$waliKelas, $p]) }}" method="POST" class="row g-3">
                                            @csrf @method('PUT')
                                            <div class="col-md-6">
                                                <label class="form-label">Siswa</label>
                                                <select name="siswa_id" class="form-select" required>
                                                    @foreach($siswaList as $s)
                                                        <option value="{{ $s->id }}" @selected($p->siswa_id == $s->id)>{{ $s->nis }} - {{ $s->user?->nama_lengkap }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kondisi</label>
                                                <input type="text" name="kondisi" class="form-control" value="{{ $p->kondisi }}" maxlength="200" required>
                                            </div>
                                            <div class="col-md-6"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3">{{ $p->deskripsi }}</textarea></div>
                                            <div class="col-md-6"><label class="form-label">Tindak Lanjut</label><textarea name="tindak_lanjut" class="form-control" rows="3">{{ $p->tindak_lanjut }}</textarea></div>
                                            <div class="col-md-6"><label class="form-label">Hasil</label><textarea name="hasil" class="form-control" rows="3">{{ $p->hasil }}</textarea></div>
                                            <div class="col-md-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="baru" @selected($p->status === 'baru')>Baru</option>
                                                    <option value="proses" @selected($p->status === 'proses')>Proses</option>
                                                    <option value="selesai" @selected($p->status === 'selesai')>Selesai</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-grid align-self-end">
                                                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada penanganan siswa.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-table-wrapper>
            @if($penanganan->hasPages())
                <x-slot:footer>{{ $penanganan->links() }}</x-slot:footer>
            @endif
        </x-card>
    </div>
</div>
@endsection
