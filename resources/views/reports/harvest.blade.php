<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Panen SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#f97316', 'accentDark' => '#ea580c', 'accentBg' => '#fff7ed', 'accentBorder' => '#fed7aa', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PANEN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Total Panen</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_berat'], 0) }} kg</div>
            <div class="label">Total Berat</div>
        </div>
        <div class="summary-box">
            <div class="value">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</div>
            <div class="label">Total Pendapatan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal Panen</th>
                <th>Tanaman</th>
                <th>Program</th>
                <th>Berat (kg)</th>
                <th>Kualitas</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($harvests as $i => $h)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $h->kode_panen ?? '-' }}</td>
                <td>{{ $h->tanggal_panen->format('d/m/Y') }}</td>
                <td><strong>{{ $h->farmingPlan?->cropType?->nama_tanaman ?? '-' }}</strong></td>
                <td>{{ $h->farmingPlan?->foodProgram?->nama_program ?? '-' }}</td>
                <td><strong>{{ number_format($h->total_berat_kg, 0) }}</strong></td>
                <td>{{ ucfirst($h->kualitas ?? '-') }}</td>
                <td>Rp {{ number_format($h->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
