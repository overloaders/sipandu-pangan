<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Distribusi - {{ $distribution->kode_distribusi }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#ec4899', 'accentDark' => '#db2777', 'accentBg' => '#fdf2f8', 'accentBorder' => '#fbcfe8', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL DISTRIBUSI'])

    {{-- TOTAL NILAI --}}
    <div class="amount-box">
        <div class="amount-label">TOTAL NILAI DISTRIBUSI</div>
        <div class="amount-value">Rp {{ number_format($distribution->total_nilai ?? 0, 0, ',', '.') }}</div>
    </div>

    {{-- INFORMASI DISTRIBUSI --}}
    <div class="section">
        <div class="section-title">Informasi Distribusi</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Distribusi</span><span class="info-value font-bold">{{ $distribution->kode_distribusi }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal</span><span class="info-value">{{ $distribution->tanggal_distribusi->format('d M Y') }}</span></div>
                <div class="info-item"><span class="info-label">Program Pangan</span><span class="info-value">{{ $distribution->foodProgram?->nama_program ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $distribution->status }}">{{ ucfirst($distribution->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Penerima</span><span class="info-value">{{ $distribution->nama_penerima ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Alamat Tujuan</span><span class="info-value">{{ $distribution->alamat_tujuan ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Dibuat oleh</span><span class="info-value">{{ $distribution->creator?->name ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Metode Pengiriman</span><span class="info-value">{{ ucfirst($distribution->metode_pengiriman ?? '-') }}</span></div>
                <div class="info-item info-full"><span class="info-label">Keterangan</span><span class="info-value">{{ $distribution->keterangan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- DETAL ITEM --}}
    @if($distribution->items->count() > 0)
    <div class="section">
        <div class="section-title">Detail Item Distribusi ({{ $distribution->items->count() }} item)</div>
        <table>
            <thead>
                <tr>
                    <th width="25">No</th>
                    <th>Nama Item</th>
                    <th>Satuan</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($distribution->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $item->item?->nama_item ?? '-' }}</td>
                    <td>{{ $item->item?->satuan ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->jumlah ?? 0, 2) }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f1f5f9;font-weight:700;">
                    <td colspan="3">TOTAL</td>
                    <td class="text-right">{{ number_format($distribution->items->sum('jumlah'), 2) }}</td>
                    <td></td>
                    <td class="text-right">Rp {{ number_format($distribution->total_nilai ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'dikirim' => 'badge-info', 'diterima' => 'badge-success', 'dibatalkan' => 'badge-danger'];
        $history = $distribution->statusHistories->sortByDesc('created_at');
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
