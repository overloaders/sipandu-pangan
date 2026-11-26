<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Program Pangan SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PROGRAM KETAHANAN PANGAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total']) }}</div>
            <div class="label">Total Program</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['aktif']) }}</div>
            <div class="label">Aktif</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['perencanaan']) }}</div>
            <div class="label">Perencanaan</div>
        </div>
        <div class="summary-box">
            <div class="value">Rp {{ number_format($summary['total_anggaran'], 0, ',', '.') }}</div>
            <div class="label">Total Anggaran</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Program</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Tgl Mulai</th>
                <th>Anggaran</th>
                <th>Peserta</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->kode_program }}</td>
                <td><strong>{{ $p->nama_program }}</strong></td>
                <td>{{ ucfirst($p->jenis_program) }}</td>
                <td>
                    @if($p->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @elseif($p->status == 'perencanaan')
                        <span class="badge badge-warning">Perencanaan</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($p->status) }}</span>
                    @endif
                </td>
                <td>{{ $p->tanggal_mulai->format('d/m/Y') }}</td>
                <td>Rp {{ number_format($p->anggaran, 0, ',', '.') }}</td>
                <td>{{ $p->participants->count() }}</td>
                <td>{{ $p->unit?->nama_unit ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
