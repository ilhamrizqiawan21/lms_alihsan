@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
@php
    $themeBaseColors = [
        'hijau' => '#198754',
        'biru-azure' => '#0d6efd',
        'biru-aqua' => '#0891b2',
    ];
    $selectedTheme = $settings['warna_tema'] ?? 'hijau';
    $baseColor = $settings['warna_base'] ?? ($themeBaseColors[$selectedTheme] ?? $themeBaseColors['hijau']);
@endphp
<div class="page-header"><h4><i class="bi bi-gear-fill me-2"></i> Konfigurasi Sistem</h4></div>

@if($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Data belum bisa disimpan.</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-buildings-fill text-success"></i>
    <h5 class="mb-0">Pengaturan Sekolah</h5>
</div>
@include('admin.school-settings._form', ['setting' => $schoolSetting])

<div class="d-flex align-items-center gap-2 mb-3 mt-2">
    <i class="bi bi-sliders text-success"></i>
    <h5 class="mb-0">Pengaturan Sistem</h5>
</div>
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-palette me-2"></i> Tampilan & Umum</div>
            <div class="card-body">
                <form action="{{ route('admin.pengaturan.save') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Warna Tema</label>
                        <select name="warna_tema" class="form-select">
                            <option value="hijau" {{ ($settings['warna_tema'] ?? 'hijau') === 'hijau' ? 'selected' : '' }}>🟢 Hijau (Default)</option>
                            <option value="biru-azure" {{ ($settings['warna_tema'] ?? '') === 'biru-azure' ? 'selected' : '' }}>🔵 Biru Azure</option>
                            <option value="biru-aqua" {{ ($settings['warna_tema'] ?? '') === 'biru-aqua' ? 'selected' : '' }}>🌊 Biru Aqua</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Warna Base</label>
                        <input type="color" name="warna_base" class="form-control form-control-color @error('warna_base') is-invalid @enderror" value="{{ old('warna_base', $baseColor) }}">
                        @error('warna_base') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
