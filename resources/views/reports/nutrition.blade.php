<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nutrisi & Menu</title>
    @include('reports.partials.pdf-styles', ['accent' => '#84cc16', 'accentDark' => '#65a30d', 'accentBg' => '#f7fee7', 'accentBorder' => '#d9f99d', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN NUTRISI & MENU'])

<div class="summary">
        <div class="summary-box"><div class="value">{{ $summary['total'] }}</div><div class="label">Total Menu</div></div>
        <div class="summary-box"><div class="value" style="color:#059669;">{{ $summary['aktif'] }}</div><div class="label">Aktif</div></div>
        <div class="summary-box"><div class="value" style="color:#666;">{{ $summary['nonaktif'] }}</div><div class="label">Nonaktif</div></div>
        <div class="summary-box"><div class="value" style="color:#dc2626;">{{ number_format($summary['avg_kalori'] ?? 0, 0) }}</div><div class="label">Avg Kalori (kcal)</div></div>
        <div class="summary-box"><div class="value" style="color:#2563eb;">{{ number_format($summary['avg_protein'] ?? 0, 1) }}</div><div class="label">Avg Protein (g)</div></div>
    </div>

    <table>
        <thead>
            <tr><th>#</th><th>Kode</th><th>Nama Menu</th><th>Waktu</th><th>Tipe</th><th>Kalori</th><th>Protein</th><th>Biaya/Porsi</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($plans as $i => $n)
            <tr>
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $n->kode_rencana_gizi }}</strong></td>
                <td>{{ $n->nama_menu }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($n->waktu_makan)) }}</td>
                <td>{{ $n->tipe_porsi }}</td>
                <td>{{ $n->kalori_target_kcal ?? '-' }}</td>
                <td>{{ $n->protein_target_gr ?? '-' }}</td>
                <td>Rp {{ number_format($n->estimasi_biaya_per_porsi ?? 0, 0, ',', '.') }}</td>
                <td><span class="badge badge-{{ $n->status=='aktif'?'success':'secondary' }}">{{ ucfirst($n->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#999;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
