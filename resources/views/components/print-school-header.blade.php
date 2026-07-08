@php
    $printSchoolName = school_setting('school_name', 'Nama Sekolah');
    $printSchoolAddress = school_setting('address', 'Alamat sekolah belum diatur');
    $printPrincipalName = school_setting('principal_name', 'Nama Kepala Sekolah');
    $printPrincipalId = school_setting('principal_nip') ?: school_setting('principal_nuptk');
    $printDefaultSchoolYear = isset($taAktif) ? ($taAktif?->tahun ?? '-') : '-';
    $printSchoolYear = school_setting('school_year', $printDefaultSchoolYear);
    $printSemester = school_setting('semester', isset($semester) ? ($semester == '1' ? 'Ganjil' : 'Genap') : '-');
@endphp

@once
@push('styles')
<style>
    .print-school-header { display:none; }
    @media print {
        .topbar, .sidebar, .sidebar-overlay, .page-header, form, .btn, footer { display:none !important; }
        .main-content { margin:0 !important; }
        .page-content { padding:0 !important; }
        .card { border:0 !important; box-shadow:none !important; }
        .card-header { background:white !important; color:#111 !important; border:0 !important; text-align:center; font-weight:700; }
        .table-responsive { max-height:none !important; overflow:visible !important; }
        .print-school-header { display:block; margin-bottom:12px; color:#111; }
        .print-school-header .kop { display:flex; align-items:center; gap:16px; border-bottom:3px double #111; padding-bottom:10px; margin-bottom:10px; }
        .print-school-header img { width:58px; height:58px; object-fit:contain; }
        .print-school-header .identity { flex:1; text-align:center; }
        .print-school-header .school-name { font-size:18px; font-weight:800; text-transform:uppercase; }
        .print-school-header .school-address { font-size:11px; }
        .print-school-header .meta { font-size:11px; margin-bottom:8px; }
        .print-school-header .signature { width:240px; margin-left:auto; margin-top:24px; font-size:11px; }
        .print-school-header .signature-space { height:52px; }
    }
</style>
@endpush
@endonce

<div class="print-school-header">
    <div class="kop">
        <img src="{{ school_logo_url() }}" alt="Logo {{ $printSchoolName }}">
        <div class="identity">
            <div class="school-name">{{ $printSchoolName }}</div>
            <div class="school-address">{{ $printSchoolAddress }}</div>
        </div>
    </div>
    <div class="meta">
        Tahun Ajaran: {{ $printSchoolYear }} |
        Semester: {{ $printSemester }} |
        Kepala Sekolah: {{ $printPrincipalName }}
        @if($printPrincipalId)
            | NIP/NUPTK: {{ $printPrincipalId }}
        @endif
    </div>
</div>
