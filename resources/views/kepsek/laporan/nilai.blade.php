@extends('layouts.app')

@section('title', 'Laporan Nilai')
@section('page_title', 'Laporan Nilai')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="bi bi-bar-chart-fill me-1"></i> Laporan Nilai Akhir
    </div>
    <div class="card-body">
        <form class="row g-3 mb-3" method="GET">
            <div class="col-md-3">
                <select name="kelas_id" class="form-select form-select-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="mapel_id" class="form-select form-select-sm">
                    <option value="">Semua Mapel</option>
                    @foreach($mapel as $m)
                        <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="semester" class="form-select form-select-sm">
                    <option value="">Semua Semester</option>
                    <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                    <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Sum 1</th>
                        <th>Sum 2</th>
                        <th>Sum 3</th>
                        <th>Sum 4</th>
                        <th>Nilai Harian</th>
                        <th>STS</th>
                        <th>SAS</th>
                        <th>SAT</th>
                        <th>Rata Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!blank($nilai))
                        @foreach($nilai as $n)
                    <tr>
                        <td>{{ $n->siswa?->user?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $n->siswa?->kelas?->nama_kelas ?? '-' }}</td>
                        <td>{{ $n->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                        <td>{{ $n->sum1 ?? '-' }}</td>
                        <td>{{ $n->sum2 ?? '-' }}</td>
                        <td>{{ $n->sum3 ?? '-' }}</td>
                        <td>{{ $n->sum4 ?? '-' }}</td>
                        <td>{{ $n->nilai_harian ?? '-' }}</td>
                        <td>{{ $n->sts ?? '-' }}</td>
                        <td>{{ $n->sas ?? '-' }}</td>
                        <td>{{ $n->sat ?? '-' }}</td>
                        <td><strong>{{ $n->rata_akhir ?? '-' }}</strong></td>
                    </tr>
                        @endforeach
                    @else
                    <tr><td colspan="12" class="text-center text-muted py-3">Tidak ada data</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if($nilai->hasPages())
    <div class="card-footer">
        {{ $nilai->links() }}
    </div>
    @endif
</div>
@endsection
