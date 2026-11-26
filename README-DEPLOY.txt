====================================================================
  SIPANDU PANGAN — PAKET DEPLOY KE HOSTING
====================================================================

Folder ini berisi SEMUA file aplikasi (termasuk vendor/) + file
database (sipandu_pangan.sql) yang siap di-upload ke hosting.

--------------------------------------------------------------------
 LANGKAH 1 — UPLOAD FILE
--------------------------------------------------------------------
1. Upload SELURUH isi folder ini ke hosting Anda (mis. folder
   public_html di cPanel / File Manager / FTP).
2. Biarkan struktur folder seperti apa adanya (jangan menaruh
   public_html di dalam public_html).

Catatan: Hanya dokumen root yang boleh menunjuk ke folder "public".
   Di cPanel: Domains -> Manage -> Document Root -> pilih
   ".../public_html/public" (folder public di dalamnya).

--------------------------------------------------------------------
 LANGKAH 2 — BUAT DATABASE & IMPORT
--------------------------------------------------------------------
1. Di hosting, buat database baru + user (catat nama, user, password).
2. Buka phpMyAdmin -> pilih database baru -> tab Import ->
   pilih file: sipandu_pangan.sql  -> Go.
   (Database + 66 tabel + seluruh data akan dibuat otomatis.)

--------------------------------------------------------------------
 LANGKAH 3 — ISI FILE .env
--------------------------------------------------------------------
Buka file ".env" (sudah disiapkan). Isi bagian database:

    DB_DATABASE=nama_database_hosting
    DB_USERNAME=username_database_hosting
    DB_PASSWORD=password_database_hosting

Dan ganti:
    APP_URL=https://domain-anda.com      -> domain asli Anda
    APP_DEBUG=false                      -> biarkan false
    SANCTUM_STATEFUL_DOMAINS=...         -> domain Anda

--------------------------------------------------------------------
 LANGKAH 4 — KUNCI APLIKASI
--------------------------------------------------------------------
Jalankan di terminal/SSH hosting (atau minta support hosting):

    php artisan key:generate
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan storage:link

Jika hosting TIDAK punya SSH: gunakan fitur "Terminal" di cPanel,
atau cron job satu kali, atau hubungi support hosting untuk menjalankan
perintah di atas.

--------------------------------------------------------------------
 LANGKAH 5 — PERMISSION & PENGECEKAN
--------------------------------------------------------------------
- Pastikan folder storage/ dan bootstrap/cache/ bisa ditulis
  (chmod 775 / 755 di cPanel: File Manager -> pilih folder ->
  Permissions).
- Buka https://domain-anda.com — seharusnya muncul halaman login.
  Login dengan akun admin yang sudah ada di database.

--------------------------------------------------------------------
 SYARAT HOSTING
--------------------------------------------------------------------
- PHP 8.3 (minimal 8.2)
- MySQL / MariaDB
- Extension PHP wajib: gd, dom, pdo_mysql, mbstring, openssl,
  fileinfo, zip, exif
- Modul Apache: mod_rewrite (untuk URL bersih / pretty URLs)
- Document root harus menunjuk ke folder public/

--------------------------------------------------------------------
 FILE PENTING DALAM PAKET INI
--------------------------------------------------------------------
- sipandu_pangan.sql   -> export database lengkap (66 tabel + data)
- .env                -> konfigurasi produksi (ISI bagian database!)
- app/ config/ ...    -> kode aplikasi Laravel
- vendor/             -> library PHP (jika hosting punya SSH bisa
                         dihapus lalu diinstall ulang dengan:
                         composer install --no-dev --optimize-autoloader)
- public/             -> folder yang menjadi document root
====================================================================
