<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Admin\PengumumanController as AdminPengumumanController;
use App\Http\Controllers\Admin\KelasMapelController;
use App\Http\Controllers\Admin\KelasSiswaController;
use App\Http\Controllers\Admin\KalenderController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\SchoolSettingController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\AcademicAuditLogController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\AbsensiController;
use App\Http\Controllers\Guru\MateriController as GuruMateriController;
use App\Http\Controllers\Guru\TugasController as GuruTugasController;
use App\Http\Controllers\Guru\KalenderController as GuruKalenderController;
use App\Http\Controllers\Guru\KelasMapelWorkspaceController;
use App\Http\Controllers\Guru\NilaiController;
use App\Http\Controllers\Guru\SikapController;
use App\Http\Controllers\Guru\ChatController as GuruChatController;
use App\Http\Controllers\Guru\NotifikasiController as GuruNotifikasiController;
use App\Http\Controllers\Guru\WaliKelasController as GuruWaliKelasController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\MateriController as SiswaMateriController;
use App\Http\Controllers\Siswa\TugasController as SiswaTugasController;
use App\Http\Controllers\Siswa\NilaiController as SiswaNilaiController;
use App\Http\Controllers\Siswa\KalenderController as SiswaKalenderController;
use App\Http\Controllers\Siswa\KelasMapelWorkspaceController as SiswaKelasMapelWorkspaceController;
use App\Http\Controllers\Siswa\ProgressController;
use App\Http\Controllers\Siswa\ChatController as SiswaChatController;
use App\Http\Controllers\Siswa\NotifikasiController as SiswaNotifikasiController;
use App\Http\Controllers\Siswa\PengumumanController as SiswaPengumumanController;
use App\Http\Controllers\Kepsek\DashboardController as KepsekDashboardController;
use App\Http\Controllers\Kepsek\LaporanController;
use App\Http\Controllers\Kepsek\StatistikController;
use App\Http\Controllers\Kepsek\KalenderController as KepsekKalenderController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/login', [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ADMIN (role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users/import-siswa/template', [UserController::class, 'downloadSiswaTemplate'])->name('users.import-siswa.template');
    Route::post('/users/import-siswa', [UserController::class, 'importSiswa'])->name('users.import-siswa');
    Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Kelas & Siswa
    Route::get('/kelas-siswa', [KelasSiswaController::class, 'index'])->name('kelas-siswa.index');
    Route::get('/kelas-siswa/export/excel', [KelasSiswaController::class, 'exportExcel'])->name('kelas-siswa.export.excel');
    Route::get('/kelas-siswa/import/template', [KelasSiswaController::class, 'downloadTemplate'])->name('kelas-siswa.import.template');
    Route::post('/kelas-siswa/import', [KelasSiswaController::class, 'importSiswa'])->name('kelas-siswa.import');
    Route::post('/kelas-siswa/siswa', [KelasSiswaController::class, 'storeSiswa'])->name('kelas-siswa.store-siswa');
    Route::put('/kelas-siswa/siswa/{siswa}', [KelasSiswaController::class, 'updateSiswa'])->name('kelas-siswa.update-siswa');
    Route::post('/kelas-siswa/siswa/{siswa}/reset-password', [KelasSiswaController::class, 'resetPassword'])->name('kelas-siswa.reset-password');
    Route::delete('/kelas-siswa/siswa/{siswa}', [KelasSiswaController::class, 'destroySiswa'])->name('kelas-siswa.destroy-siswa');
    Route::post('/kelas-siswa/kelas/{kelas}/luluskan', [KelasSiswaController::class, 'luluskanKelas'])->name('kelas-siswa.luluskan-kelas');

    // Mata Pelajaran
    Route::get('/mata-pelajaran', [MataPelajaranController::class, 'index'])->name('mata-pelajaran.index');
    Route::post('/mata-pelajaran', [MataPelajaranController::class, 'store'])->name('mata-pelajaran.store');
    Route::put('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'update'])->name('mata-pelajaran.update');
    Route::delete('/mata-pelajaran/{mataPelajaran}', [MataPelajaranController::class, 'destroy'])->name('mata-pelajaran.destroy');

    // Kelas-Mapel (Penugasan Guru)
    Route::get('/kelas-mapel', [KelasMapelController::class, 'index'])->name('kelas-mapel.index');
    Route::post('/kelas-mapel', [KelasMapelController::class, 'store'])->name('kelas-mapel.store');
    Route::delete('/kelas-mapel/{kelasMapel}', [KelasMapelController::class, 'destroy'])->name('kelas-mapel.destroy');
    Route::post('/wali-kelas', [KelasMapelController::class, 'storeWaliKelas'])->name('wali-kelas.store');
    Route::delete('/wali-kelas/{waliKelas}', [KelasMapelController::class, 'destroyWaliKelas'])->name('wali-kelas.destroy');

    // Tahun Ajaran
    Route::get('/tahun-ajaran', [TahunAjaranController::class, 'index'])->name('tahun-ajaran.index');
    Route::post('/tahun-ajaran', [TahunAjaranController::class, 'store'])->name('tahun-ajaran.store');
    Route::put('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'update'])->name('tahun-ajaran.update');
    Route::post('/tahun-ajaran/{tahunAjaran}/set-aktif', [TahunAjaranController::class, 'setAktif'])->name('tahun-ajaran.set-aktif');
    Route::delete('/tahun-ajaran/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])->name('tahun-ajaran.destroy');

    // Pengumuman
    Route::get('/pengumuman', [AdminPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'show'])->name('pengumuman.show');
    Route::post('/pengumuman', [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::delete('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // Kalender & Reminder
    Route::get('/kalender', [KalenderController::class, 'index'])->name('kalender');
    Route::post('/kalender', [KalenderController::class, 'store'])->name('kalender.store');
    Route::put('/kalender/{calendarEvent}', [KalenderController::class, 'update'])->name('kalender.update');
    Route::delete('/kalender/{calendarEvent}', [KalenderController::class, 'destroy'])->name('kalender.destroy');

    // Rekap
    Route::get('/rekap/absensi', [RekapController::class, 'absensi'])->name('rekap.absensi');
    Route::get('/rekap/nilai', [RekapController::class, 'nilai'])->name('rekap.nilai');
    Route::get('/rekap/sikap', [RekapController::class, 'sikap'])->name('rekap.sikap');
    Route::get('/rekap/tugas', [RekapController::class, 'tugas'])->name('rekap.tugas');

    // Export
    Route::get('/export/nilai/excel', [ExportController::class, 'excelNilai'])->name('export.nilai.excel');
    Route::get('/export/nilai/pdf', [ExportController::class, 'pdfNilai'])->name('export.nilai.pdf');
    Route::get('/export/absensi/excel', [ExportController::class, 'excelAbsensi'])->name('export.absensi.excel');
    Route::get('/export/absensi/pdf', [ExportController::class, 'pdfAbsensi'])->name('export.absensi.pdf');
    Route::get('/export/tugas/excel', [ExportController::class, 'excelTugas'])->name('export.tugas.excel');
    Route::get('/export/tugas/pdf', [ExportController::class, 'pdfTugas'])->name('export.tugas.pdf');
    Route::get('/export/sikap/excel', [ExportController::class, 'excelSikap'])->name('export.sikap.excel');
    Route::get('/export/sikap/pdf', [ExportController::class, 'pdfSikap'])->name('export.sikap.pdf');

    // Sistem
    Route::get('/pengaturan-akun', [AccountSettingsController::class, 'edit'])->name('pengaturan-akun');
    Route::put('/pengaturan-akun', [AccountSettingsController::class, 'update'])->name('pengaturan-akun.update');
    Route::get('/school-settings', [SchoolSettingController::class, 'index'])->name('school-settings.index');
    Route::put('/school-settings', [SchoolSettingController::class, 'update'])->name('school-settings.update');
    Route::get('/log-login', [SystemController::class, 'logLogin'])->name('log-login');
    Route::get('/log-login/export/excel', [SystemController::class, 'exportLogLoginExcel'])->name('log-login.export.excel');
    Route::get('/log-error', [SystemController::class, 'logError'])->name('log-error');
    Route::get('/log-akademik', [AcademicAuditLogController::class, 'index'])->name('log-akademik');
    Route::get('/pengaturan', [SystemController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/pengaturan', [SystemController::class, 'savePengaturan'])->name('pengaturan.save');
    Route::get('/blocked-ips', [SystemController::class, 'blockedIps'])->name('blocked-ips');
    Route::delete('/blocked-ips/{blockedIp}', [SystemController::class, 'unblockIp'])->name('blocked-ips.unblock');
});

// GURU (role:guru)
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelas-mapel/{kelasMapel}', [KelasMapelWorkspaceController::class, 'show'])
        ->name('kelas-mapel.show')
        ->middleware('can:mengajar,kelasMapel');

    // Kalender
    Route::get('/kalender', [GuruKalenderController::class, 'index'])->name('kalender');
    Route::post('/kalender', [GuruKalenderController::class, 'store'])->name('kalender.store');
    Route::put('/kalender/{calendarEvent}', [GuruKalenderController::class, 'update'])->name('kalender.update');
    Route::delete('/kalender/{calendarEvent}', [GuruKalenderController::class, 'destroy'])->name('kalender.destroy');

    // Pengumuman
    Route::get('/pengumuman', [AdminPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'show'])->name('pengumuman.show');
    Route::post('/pengumuman', [AdminPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::delete('/pengumuman/{pengumuman}', [AdminPengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // Absensi
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{kelasMapel}/create', [AbsensiController::class, 'create'])->name('absensi.create')->middleware('can:mengajar,kelasMapel');
    Route::post('/absensi/{kelasMapel}/store', [AbsensiController::class, 'store'])->name('absensi.store')->middleware('can:mengajar,kelasMapel');
    Route::get('/absensi/{kelasMapel}/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap')->middleware('can:mengajar,kelasMapel');
    Route::get('/absensi/{kelasMapel}/export/excel', [ExportController::class, 'guruAbsensiExcel'])->name('absensi.export.excel')->middleware('can:mengajar,kelasMapel');
    Route::get('/absensi/{kelasMapel}/export/pdf', [ExportController::class, 'guruAbsensiPdf'])->name('absensi.export.pdf')->middleware('can:mengajar,kelasMapel');

    // Materi
    Route::get('/materi', [GuruMateriController::class, 'index'])->name('materi.index');
    Route::post('/materi/store', [GuruMateriController::class, 'storeBulk'])->name('materi.store.bulk');
    Route::get('/materi/{kelasMapel}/list', [GuruMateriController::class, 'list'])->name('materi.list')->middleware('can:mengajar,kelasMapel');
    Route::post('/materi/{kelasMapel}/store', [GuruMateriController::class, 'store'])->name('materi.store')->middleware('can:mengajar,kelasMapel');

    // Tugas
    Route::get('/tugas', [GuruTugasController::class, 'index'])->name('tugas.index');
    Route::post('/tugas/store', [GuruTugasController::class, 'storeBulk'])->name('tugas.store.bulk');
    Route::get('/tugas/{kelasMapel}/list', [GuruTugasController::class, 'list'])->name('tugas.list')->middleware('can:mengajar,kelasMapel');
    Route::post('/tugas/{kelasMapel}/store', [GuruTugasController::class, 'store'])->name('tugas.store')->middleware('can:mengajar,kelasMapel');
    Route::get('/tugas/{kelasMapel}/{tugas}/pengumpulan', [GuruTugasController::class, 'pengumpulan'])->name('tugas.pengumpulan')->middleware('can:mengajar,kelasMapel');

    // Nilai & Sikap
    Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::post('/nilai/store', [NilaiController::class, 'storeBulk'])->name('nilai.store.bulk');
    Route::get('/nilai/{kelasMapel}/input', [NilaiController::class, 'input'])->name('nilai.input')->middleware('can:mengajar,kelasMapel');
    Route::post('/nilai/{kelasMapel}/store', [NilaiController::class, 'store'])->name('nilai.store')->middleware('can:mengajar,kelasMapel');
    Route::get('/sikap', [SikapController::class, 'index'])->name('sikap.index');
    Route::post('/sikap/store', [SikapController::class, 'storeBulk'])->name('sikap.store.bulk');
    Route::get('/sikap/{kelasMapel}', [SikapController::class, 'indexKelas'])->name('sikap.kelas')->middleware('can:mengajar,kelasMapel');

    // Chat, Notifikasi, Wali Kelas
    Route::get('/chat', [GuruChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [GuruChatController::class, 'store'])->name('chat.store');
    Route::get('/notifikasi', [GuruNotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/read', [GuruNotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::get('/wali-kelas', [GuruWaliKelasController::class, 'index'])->name('wali-kelas.index');
    Route::post('/wali-kelas/{waliKelas}/siswa', [GuruWaliKelasController::class, 'storeSiswa'])->name('wali-kelas.siswa.store');
});

// SISWA (role:siswa)
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelas-mapel/{kelasMapel}', [SiswaKelasMapelWorkspaceController::class, 'show'])->name('kelas-mapel.show');
    Route::get('/kalender', [SiswaKalenderController::class, 'index'])->name('kalender');
    Route::get('/materi', [SiswaMateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/{kelasMapel}', [SiswaMateriController::class, 'list'])->name('materi.list');
    Route::get('/materi/{kelasMapel}/{materi}/download', [SiswaMateriController::class, 'download'])->name('materi.download');
    Route::get('/tugas', [SiswaTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{kelasMapel}/{tugas}', [SiswaTugasController::class, 'show'])->name('tugas.show');
    Route::post('/tugas/{kelasMapel}/{tugas}/submit', [SiswaTugasController::class, 'store'])->name('tugas.submit');
    Route::get('/tugas/{kelasMapel}/{tugas}/download/{file}', [SiswaTugasController::class, 'download'])->name('tugas.download');
    Route::get('/nilai', [SiswaNilaiController::class, 'index'])->name('nilai.index');
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/chat', [SiswaChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [SiswaChatController::class, 'store'])->name('chat.store');
    Route::get('/notifikasi', [SiswaNotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/read', [SiswaNotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::get('/pengumuman', [SiswaPengumumanController::class, 'index'])->name('pengumuman.index');
});

// KEPSEK (role:kepsek)
Route::middleware(['auth', 'role:kepsek'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [KepsekDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
    Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');
    Route::get('/laporan/sikap', [LaporanController::class, 'sikap'])->name('laporan.sikap');
    Route::get('/laporan/tugas', [LaporanController::class, 'tugas'])->name('laporan.tugas');
    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
    Route::get('/kalender', [KepsekKalenderController::class, 'index'])->name('kalender');
});

// ACCOUNT SETTINGS
Route::middleware('auth')->group(function () {
    Route::get('/pengaturan-akun', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::put('/pengaturan-akun', [AccountSettingsController::class, 'update'])->name('account.settings.update');
});
