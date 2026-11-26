<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Kategori Inventaris</title>
    @include('reports.partials.pdf-styles', ['accent' => '#0ea5e9', 'accentDark' => '#0284c7', 'accentBg' => '#f0f9ff', 'accentBorder' => '#bae6fd', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'DATA MASTER KATEGORI INVENTARIS'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($categories->count()) }}</div>
            <div class="label">Total Kategori</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $categories->pluck('tipe')->unique()->count() }}</div>
            <div class="label">Tipe</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Tipe</th>
                <th>Satuan Standar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $c->kode_kategori }}</strong></td>
                <td>{{ $c->nama_kategori }}</td>
                <td>{{ ucfirst($c->tipe ?? '-') }}</td>
                <td>{{ $c->satuan_standar ?? '-' }}</td>
                <td>{{ $c->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#999;">Belum ada kategori inventaris tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.report-footer')
</body>
</html>
