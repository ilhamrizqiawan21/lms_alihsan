@extends('layouts.app')

@section('title', 'Detail Wali Kelas')

@section('content')
<x-page-header title="Detail Wali Kelas" icon="bi-person-badge-fill">
    <x-button :href="route('kepsek.laporan.wali-kelas')" color="outline-secondary" icon="bi-arrow-left">Kembali</x-button>
</x-page-header>

@php
    $siswaRows = collect($siswaList ?? []);
    $pertemuanRows = collect($pertemuan ?? []);
    $penangananRows = collect($penanganan ?? []);
@endphp

<div class="row gy-4">
    <div class="col-12">
        <x-card title="{{ $waliKelas->kelas?->tingkat }} {{ $waliKelas->kelas?->nama_kelas }} - {{ $waliKelas->guru?->nama_lengkap }}" icon="bi-info-circle">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <x-form.select name="bulan" label="Bulan Absensi" :selected="$bulan" wrapper-class="mb-0">
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
        <x-card title="Rekap Absensi Bulanan" icon="bi-clipboard-data" body-class="p-0">
            <x-table-wrapper>
                <table class="table table-bordered table-hover mb-0 wali-report-table">
                    <thead>
                        <tr>
                            <th style="min-width:90px;">NIS</th>
                            <th style="min-width:180px;">Nama</th>
                            @foreach($tanggalList as $tanggal)
                                <th class="text-center" style="min-width:48px;">{{ $tanggal->format('d') }}</th>
                            @endforeach
                            <th class="text-center">H</th>
                            <th class="text-center">S</th>
                            <th class="text-center">I</th>
                            <th class="text-center">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($siswaRows->isNotEmpty())
                            @foreach($siswaRows as $s)
                                @php
                                    $counts = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0];
                                @endphp
                                <tr>
                                    <td>{{ $s->nis }}</td>
                                    <td><strong>{{ $s->user?->nama_lengkap ?? '-' }}</strong></td>
                                    @foreach($tanggalList as $tanggal)
                                        @php
                                            $status = $absensiData[$s->id][$tanggal->format('Y-m-d')] ?? null;
                                            if ($status) $counts[$status]++;
                                            $label = ['hadir' => 'H', 'sakit' => 'S', 'izin' => 'I', 'alpha' => 'A'][$status] ?? '-';
                                        @endphp
                                        <td class="text-center status-{{ $status }}">{{ $label }}</td>
                                    @endforeach
                                    <td class="text-center text-success fw-bold">{{ $counts['hadir'] }}</td>
                                    <td class="text-center text-warning">{{ $counts['sakit'] }}</td>
                                    <td class="text-center text-info">{{ $counts['izin'] }}</td>
                                    <td class="text-center text-danger fw-bold">{{ $counts['alpha'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="{{ 6 + count($tanggalList) }}" class="text-center text-muted py-4">Tidak ada siswa aktif.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-table-wrapper>
        </x-card>
    </div>

    <div class="col-lg-6">
        <x-card title="Pertemuan Terbaru" icon="bi-calendar-event" body-class="p-0">
            <x-table-wrapper>
                <table class="table table-hover mb-0">
                    <thead><tr><th>Tanggal</th><th>Topik</th><th>Hasil</th></tr></thead>
                    <tbody>
                        @if($pertemuanRows->isNotEmpty())
                            @foreach($pertemuanRows as $p)
                                <tr><td>{{ $p->tanggal?->format('d/m/Y') }}</td><td>{{ $p->topik }}</td><td>{{ $p->hasil }}</td></tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada pertemuan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-table-wrapper>
        </x-card>
    </div>

    <div class="col-lg-6">
        <x-card title="Penanganan Siswa" icon="bi-heart-pulse" body-class="p-0">
            <x-table-wrapper>
                <table class="table table-hover mb-0">
                    <thead><tr><th>Siswa</th><th>Kondisi</th><th>Status</th></tr></thead>
                    <tbody>
                        @if($penangananRows->isNotEmpty())
                            @foreach($penangananRows as $p)
                                <tr>
                                    <td>{{ $p->siswa?->user?->nama_lengkap ?? '-' }}<div class="small text-muted">{{ $p->siswa?->nis }}</div></td>
                                    <td>{{ $p->kondisi }}<div class="small text-muted">{{ $p->tindak_lanjut }}</div></td>
                                    <td><span class="badge bg-{{ $p->status === 'selesai' ? 'success' : ($p->status === 'proses' ? 'warning text-dark' : 'danger') }}">{{ ucfirst($p->status) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada penanganan siswa.</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-table-wrapper>
        </x-card>
    </div>
</div>
@endsection

@push('styles')
<style>
.wali-report-table td.status-hadir { background:#dcfce7; color:#166534; }
.wali-report-table td.status-sakit { background:#fef3c7; color:#92400e; }
.wali-report-table td.status-izin { background:#dbeafe; color:#1e40af; }
.wali-report-table td.status-alpha { background:#fee2e2; color:#991b1b; }
</style>
@endpush
