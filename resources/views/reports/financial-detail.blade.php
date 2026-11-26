<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Keuangan - {{ $transaction->kode_transaksi_keu }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#059669', 'accentDark' => '#047857', 'accentBg' => '#ecfdf5', 'accentBorder' => '#a7f3d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL TRANSAKSI KEUANGAN'])

    {{-- NOMINAL --}}
    <div class="amount-box {{ $transaction->tipe == 'pengeluaran' ? 'expense' : '' }}">
        <div class="amount-label">{{ $transaction->tipe == 'pemasukan' ? 'PEMASUKAN' : 'PENGELUARAN' }}</div>
        <div class="amount-value {{ $transaction->tipe == 'pemasukan' ? 'income' : 'expense' }}">
            {{ $transaction->tipe == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaction->jumlah, 0, ',', '.') }}
        </div>
    </div>

    {{-- INFORMASI TRANSAKSI --}}
    <div class="section">
        <div class="section-title">Informasi Transaksi</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Transaksi</span><span class="info-value font-bold">{{ $transaction->kode_transaksi_keu }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal</span><span class="info-value">{{ $transaction->tanggal_transaksi->format('d M Y') }}</span></div>
                <div class="info-item"><span class="info-label">Tipe</span><span class="info-value"><strong>{{ ucfirst($transaction->tipe) }}</strong></span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Kategori</span><span class="info-value">{{ $transaction->category?->nama_kategori ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Sumber Dana</span><span class="info-value">{{ ucfirst(str_replace('_', ' ', $transaction->sumber_dana ?? '-')) }}</span></div>
                <div class="info-item"><span class="info-label">Metode Pembayaran</span><span class="info-value">{{ ucfirst(str_replace('_', ' ', $transaction->metode_pembayaran ?? '-')) }}</span></div>
            </div>
        </div>
    </div>

    {{-- DESKRIPSI --}}
    @if($transaction->deskripsi)
    <div class="section">
        <div class="section-title">Deskripsi</div>
        <div class="info-box">
            <p>{{ $transaction->deskripsi }}</p>
        </div>
    </div>
    @endif

    {{-- KETERANGAN --}}
    @if($transaction->keterangan)
    <div class="section">
        <div class="section-title">Keterangan</div>
        <div class="info-box">
            <p>{{ $transaction->keterangan }}</p>
        </div>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'pending_approval' => 'badge-info', 'disetujui' => 'badge-success', 'ditolak' => 'badge-danger', 'selesai' => 'badge-success'];
        $history = $transaction->statusHistories->sortByDesc('created_at');
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
