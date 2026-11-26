<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stock Opname</title>
    @include('reports.partials.pdf-styles', ['accent' => '#0ea5e9', 'accentDark' => '#0284c7', 'accentBg' => '#f0f9ff', 'accentBorder' => '#bae6fd', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN STOCK OPNAME'])

<div class="summary">
        <div class="summary-box"><div class="value">{{ $summary['total'] }}</div><div class="label">Total Opname</div></div>
        <div class="summary-box"><div class="value" style="color:#2563eb;">{{ $summary['draft'] }}</div><div class="label">Draft</div></div>
        <div class="summary-box"><div class="value" style="color:#059669;">{{ $summary['selesai'] }}</div><div class="label">Selesai</div></div>
        <div class="summary-box"><div class="value" style="color:#d97706;">{{ $summary['dibatalkan'] }}</div><div class="label">Dibatalkan</div></div>
        <div class="summary-box"><div class="value" style="color:{{ $summary['total_selisih'] != 0 ? '#dc2626' : '#059669' }};">Rp {{ number_format($summary['total_selisih'], 0, ',', '.') }}</div><div class="label">Total Selisih</div></div>
    </div>

    <table>
        <thead>
            <tr><th>#</th><th>Kode</th><th>Tanggal</th><th>Jenis</th><th>Item Diperiksa</th><th>Selisih Item</th><th>Selisih Nilai</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse($opnames as $i => $o)
            <tr>
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $o->kode_opname }}</strong></td>
                <td>{{ $o->tanggal_opname->format('d/m/Y') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $o->jenis_opname)) }}</td>
                <td>{{ $o->total_item_checked }}</td>
                <td>{{ $o->item_selisih }}</td>
                <td class="{{ $o->total_selisih_nilai > 0 ? 'selisih-plus' : ($o->total_selisih_nilai < 0 ? 'selisih-minus' : '') }}">Rp {{ number_format($o->total_selisih_nilai, 0, ',', '.') }}</td>
                <td><span class="badge badge-{{ $o->status=='selesai'?'success':($o->status=='dibatalkan'?'danger':'info') }}">{{ ucfirst(str_replace('_', ' ', $o->status)) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#999;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
