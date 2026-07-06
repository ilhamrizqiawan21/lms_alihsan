@extends('layouts.app')

@section('title', 'Input Nilai — ' . $kelasMapel->mataPelajaran->nama_mapel)

@section('content')
<x-page-header title="Input Nilai" icon="bi-pencil-square">
    <x-badge color="primary">{{ $kelasMapel->mataPelajaran->nama_mapel }}</x-badge>
    <x-badge color="secondary">{{ $kelasMapel->kelas->nama_kelas }}</x-badge>
    <x-badge color="info">
        @if($tahunAjaran) TA {{ $tahunAjaran->tahun }} @else — @endif
        &middot; Semester {{ $semester }}
    </x-badge>
</x-page-header>

<form action="{{ route('guru.nilai.store', $kelasMapel) }}" method="POST">
    @csrf
    <input type="hidden" name="semester" value="{{ $semester }}">

    <x-card title="Input Nilai Kurikulum Merdeka" icon="bi-table" body-class="p-0">
        <x-slot:actions>
            <x-button type="submit" color="success" icon="bi-save">Simpan Semua</x-button>
        </x-slot:actions>

        <x-table-wrapper>
            <table class="table table-bordered table-hover app-table mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center w-row-number">#</th>
                        <th class="min-w-nis">NIS</th>
                        <th class="min-w-student">Nama Siswa</th>
                        <th colspan="4" class="text-center bg-soft-success">Sumatif Harian</th>
                        <th class="text-center bg-soft-warning">STS</th>
                        <th class="text-center bg-soft-warning">SAS</th>
                        <th class="text-center bg-soft-danger">SAT</th>
                        <th class="text-center bg-soft-muted">Rata-rata Akhir</th>
                    </tr>
                    <tr class="table-light">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-center w-score">SUM1</th>
                        <th class="text-center w-score">SUM2</th>
                        <th class="text-center w-score">SUM3</th>
                        <th class="text-center w-score">SUM4</th>
                        <th class="text-center w-score">Nilai</th>
                        <th class="text-center w-score">Nilai</th>
                        <th class="text-center w-score">Nilai</th>
                        <th class="text-center w-score-total">Auto</th>
                    </tr>
                </thead>
                <tbody>
                    @php($fields = ['sum1','sum2','sum3','sum4','sts','sas','sat'])

                    @forelse($siswa as $i => $s)
                        @php
                            $nilai = $nilaiList[$s->id] ?? null;
                            $rataAkhir = $nilai?->rata_akhir;
                            $scoreClass = match (true) {
                                $rataAkhir >= 92 => 'excellent',
                                $rataAkhir >= 83 => 'good',
                                $rataAkhir >= 75 => 'fair',
                                default => 'low',
                            };
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td><code>{{ $s->nis }}</code></td>
                            <td>{{ $s->user->nama_lengkap ?? $s->nis }}</td>
                            @foreach($fields as $f)
                                <td class="text-center">
                                    <input
                                        type="number"
                                        name="nilai[{{ $s->id }}][{{ $f }}]"
                                        class="form-control form-control-sm score-input {{ $rataAkhir && $f === 'sat' ? 'border-danger border-opacity-25' : '' }}"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        value="{{ $nilai?->$f }}"
                                        placeholder="—"
                                    >
                                </td>
                            @endforeach
                            <td class="text-center">
                                @if($rataAkhir)
                                    <strong class="score-result {{ $scoreClass }}">{{ number_format($rataAkhir, 1) }}</strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12">
                                <x-empty-state title="Tidak ada siswa di kelas ini." icon="bi-people" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-wrapper>

        <x-slot:footer>
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <x-button :href="route('guru.nilai.index')" color="outline-secondary" icon="bi-arrow-left">Kembali</x-button>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted text-xs">{{ $siswa->count() }} siswa</span>
                    <x-button type="submit" color="success" icon="bi-save">Simpan Semua</x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-card>
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
