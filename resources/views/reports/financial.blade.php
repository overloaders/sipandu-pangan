<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#059669', 'accentDark' => '#047857', 'accentBg' => '#ecfdf5', 'accentBorder' => '#a7f3d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN KEUANGAN'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_transaksi']) }}</div>
            <div class="label">Total Transaksi</div>
        </div>
        <div class="summary-box income">
            <div class="value">Rp {{ number_format($summary['total_pemasukan'], 0, ',', '.') }}</div>
            <div class="label">Total Pemasukan</div>
        </div>
        <div class="summary-box expense">
            <div class="value">Rp {{ number_format($summary['total_pengeluaran'], 0, ',', '.') }}</div>
            <div class="label">Total Pengeluaran</div>
        </div>
        <div class="summary-box">
            <div class="value {{ $summary['selisih'] >= 0 ? 'income' : 'expense' }}">Rp {{ number_format($summary['selisih'], 0, ',', '.') }}</div>
            <div class="label">Selisih</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Deskripsi</th>
                <th>Sumber Dana</th>
                <th>Metode</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->kode_transaksi_keu }}</td>
                <td>{{ $t->tanggal_transaksi->format('d/m/Y') }}</td>
                <td>{{ ucfirst($t->tipe) }}</td>
                <td>{{ $t->deskripsi }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $t->sumber_dana)) }}</td>
                <td>{{ ucfirst($t->metode_pembayaran) }}</td>
                <td class="{{ $t->tipe == 'pemasukan' ? 'income' : 'expense' }}">
                    {{ $t->tipe == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
