<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Batch Pengolahan - {{ $batch->kode_batch }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#db2777', 'accentDark' => '#be185d', 'accentBg' => '#fdf2f8', 'accentBorder' => '#fbcfe8', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL BATCH PENGOLAHAN'])

    <h5>Informasi Batch</h5>
    <table>
        <tr><td width="180"><strong>Kode Batch</strong></td><td>{{ $batch->kode_batch }}</td></tr>
        <tr><td><strong>Nama Produk</strong></td><td>{{ $batch->nama_produk }}</td></tr>
        <tr><td><strong>Unit</strong></td><td>{{ $batch->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td><strong>Jenis Pengolahan</strong></td><td>{{ ucfirst(str_replace('_',' ',$batch->jenis_pengolahan)) }}</td></tr>
        <tr><td><strong>Tanggal Produksi</strong></td><td>{{ $batch->tanggal_produksi ? \Carbon\Carbon::parse($batch->tanggal_produksi)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Tanggal Kadaluarsa</strong></td><td>{{ $batch->tanggal_kadaluarsa ? \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Jumlah Produksi</strong></td><td>{{ $batch->jumlah_produksi }} {{ $batch->satuan_output }}</td></tr>
        <tr><td><strong>Biaya Produksi</strong></td><td>Rp {{ number_format($batch->biaya_produksi ?? 0) }}</td></tr>
        <tr><td><strong>Harga Jual/Satuan</strong></td><td>Rp {{ number_format($batch->harga_jual_satuan ?? 0) }}</td></tr>
        <tr><td><strong>PIC (WBP)</strong></td><td>{{ $batch->picWbp?->nama_lengkap ?? '-' }}</td></tr>
        <tr><td><strong>Status</strong></td><td>{{ ucfirst($batch->status) }}</td></tr>
    </table>

    @if($batch->keterangan)
    <h5>Keterangan</h5>
    <p>{{ $batch->keterangan }}</p>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'proses' => 'badge-info', 'selesai' => 'badge-success', 'gagal' => 'badge-danger'];
        $history = $batch->statusHistories->sortByDesc('created_at');
    @endphp
    @if($history->isNotEmpty())
    <div class="section">
        <div class="section-title">Riwayat Perubahan Status</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%">Waktu</th>
                    <th style="width: 40%">Perubahan</th>
                    <th style="width: 30%">Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $h)
                <tr>
                    <td>{{ $h->created_at->format('d M Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge {{ $statusBadge[$h->status_lama] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $h->status_lama)) }}</span>
                        @else
                            <span class="badge badge-secondary">-</span>
                        @endif
                        &nbsp;&rarr;&nbsp;
                        <span class="badge {{ $statusBadge[$h->status_baru] ?? 'badge-secondary' }}">{{ ucfirst(str_replace('_', ' ', $h->status_baru)) }}</span>
                    </td>
                    <td>{{ $h->changedBy?->name ?? 'Sistem' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
        @include('reports.partials.report-footer')

</body>
</html>
