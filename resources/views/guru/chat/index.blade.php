@extends('layouts.app')
@section('title', 'Chat Kelas')

@section('content')
<div class="page-header"><h4><i class="bi bi-chat-dots-fill me-2"></i> Chat Kelas</h4></div>

@if($kelasMapel->count() == 0)
<div class="card"><div class="card-body text-center text-muted py-5">Anda belum memiliki penugasan.</div></div>
@else
<div class="card">
    <div class="card-header"><i class="bi bi-book me-2"></i> Pilih Kelas</div>
    <div class="card-body">
        <div style="max-width:560px;">
            <label for="chat-room-select" class="form-label">Kelas Chat</label>
            <select id="chat-room-select" class="form-select" onchange="if (this.value) window.location.href = this.value;">
                <option value="" selected disabled>-- Pilih Kelas --</option>
            @foreach($kelasMapel as $km)
                <option value="{{ route('guru.chat.show', $km) }}">
                    {{ $km->mataPelajaran?->nama_mapel ?? '-' }} - {{ $km->kelas?->nama_kelas ?? '-' }}
                </option>
            @endforeach
            </select>
        </div>
    </div>
</div>
@endif
@endsection
