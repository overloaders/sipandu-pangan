<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Jenis Ternak</title>
    @include('reports.partials.pdf-styles', ['accent' => '#8b5cf6', 'accentDark' => '#7c3aed', 'accentBg' => '#f5f3ff', 'accentBorder' => '#ddd6fe', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'DATA MASTER JENIS TERNAK'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($types->count()) }}</div>
            <div class="label">Total Jenis</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $types->pluck('kategori')->unique()->count() }}</div>
            <div class="label">Kategori</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Jenis</th>
                <th>Kategori</th>
                <th>Berat Rata-rata</th>
                <th>Harga Beli</th>
                <th>Harga Jual/kg</th>
                <th>Masa Pemeliharaan</th>
                <th>Pakan Utama</th>
            </tr>
        </thead>
        <tbody>
            @forelse($types as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $t->kode_jenis }}</strong></td>
                <td>{{ $t->nama_jenis }}</td>
                <td>{{ ucfirst($t->kategori ?? '-') }}</td>
                <td>{{ $t->berat_rata_kg ? number_format($t->berat_rata_kg, 1) . ' kg' : '-' }}</td>
                <td>Rp {{ number_format($t->harga_beli_per_ekor ?? 0) }}</td>
                <td>Rp {{ number_format($t->harga_jual_per_kg ?? 0) }}</td>
                <td>{{ $t->masa_pemeliharaan_hari ? $t->masa_pemeliharaan_hari . ' hari' : '-' }}</td>
                <td>{{ ucfirst($t->pakan_utama ?? '-') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#999;">Belum ada jenis ternak tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.report-footer')
</body>
</html>
