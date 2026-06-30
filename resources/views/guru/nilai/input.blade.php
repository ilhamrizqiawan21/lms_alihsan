@extends('layouts.app')

@section('title', 'Input Nilai — ' . $kelasMapel->mataPelajaran->nama_mapel)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-pencil-square me-2"></i> Input Nilai</h4>
    <div class="d-flex align-items-center gap-2 mt-2">
        <span class="badge bg-primary" style="font-size:0.8rem;">{{ $kelasMapel->mataPelajaran->nama_mapel }}</span>
        <span class="badge bg-secondary" style="font-size:0.8rem;">{{ $kelasMapel->kelas->nama_kelas }}</span>
        <span class="badge bg-info" style="font-size:0.8rem;">
            @if($tahunAjaran) TA {{ $tahunAjaran->tahun }} @else — @endif
            • Semester {{ $semester }}
        </span>
    </div>
</div>

<form action="{{ route('guru.nilai.store', $kelasMapel) }}" method="POST">
    @csrf
    <input type="hidden" name="semester" value="{{ $semester }}">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-table me-2"></i> Input Nilai Kurikulum Merdeka</span>
            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-save me-1"></i> Simpan Semua</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35px;">#</th>
                            <th style="min-width:120px;">NIS</th>
                            <th style="min-width:160px;">Nama Siswa</th>
                            <th colspan="4" class="text-center bg-success bg-opacity-10">Sumatif Harian</th>
                            <th class="text-center" style="background:#fef3c7;">STS</th>
                            <th class="text-center" style="background:#fef3c7;">SAS</th>
                            <th class="text-center" style="background:#fee2e2;">SAT</th>
                            <th class="text-center" style="background:var(--gray-100);">Rata² Akhir</th>
                        </tr>
                        <tr class="table-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-center" style="width:65px;">SUM1</th>
                            <th class="text-center" style="width:65px;">SUM2</th>
                            <th class="text-center" style="width:65px;">SUM3</th>
                            <th class="text-center" style="width:65px;">SUM4</th>
                            <th class="text-center" style="width:65px;">Nilai</th>
                            <th class="text-center" style="width:65px;">Nilai</th>
                            <th class="text-center" style="width:65px;">Nilai</th>
                            <th class="text-center" style="width:80px;">Auto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fields = ['sum1','sum2','sum3','sum4','sts','sas','sat'];
                        @endphp
                        @forelse($siswa as $i => $s)
                        @php
                            $nilai = $nilaiList[$s->id] ?? null;
                            $rataAkhir = $nilai?->rata_akhir;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td><code>{{ $s->nis }}</code></td>
                            <td>{{ $s->user->nama_lengkap ?? $s->nis }}</td>
                            @foreach($fields as $f)
                            <td>
                                <input type="number"
                                    name="nilai[{{ $s->id }}][{{ $f }}]"
                                    class="form-control form-control-sm text-center {{ $rataAkhir && $f === 'sat' ? 'border-danger border-opacity-25' : '' }}"
                                    style="width:65px;display:inline;padding:4px;"
                                    min="0" max="100" step="0.01"
                                    value="{{ $nilai?->$f }}"
                                    placeholder="—">
                            </td>
                            @endforeach
                            <td class="text-center">
                                @if($rataAkhir)
                                <strong style="font-size:0.85rem;
                                    color:{{ $rataAkhir >= 92 ? '#166534' : ($rataAkhir >= 83 ? '#1e40af' : ($rataAkhir >= 75 ? '#92400e' : '#991b1b')) }};">
                                    {{ number_format($rataAkhir, 1) }}
                                </strong>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="12" class="text-center text-muted py-4">Tidak ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('guru.nilai.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div>
                <span class="text-muted me-3" style="font-size:0.78rem;">{{ $siswa->count() }} siswa</span>
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-save me-1"></i> Simpan Semua
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Auto-tab: pindah ke input berikutnya setelah ketik 3 digit
    document.querySelectorAll('input[type="number"]').forEach(function(input, idx, inputs) {
        input.addEventListener('keyup', function(e) {
            if (this.value.length >= 3) {
                var next = inputs[idx + 1];
                if (next) next.focus();
            }
        });
        // Pilih semua teks saat fokus
        input.addEventListener('focus', function() {
            this.select();
        });
    });
</script>
@endpush
