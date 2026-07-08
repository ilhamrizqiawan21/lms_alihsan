# LMS Sekolah

LMS Sekolah adalah aplikasi Learning Management System single-school berbasis Laravel untuk sekolah, madrasah, atau lembaga pendidikan yang ingin menjalankan pembelajaran digital dari satu instalasi mandiri. Identitas sekolah, logo, favicon, warna tema, kepala sekolah, tahun ajaran, dan semester dapat diubah dari dashboard admin tanpa menyentuh kode.

Status produk: single-school LMS, bukan SaaS dan bukan multi-school.

## Teknologi

- PHP 8.3+
- Laravel 13.x
- MySQL 8.0 / MariaDB 10.6+
- Blade, Bootstrap 5, Vite
- DomPDF untuk PDF
- OpenSpout untuk export Excel

## Fitur Utama

- Dashboard untuk admin, kepala sekolah, guru, dan siswa
- Manajemen pengguna, role, kelas, siswa, mata pelajaran, dan guru pengampu
- Tahun ajaran dan semester aktif
- Absensi siswa per kelas-mapel
- Materi pembelajaran dan upload file
- Tugas, pengumpulan tugas, multi-file jawaban, nilai, dan catatan guru
- Penilaian akademik, sikap spiritual, dan sikap sosial
- Chat kelas, notifikasi, pengumuman, dan kalender
- Rekap admin dan laporan kepala sekolah
- Export PDF dan Excel dengan kop sekolah dinamis
- Pengaturan branding sekolah dari dashboard admin
- Seeder demo aman dan seeder produk kosong

## Role Pengguna

| Role | Akses Utama |
|---|---|
| Admin | Mengelola user, kelas, mapel, siswa, penugasan guru, pengaturan sekolah, sistem, rekap, dan export |
| Kepala Sekolah | Melihat dashboard, statistik, kalender, pengumuman, dan laporan akademik |
| Guru | Mengelola absensi, materi, tugas, nilai, sikap, chat, dan notifikasi kelas yang diampu |
| Siswa | Melihat materi, mengumpulkan tugas, melihat nilai/progress, chat, kalender, dan notifikasi |

## Instalasi Singkat

```bash
git clone <repository-url> lms_school
cd lms_school

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Atur database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_school
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan setup database dan asset:

```bash
php artisan migrate --seed
npm run build
php artisan storage:link
php artisan serve
```

Aplikasi lokal tersedia di:

```text
http://127.0.0.1:8000
```

> Penting: file `.env` berisi konfigurasi lingkungan dan kredensial rahasia. File ini tidak diikutkan ke repositori agar data sensitif tidak bocor. Gunakan `.env.example` sebagai template.

Panduan lengkap tersedia di [docs/INSTALLATION.md](docs/INSTALLATION.md).

## Akun Demo

Seeder demo default membuat akun berikut:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.test` | `password` |
| Guru | `guru@demo.test` | `password` |
| Siswa | `siswa@demo.test` | `password` |
| Kepala Sekolah | `kepsek@demo.test` | `password` |

Untuk instalasi produk kosong tanpa data guru, siswa, kelas, mapel, materi, tugas, absensi, dan nilai demo:

```bash
php artisan migrate:fresh --seeder=EmptyProductSeeder
```

Mode kosong hanya membuat role default, akun admin, pengaturan sekolah default, tahun ajaran default, dan semester aktif. Akun admin awal mengikuti `DEFAULT_ADMIN_USERNAME`, `DEFAULT_ADMIN_EMAIL`, `DEFAULT_ADMIN_PASSWORD`, dan `DEFAULT_ADMIN_NAME` di `.env`.

## Custom Nama Sekolah dan Logo

Login sebagai admin, lalu buka:

```text
Admin > Pengaturan
```

Dari halaman tersebut admin dapat mengubah:

- Nama sekolah dan nama pendek
- Logo dan favicon
- Alamat dan kontak sekolah
- Kepala sekolah
- Tahun ajaran dan semester
- Warna utama, sekunder, sidebar, dan navbar
- Visi, misi, motto, dan data legal sekolah

Perubahan branding digunakan oleh login, layout aplikasi, favicon, laporan cetak, PDF, dan Excel. Panduan detail tersedia di [docs/CUSTOM_BRANDING.md](docs/CUSTOM_BRANDING.md).

## Dokumentasi

- [Installation Guide](docs/INSTALLATION.md)
- [Custom Branding](docs/CUSTOM_BRANDING.md)
- [Security Check Result](docs/SECURITY_CHECK_RESULT.md)
- [Commercial Readiness Checklist](docs/COMMERCIAL_READY_CHECKLIST.md)
- [Manual Test Result](docs/MANUAL_TEST_RESULT.md)

## Catatan Production

- Ubah semua password default setelah instalasi.
- Set `APP_ENV=production` dan `APP_DEBUG=false`.
- Jalankan `php artisan optimize`.
- Pastikan `storage/` dan `bootstrap/cache/` writable oleh web server.
- Rotasi credential lokal sebelum distribusi atau pemasangan ke klien.

## Lisensi Komersial

Kode dapat digunakan sebagai basis produk LMS single-school untuk instalasi sekolah/klien. Atur skema lisensi, kontrak support, dan hak distribusi sesuai perjanjian komersial Anda.

## Kontak Developer

Isi kontak developer atau tim support Anda di bagian ini sebelum diserahkan ke calon pembeli.

```text
Nama Developer/Tim:
Email:
WhatsApp:
Website:
```
