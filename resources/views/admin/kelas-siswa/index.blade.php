@extends('layouts.app')

@section('title', 'Kelas dan Siswa')

@section('content')

<x-page-header title="Kelola Kelas & Siswa" icon="bi-mortarboard-fill" />

@if(session('import_errors'))
<div class="alert alert-danger mb-3" role="alert">
    <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Import siswa gagal</div>
    <ul class="mb-0 ps-3">
        @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@error('file_siswa')
<div class="alert alert-danger mb-3" role="alert">
    <i class="bi bi-x-circle-fill me-1"></i> {{ $message }}
</div>
@enderror

@if(session('student_password'))
@php($credential = session('student_password'))
<div class="alert alert-warning mb-3" role="alert">
    <div class="fw-semibold mb-1"><i class="bi bi-key-fill me-1"></i> {{ $credential['title'] }}</div>
    <div>Nama: <strong>{{ $credential['name'] }}</strong></div>
    <div>Username: <code>{{ $credential['username'] }}</code></div>
    <div>Password: <code>{{ $credential['password'] }}</code></div>
    <div class="small mt-2">Catat dan serahkan password ini secara langsung. Password hanya ditampilkan pada halaman ini setelah proses berhasil.</div>
</div>
@endif

<x-card title="Import Siswa dari Excel" icon="bi-file-earmark-spreadsheet-fill" class="mb-3">
    <x-slot:actions>
        <x-button :href="route('admin.kelas-siswa.import.template')" color="outline-success" icon="bi-download">
            Download Template
        </x-button>
    </x-slot:actions>

    <form action="{{ route('admin.kelas-siswa.import') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-8">
            <x-form.file
                name="file_siswa"
                label="File Excel"
                accept=".xlsx"
                accept-label="Format .xlsx"
                max-size="5MB"
                required
                wrapper-class="mb-0"
                help="Gunakan template yang disediakan. Maksimal 500 siswa per file."
            />
        </div>
        <div class="col-md-4">
            <x-button type="submit" color="primary" size="" icon="bi-upload" class="w-100">
                Import Siswa
            </x-button>
        </div>
    </form>
</x-card>

<x-card title="Tambah Siswa Baru" icon="bi-person-plus-fill" class="mb-3">
    <form action="{{ route('admin.kelas-siswa.store-siswa') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-3">
            <x-form.input name="nis" label="NIS" placeholder="NIS" required wrapper-class="mb-0" />
        </div>
        <div class="col-md-3">
            <x-form.input name="nama_lengkap" label="Nama Lengkap" placeholder="Nama" required wrapper-class="mb-0" />
        </div>
        <div class="col-md-3">
            <x-form.select name="kelas_id" label="Kelas" placeholder="-- Pilih Kelas --" required wrapper-class="mb-0">
                @foreach ($kelasList as $kls)
                    <option value="{{ $kls->id }}" @selected(old('kelas_id') == $kls->id)>{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                @endforeach
            </x-form.select>
        </div>
        <div class="col-md-2">
            <x-form.select name="jenis_kelamin" label="Jenis Kelamin" placeholder="--" required wrapper-class="mb-0">
                <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
            </x-form.select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <x-button type="submit" color="success" size="" icon="bi-plus-lg" class="w-100" aria-label="Tambah siswa" />
        </div>
    </form>
</x-card>

<x-card title="Daftar Kelas" icon="bi-building" class="mb-3" body-class="p-0">
        <x-table-wrapper>
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
                            <button class="btn btn-sm btn-outline-danger btn-icon" data-confirm="Hapus kelas {{ $kls->nama_kelas }}?" title="Hapus kelas {{ $kls->nama_kelas }}" aria-label="Hapus kelas {{ $kls->nama_kelas }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">
                        <x-empty-state title="Belum ada kelas" icon="bi-building" />
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        </x-table-wrapper>
</x-card>

<x-card title="Daftar Siswa" icon="bi-people-fill" body-class="p-0">
    <x-slot:actions>
        <form method="GET" class="d-flex gap-2">
            <select name="kelas_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $kls)
                <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                @endforeach
            </select>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari NIS/Nama..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary" type="submit" title="Cari siswa" aria-label="Cari siswa"><i class="bi bi-search" aria-hidden="true"></i></button>
        </form>
    </x-slot:actions>

        <x-table-wrapper>
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
                            <x-action-buttons
                                edit-target="#editModal{{ $s->id }}"
                                edit-label="Edit {{ $s->user->nama_lengkap ?? $s->nis }}"
                                :reset-action="route('admin.kelas-siswa.reset-password', $s)"
                                reset-confirm="Reset password siswa ke password default 123456?"
                                reset-label="Reset password {{ $s->user->nama_lengkap ?? $s->nis }}"
                                :delete-action="route('admin.kelas-siswa.destroy-siswa', $s)"
                                delete-confirm="Hapus siswa {{ $s->user->nama_lengkap ?? $s->nis }}?"
                                delete-label="Hapus {{ $s->user->nama_lengkap ?? $s->nis }}"
                            />
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6">
                            <x-empty-state title="Tidak ada data siswa" icon="bi-people" />
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </x-table-wrapper>
        <div class="d-flex justify-content-end p-3">
            {{ $siswa->links() }}
        </div>
</x-card>

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
                    <x-form.input name="nis" label="NIS" :value="$s->nis" :use-old="false" required />
                    <x-form.input name="nama_lengkap" label="Nama Lengkap" :value="$s->user->nama_lengkap" :use-old="false" required />
                    <x-form.select name="kelas_id" label="Kelas" :selected="$s->kelas_id" required>
                        @foreach ($kelasList as $kls)
                            <option value="{{ $kls->id }}" @selected($s->kelas_id == $kls->id)>{{ $kls->tingkat }} {{ $kls->nama_kelas }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.select name="jenis_kelamin" label="Jenis Kelamin" :selected="$s->user->jenis_kelamin" required>
                        <option value="L" @selected($s->user->jenis_kelamin === 'L')>Laki-laki</option>
                        <option value="P" @selected($s->user->jenis_kelamin === 'P')>Perempuan</option>
                    </x-form.select>
                    <div class="form-check">
                        <input type="checkbox" name="tinggal_kelas" value="1" class="form-check-input" id="tinggal{{ $s->id }}" {{ $s->tinggal_kelas ? 'checked' : '' }}>
                        <label class="form-check-label" for="tinggal{{ $s->id }}">Tinggal Kelas</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <x-button type="submit" color="primary" size="">Simpan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
