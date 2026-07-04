@extends('layouts.app')
@section('title', 'IP Diblokir')

@section('content')
<div class="page-header"><h4><i class="bi bi-shield-fill-x me-2"></i> IP Diblokir</h4></div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-ul me-1"></i> Daftar IP yang Diblokir</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.82rem;">
                <thead style="background:var(--primary-100);">
                    <tr><th>IP Address</th><th>Diblokir Sampai</th><th>Alasan</th><th>Waktu Blokir</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($ips as $ip)
                    <tr>
                        <td style="font-family:monospace;"><strong>{{ $ip->ip_address }}</strong></td>
                        <td>
                            @if($ip->blocked_until && $ip->blocked_until->isPast())
                                <span class="badge bg-secondary">Kedaluwarsa</span>
                            @else
                                <span class="badge bg-danger">{{ $ip->blocked_until ? $ip->blocked_until->format('d M Y H:i') : '-' }}</span>
                            @endif
                        </td>
                        <td>{{ $ip->reason }}</td>
                        <td class="text-muted">{{ $ip->created_at ? $ip->created_at->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <form action="{{ route('admin.blocked-ips.unblock', $ip) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-success" data-confirm="Unblock IP {{ $ip->ip_address }}?">
                                    <i class="bi bi-unlock-fill"></i> Unblock
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada IP yang diblokir</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">{{ $ips->links() }}</div>
    </div>
</div>
@endsection
