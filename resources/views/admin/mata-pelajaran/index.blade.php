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
@endsection
