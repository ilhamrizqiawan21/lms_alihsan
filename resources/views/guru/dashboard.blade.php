@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i> Dashboard Guru</h4>
    <p class="text-muted">Selamat datang, {{ auth()->user()->nama_lengkap }}</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-diagram-3-fill"></i></div>
        <div><div class="stat-label">Kelas & Mapel</div><div class="stat-number">{{ $statistik['total_kelas_mapel'] ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div><div class="stat-label">Total Siswa Diajar</div><div class="stat-number">{{ $statistik['total_siswa'] ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
        <div><div class="stat-label">Total Materi</div><div class="stat-number">{{ $statistik['total_materi'] ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-journal-fill"></i></div>
        <div><div class="stat-label">Total Tugas</div><div class="stat-number">{{ $statistik['total_tugas'] ?? 0 }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
        <div><div class="stat-label">Belum Mengumpulkan</div><div class="stat-number">{{ collect($tugasBelumDikumpulkan ?? [])->sum('belum') }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-pencil-square"></i></div>
        <div><div class="stat-label">Perlu Dinilai</div><div class="stat-number">{{ collect($tugasPerluDinilai ?? [])->sum('total') }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-person-exclamation"></i></div>
        <div><div class="stat-label">Kehadiran Rendah</div><div class="stat-number">{{ collect($siswaJarangMasuk ?? [])->count() }}</div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-exclamation-circle me-2"></i> Belum Mengumpulkan</div>
            <div class="card-body p-0">
                @forelse(($tugasBelumDikumpulkan ?? []) as $item)
                    <a href="{{ $item['url'] }}" class="d-flex justify-content-between gap-3 p-3 border-bottom text-decoration-none" style="color:var(--text-body);">
                        <span class="d-flex flex-column">
                            <strong style="font-size:0.86rem;color:var(--text-strong);">{{ $item['judul'] }}</strong>
                            <span class="text-muted" style="font-size:0.76rem;">{{ $item['kelas'] }} - {{ $item['mata_pelajaran'] }}</span>
                            <small class="text-muted">Deadline {{ $item['batas_waktu'] }}</small>
                        </span>
                        <span class="badge bg-warning align-self-center">{{ $item['belum'] }}/{{ $item['total_siswa'] }}</span>
                    </a>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Tidak ada tunggakan tugas.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-exclamation me-2"></i> Siswa Perlu Perhatian</div>
            <div class="card-body p-0">
                @forelse(($siswaJarangMasuk ?? []) as $item)
                    <a href="{{ $item['url'] }}" class="d-flex justify-content-between gap-3 p-3 border-bottom text-decoration-none" style="color:var(--text-body);">
                        <span class="d-flex flex-column">
                            <strong style="font-size:0.86rem;color:var(--text-strong);">{{ $item['nama'] }}</strong>
                            <span class="text-muted" style="font-size:0.76rem;">{{ $item['kelas'] }} - NIS {{ $item['nis'] }}</span>
                            <small class="text-muted">{{ $item['total_absensi'] }} catatan absensi, {{ $item['total_alpha'] }} alpha</small>
                        </span>
                        <span class="badge bg-{{ $item['persen_hadir'] < 60 ? 'danger' : 'warning' }} align-self-center">{{ $item['persen_hadir'] }}%</span>
                    </a>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Kehadiran siswa masih aman.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i> Perlu Dinilai</div>
            <div class="card-body p-0">
                @forelse(($tugasPerluDinilai ?? []) as $item)
                    <a href="{{ $item['url'] }}" class="d-flex justify-content-between gap-3 p-3 border-bottom text-decoration-none" style="color:var(--text-body);">
                        <span class="d-flex flex-column">
                            <strong style="font-size:0.86rem;color:var(--text-strong);">{{ $item['judul'] }}</strong>
                            <span class="text-muted" style="font-size:0.76rem;">{{ $item['kelas'] }} - {{ $item['mata_pelajaran'] }}</span>
                            <small class="text-muted">Sudah masuk, belum dinilai</small>
                        </span>
                        <span class="badge bg-info align-self-center">{{ $item['total'] }}</span>
                    </a>
                @empty
                    <p class="text-muted text-center py-4 mb-0">Tidak ada antrean nilai.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-book me-2"></i> Kelas & Mapel Diampu</div>
            <div class="card-body p-0">
                @if($kelasMapel->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Kelas</th><th>Mata Pelajaran</th><th>Semester</th></tr></thead>
                        <tbody>
                            @foreach($kelasMapel as $km)
                            <tr>
                                <td><strong>{{ $km->kelas?->nama_kelas ?? '-' }}</strong></td>
                                <td>{{ $km->mataPelajaran?->nama_mapel ?? '-' }}</td>
                                <td>{{ $km->semester == '1' ? 'Ganjil' : 'Genap' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center py-4">Belum ada penugasan mengajar semester ini.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell-fill me-2"></i> Notifikasi</span>
                @if(($unreadNotifCount ?? 0) > 0)
                <a href="{{ route('guru.notifikasi.index') }}" class="text-decoration-none small" style="color: var(--primary-600);">Lihat Semua</a>
                @endif
            </div>
            <div class="card-body">
                @if(!blank(($notifikasi ?? [])))
                    @foreach(($notifikasi ?? []) as $n)
                <div style="border-left:3px solid {{ $n->is_read ? '#d1d5db' : '#ef4444' }};padding:0.5rem 0.75rem;margin-bottom:0.5rem;background:{{ $n->is_read ? '#f9fafb' : '#fef2f2' }};border-radius:0 6px 6px 0;">
                    <strong style="font-size:0.85rem;">{{ $n->judul }}</strong>
                    <div class="text-muted" style="font-size:0.7rem;">{{ $n->pesan }}</div>
                    <small class="text-muted" style="font-size:0.65rem;">{{ $n->created_at ? \Carbon\Carbon::parse($n->created_at)->diffForHumans() : '' }}</small>
                </div>
                    @endforeach
                @else
                <p class="text-muted text-center py-3">Belum ada notifikasi.</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone me-2"></i> Pengumuman</div>
            <div class="card-body">
                @if(!blank($pengumuman))
                    @foreach($pengumuman as $p)
                <div style="border-left:3px solid var(--primary-500);padding:0.5rem 0.75rem;margin-bottom:0.5rem;background:#f9fafb;border-radius:0 6px 6px 0;">
                    <strong style="font-size:0.85rem;">{{ $p->judul }}</strong>
                    <div class="text-muted" style="font-size:0.7rem;">{{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y') : '' }}</div>
                </div>
                    @endforeach
                @else
                <p class="text-muted text-center py-3">Belum ada pengumuman.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
