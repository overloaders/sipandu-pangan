{{--
    Shared PDF Styles for all report views (DomPDF-safe).

    DomPDF v3 does NOT support CSS Grid or flexbox `gap`, so layout is done
    with `display: table` / `table-cell` which DomPDF renders reliably.

    Usage:
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
--}}

@php
    $accent = $accent ?? '#10b981';
    $accentDark = $accentDark ?? '#059669';
    $accentBg = $accentBg ?? '#f0fdf4';
    $accentBorder = $accentBorder ?? '#bbf7d0';
    $accentRow = $accentRow ?? '#f8fafc';
@endphp    <style>
        @page { margin: 14mm 12mm; }
        /* NOTE: jangan reset margin/padding pada html atau pakai universal selector (*)
           di dompdf — keduanya membatalkan margin @page dan konten melebar penuh halaman */
        body, div, p, h1, h2, h4, h5, ul, ol, li, table, thead, tbody, tfoot, tr, td, th, span, strong, em {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
        }

    /* ===== KOP SURAT (letterhead) ===== */
    .kop {
        margin-bottom: 18px;
        padding-bottom: 10px;
    }
    .kop-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0;
    }
    .kop-logo-cell {
        width: 80px;
        text-align: center;
        vertical-align: middle;
        border: none;
        padding: 2px 10px 2px 0;
    }
    .kop-logo-img {
        max-width: 70px;
        max-height: 70px;
    }
    .kop-text-cell {
        text-align: center;
        vertical-align: middle;
        border: none;
        padding: 2px 0;
    }
    .kop-instansi {
        font-size: 15px;
        font-weight: bold;
        color: #111827;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.4;
    }
    .kop-alamat {
        font-size: 9px;
        color: #475569;
        margin-top: 3px;
        line-height: 1.4;
    }
    .kop-kontak {
        font-size: 9px;
        color: #64748b;
        margin-top: 2px;
    }
    .kop-garis-tebal {
        border-bottom: 2.5px solid #111827;
        margin-top: 8px;
    }
    .kop-garis-tipis {
        border-bottom: 1px solid #111827;
        margin-top: 1.5px;
    }
    .kop-judul {
        text-align: center;
        margin-top: 14px;
    }
    .kop-judul h1 {
        color: {{ $accentDark }};
        font-size: 16px;
        margin: 0 0 4px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .kop-judul .kop-subtitle {
        color: #64748b;
        font-size: 10px;
        margin-top: 3px;
    }
    .kop-judul .kop-unit {
        color: #475569;
        font-size: 10px;
        font-weight: bold;
        margin-top: 3px;
    }

    /* ===== HEADER (legacy, sebagian laporan) ===== */
    .header {
        text-align: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 3px solid {{ $accent }};
    }
    .header h1 { color: {{ $accentDark }}; font-size: 20px; margin: 0 0 4px; }
    .header h2 { color: #64748b; font-size: 14px; margin: 0; font-weight: normal; }
    .header .date { color: #94a3b8; font-size: 10px; margin-top: 6px; }
    .header .unit { color: #475569; font-size: 11px; font-weight: bold; margin-top: 4px; }

    /* ===== SUMMARY BOXES (flexbox-free, table layout) ===== */
    .summary {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
        margin: -8px 0 16px;
    }
    .summary-box {
        display: table-cell;
        vertical-align: middle;
        background: {{ $accentBg }};
        border: 1px solid {{ $accentBorder }};
        border-radius: 8px;
        padding: 12px 10px;
        text-align: center;
    }
    .summary-box .value { font-size: 17px; font-weight: bold; color: {{ $accentDark }}; }
    .summary-box .label { font-size: 10px; color: #64748b; margin-top: 3px; }
    .summary-box.income { background: #f0fdf4; border-color: #bbf7d0; }
    .summary-box.expense { background: #fef2f2; border-color: #fecaca; }
    .summary-box.danger { background: #fef2f2; border-color: #fecaca; }
    .summary-box.income .value { color: #059669; }
    .summary-box.expense .value { color: #dc2626; }
    .summary-box.danger .value { color: #dc2626; }

    /* ===== SUMMARY GRID (ringkasan: box cards) ===== */
    .summary-grid {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 10px;
        margin: -10px 0 10px;
    }
    .summary-grid .summary-box {
        display: table-cell;
        vertical-align: top;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
    }
    .summary-grid .summary-box h5 { color: {{ $accentDark }}; font-size: 14px; margin: 0 0 10px; }
    .summary-item {
        display: table;
        width: 100%;
        padding: 5px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .summary-item:last-child { border-bottom: none; }
    .summary-label { display: table-cell; color: #64748b; font-size: 11px; }
    .summary-value { display: table-cell; font-weight: bold; color: #1e293b; font-size: 11px; text-align: right; }
    .highlight { color: {{ $accentDark }}; font-size: 16px; font-weight: bold; }

    /* ===== SUMMARY CARDS (detail reports) ===== */
    .summary-cards {
        display: table;
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
        margin: -8px 0 16px;
    }
    .summary-card {
        display: table-cell;
        vertical-align: middle;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 10px;
        text-align: center;
    }
    .summary-card .value { font-size: 16px; font-weight: 700; color: {{ $accentDark }}; }
    .summary-card .label { font-size: 9px; color: #64748b; margin-top: 3px; }

    /* ===== DATA TABLES ===== */
    table { width: 100%; border-collapse: collapse; margin-top: 12px; table-layout: auto; }
    thead { display: table-header-group; }
    th {
        background: {{ $accent }};
        color: #ffffff;
        padding: 6px 6px;
        text-align: left;
        font-size: 9px;
        font-weight: bold;
        word-wrap: break-word;
    }
    td {
        padding: 5px 6px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 9px;
        vertical-align: top;
        word-wrap: break-word;
    }
    tr { page-break-inside: avoid; }
    tr:nth-child(even) { background: {{ $accentRow }}; }
    tfoot td { background: #f1f5f9; font-weight: bold; }

    /* ===== SECTIONS & INFO BOXES (detail reports) ===== */
    .section { margin-bottom: 16px; }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: {{ $accentDark }};
        margin-bottom: 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #e2e8f0;
    }
    .info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
    }
    .info-grid, .info-grid-3 {
        display: table;
        width: 100%;
    }
    .info-item { display: table-row; }
    .info-label {
        display: table-cell;
        font-weight: 600;
        color: #64748b;
        width: 130px;
        padding: 4px 10px 4px 0;
        font-size: 10px;
        vertical-align: top;
    }
    .info-value {
        display: table-cell;
        padding: 4px 0;
        font-size: 10px;
        vertical-align: top;
    }
    .info-full { display: table-row; }

    /* ===== AMOUNT BOX ===== */
    .amount-box {
        background: #f0fdf4;
        border: 2px solid {{ $accentDark }};
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        margin: 14px 0;
    }
    .amount-box.expense { background: #fef2f2; border-color: #dc2626; }
    .amount-label { font-size: 10px; color: #64748b; }
    .amount-value { font-size: 24px; font-weight: 700; margin-top: 5px; }
    .amount-value.income { color: #059669; }
    .amount-value.expense { color: #dc2626; }

    /* ===== BADGES / STATUS ===== */
    .badge, .status {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 9px;
        font-weight: bold;
        white-space: nowrap;
    }
    .badge-success, .status-aktif, .status-dikonfirmasi, .status-diterima, .status-selesai,
    .status-tersedia, .status-tinggi, .status-disetujui, .status-aman { background: #d1fae5; color: #059669; }
    .badge-warning, .status-draft, .status-dikirim, .status-dikemas, .status-ditanam,
    .status-panen, .status-proses, .status-sedang, .status-perencanaan, .status-menipis,
    .status-terbuka, .status-berlangsung { background: #fef3c7; color: #d97706; }
    .badge-danger, .status-ditolak, .status-kadaluarsa, .status-rendah, .status-rusak,
    .status-dikeluarkan { background: #fee2e2; color: #dc2626; }
    .badge-info, .status-info, .status-bebas { background: #dbeafe; color: #2563eb; }
    .badge-primary { background: #dbeafe; color: #2563eb; }
    .badge-secondary, .status-secondary, .status-nonaktif { background: #f1f5f9; color: #475569; }
    .badge-pertanian { background: #d1fae5; color: #059669; }
    .badge-peternakan { background: #fef3c7; color: #d97706; }
    .badge-perikanan { background: #dbeafe; color: #2563eb; }

    /* ===== SECTION TITLES (detail reports) ===== */
    h5, h5.section-heading {
        color: {{ $accentDark }};
        border-bottom: 2px solid {{ $accent }};
        padding-bottom: 5px;
        font-size: 14px;
        margin: 20px 0 10px;
    }

    /* ===== CALLOUT ===== */
    .callout {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 10px 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    .callout p { margin: 0; font-size: 11px; color: #334155; }

    /* ===== HELPERS ===== */
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .font-bold { font-weight: 700; }
    .low-stock { background: #fef2f2 !important; }
    .selisih-plus { color: #059669; font-weight: bold; }
    .selisih-minus { color: #dc2626; font-weight: bold; }
    .page-break { page-break-before: always; }

    /* ===== FOOTER ===== */
    .footer {
        text-align: center;
        margin-top: 30px;
        padding-top: 14px;
        border-top: 1px solid #e2e8f0;
        color: #94a3b8;
        font-size: 9px;
    }
    .report-footer {
        position: fixed;
        bottom: -10mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8px;
        color: #94a3b8;
        border-top: 1px solid #e2e8f0;
        padding-top: 4px;
    }
    .report-footer-page {
        display: block;
        margin-top: 2px;
    }
    .pagenum:before {
        content: counter(page);
    }
</style>
