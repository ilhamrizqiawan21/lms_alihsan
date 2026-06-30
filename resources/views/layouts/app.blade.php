<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LMS MTs. Al-Ihsan Batujajar')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        @php
            $warna = \App\Models\Pengaturan::getValue('warna_tema', 'hijau');
            $themes = [
                'hijau' => [
                    'p500' => '#22c55e', 'p600' => '#16a34a', 'p700' => '#15803d', 'p800' => '#166534',
                    'p100' => '#dcfce7', 'p50' => '#f0fdf4',
                    'bg' => '#f1f5f0',
                    'shadow' => 'rgba(22,163,74,0.20)',
                    'toast' => 'linear-gradient(135deg, #16a34a, #15803d)',
                ],
                'biru-azure' => [
                    'p500' => '#3b82f6', 'p600' => '#2563eb', 'p700' => '#1d4ed8', 'p800' => '#1e40af',
                    'p100' => '#dbeafe', 'p50' => '#eff6ff',
                    'bg' => '#f0f4f8',
                    'shadow' => 'rgba(37,99,235,0.20)',
                    'toast' => 'linear-gradient(135deg, #2563eb, #1d4ed8)',
                ],
                'biru-aqua' => [
                    'p500' => '#14b8a6', 'p600' => '#0d9488', 'p700' => '#0f766e', 'p800' => '#115e59',
                    'p100' => '#ccfbf1', 'p50' => '#f0fdfa',
                    'bg' => '#f0faf8',
                    'shadow' => 'rgba(13,148,136,0.20)',
                    'toast' => 'linear-gradient(135deg, #0d9488, #0f766e)',
                ],
            ];
            $t = $themes[$warna] ?? $themes['hijau'];
        @endphp
        :root {
            --primary-500: {{ $t['p500'] }};
            --primary-600: {{ $t['p600'] }};
            --primary-700: {{ $t['p700'] }};
            --primary-800: {{ $t['p800'] }};
            --primary-100: {{ $t['p100'] }};
            --primary-50: {{ $t['p50'] }};
            --gold-400: #fbbf24;
            --gold-500: #f59e0b;
            --gold-600: #d97706;
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --sidebar-width: 272px;
            --topbar-height: 58px;
            --radius-sm: 0.375rem;
            --radius-md: 0.625rem;
            --radius-lg: 0.875rem;
            --radius-xl: 1.125rem;
            --radius-full: 9999px;
            --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-default: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 24px rgba(0,0,0,0.10), 0 4px 8px rgba(0,0,0,0.06);
            --shadow-green: 0 4px 16px {{ $t['shadow'] }};
        }

        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeInLeft { from { opacity:0; transform:translateX(-12px); } to { opacity:1; transform:translateX(0); } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes scaleIn { from { opacity:0; transform:scale(0.95); } to { opacity:1; transform:scale(1); } }
        @keyframes slideIn { from { transform:translateX(110%); opacity:0; } to { transform:translateX(0); opacity:1; } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(110%); } }

        * { margin:0; padding:0; box-sizing:border-box; }
        html { height:100%; scroll-behavior:smooth; }
        body {
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            background: {{ $t['bg'] }};
            color: var(--gray-800);
            min-height:100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position:fixed; top:var(--topbar-height); left:0;
            height:calc(100vh - var(--topbar-height));
            width:var(--sidebar-width);
            background:linear-gradient(165deg, var(--primary-600) 0%, var(--primary-700) 40%, var(--primary-800) 100%);
            color:white; z-index:300;
            transform:translateX(-100%);
            transition:transform 0.35s cubic-bezier(0.4,0,0.2,1);
            box-shadow:6px 0 32px rgba(0,0,0,0.18);
            display:flex; flex-direction:column;
            border-right:2px solid rgba(251,191,36,0.25);
        }
        .sidebar::before {
            content:''; position:absolute; top:0; right:0;
            width:120px; height:120px;
            background:radial-gradient(circle at top right, rgba(251,191,36,0.12), transparent 70%);
            pointer-events:none;
        }
        .sidebar.sidebar-open { transform:translateX(0); }
        @media(min-width:992px) { .sidebar { transform:translateX(0); } }

        .sidebar-header {
            display:flex; align-items:center; gap:0.65rem;
            padding:1rem 1.1rem;
            border-bottom:1px solid rgba(255,255,255,0.10);
            background:rgba(0,0,0,0.08);
        }
        .sidebar-logo-icon {
            width:40px; height:40px; background:rgba(255,255,255,0.15);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            border:2px solid rgba(251,191,36,0.4);
        }
        .sidebar-logo-text { display:flex; flex-direction:column; }
        .sidebar-logo-title { font-size:0.88rem; font-weight:700; }
        .sidebar-logo-sub { font-size:0.65rem; color:rgba(255,255,255,0.70); }

        .sidebar-user {
            display:flex; align-items:center; gap:0.75rem;
            padding:0.9rem 1.1rem;
            border-bottom:1px solid rgba(255,255,255,0.10);
            background:rgba(0,0,0,0.05);
        }
        .sidebar-user-avatar {
            width:40px; height:40px;
            background:linear-gradient(135deg, rgba(251,191,36,0.3), rgba(255,255,255,0.15));
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:0.9rem; border:2px solid rgba(251,191,36,0.35); flex-shrink:0;
        }
        .sidebar-user-name { font-size:0.82rem; font-weight:700; max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .sidebar-user-role { font-size:0.65rem; color:rgba(255,255,255,0.65); }

        .sidebar-nav { flex:1; overflow-y:auto; padding:0.6rem 0; }
        .sidebar-nav::-webkit-scrollbar { width:3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.25); border-radius:4px; }

        .sidebar-menu { list-style:none; padding:0 0.7rem; }
        .sidebar .nav-link {
            display:flex; align-items:center; gap:0.75rem;
            padding:0.62rem 0.9rem; color:rgba(255,255,255,0.82);
            text-decoration:none; font-size:0.84rem; font-weight:500;
            border-radius:0.6rem; transition:var(--transition-default);
            border-left:3px solid transparent; margin-bottom:1px;
        }
        .sidebar .nav-link i { width:18px; text-align:center; font-size:0.9rem; flex-shrink:0; transition:var(--transition-default); }
        .sidebar .nav-link:hover {
            background:rgba(255,255,255,0.13); color:white;
            border-left-color:var(--gold-400); transform:translateX(4px);
        }
        .sidebar .nav-link:hover i { color:var(--gold-400); transform:scale(1.15); }
        .sidebar .nav-link.active {
            background:rgba(255,255,255,0.18); font-weight:700;
            border-left-color:var(--gold-400); color:white;
        }
        .sidebar .nav-link.active i { color:var(--gold-400); }
        .sidebar .nav-section {
            padding:10px 15px 5px; font-size:0.65rem;
            text-transform:uppercase; letter-spacing:1px;
            color:rgba(255,255,255,0.35); font-weight:700;
        }

        .sidebar-footer {
            padding:0.7rem; border-top:1px solid rgba(255,255,255,0.10);
            background:rgba(0,0,0,0.08);
        }
        .sidebar-logout-btn {
            display:flex; align-items:center; gap:0.7rem;
            padding:0.65rem 0.9rem; color:rgba(255,255,255,0.85);
            text-decoration:none; border-radius:0.6rem;
            background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.20);
            font-size:0.84rem; font-weight:500; transition:var(--transition-default);
        }
        .sidebar-logout-btn:hover {
            background:rgba(239,68,68,0.28); color:white;
            border-color:rgba(239,68,68,0.4);
            transform:translateY(-1px); box-shadow:0 4px 12px rgba(239,68,68,0.2);
        }

        /* ── SIDEBAR OVERLAY ── */
        .sidebar-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.4); backdrop-filter:blur(2px);
            z-index:200; top:var(--topbar-height);
        }
        .sidebar-overlay.show { display:block; }
        @media(min-width:992px) { .sidebar-overlay { display:none !important; } }

        /* ── TOPBAR ── */
        .topbar {
            position:fixed; top:0; left:0; right:0;
            height:var(--topbar-height);
            background:linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 40%, var(--primary-800) 100%);
            color:white; display:flex; align-items:center; gap:0.75rem;
            padding:0 1.1rem; z-index:400;
            box-shadow:0 3px 16px rgba(0,0,0,0.20);
            border-bottom:2px solid rgba(251,191,36,0.30);
        }
        .topbar-toggle-btn {
            background:rgba(255,255,255,0.12); border:none; color:white;
            width:38px; height:38px; border-radius:0.55rem;
            cursor:pointer; display:flex; align-items:center; justify-content:center;
            font-size:1rem; transition:var(--transition-bounce);
        }
        .topbar-toggle-btn:hover { background:rgba(255,255,255,0.22); transform:scale(1.08); }
        .topbar-brand { display:flex; align-items:center; gap:0.65rem; flex:1; }
        .topbar-logo-icon {
            width:36px; height:36px; background:rgba(255,255,255,0.15);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            border:2px solid rgba(251,191,36,0.35); overflow:hidden;
        }
        .topbar-title { display:flex; flex-direction:column; }
        .topbar-title-main { font-size:0.92rem; font-weight:800; line-height:1.2; }
        .topbar-title-sub { font-size:0.62rem; color:rgba(255,255,255,0.75); }
        .topbar-actions { display:flex; align-items:center; gap:0.5rem; }
        .topbar-actions .dropdown-toggle { color:white; background:rgba(255,255,255,0.12); border:none; }
        .topbar-actions .dropdown-toggle:hover { background:rgba(255,255,255,0.22); }
        @media(min-width:992px) { .topbar-toggle-btn { display:none; } }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-top:var(--topbar-height);
            margin-left:0; min-height:calc(100vh - var(--topbar-height));
            transition:margin-left 0.35s ease;
        }
        @media(min-width:992px) { .main-content { margin-left:var(--sidebar-width); } }

        .page-content { padding:2rem 1.5rem; max-width:1300px; margin:0 auto; animation:fadeIn 0.35s ease; }

        .page-header { margin-bottom:1.75rem; animation:fadeInUp 0.4s ease both; }
        .page-header h4 {
            font-size:1.75rem; font-weight:800;
            background:linear-gradient(135deg, var(--gray-800) 30%, var(--primary-700) 100%);
            -webkit-background-clip:text; background-clip:text; color:transparent;
            letter-spacing:-0.02em; line-height:1.2;
        }
        .page-header .breadcrumb { background:none; padding:0; margin:8px 0 0; font-size:0.85rem; }

        /* ── CARDS ── */
        .card { border:none; border-radius:var(--radius-xl); box-shadow:var(--shadow-sm); margin-bottom:1.25rem; transition:var(--transition-default); }
        .card:hover { box-shadow:var(--shadow-md); }
        .card-header {
            background:white; border-bottom:1px solid var(--gray-200);
            padding:1rem 1.25rem; font-weight:700; font-size:0.95rem;
            border-radius:var(--radius-xl) var(--radius-xl) 0 0 !important;
            display:flex; align-items:center; gap:0.5rem;
        }
        .card-body { padding:1.25rem; }

        /* ── STAT CARDS ── */
        .stats-grid {
            display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
            gap:1.25rem; margin-bottom:1.75rem;
        }
        .stat-card {
            background:white; border-radius:var(--radius-xl);
            padding:1.4rem 1.5rem; box-shadow:var(--shadow-sm);
            display:flex; align-items:center; gap:1.1rem;
            transition:var(--transition-default);
            border-left:4px solid var(--primary-500);
            position:relative; overflow:hidden;
            animation:fadeInUp 0.4s ease both;
        }
        .stat-card:nth-child(1) { animation-delay:0.05s; }
        .stat-card:nth-child(2) { animation-delay:0.10s; }
        .stat-card:nth-child(3) { animation-delay:0.15s; }
        .stat-card:nth-child(4) { animation-delay:0.20s; }
        .stat-card::before {
            content:''; position:absolute; top:-20px; right:-20px;
            width:90px; height:90px;
            background:radial-gradient(circle, var(--primary-100) 0%, transparent 70%);
            pointer-events:none; transition:transform 0.4s ease;
        }
        .stat-card::after {
            content:''; position:absolute; bottom:0; left:0; right:0;
            height:2px; background:linear-gradient(90deg, var(--primary-500), var(--gold-500));
            transform:scaleX(0); transform-origin:left;
            transition:transform 0.35s ease;
        }
        .stat-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); border-left-color:var(--primary-600); }
        .stat-card:hover::before { transform:scale(1.3); }
        .stat-card:hover::after { transform:scaleX(1); }
        .stat-icon {
            font-size:1.7rem; color:var(--primary-600);
            background:linear-gradient(135deg, var(--primary-50), var(--primary-100));
            width:58px; height:58px; display:flex; align-items:center; justify-content:center;
            border-radius:var(--radius-lg); flex-shrink:0;
            border:1px solid rgba(34,197,94,0.15); transition:var(--transition-bounce);
        }
        .stat-card:hover .stat-icon { transform:scale(1.1) rotate(-5deg); box-shadow:var(--shadow-green); }
        .stat-number { font-size:1.8rem; font-weight:700; color:var(--gray-800); }
        .stat-label { font-size:0.82rem; color:var(--gray-500); margin-top:2px; }

        /* ── TABLES ── */
        .table th {
            font-weight:600; font-size:0.82rem; color:var(--gray-600);
            border-top:none; background:var(--gray-50); padding:0.75rem;
        }
        .table td { font-size:0.85rem; vertical-align:middle; padding:0.65rem 0.75rem; }
        .table tbody tr { transition:var(--transition-fast); }
        .table tbody tr:hover { background:var(--primary-50); }

        /* ── BADGES ── */
        .badge { font-weight:500; font-size:0.72rem; padding:5px 10px; border-radius:var(--radius-full); }
        .badge-hadir { background:#dcfce7; color:#166534; }
        .badge-sakit { background:#fef3c7; color:#92400e; }
        .badge-izin { background:#dbeafe; color:#1e40af; }
        .badge-alpha { background:#fee2e2; color:#991b1b; }
        .badge-admin { background:#f3e8ff; color:#6b21a8; }
        .badge-guru { background:#dcfce7; color:#166534; }
        .badge-siswa { background:#e0e7ff; color:#3730a3; }
        .badge-kepala_sekolah { background:#fef3c7; color:#92400e; }

        /* ── BUTTONS ── */
        .btn { font-weight:600; font-size:0.85rem; border-radius:var(--radius-md); padding:0.45rem 1rem; transition:var(--transition-default); }
        .btn-primary { background:linear-gradient(135deg, var(--primary-500), var(--primary-600)); border:none; }
        .btn-primary:hover { background:linear-gradient(135deg, var(--primary-600), var(--primary-700)); transform:translateY(-1px); box-shadow:var(--shadow-green); }
        .btn-sm { font-size:0.78rem; padding:0.35rem 0.75rem; }
        .btn-outline-primary { border-color:var(--primary-500); color:var(--primary-600); }
        .btn-outline-primary:hover { background:var(--primary-500); border-color:var(--primary-500); color:white; }

        /* ── ALERTS ── */
        .alert { border:none; border-radius:var(--radius-md); font-size:0.88rem; padding:0.75rem 1rem; }
        .alert-success { background:#dcfce7; color:#166534; }
        .alert-danger { background:#fee2e2; color:#991b1b; }
        .alert-warning { background:#fef3c7; color:#92400e; }
        .alert-info { background:#dbeafe; color:#1e40af; }

        /* ── FORM CONTROLS ── */
        .form-control, .form-select { border-radius:var(--radius-md); border:1px solid var(--gray-300); padding:0.5rem 0.75rem; font-size:0.88rem; transition:var(--transition-fast); }
        .form-control:focus, .form-select:focus { border-color:var(--primary-500); box-shadow:0 0 0 3px rgba(34,197,94,0.15); }
        .form-label { font-weight:600; font-size:0.84rem; color:var(--gray-600); margin-bottom:0.35rem; }

        /* ── TOGGLE SWITCH ── */
        .form-switch { padding-left:3em; }
        .form-switch .form-check-input {
            width:2.5em; height:1.35em; margin-left:-3em;
            border-radius:2em; cursor:pointer;
            border:2px solid var(--gray-300);
            background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%239ca3af'/%3e%3c/svg%3e");
        }
        .form-switch .form-check-input:checked {
            background-color:var(--primary-500);
            border-color:var(--primary-500);
            background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
        }
        .form-switch .form-check-label { font-size:0.82rem; padding-top:2px; cursor:pointer; }

        /* ── PAGINATION ── */
        .pagination { margin-bottom:0; }
        .page-item.active .page-link { background-color:var(--primary-600); border-color:var(--primary-600); }
        .page-link { color:var(--primary-600); border-radius:var(--radius-sm); }
        .page-link:hover { color:var(--primary-800); background:var(--primary-50); }

        /* ── FOOTER ── */
        footer { text-align:center; padding:1.5rem; color:var(--gray-400); font-size:0.78rem; border-top:1px solid var(--gray-200); margin-top:2rem; }

        /* ── TOAST ── */
        .toast-container {
            position:fixed; bottom:1.5rem; right:1.5rem; z-index:99999;
            display:flex; flex-direction:column-reverse; gap:0.5rem; max-width:380px;
        }
        .toast-item {
            display:flex; align-items:center; gap:0.65rem;
            padding:0.85rem 1.25rem; border-radius:0.625rem;
            font-size:0.875rem; font-weight:500; line-height:1.4;
            color:white; box-shadow:0 8px 24px rgba(0,0,0,0.15);
            animation:slideIn 0.35s ease; pointer-events:auto;
        }
        .toast-item.removing { animation:toastOut 0.35s ease forwards; }
        .toast-item.success { background:{{ $t['toast'] }}; }
        .toast-item.error { background:linear-gradient(135deg, #ef4444, #dc2626); }
        .toast-item.warning { background:linear-gradient(135deg, #f59e0b, #d97706); }
        .toast-item.info { background:linear-gradient(135deg, #3b82f6, #2563eb); }

        /* ── MODAL ── */
        .modal-content { border:none; border-radius:var(--radius-xl); box-shadow:0 20px 60px rgba(0,0,0,0.15); }
        .modal-header { border-bottom:1px solid var(--gray-200); padding:1rem 1.25rem; }
        .modal-footer { border-top:1px solid var(--gray-200); padding:1rem 1.25rem; }

        /* ── PROGRESS BAR ── */
        .progress { height:10px; border-radius:20px; background:var(--gray-200); }
        .progress-bar { background:linear-gradient(90deg, var(--primary-500), var(--primary-600)); border-radius:20px; transition:width 0.6s ease; }

        /* ── MOBILE ── */
        @media(max-width:991px) {
            .sidebar.sidebar-open ~ .sidebar-overlay { display:block; }
            .main-content { margin-left:0 !important; }
        }
        @media(max-width:768px) {
            .page-content { padding:1rem 0.75rem; }
            .stats-grid { grid-template-columns:1fr 1fr; }
            .page-header h4 { font-size:1.3rem; }
        }
        @media(max-width:480px) {
            .stats-grid { grid-template-columns:1fr; }
            .topbar-title-sub { display:none; }
            .page-content { padding:0.75rem 0.5rem; }
            .card-body { padding:0.75rem; }
            .btn { font-size:0.78rem; padding:0.35rem 0.7rem; }
        }
        /* ── RESPONSIVE TABLE (selalu scroll di mobile) ── */
        @media(max-width:767px) {
            .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
            .table td, .table th { white-space:nowrap; font-size:0.78rem; padding:0.4rem 0.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('sidebar-open');this.classList.remove('show')"></div>

    <!-- Topbar -->
    <div class="topbar">
        <button class="topbar-toggle-btn" onclick="var s=document.getElementById('sidebar');var o=document.getElementById('sidebarOverlay');s.classList.toggle('sidebar-open');o.classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-brand">
            <div class="topbar-logo-icon">
            <img src="{{ asset('logo-sekolah.png') }}" alt="Logo MTs Al-Ihsan" style="width:32px;height:32px;object-fit:contain;">
            </div>
            <div class="topbar-title">
                <span class="topbar-title-main">Digitalisasi Pembelajaran</span>
                <span class="topbar-title-sub">MTs. Al-Ihsan Batujajar — Kurikulum Merdeka</span>
            </div>
        </div>
        <div class="topbar-actions">
            @php
                $topbarRole = auth()->user()->role?->nama_role;
                $topbarUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count();
                $topbarNotifRoute = match($topbarRole) {
                    'guru' => route('guru.notifikasi.index'),
                    'siswa' => route('siswa.notifikasi.index'),
                    default => null,
                };
                $topbarNotifs = \App\Models\Notifikasi::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp
            @if($topbarNotifRoute && in_array($topbarRole, ['guru', 'siswa']))
            <div class="dropdown">
                <button class="btn btn-sm position-relative" style="background: rgba(255,255,255,0.12); border: none; color: white;" data-bs-toggle="dropdown" title="Notifikasi">
                    <i class="bi bi-bell-fill"></i>
                    @if($topbarUnread > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.55rem;">
                        {{ $topbarUnread > 99 ? '99+' : $topbarUnread }}
                    </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 340px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-item-text d-flex justify-content-between align-items-center">
                        <strong style="font-size: 0.85rem;">Notifikasi</strong>
                        @if($topbarUnread > 0)
                        <form action="{{ $topbarRole === 'guru' ? route('guru.notifikasi.mark-all-read') : route('siswa.notifikasi.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none" style="font-size: 0.7rem; color: var(--primary-600);">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    @forelse($topbarNotifs as $tn)
                    <li>
                        <a href="{{ $topbarRole === 'guru' ? route('guru.notifikasi.mark-read', $tn) : route('siswa.notifikasi.mark-read', $tn) }}" 
                           class="dropdown-item" style="white-space: normal; {{ $tn->is_read ? '' : 'background: #fef2f2;' }}">
                            <div style="font-size: 0.8rem; font-weight: {{ $tn->is_read ? '400' : '600' }};">{{ $tn->judul }}</div>
                            <div style="font-size: 0.7rem; color: #6b7280;">{{ Str::limit($tn->pesan, 80) }}</div>
                            <small style="font-size: 0.6rem; color: #9ca3af;">{{ $tn->created_at ? \Carbon\Carbon::parse($tn->created_at)->diffForHumans() : '' }}</small>
                        </a>
                    </li>
                    @empty
                    <li><span class="dropdown-item-text text-muted text-center" style="font-size: 0.8rem;">Belum ada notifikasi</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a href="{{ $topbarNotifRoute }}" class="dropdown-item text-center" style="font-size: 0.8rem; color: var(--primary-600);">Lihat Semua Notifikasi</a></li>
                </ul>
            </div>
            @endif
            <span class="d-none d-md-inline me-2" style="font-size:0.82rem; opacity:0.9;">{{ auth()->user()->nama_lengkap }}</span>
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text fw-bold">{{ auth()->user()->nama_lengkap }}</span></li>
                    <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->username }} — {{ auth()->user()->role?->nama_role }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <img src="{{ asset('logo-sekolah.png') }}" alt="Logo MTs Al-Ihsan" style="width:36px;height:36px;object-fit:contain;border-radius:50%;">
            </div>
            <div class="sidebar-logo-text">
                <span class="sidebar-logo-title">LMS Al-Ihsan</span>
                <span class="sidebar-logo-sub">MTs. Al-Ihsan Batujajar</span>
            </div>
        </div>
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><i class="bi bi-person-fill"></i></div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->nama_lengkap }}</div>
                <div class="sidebar-user-role">{{ auth()->user()->role?->nama_role }}</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                @php
                    $role = auth()->user()->role?->nama_role;
                    $prefix = match($role) {
                        'admin' => 'admin',
                        'guru' => 'guru',
                        'siswa' => 'siswa',
                        'kepala_sekolah' => 'kepsek',
                        default => '#'
                    };
                @endphp
                @include('layouts.sidebar')
            </ul>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-logout-btn"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-content">
            @yield('content')
        </div>
        <footer>
            &copy; {{ date('Y') }} MTs. Al-Ihsan Batujajar — LMS v2.0 &middot; Ilham Rizqiawan, S.Pd.
        </footer>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if(session('success'))<script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('success')),'success')})</script>@endif
    @if(session('error'))<script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('error')),'error')})</script>@endif
    @if(session('warning'))<script>document.addEventListener('DOMContentLoaded',function(){showToast(@json(session('warning')),'warning')})</script>@endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // ── Global Toast ──
        window.showToast = function(message, type) {
            type = type || 'success';
            var icons = { success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' };
            var container = document.getElementById('toastContainer');
            var el = document.createElement('div');
            el.className = 'toast-item ' + type;
            el.innerHTML = '<i class="bi ' + (icons[type]||icons.info) + '"></i><span>' + message.replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]}) + '</span>';
            container.appendChild(el);
            setTimeout(function(){ el.classList.add('removing'); setTimeout(function(){ if(el.parentNode) el.remove(); }, 400); }, 4000);
        };

        // ── Confirm Modal ──
        window.confirmAction = function(message, callback, options) {
            options = options || {};
            var title = options.title || 'Konfirmasi';
            var confirmText = options.confirmText || 'Ya, lanjutkan';
            var cancelText = options.cancelText || 'Batal';
            var isDanger = options.danger === true;
            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99998;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(3px);';
            overlay.innerHTML = '<div style="background:white;border-radius:16px;padding:28px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2);animation:scaleIn 0.2s ease;">' +
                '<div style="text-align:center;font-size:2.5rem;margin-bottom:12px;color:'+(isDanger?'#ef4444':'#f59e0b')+'"><i class="bi '+(isDanger?'bi-exclamation-triangle-fill':'bi-question-circle-fill')+'"></i></div>' +
                '<h5 style="text-align:center;margin-bottom:8px;font-weight:700;">'+title.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]})+'</h5>' +
                '<p style="text-align:center;color:#6b7280;margin-bottom:20px;font-size:0.9rem;">'+message.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]})+'</p>' +
                '<div style="display:flex;gap:10px;justify-content:center;">' +
                '<button id="confirmCancel" style="padding:8px 24px;border-radius:8px;border:1px solid #d1d5db;background:white;font-weight:600;cursor:pointer;">'+cancelText.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]})+'</button>' +
                '<button id="confirmOk" style="padding:8px 24px;border-radius:8px;border:none;background:'+(isDanger?'#ef4444':'#22c55e')+';color:white;font-weight:600;cursor:pointer;">'+confirmText.replace(/[&<>"']/g,function(m){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]})+'</button>' +
                '</div></div>';
            document.body.appendChild(overlay);
            document.getElementById('confirmOk').onclick = function(){ document.body.removeChild(overlay); callback(true); };
            document.getElementById('confirmCancel').onclick = function(){ document.body.removeChild(overlay); callback(false); };
            overlay.onclick = function(e){ if(e.target===overlay){ document.body.removeChild(overlay); callback(false); } };
            document.addEventListener('keydown', function handler(e){ if(e.key==='Escape'){ document.body.removeChild(overlay); document.removeEventListener('keydown',handler); callback(false); } });
        };

        // ── Data-confirm attribute handler ──
        document.addEventListener('click', function(e) {
            var el = e.target.closest('[data-confirm]');
            if (!el) return;
            e.preventDefault();
            e.stopPropagation();
            var msg = el.getAttribute('data-confirm');
            var danger = el.classList.contains('btn-danger') || el.getAttribute('data-danger') === 'true';
            confirmAction(msg, function(ok) {
                if (!ok) return;
                var formAction = el.getAttribute('data-action');
                var method = (el.getAttribute('data-method') || 'get').toUpperCase();
                if (formAction) {
                    var form = document.createElement('form');
                    form.method = method === 'POST' ? 'POST' : 'GET';
                    form.action = formAction;
                    if (method === 'DELETE') { form.innerHTML = '<input type="hidden" name="_method" value="DELETE">'; form.method = 'POST'; }
                    var csrf = document.querySelector('meta[name="csrf-token"]');
                    if (csrf && method !== 'GET') { var inp = document.createElement('input'); inp.type = 'hidden'; inp.name = '_token'; inp.value = csrf.content; form.appendChild(inp); }
                    document.body.appendChild(form);
                    form.submit();
                } else if (el.href) {
                    window.location.href = el.href;
                }
            }, { danger: danger, title: el.getAttribute('data-title') || 'Konfirmasi' });
        });

        // ── DataTables + Select2 init ──
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
            $('.datatable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100]
            });
        });

        // ── Sidebar open on desktop ──
        if (window.innerWidth >= 992) {
            document.getElementById('sidebar').classList.add('sidebar-open');
        }
    </script>
    @stack('scripts')
</body>
</html>
