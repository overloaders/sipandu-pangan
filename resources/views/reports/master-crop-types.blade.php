<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Jenis Tanaman</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'DATA MASTER JENIS TANAMAN'])

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
                <th>Nama Tanaman</th>
                <th>Nama Latin</th>
                <th>Kategori</th>
                <th>Masa Tanam</th>
                <th>Hasil/m²</th>
                <th>Harga Jual/kg</th>
                <th>Musim</th>
            </tr>
        </thead>
        <tbody>
            @forelse($types as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $t->kode_tanaman }}</strong></td>
                <td>{{ $t->nama_tanaman }}</td>
                <td>{{ $t->nama_latin ?? '-' }}</td>
                <td>{{ ucfirst($t->kategori ?? '-') }}</td>
                <td>{{ $t->masa_tanam_hari ? $t->masa_tanam_hari . ' hari' : '-' }}</td>
                <td>{{ $t->hasil_per_m2_kg ? number_format($t->hasil_per_m2_kg, 1) . ' kg' : '-' }}</td>
                <td>Rp {{ number_format($t->harga_jual_per_kg ?? 0) }}</td>
                <td>{{ ucfirst($t->musim_tanam ?? '-') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#999;">Belum ada jenis tanaman tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.report-footer')
</body>
</html>
