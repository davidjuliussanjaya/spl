# Tutorial Setup PostgreSQL untuk Project SPL

Panduan ini ditujukan untuk Windows dan project Laravel SPL di:

```text
C:\laragon\www\spl
```

Konfigurasi aplikasi saat ini menggunakan PostgreSQL melalui driver `pgsql`.

## 1. Prasyarat

Pastikan komponen berikut sudah tersedia:

- PostgreSQL 18 atau versi PostgreSQL lain yang masih didukung.
- PHP 8.2 atau lebih baru.
- Ekstensi PHP `pdo_pgsql` dan `pgsql`.
- Composer dan Node.js.

Periksa instalasi dari PowerShell:

```powershell
php -v
php -m | Select-String -Pattern "pdo_pgsql|pgsql"
psql --version
composer --version
node --version
npm.cmd --version
```

Pada komputer pengembangan saat panduan ini dibuat, `psql.exe` berada di:

```text
C:\Program Files\PostgreSQL\18\bin\psql.exe
```

Jika perintah `psql` belum dikenali, gunakan path lengkap tersebut atau tambahkan folder `bin` PostgreSQL ke `PATH` Windows.

## 2. Pastikan service PostgreSQL berjalan

Buka PowerShell sebagai Administrator, lalu cari nama service PostgreSQL:

```powershell
Get-Service | Where-Object { $_.Name -like "*postgres*" }
```

Jika statusnya `Stopped`, jalankan service sesuai nama yang ditampilkan. Contoh:

```powershell
Start-Service -Name "postgresql-x64-18"
```

Alternatifnya, buka `services.msc`, cari service PostgreSQL, lalu pilih **Start**.

Periksa apakah server menerima koneksi pada port 5432:

```powershell
Test-NetConnection 127.0.0.1 -Port 5432
```

Nilai `TcpTestSucceeded` harus `True`.

## 3. Masuk sebagai administrator PostgreSQL

Jalankan perintah berikut. Opsi `-W` akan meminta password secara interaktif sehingga password tidak tersimpan di riwayat perintah.

```powershell
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -h 127.0.0.1 -p 5432 -U postgres -W
```

Masukkan password user `postgres` yang dibuat ketika PostgreSQL diinstal.

Jika menggunakan pgAdmin, buka **Servers > PostgreSQL > Databases**, lalu gunakan **Query Tool** untuk menjalankan SQL pada langkah berikutnya.

## 4. Buat role dan database khusus aplikasi

Jangan gunakan akun superuser `postgres` sebagai akun harian aplikasi. Dari prompt `psql`, jalankan:

```sql
CREATE ROLE spl_app
    WITH LOGIN
    PASSWORD 'GANTI_DENGAN_PASSWORD_KUAT';

CREATE DATABASE spl
    WITH OWNER = spl_app
    ENCODING = 'UTF8'
    TEMPLATE = template0;
```

Hubungkan ke database yang baru dibuat:

```sql
\c spl
```

Pastikan role aplikasi dapat menggunakan schema `public`:

```sql
GRANT CONNECT ON DATABASE spl TO spl_app;
GRANT USAGE, CREATE ON SCHEMA public TO spl_app;
ALTER SCHEMA public OWNER TO spl_app;
```

Keluar dari `psql`:

```sql
\q
```

Gunakan password yang panjang dan unik. Jangan menyalin password contoh ke server produksi.

## 5. Uji koneksi role aplikasi

```powershell
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -h 127.0.0.1 -p 5432 -U spl_app -d spl -W
```

Setelah berhasil masuk, periksa koneksi:

```sql
SELECT current_database(), current_user, current_schema();
\q
```

Hasil yang diharapkan:

```text
current_database = spl
current_user     = spl_app
current_schema   = public
```

## 6. Konfigurasi file `.env`

Masuk ke folder project:

```powershell
Set-Location C:\laragon\www\spl
```

Jika `.env` belum tersedia:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Ubah bagian database pada `.env` menjadi:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=spl
DB_USERNAME=spl_app
DB_PASSWORD="GANTI_DENGAN_PASSWORD_KUAT"
DB_SCHEMA=public
DB_SSLMODE=prefer
```

Catatan:

- Gunakan tanda kutip jika password memiliki `#`, spasi, atau karakter khusus.
- Jangan commit file `.env` ke Git.
- Untuk server produksi yang menyediakan SSL, gunakan `DB_SSLMODE=require` sesuai konfigurasi penyedia database.

Bersihkan cache konfigurasi setelah mengubah `.env`:

```powershell
php artisan optimize:clear
```

Uji koneksi Laravel:

```powershell
php artisan migrate:status
```

Jika koneksi benar dan database masih kosong, perintah tersebut akan menampilkan status migration atau informasi bahwa tabel migration belum tersedia.

## 7. Jalankan migration

### Database baru dan kosong

```powershell
php artisan migrate
```

Periksa tabel yang terbentuk:

```powershell
php artisan db:show --counts
php artisan migrate:status
```

### Database yang sudah berisi hasil impor Oracle

Jangan menjalankan `migrate:fresh`, karena perintah tersebut menghapus seluruh tabel dan data.

Lakukan backup terlebih dahulu:

```powershell
New-Item -ItemType Directory -Force C:\laragon\www\spl\storage\app\backups
& "C:\Program Files\PostgreSQL\18\bin\pg_dump.exe" -h 127.0.0.1 -p 5432 -U spl_app -W -Fc -d spl -f "C:\laragon\www\spl\storage\app\backups\spl_before_migration.dump"
```

Kemudian periksa:

```powershell
php artisan migrate:status
```

Jika tabel aplikasi sudah ada tetapi tabel `migrations` tidak ada atau kosong, jangan menjalankan `php artisan migrate` secara langsung. Laravel akan menganggap semua migration belum pernah dijalankan dan mencoba membuat tabel yang sudah ada. Buat baseline migration terlebih dahulu atau konsultasikan status tabel sebelum melanjutkan.

Jika riwayat migration sudah benar, jalankan:

```powershell
php artisan migrate
```

Setelah impor data Oracle yang membawa ID eksplisit, sinkronkan sequence PostgreSQL:

```powershell
php artisan db:seed --class='Database\Seeders\PostgreSqlSequenceSeeder'
```

Langkah ini mencegah error `duplicate key value violates unique constraint` ketika PostgreSQL membuat ID baru.

## 8. Jalankan seeder

### Hanya buat role dan akun login

Konfigurasikan akun awal di `.env`:

```dotenv
SEED_ADMIN_NAME="Admin Utama"
SEED_ADMIN_EMAIL=admin@gmail.com
SEED_ADMIN_PASSWORD="GANTI_PASSWORD_ADMIN"

SEED_USER_NAME="User Biasa"
SEED_USER_EMAIL=user@gmail.com
SEED_USER_PASSWORD="GANTI_PASSWORD_USER"
```

Kemudian jalankan:

```powershell
php artisan config:clear
php artisan db:seed --class='Database\Seeders\AccessControlSeeder'
```

Gunakan alamat email, bukan username biasa, pada halaman login.

### Isi seluruh data demo

Perintah berikut membuat role, akun, perusahaan, lulusan, instrumen, survey, respons, dan arsip demo:

```powershell
php artisan db:seed
```

Jalankan data demo hanya pada lingkungan pengembangan atau pengujian.

### Reset total database pengembangan

Perintah berikut menghapus seluruh tabel dan data sebelum membuat ulang schema:

```powershell
php artisan migrate:fresh --seed
```

Jangan gunakan `migrate:fresh` pada database produksi atau database hasil impor yang ingin dipertahankan.

## 9. Build frontend Vite

Error berikut berarti aset frontend belum dibangun:

```text
Vite manifest not found at public/build/manifest.json
```

Perbaikannya:

```powershell
npm.cmd install
npm.cmd run build
```

Untuk mode pengembangan dengan hot reload:

```powershell
npm.cmd run dev
```

Di Windows, gunakan `npm.cmd` jika PowerShell menolak menjalankan `npm.ps1` karena execution policy.

## 10. Jalankan aplikasi

```powershell
php artisan serve --port=8001
```

Buka:

```text
http://127.0.0.1:8001/login
```

Setelah menjalankan `AccessControlSeeder`, login menggunakan email dan password yang ditentukan melalui `SEED_ADMIN_EMAIL` dan `SEED_ADMIN_PASSWORD`.

## 11. Verifikasi akhir

Jalankan pemeriksaan berikut:

```powershell
php artisan about --only=environment,drivers
php artisan migrate:status
php artisan db:show --counts
php artisan test
```

Pada bagian `Drivers`, nilai database harus menunjukkan:

```text
Database ... pgsql
```

## 12. Troubleshooting

### `fe_sendauth: no password supplied`

Penyebab: `DB_PASSWORD` kosong atau konfigurasi lama masih tersimpan pada cache Laravel.

Perbaikan:

```powershell
php artisan config:clear
```

Pastikan `DB_PASSWORD` pada `.env` terisi dan coba kembali:

```powershell
php artisan migrate:status
```

### `password authentication failed for user`

Penyebab: username/password PostgreSQL salah.

Uji langsung dengan `psql`:

```powershell
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -h 127.0.0.1 -p 5432 -U spl_app -d spl -W
```

Jika lupa password dan masih dapat masuk sebagai `postgres`, ubah password:

```sql
ALTER ROLE spl_app WITH PASSWORD 'PASSWORD_BARU_YANG_KUAT';
```

Perbarui `DB_PASSWORD` di `.env`, lalu jalankan `php artisan config:clear`.

### `connection refused` pada port 5432

Penyebab umum:

- Service PostgreSQL belum berjalan.
- PostgreSQL menggunakan port lain.
- `DB_HOST` atau `DB_PORT` salah.
- Firewall memblokir koneksi.

Periksa:

```powershell
Get-Service | Where-Object { $_.Name -like "*postgres*" }
Test-NetConnection 127.0.0.1 -Port 5432
```

### `database "spl" does not exist`

Buat database menggunakan langkah 4 atau periksa nilai `DB_DATABASE` pada `.env`.

### `permission denied for schema public`

Masuk sebagai `postgres`, lalu jalankan:

```sql
\c spl
GRANT USAGE, CREATE ON SCHEMA public TO spl_app;
ALTER SCHEMA public OWNER TO spl_app;
```

### `duplicate key value violates unique constraint`

Jika data berasal dari Oracle, sequence PostgreSQL mungkin tertinggal dari ID terbesar pada tabel. Jalankan:

```powershell
php artisan db:seed --class='Database\Seeders\PostgreSqlSequenceSeeder'
```

### `Class "Yajra\Oci8\Oci8ServiceProvider" not found`

Project tidak lagi menggunakan driver Oracle. Pastikan dependency sudah sinkron:

```powershell
composer install
php artisan optimize:clear
```

Pastikan tidak ada konfigurasi Oracle lama pada `.env` dan nilai `DB_CONNECTION` adalah `pgsql`.

## 13. Urutan setup yang direkomendasikan

Untuk database pengembangan baru:

```powershell
Set-Location C:\laragon\www\spl
composer install
npm.cmd install
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class='Database\Seeders\AccessControlSeeder'
npm.cmd run build
php artisan test
php artisan serve --port=8001
```

Untuk database hasil impor Oracle yang datanya harus dipertahankan:

```powershell
Set-Location C:\laragon\www\spl
php artisan optimize:clear
php artisan migrate:status
php artisan migrate
php artisan db:seed --class='Database\Seeders\PostgreSqlSequenceSeeder'
php artisan db:seed --class='Database\Seeders\AccessControlSeeder'
npm.cmd run build
```

Pastikan backup tersedia sebelum menjalankan migration pada database yang berisi data penting.
