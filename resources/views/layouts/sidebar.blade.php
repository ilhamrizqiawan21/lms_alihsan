@php
    $role = auth()->user()->role?->nama_role;
@endphp

@if($role == 'admin')
<li class="nav-section">Menu Utama</li>
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="bi bi-people-fill"></i> Data User
</a>
<a href="{{ route('admin.kelas.index') }}" class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
    <i class="bi bi-building"></i> Data Kelas
</a>
<a href="{{ route('admin.kelas-siswa.index') }}" class="nav-link {{ request()->routeIs('admin.kelas-siswa.*') ? 'active' : '' }}">
    <i class="bi bi-mortarboard-fill"></i> Kelas & Siswa
</a>
<a href="{{ route('admin.mata-pelajaran.index') }}" class="nav-link {{ request()->routeIs('admin.mata-pelajaran.*') ? 'active' : '' }}">
    <i class="bi bi-book-fill"></i> Mata Pelajaran
</a>
<a href="{{ route('admin.kelas-mapel.index') }}" class="nav-link {{ request()->routeIs('admin.kelas-mapel.*') ? 'active' : '' }}">
    <i class="bi bi-diagram-3-fill"></i> Penugasan Guru
</a>
<a href="{{ route('admin.tahun-ajaran.index') }}" class="nav-link {{ request()->routeIs('admin.tahun-ajaran.*') ? 'active' : '' }}">
    <i class="bi bi-calendar-event-fill"></i> Tahun Ajaran
</a>
<a href="{{ route('admin.pengumuman.index') }}" class="nav-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
    <i class="bi bi-megaphone-fill"></i> Pengumuman
</a>
<li class="nav-section">Laporan &amp; Monitoring</li>
<a href="{{ route('admin.kalender') }}" class="nav-link {{ request()->routeIs('admin.kalender') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalender & Reminder
</a>
<a href="{{ route('admin.rekap.absensi') }}" class="nav-link {{ request()->routeIs('admin.rekap.absensi') ? 'active' : '' }}">
    <i class="bi bi-clipboard-check-fill"></i> Rekap Absensi
</a>
<a href="{{ route('admin.rekap.nilai') }}" class="nav-link {{ request()->routeIs('admin.rekap.nilai') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Rekap Nilai
</a>
<a href="{{ route('admin.rekap.sikap') }}" class="nav-link {{ request()->routeIs('admin.rekap.sikap') ? 'active' : '' }}">
    <i class="bi bi-heart-fill"></i> Rekap Sikap
</a>
<a href="{{ route('admin.rekap.tugas') }}" class="nav-link {{ request()->routeIs('admin.rekap.tugas') ? 'active' : '' }}">
    <i class="bi bi-journal-check"></i> Rekap Tugas
</a>
<li class="nav-section">Sistem</li>
<a href="{{ route('admin.school-settings.index') }}" class="nav-link {{ request()->routeIs('admin.school-settings.*') ? 'active' : '' }}">
    <i class="bi bi-buildings-fill"></i> Pengaturan Sekolah
</a>
<a href="{{ route('admin.log-login') }}" class="nav-link {{ request()->routeIs('admin.log-login') ? 'active' : '' }}">
    <i class="bi bi-clock-history"></i> Log Login
</a>
<a href="{{ route('admin.log-error') }}" class="nav-link {{ request()->routeIs('admin.log-error') ? 'active' : '' }}">
    <i class="bi bi-bug-fill"></i> Log Error
</a>
<a href="{{ route('admin.pengaturan') }}" class="nav-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}">
    <i class="bi bi-gear-fill"></i> Pengaturan
</a>
<a href="{{ route('admin.blocked-ips') }}" class="nav-link {{ request()->routeIs('admin.blocked-ips') ? 'active' : '' }}">
    <i class="bi bi-shield-fill-x"></i> IP Diblokir
</a>

@elseif($role == 'guru')
<li class="nav-section">Menu Utama</li>
<a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('guru.kalender') }}" class="nav-link {{ request()->routeIs('guru.kalender') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalender & Reminder
</a>
<a href="{{ route('guru.pengumuman.index') }}" class="nav-link {{ request()->routeIs('guru.pengumuman.*') ? 'active' : '' }}">
    <i class="bi bi-megaphone-fill"></i> Pengumuman
</a>
<a href="{{ route('guru.absensi.index') }}" class="nav-link {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-check-fill"></i> Absensi
</a>
<a href="{{ route('guru.materi.index') }}" class="nav-link {{ request()->routeIs('guru.materi.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-text-fill"></i> Materi
</a>
<a href="{{ route('guru.tugas.index') }}" class="nav-link {{ request()->routeIs('guru.tugas.*') ? 'active' : '' }}">
    <i class="bi bi-journal-fill"></i> Tugas
</a>
<a href="{{ route('guru.nilai.index') }}" class="nav-link {{ request()->routeIs('guru.nilai.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Nilai
</a>
<a href="{{ route('guru.sikap.index') }}" class="nav-link {{ request()->routeIs('guru.sikap.*') ? 'active' : '' }}">
    <i class="bi bi-emoji-smile-fill"></i> Sikap
</a>
<li class="nav-section">Laporan &amp; Lainnya</li>
<a href="{{ route('guru.rekap-nilai') }}" class="nav-link {{ request()->routeIs('guru.rekap-nilai') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-bar-graph-fill"></i> Rekap Nilai
</a>
<a href="{{ route('guru.rekap-sikap') }}" class="nav-link {{ request()->routeIs('guru.rekap-sikap') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-text-fill"></i> Rekap Sikap
</a>
<a href="{{ route('guru.chat.index') }}" class="nav-link {{ request()->routeIs('guru.chat.*') ? 'active' : '' }}">
    <i class="bi bi-chat-dots-fill"></i> Chat Kelas
</a>
<a href="{{ route('guru.notifikasi.index') }}" class="nav-link {{ request()->routeIs('guru.notifikasi.*') ? 'active' : '' }}">
    <i class="bi bi-bell-fill"></i> Notifikasi
    @php $guruUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
    @if($guruUnread > 0)
    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;">{{ $guruUnread }}</span>
    @endif
</a>
<a href="{{ route('guru.profil') }}" class="nav-link {{ request()->routeIs('guru.profil') ? 'active' : '' }}">
    <i class="bi bi-person-circle"></i> Profil
</a>

@elseif($role == 'siswa')
<li class="nav-section">Menu Utama</li>
<a href="{{ route('siswa.dashboard') }}" class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('siswa.progress') }}" class="nav-link {{ request()->routeIs('siswa.progress') ? 'active' : '' }}">
    <i class="bi bi-graph-up-arrow"></i> Progress Saya
</a>
<a href="{{ route('siswa.kalender') }}" class="nav-link {{ request()->routeIs('siswa.kalender') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalender & Reminder
</a>
<a href="{{ route('siswa.materi.index') }}" class="nav-link {{ request()->routeIs('siswa.materi.*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-text-fill"></i> Materi Saya
</a>
<a href="{{ route('siswa.tugas.index') }}" class="nav-link {{ request()->routeIs('siswa.tugas.*') ? 'active' : '' }}">
    <i class="bi bi-journal-fill"></i> Tugas Saya
</a>
<a href="{{ route('siswa.nilai.index') }}" class="nav-link {{ request()->routeIs('siswa.nilai.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Nilai Saya
</a>
<a href="{{ route('siswa.chat.index') }}" class="nav-link {{ request()->routeIs('siswa.chat.*') ? 'active' : '' }}">
    <i class="bi bi-chat-dots-fill"></i> Chat Kelas
</a>
<a href="{{ route('siswa.notifikasi.index') }}" class="nav-link {{ request()->routeIs('siswa.notifikasi.*') ? 'active' : '' }}">
    <i class="bi bi-bell-fill"></i> Notifikasi
    @php $siswaUnread = \App\Models\Notifikasi::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
    @if($siswaUnread > 0)
    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;">{{ $siswaUnread }}</span>
    @endif
</a>
<a href="{{ route('siswa.profil') }}" class="nav-link {{ request()->routeIs('siswa.profil') ? 'active' : '' }}">
    <i class="bi bi-person-circle"></i> Profil
</a>

@elseif($role == 'kepala_sekolah')
<li class="nav-section">Menu Utama</li>
<a href="{{ route('kepsek.dashboard') }}" class="nav-link {{ request()->routeIs('kepsek.dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
</a>
<a href="{{ route('kepsek.kalender') }}" class="nav-link {{ request()->routeIs('kepsek.kalender') ? 'active' : '' }}">
    <i class="bi bi-calendar3"></i> Kalender & Reminder
</a>
<a href="{{ route('kepsek.pengumuman.index') }}" class="nav-link {{ request()->routeIs('kepsek.pengumuman.*') ? 'active' : '' }}">
    <i class="bi bi-megaphone-fill"></i> Pengumuman
</a>
<li class="nav-section">Laporan</li>
<a href="{{ route('kepsek.laporan.absensi') }}" class="nav-link {{ request()->routeIs('kepsek.laporan.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard-data-fill"></i> Laporan Absensi
</a>
<a href="{{ route('kepsek.laporan.nilai') }}" class="nav-link {{ request()->routeIs('kepsek.laporan.nilai') ? 'active' : '' }}">
    <i class="bi bi-bar-chart-fill"></i> Laporan Nilai
</a>
<a href="{{ route('kepsek.laporan.rekap-absensi') }}" class="nav-link {{ request()->routeIs('kepsek.laporan.rekap-absensi') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-bar-graph-fill"></i> Rekap Absensi
</a>
<a href="{{ route('kepsek.laporan.rekap-tugas') }}" class="nav-link {{ request()->routeIs('kepsek.laporan.rekap-tugas') ? 'active' : '' }}">
    <i class="bi bi-journal-check"></i> Rekap Tugas
</a>
<a href="{{ route('kepsek.laporan.rekap-sikap') }}" class="nav-link {{ request()->routeIs('kepsek.laporan.rekap-sikap') ? 'active' : '' }}">
    <i class="bi bi-heart-fill"></i> Rekap Sikap
</a>
<a href="{{ route('kepsek.statistik') }}" class="nav-link {{ request()->routeIs('kepsek.statistik') ? 'active' : '' }}">
    <i class="bi bi-graph-up-arrow"></i> Statistik
</a>
@endif
