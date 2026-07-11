@extends('layouts.app')

@section('title', 'Pengumpulan Tugas')
@section('page_title', 'Pengumpulan Tugas')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-journal-fill me-1"></i>
            Pengumpulan: <strong>{{ $tugas->judul }}</strong>
            <small class="text-muted ms-2">(Deadline: {{ $tugas->batas_waktu ? \Carbon\Carbon::parse($tugas->batas_waktu)->format('d/m/Y H:i') : '-' }})</small>
        </span>
        <a href="{{ route('guru.tugas.list', $kelasMapel) }}" class="btn btn-sm btn-secondary">
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
                        <th>Jawaban</th>
                        <th>Nilai</th>
                        <th>Catatan</th>
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
                                    'sudah' => 'success',
                                    'dinilai' => 'primary',
                                    'terlambat' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ ucwords($p->status) }}</span>
                        </td>
                        <td>{{ $p->tanggal_kumpul ? \Carbon\Carbon::parse($p->tanggal_kumpul)->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @if($p->files && $p->files->count() > 0)
                                @foreach($p->files as $file)
                                    <a href="{{ route('guru.tugas.file.download', [$kelasMapel, $tugas, $file]) }}" class="btn btn-sm btn-outline-primary mb-1" target="_blank" title="{{ $file->file_name }}">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                @endforeach
                            @elseif($p->file_upload)
                                <a href="{{ route('guru.tugas.pengumpulan.download', [$kelasMapel, $tugas, $p]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-download"></i>
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->teks_jawaban)
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="{{ $p->teks_jawaban }}">
                                    <i class="bi bi-text-left"></i>
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('guru.tugas.nilai', [$kelasMapel, $tugas, $p]) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="input-group input-group-sm" style="width:130px">
                                    <input type="number" name="nilai" class="form-control form-control-sm"
                                           value="{{ $p->nilai }}" min="0" max="100" step="0.01" required>
                                    <input type="text" name="catatan" class="form-control form-control-sm" 
                                           placeholder="Catatan" value="{{ $p->catatan }}" style="display:none;">
                                    <button class="btn btn-success btn-sm" type="submit" title="Simpan nilai {{ $p->siswa?->user?->nama_lengkap ?? 'siswa' }}" aria-label="Simpan nilai {{ $p->siswa?->user?->nama_lengkap ?? 'siswa' }}">
                                        <i class="bi bi-check" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td>
                            @if($p->catatan)
                                <span class="text-muted small">{{ \Illuminate\Support\Str::limit($p->catatan, 30) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail{{ $p->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Detail Pengumpulan --}}
                    <div class="modal fade" id="detail{{ $p->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Pengumpulan - {{ $p->siswa?->user?->nama_lengkap ?? '-' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Status:</strong> <span class="badge bg-{{ $badge }}">{{ ucwords($p->status) }}</span></p>
                                    <p><strong>Tanggal Kumpul:</strong> {{ $p->tanggal_kumpul ? \Carbon\Carbon::parse($p->tanggal_kumpul)->format('d/m/Y H:i') : '-' }}</p>
                                    
                                    @if($p->files && $p->files->count() > 0)
                                    <p><strong>File:</strong></p>
                                    <ul>
                                        @foreach($p->files as $file)
                                        <li><a href="{{ route('guru.tugas.file.download', [$kelasMapel, $tugas, $file]) }}" target="_blank">{{ $file->file_name }}</a></li>
                                        @endforeach
                                    </ul>
                                    @elseif($p->file_upload)
                                    <p><strong>File:</strong> <a href="{{ route('guru.tugas.pengumpulan.download', [$kelasMapel, $tugas, $p]) }}" target="_blank">Download</a></p>
                                    @endif

                                    @if($p->teks_jawaban)
                                    <p><strong>Jawaban Teks:</strong></p>
                                    <div class="p-2 bg-light rounded">{{ $p->teks_jawaban }}</div>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('guru.tugas.nilai', [$kelasMapel, $tugas, $p]) }}" method="POST" class="w-100">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="number" name="nilai" class="form-control" value="{{ $p->nilai }}" min="0" max="100" step="0.01" placeholder="Nilai" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="catatan" class="form-control" value="{{ $p->catatan }}" placeholder="Catatan (opsional)">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada pengumpulan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
