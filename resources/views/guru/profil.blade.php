@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="page-header"><h4><i class="bi bi-person-circle me-2"></i> Profil Guru</h4></div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i> Edit Profil</div>
            <div class="card-body">
                <form action="{{ route('guru.profil.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIP / NIK</label>
                        <input type="text" name="nip_nis" class="form-control" value="{{ old('nip_nis', $user->nip_nis) }}">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Wajib bila mengganti password" autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah" minlength="8" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password">
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i> Informasi Akun</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td style="width:120px;color:var(--gray-500);">Username</td><td><strong>{{ $user->username }}</strong></td></tr>
                    <tr><td style="color:var(--gray-500);">Nama</td><td>{{ $user->nama_lengkap }}</td></tr>
                    <tr><td style="color:var(--gray-500);">Role</td><td><span class="badge badge-guru">Guru</span></td></tr>
                    <tr><td style="color:var(--gray-500);">NIP/NIK</td><td>{{ $user->nip_nis ?? '-' }}</td></tr>
                    <tr><td style="color:var(--gray-500);">Status</td><td>{!! $user->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>' !!}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
