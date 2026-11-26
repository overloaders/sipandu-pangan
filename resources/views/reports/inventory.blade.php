<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Inventaris SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#8b5cf6', 'accentDark' => '#7c3aed', 'accentBg' => '#f5f3ff', 'accentBorder' => '#ddd6fe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN INVENTARIS & STOK'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_item']) }}</div>
            <div class="label">Total Item</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['tersedia']) }}</div>
            <div class="label">Tersedia</div>
        </div>
        <div class="summary-box danger">
            <div class="value">{{ number_format($summary['stok_menipis']) }}</div>
            <div class="label">Stok Menipis</div>
        </div>
        <div class="summary-box">
            <div class="value">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</div>
            <div class="label">Total Nilai Stok</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Item</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Min</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr class="{{ $item->stok_saat_ini < $item->stok_minimum ? 'low-stock' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->kode_item }}</td>
                <td><strong>{{ $item->nama_item }}</strong></td>
                <td>{{ $item->category?->nama_kategori ?? '-' }}</td>
                <td>{{ $item->satuan }}</td>
                <td><strong>{{ number_format($item->stok_saat_ini) }}</strong></td>
                <td>{{ number_format($item->stok_minimum) }}</td>
                <td>Rp {{ number_format($item->harga_beli_rata, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
