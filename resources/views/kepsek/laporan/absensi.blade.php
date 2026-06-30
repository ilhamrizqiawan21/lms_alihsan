@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-clipboard-data-fill me-2"></i> Laporan Absensi</h4>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-funnel me-1"></i> Filter
    </div>
    <div class="card-body">
        <form class="row g-2" method="GET">
            <div class="col-md-3">
                <select name="kelas_mapel_id" class="form-select form-select-sm">
                    <option value="">Semua Kelas & Mapel</option>
                    @foreach($kelasMapel as $km)
                        <option value="{{ $km->id }}" {{ request('kelas_mapel_id') == $km->id ? 'selected' : '' }}>
                            {{ $km->kelas->nama_kelas ?? '—' }} — {{ $km->mataPelajaran->nama_mapel ?? '—' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_awal" class="form-control form-control-sm" placeholder="Tgl awal" value="{{ request('tanggal_awal') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal_akhir" class="form-control form-control-sm" placeholder="Tgl akhir" value="{{ request('tanggal_akhir') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('kepsek.laporan.absensi') }}" class="btn btn-sm btn-outline-secondary w-100" title="Reset">↻</a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $i => $a)
                    <tr>
                        <td class="text-center">{{ $absensi->firstItem() + $i }}</td>
                        <td>{{ $a->siswa->user->nama_lengkap ?? $a->siswa->nis ?? '—' }}</td>
                        <td>{{ $a->kelasMapel->kelas->nama_kelas ?? '—' }}</td>
                        <td>{{ $a->kelasMapel->mataPelajaran->nama_mapel ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                        <td>
                            @php
                                $badge = match($a->status) {
                                    'hadir' => 'bg-success',
                                    'sakit' => 'bg-warning text-dark',
                                    'izin' => 'bg-info text-dark',
                                    'alpha' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($a->status) }}</span>
                        </td>
                        <td>{{ $a->keterangan ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($absensi->hasPages())
    <div class="card-footer">
        {{ $absensi->links() }}
    </div>
    @endif
</div>
@endsection
