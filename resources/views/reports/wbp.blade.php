<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan WBP SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#3b82f6', 'accentDark' => '#2563eb', 'accentBg' => '#eff6ff', 'accentBorder' => '#bfdbfe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DATA WARGA BINAAN PEMASYARAKATAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Total WBP</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['aktif']) }}</div>
            <div class="label">Aktif</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['dikeluarkan']) }}</div>
            <div class="label">Dikeluarkan</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['pindah']) }}</div>
            <div class="label">Pindah</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Induk</th>
                <th>Nama Lengkap</th>
                <th>L/P</th>
                <th>Usia</th>
                <th>Unit</th>
                <th>Blok/Kamar</th>
                <th>Status</th>
                <th>Tgl Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wbp as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->nomor_induk }}</td>
                <td><strong>{{ $item->nama_lengkap }}</strong></td>
                <td>{{ $item->jenis_kelamin }}</td>
                <td>{{ $item->tanggal_lahir->age }} th</td>
                <td>{{ $item->unit?->nama_unit ?? '-' }}</td>
                <td>{{ $item->block?->kode_blok ?? '-' }}/{{ $item->room?->kode_kamar ?? '-' }}</td>
                <td>
                    @if($item->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @elseif($item->status == 'dikeluarkan')
                        <span class="badge badge-danger">Dikeluarkan</span>
                    @elseif($item->status == 'pindah')
                        <span class="badge badge-warning">Pindah</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($item->status) }}</span>
                    @endif
                </td>
                <td>{{ $item->tanggal_masuk->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
