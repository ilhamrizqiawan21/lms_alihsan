@extends('layouts.app')

@section('title', 'Kalender & Reminder')

@php
    $bulanIndo = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $hariIndo = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
    $today = date('Y-m-d');
@endphp

@section('content')
<div class="page-header">
    <h4><i class="bi bi-calendar3 me-2"></i> Kalender & Monitoring Event Sekolah</h4>
</div>

<div class="row">
    {{-- Kalender Bulanan --}}
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar3 me-2"></i> {{ $bulanIndo[(int)$month] }} {{ $year }}</span>
                <div class="d-flex gap-2">
                    <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}" class="btn btn-sm btn-outline-secondary">&laquo; {{ $bulanIndo[$prevMonth->month] }}</a>
                    <a href="?year={{ date('Y') }}&month={{ date('m') }}" class="btn btn-sm btn-outline-success">Hari Ini</a>
                    <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}" class="btn btn-sm btn-outline-secondary">{{ $bulanIndo[$nextMonth->month] }} &raquo;</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" style="table-layout:fixed;">
                    <thead>
                        <tr class="text-center" style="background:var(--gray-100);">
                            @foreach($hariIndo as $hari)
                            <th style="font-size:0.75rem;padding:8px 0;">{{ $hari }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $day = 1; $done = false; @endphp
                        @for($row = 0; $row < 6; $row++)
                        <tr>
                            @for($col = 0; $col < 7; $col++)
                                @php
                                    $idx = $row * 7 + $col;
                                    $cellDate = '';
                                    $cellEvents = collect();
                                    $isToday = false;
                                    if ($idx >= $startDayOfWeek && !$done && $day <= $daysInMonth) {
                                        $cellDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                        $cellEvents = $events[$cellDate] ?? collect();
                                        $isToday = ($cellDate === $today);
                                        $day++;
                                    } elseif ($day > $daysInMonth) {
                                        $done = true;
                                    }
                                @endphp
                                <td style="height:80px;vertical-align:top;padding:4px;
                                    {{ $isToday ? 'background:#dcfce7;' : '' }}
                                    {{ !$cellDate && !$isToday ? 'background:#f9fafb;' : '' }}
                                    {{ in_array($col, [0,6]) && !$isToday ? 'background:#f5f5f5;' : '' }}">
                                    @if($cellDate)
                                    <div style="font-size:0.75rem;font-weight:{{ $isToday ? '800' : '500' }};color:{{ $isToday ? '#166534' : '#6b7280' }};margin-bottom:4px;">
                                        {{ (int)substr($cellDate, 8, 2) }}
                                    </div>
                                    @foreach($cellEvents as $ev)
                                    <div class="mb-1 p-1 rounded" style="font-size:0.68rem;line-height:1.2;cursor:pointer;
                                        background:{{ $ev->is_holiday ? '#fee2e2' : '#dbeafe' }};
                                        color:{{ $ev->is_holiday ? '#991b1b' : '#1e40af' }};
                                        {{ $ev->is_done ? 'opacity:0.5;text-decoration:line-through;' : '' }}"
                                        data-bs-toggle="modal" data-bs-target="#eventModal{{ $ev->id }}"
                                        title="{{ $ev->title }}">
                                        @if($ev->is_holiday)🔴 @endif{{ \Illuminate\Support\Str::limit($ev->title, 18) }}
                                    </div>
                                    @endforeach
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @if($done && $day > $daysInMonth) @break @endif
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Tambah Event Sekolah --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-plus-circle me-2"></i> Tambah Event Sekolah</div>
            <div class="card-body">
                <form action="{{ route('kepsek.kalender.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="scope" value="school">
                    <div class="mb-2">
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="Judul event" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="event_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Deskripsi (opsional)"></textarea>
                    </div>
                    <div class="mb-2 d-flex gap-3 align-items-center">
                        <div class="form-check">
                            <input type="checkbox" name="is_holiday" value="1" class="form-check-input" id="isHoliday">
                            <label class="form-check-label" for="isHoliday" style="font-size:0.82rem;">Libur</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_done" value="1" class="form-check-input" id="isDone">
                            <label class="form-check-label" for="isDone" style="font-size:0.82rem;">Selesai</label>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-success w-100"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>

        {{-- Daftar Event Bulan Ini --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i> Event {{ $bulanIndo[(int)$month] }} {{ $year }}</span>
                <span class="badge bg-secondary" style="font-size:0.7rem;">{{ $monthEvents->count() }} event</span>
            </div>
            <div class="card-body p-0">
                @if($monthEvents->count() > 0)
                @foreach($monthEvents as $e)
                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                    <div style="cursor:pointer;flex:1;" data-bs-toggle="modal" data-bs-target="#eventModal{{ $e->id }}">
                        <div style="font-size:0.82rem;{{ $e->is_done ? 'text-decoration:line-through;opacity:0.5;' : '' }}">
                            @if($e->is_holiday)🔴 @else 📅 @endif
                            <strong>{{ $e->title }}</strong>
                        </div>
                        <div class="text-muted" style="font-size:0.7rem;">
                            {{ $e->event_date->format('d M Y') }}
                            @if($e->description)
                            — {{ \Illuminate\Support\Str::limit($e->description, 30) }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-1 align-items-center">
                        @if($e->is_done)
                        <span class="badge bg-success" style="font-size:0.6rem;">✅</span>
                        @else
                        <form action="{{ route('kepsek.kalender.toggle-done', $e) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm p-0" style="font-size:1rem;line-height:1;" title="Tandai selesai">✅</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-muted text-center py-3" style="font-size:0.85rem;">Tidak ada event bulan ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit/Hapus Modals --}}
@foreach($monthEvents as $e)
<div class="modal fade" id="eventModal{{ $e->id }}" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">
            @if($e->is_holiday)🔴 @endif {{ $e->title }}
            @if($e->is_done)<span class="badge bg-success ms-2">Selesai</span>@endif
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="{{ route('kepsek.kalender.update', $e) }}" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
            <div class="mb-2">
                <label class="form-label" style="font-size:0.82rem;">Judul</label>
                <input type="text" name="title" class="form-control form-control-sm" value="{{ $e->title }}" required>
            </div>
            <div class="mb-2">
                <label class="form-label" style="font-size:0.82rem;">Tanggal</label>
                <input type="date" name="event_date" class="form-control form-control-sm" value="{{ $e->event_date->format('Y-m-d') }}" required>
            </div>
            <div class="mb-2">
                <label class="form-label" style="font-size:0.82rem;">Deskripsi</label>
                <textarea name="description" class="form-control form-control-sm" rows="2">{{ $e->description }}</textarea>
            </div>
            <div class="mb-2">
                <label class="form-label" style="font-size:0.82rem;">Dibuat oleh</label>
                <div style="font-size:0.82rem;color:var(--gray-500);">{{ $e->user?->nama_lengkap ?? '—' }}</div>
            </div>
            <div class="d-flex gap-3 mb-2">
                <div class="form-check">
                    <input type="checkbox" name="is_holiday" value="1" class="form-check-input" id="eh{{ $e->id }}" {{ $e->is_holiday ? 'checked' : '' }}>
                    <label class="form-check-label" for="eh{{ $e->id }}" style="font-size:0.82rem;">Libur</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_done" value="1" class="form-check-input" id="ed{{ $e->id }}" {{ $e->is_done ? 'checked' : '' }}>
                    <label class="form-check-label" for="ed{{ $e->id }}" style="font-size:0.82rem;">Selesai</label>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
            <form action="{{ route('kepsek.kalender.destroy', $e) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus event ini?"><i class="bi bi-trash"></i> Hapus</button>
            </form>
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
        </div>
    </form>
    </div></div>
</div>
@endforeach
@endsection
