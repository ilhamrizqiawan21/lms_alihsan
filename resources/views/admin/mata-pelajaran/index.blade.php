@extends('layouts.app')

@section('title', 'Mata Pelajaran')
@section('page_title', 'Mata Pelajaran')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <x-card title="Tambah Mata Pelajaran" icon="bi-plus-circle">
                <form action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
                    @csrf
                    <x-form.input name="kode" label="Kode" placeholder="Contoh: MTK" maxlength="10" required />
                    <x-form.input name="nama_mapel" label="Nama Mata Pelajaran" placeholder="Contoh: Matematika" maxlength="100" required />
                    <x-form.input name="urutan" type="number" label="Urutan" :value="0" min="0" />
                    <x-button type="submit" color="success" size="" icon="bi-save" class="w-100">Simpan</x-button>
                </form>
        </x-card>
    </div>
    <div class="col-md-7 mb-4">
        <x-card title="Daftar Mata Pelajaran" icon="bi-book-fill" body-class="p-0">
                <x-table-wrapper>
                    <table class="table table-hover app-table mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mapel</th>
                                <th>Urutan</th>
                                <th class="table-action-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank($mapel))
                                @foreach($mapel as $m)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $m->kode }}</span></td>
                                <td>{{ $m->nama_mapel }}</td>
                                <td>{{ $m->urutan }}</td>
                                <td class="table-action-column">
                                    <x-action-buttons
                                        edit-target="#editMapelModal{{ $m->id }}"
                                        edit-label="Edit {{ $m->nama_mapel }}"
                                        :delete-action="route('admin.mata-pelajaran.destroy', $m)"
                                        delete-confirm="Hapus {{ $m->nama_mapel }}?"
                                        delete-label="Hapus {{ $m->nama_mapel }}"
                                    />
                                </td>
                            </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada mata pelajaran" icon="bi-book" />
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </x-table-wrapper>
        </x-card>
    </div>
</div>

@foreach($mapel as $m)
<div class="modal fade" id="editMapelModal{{ $m->id }}" tabindex="-1" aria-labelledby="editMapelLabel{{ $m->id }}" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('admin.mata-pelajaran.update', $m) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title" id="editMapelLabel{{ $m->id }}">Edit Mata Pelajaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label" for="kode{{ $m->id }}">Kode</label><input type="text" name="kode" id="kode{{ $m->id }}" class="form-control" value="{{ $m->kode }}" maxlength="10" required></div>
                <div class="mb-3"><label class="form-label" for="namaMapel{{ $m->id }}">Nama Mata Pelajaran</label><input type="text" name="nama_mapel" id="namaMapel{{ $m->id }}" class="form-control" value="{{ $m->nama_mapel }}" maxlength="100" required></div>
                <div><label class="form-label" for="urutan{{ $m->id }}">Urutan</label><input type="number" name="urutan" id="urutan{{ $m->id }}" class="form-control" value="{{ $m->urutan }}" min="0"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </div></div>
</div>
@endforeach
@endsection
