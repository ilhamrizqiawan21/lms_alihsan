@extends('layouts.app')

@section('title', 'Guru & Staf')
@section('page_title', 'Guru & Staf')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-1"></i> Daftar Guru & Staf</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Guru/Staf
        </a>
    </div>
    <div class="card-body">
        <form class="row g-3 mb-3" method="GET">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari username/nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role_id" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucwords($role->nama_role) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit"><i class="bi bi-search"></i> Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>{{ $user->username }}</strong></td>
                        <td>{{ $user->nama_lengkap }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ ucwords($user->role?->nama_role ?? '-') }}</span></td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-{{ $user->is_active ? 'secondary' : 'success' }}"
                                        data-confirm="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user ini?">
                                    <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" data-confirm="Hapus user ini?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data guru atau staf</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
