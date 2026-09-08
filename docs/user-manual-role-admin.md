# User Manual Admin

## Sistem Penilaian Lulusan Universitas Dinamika

Dokumen ini berisi panduan penggunaan aplikasi SPL untuk pengguna dengan role `admin`. Admin bertugas mengelola data master, membuat survey penilaian lulusan, memantau hasil evaluasi, dan mengunduh laporan.

## 1. Hak Akses Admin

Admin dapat mengakses menu berikut setelah login:

| Menu | Fungsi Utama |
| --- | --- |
| Dashboard | Melihat ringkasan hasil evaluasi lulusan |
| Survey | Membuat, melihat, mengubah, dan menghapus sesi survey |
| Lulusan | Mengelola data lulusan yang akan dinilai |
| Pengguna Lulusan | Mengelola data perusahaan/instansi dan penyelia |
| Pertanyaan | Mengelola daftar pertanyaan survey |
| Aspek Evaluasi | Mengelola kategori/aspek penilaian |
| Cetak Laporan | Mengunduh laporan hasil survey dalam format Excel |
| Arsip Survey | Melihat arsip survey yang sudah selesai diisi |

## 2. Login ke Sistem

1. Buka halaman login aplikasi.
2. Masukkan email dan password akun admin.
3. Klik tombol `Log in`.
4. Setelah berhasil login, sistem menampilkan halaman `Dashboard`.

Catatan:

- Jika email atau password salah, sistem akan menampilkan pesan gagal login.
- Pastikan akun yang digunakan memiliki role `admin`, karena menu pengelolaan data hanya tersedia untuk admin.

## 3. Alur Kerja Utama Admin

Alur penggunaan yang disarankan:

1. Kelola data `Pengguna Lulusan` atau perusahaan.
2. Tambahkan data `Lulusan` dan hubungkan dengan perusahaan tempat lulusan bekerja.
3. Kelola `Aspek Evaluasi`.
4. Tambahkan dan aktifkan `Pertanyaan`.
5. Buat `Survey` tunggal atau `Survey Massal`.
6. Bagikan `Kode Akses` kepada penyelia perusahaan.
7. Pantau status survey sampai selesai diisi.
8. Lihat ringkasan pada `Dashboard`, cek `Arsip Survey`, dan unduh laporan melalui `Cetak Laporan`.

## 4. Dashboard

Menu `Dashboard` digunakan untuk melihat ringkasan hasil survey yang sudah masuk ke arsip.

Informasi yang tersedia:

- jumlah lulusan yang sudah dinilai;
- jumlah respon survey terarsip;
- indeks kepuasan rata-rata;
- kategori/aspek dengan nilai terbaik;
- kategori/aspek dengan nilai terendah;
- grafik responden berdasarkan program studi;
- grafik rata-rata penilaian per kategori;
- distribusi kepuasan per kategori;
- umpan balik atau jawaban esai terbaru.

Cara menggunakan filter Dashboard:

1. Pilih `Periode` jika ingin melihat tahun instrumen tertentu.
2. Pilih `Fakultas` jika ingin membatasi data berdasarkan fakultas.
3. Pilih `Program Studi` jika ingin membatasi data lebih spesifik.
4. Klik `Terapkan`.
5. Klik tombol reset jika ingin kembali menampilkan semua data.

Catatan:

- Data Dashboard diambil dari `Arsip Survey`, sehingga survey yang belum diisi belum masuk ke perhitungan dashboard.
- Jika filter tidak menghasilkan data, grafik dan ringkasan dapat tampil kosong.

## 5. Mengelola Pengguna Lulusan

Menu `Pengguna Lulusan` digunakan untuk menyimpan data perusahaan/instansi dan penyelia yang akan menilai lulusan.

### 5.1 Menambah Pengguna Lulusan

1. Buka menu `Pengguna Lulusan`.
2. Klik tombol `Tambah Pengguna`.
3. Isi bagian `Informasi Perusahaan`:
   - `Nama Perusahaan/Instansi`, wajib diisi.
   - `Jenis Perusahaan`, wajib dipilih.
   - `Nomor Badan Hukum`, opsional.
   - `Alamat Perusahaan`, opsional.
4. Isi bagian `Kontak Penyelia`:
   - `Nama Penyelia`, wajib diisi.
   - `Jabatan Penyelia`, opsional.
   - `Email Penyelia`, wajib diisi dan harus unik.
   - `Nomor WhatsApp/HP`, opsional.
5. Isi bagian `Cakupan Wilayah` jika tersedia:
   - jumlah cabang nasional;
   - jumlah cabang luar negeri;
   - jumlah lulusan yang pernah bekerja;
   - durasi rata-rata bekerja dalam bulan.
6. Klik `Simpan Instansi`.

Catatan input:

- `Email Penyelia` harus menggunakan format email yang valid.
- Nilai jumlah cabang, jumlah lulusan, dan durasi bekerja harus berupa angka minimal 0.
- Jika memilih jenis perusahaan `Lainnya`, isi nama jenis perusahaan secara manual.

### 5.2 Mengubah Pengguna Lulusan

1. Buka menu `Pengguna Lulusan`.
2. Pada baris perusahaan yang akan diubah, klik tombol edit.
3. Ubah data yang diperlukan.
4. Simpan perubahan.

### 5.3 Menghapus Pengguna Lulusan

1. Buka menu `Pengguna Lulusan`.
2. Pada baris perusahaan yang akan dihapus, klik tombol hapus.
3. Konfirmasi penghapusan.

Perhatian:

- Jangan hapus data perusahaan yang masih diperlukan untuk survey aktif atau data lulusan, kecuali sudah dipastikan aman.

## 6. Mengelola Data Lulusan

Menu `Lulusan` digunakan untuk mencatat lulusan yang akan dinilai oleh pengguna lulusan.

### 6.1 Melihat dan Memfilter Data Lulusan

1. Buka menu `Lulusan`.
2. Gunakan filter yang tersedia:
   - nama lulusan;
   - NIM;
   - fakultas;
   - program studi;
   - rentang tahun lulus;
   - status bekerja.
3. Klik `Cari`.
4. Klik `Reset` untuk menghapus filter.

### 6.2 Menambah Data Lulusan

1. Buka menu `Lulusan`.
2. Klik `Tambah Lulusan`.
3. Isi `Informasi Akademik`:
   - `Nama Lengkap`, wajib diisi.
   - `NIM`, wajib diisi dan harus unik.
   - `Fakultas`, wajib dipilih.
   - `Program Studi`, wajib dipilih.
   - `Tahun Lulus`, wajib diisi dengan tanggal kelulusan.
4. Pada bagian `Pengaturan Data`, pilih `Perusahaan / Pengguna Lulusan`.
5. Atur `Status Lulusan`:
   - aktif/tercentang berarti status bekerja;
   - tidak aktif berarti belum bekerja.
6. Klik `Simpan Data`.

Catatan:

- Lulusan wajib dihubungkan dengan perusahaan agar dapat dipakai dalam pembuatan survey massal.
- Fakultas yang digunakan sistem adalah `FTI`, `FDIK`, dan `FEB`.

### 6.3 Melihat Detail Lulusan

1. Buka menu `Lulusan`.
2. Klik tombol lihat/detail pada baris lulusan.
3. Sistem menampilkan detail data lulusan dan perusahaan terkait.

## 7. Mengelola Aspek Evaluasi

Menu `Aspek Evaluasi` digunakan untuk mengelompokkan pertanyaan survey berdasarkan kategori penilaian.

### 7.1 Menambah Aspek Evaluasi

1. Buka menu `Aspek Evaluasi`.
2. Klik `Tambah Kategori`.
3. Isi `Nama Kategori`.
4. Isi `Deskripsi` jika diperlukan.
5. Simpan data.

### 7.2 Mengubah Aspek Evaluasi

1. Buka menu `Aspek Evaluasi`.
2. Klik tombol edit pada kategori yang akan diubah.
3. Ubah nama kategori atau deskripsi.
4. Simpan perubahan.

### 7.3 Menghapus Aspek Evaluasi

1. Buka menu `Aspek Evaluasi`.
2. Klik tombol hapus pada kategori yang akan dihapus.
3. Konfirmasi penghapusan.

Perhatian:

- Penghapusan kategori dapat memengaruhi pertanyaan yang memakai kategori tersebut.

## 8. Mengelola Pertanyaan

Menu `Pertanyaan` digunakan untuk membuat daftar pertanyaan yang akan dipilih saat pembuatan survey.

### 8.1 Menambah Pertanyaan

1. Buka menu `Pertanyaan`.
2. Klik `Buat Pertanyaan`.
3. Isi `Soal Pertanyaan`.
4. Pilih `Kategori Pertanyaan`.
5. Pilih `Peruntukan Fakultas`:
   - `Umum`, tampil untuk semua fakultas;
   - `FTI`, hanya untuk lulusan Fakultas Teknologi dan Informatika;
   - `FDIK`, hanya untuk lulusan Fakultas Desain dan Industri Kreatif;
   - `FEB`, hanya untuk lulusan Fakultas Ekonomi dan Bisnis.
6. Pilih `Tipe Masukan`:
   - `Pilihan Ganda (Radio / Rating)`;
   - `Teks Bebas (Essay)`.
7. Isi `Kode Pertanyaan` jika pertanyaan mengikuti kode standar tertentu.
8. Tentukan apakah pertanyaan wajib diisi melalui switch `Required`.
9. Jika tipe masukan adalah pilihan ganda/rating, atur opsi jawaban dan nilai.
10. Klik `Simpan Pertanyaan`.

Catatan:

- Minimal isi teks pertanyaan, kategori, peruntukan fakultas, dan tipe masukan.
- Untuk tipe pilihan ganda/rating, opsi jawaban wajib diisi.
- Nilai jawaban dipakai dalam perhitungan dashboard dan laporan.

### 8.2 Mengubah Pertanyaan

1. Buka menu `Pertanyaan`.
2. Klik tombol edit pada pertanyaan.
3. Ubah data pertanyaan, kategori, peruntukan, tipe, required, atau opsi jawaban.
4. Simpan perubahan.

### 8.3 Mengaktifkan atau Menonaktifkan Pertanyaan

1. Buka menu `Pertanyaan`.
2. Cari pertanyaan yang akan diubah statusnya.
3. Klik `Matikan` untuk menonaktifkan pertanyaan aktif.
4. Klik `Hidupkan` untuk mengaktifkan kembali pertanyaan nonaktif.

Catatan:

- Hanya pertanyaan aktif yang tersedia saat admin membuat survey baru.

## 9. Mengelola Survey

Menu `Survey` digunakan untuk membuat sesi survey dan melihat status pengisian.

Informasi pada tabel survey:

- judul survey;
- tahun survey;
- nama perusahaan;
- lulusan terkait;
- kode akses;
- status survey;
- aksi lihat/detail dan hapus.

Status survey:

- `Belum Diisi`: penyelia belum mengirim jawaban, survey masih dapat diubah.
- `Selesai`: penyelia sudah mengirim jawaban, survey terkunci dan tidak dapat diubah.

### 9.1 Membuat Survey Tunggal

1. Buka menu `Survey`.
2. Klik `Buat Survey Baru`.
3. Isi bagian `Informasi Utama Survey`:
   - `Judul Survey`, wajib diisi.
   - `Tahun Survey`, wajib dipilih.
   - `Deskripsi / Instruksi Tambahan`, opsional.
4. Pada bagian `Identitas Alumni / Lulusan`, pilih data lulusan.
5. Pada bagian `Identitas Perusahaan`, pilih instansi.
6. Setelah instansi dipilih, data penyelia dan perusahaan dapat terisi otomatis.
7. Periksa dan lengkapi data penyelia/perusahaan jika masih kosong.
8. Pada bagian `Pilih Pertanyaan yang Digunakan`, centang pertanyaan yang akan dimunculkan.
9. Klik `Simpan & Generate Kode`.
10. Sistem menyimpan survey dan membuat `Kode Akses`.

Catatan:

- Minimal satu pertanyaan harus dipilih.
- Pertanyaan yang tampil pada halaman pengisian tetap difilter sesuai peruntukan fakultas lulusan. Pertanyaan `Umum` berlaku untuk semua fakultas.
- Kode akses harus diberikan kepada penyelia agar penyelia dapat mengisi survey tanpa login.

### 9.2 Membuat Survey Massal

Survey massal digunakan untuk membuat survey sekaligus untuk semua lulusan pada tahun lulus tertentu.

1. Buka menu `Survey`.
2. Klik `Buat Survey Massal`.
3. Isi `Judul Survey`.
4. Pilih `Tahun Survey`.
5. Isi `Deskripsi / Instruksi` jika diperlukan.
6. Pilih `Tahun Lulus`.
7. Klik `Lihat Preview Lulusan`.
8. Periksa daftar lulusan yang muncul.
9. Pilih pertanyaan yang akan digunakan.
10. Klik `Buat Survey untuk Semua Lulusan`.

Catatan:

- Tombol pembuatan survey massal akan aktif setelah preview menemukan lulusan.
- Survey massal hanya dibuat untuk lulusan pada tahun tersebut yang sudah memiliki data perusahaan terhubung.
- Jika tidak ada lulusan yang sesuai, sistem menampilkan peringatan.

### 9.3 Melihat Detail Survey

1. Buka menu `Survey`.
2. Klik tombol lihat/detail pada baris survey.
3. Sistem menampilkan judul, deskripsi, data lulusan, data perusahaan, dan pertanyaan yang digunakan.

Jika status `Belum Diisi`:

- admin dapat mengubah survey;
- admin dapat mengubah lulusan, perusahaan, dan daftar pertanyaan;
- klik `Simpan Perubahan` untuk menyimpan.

Jika status `Selesai`:

- form dikunci;
- admin tidak dapat mengubah survey;
- sistem menampilkan hasil jawaban responden.

### 9.4 Menghapus Survey

1. Buka menu `Survey`.
2. Klik tombol hapus pada baris survey.
3. Konfirmasi penghapusan.

Perhatian:

- Penghapusan survey juga menghapus hubungan survey dengan pertanyaan dan jawaban yang tersimpan pada survey tersebut.
- Pastikan data survey tidak lagi dibutuhkan sebelum menghapus.

## 10. Kode Akses Survey

Setiap survey memiliki `Kode Akses` unik.

Cara menggunakan kode akses:

1. Admin mengambil kode dari tabel `Survey`.
2. Admin mengirim kode kepada penyelia perusahaan.
3. Penyelia membuka halaman utama aplikasi.
4. Penyelia memasukkan kode akses.
5. Jika kode valid dan survey belum selesai, sistem membuka halaman pengisian.
6. Setelah penyelia mengirim jawaban, survey berubah menjadi `Selesai`.

Catatan:

- Kode yang salah, tidak valid, atau sudah selesai diisi tidak dapat digunakan.
- Penyelia tidak perlu login untuk mengisi survey.

## 11. Cetak Laporan

Menu `Cetak Laporan` digunakan untuk mengunduh laporan hasil survey dalam format Excel `.xlsx`.

Cara mengunduh laporan:

1. Buka menu `Cetak Laporan`.
2. Pilih filter jika diperlukan:
   - `Tahun Lulus`;
   - `Fakultas`;
   - `Program Studi`.
3. Klik `Download Excel`.
4. Sistem mengunduh file laporan.

Catatan format laporan:

- Jika tahun lulus dikosongkan, laporan dibuat dengan sheet per tahun lulus.
- Data pada sheet dibagi berdasarkan fakultas.
- Laporan berisi data lulusan, responden, perusahaan, dan nilai per soal.
- Bagian bawah laporan memuat ringkasan distribusi persentase jawaban.

## 12. Arsip Survey

Menu `Arsip Survey` digunakan untuk melihat survey yang sudah selesai dan tersimpan sebagai arsip evaluasi.

### 12.1 Melihat Daftar Arsip

1. Buka menu `Arsip Survey`.
2. Gunakan filter jika diperlukan:
   - pencarian nama, NIM, perusahaan, atau penyelia;
   - tahun instrumen;
   - fakultas;
   - program studi.
3. Klik `Terapkan`.
4. Klik `Reset` untuk menghapus filter.

### 12.2 Melihat Detail Arsip

1. Buka menu `Arsip Survey`.
2. Klik `Lihat` pada salah satu data arsip.
3. Sistem menampilkan:
   - identitas lulusan;
   - identitas perusahaan;
   - identitas penyelia;
   - tanggal pengisian;
   - daftar pertanyaan dan jawaban.

### 12.3 Cetak atau Simpan Detail Arsip sebagai PDF

1. Buka detail arsip.
2. Klik `Cetak / Simpan PDF`.
3. Pada dialog print browser, pilih printer atau opsi simpan PDF.
4. Simpan atau cetak dokumen.

Catatan:

- Arsip bersifat permanen sebagai snapshot data saat survey disubmit.
- Perubahan pada data master setelah survey selesai tidak mengubah isi arsip.

## 13. Profil Akun Admin

Admin dapat mengelola profil akun melalui menu profil.

Fitur yang tersedia:

- mengubah informasi profil;
- mengubah password;
- menghapus akun.

Perhatian:

- Gunakan password yang kuat.
- Jangan menghapus akun admin utama jika masih dibutuhkan untuk operasional sistem.

## 14. Troubleshooting

| Masalah | Penyebab Umum | Solusi |
| --- | --- | --- |
| Menu admin tidak muncul | Akun tidak memiliki role admin | Gunakan akun admin atau periksa role akun |
| Survey massal tidak bisa dibuat | Tidak ada lulusan pada tahun tersebut yang terhubung dengan perusahaan | Lengkapi data lulusan dan pengguna lulusan terlebih dahulu |
| Pertanyaan tidak muncul saat membuat survey | Pertanyaan nonaktif | Aktifkan pertanyaan pada menu Pertanyaan |
| Pertanyaan tidak tampil di halaman pengisian | Peruntukan fakultas tidak sesuai | Gunakan peruntukan `Umum` atau fakultas yang sesuai dengan lulusan |
| Kode akses ditolak | Kode salah atau survey sudah selesai | Periksa kode pada tabel Survey dan status survey |
| Laporan kosong | Belum ada survey selesai atau filter terlalu spesifik | Cek Arsip Survey atau reset filter laporan |
| Survey selesai tidak bisa diedit | Sistem mengunci survey setelah responden submit | Gunakan data arsip/detail survey untuk melihat hasil |

## 15. Rekomendasi Operasional

- Input data perusahaan terlebih dahulu sebelum data lulusan.
- Pastikan setiap lulusan yang akan disurvey sudah terhubung dengan perusahaan.
- Gunakan pertanyaan `Umum` untuk aspek yang berlaku lintas fakultas.
- Gunakan peruntukan fakultas khusus hanya untuk pertanyaan yang benar-benar spesifik.
- Periksa preview lulusan sebelum membuat survey massal.
- Simpan daftar kode akses yang sudah dibagikan kepada penyelia.
- Unduh laporan secara berkala sebagai cadangan administratif.
- Gunakan Arsip Survey sebagai acuan resmi untuk data survey yang sudah selesai.
