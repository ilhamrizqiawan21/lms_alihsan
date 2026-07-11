@extends('layouts.app')

@section('title', 'Absensi Wali Kelas')

@section('content')
<x-page-header title="Absensi Wali Kelas" icon="bi-clipboard-check-fill">
    <x-badge color="primary">{{ $waliKelas->kelas?->tingkat }} {{ $waliKelas->kelas?->nama_kelas }}</x-badge>
</x-page-header>

@php
    $siswaRows = collect($siswaList ?? []);
@endphp

<div class="row gy-4">
    <div class="col-12">
        <x-card title="Filter Bulan" icon="bi-funnel-fill">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <x-form.select name="bulan" label="Bulan" :selected="$bulan" wrapper-class="mb-0">
                        @foreach($bulanOptions as $value => $label)
                            <option value="{{ $value }}" @selected($bulan === $value)>{{ $label }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                <div class="col-md-3 d-grid">
                    <x-button type="submit" color="primary" icon="bi-search">Tampilkan</x-button>
                </div>
            </form>
        </x-card>
    </div>

    <div class="col-12">
        <form action="{{ route('guru.wali-kelas.absensi.store', $waliKelas) }}" method="POST">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <x-card title="Absensi Harian {{ date('F Y', strtotime($bulan . '-01')) }}" icon="bi-table" body-class="p-0">
                <div class="p-3 d-flex flex-wrap gap-2">
                    <span class="badge bg-success">H=Hadir</span>
                    <span class="badge bg-warning text-dark">S=Sakit</span>
                    <span class="badge bg-info text-dark">I=Izin</span>
                    <span class="badge bg-danger">A=Alpha</span>
                </div>
                <x-table-wrapper>
                    <table class="table table-bordered table-hover mb-0 wali-attendance-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:44px;">No</th>
                                <th style="min-width:90px;">NIS</th>
                                <th style="min-width:180px;">Nama</th>
                                @foreach($tanggalList as $tanggal)
                                    <th class="text-center" style="min-width:62px;">
                                        {{ $tanggal->format('d') }}<br><small class="text-muted">{{ $tanggal->translatedFormat('D') }}</small>
                                    </th>
                                @endforeach
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                @foreach($tanggalList as $tanggal)
                                    <td class="text-center p-1">
                                        <select class="form-select form-select-sm wali-attendance-select" onchange="fillWaliColumn(this, '{{ $tanggal->format('Y-m-d') }}')">
                                            <option value="">-</option>
                                            <option value="hadir">H</option>
                                            <option value="sakit">S</option>
                                            <option value="izin">I</option>
                                            <option value="alpha">A</option>
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @if($siswaRows->isNotEmpty())
                                @foreach($siswaRows as $i => $s)
                                    <tr>
                                        <td class="text-center text-muted align-middle">{{ $i + 1 }}</td>
                                        <td class="align-middle">{{ $s->nis }}</td>
                                        <td class="align-middle"><strong>{{ $s->user?->nama_lengkap ?? '-' }}</strong></td>
                                        @foreach($tanggalList as $tanggal)
                                            @php
                                                $key = $tanggal->format('Y-m-d');
                                                $st = $absensiData[$s->id][$key] ?? null;
                                            @endphp
                                            <td class="p-1 text-center align-middle">
                                                <select name="absensi[{{ $s->id }}][{{ $key }}]" class="form-select form-select-sm wali-attendance-select status-{{ $st }}">
                                                    <option value="">-</option>
                                                    <option value="hadir" @selected($st === 'hadir')>H</option>
                                                    <option value="sakit" @selected($st === 'sakit')>S</option>
                                                    <option value="izin" @selected($st === 'izin')>I</option>
                                                    <option value="alpha" @selected($st === 'alpha')>A</option>
                                                </select>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="{{ 3 + count($tanggalList) }}" class="text-center text-muted py-4">Tidak ada siswa aktif di kelas ini.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </x-table-wrapper>
                <x-slot:footer>
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                        <x-button :href="route('guru.wali-kelas.index')" color="outline-secondary" icon="bi-arrow-left">Kembali</x-button>
                        <x-button type="submit" color="success" icon="bi-save">Simpan Absensi</x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.wali-attendance-select { font-size:0.72rem; min-width:54px; padding:0.25rem 0.35rem; text-align:center; }
.wali-attendance-select.status-hadir { background:#dcfce7; color:#166534; }
.wali-attendance-select.status-sakit { background:#fef3c7; color:#92400e; }
.wali-attendance-select.status-izin { background:#dbeafe; color:#1e40af; }
.wali-attendance-select.status-alpha { background:#fee2e2; color:#991b1b; }
.wali-attendance-table th { vertical-align:middle; }
</style>
@endpush

@push('scripts')
<script>
function fillWaliColumn(select, tanggal) {
    var val = select.value;
    if (!val) return;
    var table = select.closest('table');
    table.querySelectorAll('tbody select[name$="[' + tanggal + ']"]').forEach(function(item) {
        item.value = val;
    });
    select.value = '';
}
</script>
@endpush
