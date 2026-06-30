@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</h4>
    <nav class="breadcrumb">
        <span class="breadcrumb-item active">Dashboard</span>
    </nav>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-number">{{ $statistik['total_siswa'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-person-workspace"></i></div>
        <div>
            <div class="stat-label">Total Guru</div>
            <div class="stat-number">{{ $statistik['total_guru'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-building"></i></div>
        <div>
            <div class="stat-label">Total Kelas</div>
            <div class="stat-number">{{ $statistik['total_kelas'] ?? 0 }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
        <div>
            <div class="stat-label">Mata Pelajaran</div>
            <div class="stat-number">{{ $statistik['total_mapel'] ?? 0 }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i> Login Terbaru</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Nama</th><th>Role</th><th>Waktu</th><th>IP</th></tr></thead>
                        <tbody>
                            @forelse($loginTerbaru ?? [] as $log)
                            <tr>
                                <td><strong>{{ $log->nama_lengkap }}</strong></td>
                                <td><span class="badge badge-{{ $log->role }}">{{ $log->role }}</span></td>
                                <td class="text-muted small">{{ $log->login_time ? \Carbon\Carbon::parse($log->login_time)->diffForHumans() : '-' }}</td>
                                <td class="text-muted small">{{ $log->ip_address }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data login</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-megaphone-fill me-2"></i> Pengumuman Terbaru</div>
            <div class="card-body p-0">
                @forelse($pengumuman ?? [] as $p)
                <div style="border-left:4px solid var(--primary-500); padding:0.8rem 1rem; border-bottom:1px solid var(--gray-200);">
                    <strong>{{ $p->judul }}</strong>
                    <div class="text-muted small mt-1">
                        {{ $p->created_at ? \Carbon\Carbon::parse($p->created_at)->format('d M Y') : '-' }}
                        &mdash; {{ $p->creator ? $p->creator->nama_lengkap : 'Admin' }}
                    </div>
                    <div class="mt-1" style="font-size:0.85rem;">{{ \Illuminate\Support\Str::limit($p->isi, 120) }}</div>
                </div>
                @empty
                <div class="text-center text-muted py-4">Belum ada pengumuman</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
