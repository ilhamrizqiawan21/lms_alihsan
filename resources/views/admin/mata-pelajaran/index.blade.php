@extends('layouts.app')

@section('title', 'Mata Pelajaran')
@section('page_title', 'Mata Pelajaran')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Tambah Mata Pelajaran</div>
            <div class="card-body">
                <form action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                               placeholder="Contoh: MTK" value="{{ old('kode') }}" required>
                        @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control @error('nama_mapel') is-invalid @enderror"
                               placeholder="Contoh: Matematika" value="{{ old('nama_mapel') }}" required>
                        @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 0) }}" min="0">
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-book-fill me-1"></i> Daftar Mata Pelajaran</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Mapel</th>
                                <th>Urutan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapel as $m)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $m->kode }}</span></td>
                                <td>{{ $m->nama_mapel }}</td>
                                <td>{{ $m->urutan }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editMapelModal{{ $m->id }}" title="Edit mata pelajaran"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('admin.mata-pelajaran.destroy', $m) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-confirm="Hapus {{ $m->nama_mapel }}?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada mapel</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
