# DOKUMENTASI PROYEK SPL

## Sistem Penilaian Lulusan Universitas Dinamika

Dokumen ini menjelaskan kondisi aktual proyek **SPL (Sistem Penilaian Lulusan)** berdasarkan implementasi kode pada Juni 2026. Dokumen dapat digunakan sebagai:

- sumber konteks untuk ChatGPT ketika membantu penulisan laporan Tugas Akhir;
- acuan untuk menjelaskan analisis, perancangan, implementasi, dan pengujian sistem;
- panduan pengembangan dan pemeliharaan aplikasi;
- catatan perbedaan antara rancangan sistem dan implementasi yang sedang berjalan.

> Catatan penting untuk ChatGPT: gunakan dokumen ini sebagai sumber utama ketika membahas proyek SPL. Bedakan fitur yang sudah diimplementasikan, keterbatasan implementasi, dan rekomendasi pengembangan. Jangan menganggap rekomendasi sebagai fitur yang sudah tersedia.

---

## 1. Identitas dan Gambaran Umum

### 1.1 Nama Sistem

**SPL - Sistem Penilaian Lulusan**

### 1.2 Latar Belakang

Perguruan tinggi memerlukan umpan balik dari pengguna lulusan, seperti perusahaan dan instansi, untuk mengetahui kualitas lulusan di dunia kerja. Informasi tersebut dapat digunakan sebagai bahan evaluasi kurikulum, peningkatan kompetensi lulusan, dan pendukung proses akreditasi.

SPL dibangun untuk mendigitalisasi proses pengumpulan, pengelolaan, analisis, dan pelaporan penilaian pengguna lulusan Universitas Dinamika. Penilaian diberikan oleh penyelia atau perwakilan perusahaan terhadap seorang lulusan melalui survei berbasis web.

### 1.3 Tujuan Sistem

Sistem bertujuan untuk:

1. Memusatkan data pengguna lulusan, lulusan, pertanyaan, dan hasil survei.
2. Memudahkan admin membuat survei penilaian untuk satu atau banyak lulusan.
3. Memudahkan penyelia perusahaan mengisi survei tanpa harus membuat akun.
4. Menampilkan ringkasan penilaian lulusan melalui dashboard.
5. Menghasilkan laporan Excel yang dapat digunakan untuk evaluasi dan kebutuhan akreditasi.

### 1.4 Ruang Lingkup

Ruang lingkup implementasi saat ini meliputi:

- autentikasi pengguna internal;
- otorisasi berbasis role `admin` dan `user`;
- pengelolaan data pengguna lulusan atau perusahaan;
- pengelolaan data lulusan;
- pengelolaan kategori dan pertanyaan;
- pembuatan survei tunggal dan survei massal;
- pengisian survei publik menggunakan kode akses;
- penyimpanan jawaban rating, pilihan ganda, dan esai;
- dashboard analisis penilaian;
- ekspor laporan ke format `.xlsx`.

---

## 2. Aktor Sistem

| Aktor | Deskripsi | Hak Akses Aktual |
|---|---|---|
| Admin | Pengguna internal yang mengelola seluruh data dan proses survei | Dashboard, data perusahaan, data lulusan, kategori, pertanyaan, survei, dan laporan |
| User Reguler | Pengguna internal non-admin | Login, dashboard, dan profil |
| Penyelia Perusahaan | Perwakilan perusahaan atau instansi yang menilai lulusan | Mengakses dan mengisi survei melalui kode akses tanpa login |

### 2.1 Ringkasan Use Case

**Admin dapat:**

- login ke sistem;
- melihat dashboard evaluasi;
- menambahkan dan melihat data lulusan;
- menambah, mengubah, dan menghapus data pengguna lulusan;
- mengelola kategori pertanyaan;
- menambah, mengubah, dan mengaktifkan atau menonaktifkan pertanyaan;
- membuat survei tunggal;
- membuat survei massal berdasarkan tahun lulus;
- melihat daftar dan status survei;
- mengubah survei yang belum selesai;
- membuka halaman laporan dan mengunduh laporan Excel.

**User reguler dapat:**

- login ke sistem;
- melihat dashboard;
- mengelola profil akun.

**Penyelia perusahaan dapat:**

- memasukkan kode akses survei;
- melihat data lulusan yang dinilai;
- mengonfirmasi atau memperbarui data perusahaan dan identitas penyelia;
- mengisi pertanyaan survei;
- mengirim hasil survei.

---

## 3. Alur Bisnis Utama

### 3.1 Alur Umum

```text
Admin mengelola data perusahaan dan lulusan
    -> Admin mengelola instrumen pertanyaan
    -> Admin membuat survei
    -> Sistem membuat kode akses unik
    -> Kode diberikan kepada penyelia perusahaan
    -> Penyelia membuka survei tanpa login
    -> Penyelia mengonfirmasi identitas dan mengisi penilaian
    -> Sistem menyimpan jawaban dan menandai survei selesai
    -> Admin melihat dashboard dan mengunduh laporan
```

### 3.2 Pembuatan Survei Tunggal

1. Admin memilih satu lulusan dan pengguna lulusan.
2. Admin mengisi judul, deskripsi, dan memilih pertanyaan.
3. `SurveyService::createSurvey()` membuat survei dalam transaksi database.
4. Sistem menghasilkan `access_code` acak sepanjang 8 karakter menggunakan `Str::random(8)`.
5. Pertanyaan yang disimpan ke pivot `survey_soal` hanya pertanyaan dengan peruntukan:
   - `Umum`; atau
   - sama dengan fakultas lulusan.
6. Survei dibuat dengan status aktif dan belum selesai.

### 3.3 Pembuatan Survei Massal

1. Admin memilih tahun lulus, judul, deskripsi, dan daftar pertanyaan.
2. Sistem mengambil semua lulusan pada tahun tersebut yang memiliki `pengguna_lulusan_id`.
3. Sistem membuat satu survei untuk setiap lulusan.
4. Pertanyaan setiap survei difilter berdasarkan fakultas lulusan.
5. Seluruh proses dijalankan dalam satu transaksi database.

### 3.4 Pengisian Survei Publik

1. Penyelia memasukkan kode akses pada halaman utama.
2. Sistem mencari survei dengan kode tersebut yang belum selesai.
3. Penyelia diarahkan ke halaman `/fill-survey/{code}`.
4. Sistem menampilkan informasi lulusan, perusahaan, penyelia, dan pertanyaan survei.
5. Pertanyaan dikelompokkan berdasarkan kategori.
6. Penyelia dapat memperbarui data perusahaan dan identitas responden.
7. Jawaban dikirim ke `/submit-survey/{code}`.
8. `SurveyService::submitJawaban()` menyimpan setiap jawaban ke `respon_jawaban`.
9. Sistem memperbarui data pengguna lulusan dan menandai survei sebagai selesai.

### 3.5 Jenis Jawaban

| Jenis Soal | Penyimpanan |
|---|---|
| `rating` | Menyimpan referensi pilihan pada `jawaban_id` |
| `multiple_choice` | Menyimpan referensi pilihan pada `jawaban_id` |
| `essay` | Menyimpan teks pada `jawaban_text` |

Nilai rating diperoleh dari kolom `jawaban.nilai`, bukan disimpan langsung pada tabel `respon_jawaban`.

---

## 4. Teknologi yang Digunakan

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Database utama | Oracle XE |
| Driver Oracle | `yajra/laravel-oci8` versi 12 |
| Autentikasi | Laravel Breeze 2, session-based authentication |
| Frontend | Blade, Bootstrap pada template admin, Tailwind CSS, Alpine.js |
| Visualisasi | ApexCharts dan Chart.js tersedia pada aset publik |
| Build tool | Vite 7 |
| Ekspor Excel | PhpSpreadsheet melalui paket `maatwebsite/excel` |
| Testing | Pest PHP 3 |
| Dependency manager | Composer dan npm |

### 4.1 Pola Arsitektur Aplikasi

SPL merupakan aplikasi web monolitik Laravel dengan pola MVC yang ditambah lapisan service.

```text
Browser
  -> Route
  -> Middleware
  -> Controller
  -> Form Request / Validation
  -> Service
  -> Eloquent Model / Query Builder
  -> Oracle Database
  -> Blade View atau file Excel
```

Pembagian tanggung jawab:

- **Route** menentukan URL, HTTP method, middleware, dan controller.
- **Middleware** memeriksa autentikasi dan role.
- **Controller** menerima request dan memilih respons.
- **Form Request** melakukan validasi input pada sebagian besar proses utama.
- **Service** menangani logika bisnis dan transaksi.
- **Model** merepresentasikan tabel dan relasi database.
- **Blade View** menampilkan antarmuka pengguna.
- **ReportExport** membangun laporan Excel menggunakan PhpSpreadsheet.

---

## 5. Struktur Direktori Penting

```text
app/
|-- Exports/
|   `-- ReportExport.php
|-- Http/
|   |-- Controllers/
|   |-- Middleware/CheckRole.php
|   `-- Requests/
|-- Models/
`-- Services/

database/
|-- migrations/
`-- seeders/

resources/
|-- css/
|-- js/
`-- views/
    |-- admin/
    |-- auth/
    |-- layouts/
    |-- fill_page.blade.php
    `-- landing.blade.php

routes/
|-- auth.php
`-- web.php

docs/
|-- contoh perhitungan.xlsx
`-- PROJECT_DOCUMENTATION.md

tests/
|-- Feature/
`-- Unit/
```

### 5.1 Service Utama

| Service | Tanggung Jawab |
|---|---|
| `SurveyService` | Membuat, memperbarui, membuat massal, dan menyimpan jawaban survei |
| `DashboardService` | Menghitung statistik dan data dashboard |
| `LulusanService` | Menyimpan dan memfilter data lulusan |
| `PenggunaLulusanService` | CRUD data perusahaan atau pengguna lulusan |
| `PertanyaanService` | Menyimpan, memperbarui, dan mengubah status pertanyaan |

---

## 6. Autentikasi dan Otorisasi

### 6.1 Autentikasi

Autentikasi internal menggunakan Laravel Breeze dengan email dan password. Fitur autentikasi yang tersedia mencakup:

- registrasi;
- login dan logout;
- lupa dan reset password;
- verifikasi email;
- konfirmasi password;
- pengelolaan profil.

### 6.2 Role-Based Access Control

Role disimpan pada tabel `roles` dan dihubungkan ke pengguna melalui tabel pivot `user_roles`.

```text
users >-- user_roles --< roles
```

Kolom penting `user_roles`:

- `user_id`;
- `role_id`;
- `is_active`;
- `assigned_at`;
- `ended_at`.

Model `User` menyediakan helper:

```php
$user->hasRole('admin');
```

Middleware `CheckRole` didaftarkan dengan alias `role`. Semua modul administrasi berada di dalam middleware:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // route administrasi
});
```

Dashboard dan profil dapat diakses oleh semua pengguna yang sudah login.

---

## 7. Modul dan Fitur Aktual

### 7.1 Dashboard Evaluasi

Dashboard menampilkan:

- total survei selesai;
- total lulusan unik yang dinilai;
- indeks kepuasan atau rata-rata nilai keseluruhan;
- kategori terbaik dan kategori terlemah;
- rata-rata nilai per kategori;
- distribusi persentase rating per kategori;
- total dan rata-rata distribusi kepuasan;
- daftar 10 lulusan terbaru;
- 5 umpan balik esai terbaru.

Filter dashboard:

- tahun survei;
- fakultas;
- program studi;
- jenis perusahaan.

**Definisi tahun pada dashboard saat ini:** tahun diambil dari `survey.updated_at`, bukan dari `lulusan.tahun_lulus`.

### 7.2 Manajemen Pengguna Lulusan

Pengguna lulusan adalah perusahaan atau instansi yang mempekerjakan lulusan dan menjadi pihak pengisi survei.

Fitur:

- melihat daftar perusahaan dan jumlah lulusan terkait;
- menambah perusahaan;
- mengubah perusahaan;
- menghapus perusahaan.

Data yang dikelola:

- nama perusahaan;
- nama dan jabatan penyelia;
- email dan kontak penyelia;
- jenis perusahaan;
- alamat perusahaan;
- jumlah lulusan;
- durasi lulusan bekerja;
- informasi cabang kota dan negara.

### 7.3 Manajemen Lulusan

Fitur aktual:

- melihat dan memfilter daftar lulusan;
- menambahkan lulusan.

Filter lulusan:

- nama;
- NIM;
- program studi;
- fakultas;
- rentang tahun lulus;
- status bekerja.

Data lulusan:

- perusahaan tempat bekerja;
- nama;
- NIM unik;
- program studi;
- fakultas;
- tanggal atau tahun lulus;
- status bekerja.

> Implementasi route saat ini belum menyediakan edit dan hapus data lulusan.

### 7.4 Manajemen Kategori

Admin dapat menambah, melihat, mengubah, dan menghapus kategori pertanyaan. Kategori berisi:

- `nama_kategori`;
- `deskripsi`.

Jika kategori dihapus, `kategori_id` pada soal terkait menjadi `NULL`.

### 7.5 Manajemen Pertanyaan

Admin dapat:

- melihat daftar pertanyaan;
- menambahkan pertanyaan;
- mengubah pertanyaan;
- mengaktifkan atau menonaktifkan pertanyaan.

Konfigurasi pertanyaan:

- kode unik;
- teks pertanyaan;
- kategori;
- peruntukan fakultas: `FTI`, `FDIK`, `FEB`, atau `Umum`;
- jenis soal: `rating`, `multiple_choice`, atau `essay`;
- wajib atau opsional;
- aktif atau nonaktif;
- pilihan jawaban dan nilai.

Pada form internal, jenis input dipetakan oleh `PertanyaanService`:

| Input Form | Nilai Database |
|---|---|
| `radio` | `multiple_choice` |
| `text` | `essay` |
| selain keduanya | `rating` |

### 7.6 Manajemen Survei

Admin dapat:

- melihat daftar survei;
- membuat survei tunggal;
- membuat survei massal berdasarkan tahun lulus;
- melihat atau mengubah survei yang belum selesai.

Status utama survei:

- `is_active`: menandai survei aktif atau tidak;
- `is_completed`: menandai survei sudah dikirim oleh responden atau belum.

### 7.7 Laporan Excel

Halaman laporan menyediakan filter:

- tahun lulus;
- fakultas;
- program studi.

File Excel yang dihasilkan berisi:

- sheet terpisah untuk setiap tahun lulus;
- data responden dan lulusan per fakultas;
- jawaban rating setiap pertanyaan;
- distribusi persentase rating per program studi;
- subtotal rata-rata per kategori;
- ringkasan tingkat kepuasan;
- total dan rata-rata kategori.

**Definisi tahun pada laporan:** tahun berasal dari `lulusan.tahun_lulus`.

---

## 8. Route Utama

### 8.1 Route Publik

| Method | URL | Fungsi |
|---|---|---|
| GET | `/` | Halaman utama |
| POST | `/access-survey` | Memvalidasi kode akses |
| GET | `/fill-survey/{code}` | Menampilkan formulir survei |
| POST | `/submit-survey/{code}` | Menyimpan jawaban survei |

### 8.2 Route Pengguna Terautentikasi

| Method | URL | Fungsi |
|---|---|---|
| GET | `/dashboard` | Dashboard evaluasi |
| GET/PATCH/DELETE | `/profile` | Pengelolaan profil |

### 8.3 Route Admin

| Modul | URL Utama | Fungsi |
|---|---|---|
| Survei | `/survey`, `/addsurvey`, `/survey/bulk` | Daftar, tambah tunggal, dan tambah massal |
| Lulusan | `/lulusan`, `/addgrad` | Daftar dan tambah lulusan |
| Pengguna Lulusan | `/penggunalulusan`, `/create` | CRUD perusahaan |
| Kategori | `/kategori` | CRUD kategori |
| Pertanyaan | `/pertanyaan`, `/addquestion` | Kelola pertanyaan |
| Laporan | `/report`, `/report/download` | Filter dan unduh laporan |

Project memiliki 55 route non-vendor berdasarkan `php artisan route:list --except-vendor`.

---

## 9. Desain Database

### 9.1 Relasi Konseptual

```text
users >-- user_roles --< roles

pengguna_lulusan 1 --- n lulusan
pengguna_lulusan 1 --- n survey
lulusan          1 --- n survey

survey >-- survey_soal --< soal
kategoris 1 --- n soal
soal       1 --- n jawaban

survey  1 --- n respon_jawaban
soal    1 --- n respon_jawaban
jawaban 1 --- n respon_jawaban
```

### 9.2 Tabel Domain Utama

| Tabel | Fungsi |
|---|---|
| `pengguna_lulusan` | Menyimpan perusahaan, instansi, dan data penyelia |
| `lulusan` | Menyimpan lulusan yang dinilai |
| `survey` | Menyimpan sesi survei dan kode akses |
| `survey_soal` | Menyimpan pertanyaan yang dipilih untuk setiap survei |
| `kategoris` | Mengelompokkan pertanyaan berdasarkan kompetensi |
| `soal` | Menyimpan instrumen pertanyaan |
| `jawaban` | Menyimpan pilihan jawaban dan bobot nilai |
| `respon_jawaban` | Menyimpan jawaban responden |

### 9.3 Tabel Pengguna dan Otorisasi

| Tabel | Fungsi |
|---|---|
| `users` | Akun pengguna internal |
| `roles` | Daftar role |
| `user_roles` | Relasi many-to-many pengguna dan role |

### 9.4 Kolom Penting

#### `pengguna_lulusan`

| Kolom | Keterangan |
|---|---|
| `nama_perusahaan` | Nama perusahaan atau instansi |
| `nama_penyelia` | Nama perwakilan pengisi survei |
| `jabatan_penyelia` | Jabatan penyelia |
| `email_penyelia` | Email unik penyelia |
| `jumlah_lulusan` | Jumlah lulusan yang pernah bekerja |
| `durasi_lulusan_bekerja` | Durasi kerja lulusan |
| `jenis_perusahaan` | Kategori perusahaan dalam bentuk string |
| `cabang_kota` | Informasi cabang kota |
| `cabang_negara` | Informasi cabang negara |

#### `lulusan`

| Kolom | Keterangan |
|---|---|
| `pengguna_lulusan_id` | Perusahaan tempat lulusan bekerja |
| `nama` | Nama lulusan |
| `nim` | Nomor induk mahasiswa |
| `program_studi` | Program studi |
| `fakultas` | Fakultas |
| `tahun_lulus` | Tanggal kelulusan |
| `status` | Status bekerja dalam bentuk boolean |

#### `survey`

| Kolom | Keterangan |
|---|---|
| `access_code` | Kode akses unik dengan batas kolom 10 karakter |
| `lulusan_id` | Lulusan yang dinilai |
| `pengguna_lulusan_id` | Perusahaan pengisi |
| `judul` | Judul survei |
| `deskripsi` | Deskripsi survei |
| `is_completed` | Status pengisian |
| `is_active` | Status aktif survei |

#### `soal`

| Kolom | Keterangan |
|---|---|
| `kode` | Kode soal yang unik secara global |
| `kategori_id` | Kategori soal |
| `peruntukan_fakultas` | `FTI`, `FDIK`, `FEB`, atau `Umum` |
| `jenis_soal` | `rating`, `multiple_choice`, atau `essay` |
| `is_required` | Menentukan soal wajib diisi |
| `is_active` | Menentukan soal aktif |

#### `respon_jawaban`

| Kolom | Keterangan |
|---|---|
| `survey_id` | Survei terkait |
| `soal_id` | Pertanyaan terkait |
| `jawaban_id` | Pilihan jawaban untuk soal non-esai |
| `jawaban_text` | Jawaban untuk soal esai |
| `responden` | Nama pengisi survei |
| `jumlah_lulusan_bekerja` | Catatan jumlah lulusan pada saat survei |

Saat submit, `jumlah_lulusan_bekerja` hanya disimpan pada record jawaban pertama dari survei tersebut. Nilai yang sama juga digunakan untuk memperbarui `pengguna_lulusan.jumlah_lulusan`.

### 9.5 Aturan Foreign Key

- Penghapusan pengguna lulusan akan menghapus lulusan dan survei terkait melalui `cascadeOnDelete`.
- Penghapusan lulusan akan menghapus survei terkait.
- Penghapusan survei akan menghapus pivot dan respons terkait.
- Penghapusan soal akan menghapus pilihan jawaban dan respons terkait.
- Penghapusan kategori akan membuat `soal.kategori_id` menjadi `NULL`.

---

## 10. Metode Pengolahan dan Perhitungan Data

### 10.1 Skala Penilaian

Dataset referensi Excel menggunakan skala:

| Nilai | Label |
|---|---|
| 4 | Sangat Baik |
| 3 | Baik |
| 2 | Kurang |
| 1 | Sangat Kurang |

Seeder instrumen 2026 menggunakan label:

| Nilai | Label |
|---|---|
| 4 | Sangat Baik |
| 3 | Baik |
| 2 | Cukup |
| 1 | Kurang |

Dengan demikian, arti label nilai 1 dan 2 bergantung pada instrumen yang digunakan. Kode dashboard dan laporan tetap mengolah bobot numerik `1` sampai `4`.

### 10.2 Rata-Rata Nilai Keseluruhan

Dashboard menghitung indeks kepuasan menggunakan rata-rata seluruh `jawaban.nilai` untuk:

- soal berjenis `rating`;
- survei dengan `is_completed = true`;
- data yang memenuhi filter dashboard.

Rumus:

```text
Rata-rata keseluruhan = jumlah seluruh nilai rating / jumlah jawaban rating
```

### 10.3 Rata-Rata Per Kategori

```text
Rata-rata kategori =
jumlah seluruh nilai rating dalam kategori
/
jumlah jawaban rating dalam kategori
```

Kategori dengan rata-rata tertinggi ditampilkan sebagai kategori terbaik. Kategori dengan rata-rata terendah ditampilkan sebagai kategori terlemah.

### 10.4 Distribusi Kepuasan Dashboard

Untuk setiap kategori, sistem menghitung jumlah jawaban bernilai 4, 3, 2, dan 1.

```text
Persentase nilai X =
jumlah jawaban dengan nilai X
/
jumlah seluruh jawaban rating pada kategori
x 100%
```

### 10.5 Distribusi pada Laporan Excel

Laporan Excel menghitung distribusi per pertanyaan untuk setiap program studi.

```text
Persentase nilai X per soal =
jumlah responden yang memilih nilai X pada soal
/
jumlah respons valid pada soal
x 100%
```

Subtotal kategori dihitung sebagai rata-rata persentase seluruh pertanyaan di dalam kategori.

```text
Subtotal kategori untuk nilai X =
jumlah persentase nilai X seluruh soal dalam kategori
/
jumlah soal dalam kategori
```

Baris rata-rata akhir dihitung dari rata-rata seluruh subtotal kategori.

### 10.6 Perbedaan Dasar Filter Tahun

| Bagian | Sumber Tahun |
|---|---|
| Dashboard | Tahun dari `survey.updated_at` |
| Laporan Excel | Tahun dari `lulusan.tahun_lulus` |
| Pembuatan survei massal | Tahun dari `lulusan.tahun_lulus` |

Perbedaan ini harus disebutkan ketika membahas hasil analisis sistem.

---

## 11. Seeder dan Data Demonstrasi

### 11.1 `DatabaseSeeder`

Seeder default:

1. Membuat atau memperbarui role `admin`.
2. Membuat atau memperbarui role `user`.
3. Membuat akun admin.
4. Membuat akun user reguler.
5. Menghubungkan akun dengan role aktif.
6. Menjalankan `ExcelDataSeeder`.

### 11.2 `ExcelDataSeeder`

`ExcelDataSeeder` mengisi data berdasarkan file:

```text
docs/contoh perhitungan.xlsx
```

Data demonstrasi:

| Fakultas | Responden | Program Studi |
|---|---:|---|
| FTI | 4 | S1 Teknik Komputer dan S1 Sistem Informasi |
| FDIK | 2 | S1 Desain Komunikasi Visual |
| FEB | 1 | S1 Manajemen |

Total data demonstrasi adalah 7 responden dengan tahun lulus 2024.

Seeder ini:

- menghapus data domain lama dalam urutan yang aman terhadap foreign key;
- membuat kategori, pertanyaan, dan pilihan jawaban;
- membuat perusahaan, lulusan, survei selesai, dan jawaban;
- menggunakan kode pertanyaan berawalan fakultas agar unik;
- digunakan untuk membandingkan hasil aplikasi dengan rumus Excel referensi.

> Catatan implementasi: `ExcelDataSeeder` tidak memasukkan data ke tabel `survey_soal`. Laporan tetap dapat membaca respons, tetapi relasi pertanyaan terpilih untuk survei hasil seeder tidak terbentuk.

### 11.3 `SoalSeeder`

`SoalSeeder` berisi instrumen Universitas Dinamika 2026. Seeder ini:

- menghapus data `jawaban`, `soal`, dan `kategoris`;
- membuat kategori B sampai M;
- membuat pertanyaan umum;
- membuat pilihan skala 4 sampai 1.

Seeder dijalankan manual:

```bash
php artisan db:seed --class=SoalSeeder
```

> Perhatian: menjalankan `SoalSeeder` pada database yang telah memiliki relasi survei dan respons dapat menimbulkan masalah foreign key atau mengubah instrumen data demonstrasi.

### 11.4 Akun Default

| Role | Email | Password |
|---|---|---|
| Administrator | `admin@gmail.com` | `password` |
| User Reguler | `user@gmail.com` | `password` |

Password default wajib diganti sebelum aplikasi digunakan pada lingkungan produksi.

---

## 12. Instalasi dan Menjalankan Aplikasi

### 12.1 Prasyarat

- PHP 8.2 atau lebih baru;
- Composer;
- Node.js dan npm;
- Oracle XE;
- ekstensi PHP OCI8;
- kredensial database Oracle.

### 12.2 Langkah Instalasi

```bash
composer install
npm install
```

Salin file environment dan buat application key:

```bash
cp .env.example .env
php artisan key:generate
```

Contoh konfigurasi Oracle:

```env
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=XE
DB_USERNAME=spl
DB_PASSWORD=password
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate
php artisan db:seed
```

Jalankan aplikasi:

```bash
npm run dev
php artisan serve
```

Atau gunakan perintah development gabungan dari Composer:

```bash
composer run dev
```

Build aset produksi:

```bash
npm run build
```

---

## 13. Pengujian

Framework pengujian yang digunakan adalah Pest PHP.

Menjalankan test:

```bash
composer test
```

atau:

```bash
php artisan test
```

### 13.1 Cakupan Test Aktual

Test otomatis saat ini terutama berasal dari Laravel Breeze dan mencakup:

- autentikasi;
- registrasi;
- verifikasi email;
- reset dan pembaruan password;
- konfirmasi password;
- pengelolaan profil;
- test contoh unit dan feature.

### 13.2 Pengujian yang Belum Tersedia

Belum terdapat test otomatis khusus untuk:

- CRUD pengguna lulusan;
- penyimpanan dan filter lulusan;
- CRUD kategori dan pertanyaan;
- pembuatan survei tunggal;
- pembuatan survei massal;
- validasi kode akses;
- submit jawaban survei;
- perhitungan dashboard;
- perhitungan dan isi laporan Excel;
- otorisasi route admin.

Untuk laporan TA, pengujian fungsional dapat dilakukan menggunakan skenario black-box terhadap setiap use case dan membandingkan hasil laporan dengan `docs/contoh perhitungan.xlsx`.

---

## 14. Catatan Kondisi Implementasi Saat Ini

Bagian ini menjelaskan fakta implementasi yang perlu diperhatikan. Poin-poin berikut bukan fitur tambahan, melainkan keterbatasan atau ketidaksesuaian pada kode saat ini.

### 14.1 Validasi Akses Survei

- Proses verifikasi kode pada `/access-survey` memeriksa `is_completed = false`.
- Proses verifikasi kode belum memeriksa `is_active`.
- Halaman langsung `/fill-survey/{code}` hanya mencari kode dan belum memeriksa `is_active` atau `is_completed`.
- Endpoint submit juga belum menolak survei yang sudah selesai atau nonaktif.

### 14.2 Validasi Jawaban Survei

- Request submit hanya memastikan `jawaban` berbentuk array.
- Backend belum memastikan setiap `soal_id` yang dikirim benar-benar termasuk dalam `survey_soal`.
- Backend belum memastikan `jawaban_id` sesuai dengan soal yang dinilai.
- Atribut wajib per pertanyaan terutama ditegakkan melalui HTML `required`, belum melalui validasi dinamis server.

### 14.3 Duplikasi Respons

Tabel `respon_jawaban` belum memiliki unique constraint untuk pasangan `survey_id` dan `soal_id`. Endpoint submit belum menghapus atau memperbarui respons lama sebelum insert, sehingga submit ulang secara langsung berpotensi menghasilkan respons ganda.

### 14.4 Status Aktif Survei

Kolom `survey.is_active` tersedia, tetapi belum ada route atau fitur admin khusus untuk mengaktifkan dan menonaktifkan survei.

### 14.5 Redirect Update Survei

Setelah update survei, controller mengarahkan ke route bernama `survey.index`, sedangkan route daftar survei saat ini bernama `survey`. Kondisi ini berpotensi menghasilkan error route tidak ditemukan.

### 14.6 Filter Dashboard

`DashboardController::index()` saat ini memanggil service tanpa meneruskan parameter request. Tampilan dashboard menyediakan form filter, tetapi nilai filter belum diteruskan oleh controller ke `DashboardService`.

### 14.7 Jenis Perusahaan dan Cabang

- Migrasi project mendefinisikan `jenis_perusahaan` sebagai string nullable tanpa enum atau check constraint.
- Form aktual menyediakan kategori industri seperti Teknologi Informasi, Keuangan, Manufaktur, Pemerintahan, dan BUMN/BUMD, serta pilihan lainnya.
- Pada model database, `cabang_kota` dan `cabang_negara` dibuat sebagai boolean.
- Pada halaman pengisian survei, kedua data tersebut diisi melalui input teks.
- Request submit juga memvalidasinya sebagai string.

Hal ini menunjukkan ketidaksesuaian tipe data antara formulir publik dan skema database.

### 14.8 Pengelolaan Pertanyaan

- Route update pertanyaan menggunakan `Request` biasa, bukan `PertanyaanStoreRequest`.
- Kode soal bersifat unik pada database, tetapi validasi form belum memeriksa aturan unik.
- Service hanya membuat pilihan jawaban ketika jenis soal adalah `multiple_choice`. Soal rating yang dibuat melalui form admin berpotensi tidak memiliki pilihan jawaban apabila form tidak menangani proses lain.

### 14.9 Pengelolaan Lulusan

Modul lulusan saat ini hanya menyediakan daftar, filter, dan tambah. Route edit, update, dan delete belum tersedia.

### 14.10 Konsistensi Penamaan

Beberapa model memakai nama class huruf kecil, misalnya:

- `lulusan`;
- `penggunalulusan`;
- `soal`;
- `jawaban`.

PHP menerima penamaan tersebut, tetapi tidak mengikuti konvensi PSR/Laravel yang biasanya menggunakan `Lulusan`, `PenggunaLulusan`, `Soal`, dan `Jawaban`.

---

## 15. Kebutuhan Nonfungsional yang Terlihat dari Implementasi

### 15.1 Keamanan

- Pengguna internal harus login.
- Modul admin dilindungi middleware role.
- Form Laravel menggunakan perlindungan CSRF.
- Password disimpan dalam bentuk hash.
- Penyelia dapat mengakses survei tanpa login menggunakan kode unik.

### 15.2 Integritas Data

- Proses utama pembuatan dan submit survei menggunakan transaksi database.
- Foreign key digunakan untuk menjaga hubungan antarentitas.
- Email penyelia dan kode survei bersifat unik.
- Kode soal bersifat unik.

### 15.3 Usability

- Responden tidak perlu membuat akun.
- Data perusahaan dan penyelia diisi otomatis dari sistem dan dapat dikonfirmasi.
- Pertanyaan dikelompokkan berdasarkan kategori.
- Pertanyaan difilter berdasarkan fakultas lulusan.
- Admin dapat membuat survei massal.

### 15.4 Portabilitas dan Ketergantungan

Aplikasi bergantung pada Oracle XE dan ekstensi OCI8. Beberapa query menggunakan sintaks Oracle:

```sql
EXTRACT(YEAR FROM ...)
```

Karena itu, pemindahan ke database lain memerlukan pengujian dan kemungkinan penyesuaian query.

---

## 16. Referensi File Implementasi

| Topik | File Utama |
|---|---|
| Route aplikasi | `routes/web.php` |
| Registrasi middleware | `bootstrap/app.php` |
| Otorisasi role | `app/Http/Middleware/CheckRole.php` |
| Alur survei | `app/Http/Controllers/SurveyController.php` |
| Logika survei | `app/Services/SurveyService.php` |
| Dashboard | `app/Services/DashboardService.php` |
| Laporan | `app/Exports/ReportExport.php` |
| Model domain | `app/Models/` |
| Validasi request | `app/Http/Requests/` |
| Skema database | `database/migrations/` |
| Data referensi Excel | `database/seeders/ExcelDataSeeder.php` |
| Instrumen 2026 | `database/seeders/SoalSeeder.php` |
| Form survei publik | `resources/views/fill_page.blade.php` |
| Excel referensi | `docs/contoh perhitungan.xlsx` |

---

## 17. Panduan Menggunakan Dokumen Ini dengan ChatGPT

Contoh prompt:

```text
Saya sedang menyusun laporan Tugas Akhir tentang Sistem Penilaian Lulusan.
Gunakan DOKUMENTASI.md sebagai sumber fakta utama.

Tolong buatkan bagian analisis kebutuhan fungsional dalam bahasa akademik.
Pisahkan kebutuhan Admin, User Reguler, dan Penyelia Perusahaan.
Jangan menyatakan fitur rekomendasi sebagai fitur yang sudah diimplementasikan.
```

Contoh kebutuhan penulisan yang dapat dibantu:

- latar belakang dan rumusan masalah;
- tujuan dan manfaat sistem;
- analisis proses bisnis;
- kebutuhan fungsional dan nonfungsional;
- deskripsi use case;
- penjelasan activity diagram;
- penjelasan class diagram;
- rancangan basis data;
- penjelasan implementasi Laravel;
- skenario pengujian black-box;
- pembahasan hasil perhitungan;
- kesimpulan dan saran pengembangan.

Saat meminta ChatGPT membuat isi laporan:

1. Sebutkan bab atau subbab yang ingin dibuat.
2. Jelaskan format penulisan kampus jika ada.
3. Minta ChatGPT membedakan kondisi aktual dan rekomendasi.
4. Verifikasi kembali istilah, rumus, dan klaim sebelum dimasukkan ke laporan final.

---

## 18. Ringkasan Singkat untuk Konteks AI

SPL adalah aplikasi web Laravel 12 dengan database Oracle untuk mengumpulkan penilaian perusahaan terhadap lulusan Universitas Dinamika. Admin mengelola perusahaan, lulusan, kategori, pertanyaan, survei, dashboard, dan laporan. Penyelia perusahaan mengisi survei tanpa login menggunakan kode akses unik. Setiap survei menilai satu lulusan dan terhubung ke satu perusahaan. Pertanyaan dapat berupa rating, pilihan ganda, atau esai dan dapat diperuntukkan bagi fakultas tertentu atau semua fakultas. Jawaban rating memiliki bobot 1 sampai 4. Dashboard menghitung rata-rata dan distribusi nilai, sedangkan laporan Excel menyajikan data serta ringkasan per tahun lulus, fakultas, program studi, pertanyaan, dan kategori. Sistem telah berfungsi untuk alur utama, tetapi masih memiliki beberapa keterbatasan validasi, konsistensi tipe data, filter dashboard, dan cakupan automated testing.
