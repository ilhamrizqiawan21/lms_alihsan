@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="page-header"><h4><i class="bi bi-gear-fill me-2"></i> Konfigurasi Sistem</h4></div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-palette me-2"></i> Tampilan & Umum</div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.save') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Sekolah</label>
                        <input type="text" name="nama_sekolah" class="form-control" value="{{ $settings['nama_sekolah'] ?? school_setting('school_name', 'Nama Sekolah') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna Tema</label>
                        <select name="warna_tema" class="form-select">
                            <option value="hijau" {{ ($settings['warna_tema'] ?? 'hijau') === 'hijau' ? 'selected' : '' }}>🟢 Hijau (Default)</option>
                            <option value="biru-azure" {{ ($settings['warna_tema'] ?? '') === 'biru-azure' ? 'selected' : '' }}>🔵 Biru Azure</option>
                            <option value="biru-aqua" {{ ($settings['warna_tema'] ?? '') === 'biru-aqua' ? 'selected' : '' }}>🌊 Biru Aqua</option>
                        </select>
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar3 me-2"></i> Akademik</div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.save') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Semester Aktif</label>
                        <select name="semester_aktif" class="form-select">
                            <option value="1" {{ ($settings['semester_aktif'] ?? '1') == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ ($settings['semester_aktif'] ?? '') == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran Aktif</label>
                        <input type="text" class="form-control" value="{{ $tahunAjaranAktif?->tahun ?? 'Belum diatur' }}" readonly>
                        <div class="form-text">Ganti tahun ajaran melalui menu Tahun Ajaran agar arsip lama tetap tersimpan dan kelas siswa aktif dikosongkan.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mode Kenaikan Kelas</label>
                        <select name="mode_kenaikan" class="form-select">
                            <option value="manual" {{ ($settings['mode_kenaikan'] ?? 'manual') === 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="auto" {{ ($settings['mode_kenaikan'] ?? '') === 'auto' ? 'selected' : '' }}>Otomatis</option>
                        </select>
                    </div>
                    <button class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
