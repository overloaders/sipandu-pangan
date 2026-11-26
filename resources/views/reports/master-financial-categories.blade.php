<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Kategori Keuangan</title>
    @include('reports.partials.pdf-styles', ['accent' => '#059669', 'accentDark' => '#047857', 'accentBg' => '#ecfdf5', 'accentBorder' => '#a7f3d0', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'DATA MASTER KATEGORI KEUANGAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($categories->count()) }}</div>
            <div class="label">Total Kategori</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $categories->where('tipe', 'pemasukan')->count() }}</div>
            <div class="label">Pemasukan</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $categories->where('tipe', 'pengeluaran')->count() }}</div>
            <div class="label">Pengeluaran</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kategori</th>
                <th>Tipe</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $i => $c)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $c->kode_kategori_keu }}</strong></td>
                <td>{{ $c->nama_kategori }}</td>
                <td>{{ ucfirst($c->tipe ?? '-') }}</td>
                <td>{{ $c->keterangan ?? '-' }}</td>
                <td>
                    @if($c->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Nonaktif</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;color:#999;">Belum ada kategori keuangan tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.report-footer')
</body>
</html>
