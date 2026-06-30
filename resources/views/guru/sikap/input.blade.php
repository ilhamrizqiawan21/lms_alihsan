@extends('layouts.app')

@section('title', 'Input Sikap — ' . $kelasMapel->mataPelajaran->nama_mapel)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-emoji-smile-fill me-2"></i> Input Nilai Sikap</h4>
    <div class="d-flex align-items-center gap-2 mt-2">
        <span class="badge bg-primary" style="font-size:0.8rem;">{{ $kelasMapel->mataPelajaran->nama_mapel }}</span>
        <span class="badge bg-secondary" style="font-size:0.8rem;">{{ $kelasMapel->kelas->nama_kelas }}</span>
        <span class="badge bg-info" style="font-size:0.8rem;">
            @if($tahunAjaran) TA {{ $tahunAjaran->tahun }} @else — @endif
            • Semester {{ $semester }}
        </span>
    </div>
</div>

<form action="{{ route('guru.sikap.store', $kelasMapel) }}" method="POST">
    @csrf
    <input type="hidden" name="semester" value="{{ $semester }}">

    {{-- Sikap Spiritual (KI-1) --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-star-fill me-2"></i> Sikap Spiritual (KI-1)</span>
            <span class="text-muted" style="font-size:0.75rem;">Skala 1–5</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35px;">#</th>
                            <th style="min-width:140px;">Nama Siswa</th>
                            <th class="text-center" style="width:72px;">Taqwa</th>
                            <th class="text-center" style="width:72px;">Kejujuran</th>
                            <th class="text-center" style="width:72px;">Disiplin</th>
                            <th class="text-center" style="width:72px;">Sabar</th>
                            <th class="text-center" style="width:72px;">Syukur</th>
                            <th class="text-center" style="width:72px;">Tawadhu</th>
                            <th class="text-center" style="width:70px;">Rata²</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $spFields = ['taqwa','kejujuran','disiplin','sabar','syukur','tawadhu'];
                        @endphp
                        @forelse($siswa as $i => $s)
                        @php
                            $sp = $sikapSpiritual[$s->id] ?? null;
                            $spAvg = $sp ? round(array_sum(array_map(fn($f) => $sp->$f ?? 0, $spFields)) / count($spFields), 1) : null;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>{{ $s->user->nama_lengkap ?? $s->nis }}</td>
                            @foreach($spFields as $f)
                            <td>
                                <select name="spiritual[{{ $s->id }}][{{ $f }}]" class="form-select form-select-sm" style="min-width:62px;">
                                    <option value="">—</option>
                                    @for($v = 1; $v <= 5; $v++)
                                    <option value="{{ $v }}" {{ ($sp?->$f ?? null) == $v ? 'selected' : '' }}>{{ $v }}</option>
                                    @endfor
                                </select>
                            </td>
                            @endforeach
                            <td class="text-center">
                                @if($spAvg)
                                <strong style="font-size:0.85rem;color:{{ $spAvg >= 4 ? '#166534' : ($spAvg >= 3 ? '#92400e' : '#991b1b') }};">
                                    {{ $spAvg }}
                                </strong>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sikap Sosial (KI-2) --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-people-fill me-2"></i> Sikap Sosial (KI-2)</span>
            <span class="text-muted" style="font-size:0.75rem;">Skala 1–5</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size:0.82rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width:35px;">#</th>
                            <th style="min-width:140px;">Nama Siswa</th>
                            <th class="text-center" style="width:72px;">Empati</th>
                            <th class="text-center" style="width:72px;">Kerjasama</th>
                            <th class="text-center" style="width:72px;">Toleransi</th>
                            <th class="text-center" style="width:72px;">Percaya Diri</th>
                            <th class="text-center" style="width:72px;">Komunikasi</th>
                            <th class="text-center" style="width:70px;">Rata²</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $soFields = ['empati','kerjasama','toleransi','percaya_diri','komunikasi'];
                        @endphp
                        @forelse($siswa as $i => $s)
                        @php
                            $so = $sikapSosial[$s->id] ?? null;
                            $soAvg = $so ? round(array_sum(array_map(fn($f) => $so->$f ?? 0, $soFields)) / count($soFields), 1) : null;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $i + 1 }}</td>
                            <td>{{ $s->user->nama_lengkap ?? $s->nis }}</td>
                            @foreach($soFields as $f)
                            <td>
                                <select name="sosial[{{ $s->id }}][{{ $f }}]" class="form-select form-select-sm" style="min-width:62px;">
                                    <option value="">—</option>
                                    @for($v = 1; $v <= 5; $v++)
                                    <option value="{{ $v }}" {{ ($so?->$f ?? null) == $v ? 'selected' : '' }}>{{ $v }}</option>
                                    @endfor
                                </select>
                            </td>
                            @endforeach
                            <td class="text-center">
                                @if($soAvg)
                                <strong style="font-size:0.85rem;color:{{ $soAvg >= 4 ? '#166534' : ($soAvg >= 3 ? '#92400e' : '#991b1b') }};">
                                    {{ $soAvg }}
                                </strong>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada siswa.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('guru.sikap.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <span class="text-muted me-3" style="font-size:0.78rem;">{{ $siswa->count() }} siswa</span>
            <button type="submit" class="btn btn-success">
                <i class="bi bi-save me-1"></i> Simpan Semua
            </button>
        </div>
    </div>
</form>
@endsection
