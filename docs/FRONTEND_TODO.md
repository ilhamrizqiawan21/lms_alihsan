# Frontend Modernization TODO

Dokumen ini adalah roadmap perubahan frontend LMS agar lebih modern, konsisten, dan mudah dirawat tanpa mengubah arsitektur utama Laravel Blade.

## Prinsip Arah

- Tetap gunakan Laravel Blade sebagai rendering utama.
- Pertahankan Bootstrap 5 sebagai fondasi UI.
- Gunakan Vite sebagai asset pipeline utama untuk produksi.
- Tambahkan interaktivitas ringan secara bertahap, bukan migrasi ke SPA.
- Prioritaskan dashboard yang compact, cepat discan, dan nyaman dipakai berulang.

## Tahap 1 - Fondasi Asset

- [ ] Pastikan semua halaman produksi memakai asset hasil `npm run build`.
- [ ] Kurangi ketergantungan CDN untuk Bootstrap, DataTables, Select2, dan icon.
- [ ] Pindahkan inline JavaScript global dari `resources/views/layouts/app.blade.php` ke `resources/js/app.js`.
- [ ] Pindahkan inline CSS theme base ke file CSS yang lebih terstruktur jika memungkinkan.
- [ ] Tambahkan dokumentasi cara build frontend di README atau docs install.
- [ ] Audit halaman yang masih load Chart.js via CDN dan tentukan pola lazy-load.

## Tahap 2 - Design Token

- [ ] Rapikan CSS variables utama: warna, radius, shadow, spacing, typography.
- [ ] Standarkan token untuk status: success, warning, danger, info, muted.
- [ ] Standarkan ukuran tombol: default, small, icon-only.
- [ ] Standarkan ukuran table, form, card, stat card, dan modal.
- [ ] Pastikan tema warna dari pengaturan sekolah tetap kompatibel.
- [ ] Tambahkan checklist kontras warna untuk tema hijau, biru-azure, dan biru-aqua.

## Tahap 3 - Blade Components

- [ ] Buat atau rapikan komponen `x-page-header`.
- [ ] Buat komponen `x-card` untuk header, body, footer, dan actions.
- [ ] Buat komponen `x-form.input`, `x-form.select`, `x-form.file`, dan `x-form.error`.
- [ ] Buat komponen `x-empty-state` yang konsisten.
- [ ] Buat komponen `x-action-buttons` untuk edit, delete, reset, download.
- [ ] Migrasikan halaman admin prioritas ke komponen secara bertahap.

## Tahap 4 - Interaktivitas Modern

- [ ] Tambahkan Alpine.js untuk state kecil seperti dropdown, modal ringan, sidebar, dan loading submit.
- [ ] Ganti inline `onclick` sidebar dengan handler JS/Alpine yang rapi.
- [ ] Tambahkan loading state otomatis pada form submit.
- [ ] Tambahkan disabled state pada tombol submit saat proses berjalan.
- [ ] Standarkan confirm dialog untuk aksi delete, reset password, dan luluskan kelas.
- [ ] Pastikan toast global bisa dipanggil dari script halaman.

## Tahap 5 - Table & Data Density

- [ ] Tentukan halaman mana yang memakai DataTables dan mana yang cukup pagination Laravel.
- [ ] Standarkan class table: compact, hover, responsive, action column.
- [ ] Pastikan kolom aksi memakai icon button dengan tooltip atau title.
- [ ] Tambahkan empty state untuk tabel kosong.
- [ ] Pastikan filter table selalu terlihat jelas di mobile.
- [ ] Audit overflow tabel nilai, absensi, sikap, dan rekap.

## Tahap 6 - Form UX

- [ ] Standarkan validasi error di bawah field.
- [ ] Tambahkan helper text hanya untuk field yang memang perlu penjelasan.
- [ ] Gunakan Select2 hanya untuk select panjang atau relasional.
- [ ] Pastikan upload file menampilkan ekstensi dan batas ukuran.
- [ ] Tambahkan pola form section untuk halaman yang banyak input.
- [ ] Audit form import Excel agar template, upload, dan error baris mudah dipahami.

## Tahap 7 - Mobile & Responsive

- [ ] Audit semua dashboard di viewport mobile.
- [ ] Pastikan sidebar drawer tidak menutup topbar secara aneh.
- [ ] Pastikan topbar title tidak bertabrakan dengan account menu.
- [ ] Buat tombol aksi tabel menjadi compact di mobile.
- [ ] Pastikan card stat dan chart tidak overflow.
- [ ] Pastikan form filter memakai layout stacked yang rapi di mobile.

## Tahap 8 - Accessibility

- [ ] Tambahkan `aria-label` untuk semua icon-only button.
- [ ] Pastikan focus state jelas untuk link, tombol, input, dan select.
- [ ] Pastikan modal confirm bisa ditutup dengan keyboard.
- [ ] Pastikan warna badge tidak menjadi satu-satunya penanda status.
- [ ] Pastikan heading tiap halaman berurutan dan jelas.
- [ ] Audit contrast text pada sidebar, topbar, badge, dan alert.

## Tahap 9 - Performance

- [ ] Pastikan CSS dan JS hasil build sudah minified.
- [ ] Lazy-load Chart.js hanya pada halaman statistik/progress.
- [ ] Hindari inisialisasi DataTables global jika tidak ada `.datatable`.
- [ ] Hindari query notifikasi berat di layout jika halaman tidak membutuhkan.
- [ ] Optimalkan ukuran logo sekolah dengan batas dimensi dan format.
- [ ] Cek bundle size setelah dependensi frontend dirapikan.

## Tahap 10 - Halaman Prioritas

- [ ] Login.
- [ ] Admin Dashboard.
- [ ] Kelas & Siswa.
- [ ] Guru Dashboard.
- [ ] Absensi Guru.
- [ ] Input Nilai Guru.
- [ ] Tugas Guru dan Siswa.
- [ ] Materi Guru dan Siswa.
- [ ] Progress Siswa.
- [ ] Laporan Kepala Sekolah.

## Urutan Eksekusi Disarankan

1. Asset pipeline dan pengurangan CDN.
2. Design token dan struktur CSS.
3. Komponen Blade dasar.
4. Modernisasi halaman Kelas & Siswa sebagai contoh pertama.
5. Terapkan pola yang sama ke halaman admin lain.
6. Lanjutkan ke halaman guru yang padat data.
7. Lanjutkan ke halaman siswa dan kepala sekolah.
8. Audit mobile, accessibility, dan performance.

## Definition of Done

- Tidak ada perubahan visual yang merusak flow role admin, guru, siswa, dan kepala sekolah.
- Semua halaman utama tetap responsive.
- Tidak ada tombol aksi tanpa label atau title.
- Tidak ada dependency CDN kritikal di produksi kecuali memang sengaja.
- `npm run build` berhasil.
- Route dan form penting tetap lolos manual test.
