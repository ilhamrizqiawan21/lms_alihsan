<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $schoolName = school_setting('school_name', 'Nama Sekolah');
        $schoolShortName = school_setting('school_short_name', 'LMS');
        $schoolMotto = school_setting('motto', 'Learning Management System');
        $schoolAddress = school_setting('address', 'Alamat sekolah belum diatur');
        $logoUrl = school_logo_url();
        $faviconUrl = school_favicon_url();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="application-name" content="{{ $schoolShortName }}">
    <title>Login — {{ $schoolShortName }} {{ $schoolName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @endif

    <style>
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pulse { 0%,100% { box-shadow:0 0 0 0 rgba(34,197,94,0.3); } 50% { box-shadow:0 0 0 10px rgba(34,197,94,0); } }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family:'Plus Jakarta Sans','Segoe UI',system-ui,sans-serif;
            background:linear-gradient(135deg, #166534 0%, #145c32 40%, #0d3625 100%);
            min-height:100vh; display:flex; align-items:center; justify-content:center;
            padding:20px;
        }
        body::before {
            content:''; position:fixed; top:-50%; left:-50%; width:200%; height:200%;
            background:radial-gradient(circle at 30% 20%, rgba(251,191,36,0.08) 0%, transparent 50%),
                       radial-gradient(circle at 70% 80%, rgba(34,197,94,0.12) 0%, transparent 50%);
            pointer-events:none; animation:pulse 4s ease-in-out infinite;
        }
        .login-wrapper { position:relative; width:100%; max-width:440px; animation:fadeInUp 0.5s ease; }
        .login-header { text-align:center; color:white; margin-bottom:20px; }
        .login-header .logo-circle {
            width:80px; height:80px; background:rgba(255,255,255,0.15);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            margin:0 auto 12px; font-size:2.2rem;
            border:3px solid rgba(251,191,36,0.5);
            box-shadow:0 0 30px rgba(251,191,36,0.15);
        }
        .login-header h3 { font-weight:800; font-size:1.4rem; margin-bottom:4px; }
        .login-header p { opacity:0.75; font-size:0.85rem; }
        .login-header .product-name {
            display:inline-flex; align-items:center; justify-content:center;
            padding:4px 10px; margin-bottom:8px;
            background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.18);
            border-radius:999px; color:rgba(255,255,255,0.9);
            font-weight:700; font-size:0.74rem; letter-spacing:0.02em;
        }
        .login-header .school-motto { max-width:360px; margin:0 auto 6px; color:rgba(255,255,255,0.78); font-size:0.84rem; line-height:1.45; }
        .login-header .school-address { max-width:360px; margin:0 auto; color:rgba(255,255,255,0.62); font-size:0.74rem; line-height:1.4; }
        .login-card {
            background:white; border-radius:18px; padding:35px 30px;
            box-shadow:0 20px 60px rgba(0,0,0,0.3);
        }
        .login-card .form-label { font-weight:600; font-size:0.84rem; color:#4b5563; margin-bottom:5px; }
        .login-card .input-group-text {
            background:#f9fafb; border:1px solid #e5e7eb; border-right:none;
            color:#9ca3af; border-radius:10px 0 0 10px;
        }
        .login-card .form-control {
            border:1px solid #e5e7eb; border-left:none; border-radius:0 10px 10px 0;
            padding:12px 16px; font-size:0.92rem; font-family:inherit;
            transition:all 0.2s ease;
        }
        .login-card .input-group:focus-within .input-group-text { border-color:#22c55e; color:#22c55e; }
        .login-card .form-control:focus { box-shadow:none; border-color:#22c55e; }
        .btn-login {
            width:100%; padding:14px; border-radius:12px; border:none;
            background:linear-gradient(135deg, #22c55e, #16a34a);
            color:white; font-weight:700; font-size:0.95rem;
            transition:all 0.3s ease; cursor:pointer;
            font-family:inherit; letter-spacing:0.02em;
        }
        .btn-login:hover {
            transform:translateY(-2px);
            box-shadow:0 8px 25px rgba(22,163,74,0.35);
            background:linear-gradient(135deg, #16a34a, #15803d);
        }
        .alert { border:none; border-radius:10px; font-size:0.85rem; padding:12px 16px; margin-bottom:20px; }
        .alert-danger { background:#fee2e2; color:#991b1b; }
        .alert-success { background:#dcfce7; color:#166534; }
        .form-check-input:checked { background-color:#22c55e; border-color:#22c55e; }
        .login-footer { text-align:center; margin-top:20px; color:rgba(255,255,255,0.6); font-size:0.78rem; }
        .login-footer span { color:rgba(251,191,36,0.8); }
        @media(max-width:480px) { .login-card { padding:25px 20px; } }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-header">
        <div class="logo-circle">
            <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" width="36" height="36" decoding="async" style="width:36px;height:36px;object-fit:contain;border-radius:50%;">
        </div>
        <h3>{{ $schoolName }}</h3>
        <div class="product-name">{{ $schoolShortName }}</div>
        <div class="school-motto">{{ $schoolMotto }}</div>
        <div class="school-address">{{ $schoolAddress }}</div>
    </div>

    <div class="login-card">
        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username atau Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" placeholder="Masukkan username atau email" required autofocus>
                </div>
                @error('username')<div class="text-danger" style="font-size:0.75rem;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Masukkan password" required>
                </div>
                @error('password')<div class="text-danger" style="font-size:0.75rem;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:0.85rem;color:#6b7280;">Ingat saya</label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
            </button>
        </form>
    </div>

    <div class="login-footer">
        &copy; {{ date('Y') }} {{ $schoolName }}<br>
        <span>{{ $schoolShortName }}</span>
    </div>
</div>
</body>
</html>
