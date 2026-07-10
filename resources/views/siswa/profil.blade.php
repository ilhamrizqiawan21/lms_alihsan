@extends('layouts.app')
@section('title', 'Profil')

@section('content')
<div class="page-header"><h4><i class="bi bi-person-circle me-2"></i> Profil Siswa</h4></div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i> Informasi Saya</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td style="width:120px;color:var(--gray-500);">NIS</td><td><strong>{{ $user->siswa?->nis ?? '-' }}</strong></td></tr>
                    <tr><td style="color:var(--gray-500);">Nama</td><td>{{ $user->nama_lengkap }}</td></tr>
                    <tr><td style="color:var(--gray-500);">Username</td><td>{{ $user->username }}</td></tr>
                    <tr><td style="color:var(--gray-500);">Kelas</td><td>{{ $user->siswa?->kelas?->tingkat }} {{ $user->siswa?->kelas?->nama_kelas }}</td></tr>
                    <tr><td style="color:var(--gray-500);">Status</td><td>{!! $user->siswa?->status === 'aktif' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">'.$user->siswa?->status.'</span>' !!}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-key-fill me-2"></i> Ganti Password</div>
            <div class="card-body">
                <form action="{{ route('siswa.profil.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save me-1"></i> Ganti Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
