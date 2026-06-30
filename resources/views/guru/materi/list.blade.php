@extends('layouts.app')

@section('title', 'Materi: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? '') . ' - ' . ($kelasMapel->kelas->nama_kelas ?? ''))

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-text-fill me-2"></i> Materi {{ $kelasMapel->mataPelajaran?->nama_mapel }} — {{ $kelasMapel->kelas?->nama_kelas }}</h4>
    <a href="{{ route('guru.materi.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-cloud-upload-fill me-2"></i> Upload Materi</div>
            <div class="card-body">
                <form action="{{ route('guru.materi.store', $kelasMapel) }}" method="POST" enctype="multipart/form-data">
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
                        <label class="form-label">File (PDF/DOC/ZIP, max 20MB)</label>
                        <input type="file" name="file_materi" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100"><i class="bi bi-upload me-1"></i> Upload</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i> Daftar Materi</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Judul</th><th>Deskripsi</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @forelse($materi as $m)
                        <tr>
                            <td><strong>{{ $m->judul }}</strong></td>
                            <td style="font-size:0.82rem;">{{ \Illuminate\Support\Str::limit($m->deskripsi, 60) }}</td>
                            <td style="white-space:nowrap;font-size:0.82rem;">{{ $m->created_at ? \Carbon\Carbon::parse($m->created_at)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($m->file_materi)
                                <a href="{{ asset('storage/'.$m->file_materi) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-download"></i></a>
                                @endif
                                <form action="{{ route('guru.materi.destroy', [$kelasMapel, $m]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada materi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
