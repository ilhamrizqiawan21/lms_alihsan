# INSTALLATION.md

Panduan ini menjelaskan instalasi LMS Sekolah dari nol untuk satu sekolah.

## Requirement Server

- PHP 8.3 atau lebih baru
- Composer
- Node.js 20+ dan npm
- MySQL 8.0 / MariaDB 10.6+
- Web server Nginx/Apache untuk production
- Extension PHP: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`

## Clone Project

```bash
git clone <repository-url> lms_school
cd lms_school
```

## Install Dependency PHP

```bash
composer install
```

Untuk production:

```bash
composer install --no-dev --optimize-autoloader
```

## Install Dependency Frontend

```bash
npm install
```

## Siapkan Environment

```bash
cp .env.example .env
php artisan key:generate
```

> Catatan: file `.env` berisi konfigurasi lingkungan, kunci aplikasi, dan kredensial database. File ini tidak dikomit ke repositori untuk menjaga kerahasiaan dan keamanan.

## Setting Database

Buat database MySQL:

```sql
CREATE DATABASE lms_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atur `.env`:

```env
APP_NAME="LMS Sekolah"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_school
DB_USERNAME=root
DB_PASSWORD=

DEFAULT_ADMIN_USERNAME=admin
DEFAULT_ADMIN_EMAIL=admin@example.test
DEFAULT_ADMIN_PASSWORD=ganti-password-kuat
DEFAULT_ADMIN_NAME="Administrator"
```

Untuk production, gunakan:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-sekolah.example
```

## Migrasi dan Seeder

Untuk instalasi dengan data demo aman:

```bash
php artisan migrate --seed
```

Untuk instalasi kosong siap input data sekolah:

```bash
php artisan migrate:fresh --seeder=EmptyProductSeeder
```

## Build Asset

```bash
npm run build
```

## Storage Link

```bash
php artisan storage:link
```

Pastikan folder berikut writable:

```bash
storage
bootstrap/cache
```

## Jalankan Aplikasi

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

## Login Admin

Jika memakai seeder demo:

```text
Email: admin@demo.test
Password: password
```

Jika memakai `EmptyProductSeeder`, akun admin awal mengikuti nilai `DEFAULT_ADMIN_*` di `.env`:

```text
Username: DEFAULT_ADMIN_USERNAME
Email: DEFAULT_ADMIN_EMAIL
Password: DEFAULT_ADMIN_PASSWORD
```

Segera ubah password setelah login pertama.

## Ubah Identitas Sekolah

Setelah login admin:

1. Buka menu `Pengaturan`.
2. Isi nama sekolah, alamat, kepala sekolah, tahun ajaran, semester, dan kontak.
3. Upload logo dan favicon.
4. Simpan pengaturan.
5. Refresh halaman dan cek login, navbar, sidebar, footer, laporan, PDF, dan Excel.

## Production Checklist Singkat

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Gunakan `APP_KEY` baru hasil `php artisan key:generate`
- Gunakan database dan user database khusus aplikasi
- Jalankan `php artisan optimize`
- Pastikan backup database aktif
- Pastikan `DEFAULT_ADMIN_PASSWORD` sudah diganti dari nilai contoh sebelum menjalankan `EmptyProductSeeder`
- Pastikan `.env` hanya ada di server produksi, bukan di repositori
