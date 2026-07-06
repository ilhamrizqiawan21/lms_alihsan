@extends('layouts.app')

@section('title', 'Pengumuman')
@section('page_title', 'Pengumuman')

@section('content')
<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-1"></i> Buat Pengumuman</div>
            <div class="card-body">
                <form action="{{ route($routePrefix . '.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                               value="{{ old('judul') }}" required>
                        @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="isi" class="form-control @error('isi') is-invalid @enderror"
                                  rows="4" required>{{ old('isi') }}</textarea>
                        @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target</label>
                        <select name="target" class="form-select @error('target') is-invalid @enderror">
                            <option value="semua">Semua</option>
                            <option value="guru">Guru</option>
                            <option value="siswa">Siswa</option>
                            <option value="kelas_mapel">Kelas Mapel Tertentu</option>
                        </select>
                        @error('target') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-send"></i> Kirim</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone-fill me-1"></i> Daftar Pengumuman</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Target</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengumuman as $p)
                            <tr>
                                <td>{{ $p->judul }}</td>
                                <td><span class="badge bg-info">{{ $p->target }}</span></td>
                                <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if(auth()->user()->isAdmin() || $p->created_by === auth()->id())
                                    <form action="{{ route($routePrefix . '.destroy', $p) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" data-confirm="Hapus pengumuman?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pengumuman</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
