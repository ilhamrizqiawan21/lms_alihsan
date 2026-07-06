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
            --app-primary: #16a34a;
            --app-primary-dark: #15803d;
            --app-accent: #f59e0b;
            --app-bg: #f1f5f0;
            --app-radius: 0.875rem;
            --app-shadow: 0 4px 12px rgba(0,0,0,.08);
            --primary-500: {{ $t['p500'] }};
            --primary-600: {{ $t['p600'] }};
            --primary-700: {{ $t['p700'] }};
            --primary-800: {{ $t['p800'] }};
            --primary-100: {{ $t['p100'] }};
            --primary-50: {{ $t['p50'] }};
            --primary-300: {{ $t['p500'] }};
            --app-bg: {{ $t['bg'] }};
            --toast-success-bg: {{ $t['toast'] }};
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
            --shadow-green: 0 4px 16px {{ $t['shadow'] }};
            --bs-primary: var(--app-primary);
            --bs-primary-rgb: 22, 163, 74;
            --bs-success: var(--app-primary);
            --bs-success-rgb: 22, 163, 74;
            --bs-warning: var(--app-accent);
            --bs-warning-rgb: 245, 158, 11;
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
            <img src="{{ asset('logo-sekolah.png') }}" alt="Logo MTs Al-Ihsan" class="app-logo-sm">
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
                <img src="{{ asset('logo-sekolah.png') }}" alt="Logo MTs Al-Ihsan" class="app-logo-md">
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
            &copy; {{ date('Y') }} MTs. Al-Ihsan Batujajar — LMS v2.0 &middot; Ilham Rizqiawan, S.Pd.
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
