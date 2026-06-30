@extends('layouts.app')
@section('title', 'Chat Kelas')
@section('page_title', 'Chat: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? '') . ' - ' . ($kelasMapel->kelas->nama_kelas ?? ''))
@section('content')
<div class="card">
    <div class="card-header"><i class="bi bi-chat-dots-fill me-1"></i> Chat Room</div>
    <div class="card-body">
        <div class="chat-area" style="height:400px; overflow-y:auto; border:1px solid #eee; border-radius:8px; padding:15px; margin-bottom:15px; background:#f9fafb;">
            @if(isset($messages) && count($messages) > 0)
                @foreach($messages as $msg)
                <div class="mb-2 {{ $msg->user_id == auth()->id() ? 'text-end' : '' }}">
                    <small class="text-muted">{{ $msg->user->nama_lengkap ?? 'Unknown' }}</small>
                    <div class="d-inline-block p-2 rounded-3 {{ $msg->user_id == auth()->id() ? 'bg-success text-white' : 'bg-light border' }}" style="max-width:75%;">
                        {{ $msg->message }}
                    </div>
                    <small class="text-muted d-block">{{ $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('H:i') : '' }}</small>
                </div>
                @endforeach
            @else
                <p class="text-muted text-center pt-5">Belum ada pesan. Mulai percakapan!</p>
            @endif
        </div>
        <form action="{{ route('guru.chat.send', $kelasMapel) }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="text" name="message" class="form-control" placeholder="Ketik pesan..." required maxlength="1000">
            <button type="submit" class="btn btn-success"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
</div>
@endsection
