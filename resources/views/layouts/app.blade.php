<!DOCTYPE html>
<html lang="id">
<head>
    @php
        $layoutSchoolName = school_setting('school_name', 'Nama Sekolah');
        $layoutSchoolShortName = school_setting('school_short_name', 'LMS');
        $layoutAppName = 'LMS Sekolah';
        $layoutLogoUrl = school_logo_url();
        $layoutFaviconUrl = school_favicon_url();
        $layoutTheme = \App\Models\Pengaturan::getValue('warna_tema', 'hijau');
        $layoutThemeColors = [
            'hijau' => [
                'primary' => '#198754',
                'secondary' => '#0d6efd',
                'sidebar' => '#166534',
                'navbar' => '#198754',
            ],
            'biru-azure' => [
                'primary' => '#0d6efd',
                'secondary' => '#22c55e',
                'sidebar' => '#1d4ed8',
                'navbar' => '#0d6efd',
            ],
            'biru-aqua' => [
                'primary' => '#0891b2',
                'secondary' => '#14b8a6',
                'sidebar' => '#0e7490',
                'navbar' => '#0891b2',
            ],
        ];
        $layoutActiveTheme = $layoutThemeColors[$layoutTheme] ?? $layoutThemeColors['hijau'];
        $layoutBaseColor = \App\Models\Pengaturan::getValue('warna_base');
        $layoutBaseColor = is_string($layoutBaseColor) && preg_match('/^#[0-9A-Fa-f]{6}$/', $layoutBaseColor)
            ? $layoutBaseColor
            : null;
        $layoutPrimaryColor = $layoutBaseColor ?: $layoutActiveTheme['primary'];
        $layoutSecondaryColor = $layoutActiveTheme['secondary'];
        $layoutSidebarColor = $layoutBaseColor ?: $layoutActiveTheme['sidebar'];
        $layoutNavbarColor = $layoutBaseColor ?: $layoutActiveTheme['navbar'];
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="application-name" content="{{ $layoutAppName }}">
    <meta name="theme-color" content="{{ $layoutPrimaryColor }}">
    <title>@yield('title', $layoutAppName . ' - ' . $layoutSchoolName)</title>
    <link rel="icon" href="{{ $layoutFaviconUrl }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    @endif
    <style>
        :root {
            --app-primary: {{ $layoutPrimaryColor }};
            --app-primary-dark: {{ $layoutSidebarColor }};
            --app-accent: {{ $layoutSecondaryColor }};
            --app-bg: #f8fafc;
            --app-radius: 0.875rem;
            --app-shadow: 0 4px 12px rgba(0,0,0,.08);
            --primary-500: {{ $layoutPrimaryColor }};
            --primary-600: {{ $layoutPrimaryColor }};
            --primary-700: {{ $layoutSidebarColor }};
            --primary-800: {{ $layoutSidebarColor }};
            --primary-100: color-mix(in srgb, {{ $layoutPrimaryColor }} 18%, white);
            --primary-50: color-mix(in srgb, {{ $layoutPrimaryColor }} 8%, white);
            --primary-300: color-mix(in srgb, {{ $layoutPrimaryColor }} 70%, white);
            --sidebar-bg: {{ $layoutSidebarColor }};
            --navbar-bg: {{ $layoutNavbarColor }};
            --toast-success-bg: linear-gradient(135deg, {{ $layoutPrimaryColor }}, {{ $layoutSidebarColor }});
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
            --radius-lg: var(--app-radius);
            --radius-xl: 1.125rem;
            --radius-full: 9999px;
            --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-default: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: var(--app-shadow), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 24px rgba(0,0,0,0.10), 0 4px 8px rgba(0,0,0,0.06);
            --shadow-green: 0 4px 16px color-mix(in srgb, {{ $layoutPrimaryColor }} 24%, transparent);
            --bs-primary: var(--app-primary);
            --bs-success: var(--app-primary);
            --bs-warning: var(--app-accent);
        }
        .topbar {
            background:linear-gradient(135deg, var(--navbar-bg), var(--primary-700));
        }
        .sidebar {
            background:linear-gradient(165deg, var(--sidebar-bg) 0%, var(--primary-700) 45%, var(--primary-800) 100%);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/lms-app.css') }}">
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
                <img src="{{ $layoutLogoUrl }}" alt="Logo {{ $layoutSchoolName }}" class="app-logo-sm">
            </div>
            <div class="topbar-title">
                <span class="topbar-title-main">{{ $layoutAppName }}</span>
                <span class="topbar-title-sub">{{ $layoutSchoolName }}</span>
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
                <button class="btn btn-sm position-relative topbar-icon-btn" data-bs-toggle="dropdown" title="Notifikasi">
                    <i class="bi bi-bell-fill"></i>
                    @if($topbarUnread > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count">
                        {{ $topbarUnread > 99 ? '99+' : $topbarUnread }}
                    </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-menu">
                    <li class="dropdown-item-text d-flex justify-content-between align-items-center">
                        <strong class="notification-title">Notifikasi</strong>
                        @if($topbarUnread > 0)
                        <form action="{{ $topbarRole === 'guru' ? route('guru.notifikasi.mark-all-read') : route('siswa.notifikasi.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none notification-mark-all">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    @forelse($topbarNotifs as $tn)
                    <li>
                        <form action="{{ $topbarRole === 'guru' ? route('guru.notifikasi.mark-read', $tn) : route('siswa.notifikasi.mark-read', $tn) }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item notification-link {{ $tn->is_read ? '' : 'unread' }}">
                                <div class="notification-item-title">{{ $tn->judul }}</div>
                                <div class="notification-item-message">{{ Str::limit($tn->pesan, 80) }}</div>
                                <small class="notification-item-time">{{ $tn->created_at ? \Carbon\Carbon::parse($tn->created_at)->diffForHumans() : '' }}</small>
                            </button>
                        </form>
                    </li>
                    @empty
                    <li><span class="dropdown-item-text text-muted text-center notification-action-link">Belum ada notifikasi</span></li>
                    @endforelse
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a href="{{ $topbarNotifRoute }}" class="dropdown-item text-center notification-action-link">Lihat Semua Notifikasi</a></li>
                </ul>
            </div>
            @endif
            <span class="d-none d-md-inline me-2 topbar-user-name">{{ auth()->user()->nama_lengkap }}</span>
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
                <img src="{{ $layoutLogoUrl }}" alt="Logo {{ $layoutSchoolName }}" class="app-logo-md">
            </div>
            <div class="sidebar-logo-text">
                <span class="sidebar-logo-title">{{ $layoutAppName }}</span>
                <span class="sidebar-logo-sub">{{ $layoutSchoolName }}</span>
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
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-x-circle-fill"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif
            @yield('content')
        </div>
        <footer>
            &copy; {{ date('Y') }} {{ $layoutSchoolName }} — {{ $layoutAppName }}
        </footer>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    @unless(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @endunless
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
            var msg = el.getAttribute('data-confirm') || 'Anda yakin ingin melanjutkan?';
            var danger = el.className.indexOf('danger') !== -1 || el.getAttribute('data-danger') === 'true';
            confirmAction(msg, function(ok) {
                if (!ok) return;
                var parentForm = el.closest('form');
                var formAction = el.getAttribute('data-action');
                var method = (el.getAttribute('data-method') || 'get').toUpperCase();
                if (parentForm) {
                    if (el.name) {
                        var submitValue = document.createElement('input');
                        submitValue.type = 'hidden';
                        submitValue.name = el.name;
                        submitValue.value = el.value;
                        parentForm.appendChild(submitValue);
                    }
                    parentForm.submit();
                } else if (formAction) {
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

            // DataTables hanya untuk tabel dengan class 'datatable'
            if ($.fn.DataTable) {
                $('.datatable').each(function() {
                    var hasServerPagination = $(this).closest('.card').find('.pagination').length > 0;
                    if (!hasServerPagination) {
                        $(this).DataTable({
                            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
                            pageLength: 25,
                            lengthMenu: [10, 25, 50, 100],
                            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                        });
                    }
                });
            }
        });

        // ── Sidebar open on desktop ──
        if (window.innerWidth >= 992) {
            document.getElementById('sidebar').classList.add('sidebar-open');
        }
    </script>
    @stack('scripts')
</body>
</html>
