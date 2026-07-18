@extends('layouts.app')

@section('title', 'Tugas Saya')
@section('page_title', 'Tugas Saya')

@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-journal-fill me-1"></i> Daftar Tugas</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Mapel</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!blank($tugas))
                        @foreach($tugas as $t)
                    @php
                        $pengumpulan = $t->pengumpulan->where('siswa_id', auth()->user()->siswa?->id)->first();
                    @endphp
                    <tr>
                        <td><a href="{{ route('siswa.tugas.show', $t->id) }}" class="text-decoration-none fw-bold">{{ $t->judul }}</a></td>
                        <td>{{ $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                        <td>{{ $t->batas_waktu ? \Carbon\Carbon::parse($t->batas_waktu)->format('d/m/Y') : '-' }}</td>
                        <td>
                            @if($pengumpulan)
                                @php
                                    $badge = match($pengumpulan->status) {
                                        'sudah' => 'success',
                                        'dinilai' => 'primary',
                                        'terlambat' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucwords($pengumpulan->status) }}</span>
                            @else
                                <span class="badge bg-warning text-dark">Belum Dikumpul</span>
                            @endif
                        </td>
                        <td>{{ $pengumpulan?->nilai ?? '-' }}</td>
                        <td>
                            <a href="{{ route('siswa.tugas.show', $t->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                        @endforeach
                    @else
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada tugas</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
