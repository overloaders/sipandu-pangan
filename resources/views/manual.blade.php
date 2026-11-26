<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manual Book — SIDUMA PANGAN</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; scroll-padding-top: 24px; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 15px;
            line-height: 1.7;
        }
        a { color: #059669; text-decoration: none; }
        a:hover { text-decoration: underline; }
        code, kbd {
            font-family: Menlo, Consolas, monospace;
            font-size: 0.86em;
            background: #e2e8f0;
            color: #0f172a;
            padding: 2px 6px;
            border-radius: 5px;
        }
        pre {
            background: #0f172a;
            color: #e2e8f0;
            padding: 16px 18px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.6;
            margin: 14px 0 20px;
        }
        pre code { background: none; color: inherit; padding: 0; }

        /* Layout */
        .layout { display: flex; align-items: flex-start; width: 100%; }
        .sidebar {
            width: 300px;
            flex: 0 0 300px;
            position: sticky;
            top: 0;
            max-height: 100vh;
            overflow-y: auto;
            background: #0f172a;
            color: #cbd5e1;
            padding: 26px 20px 40px;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: 10px;
            color: #fff; font-weight: 700; font-size: 17px;
            margin-bottom: 4px;
        }
        .sidebar .brand i { color: #10b981; font-size: 18px; }
        .sidebar .brand span { color: #10b981; }
        .sidebar .tagline { font-size: 12px; color: #94a3b8; line-height: 1.5; margin-bottom: 22px; }
        .sidebar .nav-label {
            font-size: 11px; letter-spacing: .08em; text-transform: uppercase;
            color: #64748b; margin: 18px 0 8px; font-weight: 600;
        }
        .sidebar nav ol { list-style: none; counter-reset: toc; }
        .sidebar nav li { counter-increment: toc; }
        .sidebar nav li a {
            display: block;
            padding: 7px 10px 7px 34px;
            border-radius: 8px;
            color: #cbd5e1;
            font-size: 13px;
            position: relative;
            line-height: 1.4;
        }
        .sidebar nav li a::before {
            content: counter(toc) ".";
            position: absolute; left: 10px;
            color: #10b981; font-weight: 600; font-size: 12px;
            top: 7px;
        }
        .sidebar nav li a:hover { background: #1e293b; color: #fff; text-decoration: none; }
        .sidebar .side-actions { margin-top: 26px; display: flex; flex-direction: column; gap: 8px; }
        .sidebar .side-actions a {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; padding: 10px 12px; border-radius: 9px;
            background: #1e293b; color: #e2e8f0;
        }
        .sidebar .side-actions a.primary { background: #10b981; color: #fff; font-weight: 600; }
        .sidebar .side-actions a:hover { text-decoration: none; opacity: .9; }

        .main { flex: 1; min-width: 0; padding: 0 0 80px; }
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #0f2e26 100%);
            color: #fff;
            padding: 56px 56px 48px;
            position: relative;
            overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(16,185,129,.25) 0%, transparent 55%);
            pointer-events: none;
        }
        .hero .badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(16,185,129,.16); border: 1px solid rgba(16,185,129,.4);
            color: #6ee7b7; border-radius: 999px;
            padding: 5px 14px; font-size: 12px; font-weight: 600; letter-spacing: .04em;
            margin-bottom: 18px;
        }
        .hero h1 { font-size: 36px; line-height: 1.15; font-weight: 700; margin-bottom: 12px; position: relative; }
        .hero h1 span { color: #10b981; }
        .hero p { color: #cbd5e1; max-width: 620px; font-size: 15px; position: relative; }
        .hero .meta { margin-top: 22px; display: flex; flex-wrap: wrap; gap: 20px; font-size: 12.5px; color: #94a3b8; position: relative; }
        .hero .meta i { color: #10b981; margin-right: 5px; }

        .content { padding: 0 56px; }
        .content section { width: 100%; max-width: 1120px; margin-left: auto; margin-right: auto; }

        /* Daftar isi versi mobile */
        .toc-mobile { display: none; }
        section { padding-top: 42px; }
        section > h2 {
            font-size: 23px; font-weight: 700; color: #0f172a;
            padding-bottom: 10px; border-bottom: 2px solid #e2e8f0;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 10px;
        }
        section > h2 .num {
            flex: 0 0 auto;
            width: 30px; height: 30px; border-radius: 9px;
            background: #10b981; color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700;
        }
        h3 { font-size: 17px; font-weight: 700; color: #0f172a; margin: 26px 0 10px; }
        h4 { font-size: 14.5px; font-weight: 700; color: #0f172a; margin: 18px 0 6px; }
        p { margin-bottom: 12px; color: #334155; }
        ul, ol { margin: 0 0 14px 22px; color: #334155; }
        li { margin-bottom: 5px; }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px 24px;
            margin-bottom: 18px;
        }
        .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .note {
            border-left: 4px solid #10b981;
            background: #ecfdf5;
            border-radius: 0 10px 10px 0;
            padding: 14px 18px;
            margin: 16px 0 20px;
            font-size: 14px;
            color: #065f46;
        }
        .note.warn { border-color: #f59e0b; background: #fffbeb; color: #92400e; }
        .note.warn strong { color: #78350f; }
        .note i { margin-right: 6px; }
        .table-wrap { overflow-x: auto; margin: 14px 0 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; font-size: 14px; }
        th, td { text-align: left; padding: 11px 14px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        th { background: #f8fafc; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8fafc; }

        .badge-role {
            display: inline-block; font-size: 11.5px; font-weight: 600;
            padding: 2px 9px; border-radius: 999px;
            background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;
        }

        /* Screenshots */
        .shots { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin: 18px 0 8px; }
        figure.shot {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(15,23,42,.05);
            display: flex;
            flex-direction: column;
        }
        figure.shot .frame {
            background: #0f172a;
            padding: 8px 12px;
            display: flex; align-items: center; gap: 6px;
        }
        figure.shot .frame span { width: 9px; height: 9px; border-radius: 50%; background: #475569; }
        figure.shot .frame span:first-child { background: #f87171; }
        figure.shot .frame span:nth-child(2) { background: #fbbf24; }
        figure.shot .frame span:nth-child(3) { background: #34d399; }
        figure.shot .frame em { margin-left: 8px; color: #94a3b8; font-size: 11px; font-style: normal; }
        figure.shot img { display: block; width: 100%; height: auto; }
        figure.shot.tall img { max-height: 520px; object-fit: cover; object-position: top; }
        figure.shot figcaption {
            padding: 11px 15px;
            font-size: 12.5px;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
            line-height: 1.5;
        }
        figure.shot.wide { grid-column: 1 / -1; }
        .shot-hint { font-size: 12px; color: #64748b; margin-top: -6px; }

        .steps { counter-reset: step; list-style: none; margin-left: 0; }
        .steps > li {
            counter-increment: step;
            position: relative;
            padding-left: 42px;
            margin-bottom: 14px;
        }
        .steps > li::before {
            content: counter(step);
            position: absolute; left: 0; top: 1px;
            width: 26px; height: 26px;
            background: #10b981; color: #fff; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
        }

        .dev {
            background: #0f172a; color: #cbd5e1;
            border-radius: 16px; padding: 26px 28px; margin-top: 10px;
        }
        .dev h3 { color: #fff; margin-top: 0; }
        .dev strong { color: #6ee7b7; }
        .dev a { color: #6ee7b7; }
        .dev p { color: #cbd5e1; }

        footer.manual-footer {
            margin-top: 54px;
            padding: 22px 0 0;
            border-top: 1px solid #e2e8f0;
            font-size: 12.5px;
            color: #94a3b8;
            display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }

        .mobile-toc {
            display: none;
            background: #0f172a; color: #e2e8f0;
            padding: 14px 20px; position: sticky; top: 0; z-index: 20;
        }
        .mobile-toc a { color: #6ee7b7; font-weight: 600; font-size: 13px; }

        .toc-mobile details {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 4px;
            margin-bottom: 4px;
        }
        .toc-mobile summary {
            cursor: pointer;
            list-style: none;
            padding: 14px 14px;
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
        }
        .toc-mobile summary::-webkit-details-marker { display: none; }
        .toc-mobile summary::after { content: "▾"; float: right; color: #10b981; }
        .toc-mobile details[open] summary::after { content: "▴"; }
        .toc-mobile ol { margin: 0 14px 14px 30px; }
        .toc-mobile li { margin-bottom: 6px; }
        .toc-mobile a { font-size: 13.5px; }

        @media (max-width: 1080px) {
            html { scroll-padding-top: 68px; }
            .sidebar { display: none; }
            .toc-mobile { display: block; margin-top: 26px; }
            .mobile-toc { display: flex; justify-content: space-between; align-items: center; gap: 12px; font-size: 13px; }
            .hero, .content { padding-left: 26px; padding-right: 26px; }
            .hero { padding-top: 38px; padding-bottom: 34px; }
            .hero h1 { font-size: 27px; }
            .grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            body { font-size: 14.5px; }
            .hero, .content { padding-left: 18px; padding-right: 18px; }
            figure.shot.tall img { max-height: 430px; }
            section > h2 { font-size: 20px; }
        }

        @media print {
            @page { size: A4; margin: 14mm 12mm 16mm; }
            html, body { background: #fff; font-size: 11pt; }
            .sidebar, .mobile-toc, .toc-mobile { display: none !important; }
            .layout { display: block; }
            .main { width: 100%; padding: 0; }
            .hero {
                padding: 26px 26px 22px;
                margin-bottom: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .hero h1 { font-size: 30px; }
            .hero p { font-size: 12px; }
            .content { padding: 0; }
            .content section { max-width: none; padding-top: 22px; }
            section > h2 { page-break-after: avoid; }
            h3, h4 { page-break-after: avoid; }
            .shots { display: block; }
            figure.shot {
                margin-bottom: 12px;
                break-inside: avoid;
                page-break-inside: avoid;
            }
            figure.shot .frame { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .note, .card, table, .dev, .steps > li, .badge-role {
                break-inside: avoid;
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            figure.shot img { width: 100%; height: auto; }
            .shot-hint { font-size: 10px; }
            a { color: #0f172a; text-decoration: none; }
            .manual-pdf-actions, .manual-link { display: none !important; }
            footer.manual-footer { margin-top: 26px; }
        }
    </style>
</head>
<body>
<div class="mobile-toc">
    <span><strong>SIDUMA PANGAN</strong> — Manual Book</span>
    <a href="{{ url('/login') }}">&larr; Login</a>
</div>

<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span style="font-size:19px">🌱</span>SIDUMA <span>PANGAN</span></div>
        <p class="tagline">Manual Book<br>Sistem Informasi Terpadu Pemasyarakatan dan Ketahanan Pangan</p>

        <div class="nav-label">Daftar Isi</div>
        <nav>
            <ol>
                <li><a href="#tentang">Tentang Aplikasi</a></li>
                <li><a href="#akses">Masuk &amp; Peran Pengguna</a></li>
                <li><a href="#navigasi">Navigasi &amp; Tampilan</a></li>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#wbp">Registrasi WBP</a></li>
                <li><a href="#blok-kamar">Blok &amp; Kamar</a></li>
                <li><a href="#laporan-harian">Laporan Harian</a></li>
                <li><a href="#program-pangan">Program Pangan</a></li>
                <li><a href="#lahan">Manajemen Lahan</a></li>
                <li><a href="#pertanian">Pertanian &amp; Panen</a></li>
                <li><a href="#peternakan">Peternakan</a></li>
                <li><a href="#perikanan">Perikanan</a></li>
                <li><a href="#nutrisi">Nutrisi &amp; Menu</a></li>
                <li><a href="#pengolahan">Pengolahan Hasil</a></li>
                <li><a href="#inventaris">Inventaris &amp; Stok</a></li>
                <li><a href="#stock-opname">Stock Opname</a></li>
                <li><a href="#distribusi">Distribusi</a></li>
                <li><a href="#keuangan">Keuangan &amp; Anggaran</a></li>
                <li><a href="#pelatihan">Pelatihan</a></li>
                <li><a href="#sertifikasi">Sertifikasi &amp; Kepatuhan</a></li>
                <li><a href="#analitik">Analitik</a></li>
                <li><a href="#laporan">Pusat Laporan</a></li>
                <li><a href="#master">Data Master</a></li>
                <li><a href="#admin">Pengaturan &amp; Audit</a></li>
                <li><a href="#alur-status">Alur Status Data</a></li>
                <li><a href="#api">REST API</a></li>
                <li><a href="#kendala">Kendala Umum</a></li>
                <li><a href="#pengembang">Pengembang</a></li>
            </ol>
        </nav>

        <div class="side-actions">
            <a href="{{ url('/login') }}" class="primary">&#8592; Kembali ke Login</a>
            <a href="{{ asset('manual-assets/SIDUMA-PANGAN-Manual-Book.pdf') }}" download>&#8595; Unduh Manual (PDF)</a>
            <a href="{{ url('/api/documentation') }}">Dokumentasi API (Swagger)</a>
        </div>
    </aside>

    <main class="main">
        <header class="hero">
            <div class="badge">📘 MANUAL BOOK PENGGUNA</div>
            <h1>SIDUMA <span>PANGAN</span></h1>
            <p>
                Panduan lengkap penggunaan panel web Sistem Informasi Terpadu Pemasyarakatan dan
                Ketahanan Pangan — mulai dari pendataan WBP, pengelolaan program pangan, produksi
                pertanian dan peternakan, inventaris, keuangan, hingga pelaporan dan analitik.
            </p>
            <div class="meta">
                <span><i>📅</i>Versi manual 1.0 — November 2026</span>
                <span><i>🖥️</i>Panel web AdminLTE + REST API v1</span>
                <span><i>👤</i>Untuk operator, supervisor, admin, dan pengelola keuangan</span>
            </div>
        </header>

        <div class="content">

            <div class="toc-mobile">
                <details>
                    <summary>Daftar Isi — 28 bagian</summary>
                    <ol>
                        <li><a href="#tentang">Tentang Aplikasi</a></li>
                        <li><a href="#akses">Masuk &amp; Peran Pengguna</a></li>
                        <li><a href="#navigasi">Navigasi &amp; Tampilan</a></li>
                        <li><a href="#dashboard">Dashboard</a></li>
                        <li><a href="#wbp">Registrasi WBP</a></li>
                        <li><a href="#blok-kamar">Blok &amp; Kamar</a></li>
                        <li><a href="#laporan-harian">Laporan Harian</a></li>
                        <li><a href="#program-pangan">Program Pangan</a></li>
                        <li><a href="#lahan">Manajemen Lahan</a></li>
                        <li><a href="#pertanian">Pertanian &amp; Panen</a></li>
                        <li><a href="#peternakan">Peternakan</a></li>
                        <li><a href="#perikanan">Perikanan</a></li>
                        <li><a href="#nutrisi">Nutrisi &amp; Menu</a></li>
                        <li><a href="#pengolahan">Pengolahan Hasil</a></li>
                        <li><a href="#inventaris">Inventaris &amp; Stok</a></li>
                        <li><a href="#stock-opname">Stock Opname</a></li>
                        <li><a href="#distribusi">Distribusi</a></li>
                        <li><a href="#keuangan">Keuangan &amp; Anggaran</a></li>
                        <li><a href="#pelatihan">Pelatihan</a></li>
                        <li><a href="#sertifikasi">Sertifikasi &amp; Kepatuhan</a></li>
                        <li><a href="#analitik">Analitik</a></li>
                        <li><a href="#laporan">Pusat Laporan</a></li>
                        <li><a href="#master">Data Master</a></li>
                        <li><a href="#admin">Pengaturan &amp; Audit</a></li>
                        <li><a href="#alur-status">Alur Status Data</a></li>
                        <li><a href="#api">REST API</a></li>
                        <li><a href="#kendala">Kendala Umum</a></li>
                        <li><a href="#pengembang">Pengembang</a></li>
                    </ol>
                    <p style="margin:0 14px 14px 30px;">
                        <a href="{{ asset('manual-assets/SIDUMA-PANGAN-Manual-Book.pdf') }}" download>&#8595; Unduh versi PDF</a>
                    </p>
                </details>
            </div>

            <section id="tentang">
                <h2><span class="num">1</span>Tentang Aplikasi</h2>
                <p>
                    <strong>SIDUMA PANGAN</strong> adalah sistem informasi terpadu untuk mengelola program
                    ketahanan pangan di lingkungan pemasyarakatan. Seluruh data kegiatan — mulai dari
                    warga binaan, hunian blok/kamar, program pangan, lahan, pertanian, peternakan,
                    perikanan, pengolahan hasil, stok, distribusi, sampai keuangan — dikelola dalam
                    satu aplikasi sehingga laporan dapat disusun otomatis.
                </p>

                <div class="grid-2">
                    <div class="card">
                        <h4>🖥️ Panel Web</h4>
                        <p style="margin-bottom:0;">
                            Antarmuka utama yang dijelaskan di manual ini. Diakses melalui browser
                            (Chrome, Edge, Firefox, atau Safari versi terbaru), responsif untuk
                            desktop maupun ponsel.
                        </p>
                    </div>
                    <div class="card">
                        <h4>🔌 REST API v1</h4>
                        <p style="margin-bottom:0;">
                            Untuk integrasi dengan aplikasi lain. Autentikasi memakai token Bearer
                            (Laravel Sanctum) dan didokumentasikan dengan OpenAPI/Swagger.
                        </p>
                    </div>
                </div>

                <h3>Cakupan modul</h3>
                <ul>
                    <li><strong>Modul SDP:</strong> Registrasi WBP, Blok &amp; Kamar, Laporan Harian.</li>
                    <li><strong>Ketahanan pangan:</strong> Program Pangan, Manajemen Lahan, Pertanian, Hasil Panen, Peternakan, Perikanan, Nutrisi &amp; Menu, Pengolahan Hasil.</li>
                    <li><strong>Kelola hasil:</strong> Inventaris &amp; Stok, Stock Opname, Distribusi, Keuangan.</li>
                    <li><strong>Pembinaan:</strong> Pelatihan &amp; Keterampilan, Sertifikasi.</li>
                    <li><strong>Monitoring &amp; laporan:</strong> Analitik, Pusat Laporan (18 jenis laporan + cetak PDF).</li>
                    <li><strong>Administrasi:</strong> Data Master, Manajemen User, Audit Trail, Slow Query.</li>
                </ul>

                <div class="note">
                    <i>ℹ️</i> Data ditampilkan sesuai <strong>unit kerja</strong> pengguna. Jika daftar
                    terlihat kosong, periksa filter unit di halaman tersebut atau pastikan akun Anda
                    memang terhubung ke unit yang benar.
                </div>
            </section>

            <section id="akses">
                <h2><span class="num">2</span>Masuk &amp; Peran Pengguna</h2>

                <h3>Langkah masuk</h3>
                <ol class="steps">
                    <li>Buka alamat aplikasi, misalnya <code>https://pangan.asrar.my.id</code>. Anda akan diarahkan ke halaman login.</li>
                    <li>Isi <strong>Email</strong> dan <strong>Password</strong> yang diberikan administrator.</li>
                    <li>Centang <strong>Ingat saya</strong> bila memakai perangkat pribadi agar sesi tidak cepat berakhir.</li>
                    <li>Klik tombol <strong>Masuk</strong>. Jika berhasil, Anda masuk ke Dashboard.</li>
                </ol>
                <p>
                    Tautan <strong>“Baca Manual Book”</strong> tersedia di bagian bawah halaman login,
                    sehingga panduan ini bisa dibuka tanpa harus masuk terlebih dahulu.
                </p>

                <div class="shots">
                    <figure class="shot wide">
                        <div class="frame"><span></span><span></span><span></span><em>Halaman login</em></div>
                        <img src="{{ asset('manual-assets/shots/login.png') }}" alt="Halaman login SIDUMA PANGAN" width="1280" height="800" loading="lazy">
                        <figcaption>Halaman login: formulir email &amp; password, opsi “Ingat saya”, dan tombol <strong>Baca Manual Book</strong> di bawah tombol Masuk.</figcaption>
                    </figure>
                </div>

                <div class="note warn">
                    <i>⚠️</i> Lupa password? Fitur reset mandiri belum disediakan. Hubungi
                    administrator (Super Admin) untuk dilakukan reset melalui menu
                    <strong>Pengaturan → Manajemen User</strong>.
                </div>

                <h3>Peran pengguna dan hak akses</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Peran</th><th>Fokus pekerjaan</th><th>Akses menu utama</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge-role">Super Admin</span></td>
                                <td>Pengelola sistem secara keseluruhan</td>
                                <td>Semua menu, termasuk Manajemen User, Audit Trail, Slow Query, Data Master</td>
                            </tr>
                            <tr>
                                <td><span class="badge-role">Administrator</span></td>
                                <td>Pengelolaan data &amp; pengguna</td>
                                <td>Hampir semua modul operasional + Data Master; tanpa Audit Trail/Slow Query</td>
                            </tr>
                            <tr>
                                <td><span class="badge-role">Supervisor</span></td>
                                <td>Supervisi kegiatan lapangan &amp; verifikasi laporan</td>
                                <td>WBP, Blok &amp; Kamar, Laporan Harian (konfirmasi/tolak), Program Pangan, Pertanian, Peternakan, Perikanan, Nutrisi, Pengolahan, Pelatihan, Sertifikasi, Analitik, Laporan</td>
                            </tr>
                            <tr>
                                <td><span class="badge-role">Operator</span></td>
                                <td>Input data harian di lapangan</td>
                                <td>WBP, Laporan Harian, Pertanian, Hasil Panen, Peternakan, Perikanan, Inventaris, Stock Opname, Distribusi</td>
                            </tr>
                            <tr>
                                <td><span class="badge-role">Keuangan</span></td>
                                <td>Pengelolaan anggaran &amp; transaksi</td>
                                <td>Keuangan, serta modul laporan yang terkait</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>
                    Jika sebuah menu tidak terlihat, berarti peran akun Anda memang tidak memiliki akses
                    ke menu tersebut. Hubungi administrator bila akses perlu ditambah.
                </p>

                <h3>Keluar dari aplikasi</h3>
                <p>
                    Klik ikon pengguna di sudut kanan atas, lalu pilih <strong>Logout</strong>.
                    Selalu keluar setelah memakai komputer bersama.
                </p>
            </section>

            <section id="navigasi">
                <h2><span class="num">3</span>Navigasi &amp; Tampilan</h2>
                <p>
                    Di sisi kiri terdapat <strong>sidebar</strong> berisi menu yang dikelompokkan per
                    bidang. Menu yang tampil menyesuaikan peran Anda.
                </p>

                <h4>Kelompok menu</h4>
                <ul>
                    <li><strong>Dashboard</strong> — ringkasan KPI seluruh modul.</li>
                    <li><strong>MODUL SDP</strong> — Registrasi WBP, Blok &amp; Kamar, Laporan Harian.</li>
                    <li><strong>KETAHANAN PANGAN</strong> — Program Pangan, Manajemen Lahan, Pertanian, Hasil Panen, Peternakan, Perikanan, Nutrisi &amp; Menu, Pengolahan Hasil.</li>
                    <li><strong>KELOLA HASIL</strong> — Inventaris &amp; Stok, Stock Opname, Distribusi, Keuangan.</li>
                    <li><strong>PEMBINAAN</strong> — Pelatihan &amp; Keterampilan, Sertifikasi.</li>
                    <li><strong>MONITORING</strong> — Analitik; <strong>LAPORAN</strong> — Pusat Laporan dengan 18 sub-laporan.</li>
                    <li><strong>SISTEM</strong> — Slow Queries (Super Admin); <strong>DATA MASTER</strong> dan <strong>PENGATURAN</strong> — master data, manajemen user, audit trail.</li>
                </ul>

                <h4>Elemen yang sering muncul</h4>
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Elemen</th><th>Fungsi</th></tr></thead>
                        <tbody>
                            <tr><td><strong>Kartu statistik</strong> di atas tabel</td><td>Ringkasan jumlah data, total nilai, atau status pada modul tersebut.</td></tr>
                            <tr><td><strong>Filter</strong> (rentang tanggal, unit, status, pencarian)</td><td>Mempersempit data yang ditampilkan. Klik tombol <em>Filter</em> / ikon kaca pembesar untuk menerapkan.</td></tr>
                            <tr><td><strong>Export PDF</strong></td><td>Mengunduh data yang sedang tampil (mengikuti filter) dalam bentuk PDF.</td></tr>
                            <tr><td><strong>+ Tambah …</strong></td><td>Membuka formulir data baru.</td></tr>
                            <tr><td><strong>Ikon mata / pensil / tempat sampah</strong></td><td>Lihat detail, ubah data, dan hapus data.</td></tr>
                            <tr><td><strong>Notifikasi hijau/merah</strong></td><td>Konfirmasi berhasil atau pesan kesalahan setelah suatu aksi.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="note">
                    <i>💡</i> Semua aksi penting (hapus, kirim, konfirmasi) selalu meminta konfirmasi
                    terlebih dahulu. Data yang dihapus tidak dapat dikembalikan, jadi baca dialog
                    konfirmasi dengan teliti.
                </div>
            </section>

            <section id="dashboard">
                <h2><span class="num">4</span>Dashboard</h2>
                <p>
                    Dashboard menampilkan ringkasan kondisi ketahanan pangan pada rentang tanggal dan
                    unit yang dipilih: jumlah WBP aktif, program berjalan, total panen, kondisi stok,
                    keuangan, dan indikator lainnya.
                </p>

                <div class="shots">
                    <figure class="shot wide">
                        <div class="frame"><span></span><span></span><span></span><em>Dashboard — tampilan desktop</em></div>
                        <img src="{{ asset('manual-assets/shots/dashboard-desktop.png') }}" alt="Dashboard SIDUMA PANGAN" width="1280" height="800" loading="lazy">
                        <figcaption>Dashboard: filter periode &amp; unit, tombol Export PDF, dan kartu KPI ringkasan.</figcaption>
                    </figure>
                </div>

                <h3>Cara memakai</h3>
                <ol class="steps">
                    <li>Atur <strong>Dari</strong> dan <strong>Sampai</strong> untuk memilih periode data.</li>
                    <li>Pilih <strong>unit</strong> bila hanya ingin melihat satu unit kerja, atau biarkan <em>Semua Unit</em>.</li>
                    <li>Klik <strong>Filter</strong> untuk memuat ulang data; tombol panah melingkar mengembalikan filter ke awal.</li>
                    <li>Klik <strong>PDF</strong> untuk mengunduh ringkasan dashboard pada periode tersebut.</li>
                </ol>
                <p class="shot-hint">
                    Dashboard diperbarui otomatis setiap 60 detik (terlihat pada label hitung mundur).
                    Tombol refresh manual tetap tersedia.
                </p>
            </section>

            <section id="wbp">
                <h2><span class="num">5</span>Registrasi WBP</h2>
                <p>
                    Modul untuk mendata warga binaan pemasyarakatan (WBP) beserta identitas, dokumen,
                    anggota keluarga, penempatan kamar, dan riwayat statusnya.
                </p>

                <div class="shots">
                    <figure class="shot wide">
                        <div class="frame"><span></span><span></span><span></span><em>Registrasi WBP — daftar</em></div>
                        <img src="{{ asset('manual-assets/shots/wbp-index-desktop.png') }}" alt="Daftar WBP" width="1280" height="800" loading="lazy">
                        <figcaption>Ringkasan total/aktif/baru/dikeluarkan, pencarian, filter status, serta aksi lihat–ubah–hapus.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail WBP</em></div>
                        <img src="{{ asset('manual-assets/shots/wbp-show.png') }}" alt="Detail WBP" width="1280" height="800" loading="lazy">
                        <figcaption>Halaman detail berisi identitas, status, dokumen, dan anggota keluarga.</figcaption>
                    </figure>
                </div>

                <h3>Menambah WBP</h3>
                <p>Klik <strong>+ Tambah WBP</strong> lalu lengkapi:</p>
                <ul>
                    <li><strong>Nomor Induk</strong>, <strong>NIK</strong>, <strong>Nama Lengkap</strong> (wajib).</li>
                    <li><strong>Jenis Kelamin</strong>, <strong>Tanggal Lahir</strong>, <strong>Tempat Lahir</strong>, <strong>Agama</strong>.</li>
                    <li><strong>Tanggal Masuk</strong> dan <strong>Masa Hukuman (hari)</strong>.</li>
                    <li><strong>Unit</strong>, serta <strong>Blok</strong> dan <strong>Kamar</strong> penempatan (opsional, bisa diubah kemudian).</li>
                </ul>

                <h3>Data pendukung</h3>
                <ul>
                    <li><strong>Dokumen:</strong> unggah/ubah/hapus dokumen pendukung pada halaman detail.</li>
                    <li><strong>Anggota keluarga:</strong> tambahkan data keluarga inti beserta hubungannya.</li>
                    <li><strong>Riwayat status:</strong> perubahan status (mis. aktif → bebas) tercatat lengkap dengan waktunya.</li>
                </ul>
            </section>

            <section id="blok-kamar">
                <h2><span class="num">6</span>Blok &amp; Kamar</h2>
                <p>
                    Mengelola hunian: blok (dengan jenis dan kapasitas) serta kamar di dalamnya.
                    Data ini menjadi rujukan penempatan WBP.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar Blok</em></div>
                        <img src="{{ asset('manual-assets/shots/blocks-index.png') }}" alt="Daftar blok" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik total blok &amp; kamar, filter unit, dan daftar blok beserta kapasitas.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail Blok</em></div>
                        <img src="{{ asset('manual-assets/shots/blocks-show.png') }}" alt="Detail blok" width="1280" height="800" loading="lazy">
                        <figcaption>Detail blok berisi daftar kamar, kapasitas, dan status hunian.</figcaption>
                    </figure>
                </div>

                <h3>Menambah blok</h3>
                <p>Klik <strong>+ Tambah Blok</strong>: isi <strong>Kode Blok</strong>, <strong>Nama Blok</strong>, <strong>Unit</strong>, <strong>Jenis Blok</strong>, dan <strong>Kapasitas</strong>.</p>

                <h3>Mengelola kamar</h3>
                <ol class="steps">
                    <li>Buka detail blok yang dituju.</li>
                    <li>Tambahkan kamar beserta kapasitasnya.</li>
                    <li>Gunakan aksi status untuk menandai kamar (mis. perbaikan/penuh).</li>
                </ol>
                <p class="shot-hint">Perubahan status blok dan kamar tersimpan pada riwayat status sehingga dapat ditelusuri.</p>
            </section>

            <section id="laporan-harian">
                <h2><span class="num">7</span>Laporan Harian</h2>
                <p>
                    Operator mengisi laporan kegiatan harian per unit, kemudian supervisor atau admin
                    melakukan konfirmasi. Alurnya: <strong>draft → dikirim → dikonfirmasi</strong>, atau
                    <strong>dikembalikan</strong> ke draft untuk diperbaiki.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar laporan harian</em></div>
                        <img src="{{ asset('manual-assets/shots/daily-reports-index.png') }}" alt="Daftar laporan harian" width="1280" height="800" loading="lazy">
                        <figcaption>Filter tanggal/status, ringkasan laporan, dan status tiap laporan.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail laporan harian</em></div>
                        <img src="{{ asset('manual-assets/shots/daily-reports-show.png') }}" alt="Detail laporan harian" width="1280" height="800" loading="lazy">
                        <figcaption>Detail laporan beserta rincian kegiatan dan aksi sesuai status.</figcaption>
                    </figure>
                </div>

                <h3>Membuat laporan</h3>
                <p>Klik <strong>+ Tambah Laporan</strong>, lalu isi <strong>Unit</strong>, <strong>Tanggal</strong>, <strong>Ringkasan Kegiatan</strong>, <strong>Catatan Khusus</strong>, dan <strong>Masalah Ditemukan</strong>. Rincian kegiatan ditambahkan pada bagian detail laporan.</p>

                <h3>Alur kerja</h3>
                <ol class="steps">
                    <li><strong>Simpan sebagai draft</strong> selama laporan masih dilengkapi.</li>
                    <li>Klik <strong>Kirim</strong> — status berubah menjadi <em>dikirim</em> dan menunggu review.</li>
                    <li>Supervisor/admin membuka laporan lalu memilih <strong>Konfirmasi</strong> (status <em>dikonfirmasi</em>) atau <strong>Kembalikan</strong> (status kembali <em>draft</em> dengan catatan perbaikan).</li>
                </ol>
                <div class="note warn">
                    <i>⚠️</i> Hanya laporan berstatus <strong>draft</strong> yang bisa dikirim. Jika laporan
                    sudah dikirim, tunggu keputusan supervisor atau minta dikembalikan lebih dulu.
                </div>
            </section>

            <section id="program-pangan">
                <h2><span class="num">8</span>Program Pangan</h2>
                <p>
                    Mendata program ketahanan pangan (jenis, anggaran, periode) beserta peserta
                    program. Program menjadi payung kegiatan pertanian, peternakan, dan perikanan.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar program pangan</em></div>
                        <img src="{{ asset('manual-assets/shots/food-programs-index.png') }}" alt="Daftar program pangan" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik program, filter status/unit, dan daftar program.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail program</em></div>
                        <img src="{{ asset('manual-assets/shots/food-programs-show.png') }}" alt="Detail program pangan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail program: anggaran, periode, peserta, dan kegiatan terkait.</figcaption>
                    </figure>
                </div>

                <h3>Menambah program</h3>
                <p>Isi <strong>Kode Program</strong>, <strong>Jenis Program</strong>, <strong>Nama Program</strong>, <strong>Deskripsi</strong>, <strong>Unit</strong>, <strong>Tanggal Mulai</strong>, <strong>Rencana Selesai</strong>, dan <strong>Anggaran</strong>.</p>

                <h3>Mengelola peserta</h3>
                <p>Pada halaman detail program, tambahkan peserta dari daftar WBP. Peserta bisa dihapus bila tidak lagi terlibat. Status program diubah melalui aksi status (mis. perencanaan → berjalan → selesai).</p>
            </section>

            <section id="lahan">
                <h2><span class="num">9</span>Manajemen Lahan</h2>
                <p>
                    Mendata lahan yang dipakai untuk kegiatan pertanian: luas, jenis tanah, sistem
                    irigasi, dan status pemanfaatannya. Lahan menjadi pilihan saat membuat rencana
                    kerja pertanian.
                </p>
                <div class="shots">
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar lahan</em></div>
                        <img src="{{ asset('manual-assets/shots/land-plots-index.png') }}" alt="Daftar lahan" width="1280" height="800" loading="lazy">
                        <figcaption>Ringkasan luas lahan, jumlah lahan produktif, dan daftar lahan per unit.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail lahan</em></div>
                        <img src="{{ asset('manual-assets/shots/land-plots-show.png') }}" alt="Detail lahan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail lahan: luas, jenis tanah, irigasi, status, serta daftar rencana pertanian di lahan tersebut.</figcaption>
                    </figure>
                </div>

                <h3>Menambah lahan</h3>
                <p>Isi <strong>Kode Lahan</strong>, <strong>Nama Lahan</strong>, <strong>Unit</strong>, <strong>Luas (m²)</strong>, <strong>Jenis Tanah</strong>, <strong>Sistem Irigasi</strong>, <strong>Status</strong>, dan <strong>Keterangan</strong>.</p>
                <p class="shot-hint">Gunakan aksi status untuk menandai lahan sedang dipakai, istirahat, atau tidak produktif. Daftar rencana pertanian pada halaman detail memudahkan pengecekan lahan yang sedang terpakai.</p>
            </section>

            <section id="pertanian">
                <h2><span class="num">10</span>Pertanian &amp; Panen</h2>
                <p>
                    Modul inti produksi: menyusun rencana kerja pertanian di atas lahan, mencatat
                    aktivitas budidaya, lalu mencatat hasil panen beserta nilainya.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Rencana kerja pertanian</em></div>
                        <img src="{{ asset('manual-assets/shots/farming-plans-index.png') }}" alt="Rencana kerja pertanian" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik lahan tersedia, tanaman berjalan, panen bulan ini, dan pendapatan panen.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail rencana kerja</em></div>
                        <img src="{{ asset('manual-assets/shots/farming-plans-show.png') }}" alt="Detail rencana kerja pertanian" width="1280" height="800" loading="lazy">
                        <figcaption>Detail rencana: jadwal tanam/panen, aktivitas, peserta, dan tombol input panen.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Hasil panen</em></div>
                        <img src="{{ asset('manual-assets/shots/harvests-index.png') }}" alt="Daftar hasil panen" width="1280" height="800" loading="lazy">
                        <figcaption>Rekap panen: total berat, kualitas, harga jual, dan pendapatan.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail panen</em></div>
                        <img src="{{ asset('manual-assets/shots/harvests-show.png') }}" alt="Detail hasil panen" width="1280" height="800" loading="lazy">
                        <figcaption>Detail panen: rencana kerja, lahan asal, berat, kualitas, harga jual, dan total pendapatan.</figcaption>
                    </figure>
                </div>

                <h3>Membuat rencana kerja</h3>
                <p>Klik <strong>+ Tambah Rencana</strong>, lalu isi <strong>Kode Rencana</strong>, <strong>Lahan</strong>, <strong>Jenis Tanaman</strong>, <strong>Program Pangan</strong>, <strong>Jumlah Tanaman</strong>, <strong>Luas (m²)</strong>, <strong>Tanggal Tanam Rencana</strong>, <strong>Tanggal Panen Rencana</strong>, <strong>Estimasi Hasil</strong>, <strong>Estimasi Pendapatan</strong>, dan <strong>Metode Budi Daya</strong>.</p>

                <h3>Mencatat aktivitas</h3>
                <p>Dari halaman detail rencana, tambahkan aktivitas (mis. pengolahan tanah, penanaman, pemupukan, penyiangan) beserta tanggal dan catatannya. Aktivitas membentuk riwayat budidaya.</p>

                <h3>Mencatat panen</h3>
                <p>Isi <strong>Rencana Kerja</strong>, <strong>Tanggal Panen</strong>, <strong>Total Berat (kg)</strong>, <strong>Kualitas</strong>, <strong>Harga Jual/kg</strong>, dan <strong>Total Pendapatan</strong>. Panen yang tercatat otomatis terhitung pada dashboard dan laporan.</p>
            </section>

            <section id="peternakan">
                <h2><span class="num">11</span>Peternakan</h2>
                <p>
                    Mengelola kandang, populasi ternak, pasokan dan log pakan, serta rekam kesehatan
                    hewan.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar kandang</em></div>
                        <img src="{{ asset('manual-assets/shots/livestock-barns-index.png') }}" alt="Daftar kandang" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik kandang &amp; populasi, filter unit, dan daftar kandang.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail kandang</em></div>
                        <img src="{{ asset('manual-assets/shots/livestock-barns-show.png') }}" alt="Detail kandang" width="1280" height="800" loading="lazy">
                        <figcaption>Detail kandang: populasi, riwayat pakan, dan catatan kesehatan ternak.</figcaption>
                    </figure>
                </div>

                <h3>Menambah kandang</h3>
                <p>Isi <strong>Kode Kandang</strong>, <strong>Nama Kandang</strong>, <strong>Jenis Ternak</strong>, <strong>Unit</strong>, <strong>Kapasitas</strong>, <strong>Luas (m²)</strong>, <strong>Tipe Kandang</strong>, <strong>Status</strong>, dan <strong>Keterangan</strong>.</p>

                <h3>Pakan &amp; kesehatan</h3>
                <ul>
                    <li><strong>Pasokan/log pakan:</strong> catat jenis pakan, jumlah, dan tanggal pemberian agar konsumsi terpantau.</li>
                    <li><strong>Kesehatan hewan:</strong> catat pemeriksaan, vaksinasi, atau pengobatan beserta hasilnya.</li>
                    <li><strong>Populasi:</strong> perbarui jumlah ternak saat ada kelahiran, kematian, atau penjualan.</li>
                </ul>
            </section>

            <section id="perikanan">
                <h2><span class="num">12</span>Perikanan</h2>
                <p>
                    Mendata kolam, stok ikan, kualitas air, dan hasil panen ikan. Sama seperti
                    pertanian, data perikanan masuk ke dashboard, analitik, dan laporan.
                </p>

                <div class="shots">
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar kolam</em></div>
                        <img src="{{ asset('manual-assets/shots/fish-ponds-index.png') }}" alt="Daftar kolam ikan" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik kolam, luas total, dan daftar kolam per unit.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail kolam</em></div>
                        <img src="{{ asset('manual-assets/shots/fish-ponds-show.png') }}" alt="Detail kolam ikan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail kolam: luas, kedalaman, volume, sumber air, dan catatan budidaya ikan.</figcaption>
                    </figure>
                </div>

                <h3>Menambah kolam</h3>
                <p>Isi <strong>Kode Kolam</strong>, <strong>Nama Kolam</strong>, <strong>Unit</strong>, <strong>Luas (m²)</strong>, <strong>Kedalaman (m)</strong>, <strong>Volume (m³)</strong>, <strong>Tipe Kolam</strong>, <strong>Sumber Air</strong>, <strong>Status</strong>, dan <strong>Keterangan</strong>.</p>
                <p class="shot-hint">Catat tebar benih, sampling pertumbuhan, kualitas air, dan panen ikan pada halaman detail kolam agar riwayat budidaya lengkap.</p>
            </section>

            <section id="nutrisi">
                <h2><span class="num">13</span>Nutrisi &amp; Menu</h2>
                <p>
                    Menyusun rencana menu harian beserta target gizi (kalori, protein, karbohidrat,
                    lemak, serat) dan estimasi biaya per porsi. Rencana menu dapat dihubungkan dengan
                    komponen makan dan hasil produksi.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Rencana menu &amp; gizi</em></div>
                        <img src="{{ asset('manual-assets/shots/nutrition-index.png') }}" alt="Rencana menu dan gizi" width="1280" height="800" loading="lazy">
                        <figcaption>Daftar rencana menu, periode berlaku, target gizi, dan status.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail rencana menu</em></div>
                        <img src="{{ asset('manual-assets/shots/nutrition-show.png') }}" alt="Detail rencana menu dan gizi" width="1280" height="800" loading="lazy">
                        <figcaption>Detail menu: komposisi, target gizi per porsi, estimasi biaya, dan komponen makan.</figcaption>
                    </figure>
                </div>

                <h3>Menyusun menu</h3>
                <p>Isi <strong>Kode Rencana Gizi</strong>, <strong>Nama Menu</strong>, <strong>Unit</strong>, <strong>Waktu Makan</strong>, <strong>Tipe Porsi</strong>, target <strong>Kalori/Protein/Karbohidrat/Lemak/Serat</strong>, <strong>Berlaku Mulai–Sampai</strong>, <strong>Estimasi Biaya per Porsi</strong>, dan <strong>Status</strong>.</p>
                <p class="shot-hint">Pastikan periode menu tidak bertumpuk untuk unit dan waktu makan yang sama, agar laporan gizi tidak dobel.</p>
            </section>

            <section id="pengolahan">
                <h2><span class="num">14</span>Pengolahan Hasil</h2>
                <p>
                    Mencatat batch pengolahan hasil produksi (mis. penggilingan, pengeringan,
                    pengemasan) menjadi produk dengan nilai jual lebih tinggi.
                </p>
                <div class="shots">
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar batch pengolahan</em></div>
                        <img src="{{ asset('manual-assets/shots/processing-index.png') }}" alt="Daftar pengolahan hasil" width="1280" height="800" loading="lazy">
                        <figcaption>Rekap batch pengolahan, jumlah produksi, biaya, dan status tiap batch.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail batch</em></div>
                        <img src="{{ asset('manual-assets/shots/processing-show.png') }}" alt="Detail batch pengolahan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail batch: jumlah produksi, biaya produksi, harga jual per satuan, dan masa berlaku produk.</figcaption>
                    </figure>
                </div>

                <h3>Menambah batch</h3>
                <p>Isi <strong>Kode Batch</strong>, <strong>Nama Produk</strong>, <strong>Unit</strong>, <strong>Jenis Pengolahan</strong>, <strong>Tanggal Produksi</strong>, <strong>Tanggal Kadaluarsa</strong>, <strong>Jumlah Produksi</strong>, <strong>Satuan Output</strong>, <strong>Biaya Produksi</strong>, <strong>Harga Jual per Satuan</strong>, dan <strong>Status</strong>.</p>
                <div class="note">
                    <i>💡</i> Isi tanggal kadaluarsa untuk produk yang mudah rusak. Produk kedaluwarsa
                    sebaiknya segera diubah statusnya agar tidak ikut terdistribusikan.
                </div>
            </section>

            <section id="inventaris">
                <h2><span class="num">15</span>Inventaris &amp; Stok</h2>
                <p>
                    Mengelola barang/pangan yang tersimpan: kategori, satuan, stok, harga beli/jual,
                    serta transaksi masuk dan keluar.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Inventaris &amp; stok</em></div>
                        <img src="{{ asset('manual-assets/shots/inventory-index.png') }}" alt="Inventaris dan stok" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik nilai stok, item di bawah minimum, dan daftar barang.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail item inventaris</em></div>
                        <img src="{{ asset('manual-assets/shots/inventory-show.png') }}" alt="Detail item inventaris" width="1280" height="800" loading="lazy">
                        <figcaption>Detail item: stok saat ini, stok minimum, harga, serta riwayat transaksi masuk/keluar.</figcaption>
                    </figure>
                </div>

                <h3>Menambah item</h3>
                <p>Isi <strong>Kode Item</strong>, <strong>Nama Item</strong>, <strong>Kategori</strong>, <strong>Unit</strong>, <strong>Tipe Pangan</strong>, <strong>Asal</strong>, <strong>Satuan</strong>, <strong>Stok Awal</strong>, <strong>Stok Minimum</strong>, <strong>Harga Beli</strong>, dan <strong>Harga Jual</strong>.</p>

                <h3>Transaksi stok</h3>
                <p>Catat barang masuk (penerimaan/panen) dan barang keluar (pemakaian/distribusi). Stok berubah otomatis mengikuti transaksi. Item dengan stok di bawah minimum ditandai agar segera ditindaklanjuti.</p>
            </section>

            <section id="stock-opname">
                <h2><span class="num">16</span>Stock Opname</h2>
                <p>
                    Melakukan penghitungan fisik stok dan membandingkannya dengan catatan sistem.
                    Selisih yang ditemukan dicatat sebagai koreksi.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar stock opname</em></div>
                        <img src="{{ asset('manual-assets/shots/stock-opname-index.png') }}" alt="Daftar stock opname" width="1280" height="800" loading="lazy">
                        <figcaption>Riwayat opname per unit, jenis, tanggal, dan statusnya.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail stock opname</em></div>
                        <img src="{{ asset('manual-assets/shots/stock-opname-show.png') }}" alt="Detail stock opname" width="1280" height="800" loading="lazy">
                        <figcaption>Detail opname: daftar item yang dihitung, stok sistem vs fisik, dan selisihnya.</figcaption>
                    </figure>
                </div>

                <h3>Langkah opname</h3>
                <ol class="steps">
                    <li>Buat opname baru: isi <strong>Kode Opname</strong>, <strong>Unit</strong>, <strong>Tanggal</strong>, <strong>Jenis Opname</strong>, <strong>Petugas</strong>, dan <strong>Keterangan</strong>.</li>
                    <li>Masukkan hasil hitung fisik setiap item pada detail opname.</li>
                    <li>Periksa selisih antara stok sistem dan stok fisik.</li>
                    <li>Ubah status opname menjadi selesai agar koreksi stok diterapkan.</li>
                </ol>
            </section>

            <section id="distribusi">
                <h2><span class="num">17</span>Distribusi</h2>
                <p>
                    Mencatat penyaluran hasil produksi/pangan kepada penerima, lengkap dengan item,
                    jumlah, dan nilainya.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar distribusi</em></div>
                        <img src="{{ asset('manual-assets/shots/distributions-index.png') }}" alt="Daftar distribusi" width="1280" height="800" loading="lazy">
                        <figcaption>Rekap distribusi, total nilai, dan status penyaluran.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail distribusi</em></div>
                        <img src="{{ asset('manual-assets/shots/distributions-show.png') }}" alt="Detail distribusi" width="1280" height="800" loading="lazy">
                        <figcaption>Detail penerima, daftar item yang disalurkan, dan status.</figcaption>
                    </figure>
                </div>

                <h3>Membuat distribusi</h3>
                <p>Isi <strong>Kode Distribusi</strong>, <strong>Tipe Distribusi</strong>, <strong>Unit</strong>, <strong>Tanggal</strong>, <strong>Nama Penerima</strong>, <strong>Total Nilai</strong>, dan <strong>Keterangan</strong>, lalu tambahkan item yang disalurkan.</p>
            </section>

            <section id="keuangan">
                <h2><span class="num">18</span>Keuangan &amp; Anggaran</h2>
                <p>
                    Mencatat pemasukan dan pengeluaran program, dikelompokkan per kategori dan sumber
                    dana. Menu ini umumnya dikelola peran <strong>Keuangan</strong>, <strong>Admin</strong>,
                    dan <strong>Super Admin</strong>.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Keuangan &amp; anggaran</em></div>
                        <img src="{{ asset('manual-assets/shots/financial-index.png') }}" alt="Keuangan dan anggaran" width="1280" height="800" loading="lazy">
                        <figcaption>Pemasukan bulan ini, pengeluaran, saldo total, dan daftar transaksi.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail transaksi</em></div>
                        <img src="{{ asset('manual-assets/shots/financial-show.png') }}" alt="Detail transaksi keuangan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail transaksi: tipe, sumber dana, metode, kategori, jumlah, dan riwayat status.</figcaption>
                    </figure>
                </div>

                <h3>Menambah transaksi</h3>
                <p>Isi <strong>Tipe</strong> (pemasukan/pengeluaran), <strong>Sumber Dana</strong>, <strong>Metode</strong>, <strong>Kategori</strong>, <strong>Unit</strong>, <strong>Jumlah</strong>, <strong>Tanggal</strong>, dan <strong>Deskripsi</strong>.</p>
                <p class="shot-hint">Gunakan filter tipe dan rentang tanggal untuk memeriksa saldo pada periode tertentu. Perubahan status transaksi memerlukan hak akses admin/keuangan.</p>
            </section>

            <section id="pelatihan">
                <h2><span class="num">19</span>Pelatihan &amp; Keterampilan</h2>
                <p>
                    Mendata program pelatihan bagi WBP: jadwal, lokasi, kapasitas, biaya, peserta,
                    dan sertifikat yang diberikan.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Pelatihan</em></div>
                        <img src="{{ asset('manual-assets/shots/training-index.png') }}" alt="Daftar pelatihan" width="1280" height="800" loading="lazy">
                        <figcaption>Daftar pelatihan beserta status, jadwal, dan jumlah peserta.</figcaption>
                    </figure>
                    <figure class="shot">
                        <div class="frame"><span></span><span></span><span></span><em>Detail pelatihan</em></div>
                        <img src="{{ asset('manual-assets/shots/training-show.png') }}" alt="Detail pelatihan" width="1280" height="800" loading="lazy">
                        <figcaption>Detail pelatihan: materi, jadwal, kapasitas, daftar peserta, dan sertifikat.</figcaption>
                    </figure>
                </div>

                <h3>Membuat pelatihan</h3>
                <p>Isi <strong>Kode Pelatihan</strong>, <strong>Nama Pelatihan</strong>, <strong>Kategori</strong>, <strong>Tipe Program</strong>, <strong>Unit</strong>, <strong>Lokasi</strong>, <strong>Tanggal Mulai–Selesai</strong>, <strong>Jam Pelatihan</strong>, <strong>Kapasitas Peserta</strong>, <strong>Biaya</strong>, dan <strong>Materi Pokok</strong>.</p>
                <p class="shot-hint">Tambahkan peserta dari daftar WBP pada detail pelatihan, lalu tandai sertifikat yang sudah diberikan.</p>
            </section>

            <section id="sertifikasi">
                <h2><span class="num">20</span>Sertifikasi &amp; Kepatuhan</h2>
                <p>
                    Mencatat pemeriksaan kepatuhan (compliance) dan sertifikasi: hasil pemeriksaan,
                    skor, tingkat risiko, temuan, serta rekomendasi tindak lanjut.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar sertifikasi</em></div>
                        <img src="{{ asset('manual-assets/shots/compliance-index.png') }}" alt="Daftar sertifikasi" width="1280" height="800" loading="lazy">
                        <figcaption>Statistik kepatuhan, daftar pemeriksaan, masa berlaku, dan status.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail sertifikasi</em></div>
                        <img src="{{ asset('manual-assets/shots/compliance-show.png') }}" alt="Detail sertifikasi" width="1280" height="800" loading="lazy">
                        <figcaption>Detail temuan, rekomendasi, skor, dan riwayat status.</figcaption>
                    </figure>
                </div>

                <h3>Mencatat pemeriksaan</h3>
                <p>Isi <strong>Kode Komplians</strong>, <strong>Judul</strong>, <strong>Tipe Sertifikasi</strong>, <strong>Unit</strong>, <strong>Tanggal Pemeriksaan</strong>, <strong>Berlaku Sampai</strong>, <strong>Nilai Skor</strong>, <strong>Tingkat Risiko</strong>, <strong>Hasil</strong>, <strong>Temuan</strong>, dan <strong>Rekomendasi</strong>.</p>
                <div class="note warn">
                    <i>⚠️</i> Perhatikan tanggal <strong>berlaku sampai</strong>. Sertifikat yang mendekati
                    kedaluwarsa sebaiknya segera dijadwalkan ulang agar kepatuhan tidak terputus.
                </div>
            </section>

            <section id="analitik">
                <h2><span class="num">21</span>Analitik</h2>
                <p>
                    Memantau indikator kinerja: target, realisasi, dan persentase pencapaian per unit.
                    Indikator dapat ditambah dan disesuaikan sesuai kebutuhan program.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Dashboard analitik</em></div>
                        <img src="{{ asset('manual-assets/shots/analytics-dashboard.png') }}" alt="Dashboard analitik" width="1280" height="800" loading="lazy">
                        <figcaption>Total indikator, target, realisasi, pencapaian keseluruhan, dan grafik target vs realisasi.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Daftar indikator</em></div>
                        <img src="{{ asset('manual-assets/shots/analytics-index.png') }}" alt="Daftar indikator analitik" width="1280" height="800" loading="lazy">
                        <figcaption>Daftar indikator dengan target, realisasi, dan periode.</figcaption>
                    </figure>
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Detail indikator</em></div>
                        <img src="{{ asset('manual-assets/shots/analytics-show.png') }}" alt="Detail indikator analitik" width="1280" height="800" loading="lazy">
                        <figcaption>Rincian indikator: satuan, target, realisasi, capaian, dan tren.</figcaption>
                    </figure>
                </div>

                <p class="shot-hint">Gunakan filter unit di dashboard analitik untuk membandingkan capaian antar unit, lalu unduh PDF untuk lampiran rapat.</p>
            </section>

            <section id="laporan">
                <h2><span class="num">22</span>Pusat Laporan</h2>
                <p>
                    Semua laporan tersedia di satu tempat. Setiap jenis laporan dapat dipratinjau dan
                    dicetak/diunduh sebagai PDF.
                </p>

                <div class="shots">
                    <figure class="shot wide">
                        <div class="frame"><span></span><span></span><span></span><em>Pusat laporan</em></div>
                        <img src="{{ asset('manual-assets/shots/reports-index.png') }}" alt="Pusat laporan" width="1280" height="800" loading="lazy">
                        <figcaption>Pusat laporan: pilih jenis laporan, periode, dan unit sebelum mencetak.</figcaption>
                    </figure>
                </div>

                <h3>Jenis laporan yang tersedia</h3>
                <div class="grid-2">
                    <div class="card">
                        <ul style="margin-bottom:0;">
                            <li>Ringkasan</li>
                            <li>Keuangan</li>
                            <li>Data WBP</li>
                            <li>Program Pangan</li>
                            <li>Pertanian</li>
                            <li>Hasil Panen</li>
                            <li>Peternakan</li>
                            <li>Perikanan</li>
                            <li>Inventaris &amp; Stok</li>
                        </ul>
                    </div>
                    <div class="card">
                        <ul style="margin-bottom:0;">
                            <li>Laporan Harian</li>
                            <li>Distribusi</li>
                            <li>Nutrisi &amp; Menu</li>
                            <li>Pelatihan</li>
                            <li>Sertifikasi</li>
                            <li>Analitik</li>
                            <li>Pengolahan</li>
                            <li>Stock Opname</li>
                            <li>Manajemen Lahan</li>
                        </ul>
                    </div>
                </div>

                <h3>Mencetak laporan</h3>
                <ol class="steps">
                    <li>Buka <strong>Laporan</strong> di sidebar, lalu pilih jenis laporan.</li>
                    <li>Tentukan periode (dan unit bila tersedia), kemudian tampilkan pratinjau.</li>
                    <li>Periksa angka pada pratinjau.</li>
                    <li>Klik <strong>Cetak/PDF</strong>. Laporan memakai kop surat resmi dan siap dilampirkan.</li>
                </ol>
                <div class="note">
                    <i>💡</i> Tombol <strong>Export PDF</strong> di halaman modul (WBP, panen, keuangan,
                    dan lainnya) juga mengikuti filter yang sedang aktif, sehingga hasilnya sesuai
                    data yang terlihat di layar.
                </div>
            </section>

            <section id="master">
                <h2><span class="num">23</span>Data Master</h2>
                <p>
                    Data referensi yang dipakai lintas modul. Kelola data master lebih dulu sebelum
                    mengisi data operasional.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Unit / Lapas</em></div>
                        <img src="{{ asset('manual-assets/shots/master-units.png') }}" alt="Data master unit" width="1280" height="800" loading="lazy">
                        <figcaption>Daftar unit kerja/lapas sebagai dasar scoping data.</figcaption>
                    </figure>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Menu</th><th>Dipakai pada</th></tr></thead>
                        <tbody>
                            <tr><td>Unit / Lapas</td><td>Hampir semua modul (filter unit, penempatan, laporan)</td></tr>
                            <tr><td>Jenis Ternak</td><td>Kandang &amp; populasi ternak</td></tr>
                            <tr><td>Jenis Tanaman</td><td>Rencana kerja pertanian</td></tr>
                            <tr><td>Kategori Inventaris</td><td>Item inventaris &amp; stok</td></tr>
                            <tr><td>Kategori Keuangan</td><td>Transaksi pemasukan/pengeluaran</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="shot-hint">Data master hanya bisa diubah oleh Administrator dan Super Admin. Menghapus data master yang sudah dipakai dapat memengaruhi data terkait.</p>
            </section>

            <section id="admin">
                <h2><span class="num">24</span>Pengaturan, Pengguna &amp; Audit</h2>

                <h3>Manajemen User</h3>
                <p>
                    Super Admin dapat menambah pengguna, mengatur peran, serta mereset password.
                    Setiap pengguna sebaiknya memiliki akun sendiri agar aktivitasnya terekam di audit
                    trail.
                </p>

                <div class="shots">
                    <figure class="shot tall">
                        <div class="frame"><span></span><span></span><span></span><em>Manajemen user</em></div>
                        <img src="{{ asset('manual-assets/shots/admin-users.png') }}" alt="Manajemen user" width="1280" height="800" loading="lazy">
                        <figcaption>Daftar pengguna beserta peran dan status akun.</figcaption>
                    </figure>
                </div>

                <h3>Audit Trail</h3>
                <p>
                    Mencatat perubahan data penting beserta pelaku dan waktunya. Gunakan menu ini untuk
                    menelusuri siapa yang mengubah atau menghapus suatu data.
                </p>

                <div class="shots">
                    <figure class="shot wide">
                        <div class="frame"><span></span><span></span><span></span><em>Audit trail</em></div>
                        <img src="{{ asset('manual-assets/shots/admin-audit-trail.png') }}" alt="Audit trail aplikasi" width="1280" height="800" loading="lazy">
                        <figcaption>Audit trail: filter user/event/tanggal, daftar aktivitas (create, update, delete), dan ekspor CSV.</figcaption>
                    </figure>
                </div>

                <h3>Slow Query</h3>
                <p>
                    Memantau query database yang berjalan lambat. Halaman ini membantu administrator
                    menjaga performa aplikasi saat data bertambah banyak.
                </p>

                <h3>Profil &amp; password</h3>
                <p>Melalui ikon pengguna → <strong>Profile</strong>, Anda dapat memperbarui data akun dan mengganti password sendiri.</p>
            </section>

            <section id="alur-status">
                <h2><span class="num">25</span>Alur Status Data</h2>
                <p>
                    Hampir semua entitas memiliki status yang dapat diubah melalui aksi status, dan
                    setiap perubahan tercatat pada riwayat. Alur yang paling ketat ada di laporan
                    harian:
                </p>

                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Status</th><th>Artinya</th><th>Siapa yang mengubah</th></tr></thead>
                        <tbody>
                            <tr><td><strong>draft</strong></td><td>Laporan baru, masih bisa diperbaiki</td><td>Operator (pembuat)</td></tr>
                            <tr><td><strong>dikirim</strong></td><td>Menunggu review supervisor</td><td>Operator</td></tr>
                            <tr><td><strong>dikonfirmasi</strong></td><td>Sudah diverifikasi dan dianggap final</td><td>Supervisor / Admin</td></tr>
                            <tr><td><strong>dikembalikan</strong></td><td>Ditolak dan dikembalikan ke draft untuk diperbaiki</td><td>Supervisor / Admin</td></tr>
                        </tbody>
                    </table>
                </div>

                <p>
                    Pada modul lain (program, rencana pertanian, kandang, kolam, distribusi, pelatihan,
                    sertifikasi, dan lainnya), status dipakai untuk menandai tahapan kegiatan, misalnya
                    <em>perencanaan → berjalan → selesai</em>. Perubahan status memerlukan hak akses
                    tertentu sesuai peran.
                </p>
            </section>

            <section id="api">
                <h2><span class="num">26</span>REST API</h2>
                <p>
                    Selain panel web, tersedia REST API v1 untuk integrasi. Dokumentasi interaktif
                    (Swagger) dapat dibuka di <a href="{{ url('/api/documentation') }}"><code>/api/documentation</code></a>.
                </p>

                <h3>Autentikasi</h3>
                <p>API memakai token Bearer dari Laravel Sanctum. Sertakan token pada header:</p>
                <pre><code>Authorization: Bearer &lt;token-anda&gt;
Accept: application/json</code></pre>

                <h3>Contoh permintaan</h3>
                <pre><code>curl -X GET "{{ url('/api/v1/wbp') }}" \
  -H "Authorization: Bearer &lt;token-anda&gt;" \
  -H "Accept: application/json"</code></pre>

                <div class="note">
                    <i>🔐</i> Token bersifat rahasia. Jangan menyimpan token di aplikasi klien publik
                    atau membagikannya. Minta administrator mencabut token lama bila diduga bocor.
                </div>
            </section>

            <section id="kendala">
                <h2><span class="num">27</span>Kendala Umum</h2>

                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Gejala</th><th>Penyebab umum</th><th>Solusi</th></tr></thead>
                        <tbody>
                            <tr>
                                <td>Halaman <em>Server Error</em> (500)</td>
                                <td>Folder <code>storage</code>/<code>bootstrap/cache</code> tidak bisa ditulis, mis. setelah update kode</td>
                                <td>Administrator server menjalankan <code>chown -R www:www storage bootstrap/cache</code>, lalu cek <code>storage/logs/laravel.log</code></td>
                            </tr>
                            <tr>
                                <td>“Page Expired” (419)</td>
                                <td>Sesi kedaluwarsa (halaman terbuka terlalu lama)</td>
                                <td>Muat ulang halaman, lalu ulangi input</td>
                            </tr>
                            <tr>
                                <td>Daftar data kosong padahal ada</td>
                                <td>Filter unit/tanggal/status masih aktif</td>
                                <td>Reset filter atau pilih <em>Semua Unit</em></td>
                            </tr>
                            <tr>
                                <td>Tombol beraksi tidak merespons</td>
                                <td>Dialog konfirmasi belum disetujui, atau koneksi terputus</td>
                                <td>Periksa dialog konfirmasi dan koneksi internet</td>
                            </tr>
                            <tr>
                                <td>Menu tertentu tidak muncul</td>
                                <td>Peran akun tidak memiliki hak akses</td>
                                <td>Hubungi administrator untuk penyesuaian peran</td>
                            </tr>
                            <tr>
                                <td>PDF laporan tidak terunduh</td>
                                <td>Data pada filter kosong atau proses masih berjalan</td>
                                <td>Longgarkan filter lalu coba lagi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="note warn">
                    <i>⚠️</i> Bila muncul pesan error, catat <strong>waktu kejadian</strong> dan
                    <strong>halaman</strong> yang dibuka, lalu laporkan ke administrator. Detail teknis
                    tersimpan di <code>storage/logs/laravel.log</code>.
                </div>
            </section>

            <section id="pengembang">
                <h2><span class="num">28</span>Pengembang</h2>
                <div class="dev">
                    <h3>KerangRendang</h3>
                    <p>
                        Pengembang dan pemelihara aplikasi <strong>SIDUMA PANGAN</strong>.
                        Untuk pertanyaan teknis, permintaan fitur, atau pelaporan bug, silakan hubungi:
                    </p>
                    <ul style="color:#cbd5e1;">
                        <li>Email: <a href="mailto:overload.xp@gmail.com">overload.xp@gmail.com</a></li>
                        <li>Repositori: <a href="https://github.com/overloaders/sipandu-pangan" target="_blank" rel="noopener noreferrer">github.com/overloaders/sipandu-pangan</a></li>
                    </ul>
                    <p style="margin-bottom:0;font-size:12.5px;color:#94a3b8;">
                        Dibangun dengan Laravel 13, AdminLTE 3, Bootstrap 4, Chart.js, dan DomPDF.
                    </p>
                </div>

                <footer class="manual-footer">
                    <span>&copy; {{ date('Y') }} SIDUMA PANGAN — Sistem Informasi Terpadu Pemasyarakatan dan Ketahanan Pangan</span>
                    <span class="manual-pdf-actions">
                        <a href="{{ asset('manual-assets/SIDUMA-PANGAN-Manual-Book.pdf') }}" download>Unduh PDF</a>
                        &middot; <a href="{{ url('/login') }}">Kembali ke Login</a>
                    </span>
                </footer>
            </section>

        </div>
    </main>
</div>
</body>
</html>
