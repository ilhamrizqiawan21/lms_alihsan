@extends('layouts.app')

@section('title', 'Data Kelas')
@section('page_title', 'Data Kelas')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Tambah Kelas</div>
            <div class="card-body">
                <form action="{{ route('admin.kelas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tingkat</label>
                        <select name="tingkat" class="form-select @error('tingkat') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="VII">VII</option>
                            <option value="VIII">VIII</option>
                            <option value="IX">IX</option>
                        </select>
                        @error('tingkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control @error('nama_kelas') is-invalid @enderror"
                               placeholder="Contoh: VII-A" value="{{ old('nama_kelas') }}" required>
                        @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-building me-1"></i> Daftar Kelas</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tingkat</th>
                                <th>Nama Kelas</th>
                                <th>Jumlah Siswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kelas as $k)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $k->tingkat }}</span></td>
                                <td><strong>{{ $k->nama_kelas }}</strong></td>
                                <td>{{ $k->siswa_count ?? 0 }} siswa</td>
                                <td>
                                    <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-confirm="Hapus kelas {{ $k->nama_kelas }}?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada kelas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
