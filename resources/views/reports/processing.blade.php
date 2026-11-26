<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengolahan Hasil - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#f97316', 'accentDark' => '#ea580c', 'accentBg' => '#fff7ed', 'accentBorder' => '#fed7aa'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PENGOLAHAN HASIL'])

<div class="summary">
        <div class="summary-box">
            <h5>{{ $batches->count() }}</h5>
            <p>Total Batch</p>
        </div>
        <div class="summary-box">
            <h5>{{ $batches->where('status', 'proses')->count() }}</h5>
            <p>Proses</p>
        </div>
        <div class="summary-box">
            <h5>{{ $batches->where('status', 'selesai')->count() }}</h5>
            <p>Selesai</p>
        </div>
        <div class="summary-box">
            <h5>Rp {{ number_format($batches->sum('biaya_produksi'), 0, ',', '.') }}</h5>
            <p>Total Biaya</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Jenis Pengolahan</th>
                <th>Tanggal Produksi</th>
                <th>Jumlah</th>
                <th>Biaya</th>
                <th>Harga Jual</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batches as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $b->kode_batch }}</td>
                <td>{{ $b->nama_produk }}</td>
                <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $b->jenis_pengolahan)) }}</span></td>
                <td>{{ $b->tanggal_produksi ? \Carbon\Carbon::parse($b->tanggal_produksi)->format('d/m/Y') : '-' }}</td>
                <td>{{ $b->jumlah_produksi }} {{ $b->satuan_output }}</td>
                <td>Rp {{ number_format($b->biaya_produksi ?? 0, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($b->harga_jual_satuan ?? 0, 0, ',', '.') }}</td>
                <td>
                    @if($b->status == 'selesai')
                        <span class="badge badge-success">Selesai</span>
                    @elseif($b->status == 'proses')
                        <span class="badge badge-warning">Diproses</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($b->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
