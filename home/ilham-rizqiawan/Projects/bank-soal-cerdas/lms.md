# Roadmap Pengerjaan — Bank Soal Cerdas (LMS)

> Dokumen ini mencatat rencana kerja (roadmap) dan progres pengerjaan **Bank Soal Cerdas**.
> Diperbarui: menandai **Phase 8 selesai**.

---

## Ringkasan Proses

| Phase | Topik | Status |
|---|---|---|
| Phase 1 | Setup & Fondasi Aplikasi | ✅ Selesai |
| Phase 2 | Autentikasi & Manajemen Pengguna | ✅ Selesai |
| Phase 3 | Bank Soal & Authoring | ✅ Selesai |
| Phase 4 | Paket Soal & Kategori/Tag | ✅ Selesai |
| Phase 5 | Ujian Online (Siswa) | ✅ Selesai |
| Phase 6 | Analisis & Evaluasi | ✅ Selesai |
| Phase 7 | Kolaborasi & Berbagi | ✅ Selesai |
| Phase 8 | Frontend/Design System & Akses Kontrol | ✅ **Selesai (saat ini)** |
| Phase 9 | Penyempurnaan UX & Optimasi Produksi | ⏳ Berikutnya |

> **Catatan:** Fase-fase di bawah dirinci agar mudah ditelusuri. Seluruh item dengan tanda `[x]` pada Phase 1–8 sudah dikerjakan.

---

## Phase 1 — Setup & Fondasi Aplikasi

- [x] Inisialisasi project Laravel
- [x] Konfigurasi environment & database
- [x] Setup Vite (Sass + JS)
- [x] Instalasi dependency frontend (Bootstrap, Alpine.js, Font Awesome, Chart.js)
- [x] Struktur dasar layout (Blade)
- [x] Migration & seeder dasar

---

## Phase 2 — Autentikasi & Manajemen Pengguna

- [x] Login (Sessions-based Auth)
- [x] Logout
- [x] Role: Admin, Guru, Siswa
- [x] Manajemen pengguna (CRUD)
- [x] Aktif/nonaktif status akun
- [x] Access Control (middleware role)

---

## Phase 3 — Bank Soal & Authoring

- [x] CRUD soal
- [x] Klasifikasi kurikulum (Merdeka / KBC)
- [x] Tipe soal (PG, Uraian, Menjodohkan, Benar/Salah)
- [x] Level kognitif C1–C6 (Taksonomi Bloom)
- [x] KKO (Kata Kerja Operasional)
- [x] Pencarian & filter
- [x] Import soal (Excel)
- [x] Export soal (Excel)
- [x] Duplikasi soal

---

## Phase 4 — Paket Soal & Kategori/Tag

- [x] CRUD paket soal
- [x] Menambahkan soal dari bank soal
- [x] Mengelola komposisi soal
- [x] Duplikasi paket soal
- [x] Kelola kategori
- [x] Kelola tag

---

## Phase 5 — Ujian Online (Siswa)

- [x] Admin/Guru: buat, atur paket, peserta, durasi
- [x] Publish & ubah status ujian
- [x] Siswa: daftar ujian (Ujian Saya)
- [x] Siswa: kerjakan ujian
- [x] Simpan/kirim jawaban
- [x] Akhiri ujian
- [x] Lihat hasil ujian

---

## Phase 6 — Analisis & Evaluasi

- [x] Halaman analisis (dashboard analisis)
- [x] Analisis per ujian
- [x] Analisis per siswa
- [x] Export analisis

---

## Phase 7 — Kolaborasi & Berbagi

- [x] Berbagi soal
- [x] Berbagi paket soal
- [x] Menerima / menolak soal & paket
- [x] Detail berbagi
- [x] Riwayat berbagi

---

## Phase 8 — Frontend/Design System & Akses Kontrol ⭐ (Saat Ini)

- [x] Design system konsisten (hierarki visual, spacing, komponen)
- [x] Sidebar & header konsisten
- [x] Card, table, badge, action yang seragam
- [x] Responsive layout (desktop & mobile)
- [x] Light & dark mode
- [x] Skeleton loading & toast notification
- [x] Dashboard & seluruh menu menggunakan pola visual yang sama
- [x] Perbaikan asset/build Vite (konsistensi manifest)
- [x] Perbaikan alur login → dashboard

---

## Phase 9 — Penyempurnaan UX & Optimasi Produksi (Berikutnya)

- [ ] Penyempurnaan UX seluruh workflow
- [ ] Penguatan analisis hasil evaluasi
- [ ] Optimasi performa query & frontend
- [ ] Peningkatan test coverage
- [ ] Penyempurnaan sistem kolaborasi
- [ ] Dokumentasi teknis lebih lengkap
- [ ] Persiapan deployment production

---

## Log Singkat (referensi commit)

```text
656fed9  fix: hapus file debug hash password & batasi akses bank soal/paket soal (admin+guru)
725dd7a  Frontend Build Phase 1
4781d5b  Build Phase 2
4199546  Building Phase 1
3b287e5  First Commit
```

---

## Next Action

- Lanjut ke **Phase 9** (Penyempurnaan UX & Optimasi Produksi).
- Sebelumnya: verifikasi keseluruhan alur login → dashboard berjalan tanpa error di browser.

PHASE 9 — UI/UX & Responsive

Setelah fungsi selesai, baru kita lakukan polishing besar.

Desktop
 Sidebar
 Navbar
 Dashboard
 Table
 Form
 Modal
 Dropdown
 Alert
 Pagination
Mobile
 Mobile navbar
 Sidebar mobile
 Dashboard mobile
 Table responsive
 Form responsive
 Chat mobile
 Materi mobile
 Tugas mobile
Konsistensi
 Typography
 Spacing
 Button
 Card
 Icon
 Color theme
 Empty state
 Loading state
 Error state
PHASE 10 — Security Hardening

Sebelum dianggap siap digunakan sekolah.

 Authorization seluruh route
 Policy audit
 IDOR check
 File upload security
 MIME validation
 File size validation
 Mass assignment
 CSRF
 XSS
 SQL injection review
 Rate limiting
 Sensitive information exposure
 Password/security configuration
 Production .env
 Storage permission
PHASE 11 — Testing

Ini penting karena audit sebelumnya menemukan environment testing belum lengkap.

Automated test
 Authentication test
 Authorization test
 Admin test
 Guru test
 Siswa test
 Kepala sekolah test
 Materi test
 Tugas test
 Submission test
 Nilai test
 Absensi test
 Chat test
 Notification test
 Import/export test
Manual test
 Admin workflow
 Guru workflow
 Siswa workflow
 Kepala sekolah workflow
 Desktop
 Mobile
PHASE 12 — Performance
 Query optimization
 N+1 query audit
 Eager loading
 Pagination
 Cache
 Asset optimization
 Lazy loading
 Chart optimization
 File handling
 Database indexing
 Production Laravel optimization
PHASE 13 — Production Readiness

Terakhir sebelum LMS dianggap siap dipakai sekolah.

 Fresh installation test
 EmptyProductSeeder test
 DemoSeeder test
 Migration test
 Storage link
 Production build
 Production .env
 Backup database
 Backup uploaded files
 Logging
 Error page
 Maintenance mode
 Deployment documentation
 Admin installation guide
 User guide
 Security checklist
 Commercial readiness

README sendiri sudah menyediakan jalur demo seeder dan empty product seeder, jadi dua skenario instalasi ini akan kita jadikan bagian dari validasi akhir.
