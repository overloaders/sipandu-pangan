<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pertanian SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#f59e0b', 'accentDark' => '#d97706', 'accentBg' => '#fffbeb', 'accentBorder' => '#fde68a', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PERTANIAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Total Rencana</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['direncanakan']) }}</div>
            <div class="label">Direncanakan</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['proses_tanam']) }}</div>
            <div class="label">Proses Tanam</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['tumbuh']) }}</div>
            <div class="label">Tumbuh</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['siap_panen']) }}</div>
            <div class="label">Siap Panen</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanaman</th>
                <th>Lahan</th>
                <th>Jumlah</th>
                <th>Luas</th>
                <th>Metode</th>
                <th>Tgl Tanam</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->kode_rencana }}</td>
                <td><strong>{{ $p->cropType?->nama_tanaman ?? '-' }}</strong></td>
                <td>{{ $p->landPlot?->nama_lahan ?? '-' }}</td>
                <td>{{ number_format($p->jumlah_tanaman) }}</td>
                <td>{{ number_format($p->luas_tanam_m2, 0) }} m²</td>
                <td>{{ ucfirst($p->metode_budi_daya) }}</td>
                <td>{{ $p->tanggal_tanam_rencana->format('d/m/Y') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $p->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
