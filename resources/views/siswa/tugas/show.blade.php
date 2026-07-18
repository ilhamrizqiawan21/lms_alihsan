@extends('layouts.app')
@section('title', 'Detail Tugas')
@section('page_title', 'Detail Tugas')

@section('content')
<div class="row">
    <div class="col-md-8">
        {{-- Detail Tugas --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-fill me-1"></i> {{ $tugas->judul }}</span>
                <span class="badge bg-secondary">{{ $tugas->kategori_nilai ?? 'NH' }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Mata Pelajaran</small>
                        <p class="fw-bold mb-0">{{ $tugas->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Batas Waktu</small>
                        <p class="fw-bold mb-0 {{ $tugas->batas_waktu && now()->gt($tugas->batas_waktu) ? 'text-danger' : '' }}">
                            {{ $tugas->batas_waktu ? \Carbon\Carbon::parse($tugas->batas_waktu)->format('d M Y') : '-' }}
                            @if($tugas->batas_waktu && now()->gt($tugas->batas_waktu))
                                <span class="badge bg-danger ms-1">Terlambat</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted">Deskripsi</small>
                    <p class="mb-0">{{ $tugas->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                </div>
            </div>
        </div>

        {{-- Riwayat Pengumpulan --}}
        @if($pengumpulan)
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-clock-history me-1"></i> Riwayat Pengumpulan
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <small class="text-muted">Status</small>
                        @php
                            $badge = match($pengumpulan->status) {
                                'sudah' => 'success',
                                'dinilai' => 'primary',
                                'terlambat' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <p class="fw-bold"><span class="badge bg-{{ $badge }}">{{ ucwords($pengumpulan->status) }}</span></p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Tanggal Kumpul</small>
                        <p class="fw-bold">{{ $pengumpulan->tanggal_kumpul ? \Carbon\Carbon::parse($pengumpulan->tanggal_kumpul)->format('d M Y H:i') : '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Nilai</small>
                        <p class="fw-bold {{ $pengumpulan->nilai ? 'text-success' : 'text-muted' }}">
                            {{ $pengumpulan->nilai ?? 'Belum dinilai' }}
                        </p>
                    </div>
                </div>

                @if($pengumpulan->files && $pengumpulan->files->count() > 0)
                <div class="mb-2">
                    <small class="text-muted">File yang diupload:</small>
                    <ul class="list-unstyled mb-0">
                        @foreach($pengumpulan->files as $file)
                        <li>
                            <a href="{{ route('siswa.tugas.file.download', [$tugas, $file]) }}" target="_blank" class="text-decoration-none">
                                <i class="bi bi-paperclip me-1"></i> {{ $file->file_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @elseif($pengumpulan->file_upload)
                <div class="mb-2">
                    <small class="text-muted">File yang diupload:</small>
                    <br>
                    <a href="{{ route('siswa.tugas.pengumpulan.download', [$tugas, $pengumpulan]) }}" target="_blank" class="text-decoration-none">
                        <i class="bi bi-paperclip me-1"></i> Download File
                    </a>
                </div>
                @endif

                @if($pengumpulan->teks_jawaban)
                <div class="mb-2">
                    <small class="text-muted">Jawaban Teks:</small>
                    <p class="mb-0 p-2 bg-light rounded">{{ $pengumpulan->teks_jawaban }}</p>
                </div>
                @endif

                @if($pengumpulan->catatan)
                <div class="mb-0">
                    <small class="text-muted">Catatan Guru:</small>
                    <p class="mb-0 p-2 bg-warning-subtle rounded">{{ $pengumpulan->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Form Upload --}}
        @if(!$pengumpulan || $pengumpulan->status === 'belum')
        <div class="card">
            <div class="card-header">
                <i class="bi bi-upload me-1"></i> Kumpulkan Tugas
            </div>
            <div class="card-body">
                <form action="{{ route('siswa.tugas.kumpul', $tugas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <x-form.file
                        name="file_upload"
                        label="Upload File"
                        accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf"
                        accept-label="PNG, JPG, JPEG, PDF"
                        max-size="5MB"
                        help="Opsional jika jawaban dikirim lewat teks."
                    />
                    <x-form.textarea
                        name="teks_jawaban"
                        label="Jawaban Teks"
                        rows="4"
                        placeholder="Tulis jawaban di sini jika tidak upload file..."
                        help="Opsional jika jawaban dikirim lewat file."
                    />
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('siswa.tugas.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Kumpulkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('siswa.tugas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        {{-- Info Tambahan --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i> Info</div>
            <div class="card-body">
                <small class="text-muted d-block">Guru Pengampu</small>
                <p class="fw-bold">{{ $tugas->kelasMapel?->guru?->nama_lengkap ?? '-' }}</p>

                <small class="text-muted d-block">Kelas</small>
                <p class="fw-bold">{{ $tugas->kelasMapel?->kelas?->tingkat ?? '-' }} {{ $tugas->kelasMapel?->kelas?->nama_kelas ?? '' }}</p>

                <small class="text-muted d-block">Kategori Nilai</small>
                <p class="fw-bold">{{ $tugas->kategori_nilai ?? 'NH' }}</p>

                <hr>
                
                @if($pengumpulan)
                <small class="text-muted d-block">Status Pengumpulan</small>
                <p class="fw-bold">
                    <span class="badge bg-{{ $badge ?? 'secondary' }}">{{ ucwords($pengumpulan->status) }}</span>
                </p>
                @else
                <div class="alert alert-info py-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i> Anda belum mengumpulkan tugas ini.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
