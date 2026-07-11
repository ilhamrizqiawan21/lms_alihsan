@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<x-page-header title="Absensi" icon="bi-clipboard-check-fill" />

<div class="row gy-4">
    <div class="col-12">
        <x-card title="Filter Absensi" icon="bi-funnel-fill">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <x-form.select
                        name="kelas_mapel_id"
                        label="Kelas & Mata Pelajaran"
                        placeholder="-- Pilih --"
                        :selected="$kelasMapelId"
                        wrapper-class="mb-0"
                    >
                        @foreach($kelasMapel as $km)
                            <option value="{{ $km->id }}" @selected(request('kelas_mapel_id') == $km->id)>
                                {{ $km->kelas?->nama_kelas }} - {{ $km->mataPelajaran?->nama_mapel }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>
                <div class="col-md-3">
                    <x-form.input
                        type="month"
                        name="bulan"
                        label="Bulan"
                        :value="$bulan"
                        use-old="false"
                        wrapper-class="mb-0"
                    />
                </div>
                <div class="col-md-3 d-grid">
                    <x-button type="submit" color="primary" icon="bi-search">Tampilkan</x-button>
                </div>
            </form>
        </x-card>
    </div>

    @if($kelasMapel->count() == 0)
        <div class="col-12">
            <x-card>
                <x-empty-state title="Belum ada penugasan mengajar" icon="bi-clipboard-check" message="Anda belum memiliki penugasan mengajar semester ini." />
            </x-card>
        </div>
    @elseif($kelasMapelId && $kmData)
        <div class="col-12">
            <form action="{{ route('guru.absensi.store', $kmData) }}" method="POST" x-data="{ submitting: false }" @submit.prevent="if(!submitting) { submitting = true; $el.submit(); }">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">

                <x-card
                    title="Absensi {{ $kmData->kelas?->nama_kelas }} — {{ $kmData->mataPelajaran?->nama_mapel }}"
                    icon="bi-table"
                    body-class="p-0"
                    x-bind:class="{ 'opacity-50': submitting }"
                >
                    <div class="p-3 attendance-legend d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge bg-success">H=Hadir</span>
                        <span class="badge bg-warning text-dark">S=Sakit</span>
                        <span class="badge bg-info text-dark">I=Izin</span>
                        <span class="badge bg-danger">A=Alpha</span>
                    </div>

                    <x-table-wrapper>
                        <table class="table table-bordered table-hover mb-0 attendance-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:44px;">No</th>
                                    <th class="text-center" style="width:70px;">NIS</th>
                                    <th>Nama</th>
                                    @for($w = 1; $w <= $mingguCount; $w++)
                                        <th class="text-center" style="min-width:72px;">Minggu {{ $w }}<br><small class="text-muted">{{ ($tanggalMinggu[$w] ?? null) ? date('d/m', strtotime($tanggalMinggu[$w])) : '-' }}</small></th>
                                    @endfor
                                    <th class="text-center" style="width:42px;">H</th>
                                    <th class="text-center" style="width:42px;">S</th>
                                    <th class="text-center" style="width:42px;">I</th>
                                    <th class="text-center" style="width:42px;">A</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    @for($w = 1; $w <= $mingguCount; $w++)
                                        <td class="text-center py-2">
                                            <select class="form-select form-select-sm attendance-select" onchange="fillColumn(this, {{ $w }})">
                                                <option value="">-</option>
                                                <option value="hadir">H</option>
                                                <option value="sakit">S</option>
                                                <option value="izin">I</option>
                                                <option value="alpha">A</option>
                                            </select>
                                        </td>
                                    @endfor
                                    <td colspan="4"></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $i => $s)
                                    @php $h = 0; $sa = 0; $iz = 0; $al = 0; @endphp
                                    <tr>
                                        <td class="text-center text-muted align-middle">{{ $i + 1 }}</td>
                                        <td class="align-middle">{{ $s->nis }}</td>
                                        <td class="align-middle"><strong>{{ $s->user->nama_lengkap ?? '-' }}</strong></td>
                                        @for($w = 1; $w <= $mingguCount; $w++)
                                            @php
                                                $tgl = $tanggalMinggu[$w] ?? null;
                                                $st = $absensiData[$s->id][$tgl] ?? null;
                                                if ($st === 'hadir') $h++;
                                                elseif ($st === 'sakit') $sa++;
                                                elseif ($st === 'izin') $iz++;
                                                elseif ($st === 'alpha') $al++;
                                            @endphp
                                            <td class="p-0 text-center align-middle">
                                                @if($tgl)
                                                    <select name="absensi[{{ $s->id }}][{{ $w }}]" class="form-select form-select-sm attendance-select attendance-{{ $st }}">
                                                        <option value="">-</option>
                                                        <option value="hadir" @selected($st === 'hadir')>H</option>
                                                        <option value="sakit" @selected($st === 'sakit')>S</option>
                                                        <option value="izin" @selected($st === 'izin')>I</option>
                                                        <option value="alpha" @selected($st === 'alpha')>A</option>
                                                    </select>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="text-center align-middle text-success fw-bold">{{ $h }}</td>
                                        <td class="text-center align-middle text-warning">{{ $sa }}</td>
                                        <td class="text-center align-middle text-info">{{ $iz }}</td>
                                        <td class="text-center align-middle text-danger fw-bold">{{ $al }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-table-wrapper>

                    <x-slot:footer>
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                            <x-button :href="route('guru.absensi.index')" color="outline-secondary" icon="bi-arrow-left">Reset</x-button>
                            <x-button type="submit" color="success" icon="bi-save" x-bind:disabled="submitting">
                                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Absensi'">Simpan Absensi</span>
                            </x-button>
                        </div>
                    </x-slot:footer>
                </x-card>
            </form>
        </div>
    @else
        <div class="col-12">
            <x-card>
                <x-empty-state title="Pilih filter absensi" icon="bi-info-circle" message="Pilih kelas dan bulan untuk menampilkan data absensi." />
            </x-card>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function fillColumn(select, minggu) {
    var val = select.value;
    if (!val) return;
    var table = select.closest('table');
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
        var cell = row.querySelectorAll('td')[2 + minggu];
        if (cell) {
            var sel = cell.querySelector('select');
            if (sel) sel.value = val;
        }
    });
    select.value = '';
}
</script>
@endpush

@push('styles')
<style>
.attendance-select {
    font-size:0.72rem;
    min-width:70px;
    padding:0.35rem 0.5rem;
    text-align:center;
}
.attendance-select.hadir { background:#dcfce7; color:#166534; }
.attendance-select.sakit { background:#fef3c7; color:#92400e; }
.attendance-select.izin { background:#dbeafe; color:#1e40af; }
.attendance-select.alpha { background:#fee2e2; color:#991b1b; }
.attendance-legend .badge { font-size:0.78rem; }
.attendance-table th { vertical-align: middle; }
.opacity-50 { opacity: 0.5; pointer-events: none; }
</style>
@endpush
