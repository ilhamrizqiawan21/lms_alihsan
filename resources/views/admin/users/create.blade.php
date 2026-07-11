@extends('layouts.app')

@section('title', 'Tambah Guru & Staf')
@section('page_title', 'Tambah Guru & Staf')

@section('content')
<x-card title="Form Tambah Guru & Staf" icon="bi-plus-circle">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <x-form.section title="Identitas Akun" icon="bi-person-badge" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="username" label="Username" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="nama_lengkap" label="Nama Lengkap" required />
                    </div>
                </div>
            </x-form.section>

            <x-form.section title="Akses & Role" icon="bi-shield-lock" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input name="password" type="password" label="Password" help="Minimal 8 karakter." required />
                    </div>
                    <div class="col-md-3">
                        <x-form.select name="role_id" label="Role" placeholder="-- Pilih Role --" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                    {{ ucwords($role->nama_role) }}
                                </option>
                            @endforeach
                        </x-form.select>
                    </div>
                    <div class="col-md-3">
                        <x-form.input name="nip_nis" label="NIP/ID Staf" />
                    </div>
                </div>
            </x-form.section>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="is_active">
                    <label class="form-check-label" for="is_active">Akun Aktif</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
</x-card>

@endsection
