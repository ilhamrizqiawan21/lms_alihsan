@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h4><i class="bi bi-bell-fill me-2"></i> Notifikasi</h4>
        <p class="text-muted">Daftar notifikasi Anda</p>
    </div>
    @if($unreadCount > 0)
    <form action="{{ route('siswa.notifikasi.mark-all-read') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm" style="background: var(--primary-50); color: var(--primary-700); border: 1px solid var(--primary-200);">
            <i class="bi bi-check-all me-1"></i> Tandai Semua Sudah Dibaca
        </button>
    </form>
    @endif
</div>

<div class="card">
    <div class="card-body p-0">
        @if($notifikasi->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Waktu</th>
                        <th style="width: 80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifikasi as $n)
                    <tr class="{{ $n->is_read ? '' : 'table-active' }}" style="{{ $n->is_read ? '' : 'font-weight: 600;' }}">
                        <td class="text-center">
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
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; background: {{ $icon['color'] }}15; color: {{ $icon['color'] }}; font-size: 1rem;">
                                <i class="bi bi-{{ $icon['icon'] }}"></i>
                            </span>
                        </td>
                        <td>
                            <div>{{ $n->judul }}</div>
                            @if(!$n->is_read)
                            <span class="badge bg-danger" style="font-size: 0.6rem;">Baru</span>
                            @endif
                        </td>
                        <td style="max-width: 300px;">
                            <span class="text-muted" style="font-size: 0.82rem;">{{ Str::limit($n->pesan, 80) }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $n->created_at ? \Carbon\Carbon::parse($n->created_at)->diffForHumans() : '-' }}</small>
                        </td>
                        <td>
                            @if($n->link)
                            <a href="{{ route('siswa.notifikasi.mark-read', $n) }}" class="btn btn-sm" style="background: var(--primary-50); color: var(--primary-700);" title="Lihat">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            @else
                            <form action="{{ route('siswa.notifikasi.mark-read', $n) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background: var(--primary-50); color: var(--primary-700);" title="Tandai dibaca">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $notifikasi->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-bell-slash" style="font-size: 3rem; color: var(--gray-300);"></i>
            <p class="text-muted mt-3 mb-0">Belum ada notifikasi.</p>
        </div>
        @endif
    </div>
</div>
@endsection
