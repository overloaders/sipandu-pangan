<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN RINGKASAN KESELURUHAN'])

    <div class="section">
        <div class="section-title">RINGKASAN UMUM</div>
        <div class="summary-grid">
            <div class="summary-box">
                <h5>Warga Binaan</h5>
                <div class="summary-item">
                    <span class="summary-label">Total WBP</span>
                    <span class="summary-value highlight">{{ number_format($summary['wbp']['total']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">WBP Aktif</span>
                    <span class="summary-value">{{ number_format($summary['wbp']['aktif']) }}</span>
                </div>
            </div>
            <div class="summary-box">
                <h5>Program Pangan</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Program</span>
                    <span class="summary-value highlight">{{ number_format($summary['programs']['total']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Program Aktif</span>
                    <span class="summary-value">{{ number_format($summary['programs']['aktif']) }}</span>
                </div>
            </div>
            <div class="summary-box">
                <h5>Pertanian</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Rencana</span>
                    <span class="summary-value highlight">{{ number_format($summary['farming']['total']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Sedang Aktif</span>
                    <span class="summary-value">{{ number_format($summary['farming']['aktif']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">PETERNAKAN & PERIKANAN</div>
        <div class="summary-grid">
            <div class="summary-box">
                <h5>Peternakan</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Kandang</span>
                    <span class="summary-value highlight">{{ number_format($summary['livestock']['total_kandang']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Ternak</span>
                    <span class="summary-value">{{ number_format($summary['livestock']['total_ternak']) }}</span>
                </div>
            </div>
            <div class="summary-box">
                <h5>Perikanan</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Kolam</span>
                    <span class="summary-value highlight">{{ number_format($summary['fishery']['total_kolam']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Total Ikan</span>
                    <span class="summary-value">{{ number_format($summary['fishery']['total_ikan']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">INVENTARIS & KEUANGAN</div>
        <div class="summary-grid">
            <div class="summary-box">
                <h5>Inventaris</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Item</span>
                    <span class="summary-value highlight">{{ number_format($summary['inventory']['total_item']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Stok Menipis</span>
                    <span class="summary-value" style="color: #dc2626;">{{ number_format($summary['inventory']['stok_menipis']) }}</span>
                </div>
            </div>
            <div class="summary-box">
                <h5>Keuangan Bulan Ini</h5>
                <div class="summary-item">
                    <span class="summary-label">Pemasukan</span>
                    <span class="summary-value" style="color: #059669;">Rp {{ number_format($summary['financial']['pemasukan_bulan'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Pengeluaran</span>
                    <span class="summary-value" style="color: #dc2626;">Rp {{ number_format($summary['financial']['pengeluaran_bulan'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">LAPORAN HARIAN</div>
        <div class="summary-grid">
            <div class="summary-box">
                <h5>Laporan</h5>
                <div class="summary-item">
                    <span class="summary-label">Total Laporan</span>
                    <span class="summary-value highlight">{{ number_format($summary['reports']['total']) }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Menunggu Konfirmasi</span>
                    <span class="summary-value" style="color: #d97706;">{{ number_format($summary['reports']['pending']) }}</span>
                </div>
            </div>
        </div>
    </div>

        @include('reports.partials.report-footer')

</body>
</html>
