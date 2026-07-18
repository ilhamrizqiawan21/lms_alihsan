@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Dashboard Siswa')

@section('content')
<x-page-header
    title="Dashboard Siswa"
    icon="bi-speedometer2"
    subtitle="Ringkasan tugas, materi, dan kabar kelas terbaru."
/>

<div class="stats-grid">
    <x-stat-card label="Total Tugas" :value="$totalTugas ?? 0" icon="bi-journal-fill" />
    <x-stat-card label="Tugas Selesai" :value="$tugasSelesai ?? 0" icon="bi-check-circle-fill" />
    <x-stat-card label="Belum Dikerjakan" :value="$tugasBelum ?? 0" icon="bi-exclamation-circle-fill" />
    <x-stat-card label="Total Materi" :value="$totalMateri ?? 0" icon="bi-file-earmark-text-fill" />
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-journal-fill me-1"></i> Tugas Terbaru</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tugas</th>
                                <th>Mapel</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank(($tugasTerbaru ?? [])))
                                @foreach(($tugasTerbaru ?? []) as $t)
                            <tr>
                                <td>{{ $t->judul }}</td>
                                <td>{{ $t->kelasMapel?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                                <td>{{ $t->batas_waktu ? \Carbon\Carbon::parse($t->batas_waktu)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @php
                                        $sudahKumpul = $t->pengumpulan->where('siswa_id', auth()->user()->siswa?->id)->first();
                                    @endphp
                                    @if($sudahKumpul)
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-warning">Belum</span>
                                    @endif
                                </td>
                            </tr>
                                @endforeach
                            @else
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada tugas</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell-fill me-1"></i> Notifikasi</span>
                @if($notifikasi->where('is_read', false)->count() > 0)
                <a href="{{ route('siswa.notifikasi.index') }}" class="text-decoration-none small" style="color: var(--primary-600);">Lihat Semua</a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @if(!blank(($notifikasi ?? [])))
                                @foreach(($notifikasi ?? []) as $n)
                            <tr style="{{ $n->is_read ? '' : 'background: #fef2f2;' }}">
                                <td style="width: 40px; text-align: center;">
                                    @php
                                        $iconMap = [
                                            'tugas_baru' => ['icon' => 'journal-plus', 'color' => '#3b82f6'],
                                            'nilai_baru' => ['icon' => 'bar-chart-fill', 'color' => '#22c55e'],
                                            'chat_baru' => ['icon' => 'chat-dots-fill', 'color' => '#8b5cf6'],
                                            'komentar_tugas' => ['icon' => 'chat-square-text-fill', 'color' => '#f59e0b'],
                                            'kumpul_tugas' => ['icon' => 'check-circle-fill', 'color' => '#06b6d4'],
                                            'absensi' => ['icon' => 'clipboard-check-fill', 'color' => '#ef4444'],
                                        ];
                                        $icon = $iconMap[$n->tipe] ?? ['icon' => 'bell-fill', 'color' => '#6b7280'];
                                    @endphp
                                    <i class="bi bi-{{ $icon['icon'] }}" style="color: {{ $icon['color'] }};"></i>
                                </td>
                                <td>
                                    <strong style="font-size: 0.82rem;">{{ $n->judul }}</strong>
                                    @if(!$n->is_read) <span class="badge bg-danger" style="font-size: 0.55rem;">Baru</span> @endif
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($n->pesan, 60) }}</div>
                                </td>
                                <td class="text-end">
                                    <small class="text-muted">{{ $n->created_at ? \Carbon\Carbon::parse($n->created_at)->diffForHumans() : '' }}</small>
                                </td>
                            </tr>
                                @endforeach
                            @else
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada notifikasi</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone-fill me-1"></i> Pengumuman</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank(($pengumuman ?? [])))
                                @foreach(($pengumuman ?? []) as $p)
                            <tr>
                                <td>{{ $p->judul }}</td>
                                <td>{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') : '-' }}</td>
                            </tr>
                                @endforeach
                            @else
                            <tr><td colspan="2" class="text-center text-muted py-3">Tidak ada pengumuman</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
