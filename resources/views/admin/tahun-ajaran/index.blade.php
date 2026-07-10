@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Tambah Tahun Ajaran</div>
            <div class="card-body">
                <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                               placeholder="Contoh: 2026/2027" value="{{ old('tahun') }}" required>
                        @error('tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" id="is_active">
                            <label class="form-check-label" for="is_active">Jadikan Aktif</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-event-fill me-1"></i> Daftar Tahun Ajaran</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Tahun</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjaran as $ta)
                            <tr>
                                <td><strong>{{ $ta->tahun }}</strong></td>
                                <td>
                                    @if($ta->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTahunAjaranModal{{ $ta->id }}" title="Edit tahun ajaran">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if(!$ta->is_active)
                                        <form action="{{ route('admin.tahun-ajaran.set-aktif', $ta) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-primary" data-confirm="Aktifkan tahun ajaran ini? Semester aktif akan kembali ke Semester 1. Pastikan penugasan kelas dan pengajaran tahun baru sudah disiapkan.">
                                                <i class="bi bi-check-circle-fill"></i> Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.tahun-ajaran.destroy', $ta) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-confirm="Hapus tahun ajaran ini?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada tahun ajaran</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($tahunAjaran as $ta)
<div class="modal fade" id="editTahunAjaranModal{{ $ta->id }}" tabindex="-1" aria-labelledby="editTahunAjaranLabel{{ $ta->id }}" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
        <form action="{{ route('admin.tahun-ajaran.update', $ta) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title" id="editTahunAjaranLabel{{ $ta->id }}">Edit Tahun Ajaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <label class="form-label" for="tahun{{ $ta->id }}">Tahun Ajaran</label>
                <input type="text" name="tahun" id="tahun{{ $ta->id }}" class="form-control" value="{{ $ta->tahun }}" placeholder="2026/2027" maxlength="9" pattern="[0-9]{4}/[0-9]{4}" required>
                <div class="form-check mt-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="aktif{{ $ta->id }}" @checked($ta->is_active)>
                    <label class="form-check-label" for="aktif{{ $ta->id }}">Jadikan aktif</label>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
        </form>
    </div></div>
</div>
@endforeach
@endsection
