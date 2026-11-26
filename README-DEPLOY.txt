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
- PENTING: pemilik (owner) file juga harus user web server.
  File yang dimiliki root akan membuat halaman error 500
  ("Server Error"), karena PHP tidak bisa menulis session, cache,
  maupun log. Ini sering terjadi setelah upload sebagai root atau
  setelah menjalankan git checkout/reset/pull sebagai root.

  Jalankan lewat SSH / Terminal cPanel (ganti USER_WEB dengan user
  hosting Anda, mis. nama akun cPanel atau "www"):

    chown -R USER_WEB:USER_WEB storage bootstrap/cache
    chmod -R ug+rwX storage bootstrap/cache

  Cek apakah masih ada file milik root:
    find storage bootstrap/cache ! -user USER_WEB
  (tidak ada output = sudah benar)

- File public/.user.ini TIDAK disertakan di repo karena isinya
  server-spesifik (open_basedir menunjuk path absolut server).
  Jika hosting membutuhkannya, buat manual di folder public/ dan
  sesuaikan path-nya, contoh:
    open_basedir=/home/USER_WEB/domain-anda.com/:/tmp/
  Path yang salah di file ini bisa membuat SEMUA halaman 500
  ("open_basedir restriction in effect").
- Jika muncul 500, baca penyebab aslinya di:
    storage/logs/laravel.log
  Pastikan file log tersebut juga milik USER_WEB; kalau milik root,
  error tidak akan tercatat sama sekali.
- Buka https://domain-anda.com — seharusnya muncul halaman login.
  Login dengan akun admin yang sudah ada di database.
- Cek dokumentasi API di https://domain-anda.com/api/documentation.
  Jika halamannya kosong, lihat catatan Nginx pada bagian SYARAT HOSTING.
  Dokumentasi dihasilkan dengan: php artisan l5-swagger:generate

--------------------------------------------------------------------
 CATATAN GIT / UPDATE KODE
--------------------------------------------------------------------
Jika meng-update kode dengan git (pull/checkout/reset) di server:
- Jalankan sebagai USER_WEB, bukan root. Jika terpaksa sebagai root,
  file yang ditulis ulang akan menjadi milik root dan aplikasi
  kembali 500. Setelah itu WAJIB jalankan ulang perintah di LANGKAH 5:
    chown -R USER_WEB:USER_WEB storage bootstrap/cache
    chmod -R ug+rwX storage bootstrap/cache
- Setelah update, bersihkan cache lama:
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

--------------------------------------------------------------------
 SYARAT HOSTING
--------------------------------------------------------------------
- PHP 8.3 (minimal 8.2)
- MySQL / MariaDB
- Extension PHP wajib: gd, dom, pdo_mysql, mbstring, openssl,
  fileinfo, zip, exif
- Modul Apache: mod_rewrite (untuk URL bersih / pretty URLs)
- Document root harus menunjuk ke folder public/

Jika memakai Nginx, blok location untuk aset statis WAJIB punya
fallback ke Laravel, contoh:

    location ~ .*\.(js|css|png|jpg|jpeg|gif|svg)$ {
        expires 12h;
        try_files $uri $uri/ /index.php?$query_string;
    }

Tanpa try_files, URL aset yang dilayani lewat route Laravel — seperti
/docs/asset/*.css dan *.js milik Swagger UI — akan dijawab 404 oleh
Nginx sehingga halaman /api/documentation tampil kosong.

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
