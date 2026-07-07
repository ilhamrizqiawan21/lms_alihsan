# LMS MTs. Al-Ihsan Batujajar

Aplikasi Learning Management System (LMS) berbasis Laravel untuk mendukung proses pembelajaran digital di MTs. Al-Ihsan Batujajar. Sistem ini membantu administrator, kepala sekolah, guru, dan siswa mengelola pembelajaran secara terpusat, mulai dari absensi, materi, tugas, penilaian, sikap, hingga komunikasi kelas.

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?logo=vite&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Role dan Hak Akses](#role-dan-hak-akses)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Prasyarat](#prasyarat)
- [Panduan Instalasi](#panduan-instalasi)
- [Perintah Umum](#perintah-umum)
- [Akses Default](#akses-default)
- [Lisensi](#lisensi)

---

## Tentang Proyek

LMS ini dirancang khusus untuk mendukung implementasi Kurikulum Merdeka di lingkungan madrasah. Aplikasi ini memadukan berbagai kebutuhan operasional sekolah dalam satu sistem, sehingga guru dapat mengelola kelas lebih efektif, siswa dapat mengakses pembelajaran secara mandiri, dan pimpinan sekolah dapat memantau perkembangan akademik dengan lebih mudah.

Beberapa keunggulan utama aplikasi ini antara lain:

- Terintegrasi untuk admin, guru, kepala sekolah, dan siswa
- Mendukung manajemen akademik, absensi, tugas, nilai, dan sikap
- Dilengkapi notifikasi untuk mempermudah komunikasi
- Disusun dengan arsitektur Laravel yang rapi dan mudah dikembangkan

---

## Fitur Utama

### Manajemen Akademik
- Mengelola tahun ajaran, kelas, mata pelajaran, dan penugasan guru
- Menyusun pembagian kelas dan data siswa secara terstruktur

### User dan Hak Akses
- Mendukung multi-role: admin, kepala sekolah, guru, dan siswa
- Setiap role memiliki akses yang berbeda sesuai kebutuhan

### Absensi
- Input absensi per kelas dan per minggu
- Status absensi: hadir, sakit, izin, dan alpha

### Materi Pembelajaran
- Guru dapat mengunggah materi pelajaran dalam berbagai format file
- Siswa dapat melihat dan mengunduh materi yang tersedia

### Tugas dan Pengumpulan
- Guru dapat membuat tugas dengan deadline
- Siswa dapat mengunggah jawaban beserta file pendukung
- Guru dapat memberikan nilai dan catatan penilaian

### Penilaian Kurikulum Merdeka
- Mendukung komponen nilai seperti SUM1–SUM4, STS, SAS, SAT, dan nilai harian
- Menyediakan predikat nilai otomatis

### Sikap Spiritual dan Sosial
- Input penilaian KI-1 dan KI-2
- Mendukung rekap nilai sikap per siswa

### Komunikasi dan Notifikasi
- Chat kelas antar guru dan siswa
- Notifikasi untuk tugas, nilai, chat, absensi, dan pengumpulan

### Pengumuman, Kalender, dan Laporan
- Pengumuman terarah sesuai role atau kelas tertentu
- Kalender sekolah dan event personal
- Dashboard dan statistik untuk masing-masing role

---

## Role dan Hak Akses

| Role | Akses Utama |
|---|---|
| Administrator | Mengelola pengguna, kelas, mata pelajaran, data sekolah, pengaturan sistem, dan seluruh laporan |
| Kepala Sekolah | Melihat dashboard, statistik, rekap absensi, nilai, tugas, dan sikap |
| Guru | Mengelola absensi, materi, tugas, nilai, sikap, dan komunikasi dengan siswa |
| Siswa | Mengakses materi, mengerjakan tugas, melihat nilai, dan berkomunikasi dengan guru |

---

## Teknologi yang Digunakan

### Backend
- PHP 8.3+
- Laravel 13.x
- MySQL 8.0 / MariaDB 10.6+ (atau SQLite untuk pengembangan lokal)

### Frontend
- Blade Template
- Bootstrap 5.3
- Vite
- Tailwind CSS
- jQuery, DataTables, Select2

---

## Prasyarat

Pastikan lingkungan Anda sudah memiliki:

- PHP 8.3 atau lebih tinggi
- Composer
- Node.js 20+ dan npm
- Database MySQL/MariaDB, atau SQLite untuk pengembangan sederhana
- Extension PHP berikut: bcmath, ctype, curl, dom, fileinfo, filter, gd, hash, json, mbstring, openssl, pcre, pdo, pdo_mysql, session, tokenizer, xml, zip

---

## Panduan Instalasi

### 1. Clone repository

```bash
git clone <repository-url>
cd lms.didzacorp.com
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Siapkan file environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi database

Secara default, file environment sudah menggunakan SQLite. Jika ingin menggunakan SQLite, buat file database terlebih dahulu:

```bash
touch database/database.sqlite
```

Jika ingin menggunakan MySQL, ubah nilai `.env` sesuai konfigurasi server Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_alihsan
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi dan seeder

```bash
php artisan migrate --seed
```

Seeder akan membuat akun demo default berikut:

- Admin: `admin / admin123`
- Guru: `guru / guru123`
- Siswa: `siswa / siswa123`
- Kepala Sekolah: `kepsek / kepsek123`

### 6. Install dependency frontend

```bash
npm install
```

### 7. Build asset frontend

```bash
npm run build
```

### 8. Buat link storage

```bash
php artisan storage:link
```

### 9. Jalankan aplikasi

```bash
php artisan serve
```

Akses aplikasi melalui browser di alamat:

```text
http://127.0.0.1:8000
```

> Untuk fitur notifikasi yang lebih lengkap, jalankan queue worker juga:
>
> ```bash
> php artisan queue:listen --tries=1 --timeout=0
> ```

---

## Perintah Umum

```bash
# Jalankan test
php artisan test

# Jalankan development server + asset watcher
npm run dev

# Jalankan queue worker
php artisan queue:listen --tries=1 --timeout=0
```

---

## Akses Default

Setelah menjalankan seeder, Anda dapat login menggunakan akun demo berikut:

| Role | Username | Password |
|---|---|---|
| Administrator | admin | admin123 |
| Guru | guru | guru123 |
| Siswa | siswa | siswa123 |
| Kepala Sekolah | kepsek | kepsek123 |

> Sebaiknya ubah password default setelah proses instalasi selesai.

---

## Lisensi

Proyek ini dilisensikan di bawah lisensi MIT.


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
