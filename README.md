# SIDUMA PANGAN

Sistem informasi terpadu untuk pengelolaan program ketahanan pangan di lingkungan pemasyarakatan. Mencakup pendataan WBP, blok & kamar hunian, program pangan, pertanian, peternakan, perikanan, inventaris, distribusi, keuangan, pelatihan, hingga pelaporan dan analitik — tersedia sebagai **panel web (AdminLTE 3)** dan **REST API v1 (Sanctum)**.

Dibangun dengan **Laravel 13** + **AdminLTE 3**.

> 👨‍💻 **Pengembang:** KerangRendang — <overload.xp@gmail.com>
> 📘 **Manual Book pengguna:** tersedia di `/manual` (juga tertaut dari halaman login).

## ✨ Fitur Utama

| Modul | Cakupan |
|---|---|
| Autentikasi & RBAC | Login panel web, token Bearer Sanctum untuk API, middleware role, manajemen user |
| Pendataan WBP | Identitas WBP, dokumen, anggota keluarga, riwayat status |
| Blok & Kamar | Data hunian, penempatan kamar, riwayat status blok/kamar |
| Program Pangan | Program ketahanan pangan beserta peserta program |
| Pertanian | Lahan, komoditas, rencana kerja, aktivitas tanam, panen & detail panen |
| Peternakan | Kandang, jenis ternak, populasi, pasokan & log pakan, rekam kesehatan hewan |
| Perikanan | Kolam, stok ikan, kualitas air, panen ikan |
| Integrasi Usahatani | Keterkaitan lahan–kandang–kolam dalam satu program |
| Inventaris & Stok | Kategori, barang, transaksi stok, stock opname |
| Distribusi | Penyaluran hasil produksi beserta item |
| Nutrisi & Pengolahan | Rencana menu, komponen makan, batch pengolahan |
| Keuangan | Kategori, anggaran, transaksi keuangan, riwayat status |
| Pelatihan | Program pelatihan, peserta, sertifikasi |
| Kepatuhan | Catatan kepatuhan (compliance) beserta detailnya |
| Laporan Harian | Laporan harian + detail dengan alur status (draft → dikirim → direview) |
| Dashboard & Analitik | KPI dashboard, ringkasan per sektor, indikator & target analitik |
| Laporan PDF | Cetak laporan via DomPDF |
| Administrasi & Audit | Audit trail (activity log), security event, monitoring slow query, pengelolaan API client |
| Dokumentasi API | OpenAPI/Swagger via L5-Swagger (`/api/documentation`) |

**Lintas modul:** scoping data per unit kerja (`UnitScope`), riwayat status pada hampir semua entitas, soft delete, dan query cache (`CacheableQuery`).

### Skala Project

| Item | Jumlah |
|---|---|
| Eloquent model | 76 |
| Migrasi database | 88 |
| Controller web | 30 |
| Controller API v1 | 42 |
| Blade view | 177 |
| Route web / API | 169 / 58 |

---

## 🎨 Konvensi SweetAlert2 (WAJIB DIPATUHI)

> **Semua notifikasi, konfirmasi, dan alert di project ini HARUS menggunakan SweetAlert2.**
> **DILARANG KERAS menggunakan `alert()`, `confirm()`, atau `window.alert()` bawaan browser.**

SweetAlert2 sudah dimuat via CDN di `master.blade.php` — tidak perlu install ulang.

### Helper yang Tersedia

Semua helper sudah didefinisikan di `resources/views/vendor/adminlte/master.blade.php`:

#### 1. Hapus Data (AJAX DELETE)

```js
// Gunakan untuk tombol hapus di tabel atau halaman detail
confirmDelete('/path/to/delete', 'Nama Data');
```

**Contoh di Blade:**
```html
<button type="button"
    onclick="confirmDelete('{{ route('items.destroy', $item) }}', '{{ $item->nama }}')">
    <i class="fas fa-trash"></i> Hapus
</button>
```

#### 2. Hapus Data (Form Submit)

```js
// Gunakan jika harus submit form (misal ada @method('DELETE'))
confirmDeleteForm('delete-form');
```

**Contoh di Blade:**
```html
<form method="POST" action="{{ route('items.destroy', $item) }}" id="delete-form">
    @csrf @method('DELETE')
</form>
<button type="button" onclick="confirmDeleteForm('delete-form')">
    <i class="fas fa-trash"></i> Hapus
</button>
```

#### 3. Konfirmasi Aksi POST (Workflow)

```js
// Untuk aksi kirim, konfirmasi, approve, reject, dll
swalConfirmPost(url, title, text, buttonColor, confirmText);
```

| Parameter | Tipe | Keterangan |
|---|---|---|
| `url` | string | URL endpoint (POST) |
| `title` | string | Judul dialog |
| `text` | string | Deskripsi/Pesan |
| `buttonColor` | string | Warna tombol konfirmasi (hex) |
| `confirmText` | string | Teks tombol konfirmasi |

**Contoh di Blade:**
```html
<button type="button"
    onclick="swalConfirmPost(
        '{{ route('daily-reports.kirim', $report) }}',
        'Kirim Laporan?',
        'Laporan ini akan dikirim untuk review supervisor.',
        '#f59e0b',
        'Ya, Kirim'
    )">
    <i class="fas fa-paper-plane"></i> Kirim
</button>
```

#### 4. Toast Notifikasi (JavaScript)

```js
showSuccess('Data berhasil disimpan!');
showError('Terjadi kesalahan saat memproses data.');
```

#### 5. Flash Message dari Controller (Otomatis)

Master layout sudah otomatis menampilkan toast untuk semua session flash:

```php
// Di Controller:
return back()->with('success', 'Berhasil!');     // ✅ toast hijau
return back()->with('error', 'Gagal!');           // ❌ toast merah
return back()->with('warning', 'Perhatian!');     // ⚠️ toast kuning
return back()->with('info', 'Informasi...');      // ℹ️ toast biru

// Untuk validasi error (otomatis menampilkan semua error):
return back()->withErrors($validator);
```

### Aturan Penulisan Kode

| ❌ JANGAN | ✅ GUNAKAN |
|---|---|
| `alert('Berhasil!')` | `showSuccess('Berhasil!')` |
| `confirm('Yakin hapus?')` | `confirmDelete(url, name)` |
| `if(confirm('...')) form.submit()` | `confirmDeleteForm('form-id')` |
| `window.alert('Error')` | `showError('Error')` |
| `<form onsubmit="return confirm(...)">` | `onclick="swalConfirmPost(...)"` |

### Controller: Response untuk AJAX vs Non-AJAX

Jika action bisa diakses dari SweetAlert AJAX **dan** form biasa:

```php
public function kirim(DailyReport $dailyReport)
{
    if ($dailyReport->status !== 'draft') {
        $msg = 'Hanya laporan berstatus draft yang bisa dikirim';
        return request()->expectsJson()
            ? response()->json(['message' => $msg], 422)
            : back()->with('warning', $msg);
    }

    $dailyReport->update(['status' => 'dikirim']);
    $msg = 'Laporan berhasil dikirim untuk review';
    return request()->expectsJson()
        ? response()->json(['message' => $msg])
        : back()->with('success', $msg);
}
```

---

## 🚀 Quick Start (Development)

```bash
git clone <repo-url>
cd sipandu-pangan
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install && npm run dev
php artisan serve
```

Buka http://localhost:8000

### Login Default

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@sipandu.id | password |
| Supervisor | supervisor@sipandu.id | password |

---

## 📁 Struktur Project

```
app/
├── Http/
│   ├── Controllers/        # Controller panel web
│   │   ├── Admin/          # User management, audit trail, slow query
│   │   ├── Api/V1/         # 42 controller REST API v1
│   │   ├── Auth/           # Login/logout sesi
│   │   └── Master/         # CRUD master data
│   ├── Middleware/         # RoleMiddleware, CheckTokenAbility, LogApiAccess,
│   │                       # CheckApiClientIp
│   └── Requests/Web/       # Validasi form
├── Models/                 # 76 Eloquent model (+ riwayat status per entitas)
├── Providers/              # AppServiceProvider (slow query listener, dsb.)
├── Swagger/                # Generator OpenAPI yang aman untuk config:cache
└── Traits/                 # ApiResponse, CacheableQuery, HasActivityLog, UnitScope

database/
├── migrations/             # 88 migrasi
└── seeders/                # DatabaseSeeder, RealisticDataSeeder

resources/views/
├── vendor/adminlte/        # Master layout & komponen (SweetAlert2, dsb.)
├── dashboard.blade.php     # Dashboard
├── wbp/  blocks/  food-programs/  farming/  farming-plans/
├── livestock/  livestock-barns/  fishing/  fish-ponds/
├── inventory/  stock-opname/  distributions/  nutrition/  processing/
├── financial/  training/  compliance/  daily-reports/  analytics/
├── master/                 # Master data
├── reports/                # Laporan & cetak PDF
└── admin/                  # Panel administrasi

routes/
├── web.php                 # 169 route panel web (AdminLTE)
├── api.php                 # 58 route REST API (/api/v1/*, Sanctum)
└── console.php             # Perintah artisan terjadwal
```

---

## 🔧 Perintah Penting

```bash
# Clear semua cache (jika ada error tak terduga)
php artisan optimize:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate Swagger docs
php artisan l5-swagger:generate

# Link storage
php artisan storage:link
```

---

## ⚠️ Catatan Penting untuk Hosting (aaPanel / cPanel)

1. **Jalankan artisan sebagai user `www`**, bukan `root`:
   ```bash
   su -s /bin/bash www -c 'php artisan config:cache'
   ```

2. **Jika sudah terlanjur jalankan sebagai root**, perbaiki permission:
   ```bash
   chown -R www:www storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **Restart PHP-FPM** setelah deploy:
   ```bash
   /etc/init.d/php-fpm-74 restart
   ```

---

## 👨‍💻 Pengembang

| | |
|---|---|
| **Nama** | KerangRendang |
| **Email** | [overload.xp@gmail.com](mailto:overload.xp@gmail.com) |
| **GitHub** | [@overloaders](https://github.com/overloaders) |
| **Repositori** | [overloaders/sipandu-pangan](https://github.com/overloaders/sipandu-pangan) |

Aplikasi ini dikembangkan dan dipelihara oleh **KerangRendang**. Untuk pertanyaan teknis,
permintaan fitur, atau laporan bug, silakan hubungi email di atas atau buka
[issue di repositori](https://github.com/overloaders/sipandu-pangan/issues).

### 📘 Manual Book Pengguna

Panduan penggunaan lengkap (login, dashboard, seluruh modul, alur status, laporan, API, dan
troubleshooting) tersedia sebagai halaman publik di **`/manual`** dan dapat dibuka langsung dari
halaman login tanpa perlu masuk. Isinya dilengkapi 30 screenshot antarmuka aplikasi.

---

## 📚 Teknologi

- **Backend:** Laravel 13 (`laravel/framework` v13.24), PHP 8.3
- **Frontend:** AdminLTE 3, Bootstrap 4, jQuery, Vite
- **Charts:** Chart.js
- **Alerts:** SweetAlert2 (CDN)
- **Auth:** Session + Laravel Sanctum (Bearer token, ability `admin`)
- **API Docs:** L5-Swagger (swagger-php)
- **Database:** MySQL / MariaDB
- **PDF:** DomPDF / barryvdh/laravel-dompdf
- **Audit:** spatie/laravel-activitylog
