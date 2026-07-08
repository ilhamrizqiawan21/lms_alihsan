<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $schoolName = school_setting('school_name', 'Nama Sekolah');
        $schoolShortName = school_setting('school_short_name', 'LMS');
        $faviconUrl = school_favicon_url();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $schoolShortName }} {{ $schoolName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #198754 0%, #0d3625 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        .welcome-box h1 { font-size: 3rem; font-weight: 700; margin-bottom: 10px; }
        .welcome-box p { font-size: 1.2rem; opacity: 0.9; margin-bottom: 30px; }
        .btn-login {
            display: inline-block;
            padding: 15px 40px;
            background: white;
            color: #198754;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        small { opacity: 0.7; }
    </style>
</head>
<body>
    <div class="welcome-box">
        <h1>{{ $schoolShortName }}</h1>
        <p>Learning Management System<br>{{ $schoolName }}</p>
        <a href="{{ route('login') }}" class="btn-login">
            Masuk
        </a>
        <br><br>
        <small>&copy; {{ date('Y') }} {{ $schoolName }}</small>
    </div>
</body>
</html>
