@extends('layouts.app')
@section('title', 'Pengaturan Sekolah')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-buildings-fill me-2"></i> Pengaturan Sekolah</h4>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Data belum bisa disimpan.</div>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@include('admin.school-settings._form', ['setting' => $setting])
@endsection
