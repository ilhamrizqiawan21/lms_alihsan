@extends('layouts.app')
@section('title', 'Kalender & Reminder')

@php
$bulanIndo = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$hariIndo = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
$today = date('Y-m-d');
@endphp

@section('content')
<div class="page-header"><h4><i class="bi bi-calendar3 me-2"></i> Kalender & Reminder</h4></div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ $bulanIndo[(int)$month] }} {{ $year }}</span>
                <div class="d-flex gap-2">
                    <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}" class="btn btn-sm btn-outline-secondary">&laquo; {{ $bulanIndo[$prevMonth->month] }}</a>
                    <a href="?year={{ date('Y') }}&month={{ date('m') }}" class="btn btn-sm btn-outline-primary">Hari Ini</a>
                    <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}" class="btn btn-sm btn-outline-secondary">{{ $bulanIndo[$nextMonth->month] }} &raquo;</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0" style="table-layout:fixed;">
                    <thead><tr class="text-center" style="background:var(--gray-100);">@foreach($hariIndo as $h)<th style="font-size:0.75rem;padding:8px 0;">{{ $h }}</th>@endforeach</tr></thead>
                    <tbody>
                        @php $d=1; $done=false; @endphp
                        @for($r=0; $r<6; $r++)
                        <tr>
                            @for($c=0; $c<7; $c++)
                            @php $idx=$r*7+$c; $cd=''; $ce=collect(); $it=false;
                            if($idx>=$startDayOfWeek && !$done && $d<=$daysInMonth){$cd=sprintf('%04d-%02d-%02d',$year,$month,$d);$ce=$events[$cd]??collect();$it=($cd===$today);$d++;}elseif($d>$daysInMonth){$done=true;} @endphp
                            <td style="height:75px;vertical-align:top;padding:3px;{{$it?'background:#dcfce7;':''}}{{$cd?'':'background:#f9fafb;'}}">
                                @if($cd)
                                <div style="font-size:0.72rem;font-weight:{{$it?'800':'500'}};color:{{$it?'#166534':'#6b7280'}};margin-bottom:2px;">{{(int)substr($cd,8,2)}}</div>
                                @foreach($ce as $ev)
                                <div class="p-1 rounded" style="font-size:0.62rem;line-height:1.1;background:{{$ev->is_holiday?'#fee2e2':'#dbeafe'}};color:{{$ev->is_holiday?'#991b1b':'#1e40af'}};{{$ev->is_done?'opacity:0.5;':''}}">{{$ev->is_holiday?'🔴':''}}{{\Illuminate\Support\Str::limit($ev->title,14)}}</div>
                                @endforeach
                                @endif
                            </td>
                            @endfor
                        </tr>
                        @if($done && $d>$daysInMonth) @break @endif
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul me-2"></i> Event {{ $bulanIndo[(int)$month] }}</div>
            <div class="card-body p-0">
                @if($monthEvents->count()>0)
                @foreach($monthEvents as $e)
                <div class="p-2 border-bottom">
                    <strong style="font-size:0.82rem;">{{$e->is_holiday?'🔴':''}}{{ $e->title }}</strong>
                    <div class="text-muted" style="font-size:0.7rem;">{{ $e->event_date->format('d M Y') }} — {{ $e->description ?: 'Tidak ada deskripsi' }}</div>
                </div>
                @endforeach
                @else<p class="text-muted text-center py-3">Tidak ada event.</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection
