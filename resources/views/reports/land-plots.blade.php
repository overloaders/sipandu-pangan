<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Manajemen Lahan</title>
    @include('reports.partials.pdf-styles', ['accent' => '#84cc16', 'accentDark' => '#4d7c0f', 'accentBg' => '#f7fee7', 'accentBorder' => '#d9f99d', 'accentRow' => '#f7fee7'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN MANAJEMEN LAHAN'])

<div class="summary">
        <div class="summary-box"><div class="value">{{ $summary['total'] }}</div><div class="label">Total Lahan</div></div>
        <div class="summary-box"><div class="value" style="color:#059669;">{{ $summary['tersedia'] }}</div><div class="label">Tersedia</div></div>
        <div class="summary-box"><div class="value" style="color:#2563eb;">{{ $summary['ditanami'] }}</div><div class="label">Ditanami</div></div>
        <div class="summary-box"><div class="value" style="color:#d97706;">{{ $summary['istirahat'] }}</div><div class="label">Istirahat</div></div>
        <div class="summary-box"><div class="value" style="color:#65a30d;">{{ number_format($summary['total_luas'], 0) }} m²</div><div class="label">Total Luas</div></div>
    </div>

    <table>
        <thead>
            <tr><th>#</th><th>Kode</th><th>Nama Lahan</th><th>Tipe</th><th>Luas (m²)</th><th>Unit</th><th>Rencana</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($landPlots as $i => $l)
            <tr>
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $l->kode_lahan }}</strong></td>
                <td>{{ $l->nama_lahan }}</td>
                <td>{{ ucfirst($l->tipe_lahan) }}</td>
                <td>{{ number_format($l->luas_m2, 2) }}</td>
                <td>{{ $l->unit->nama_unit ?? '-' }}</td>
                <td>{{ $l->farmingPlans->count() }}</td>
                <td><span class="badge badge-{{ $l->status=='tersedia'?'success':($l->status=='ditanami'?'primary':'warning') }}">{{ ucfirst(str_replace('_', ' ', $l->status)) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#999;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
