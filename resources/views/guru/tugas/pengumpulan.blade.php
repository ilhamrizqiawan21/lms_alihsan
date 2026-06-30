@extends('layouts.app')

@section('title', 'Pengumpulan Tugas')
@section('page_title', 'Pengumpulan Tugas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-journal-fill me-1"></i>
            Pengumpulan: <strong>{{ $tugas->judul }}</strong>
            <small class="text-muted ms-2">(Deadline: {{ $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline)->format('d/m/Y H:i') : '-' }})</small>
        </span>
        <a href="{{ route('guru.tugas.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Status</th>
                        <th>Tanggal Kumpul</th>
                        <th>File</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengumpulan as $p)
                    <tr>
                        <td>{{ $p->siswa?->user?->nama_lengkap ?? '-' }}</td>
                        <td>
                            @php
                                $badge = match($p->status) {
                                    'dikumpulkan' => 'primary',
                                    'dinilai' => 'success',
                                    'terlambat' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucwords($p->status) }}</span>
                        </td>
                        <td>{{ $p->tanggal_kumpul ? \Carbon\Carbon::parse($p->tanggal_kumpul)->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @if($p->file_upload)
                                <a href="{{ asset('storage/'.$p->file_upload) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-download"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('guru.tugas.nilai', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm" style="width:120px">
                                    <input type="number" name="nilai" class="form-control form-control-sm"
                                           value="{{ $p->nilai }}" min="0" max="100" step="0.01" required>
                                    <button class="btn btn-success btn-sm" type="submit">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('guru.tugas.hapus-pengumpulan', $p->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada pengumpulan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
