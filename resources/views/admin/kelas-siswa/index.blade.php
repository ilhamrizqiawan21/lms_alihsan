@extends('layouts.app')

@section('title', 'Kelas & Siswa')

@section('content')

<div class="page-header">
    <h4><i class="bi bi-mortarboard-fill me-2"></i> Kelola Kelas & Siswa</h4>
</div>

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-person-plus-fill me-2"></i> Tambah Siswa Baru</div>
    <div class="card-body">
        <form action="{{ route('admin.kelas-siswa.store-siswa') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label">NIS</label>
                <input type="text" name="nis" class="form-control" placeholder="NIS" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <select name="kelas_id" class="form-select" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelasList as $kls)
                    <option value="{{ $kls->id }}">{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select" required>
                    <option value="">--</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-success w-100"><i class="bi bi-plus-lg"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><i class="bi bi-building me-2"></i> Daftar Kelas</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Tingkat</th><th>Kelas</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @if ($kelasList->count() > 0)
                @foreach ($kelasList as $kls)
                <tr>
                    <td><span class="badge bg-secondary">{{ $kls->tingkat }}</span></td>
                    <td><strong>{{ $kls->nama_kelas }}</strong></td>
                    <td class="text-center">
                        @if ($kls->tingkat === 'IX')
                        <form action="{{ route('admin.kelas-siswa.luluskan-kelas', $kls) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-success" data-confirm="Luluskan semua siswa kelas {{ $kls->nama_kelas }}?">
                                <i class="bi bi-check-circle"></i> Luluskan
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.kelas.destroy', $kls) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus kelas {{ $kls->nama_kelas }}?">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada kelas</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i> Daftar Siswa</span>
        <form method="GET" class="d-flex gap-2">
            <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $kls)
                <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIS/Nama..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>NIS</th><th>Nama</th><th>JK</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @if ($siswa->count() > 0)
                    @foreach ($siswa as $s)
                    <tr>
                        <td>{{ $s->nis }}</td>
                        <td><strong>{{ $s->user->nama_lengkap ?? '-' }}</strong></td>
                        <td>{{ $s->user->jenis_kelamin ?? '-' }}</td>
                        <td>{{ $s->kelas->tingkat ?? '' }} {{ $s->kelas->nama_kelas ?? '' }}</td>
                        <td>
                            @if ($s->status === 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @elseif ($s->status === 'lulus')
                                <span class="badge bg-info">Lulus</span>
                            @else
                                <span class="badge bg-secondary">{{ $s->status }}</span>
                            @endif
                            @if ($s->tinggal_kelas)
                                <span class="badge bg-warning">Tinggal Kelas</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $s->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.kelas-siswa.reset-password', $s) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-outline-secondary" data-confirm="Reset password siswa ke password acak baru?" title="Reset Password">
                                    <i class="bi bi-key"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.kelas-siswa.destroy-siswa', $s) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus siswa {{ $s->user->nama_lengkap ?? $s->nis }}?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data siswa</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">
            {{ $siswa->links() }}
        </div>
    </div>
</div>

@foreach ($siswa as $s)
<div class="modal fade" id="editModal{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Siswa: {{ $s->user->nama_lengkap ?? $s->nis }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kelas-siswa.update-siswa', $s) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIS</label>
                        <input type="text" name="nis" class="form-control" value="{{ $s->nis }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $s->user->nama_lengkap }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            @foreach ($kelasList as $kls)
                            <option value="{{ $kls->id }}" {{ $s->kelas_id == $kls->id ? 'selected' : '' }}>{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L" {{ $s->user->jenis_kelamin === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $s->user->jenis_kelamin === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="tinggal_kelas" value="1" class="form-check-input" id="tinggal{{ $s->id }}" {{ $s->tinggal_kelas ? 'checked' : '' }}>
                        <label class="form-check-label" for="tinggal{{ $s->id }}">Tinggal Kelas</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
