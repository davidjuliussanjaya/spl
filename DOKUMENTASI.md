# Dokumentasi Proyek SPL

## Sistem Penilaian Lulusan Universitas Dinamika

Dokumen ini menggambarkan implementasi SPL berdasarkan source code, route, migrasi, dan seeder yang ada pada 5 Oktober 2026. Ia membedakan perilaku yang benar-benar tersedia dari batasan yang masih ada. Gunakan [README.md](README.md) untuk instalasi dan deploy; gunakan [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md) sebagai checklist keamanan sebelum go-live.

## 1. Ringkasan

SPL mengumpulkan penilaian pengguna lulusan terhadap lulusan Universitas Dinamika. Admin menyiapkan master data dan periode, membuat survei, serta membagikan kode akses. Penyelia perusahaan mengisi survei tanpa akun. Setelah dikirim, jawaban dan identitas saat itu dibekukan menjadi arsip snapshot sehingga laporan historis tidak bergantung pada data master yang dapat berubah.

Ruang lingkup yang tersedia:

- autentikasi internal, akun aktif/nonaktif, dan role `admin`/`user`;
- data pengguna lulusan/perusahaan, lulusan, fakultas, program studi, kategori, instrumen, dan pertanyaan;
- periode, survei tunggal dan massal, serta daftar kode akses per periode dalam Excel;
- survei publik dengan rating, pilihan ganda (termasuk multi-pilih dan jawaban lain), dan esai;
- dashboard, arsip respons, dan laporan Excel.

## 2. Aktor dan akses

| Aktor | Akses aktual |
|---|---|
| Admin | Seluruh modul internal: dashboard, pengguna internal, master data, periode, survei, laporan, dan arsip. |
| User reguler | Login dan dashboard. |
| Penyelia perusahaan | Memasukkan kode akses dan mengisi survei publik tanpa login. |

Semua route admin menggunakan middleware `auth`, `active`, dan `role:admin`. Dashboard memakai `auth` dan `active`; dengan demikian user reguler yang aktif dapat melihat metrik dashboard. Akun yang menjadi nonaktif akan dikeluarkan pada request berikutnya oleh middleware `active`.

Autentikasi Laravel Breeze masih menyediakan registrasi, login/logout, lupa/reset password, verifikasi email, dan konfirmasi password. Registrasi publik membuat akun internal role `user`; kebijakan ini perlu diputuskan sebelum produksi.

## 3. Alur bisnis

```text
Admin membuat periode dan menyiapkan data master
  -> Admin memilih lulusan, perusahaan, dan pertanyaan aktif
  -> Sistem membuat survei dengan kode acak 8 karakter
  -> Admin membagikan kode atau mengunduh daftar akses Excel
  -> Penyelia memverifikasi kode di halaman utama
  -> Penyelia mengisi dan mengirim survei
  -> Sistem menyimpan jawaban, menandai selesai, dan membuat arsip snapshot
  -> Admin melihat dashboard, arsip, atau ekspor laporan
```

### Pembuatan survei

Survei selalu dikaitkan ke satu `periode`. Untuk survei tunggal, admin memilih lulusan, pengguna lulusan, dan pertanyaan. Untuk survei massal, sistem membuat satu survei untuk setiap lulusan yang memiliki perusahaan pada tahun kelulusan yang dipilih.

Pertanyaan dipilih dari pertanyaan aktif. Kategori yang tidak memiliki fakultas berlaku umum; kategori yang memiliki fakultas hanya dipasang pada lulusan dari fakultas tersebut. Urutan kategori yang dipilih admin disimpan pada pivot `survey_soal.urutan`.

### Pengisian survei

Kode harus diverifikasi melalui `POST /access-survey`; kode aktif dan belum selesai disimpan di session sebelum halaman formulir dapat dibuka. Submit memperbarui data perusahaan/penyelia, memvalidasi jawaban terhadap pertanyaan yang memang terpasang pada survei, mengunci baris survei untuk mencegah submit bersamaan, lalu menjalankan semua operasi dalam transaksi.

Saat selesai, survei tidak dapat diedit atau dihapus dari antarmuka. Sistem membuat satu baris `survey_arsip` dengan snapshot lulusan, perusahaan, periode, dan `jawaban_json`.

## 4. Arsitektur dan teknologi

| Bagian | Implementasi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Database | PostgreSQL (`pgsql`, `jsonb` untuk arsip) |
| Frontend | Blade, Vite 7, Tailwind CSS, Alpine.js |
| Autentikasi | Laravel Breeze, session web |
| Queue/cache/session | Driver database secara default |
| Ekspor | `maatwebsite/excel` / PhpSpreadsheet |
| Test | Pest PHP 3 |

Pola aplikasi: `Route → middleware → controller → form request/validasi → service → Eloquent/query builder → PostgreSQL → Blade atau Excel`.

Service utama: `SurveyService` (siklus survei dan arsip), `DashboardService` (metrik/filter), `SatisfactionScoreService` (skor kepuasan), `LulusanService`, `PenggunaLulusanService`, dan `PertanyaanService`.

## 5. Modul aktual

| Modul | Kemampuan |
|---|---|
| Dashboard | Statistik survei/responden/lulusan, nilai rata-rata, kategori, distribusi kepuasan, tren periode, prodi, kerja sama lanjutan, dan komentar esai; filter periode dan program studi. |
| Pengguna internal | Admin membuat, mengubah, mengaktifkan/menonaktifkan, dan menghapus akun; sistem melindungi akun sendiri dan admin aktif terakhir. |
| Perusahaan | CRUD pengguna lulusan/perusahaan beserta penyelia, kontak, cabang, dan data ketenagakerjaan. |
| Lulusan | Daftar/filter, tambah, detail, dan update data lulusan. Tombol sinkronisasi saat ini belum mengonfigurasi integrasi eksternal. |
| Periode | CRUD kode/nama/rentang tanggal. Periode yang telah dipakai survei tidak dapat dihapus. |
| Kategori dan soal | CRUD kategori; tambah, edit, dan aktif/nonaktif pertanyaan; jenis rating, pilihan ganda, atau esai. Kategori dapat umum atau terikat fakultas. |
| Survei | Daftar per periode, pencarian/status, buat tunggal/massal, edit/hapus sebelum selesai, serta ekspor daftar kode akses periode. |
| Laporan | Filter periode/program studi, unduh Excel, daftar arsip, dan detail snapshot arsip. |

## 6. Route penting

| Akses | Route |
|---|---|
| Publik | `GET /`, `POST /access-survey`, `GET /fill-survey/{code}`, `POST /submit-survey/{code}`, `GET /user-manual` |
| Internal aktif | `GET /dashboard` |
| Admin | `/users`, `/lulusan`, `/penggunalulusan`, `/kategori`, `/periode`, `/pertanyaan`, `/survey`, `/report` beserta aksi turunannya |
| Auth Breeze | `/register`, `/login`, `/logout`, `/forgot-password`, `/reset-password`, `/verify-email`, dan `/confirm-password` |
| Health | `GET /up` |

Periksa route yang benar pada checkout aktif dengan `php artisan route:list --except-vendor`.

## 7. Model data

```text
users >-- user_roles --< roles

fakultas 1 --- n program_studi
fakultas 1 --- n lulusan
fakultas 1 --- n kategoris
program_studi 1 --- n lulusan
instrumen 1 --- n soal
kategoris 1 --- n soal

pengguna_lulusan 1 --- n lulusan
pengguna_lulusan 1 --- n survey
periode 1 --- n survey
lulusan 1 --- n survey
survey >-- survey_soal --< soal
soal 1 --- n jawaban
survey 1 --- n respon_jawaban
survey 1 --- 0..1 survey_arsip
```

| Tabel | Fungsi |
|---|---|
| `fakultas`, `program_studi` | Master akademik; lulusan tetap menyimpan teks legacy serta foreign key master. |
| `instrumen`, `kategoris`, `soal`, `jawaban` | Instrumen survei. Soal mempunyai tipe, wajib/aktif, dukungan multi-pilih, dan jawaban lain. |
| `periode`, `survey`, `survey_soal` | Sesi survei dan urutan pertanyaan. Kolom `survey.tahun` dipertahankan untuk kompatibilitas; `periode_id` menjadi relasi operasional. |
| `respon_jawaban` | Respons operasional per jawaban; pilihan multi-pilih menghasilkan lebih dari satu baris per soal. |
| `survey_arsip` | Snapshot permanen tanpa foreign key ke survei asal; `survey_id` unik untuk mencegah arsip ganda. |
| `pengguna_lulusan`, `lulusan` | Perusahaan/penyelia serta lulusan yang dinilai. |

## 8. Validasi dan integritas

- Form mutasi memakai CSRF web Laravel.
- Password memakai hash Laravel; login memiliki throttling Breeze.
- Kode akses dibuat dengan `Str::random(8)`, diubah ke huruf besar, dan dibatasi unik oleh database.
- Submit validasi memastikan pertanyaan wajib, jenis jawaban, pilihan yang dimiliki soal, dukungan jawaban lain, dan keanggotaan pertanyaan pada survei.
- Submit menggunakan transaksi dan `lockForUpdate`; survei selesai atau nonaktif ditolak.
- Penghapusan survei selesai ditolak; arsip dipakai untuk membaca hasil final.

## 9. Seeder

`DatabaseSeeder` memanggil `AccessControlSeeder`, `FakultasProgramStudiSeeder`, `PengolahanPenggunaLulusanArchiveSeeder`, `DraftInstrumenUniversitas2026Seeder`, dan `Fresh2028SurveyPreparationSeeder`.

| Perintah | Kegunaan |
|---|---|
| `php artisan db:seed --class='Database\\Seeders\\AccessControlSeeder'` | Role dan akun awal dari `SEED_*`; aman dipakai untuk setup kosong setelah konfigurasi password. |
| `php artisan db:seed` | Paket master dan data demo; hanya development/test. |
| `php artisan db:seed --class='Database\\Seeders\\PostgreSqlSequenceSeeder'` | Sinkronkan sequence PostgreSQL setelah impor data dengan ID eksplisit. |

Jangan menjalankan seeder demo atau `migrate:fresh` di produksi.

## 10. Instalasi, pengujian, dan operasi

Panduan lengkap tersedia di [README.md](README.md) dan [docs/POSTGRESQL_SETUP.md](docs/POSTGRESQL_SETUP.md). Ringkasnya:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class='Database\Seeders\AccessControlSeeder'
npm run build
php artisan test
```

Untuk development gunakan `npm run dev` dan `php artisan serve`, atau `composer run dev`. Untuk produksi gunakan `composer install --no-dev --optimize-autoloader`, `npm ci`, `npm run build`, `php artisan migrate --force`, dan `php artisan optimize`. Detail queue worker, health check, backup/restore, dan rollback tersedia pada README.

## 11. Batasan dan keputusan sebelum go-live

- `Periode::isBerlangsung()` tersedia di model, tetapi controller pengisian belum memakainya untuk menolak tanggal di luar rentang periode. Akses saat ini ditentukan oleh `is_active` dan `is_completed`.
- Registrasi publik dan akses dashboard role `user` masih terbuka.
- Endpoint publik survei belum memakai limiter khusus.
- `trustProxies(at: '*')` masih ada pada bootstrap; batasi proxy tepercaya dan host di environment produksi.
- Security header, HSTS, email/queue produksi, backup/restore, monitoring, dan audit dependency online harus divalidasi pada hosting.
- Tidak ada upload file atau API publik aktif pada route aplikasi.

Rincian status dan checklist wajib ada di [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md).

## 12. Dokumen pendukung

- [README.md](README.md) — setup lokal sampai produksi
- [docs/POSTGRESQL_SETUP.md](docs/POSTGRESQL_SETUP.md) — setup PostgreSQL Windows dan troubleshooting
- [docs/user-manual-role-admin.md](docs/user-manual-role-admin.md) — manual admin
- [docs/user-manual-role-user.html](docs/user-manual-role-user.html) — manual user reguler
- [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md) — audit dan checklist go-live
