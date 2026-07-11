@extends('layouts.app')
@section('title', 'Log Error')

@section('content')
<div class="page-header"><h4><i class="bi bi-bug-fill me-2"></i> Log Error Sistem</h4></div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-1"></i> Daftar Error</span>
        <form class="d-flex gap-2" method="GET">
            <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Level</option>
                @foreach($levels as $lv)
                <option value="{{ $lv }}" {{ request('level') == $lv ? 'selected' : '' }}>{{ $lv }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.8rem;">
                <thead style="background:var(--primary-100);">
                    <tr><th style="width:70px;">Level</th><th style="width:130px;">Waktu</th><th>Message</th><th>File</th><th style="width:50px;">Line</th><th>URL</th></tr>
                </thead>
                <tbody>
                    @if(!blank($errors))
                        @foreach($errors as $e)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $e->error_level === 'EXCEPTION' ? 'danger' : ($e->error_level === 'WARNING' ? 'warning' : ($e->error_level === 'DEPRECATED' ? 'secondary' : 'info')) }}">{{ $e->error_level }}</span>
                        </td>
                        <td style="white-space:nowrap;">{{ $e->created_at ? \Carbon\Carbon::parse($e->created_at)->format('d/m H:i') : '-' }}</td>
                        <td><strong>{{ \Illuminate\Support\Str::limit($e->message, 100) }}</strong></td>
                        <td class="text-muted">{{ \Illuminate\Support\Str::limit($e->file, 40) }}</td>
                        <td class="text-center">{{ $e->line }}</td>
                        <td class="text-muted">{{ \Illuminate\Support\Str::limit($e->url, 40) }}</td>
                    </tr>
                        @endforeach
                    @else
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada error — sistem berjalan normal</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end p-3">{{ $errors->links() }}</div>
    </div>
</div>
@endsection
