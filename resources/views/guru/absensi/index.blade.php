@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<div class="page-header"><h4><i class="bi bi-clipboard-check-fill me-2"></i> Absensi</h4></div>

@if($kelasMapel->count() == 0)
<div class="card"><div class="card-body text-center text-muted py-5">Anda belum memiliki penugasan mengajar semester ini.</div></div>
@else
<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Kelas & Mata Pelajaran</label>
                <select name="kelas_mapel_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih --</option>
                    @foreach($kelasMapel as $km)
                    <option value="{{ $km->id }}" {{ request('kelas_mapel_id') == $km->id ? 'selected' : '' }}>
                        {{ $km->kelas?->nama_kelas }} - {{ $km->mataPelajaran?->nama_mapel }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ $bulan }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

@if($kelasMapelId && $kmData)
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i> {{ $kmData->kelas?->nama_kelas }} — {{ $kmData->mataPelajaran?->nama_mapel }} ({{ $bulanIndo[(int)$bulanNum] }} {{ $tahun }})</span>
        <span><span class="badge badge-hadir me-1">H=Hadir</span><span class="badge badge-sakit me-1">S=Sakit</span><span class="badge badge-izin me-1">I=Izin</span><span class="badge badge-alpha">A=Alpha</span></span>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('guru.absensi.store', $kmData) }}" method="POST">
            @csrf
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" style="font-size:0.75rem;">
                    <thead>
                        <tr style="background:var(--primary-700);color:white;">
                            <th style="width:30px;">No</th>
                            <th style="width:60px;">NIS</th>
                            <th style="min-width:150px;">Nama</th>
                            @for($w = 1; $w <= $mingguCount; $w++)
                            <th style="text-align:center;width:55px;">
                                Minggu {{ $w }}<br><small style="font-size:0.6rem;">{{ ($tanggalMinggu[$w] ?? null) ? date('d/m', strtotime($tanggalMinggu[$w])) : '-' }}</small>
                            </th>
                            @endfor
                            <th style="text-align:center;width:28px;background:#dcfce7;color:#166534;">H</th>
                            <th style="text-align:center;width:28px;background:#fef3c7;color:#92400e;">S</th>
                            <th style="text-align:center;width:28px;background:#dbeafe;color:#1e40af;">I</th>
                            <th style="text-align:center;width:28px;background:#fee2e2;color:#991b1b;">A</th>
                        </tr>
                        <tr style="background:var(--gray-100);">
                            <td colspan="3"></td>
                            @for($w = 1; $w <= $mingguCount; $w++)
                            <td class="text-center" style="padding:2px;">
                                <select onchange="fillColumn(this, {{ $w }})" style="font-size:0.6rem;width:100%;padding:1px;">
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
                        <tr>
                            <td class="text-center">{{ $i+1 }}</td>
                            <td>{{ $s->nis }}</td>
                            <td><strong>{{ $s->user->nama_lengkap ?? '-' }}</strong></td>
                            @php $h=0; $sa=0; $iz=0; $al=0; @endphp
                            @for($w = 1; $w <= $mingguCount; $w++)
                            @php
                                $tgl = $tanggalMinggu[$w] ?? null;
                                $st = $absensiData[$s->id][$tgl] ?? null;
                                if($st === 'hadir') $h++;
                                elseif($st === 'sakit') $sa++;
                                elseif($st === 'izin') $iz++;
                                elseif($st === 'alpha') $al++;
                            @endphp
                            <td class="text-center p-0">
                                @if($tgl)
                                <select name="absensi[{{ $s->id }}][{{ $w }}]" style="font-size:0.65rem;width:100%;border:none;padding:4px;text-align:center;
                                    background:{{ $st === 'hadir' ? '#dcfce7' : ($st === 'sakit' ? '#fef3c7' : ($st === 'izin' ? '#dbeafe' : ($st === 'alpha' ? '#fee2e2' : 'white'))) }};
                                    color:{{ $st === 'hadir' ? '#166534' : ($st === 'sakit' ? '#92400e' : ($st === 'izin' ? '#1e40af' : ($st === 'alpha' ? '#991b1b' : '#6b7280'))) }};">
                                    <option value="">-</option>
                                    <option value="hadir" {{ $st === 'hadir' ? 'selected' : '' }}>H</option>
                                    <option value="sakit" {{ $st === 'sakit' ? 'selected' : '' }}>S</option>
                                    <option value="izin" {{ $st === 'izin' ? 'selected' : '' }}>I</option>
                                    <option value="alpha" {{ $st === 'alpha' ? 'selected' : '' }}>A</option>
                                </select>
                                @else
                                <span style="color:#d1d5db;">-</span>
                                @endif
                            </td>
                            @endfor
                            <td class="text-center fw-bold" style="color:#16a34a;">{{ $h }}</td>
                            <td class="text-center" style="color:#d97706;">{{ $sa }}</td>
                            <td class="text-center" style="color:#3b82f6;">{{ $iz }}</td>
                            <td class="text-center fw-bold" style="color:#ef4444;">{{ $al }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 text-end">
                <button type="submit" class="btn btn-success" name="simpan_absensi" value="1"><i class="bi bi-save me-1"></i> Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>
@endif
@endif
@endsection

@push('scripts')
<script>
function fillColumn(select, minggu) {
    var val = select.value;
    if (!val) return;
    var table = select.closest('table');
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
        var cell = row.querySelectorAll('td')[2 + minggu]; // offset: no, nis, nama
        if (cell) {
            var sel = cell.querySelector('select');
            if (sel) sel.value = val;
        }
    });
    select.value = ''; // reset bulk selector
}
</script>
@endpush
