@extends('layouts.app')

@section('title', 'Guru & Staf')
@section('page_title', 'Guru & Staf')

@section('content')
<x-card title="Daftar Guru & Staf" icon="bi-people-fill">
    <x-slot:actions>
        <x-button :href="route('admin.users.create')" color="success" icon="bi-plus-lg">Tambah Guru/Staf</x-button>
    </x-slot:actions>

        <form class="row g-2 app-table-filter mb-3" method="GET">
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

        <x-table-wrapper>
            <table class="table table-hover app-table mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="table-action-column">Aksi</th>
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
                        <td class="table-action-column">
                            <x-action-buttons
                                :edit-href="route('admin.users.edit', $user)"
                                edit-label="Edit {{ $user->nama_lengkap }}"
                                :delete-action="route('admin.users.destroy', $user)"
                                delete-confirm="Hapus user ini?"
                                delete-label="Hapus {{ $user->nama_lengkap }}"
                            >
                                <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-{{ $user->is_active ? 'outline-secondary' : 'outline-success' }} btn-icon"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $user->nama_lengkap }}"
                                            aria-label="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $user->nama_lengkap }}"
                                            data-confirm="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} user ini?">
                                        <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                    </button>
                                </form>
                            </x-action-buttons>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state title="Tidak ada data guru atau staf" icon="bi-people" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-wrapper>
        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>
</x-card>
@endsection
