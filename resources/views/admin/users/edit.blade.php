@extends('layouts.app')

@section('title', 'Edit Guru & Staf')
@section('page_title', 'Edit Guru & Staf')

@section('content')
<x-card title="Form Edit Guru & Staf" icon="bi-pencil-square">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            <x-form.section title="Identitas Akun" icon="bi-person-badge" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="username" label="Username" :value="$user->username" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="nama_lengkap" label="Nama Lengkap" :value="$user->nama_lengkap" required />
                    </div>
                </div>
            </x-form.section>

            <x-form.section title="Akses & Role" icon="bi-shield-lock" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="password" type="password" label="Password" help="Kosongkan jika tidak ingin mengubah password." />
                    </div>
                    <div class="col-md-3">
                        <x-form.select name="role_id" label="Role" :selected="$user->role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ ucwords($role->nama_role) }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <div class="col-md-3">
                        <x-form.input name="nip_nis" label="NIP/ID Staf" :value="$user->nip_nis" />
                    </div>
                </div>
            </x-form.section>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1"
                           id="is_active" {{ $user->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
</x-card>
@endsection
