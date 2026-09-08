# Saran Pembaruan Desain Admin Panel SPL

## Ringkasan rekomendasi

Gunakan konsep **"Undika Insight"**: admin panel yang terasa tenang, profesional, dan berorientasi pada pengambilan keputusan. Fokusnya bukan membuat layar terlihat ramai, melainkan membantu admin menjawab tiga hal secepat mungkin:

1. Bagaimana perkembangan respons survei?
2. Data lulusan atau perusahaan mana yang perlu ditindaklanjuti?
3. Bagaimana mengelola dan mengekspor data tanpa bingung mencari menu?

Konsep ini cocok dengan SPL karena sistem utamanya adalah tracer study: banyak data tabel, status survei, filter periode/fakultas/prodi, dan hasil evaluasi yang perlu dibaca dengan cepat.

---

## Temuan dari tampilan saat ini

Beberapa halaman sudah mengarah ke gaya modern (khususnya dashboard), namun secara keseluruhan pengalaman masih terasa terpisah-pisah.

- **Komponen belum konsisten.** Contohnya, tombol, radius kartu, warna badge, dan gaya filter berbeda antara halaman Survei, Lulusan, dan Laporan.
- **Navigasi datar.** Semua menu admin berada dalam satu daftar tanpa pengelompokan berdasarkan pekerjaan pengguna. Ini membuat menu terlihat panjang dan sulit dipindai.
- **Halaman data terlalu berorientasi tabel.** Tabel memang penting, tetapi filter, jumlah data, status, dan aksi utama belum memiliki hierarki visual yang kuat.
- **Aksi berisiko mudah terjangkau.** Tombol hapus berbasis ikon di baris tabel perlu konfirmasi yang lebih informatif dan pemisahan visual yang lebih jelas dari aksi biasa.
- **Terlalu banyak gaya inline.** Warna dan ukuran yang ditulis langsung pada banyak elemen akan membuat penyempurnaan desain berikutnya lambat dan mudah tidak konsisten.
- **Perlu pemeriksaan encoding teks.** Ada karakter yang tampak tidak terbaca pada beberapa tampilan sumber, misalnya pengganti tanda pisah. Pastikan semua berkas Blade disimpan sebagai UTF-8 agar teks antarmuka selalu rapi.

---

## Arah visual yang disarankan

Gunakan palet netral dengan biru dan teal yang tenang sebagai penanda aksi. Identitas institusi cukup ditampilkan melalui logo resmi Undika; jangan membuat identitas tambahan dari blok warna. Latar dan komponen netral membuat dashboard serta data nyaman dibaca dalam waktu lama.

| Elemen | Rekomendasi | Penggunaan |
| --- | --- | --- |
| Warna utama | `#2563EB` biru | Tombol primer, menu aktif, dan fokus input |
| Biru gelap | `#1D4ED8` | Hover tombol primer dan tautan penting |
| Teal aksen | `#0F766E` | Ringkasan positif dan informasi pendukung, secukupnya |
| Latar aplikasi | `#F8FAFC` | Area konten utama |
| Permukaan kartu | `#FFFFFF` | Kartu, tabel, panel filter |
| Teks utama | `#0F172A` | Judul dan angka penting |
| Teks sekunder | `#64748B` | Keterangan dan metadata |
| Garis batas | `#E2E8F0` | Pemisah ringan dan border komponen |
| Berhasil/perlu perhatian | Hijau `#16A34A`, kuning `#D97706`, merah `#DC2626` | Status, bukan dekorasi |

Gunakan font **Inter** yang sudah tersedia. Hierarki yang cukup: judul halaman 24-28 px, judul panel 16-18 px, teks utama 14 px, metadata 12-13 px. Pakai radius 10-12 px secara konsisten dan bayangan sangat lembut; border tipis lebih penting daripada bayangan tebal.

### Prinsip tampilan

- Satu halaman, satu aksi primer yang paling menonjol.
- Status selalu memakai teks + warna + ikon; jangan hanya mengandalkan warna.
- Beri ruang kosong yang cukup antarbagian; jangan membungkus setiap hal kecil dalam kartu.
- Ikon selalu disertai tooltip, dan pada aksi penting sebaiknya disertai label teks.
- Gunakan istilah yang konsisten: pilih satu istilah, misalnya **Survei**, bukan bergantian dengan "Survey" pada antarmuka berbahasa Indonesia.

---

## Struktur navigasi yang disarankan

Sidebar tetap permanen di desktop dan berubah menjadi drawer di mobile. Kelompokkan menu menurut tujuan kerja, dengan label kecil agar cepat dipahami.

```text
Logo Undika + "SPL"

UTAMA
  Dashboard

DATA MASTER
  Lulusan
  Perusahaan / Pengguna Lulusan

SURVEI & INSTRUMEN
  Survei
  Pertanyaan
  Aspek Evaluasi

LAPORAN
  Buat Laporan
  Arsip Survei

------------------
Profil Saya
Keluar
```

Catatan:

- Ubah label **"Cetak Laporan"** menjadi **"Buat Laporan"** karena hasilnya berupa unduhan Excel, bukan sekadar cetak.
- Ubah **"Pengguna Lulusan"** menjadi **"Perusahaan"** pada navigasi bila data yang dikelola memang perusahaan pemberi kerja. Istilah lama masih dapat ditampilkan pada teks penjelas bila diperlukan.
- Tampilkan indikator jumlah pada menu Survei, misalnya badge untuk survei yang belum diisi atau perlu dikirim ulang.
- Navbar cukup untuk tombol sidebar, breadcrumb, notifikasi yang benar-benar berguna, dan menu profil. Jangan gunakan dropdown notifikasi kosong pada rilis final; sembunyikan hingga fitur notifikasi tersedia.

---

## Rancangan tiap halaman utama

### 1. Dashboard: halaman kerja, bukan sekadar kumpulan grafik

Susun dashboard dalam urutan tindakan berikut:

```text
Judul: Selamat datang, [Nama Admin]        [Periode] [Fakultas] [Prodi] [Terapkan]
Ringkasan: Total survei | Tingkat respons | Rata-rata penilaian | Perlu tindak lanjut
Grafik tren respons  --------------------  Distribusi respons per fakultas/prodi
Kinerja per aspek    --------------------  Aspek evaluasi terendah
Tindak lanjut cepat  --------------------  Umpan balik terbaru
```

Yang perlu diprioritaskan:

- Tambahkan **tingkat respons** (`survei selesai / survei terkirim`) karena ini metrik operasional paling penting. Dashboard saat ini sudah memiliki total survei, jumlah lulusan, dan rata-rata nilai; metrik ini melengkapinya.
- Tambahkan kartu **"Perlu tindak lanjut"** berisi survei belum diisi, data tanpa perusahaan, atau komentar baru. Kartu harus dapat diklik menuju daftar yang sudah terfilter.
- Tampilkan grafik tren berdasarkan bulan atau periode instrumen bila datanya tersedia. Grafik batang per prodi dan skor kategori yang sudah ada tetap berguna sebagai analisis lanjutan.
- Pertahankan filter periode, fakultas, dan prodi yang sudah tersedia, tetapi letakkan dalam satu panel ringkas yang dapat dilipat pada layar kecil.
- Gunakan keadaan kosong yang menjelaskan langkah berikutnya, contoh: "Belum ada respons pada filter ini. Ubah periode atau buat survei baru."

### 2. Daftar Survei: pusat operasi survei

Header halaman harus memiliki judul, jumlah hasil, dan satu tombol primer **"Buat survei"**. Tombol **"Buat massal"** menjadi aksi sekunder di sampingnya.

Filter yang disarankan: pencarian tunggal (judul, perusahaan, atau lulusan), tahun/periode, fakultas, prodi, dan status. Pada filter aktif, tampilkan chip yang bisa dihapus satu per satu serta tombol "Reset".

Tabel sebaiknya memuat: judul, periode, perusahaan, lulusan, tanggal kirim, status, terakhir diperbarui, dan aksi. Kode akses jangan menjadi kolom yang terlalu dominan; tampilkan dalam popover atau salin lewat tombol kecil. Ganti dua ikon aksi terpisah dengan menu tiga titik berisi **Lihat/Edit**, **Salin tautan/kode**, **Kirim ulang**, dan **Hapus**. Aksi hapus harus memunculkan modal konfirmasi yang menyebut judul survei yang akan dihapus.

Tambahkan ringkasan kecil di atas tabel: **Semua**, **Belum diisi**, **Selesai**, dan bila ada **Terkirim hari ini**. Setiap ringkasan berfungsi sebagai filter cepat.

### 3. Lulusan dan Perusahaan: daftar data yang mudah dicari

Gunakan pola halaman data yang sama untuk Lulusan dan Perusahaan agar pengguna tidak perlu belajar ulang.

- Bar atas: judul, total hasil, tombol primer "Tambah ...", dan tombol ekspor sekunder bila fiturnya benar-benar tersedia.
- Kolom pencarian ditempatkan paling kiri dan dibuat lebih lebar daripada filter lain.
- Filter lanjutan berada dalam panel yang dapat dibuka/tutup. Filter aktif tetap tampak sebagai chip di luar panel.
- Baris tabel dapat diklik untuk membuka detail, sementara menu aksi menjadi opsi alternatif yang mudah dijangkau.
- Halaman detail lulusan memakai ringkasan identitas di bagian atas, lalu tab: **Profil**, **Riwayat Survei**, dan **Status Pekerjaan**. Hindari membuat semua informasi dalam satu halaman panjang.

### 4. Form tambah/edit: alur yang meminimalkan kesalahan

Untuk form panjang seperti membuat survei, gunakan langkah (stepper):

```text
1. Pilih lulusan  ->  2. Pilih perusahaan/responden  ->  3. Atur survei  ->  4. Tinjau & buat
```

Setiap langkah menyimpan pilihan sementara dan menjelaskan data yang diperlukan. Pada desktop, ringkasan pilihan dapat berada di sisi kanan. Pada mobile, letakkan setelah isi langkah. Gunakan validasi di dekat input yang bermasalah, label yang jelas, bantuan singkat untuk istilah teknis, dan tombol footer yang tetap terlihat: **Batal**, **Simpan draf** (bila ada), **Lanjutkan/Buat survei**.

Form Lulusan dan Perusahaan cukup menggunakan satu kolom pada ponsel dan maksimal dua kolom pada desktop. Kelompokkan bidang menjadi "Identitas", "Akademik", dan "Pekerjaan/Perusahaan".

### 5. Pertanyaan dan Aspek Evaluasi: kelola instrumen dengan aman

Tampilkan aspek evaluasi sebagai daftar/kartu kecil dengan jumlah pertanyaan di dalamnya. Di halaman Pertanyaan, gunakan urutan yang terlihat jelas, badge jenis pertanyaan (rating/essay), status aktif, dan aspek terkait.

Sediakan aksi **Pratinjau survei** supaya admin dapat memeriksa tampilan responden sebelum instrumen digunakan. Hindari tindakan aktif/nonaktif melalui tautan GET; dari sudut pengalaman pengguna, gunakan switch dengan label status serta notifikasi berhasil/gagal.

### 6. Laporan dan Arsip: fokus pada hasil yang akan dibuat

Halaman Laporan saat ini sudah memiliki arah yang baik. Sederhanakan menjadi satu alur: pilih cakupan data -> lihat ringkasan data yang akan diekspor -> unduh Excel. Tampilkan estimasi jumlah respons dan periode di dekat tombol unduh agar admin yakin sebelum membuat berkas.

Arsip Survei sebaiknya menampilkan informasi yang mudah dipindai: periode instrumen, lulusan, perusahaan, tanggal respons, status, dan tombol lihat detail. Di detail arsip, gunakan header identitas dan tab **Respons**, **Penilaian per Aspek**, serta **Catatan**.

---

## Sistem komponen yang perlu distandardkan

Buat satu stylesheet atau komponen Blade bersama, lalu gunakan di semua halaman. Set minimum komponen berikut:

| Komponen | Aturan |
| --- | --- |
| Tombol | Primer biru; sekunder outline; tersier teks; bahaya merah. Tinggi seragam 36-40 px. |
| Kartu/panel | Latar putih, border abu-abu muda, radius 12 px, padding 16-24 px. |
| Input/filter | Label di atas input; tinggi 40 px; fokus biru dengan ring yang kontras. |
| Badge status | Bentuk pill ringan, ikon + teks, warna semantik yang konsisten. |
| Tabel | Header tetap saat scroll bila data panjang, tinggi baris sekitar 52 px, aksi terkumpul dalam menu. |
| Empty state | Ikon sederhana, penjelasan, dan satu CTA relevan. |
| Toast/notifikasi | Muncul setelah simpan, hapus, ekspor, atau perubahan status; dapat ditutup. |
| Modal konfirmasi | Dipakai untuk hapus dan tindakan permanen, menampilkan objek yang terdampak. |

Pindahkan gaya yang saat ini tersebar di atribut `style` ke kelas CSS/utility yang bermakna, misalnya `btn-brand`, `status-success`, `app-card`, dan `filter-panel`. Ini akan membuat perubahan warna atau spacing cukup dilakukan dari satu tempat.

---

## Responsif dan aksesibilitas

- Desktop: sidebar lebar sekitar 260 px, area konten maksimal sekitar 1440 px, tabel tetap dapat digulir horizontal.
- Tablet: sidebar menjadi mode ringkas/drawer; filter berubah menjadi dua kolom.
- Ponsel: drawer menutup otomatis setelah menu dipilih; seluruh filter satu kolom; tombol aksi primer dapat memenuhi lebar layar; tabel penting dapat berubah menjadi kartu ringkas.
- Pastikan kontras teks dan warna status memenuhi standar WCAG AA, ukuran target sentuh minimal 44 x 44 px pada mobile, dan semua ikon memiliki label/tooltip yang dapat dibaca pembaca layar.
- Jangan hanya mengandalkan hover: fokus keyboard harus terlihat jelas pada menu, tombol, input, dan aksi tabel.

---

## Urutan implementasi yang realistis

### Tahap 1 - fondasi dan dampak cepat

1. Tetapkan token warna, tipografi, spacing, tombol, badge, kartu, tabel, dan alert bersama.
2. Rapikan sidebar/navbar, kelompokkan menu, dan seragamkan judul halaman serta breadcrumb.
3. Perbaiki halaman Daftar Survei dan Lulusan dengan pola filter, status, aksi tabel, dan empty state yang sama.
4. Periksa UTF-8 seluruh tampilan agar tidak ada karakter rusak.

### Tahap 2 - alur kerja utama

1. Ubah form pembuatan survei menjadi stepper.
2. Tambahkan filter aktif, filter cepat status, dan aksi salin/kirim ulang survei.
3. Standarkan halaman Perusahaan, Pertanyaan, Aspek Evaluasi, serta detail data.

### Tahap 3 - insight dan penyempurnaan

1. Tambahkan tingkat respons, daftar tindak lanjut, dan tren pada dashboard.
2. Hubungkan kartu/diagram dashboard ke halaman daftar dengan filter yang sesuai.
3. Lakukan uji kegunaan singkat dengan admin: minta mereka membuat survei, mencari lulusan, dan mengunduh laporan; catat titik yang membuat mereka berhenti atau ragu.

---

## Hasil yang diharapkan

Setelah arah ini diterapkan, admin akan melihat konteks dan aksi utama lebih dahulu, menemukan data lebih cepat melalui pola filter yang seragam, serta dapat memahami status survei tanpa membaca tabel secara penuh. Identitas Undika ditampilkan melalui logo resminya, sementara tampilan tetap netral, matang, dan mudah dipelihara.
