@extends('layouts.app')

@section('title', 'Tugas: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? '') . ' - ' . ($kelasMapel->kelas->nama_kelas ?? ''))

@section('content')
<div class="page-header">
    <h4><i class="bi bi-journal-fill me-2"></i> Tugas {{ $kelasMapel->mataPelajaran?->nama_mapel }} — {{ $kelasMapel->kelas?->nama_kelas }}</h4>
    <a href="{{ route('guru.tugas.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i> Buat Tugas Baru</div>
            <div class="card-body">
                <form action="{{ route('guru.tugas.store', $kelasMapel) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="datetime-local" name="batas_waktu" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-save me-1"></i> Simpan Tugas</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i> Daftar Tugas <small class="text-muted">({{ $totalSiswa ?? 0 }} siswa)</small></div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Judul</th><th>Deadline</th><th>Kumpul</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($tugas as $t)
                        <tr>
                            <td><strong>{{ $t->judul }}</strong></td>
                            <td style="white-space:nowrap;font-size:0.82rem;">{{ $t->batas_waktu ? \Carbon\Carbon::parse($t->batas_waktu)->format('d M Y H:i') : '-' }}</td>
                            <td>{{ $t->sudah_mengumpulkan ?? 0 }}/{{ $totalSiswa ?? 0 }}</td>
                            <td>
                                <a href="{{ route('guru.tugas.pengumpulan', [$kelasMapel, $t]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Nilai</a>
                                <form action="{{ route('guru.tugas.destroy', $t) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada tugas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
