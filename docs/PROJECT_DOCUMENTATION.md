# 📋 SPL — Aplikasi Survey Lulusan

> Aplikasi web berbasis Laravel untuk pengelolaan survey kepuasan kerja lulusan oleh instansi kampus (Universitas Dinamika).

---

## 📌 Gambaran Umum

**SPL (Survey Pengguna Lulusan)** adalah sebuah aplikasi web yang dirancang untuk memfasilitasi proses pengumpulan data kepuasan dunia kerja terhadap lulusan perguruan tinggi. Aplikasi ini memungkinkan admin kampus untuk membuat dan mengelola survey, sementara pihak perusahaan/instansi pengguna lulusan dapat mengisi survey tersebut menggunakan kode akses unik — **tanpa perlu membuat akun**.

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| **Backend Framework** | Laravel 12 (PHP ^8.2) |
| **Frontend Starter** | Laravel Breeze |
| **Build Tool** | Vite |
| **CSS Framework** | Tailwind CSS |
| **Database Driver** | Oracle (via `yajra/laravel-oci8`) |
| **Testing** | Pest PHP |
| **Dev Tools** | Laravel Pail, Laravel Pint |

---

## 👥 Peran Pengguna (Roles)

Sistem ini memiliki **3 peran** dengan hak akses yang berbeda-beda:

### 1. 🔴 Admin
- Memiliki **akses penuh** ke seluruh fitur website.
- Dapat mengelola semua data: survey, pertanyaan, kategori, lulusan, dan pengguna lulusan.
- Mengakses dashboard statistik dan laporan.
- Login melalui halaman autentikasi standar.

### 2. 🟡 Petinggi Kampus *(User Biasa)*
- Hanya dapat mengakses:
  - **Dashboard** — melihat ringkasan statistik.
  - **Halaman Cetak Report** *(belum diimplementasikan — dalam rencana pengembangan)*.
- Tidak dapat membuat atau mengubah data apapun.
- Login melalui halaman autentikasi standar.

### 3. 🟢 Pengguna Lulusan / Perusahaan
- **Tidak perlu login/registrasi**.
- Mengisi survey dengan memasukkan **kode akses unik** yang diberikan oleh admin.
- Hanya dapat mengakses halaman pengisian survey sesuai kode akses.
- Data perusahaan dan penyelia dapat diverifikasi/diperbarui saat pengisian form.

---

## 🏗️ Arsitektur Aplikasi

Aplikasi mengikuti pola arsitektur **MVC dengan Service Layer**:

```
app/
├── Http/
│   ├── Controllers/     # Menghandle request HTTP
│   ├── Middleware/      # Middleware autentikasi & role
│   └── Requests/        # Form Request Validation
├── Models/              # Eloquent Models
├── Services/            # Business Logic Layer
└── View/                # View Composers / Providers
```

### Service Layer
| Service | Tanggung Jawab |
|---|---|
| `DashboardService` | Mengambil statistik dan ringkasan data untuk dashboard |
| `LulusanService` | Logika pengelolaan data lulusan |
| `PenggunaLulusanService` | Logika pengelolaan data perusahaan |
| `PertanyaanService` | Pengelolaan soal/pertanyaan survey termasuk jawaban |
| `SurveyService` | Alur pengisian survey: verifikasi kode, penampilan soal, simpan jawaban |

---

## 🗂️ Struktur Database

### Entity Relationship Overview

```
pengguna_lulusan (Perusahaan)
    └──< lulusan (Alumni)
              └──< survey (Sesi Survey)
                        ├──>< soal (via survey_soal — Pivot)
                        └──< respon_jawaban (Jawaban Responden)
                                  ├──> soal
                                  └──> jawaban (Opsi Pilihan)

kategoris ──< soal (setiap pertanyaan punya kategori)
```

---

### 📊 Detail Tabel

#### `pengguna_lulusan` — Data Perusahaan / Instansi
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `nama_perusahaan` | string | Nama perusahaan/instansi |
| `nama_penyelia` | string | Nama pengawas/atasan lulusan |
| `kontak_penyelia` | string (nullable) | Nomor HP penyelia |
| `email_penyelia` | string (unique) | Email penyelia |
| `jumlah_lulusan` | integer (nullable) | Jumlah lulusan yang bekerja |
| `durasi_lulusan_bekerja` | integer (nullable) | Lama bekerja (dalam bulan) |
| `nomor_badan_hukum` | string (nullable) | Nomor legalitas perusahaan |
| `alamat_perusahaan` | text (nullable) | Alamat lengkap |
| `kontak_perusahaan` | string (nullable) | Kontak kantor |
| `jenis_perusahaan` | enum | `government`, `private`, `startup`, `nonprofit` |
| `cabang_kota` | boolean | Memiliki cabang di kota lain |
| `cabang_negara` | boolean | Memiliki cabang di luar negeri |

---

#### `lulusan` — Data Alumni
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `pengguna_lulusan_id` | FK | Relasi ke tabel `pengguna_lulusan` |
| `nama` | string | Nama lengkap lulusan |
| `nim` | string | Nomor Induk Mahasiswa |
| `program_studi` | string | Program studi |
| `fakultas` | string | Fakultas lulusan *(ditambahkan via migration)* |
| `tahun_lulus` | date | Tahun kelulusan |
| `status` | boolean | Status aktif |

---

#### `kategoris` — Kategori Pertanyaan *(Baru)*
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `nama_kategori` | string | Nama kategori (misal: Kompetensi, Etika, dll.) |
| `deskripsi` | text (nullable) | Keterangan tambahan kategori |

---

#### `soal` — Bank Pertanyaan Survey
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `soal` | text | Isi pertanyaan |
| `kode` | string (unique) | Kode unik pertanyaan (misal: `f101`) |
| `kategori_id` | FK | Relasi ke tabel `kategoris` *(Baru)* |
| `peruntukan_fakultas` | enum | `FTI`, `FDIK`, `FEB`, `Umum` — default: `Umum` *(Baru)* |
| `jenis_soal` | enum | `multiple_choice`, `essay`, `rating` |
| `is_required` | boolean | Wajib dijawab atau tidak |
| `is_active` | boolean | Status aktif pertanyaan |

---

#### `jawaban` — Opsi Jawaban (untuk Multiple Choice)
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `soal_id` | FK | Relasi ke tabel `soal` |
| `jawaban` | text | Isi pilihan jawaban |
| `nilai` | integer | Bobot/skor jawaban |
| `urutan` | integer (nullable) | Urutan tampil |

---

#### `survey` — Sesi Survey
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `access_code` | string (unique) | Kode unik untuk akses survey (maks. 10 karakter) |
| `lulusan_id` | FK | Relasi ke tabel `lulusan` |
| `pengguna_lulusan_id` | FK | Relasi ke tabel `pengguna_lulusan` |
| `judul` | string | Judul sesi survey |
| `deskripsi` | text (nullable) | Keterangan sesi survey |
| `is_completed` | boolean | Status selesai diisi |
| `is_active` | boolean | Status aktif sesi |

---

#### `survey_soal` — Pivot Survey ↔ Soal
| Kolom | Tipe | Keterangan |
|---|---|---|
| `survey_id` | FK | Relasi ke tabel `survey` |
| `soal_id` | FK | Relasi ke tabel `soal` |

---

#### `respon_jawaban` — Rekaman Jawaban Responden
| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary Key |
| `survey_id` | FK | Relasi ke tabel `survey` |
| `soal_id` | FK | Pertanyaan yang dijawab |
| `jawaban_id` | FK (nullable) | Opsi yang dipilih (untuk multiple choice) |
| `jawaban_text` | text (nullable) | Jawaban teks bebas (untuk essay) |
| `responden` | string (nullable) | Identitas responden |

---

## 🔗 Routing

### Publik (Tanpa Login)
| Method | URI | Aksi |
|---|---|---|
| `GET` | `/` | Redirect ke halaman login |
| `POST` | `/access-survey` | Verifikasi kode akses survey |
| `GET` | `/fill-survey/{code}` | Halaman pengisian survey |
| `POST` | `/submit-survey/{code}` | Simpan jawaban survey |

### Autentikasi (Admin & User Biasa)
| Method | URI | Aksi |
|---|---|---|
| `GET` | `/dashboard` | Dashboard utama |
| `GET/PATCH/DELETE` | `/profile` | Manajemen profil |

### Khusus Admin
| Kelompok | Method | URI | Aksi |
|---|---|---|---|
| **Survey** | `GET` | `/survey` | Daftar survey |
| | `GET` | `/addsurvey` | Form tambah survey |
| | `POST` | `/survey/store` | Simpan survey baru |
| | `GET/PUT` | `/survey/{id}/edit` | Edit survey |
| **Lulusan** | `GET` | `/lulusan` | Daftar lulusan |
| | `GET` | `/addgrad` | Form tambah lulusan |
| | `POST` | `/lulusan.store` | Simpan lulusan baru |
| **Pengguna Lulusan** | `GET` | `/penggunalulusan` | Daftar perusahaan |
| | `GET` | `/create` | Form tambah perusahaan |
| | `POST` | `/pengguna.store` | Simpan perusahaan |
| | `GET/PUT/DELETE` | `/penggunalulusan/{id}` | Edit & hapus perusahaan |
| **Kategori** | *Resource* | `/kategori` | CRUD Kategori (kecuali `show`) |
| **Pertanyaan** | `GET` | `/pertanyaan` | Daftar pertanyaan |
| | `GET` | `/addquestion` | Form tambah pertanyaan |
| | `GET` | `/pertanyaan/{id}/edit` | Edit pertanyaan |
| | `GET` | `/pertanyaan/{id}/switch` | Toggle aktif/nonaktif |
| | `PUT` | `/pertanyaan/{id}` | Update pertanyaan |
| | `POST` | `/savequestion` | Simpan pertanyaan baru |

---

## 📁 Halaman / Views

```
resources/views/
├── welcome.blade.php          # Landing / halaman publik utama
├── fill_page.blade.php        # Halaman pengisian survey (publik)
├── dashboard.blade.php        # Dashboard (admin & user biasa)
├── layouts/                   # Layout utama
├── auth/                      # Halaman login, register
├── profile/                   # Halaman profil pengguna
├── components/                # Komponen UI reusable
└── admin/
    ├── dashboard/             # Konten dashboard admin
    ├── survey/                # CRUD Survey
    ├── pertanyaan/            # CRUD Pertanyaan
    ├── kategori/              # CRUD Kategori
    ├── lulusan/               # CRUD Lulusan
    └── penggunalulusan/       # CRUD Pengguna Lulusan (Perusahaan)
```

---

## 🔄 Alur Pengisian Survey

```
1. Admin membuat sesi Survey
   → menentukan Lulusan & Perusahaan
   → memilih Soal dari bank pertanyaan
   → sistem generate kode akses unik

2. Kode akses dikirim ke Penyelia Perusahaan

3. Penyelia membuka website → input kode akses
   → sistem validasi kode
   → redirect ke halaman fill_page

4. Penyelia mengisi form:
   → verifikasi/update data perusahaan
   → menjawab semua pertanyaan survey

5. Submit → jawaban tersimpan di tabel respon_jawaban
   → survey ditandai is_completed = true
```

---

## 📅 Riwayat Pembaruan (Changelog)

| Tanggal | Perubahan |
|---|---|
| Maret 2026 | Inisialisasi project, struktur dasar tabel (`pengguna_lulusan`, `lulusan`, `soal`, `jawaban`, `survey`) |
| 24 Mar 2026 | Penambahan tabel `respon_jawaban` untuk merekam jawaban responden |
| 15 Apr 2026 | Implementasi sistem roles (`roles`, `user_roles`) |
| 19 Apr 2026 | Penambahan pivot table `survey_soal` |
| 04 Mei 2026 | Penambahan kolom `fakultas` di tabel `lulusan` |
| **05 Mei 2026** | **Penambahan tabel `kategoris` — setiap pertanyaan kini dapat dikelompokkan ke dalam kategori** |
| **05 Mei 2026** | **Penambahan kolom `kategori_id` (FK) dan `peruntukan_fakultas` (enum: FTI/FDIK/FEB/Umum) di tabel `soal`** |

---

## 🚧 Fitur yang Belum Diimplementasikan

- [ ] **Halaman Cetak Report** — Halaman khusus untuk petinggi kampus mencetak rekap hasil survey (PDF/Excel).
- [ ] **Filter Pertanyaan Berdasarkan Fakultas** — Logika otomatis menampilkan soal yang sesuai `peruntukan_fakultas` dengan `fakultas` lulusan saat pengisian survey.
- [ ] **Notifikasi / Email Blast** — Pengiriman kode akses otomatis ke email penyelia.
- [ ] **Analitik & Visualisasi Grafik** — Grafik hasil survey per kategori di dashboard.

---

## ⚙️ Cara Menjalankan Aplikasi (Development)

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & npm
- Oracle Database (dengan konfigurasi OCI8)
- Laragon (Windows) atau server lokal setara

### Langkah Setup
```bash
# 1. Clone repository
git clone <repo-url>
cd spl

# 2. Install dependencies
composer install
npm install

# 3. Salin file environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env (koneksi Oracle)
# DB_CONNECTION=oracle
# DB_HOST=127.0.0.1
# DB_PORT=1521
# DB_DATABASE=<nama_db>
# DB_USERNAME=<user>
# DB_PASSWORD=<password>

# 5. Jalankan migrasi
php artisan migrate

# 6. Jalankan server development
php artisan serve
npm run dev

# Atau gunakan composer script (semua sekaligus)
composer dev
```

### URL Akses
- **Aplikasi**: `http://localhost:8000`
- **Login Admin/User**: `http://localhost:8000/login`
- **Akses Survey Publik**: `http://localhost:8000/` (input kode akses)

---

## 📂 Struktur File Kunci

```
spl/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── KategoriController.php      ← CRUD Kategori
│   │   ├── LulusanController.php
│   │   ├── PenggunaLulusanController.php
│   │   ├── PertanyaanController.php    ← CRUD Pertanyaan + Jawaban
│   │   └── SurveyController.php        ← Logic survey publik
│   ├── Models/
│   │   ├── Kategori.php
│   │   ├── soal.php                    ← Model pertanyaan (dengan kategori & fakultas)
│   │   ├── Survey.php
│   │   ├── lulusan.php
│   │   ├── penggunalulusan.php
│   │   ├── jawaban.php
│   │   └── ResponJawaban.php
│   └── Services/
│       ├── DashboardService.php
│       ├── LulusanService.php
│       ├── PenggunaLulusanService.php
│       ├── PertanyaanService.php
│       └── SurveyService.php
├── database/migrations/
│   ├── ..._create_pengguna_lulusans_table.php
│   ├── ..._create_lulusans_table.php
│   ├── ..._create_soal_table.php
│   ├── ..._create_jawaban_table.php
│   ├── ..._create_survey_table.php
│   ├── ..._create_respon_jawaban_table.php
│   ├── ..._roles.php
│   ├── ..._user_roles.php
│   ├── ..._survey_soal.php
│   ├── ..._add_fakultas_to_lulusan_table.php
│   ├── ..._create_kategoris_table.php           ← Kategori (Baru)
│   ├── ..._update_soal_table_for_kategori.php   ← Tambah kategori_id ke soal (Baru)
│   └── ..._add_peruntukan_fakultas_to_soal_table.php  ← Tambah peruntukan_fakultas (Baru)
├── resources/views/
│   ├── fill_page.blade.php             ← Halaman publik pengisian survey
│   ├── welcome.blade.php               ← Landing page
│   └── admin/                          ← Panel admin
└── routes/
    └── web.php
```

---

*Dokumentasi ini dibuat pada 8 Mei 2026 dan mencerminkan kondisi project terkini.*
