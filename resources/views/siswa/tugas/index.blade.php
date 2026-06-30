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
                    @forelse($tugas as $t)
                    @php
                        $pengumpulan = $t->pengumpulan->where('siswa_id', auth()->user()->siswa?->id)->first();
                    @endphp
                    <tr>
                        <td>{{ $t->judul }}</td>
                        <td>{{ $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                        <td>{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            @if($pengumpulan)
                                @php
                                    $badge = match($pengumpulan->status) {
                                        'dikumpulkan' => 'primary',
                                        'dinilai' => 'success',
                                        'terlambat' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucwords($pengumpulan->status) }}</span>
                            @else
                                <span class="badge bg-warning">Belum Dikumpul</span>
                            @endif
                        </td>
                        <td>{{ $pengumpulan?->nilai ?? '-' }}</td>
                        <td>
                            @if($pengumpulan)
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail{{ $t->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            @else
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kumpul{{ $t->id }}">
                                    <i class="bi bi-upload"></i> Kumpul
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Modal Detail -->
                    @if($pengumpulan)
                    <div class="modal fade" id="detail{{ $t->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $t->judul }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Deskripsi:</strong> {{ $t->deskripsi ?? '-' }}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-{{ $pengumpulan->status == 'dinilai' ? 'success' : 'primary' }}">{{ $pengumpulan->status }}</span></p>
                                    <p><strong>Nilai:</strong> {{ $pengumpulan->nilai ?? 'Belum dinilai' }}</p>
                                    <p><strong>Tanggal Kumpul:</strong> {{ $pengumpulan->tanggal_kumpul ? \Carbon\Carbon::parse($pengumpulan->tanggal_kumpul)->format('d/m/Y H:i') : '-' }}</p>
                                    @if($pengumpulan->file_upload)
                                        <p><strong>File:</strong> <a href="{{ asset('storage/'.$pengumpulan->file_upload) }}" target="_blank">Download</a></p>
                                    @endif
                                    @if($pengumpulan->catatan)
                                        <p><strong>Catatan Guru:</strong> {{ $pengumpulan->catatan }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Modal Kumpul -->
                    <div class="modal fade" id="kumpul{{ $t->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('siswa.tugas.kumpul', $t->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Kumpul: {{ $t->judul }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">File Tugas</label>
                                            <input type="file" name="file_upload" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Jawaban Teks (opsional)</label>
                                            <textarea name="teks_jawaban" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Kumpulkan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada tugas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
