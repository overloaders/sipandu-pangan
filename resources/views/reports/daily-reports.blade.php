<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#06b6d4', 'accentDark' => '#0891b2', 'accentBg' => '#ecfeff', 'accentBorder' => '#a5f3fc', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN HARIAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Total Laporan</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['draft']) }}</div>
            <div class="label">Draft</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['dikirim']) }}</div>
            <div class="label">Dikirim</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['dikonfirmasi']) }}</div>
            <div class="label">Dikonfirmasi</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Unit</th>
                <th>Pelapor</th>
                <th>Ringkasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $i => $r)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $r->kode_laporan }}</td>
                <td>{{ $r->tanggal_laporan->format('d/m/Y') }}</td>
                <td>{{ $r->unit?->nama_unit ?? '-' }}</td>
                <td>{{ $r->reporter?->name ?? '-' }}</td>
                <td>{{ Str::limit($r->ringkasan_kegiatan ?? '-', 50) }}</td>
                <td>{{ ucfirst($r->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
