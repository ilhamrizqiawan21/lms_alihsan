@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
@php
    $activeTheme = $settings['warna_tema'] ?? 'hijau';
    $themeOptions = [
        'hijau' => [
            'label' => 'Hijau Sekolah',
            'note' => 'Rekomendasi untuk LMS: tenang, edukatif, dan mudah dibaca.',
            'primary' => '#198754',
            'sidebar' => '#166534',
            'accent' => '#0d6efd',
            'recommended' => true,
        ],
        'biru-azure' => [
            'label' => 'Biru Akademik',
            'note' => 'Formal dan bersih untuk sekolah yang ingin kesan institusional.',
            'primary' => '#0d6efd',
            'sidebar' => '#1d4ed8',
            'accent' => '#22c55e',
        ],
        'biru-aqua' => [
            'label' => 'Aqua Modern',
            'note' => 'Segar dan ringan, cocok untuk tampilan yang lebih modern.',
            'primary' => '#0891b2',
            'sidebar' => '#0e7490',
            'accent' => '#14b8a6',
        ],
        'indigo' => [
            'label' => 'Indigo Digital',
            'note' => 'Lebih tegas untuk LMS dengan nuansa teknologi.',
            'primary' => '#4f46e5',
            'sidebar' => '#3730a3',
            'accent' => '#06b6d4',
        ],
        'marun' => [
            'label' => 'Marun Prestasi',
            'note' => 'Hangat dan berwibawa untuk identitas sekolah yang kuat.',
            'primary' => '#be123c',
            'sidebar' => '#881337',
            'accent' => '#f59e0b',
        ],
    ];
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
                        <div class="theme-option-grid">
                            @foreach($themeOptions as $key => $theme)
                                <label class="theme-option {{ $activeTheme === $key ? 'is-active' : '' }}">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="warna_tema"
                                        value="{{ $key }}"
                                        {{ $activeTheme === $key ? 'checked' : '' }}
                                    >
                                    <span class="theme-option-preview" aria-hidden="true">
                                        <span class="theme-option-sidebar" style="background: {{ $theme['sidebar'] }}"></span>
                                        <span class="theme-option-screen">
                                            <span style="background: {{ $theme['primary'] }}"></span>
                                            <span style="background: {{ $theme['accent'] }}"></span>
                                        </span>
                                    </span>
                                    <span class="theme-option-body">
                                        <span class="theme-option-title">
                                            {{ $theme['label'] }}
                                            @if($theme['recommended'] ?? false)
                                                <span class="badge bg-soft-primary ms-1">Paling cocok</span>
                                            @endif
                                        </span>
                                        <span class="theme-option-note">{{ $theme['note'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
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

@push('styles')
<style>
    .theme-option-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 0.75rem;
    }

    .theme-option {
        display: grid;
        grid-template-columns: auto 58px 1fr;
        gap: 0.7rem;
        align-items: center;
        min-height: 94px;
        padding: 0.75rem;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-md);
        background: var(--surface-card);
        cursor: pointer;
        transition: var(--transition-default);
    }

    .theme-option:hover,
    .theme-option.is-active {
        border-color: var(--primary-500);
        box-shadow: var(--focus-ring);
    }

    .theme-option-preview {
        display: grid;
        grid-template-columns: 18px 1fr;
        width: 58px;
        height: 44px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-sm);
        background: var(--gray-50);
    }

    .theme-option-screen {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 7px;
    }

    .theme-option-screen span {
        display: block;
        height: 8px;
        border-radius: 99px;
    }

    .theme-option-body {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 0.2rem;
    }

    .theme-option-title {
        color: var(--gray-800);
        font-size: 0.85rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .theme-option-note {
        color: var(--gray-500);
        font-size: 0.72rem;
        line-height: 1.35;
    }
</style>
@endpush
