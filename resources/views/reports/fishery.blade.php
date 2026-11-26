<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perikanan SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#2563eb', 'accentDark' => '#1d4ed8', 'accentBg' => '#eff6ff', 'accentBorder' => '#bfdbfe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PERIKANAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_kolam']) }}</div>
            <div class="label">Total Kolam</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['kolam_aktif']) }}</div>
            <div class="label">Kolam Aktif</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_ikan']) }}</div>
            <div class="label">Total Ikan</div>
        </div>
    </div>

    @foreach($ponds as $pond)
    <div class="section-title">{{ $pond->kode_kolam }} - {{ $pond->nama_kolam }} ({{ ucfirst($pond->tipe_kolam) }})</div>
    <table>
        <thead>
            <tr>
                <th>Kode Stok</th>
                <th>Jenis Ikan</th>
                <th>Jumlah</th>
                <th>Berat Rata-rata</th>
                <th>Tgl Tebar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pond->fishStocks as $fs)
            <tr>
                <td>{{ $fs->kode_stok_ikan }}</td>
                <td>{{ $fs->jenis_ikan }}</td>
                <td><strong>{{ number_format($fs->jumlah) }}</strong></td>
                <td>{{ number_format($fs->berat_rata_gram ?? 0, 1) }} gram</td>
                <td>{{ $fs->tanggal_tebar ? $fs->tanggal_tebar->format('d/m/Y') : '-' }}</td>
                <td>{{ ucfirst($fs->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8;">Belum ada stok ikan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endforeach

        @include('reports.partials.report-footer')

</body>
</html>
