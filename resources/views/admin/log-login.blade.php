@extends('layouts.app')
@section('title', 'Log Login')

@section('content')
<div class="page-header"><h4><i class="bi bi-clock-history me-2"></i> Riwayat Login</h4></div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-1"></i> Daftar Login</span>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari user..." value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:var(--primary-100);">
                    <tr>
                        <th>Waktu</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;">{{ $log->login_time ? \Carbon\Carbon::parse($log->login_time)->format('d M Y H:i:s') : '-' }}</td>
                        <td><strong>{{ $log->username }}</strong></td>
                        <td>{{ $log->nama_lengkap }}</td>
                        <td><span class="badge badge-{{ $log->role }}">{{ $log->role }}</span></td>
                        <td style="font-family:monospace;">{{ $log->ip_address }}</td>
                        <td class="text-muted" style="font-size:0.72rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->user_agent }}">{{ \Illuminate\Support\Str::limit($log->user_agent, 50) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data login</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
