<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Master Unit</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'DATA MASTER UNIT'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($units->count()) }}</div>
            <div class="label">Total Unit</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ $units->where('status', 'aktif')->count() }}</div>
            <div class="label">Aktif</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($units->sum('kapasitas_wbp')) }}</div>
            <div class="label">Kapasitas WBP</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Unit</th>
                <th>Tipe</th>
                <th>Provinsi</th>
                <th>Kota/Kab</th>
                <th>Telepon</th>
                <th>Kapasitas WBP</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($units as $i => $unit)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $unit->kode_unit }}</strong></td>
                <td>{{ $unit->nama_unit }}</td>
                <td>{{ ucfirst($unit->tipe_unit ?? '-') }}</td>
                <td>{{ $unit->provinsi ?? '-' }}</td>
                <td>{{ $unit->kota_kab ?? '-' }}</td>
                <td>{{ $unit->telepon ?? '-' }}</td>
                <td>{{ $unit->kapasitas_wbp ?? '-' }}</td>
                <td>
                    @if($unit->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($unit->status ?? '-') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#999;">Belum ada unit tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('reports.partials.report-footer')
</body>
</html>
