@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page_title', 'Data Kelas')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <x-card title="Tambah Kelas" icon="bi-plus-circle">
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <x-form.select name="tingkat" label="Tingkat" placeholder="-- Pilih --" required>
                        <option value="VII" @selected(old('tingkat') === 'VII')>VII</option>
                        <option value="VIII" @selected(old('tingkat') === 'VIII')>VIII</option>
                        <option value="IX" @selected(old('tingkat') === 'IX')>IX</option>
                    </x-form.select>
                    <x-form.input name="nama_kelas" label="Nama Kelas" placeholder="Contoh: VII-A" required />
                    <x-button type="submit" color="success" size="" icon="bi-save" class="w-100">Simpan</x-button>
                </form>
        </x-card>
    </div>
    <div class="col-md-7 mb-4">
        <x-card title="Daftar Kelas" icon="bi-building" body-class="p-0">
                <x-table-wrapper>
                    <table class="table table-hover app-table mb-0">
                        <thead>
                            <tr>
                                <th>Tingkat</th>
                                <th>Nama Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th class="table-action-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank($kelas))
                                @foreach($kelas as $k)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $k->tingkat }}</span></td>
                                <td><strong>{{ $k->nama_kelas }}</strong></td>
                                <td>{{ $k->siswa_count ?? 0 }} siswa</td>
                                <td class="table-action-column">
                                    <x-action-buttons
                                        edit-target="#editKelasModal{{ $k->id }}"
                                        edit-label="Edit kelas {{ $k->nama_kelas }}"
                                        :delete-action="route('admin.kelas.destroy', $k)"
                                        delete-confirm="Hapus kelas {{ $k->nama_kelas }}?"
                                        delete-label="Hapus kelas {{ $k->nama_kelas }}"
                                    />
                                </td>
                            </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="4">
                                    <x-empty-state title="Belum ada kelas" icon="bi-building" />
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </x-table-wrapper>
        </x-card>
    </div>
</div>

@foreach($kelas as $k)
<div class="modal fade" id="editKelasModal{{ $k->id }}" tabindex="-1" aria-labelledby="editKelasLabel{{ $k->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kelas.update', $k) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editKelasLabel{{ $k->id }}">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="tingkat{{ $k->id }}">Tingkat</label>
                        <select name="tingkat" id="tingkat{{ $k->id }}" class="form-select" required>
                            @foreach(['VII', 'VIII', 'IX'] as $tingkat)
                                <option value="{{ $tingkat }}" @selected($k->tingkat === $tingkat)>{{ $tingkat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="namaKelas{{ $k->id }}">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="namaKelas{{ $k->id }}" class="form-control" value="{{ $k->nama_kelas }}" maxlength="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
