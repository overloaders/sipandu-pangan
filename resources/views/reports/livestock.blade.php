<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peternakan SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#d97706', 'accentDark' => '#b45309', 'accentBg' => '#fffbeb', 'accentBorder' => '#fde68a', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PETERNAKAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_kandang']) }}</div>
            <div class="label">Total Kandang</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['kandang_aktif']) }}</div>
            <div class="label">Kandang Aktif</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_ternak']) }}</div>
            <div class="label">Total Ternak</div>
        </div>
    </div>

    @foreach($barns as $barn)
    <div class="section-title">{{ $barn->kode_kandang }} - {{ $barn->nama_kandang }} ({{ ucfirst($barn->jenis_ternak) }})</div>
    <table>
        <thead>
            <tr>
                <th>Kode Ternak</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Berat Rata-rata</th>
                <th>Tgl Masuk</th>
                <th>Asal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barn->inventories as $inv)
            <tr>
                <td>{{ $inv->kode_ternak }}</td>
                <td>{{ $inv->livestockType?->nama_jenis ?? '-' }}</td>
                <td><strong>{{ $inv->jumlah }}</strong></td>
                <td>{{ number_format($inv->berat_rata_kg, 1) }} kg</td>
                <td>{{ $inv->tanggal_masuk->format('d/m/Y') }}</td>
                <td>{{ ucfirst($inv->asal) }}</td>
                <td>{{ ucfirst($inv->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8;">Belum ada ternak</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @endforeach

        @include('reports.partials.report-footer')

</body>
</html>
