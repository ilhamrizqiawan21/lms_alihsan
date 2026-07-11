@extends('layouts.app')

@section('title', 'Materi: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? '') . ' - ' . ($kelasMapel->kelas->nama_kelas ?? ''))

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-text-fill me-2"></i> Materi {{ $kelasMapel->mataPelajaran?->nama_mapel }} — {{ $kelasMapel->kelas?->nama_kelas }}</h4>
    <a href="{{ route('guru.materi.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <x-card title="Upload Materi" icon="bi-cloud-upload-fill">
                <form action="{{ route('guru.materi.store', $kelasMapel) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <x-form.input name="judul" label="Judul" required />
                    <x-form.textarea name="deskripsi" label="Deskripsi" rows="3" />
                    <x-form.file
                        name="file_materi"
                        label="File Materi"
                        accept=".jpg,.jpeg,.pdf,image/jpeg,application/pdf"
                        accept-label="JPG, JPEG, PDF"
                        max-size="5MB"
                        required
                    />
                    <x-button type="submit" color="success" size="" icon="bi-upload" class="w-100">Upload</x-button>
                </form>
        </x-card>
    </div>
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i> Daftar Materi</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Judul</th><th>Deskripsi</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @if(!blank($materi))
                            @foreach($materi as $m)
                        <tr>
                            <td><strong>{{ $m->judul }}</strong></td>
                            <td style="font-size:0.82rem;">{{ \Illuminate\Support\Str::limit($m->deskripsi, 60) }}</td>
                            <td style="white-space:nowrap;font-size:0.82rem;">{{ $m->created_at ? \Carbon\Carbon::parse($m->created_at)->format('d M Y') : '-' }}</td>
                            <td>
                                @if($m->file_path)
                                <a href="{{ route('guru.materi.download', [$kelasMapel, $m]) }}" class="btn btn-sm btn-outline-primary btn-icon" target="_blank" title="Download {{ $m->judul }}" aria-label="Download {{ $m->judul }}">
                                    <i class="bi bi-download" aria-hidden="true"></i>
                                </a>
                                @endif
                                <form action="{{ route('guru.materi.destroy', [$kelasMapel, $m]) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-icon" data-confirm="Hapus materi ini?" title="Hapus {{ $m->judul }}" aria-label="Hapus {{ $m->judul }}">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                            @endforeach
                        @else
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada materi</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
