# SPL — Sistem Penilaian Lulusan

SPL adalah aplikasi Laravel untuk mengelola penilaian pengguna lulusan Universitas Dinamika. Administrator menyiapkan periode, data perusahaan dan lulusan, instrumen pertanyaan, lalu membuat survei. Penyelia perusahaan mengisi survei melalui kode akses tanpa membuat akun. Hasilnya tersedia pada dashboard, arsip, dan ekspor Excel.

Dokumentasi fungsional dan teknis: [DOKUMENTASI.md](DOKUMENTASI.md). Panduan PostgreSQL Windows: [docs/POSTGRESQL_SETUP.md](docs/POSTGRESQL_SETUP.md). Status keamanan sebelum rilis: [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md).

## Teknologi

- PHP 8.2+, Laravel 12, Composer
- PostgreSQL dengan ekstensi PHP `pdo_pgsql` dan `pgsql`
- Blade, Vite 7, Tailwind CSS, Alpine.js
- PostgreSQL database queue, cache, dan session
- Laravel Breeze untuk autentikasi; Maatwebsite Excel untuk laporan

## Fitur utama

- Role `admin` dan `user`, status akun aktif/nonaktif
- Master perusahaan, lulusan, kategori, pertanyaan, fakultas, dan program studi
- Periode survei, survei tunggal/massal, serta ekspor daftar akses per periode
- Survei publik memakai kode akses; rating, pilihan tunggal/jamak, jawaban lain, dan esai
- Arsip snapshot permanen saat survei selesai, dashboard, dan ekspor laporan Excel

## Setup lokal

### 1. Prasyarat

Pastikan PHP 8.2+, Composer, Node.js/npm, PostgreSQL, dan ekstensi `pdo_pgsql` serta `pgsql` tersedia.

```powershell
php -v
php -m | Select-String -Pattern "pdo_pgsql|pgsql"
composer --version
node --version
psql --version
```

### 2. Buat database dan role PostgreSQL

Buat akun database khusus aplikasi—jangan gunakan superuser PostgreSQL untuk aplikasi. Detail SQL dan troubleshooting tersedia di [docs/POSTGRESQL_SETUP.md](docs/POSTGRESQL_SETUP.md).

```sql
CREATE ROLE spl_app WITH LOGIN PASSWORD 'GANTI_PASSWORD_KUAT';
CREATE DATABASE spl WITH OWNER = spl_app ENCODING = 'UTF8' TEMPLATE = template0;
\c spl
GRANT CONNECT ON DATABASE spl TO spl_app;
GRANT USAGE, CREATE ON SCHEMA public TO spl_app;
ALTER SCHEMA public OWNER TO spl_app;
```

### 3. Instal dependensi dan konfigurasi environment

```powershell
composer install
npm.cmd install
Copy-Item .env.example .env
php artisan key:generate
```

Atur minimal nilai berikut di `.env`:

```dotenv
APP_NAME="SPL"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8001

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=spl
DB_USERNAME=spl_app
DB_PASSWORD="GANTI_PASSWORD_KUAT"
DB_SCHEMA=public
DB_SSLMODE=prefer

SEED_ADMIN_NAME="Admin Utama"
SEED_ADMIN_EMAIL=admin@example.test
SEED_ADMIN_PASSWORD="GANTI_PASSWORD_ADMIN"
SEED_USER_NAME="User Biasa"
SEED_USER_EMAIL=user@example.test
SEED_USER_PASSWORD="GANTI_PASSWORD_USER"
```

Jangan commit `.env` atau membagikan kredensialnya.

### 4. Buat skema dan akun awal

Untuk database pengembangan baru tanpa data demo:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class='Database\Seeders\AccessControlSeeder'
```

`AccessControlSeeder` membuat role `admin` dan `user` serta akun berdasarkan variabel `SEED_*`. Ubah password default sebelum akun digunakan.

Untuk data demo lengkap, gunakan `php artisan db:seed`. Seeder ini membuat master dan data survei contoh; gunakan hanya pada development/test. Jangan gunakan `migrate:fresh` pada database yang berisi data penting.

### 5. Jalankan aplikasi

```powershell
npm.cmd run dev
php artisan serve --port=8001
```

Buka `http://127.0.0.1:8001`. Untuk menjalankan server, worker queue, log, dan Vite sekaligus pada shell yang mendukungnya, gunakan `composer run dev`.

### 6. Uji dan build

```powershell
composer test
npm.cmd run build
php artisan route:list --except-vendor
```

Di PowerShell, `npm.cmd` menghindari masalah execution policy `npm.ps1`.

## Alur penggunaan singkat

1. Admin login dan membuat **periode survei**.
2. Admin mengelola perusahaan, lulusan, kategori, dan pertanyaan aktif.
3. Admin membuat survei tunggal atau massal untuk suatu periode.
4. Admin mengunduh Excel daftar akses bila kode perlu didistribusikan ke penyelia.
5. Penyelia membuka halaman utama, memasukkan kode, melengkapi formulir, lalu mengirim jawaban.
6. Sistem mengunci survei selesai dan membuat arsip snapshot. Admin memantau dashboard atau mengunduh laporan Excel.

## Deploy ke produksi

Selesaikan seluruh checklist pada [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md) sebelum rilis. Ringkasan prosedur berikut berlaku untuk server Linux dengan Nginx/Apache dan PHP-FPM.

### 1. Siapkan server

- Gunakan PHP 8.2+ beserta ekstensi PostgreSQL, Composer, Node.js, dan PostgreSQL yang didukung.
- Arahkan document root web server ke direktori `public/`, bukan root repository.
- Batasi izin tulis web user hanya ke `storage/` dan `bootstrap/cache/`.
- Konfigurasikan domain HTTPS, redirect HTTP ke HTTPS, firewall, dan database yang hanya dapat diakses aplikasi.
- Buat database dan user produksi terpisah dengan password kuat. Gunakan TLS database bila disediakan.

### 2. Rilis aplikasi

Di direktori rilis baru (bukan pada rilis aktif), jalankan:

```bash
git clone <URL_REPOSITORY> spl-release
cd spl-release
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
cp .env.example .env
php artisan key:generate --force
```

Isi `.env` produksi dari secret manager atau mekanisme server. Nilai minimum yang penting:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://spl.contoh.ac.id
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=<HOST_DATABASE>
DB_PORT=5432
DB_DATABASE=<DATABASE_PRODUKSI>
DB_USERNAME=<USER_PRODUKSI>
DB_PASSWORD=<SECRET>
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Jalankan migrasi hanya setelah backup tervalidasi:

```bash
php artisan migrate --force
php artisan optimize
php artisan storage:link
```

`storage:link` aman dijalankan bila aplikasi kelak memakai aset storage publik; aplikasi saat ini tidak memiliki alur upload file.

Jangan menjalankan `db:seed`, `migrate:fresh`, atau seeder data demo di produksi. Jalankan `AccessControlSeeder` hanya jika akun admin awal memang belum ada dan nilai `SEED_*` sudah aman.

### 3. Aktifkan worker dan scheduler bila diperlukan

Konfigurasi default memakai queue database. Jalankan worker melalui systemd atau supervisor:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Jika ada task terjadwal pada masa depan, daftarkan cron berikut:

```cron
* * * * * cd /var/www/spl/current && php artisan schedule:run >> /dev/null 2>&1
```

Saat deploy baru, restart worker setelah kode berpindah:

```bash
php artisan queue:restart
```

### 4. Verifikasi pasca-deploy

```bash
php artisan about --only=environment,drivers
php artisan migrate:status
php artisan route:list --except-vendor
curl -fsS https://spl.contoh.ac.id/up
```

Uji login admin, akun nonaktif, survei publik, submit jawaban, arsip, ekspor Excel, dan reset password dari domain HTTPS. Pastikan `APP_DEBUG=false` pada respons error.

### 5. Backup, restore, dan rollback

Backup PostgreSQL sebelum dan selama produksi, simpan terenkripsi di lokasi terpisah, lalu uji restore secara berkala:

```bash
pg_dump -h <HOST_DATABASE> -U <USER_PRODUKSI> -Fc <DATABASE_PRODUKSI> > spl-$(date +%F).dump
pg_restore --clean --if-exists -h <HOST_DATABASE_STAGING> -U <USER_STAGING> -d <DATABASE_STAGING> spl-YYYY-MM-DD.dump
```

Untuk rollback aplikasi, arahkan symlink/release web server ke artefak rilis sebelumnya dan jalankan `php artisan optimize:clear` lalu `php artisan optimize`. Jangan melakukan rollback migration secara otomatis terhadap data produksi; rencanakan migration balik per rilis dan restore backup bila diperlukan.

## Catatan keamanan penting

- Registrasi publik dan dashboard untuk role `user` masih tersedia; tetapkan kebijakan akses sebelum go-live.
- Endpoint survei publik belum memiliki rate limit khusus.
- Konfigurasi `trustProxies(at: '*')` harus dibatasi sesuai topologi reverse proxy/hosting sebelum produksi.
- Terapkan security header dan monitor `/up`, log aplikasi, backup, serta advisory dependency melalui CI/host yang memiliki akses internet.

Rincian risiko, status, dan checklist ada di [SECURITY_GO_LIVE_AUDIT.md](SECURITY_GO_LIVE_AUDIT.md).

## Dokumen terkait

- [Dokumentasi proyek](DOKUMENTASI.md)
- [Setup PostgreSQL](docs/POSTGRESQL_SETUP.md)
- [Manual admin](docs/user-manual-role-admin.md)
- [Manual user reguler](docs/user-manual-role-user.html)
- [Audit go-live](SECURITY_GO_LIVE_AUDIT.md)
