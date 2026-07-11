@extends('layouts.app')
@section('title', 'Chat: ' . ($kelasMapel->mataPelajaran->nama_mapel ?? ''))

@section('content')
<div class="page-header">
    <h4><i class="bi bi-chat-dots-fill me-2"></i> {{ $kelasMapel->mataPelajaran?->nama_mapel }}</h4>
    <a href="{{ route('siswa.chat.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <div id="chatArea" style="height:400px;overflow-y:auto;border:1px solid var(--gray-200);border-radius:8px;padding:15px;margin-bottom:15px;background:#f9fafb;">
            @forelse($messages as $msg)
            <div class="mb-3 {{ $msg->user_id == auth()->id() ? 'text-end' : '' }}">
                <small class="text-muted d-block">{{ $msg->user->nama_lengkap ?? 'Unknown' }}</small>
                <div class="d-inline-block p-2 px-3 rounded-3 {{ $msg->user_id == auth()->id() ? 'bg-success text-white' : 'bg-white border' }}" style="max-width:75%;">
                    {{ $msg->message }}
                </div>
                <small class="text-muted d-block" style="font-size:0.65rem;">{{ $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('H:i') : '' }}</small>
            </div>
            @empty
            <p class="text-muted text-center pt-5">Belum ada pesan.</p>
            @endforelse
        </div>
        <form action="{{ route('siswa.chat.send', $kelasMapel) }}" method="POST" class="d-flex gap-2">
            @csrf
            <input type="text" name="message" class="form-control" placeholder="Ketik pesan..." required maxlength="1000">
            <button class="btn btn-success" type="submit" title="Kirim pesan" aria-label="Kirim pesan">
                <i class="bi bi-send-fill" aria-hidden="true"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
    var el=document.getElementById('chatArea');
    if(el) el.scrollTop=el.scrollHeight;
});
</script>
@endpush
