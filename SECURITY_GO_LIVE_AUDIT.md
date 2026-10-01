# Audit Keamanan dan Kesiapan Go-Live

Tanggal audit: 1 Oktober 2026  
Cakupan: source code dan konfigurasi lokal proyek SPL. Audit ini memakai OWASP ASVS sebagai acuan yang disesuaikan dengan aplikasi Laravel 12. Audit tidak mencakup server hosting, DNS, TLS, firewall, database produksi, atau layanan pihak ketiga karena aksesnya tidak tersedia.

## Status ringkas

**Rekomendasi: belum siap go-live tanpa syarat.** Aplikasi memiliki dasar proteksi Laravel yang baik, tetapi ada keputusan akses dan konfigurasi produksi yang belum dipastikan. Setelah keputusan pada bagian “Menunggu persetujuan” diterapkan dan checklist hosting dipenuhi, status yang realistis adalah **siap bersyarat**.

| Status | Ringkasan |
|---|---|
| Sudah diperbaiki | Payload pertanyaan dibatasi ke data tervalidasi; error teknis tidak lagi diteruskan ke halaman survei dan data master. |
| Sudah diverifikasi | CSRF web, hash password, regenerasi session login, logout/invalidate session, login throttle, admin route guard, validasi server-side, dan transaksi respons survei. |
| Menunggu persetujuan | Registrasi publik, akses dashboard pengguna reguler, rate limit endpoint publik, trusted proxy/host, serta security headers. |
| Belum diverifikasi | Infrastruktur produksi, HTTPS, secret, backup/restore, permission server, monitoring, dan advisory dependency online. |

## Arsitektur dan cakupan

- Framework: Laravel `12.50.0`, PHP `^8.2`; database pada dokumentasi proyek adalah PostgreSQL.
- Autentikasi: session Laravel (`web` guard), password di-hash, opsi remember-me, reset password bawaan Laravel, dan rate limit login lima percobaan per kombinasi email/IP.
- Otorisasi: area administrasi berada di `auth`, `active`, dan `role:admin`; dashboard berada di `auth` dan `active` saja. `CheckRole` memeriksa role aktif pada pivot user-role.
- Data/fungsi admin: pengguna, perusahaan, lulusan, kategori, pertanyaan, periode, survei, arsip, Excel, dan PDF. Seluruh route tersebut berada dalam group admin.
- Responden: mengisi survei tanpa login melalui kode akses. Akses formulir saat ini harus dimulai dari landing page dan kode diverifikasi ke session.
- Penyimpanan/upload: tidak ditemukan upload file pada controller, route, atau view. Penyimpanan file default Laravel ada di `storage/app/private`; tidak ada alur upload untuk diaudit.
- Integrasi: tidak ditemukan route API/CORS aktif atau pemanggilan layanan pihak ketiga. Endpoint sinkronisasi lulusan hanya mengembalikan pesan bahwa integrasi belum dikonfigurasi.

## Temuan

### Tinggi - registrasi publik membuka akun internal baru

**Status: menunggu persetujuan.** `routes/auth.php` mengekspos `GET/POST /register` di middleware `guest`; `RegisteredUserController` membuat akun aktif, memasang role `user`, lalu langsung login. Dashboard bisa diakses setiap akun terautentikasi (`routes/web.php`, `DashboardController`). Dokumentasi juga menyatakan dashboard dapat diakses seluruh pengguna yang login.

Dampak: siapa pun yang menemukan URL dapat membuat akun dan melihat metrik dashboard internal. Apakah data dashboard memang boleh dilihat publik yang mendaftar sendiri adalah aturan bisnis yang belum dapat dipastikan dari kode.

Rekomendasi: nonaktifkan registrasi publik dan buat akun hanya melalui menu admin, **atau** pertahankan registrasi tetapi jadikan akun baru nonaktif sampai disetujui admin. Keduanya mengubah kebijakan akses sehingga belum diterapkan.

### Tinggi - semua header proxy dipercaya

**Status: menunggu persetujuan.** `bootstrap/app.php` memakai `trustProxies(at: '*')` tanpa daftar proxy tepercaya atau konfigurasi host tepercaya.

Dampak: bila aplikasi dapat diakses langsung selain melalui reverse proxy, header `X-Forwarded-*` yang dipalsukan dapat memengaruhi skema/host yang dianggap aplikasi. Ini berisiko pada URL absolut, cookie HTTPS, dan tautan reset password.

Rekomendasi: batasi ke alamat/IP reverse proxy dan konfigurasikan trusted host sesuai domain produksi. Perubahan ini harus divalidasi di hosting karena salah konfigurasi dapat membuat URL/cookie tidak berfungsi.

### Sedang - endpoint publik belum diberi rate limit khusus

**Status: menunggu persetujuan.** `POST /access-survey`, `GET /fill-survey/{code}`, dan `POST /submit-survey/{code}` tidak memiliki middleware `throttle`. Login sudah dibatasi oleh `LoginRequest`; email-verification juga memiliki throttle.

Dampak: penebakan kode akses dan beban berulang pada endpoint pengisian lebih mudah dilakukan. Kode akses delapan karakter acak sudah membantu, tetapi bukan pengganti pembatasan permintaan.

Rekomendasi: terapkan limiter yang cukup longgar pada verifikasi kode dan submit, lalu uji dengan volume penggunaan nyata. Ini dapat menolak request sah saat batas terlampaui, sehingga perlu persetujuan.

### Sedang - security headers dan konfigurasi cookie produksi belum dipastikan

**Status: belum diverifikasi / menunggu persetujuan.** Tidak ditemukan middleware aplikasi untuk header seperti `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, dan `Content-Security-Policy`. `SESSION_SECURE_COOKIE` tidak ditetapkan di `.env` lokal; default konfigurasi Laravel dapat menghasilkan cookie non-secure pada HTTP lokal.

Rekomendasi: pada produksi set `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, dan `SESSION_SAME_SITE=lax` atau kebijakan yang telah disetujui. Header CSP/HSTS dan konfigurasi CORS perlu diuji bersama domain, CDN, iframe, dan asset sebenarnya; jangan diterapkan dari lokal tanpa persetujuan.

### Sedang - belum ada audit trail perubahan administratif

**Status: belum diperiksa secara operasional.** Perubahan master, pembuatan/penghapusan survei, ekspor, dan pengunduhan PDF belum tampak dicatat sebagai audit trail terstruktur.

Dampak: investigasi akses data atau perubahan penting menjadi sulit setelah go-live.

Rekomendasi: tentukan kebutuhan retensi dan data yang boleh dicatat, lalu tambahkan log aktivitas yang tidak memuat jawaban survei atau kredensial. Karena membuat data baru dan memerlukan kebijakan retensi, belum diterapkan.

### Rendah - constraint dependency terlalu longgar

**Status: belum diperbaiki.** `composer.json` menggunakan `maatwebsite/excel: "*"`; lock file saat ini mengunci `3.1.69`. Constraint wildcard membuat pembaruan dependency di masa depan kurang dapat diprediksi.

Rekomendasi: pin ke rentang kompatibel yang disetujui (misalnya `^3.1`) setelah pembaruan dependency diuji. Ini menyentuh dependency sehingga memerlukan persetujuan.

### Rendah - dokumentasi deployment belum memadai

**Status: belum diperbaiki.** `README.md` masih template Laravel dan tidak memuat prosedur deployment, backup/restore, queue, health check, atau rollback khusus SPL. Ada panduan PostgreSQL lokal, tetapi belum merupakan runbook produksi.

## Kontrol yang sudah diverifikasi

- Form web memakai CSRF Laravel (`@csrf`) dan route mutasi berada pada web middleware.
- Password memakai cast `hashed`/`Hash::make`; login meregenerasi session dan logout menginvalidasi session serta meregenerasi token CSRF.
- Login membatasi lima percobaan per email/IP; akun nonaktif tidak dapat login dan akan dilogout jika status berubah.
- Semua route CRUD, laporan, ekspor Excel, dan unduhan PDF berada di group `auth`, `active`, `role:admin`.
- Validasi request tersedia untuk CRUD utama dan submit survei. Validasi jawaban memastikan pilihan benar-benar milik pertanyaan pada survei.
- Submit survei menggunakan transaksi serta `lockForUpdate`, sehingga submit ganda bersamaan dicegah.
- Blade menggunakan escaping default pada data dinamis; tidak ditemukan penggunaan output Blade raw (`{!! !!}`) dalam area aplikasi yang ditinjau.
- `.env`, backup env, log, vendor, dan node_modules diabaikan oleh Git; `.env` tidak terdaftar di Git.
- Tidak ditemukan fitur upload, path file dari input pengguna, shell execution, atau deserialisasi tidak aman pada source aplikasi.

## Perbaikan yang diterapkan

| Perubahan | Alasan | File |
|---|---|---|
| Controller pertanyaan meneruskan `$request->validated()` ke service. | Menolak field HTTP yang tidak didefinisikan sebelum diproses service. | `app/Http/Controllers/PertanyaanController.php` |
| Field `required` ditambahkan sebagai boolean ke request pertanyaan. | Mempertahankan perilaku pertanyaan wajib saat payload dibatasi ke data tervalidasi. | `app/Http/Requests/PertanyaanStoreRequest.php` |
| Error teknis tak terduga pada create/update/delete survei dan simpan lulusan dilogging melalui `report()` dan diganti pesan umum. | Mencegah detail database/stack error tampil ke pengguna. Pesan bisnis `DomainException` tetap dipertahankan. | `app/Http/Controllers/SurveyController.php`, `app/Http/Controllers/LulusanController.php` |
| Error teknis tak terduga pada submit survei tidak lagi dikirim mentah ke responden non-AJAX. | Menutup kebocoran detail internal pada endpoint publik. | `app/Http/Controllers/SurveyController.php` |

## Hasil pengujian

- Baseline `php artisan test`: 48 lulus, 8 gagal, 249 assertion.
- Setelah perbaikan: tiga test alur CRUD survei/lulusan lulus; tiga test pertanyaan lulus (total 6 test terarah, 52 assertion) dan `php -l` lulus pada file yang diubah.
- Kegagalan baseline yang belum diubah:
  - Test seeder arsip mengharapkan jumlah historis lama yang tidak lagi sesuai data sumber.
  - Dua test pengisian survei mengasumsikan URL formulir dapat dibuka langsung dan field responden yang kini wajib tidak diisi. Perilaku aplikasi saat ini memang sengaja meminta kode dari landing page terlebih dahulu.
  - Lima test profil mengharapkan route `/profile`, tetapi route tersebut tidak terdaftar.
- `composer audit --format=json` tidak dapat dijalankan karena lingkungan lokal tidak dapat terhubung ke Packagist. Tidak ada klaim bahwa dependency bebas advisory sampai audit dijalankan dari lingkungan dengan akses internet.

## Perubahan yang memerlukan persetujuan eksplisit

1. **Registrasi publik:** pilih nonaktifkan total atau akun baru menunggu aktivasi admin. Sebelum: siapa pun dapat membuat akun aktif role user dan melihat dashboard. Sesudah: hanya akun yang diizinkan dapat masuk. Dampak: calon pengguna internal dan proses onboarding. Rollback: kembalikan dua route register.
2. **Akses dashboard user reguler:** konfirmasi apakah role `user` boleh melihat seluruh metrik dashboard. Jika tidak, dashboard perlu dibatasi admin atau dipecah menjadi dashboard terbatas. Dampak: cakupan data role user.
3. **Limiter responden:** usulan batas awal yang longgar untuk verifikasi kode dan submit. Dampak: request yang melebihi batas memperoleh 429. Rollback: hapus middleware limiter.
4. **Proxy, host, cookie, dan header produksi:** berikan domain produksi, topologi reverse proxy/CDN, serta apakah aplikasi pernah ditempatkan dalam iframe. Perubahan diuji di staging; rollback dengan mengembalikan konfigurasi sebelumnya.
5. **Audit trail dan dependency constraint:** tentukan retensi log aktivitas dan persetujuan pembaruan/pinning dependency.

## Checklist sebelum go-live

- [ ] Putuskan kebijakan registrasi publik dan akses dashboard reguler.
- [ ] Set environment produksi (`APP_ENV=production`, `APP_DEBUG=false`) tanpa menampilkan atau membagikan secret.
- [ ] Set URL HTTPS final, secure session cookie, domain cookie, dan mailer produksi; uji reset password melalui domain final.
- [ ] Batasi trusted proxy/host sesuai topologi hosting dan uji URL, redirect, serta cookie dari luar proxy.
- [ ] Konfigurasi TLS, redirect HTTP ke HTTPS, HSTS setelah HTTPS stabil, firewall, dan akses database hanya dari aplikasi.
- [ ] Atur permission least-privilege untuk web user: hanya `storage/` dan `bootstrap/cache/` yang writable; document root harus `public/`.
- [ ] Jalankan `composer audit --locked` dan `npm audit` dari CI/host yang dapat mengakses registry; perbarui advisory yang relevan melalui proses uji.
- [ ] Perbaiki atau sesuaikan delapan test baseline, lalu jalankan test suite penuh hingga hijau.
- [ ] Buat backup terenkripsi, uji restore di staging, dan tetapkan RPO/RTO serta pemilik prosedur rollback.
- [ ] Konfigurasikan rotasi/retensi log, monitoring health endpoint `/up`, alert error, dan penanggung jawab insiden.
- [ ] Uji seluruh route dengan role admin, user reguler, akun nonaktif, dan responden; termasuk ekspor Excel/PDF, submit, logout, password reset, serta akses ID yang diubah.

## Keterbatasan

Audit ini tidak membuktikan keamanan mutlak atau kepatuhan penuh ASVS. Tidak ada pengujian aktif ke produksi, tidak ada access ke hosting, dan tidak ada audit dependency online karena konektivitas registry tidak tersedia. Tidak ada data produksi, secret, migration destruktif, deploy, maupun perubahan server yang dilakukan selama audit.
