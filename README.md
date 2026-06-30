<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white" alt="Laravel 13.x">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white" alt="MySQL 8.0">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white" alt="Bootstrap 5.3">
  <img src="https://img.shields.io/badge/Vite-8.x-646CFF?logo=vite&logoColor=white" alt="Vite 8.x">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4.x">
  <img src="https://img.shields.io/badge/license-MIT-green" alt="License MIT">
</p>

<h1 align="center">📚 LMS MTs. Al-Ihsan Batujajar</h1>
<p align="center"><strong>Learning Management System</strong> berbasis <strong>Kurikulum Merdeka</strong><br>untuk Madrasah Tsanawiyah Al-Ihsan Batujajar</p>

---

## 📖 Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Role & Hak Akses](#role--hak-akses)
- [Teknologi & Bahasa](#teknologi--bahasa)
- [Spesifikasi Teknis](#spesifikasi-teknis)
- [Struktur Database](#struktur-database)
- [Prasyarat Sistem](#prasyarat-sistem)
- [Panduan Instalasi](#panduan-instalasi)
  - [A. Instalasi Cepat (Laragon)](#a-instalasi-cepat-laragon)
  - [B. Instalasi Manual](#b-instalasi-manual)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Direktori](#struktur-direktori)
- [Sistem Notifikasi](#sistem-notifikasi)
- [Keunggulan](#keunggulan)
- [Lisensi](#lisensi)

---

## Tentang Proyek

**LMS MTs. Al-Ihsan Batujajar** adalah platform pembelajaran digital *(Learning Management System)* yang dirancang khusus untuk mendukung implementasi **Kurikulum Merdeka** di MTs. Al-Ihsan Batujajar. Sistem ini menghubungkan **Administrator**, **Kepala Sekolah**, **Guru**, dan **Siswa** dalam satu ekosistem digital terpadu untuk mengelola seluruh proses pembelajaran — mulai dari penjadwalan, absensi, materi, tugas, penilaian, sikap, hingga komunikasi.

Dibangun di atas **Laravel 13** (rilis terbaru), sistem ini mengadopsi arsitektur **MVC** yang bersih dengan **Service Layer** untuk logika bisnis, **Policy-based Authorization**, dan **migrasi database** yang terstruktur.

---

## Fitur Utama

### 🏫 Manajemen Akademik

| Fitur | Deskripsi |
|-------|-----------|
| **Tahun Ajaran** | Kelola tahun ajaran, set tahun aktif |
| **Kelas** | Buat dan kelola kelas (VII, VIII, IX A/B/C...) |
| **Mata Pelajaran** | Daftar mapel dengan kode dan urutan |
| **Penugasan Guru** | Assign guru ke kelas + mapel + semester (KelasMapel) |
| **Kelas & Siswa** | Tempatkan siswa ke dalam kelas, kelola status (aktif/lulus/keluar) |

### 👤 Manajemen Pengguna

| Fitur | Deskripsi |
|-------|-----------|
| **Multi-Role User** | 4 role: Admin, Kepala Sekolah, Guru, Siswa |
| **CRUD User** | Admin dapat membuat, mengedit, menonaktifkan user |
| **Profil** | Setiap role dapat mengedit profil sendiri (nama, username, password) |
| **Login History** | Pencatatan log login — IP, user agent, waktu |
| **Keamanan Login** | Rate limiting & IP blocking untuk mencegah brute-force |

### 📅 Absensi

| Fitur | Deskripsi |
|-------|-----------|
| **Input Per Minggu** | Guru mengisi absensi per minggu dalam satu bulan |
| **4 Status** | Hadir, Sakit, Izin, Alpha |
| **Rekap Absensi** | Admin & Kepsek melihat rekap absensi per kelas |
| **Notifikasi** | Siswa mendapat notifikasi saat absensi dicatat |

### 📖 Materi Pembelajaran

| Fitur | Deskripsi |
|-------|-----------|
| **Upload Materi** | Guru upload file materi (PDF, DOC, PPT, dll.) per kelas-mapel |
| **Download** | Siswa mengunduh materi dari guru masing-masing |
| **Deskripsi** | Setiap materi dilengkapi judul dan deskripsi |

### 📝 Tugas & Pengumpulan

| Fitur | Deskripsi |
|-------|-----------|
| **Buat Tugas** | Guru membuat tugas dengan judul, deskripsi, deadline |
| **Upload Jawaban** | Siswa upload file jawaban (PDF, DOC, gambar, ZIP) + teks |
| **Multi-File** | Satu pengumpulan bisa beberapa file |
| **Penilaian** | Guru memberi nilai (0-100) + catatan per pengumpulan |
| **Kategori Nilai** | NH (Nilai Harian), STS, SAS, SAT |
| **Notifikasi** | Siswa notifikasi tugas baru; Guru notifikasi tugas terkumpul |

### 📊 Penilaian Kurikulum Merdeka

| Fitur | Deskripsi |
|-------|-----------|
| **Komponen Lengkap** | SUM1–SUM4, STS, SAS, SAT, Nilai Harian |
| **Rata-rata Otomatis** | MySQL generated column menghitung rata-rata akhir |
| **Predikat** | A (Sangat Baik), B (Baik), C (Cukup), D (Kurang) |
| **Notifikasi** | Siswa mendapat notifikasi saat nilai diinput |

### 🧠 Sikap (KI-1 & KI-2)

| Fitur | Deskripsi |
|-------|-----------|
| **Sikap Spiritual (KI-1)** | Taqwa, Kejujuran, Disiplin, Sabar, Syukur, Tawadhu |
| **Sikap Sosial (KI-2)** | Empati, Kerjasama, Toleransi, Percaya Diri, Komunikasi |
| **Skala 1–5** | Penilaian per indikator sikap |
| **Rekap Sikap** | Guru & Kepsek melihat rekap sikap per siswa |

### 💬 Chat Kelas

| Fitur | Deskripsi |
|-------|-----------|
| **Real-time Chat** | Komunikasi antara guru dan siswa per kelas-mapel |
| **Read Status** | Tandai pesan sudah dibaca |
| **Notifikasi** | Notifikasi dua arah: guru ↔ siswa saat ada pesan baru |

### 🔔 Notifikasi Real-time

| Fitur | Deskripsi |
|-------|-----------|
| **Bell Icon** | Lonceng di topbar dengan badge jumlah unread |
| **Dropdown** | 5 notifikasi terbaru langsung dari topbar |
| **Halaman Penuh** | Riwayat semua notifikasi dengan paginasi |
| **Ikon Per Tipe** | Ikon berbeda untuk tugas, nilai, chat, absensi, pengumpulan |
| **Tandai Dibaca** | Per notifikasi atau semua sekaligus |
| **Auto-Redirect** | Klik notifikasi langsung ke halaman terkait |

Tipe notifikasi: `tugas_baru` · `nilai_baru` · `chat_baru` · `kumpul_tugas` · `absensi`

### 📢 Pengumuman

| Fitur | Deskripsi |
|-------|-----------|
| **Target Spesifik** | Semua, Guru, Siswa, atau Kelas-Mapel tertentu |
| **Dashboard** | Pengumuman tampil di dashboard setiap role |

### 📅 Kalender & Reminder

| Fitur | Deskripsi |
|-------|-----------|
| **Event Sekolah** | Hari libur, ujian, kegiatan sekolah |
| **Personal Event** | Event pribadi per user |
| **Toggle Done** | Tandai event selesai |
| **Semua Role** | Admin, Guru, Kepsek, dan Siswa punya kalender |

### 📈 Laporan & Statistik

| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard Statistik** | Statistik ringkas per role |
| **Rekap Absensi** | Admin & Kepsek |
| **Rekap Nilai** | Admin, Guru & Kepsek |
| **Rekap Tugas** | Admin & Kepsek |
| **Rekap Sikap** | Admin, Guru & Kepsek |
| **Progress Siswa** | Siswa melihat progress belajar sendiri |

### ⚙️ Sistem

| Fitur | Deskripsi |
|-------|-----------|
| **Pengaturan** | Key-value settings (semester aktif, warna tema, dll.) |
| **3 Tema Warna** | Hijau, Biru Azure, Biru Aqua |
| **Log Error** | Pencatatan error sistem dengan trace |
| **Log Login** | Riwayat login semua user |
| **IP Blocking** | Manajemen IP yang diblokir |
| **Dashboard Widget** | Widget dashboard yang bisa diatur per user |

---

## Role & Hak Akses

### 👑 Administrator
- Manajemen penuh: user, kelas, mapel, tahun ajaran, penugasan guru, siswa
- Rekap seluruh data: absensi, nilai, tugas, sikap
- Pengaturan sistem: tema, semester aktif, IP blocking
- Monitoring: log login, log error

### 🏫 Kepala Sekolah
- Dashboard monitoring
- Kalender & reminder sekolah
- Laporan: absensi, nilai, tugas, sikap
- Statistik menyeluruh

### 👨‍🏫 Guru
- Dashboard dengan statistik mengajar
- Mengelola absensi per kelas-mapel
- Upload materi pembelajaran
- Membuat dan menilai tugas
- Input nilai (SUM1–4, STS, SAS, SAT, NH)
- Input sikap spiritual & sosial
- Chat dengan siswa per kelas
- Melihat rekap nilai & sikap
- Menerima notifikasi: tugas terkumpul, chat dari siswa

### 🎓 Siswa
- Dashboard dengan progress belajar
- Melihat dan mengunduh materi
- Mengerjakan dan mengumpulkan tugas
- Melihat nilai sendiri
- Chat dengan guru
- Kalender & progress
- Menerima notifikasi: tugas baru, nilai baru, absensi, chat dari guru

---

## Teknologi & Bahasa

### Backend
| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **PHP** | 8.3+ | Bahasa pemrograman utama |
| **Laravel** | 13.x | Framework MVC |
| **MySQL** | 8.0 | Database relasional |
| **Carbon** | 3.x | Manipulasi tanggal & waktu |

### Frontend
| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| **Blade** | — | Template engine Laravel |
| **Bootstrap** | 5.3 | Framework CSS |
| **Bootstrap Icons** | 1.11 | Icon library |
| **Tailwind CSS** | 4.x | Utility-first CSS |
| **Vite** | 8.x | Build tool & dev server |
| **DataTables** | 1.13 | Tabel interaktif |
| **Select2** | 4.1 | Enhanced select dropdown |
| **jQuery** | 3.x | DOM manipulation (via Bootstrap) |

### Bahasa
| Aspek | Bahasa |
|-------|--------|
| **Kode (PHP/JS)** | Inggris (standar Laravel) |
| **Antarmuka (UI)** | Bahasa Indonesia |
| **Komentar Kode** | Campuran Indonesia & Inggris |
| **Database** | Bahasa Indonesia (nama tabel/kolom) |
| **Dokumentasi** | Bahasa Indonesia |

---

## Spesifikasi Teknis

- **Framework**: Laravel 13.8
- **PHP Minimum**: 8.3
- **Database**: MySQL 8.0 / MariaDB 10.6+
- **Web Server**: Apache (via Laragon) atau Nginx
- **Arsitektur**: MVC + Service Layer + Policy Authorization
- **Autentikasi**: Session-based dengan middleware `auth` + `role`
- **Frontend Build**: Vite 8 + Tailwind CSS 4
- **Environment**: Development (Laragon) / Production (Linux VPS)
- **OS Development**: Windows 10/11 dengan Laragon
- **Total Tabel Database**: 20+ tabel
- **Total Models**: 24 model Eloquent
- **Total Controllers**: 30+ controller (4 role area)

---

## Struktur Database

### Tabel Master
```
roles             — Role pengguna (admin, guru, siswa, kepala_sekolah)
users             — Data user/login semua role
tahun_ajaran      — Tahun ajaran (contoh: 2026/2027)
kelas             — Kelas (VII-A, VIII-B, dsb.)
mata_pelajaran    — Mata pelajaran
guru_mapel        — Pivot guru ↔ mata pelajaran
kelas_mapel       — Pivot kelas ↔ mapel + guru + tahun ajaran + semester
siswa             — Data siswa (NIS, kelas, status)
```

### Tabel Akademik
```
absensi              — Absensi siswa per kelas-mapel-tanggal
materi               — Materi pembelajaran (upload file)
tugas                — Tugas dengan deadline
pengumpulan_tugas    — Pengumpulan tugas oleh siswa
pengumpulan_files    — Multi-file dalam satu pengumpulan
nilai_akhir          — Nilai akhir Kurikulum Merdeka (generated rata_akhir)
sikap_spiritual      — Penilaian KI-1 (6 indikator)
sikap_sosial         — Penilaian KI-2 (5 indikator)
```

### Tabel Pendukung
```
notifikasi           — Notifikasi real-time (6+ tipe)
chat_messages        — Pesan chat guru ↔ siswa
pengumuman           — Pengumuman dengan target
calendar_events      — Event kalender sekolah & personal
log_login            — Riwayat login
login_attempts       — Percobaan login (rate limiting)
blocked_ips          — IP yang diblokir
system_errors        — Log error sistem
pengaturan           — Key-value settings
dashboard_widgets    — Widget dashboard per user
```

---

## Prasyarat Sistem

Sebelum instalasi, pastikan environment Anda memiliki:

- ✅ **PHP 8.3** atau lebih tinggi
- ✅ **Composer** (PHP dependency manager)
- ✅ **MySQL 8.0** atau **MariaDB 10.6+**
- ✅ **Node.js 20+** dan **npm** (untuk build frontend)
- ✅ **Git** (opsional, untuk clone repository)
- ✅ **Laragon** (disarankan untuk Windows) atau **XAMPP** / **Docker**

### Ekstensi PHP yang Diperlukan
```
bcmath, ctype, curl, dom, fileinfo, filter, gd, hash,
json, mbstring, openssl, pcre, pdo, pdo_mysql, session,
tokenizer, xml, zip
```

---

## Panduan Instalasi

### A. Instalasi Cepat (Laragon)

> 💡 **Direkomendasikan** untuk pengembangan di Windows.

1. **Pastikan Laragon sudah terinstall** dengan PHP 8.3+ dan MySQL 8.0

2. **Clone atau salin project** ke folder Laragon:
   ```bash
   cd C:\laragon\www
   git clone <repository-url> lms_alihsan
   # atau ekstrak zip ke C:\laragon\www\lms_alihsan
   ```

3. **Buka terminal** di folder project (`C:\laragon\www\lms_alihsan`)

4. **Install dependency PHP:**
   ```bash
   composer install
   ```

5. **Salin file environment:**
   ```bash
   copy .env.example .env
   ```

6. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

7. **Konfigurasi database** di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lms_alihsan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

8. **Buat database** di phpMyAdmin / Adminer:
   - Buka `http://localhost/phpmyadmin`
   - Buat database baru: `lms_alihsan`
   - Pilih collation: `utf8mb4_general_ci`

9. **Import database dari SQL dump:**
   - Buka database `lms_alihsan` di phpMyAdmin
   - Tab **Import** → pilih file `lms_alihsan_btr.sql`
   - Klik **Go**

   > ⚠️ Jika ada error foreign key, pastikan import dilakukan setelah database kosong.

10. **Atau jalankan migrasi (opsional, database bersih):**
    ```bash
    php artisan migrate --seed
    ```
    > Ini akan membuat struktur tabel dari migration + menjalankan seeder.

11. **Install dependency frontend:**
    ```bash
    npm install
    ```

12. **Build frontend assets:**
    ```bash
    npm run build
    ```

13. **Buat symbolic link untuk storage:**
    ```bash
    php artisan storage:link
    ```

14. **Jalankan aplikasi** melalui Laragon:
    - Klik **Start All** di Laragon
    - Akses `http://lms_alihsan.test` atau `http://localhost/lms_alihsan`

### B. Instalasi Manual

<details>
<summary><strong>Klik untuk melihat langkah instalasi manual (Linux/macOS/Docker)</strong></summary>

1. **Clone repository:**
   ```bash
   git clone <repository-url> lms_alihsan
   cd lms_alihsan
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi `.env`** sesuai server Anda (database, URL, dll.)

5. **Buat database MySQL:**
   ```sql
   CREATE DATABASE lms_alihsan CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

6. **Import SQL dump atau jalankan migrasi:**
   ```bash
   mysql -u root -p lms_alihsan < lms_alihsan_btr.sql
   # atau
   php artisan migrate --seed
   ```

7. **Build & link:**
   ```bash
   npm run build
   php artisan storage:link
   ```

8. **Set permission (Linux/macOS):**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

9. **Konfigurasi web server** (Nginx/Apache) ke `public/` directory

10. **Jalankan:**
    ```bash
    php artisan serve
    ```

</details>

---

## Menjalankan Aplikasi

### Development Mode

```bash
# Terminal 1 — Laravel dev server
php artisan serve

# Terminal 2 — Vite hot reload (frontend)
npm run dev

# Atau jalankan sekaligus (jika menggunakan Laragon: klik Start All)
```

### Production Mode

```bash
# Build optimized assets
npm run build

# Optimize Laravel
php artisan optimize

# Gunakan web server (Nginx/Apache) untuk production
```

### Akun Default

Setelah import SQL dump, akun default tersedia:

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | *(sesuai database)* |
| Guru | `ilhamzp` | *(sesuai database)* |
| Kepala Sekolah | `196808111994032001` | *(sesuai database)* |

> 🔒 **Ganti password default segera setelah instalasi!**

---

## Struktur Direktori

```
lms_alihsan/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # 11 controller admin
│   │   ├── Auth/           # Login controller
│   │   ├── Guru/           # 10 controller guru
│   │   ├── Kepsek/         # 4 controller kepsek
│   │   └── Siswa/          # 9 controller siswa
│   ├── Models/             # 24 model Eloquent
│   ├── Policies/           # Policy authorization
│   ├── Providers/          # Service providers
│   └── Services/           # 4 service classes
├── bootstrap/              # Laravel bootstrap
├── config/                 # 10 file konfigurasi
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # 8 file migrasi
│   └── seeders/            # Database seeders
├── public/                 # Web root (index.php, assets)
├── resources/
│   ├── css/                # CSS kustom
│   ├── js/                 # JavaScript
│   └── views/              # Blade templates
│       ├── admin/          # ~20 view admin
│       ├── auth/           # Login view
│       ├── guru/           # ~15 view guru
│       ├── kepsek/         # ~5 view kepsek
│       ├── layouts/        # Main layout + sidebar
│       └── siswa/          # ~10 view siswa
├── routes/
│   ├── web.php             # 230+ route lines
│   └── console.php         # Artisan commands
├── storage/                # Logs, cache, uploads
├── tests/                  # Unit & Feature tests
├── vendor/                 # Composer dependencies
├── .env.example            # Environment template
├── artisan                 # Laravel CLI
├── composer.json           # PHP dependencies
├── lms_alihsan_btr.sql     # SQL dump (database siap pakai)
├── package.json            # NPM dependencies
├── vite.config.js          # Vite configuration
└── README.md               # Dokumentasi ini
```

---

## Sistem Notifikasi

Sistem notifikasi LMS Al-Ihsan menggunakan arsitektur **trigger-based** yang terintegrasi langsung ke dalam alur bisnis aplikasi.

### Arsitektur

```
┌──────────────────────────────────────────────────────────┐
│                    TRIGGER POINTS                         │
│                                                          │
│  AbsensiController@store  ──►  NotifikasiService         │
│  NilaiController@store    ──►  NotifikasiService         │
│  TugasController@store    ──►  NotifikasiService         │
│  SiswaTugasController     ──►  NotifikasiService         │
│  GuruChatController       ──►  NotifikasiService         │
│  SiswaChatController      ──►  NotifikasiService         │
│                                                          │
└──────────────────────┬───────────────────────────────────┘
                       ▼
┌──────────────────────────────────────────────────────────┐
│              TABEL notifikasi (MySQL)                     │
│  id | user_id | tipe | judul | pesan | link | is_read    │
└──────────────────────┬───────────────────────────────────┘
                       ▼
┌──────────────────────────────────────────────────────────┐
│                     UI LAYER                              │
│  ┌─────────────┐  ┌──────────────┐  ┌─────────────────┐  │
│  │ Bell Topbar  │  │ Sidebar Menu │  │ Notif Page      │  │
│  │ + Badge      │  │ + Badge      │  │ + Mark All Read │  │
│  └─────────────┘  └──────────────┘  └─────────────────┘  │
└──────────────────────────────────────────────────────────┘
```

### Tipe Notifikasi

| Tipe | Ikon | Pemicu | Penerima |
|------|------|--------|----------|
| `tugas_baru` | 📝 | Guru membuat tugas | Semua siswa di kelas |
| `nilai_baru` | 📊 | Guru menyimpan nilai | Siswa terkait |
| `chat_baru` | 💬 | Guru/siswa kirim chat | Pihak satunya |
| `kumpul_tugas` | ✅ | Siswa kumpul tugas | Guru pengampu |
| `absensi` | 📋 | Guru simpan absensi | Siswa yang diabsen |

### Endpoint API Notifikasi

```
GET   /guru/notifikasi              — Halaman notifikasi guru
POST  /guru/notifikasi/{id}/read    — Tandai satu dibaca + redirect
POST  /guru/notifikasi/mark-all-read — Tandai semua dibaca

GET   /siswa/notifikasi             — Halaman notifikasi siswa
POST  /siswa/notifikasi/{id}/read   — Tandai satu dibaca + redirect
POST  /siswa/notifikasi/mark-all-read — Tandai semua dibaca
```

---

## Keunggulan

### 🎯 Dibangun untuk Kurikulum Merdeka
- Komponen penilaian lengkap: SUM1–4, STS, SAS, SAT, Nilai Harian
- Penilaian sikap spiritual (KI-1) dan sosial (KI-2)
- Rata-rata akhir dihitung otomatis oleh database

### 🔒 Keamanan Berlapis
- Policy-based authorization (setiap aksi dicek izinnya)
- Rate limiting & IP blocking untuk brute-force protection
- Middleware `auth` + `role` di setiap route group
- CSRF protection bawaan Laravel
- Password hashing dengan bcrypt

### 🎨 UI/UX Modern
- 3 pilihan tema warna (Hijau, Biru Azure, Biru Aqua)
- Sidebar responsif dengan mobile toggle
- DataTables untuk tabel interaktif
- Select2 untuk dropdown yang bisa dicari
- Toast notification untuk feedback aksi
- Modal konfirmasi untuk aksi destruktif

### 🏗️ Arsitektur Bersih
- **MVC + Service Layer**: logika bisnis di service, bukan di controller
- **Policy Authorization**: aturan akses terpusat per model
- **Eloquent ORM**: relasi database yang ekspresif
- **Database Migrations**: version control untuk skema database
- **Generated Column**: rata-rata nilai dihitung di level database (MySQL)

### 📱 Responsif
- Fully responsive: desktop, tablet, dan mobile
- Sidebar auto-collapse di layar kecil
- Tabel bisa di-scroll horizontal di mobile
- Font dan spacing dioptimalkan untuk berbagai ukuran layar

### 🚀 Performa
- Vite 8 untuk build frontend yang cepat
- Composer `optimize-autoloader` untuk autoloading yang efisien
- Eager loading relations untuk menghindari N+1 query
- Cache ready (route, config, view caching)

### 📦 Siap Pakai
- SQL dump lengkap dengan data contoh
- Database seeder untuk testing
- Environment template (`.env.example`)
- Script `composer setup` untuk instalasi otomatis

---

## Lisensi

Proyek ini adalah **perangkat lunak sumber terbuka** yang dilisensikan di bawah [Lisensi MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  <strong>MTs. Al-Ihsan Batujajar</strong><br>
  <em>Digitalisasi Pembelajaran — Kurikulum Merdeka</em><br>
  <sub>© 2026 — Dibangun dengan Laravel 13 & 💚</sub>
</p>
