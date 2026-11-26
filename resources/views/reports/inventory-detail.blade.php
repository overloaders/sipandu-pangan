<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Inventaris - {{ $item->kode_item }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#8b5cf6', 'accentDark' => '#7c3aed', 'accentBg' => '#f5f3ff', 'accentBorder' => '#ddd6fe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL ITEM INVENTARIS'])

    {{-- RINGKASAN STOK --}}
    @php
        $stokStatus = ($item->stok ?? 0) <= ($item->stok_minimum ?? 0) ? 'low' : 'aktif';
    @endphp
    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ number_format($item->stok ?? 0, 2) }}</div>
            <div class="label">Stok Saat Ini ({{ $item->satuan }})</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ number_format($item->stok_minimum ?? 0, 2) }}</div>
            <div class="label">Stok Minimum</div>
        </div>
        <div class="summary-card">
            <div class="value" style="color: {{ $stokStatus == 'low' ? '#dc2626' : '#059669' }};">
                {{ $stokStatus == 'low' ? '⚠️ Menipis' : '✅ Aman' }}
            </div>
            <div class="label">Status Stok</div>
        </div>
    </div>

    {{-- INFORMASI ITEM --}}
    <div class="section">
        <div class="section-title">Informasi Item</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Item</span><span class="info-value font-bold">{{ $item->kode_item }}</span></div>
                <div class="info-item"><span class="info-label">Nama Item</span><span class="info-value font-bold">{{ $item->nama_item }}</span></div>
                <div class="info-item"><span class="info-label">Kategori</span><span class="info-value">{{ $item->category?->nama_kategori ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Satuan</span><span class="info-value">{{ $item->satuan }}</span></div>
                <div class="info-item"><span class="info-label">Harga Beli</span><span class="info-value">Rp {{ number_format($item->harga_beli ?? 0, 0, ',', '.') }}</span></div>
                <div class="info-item"><span class="info-label">Harga Jual</span><span class="info-value">Rp {{ number_format($item->harga_jual ?? 0, 0, ',', '.') }}</span></div>
                <div class="info-item"><span class="info-label">Lokasi Penyimpanan</span><span class="info-value">{{ $item->lokasi ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Supplier</span><span class="info-value">{{ $item->supplier ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Deskripsi</span><span class="info-value">{{ $item->deskripsi ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Keterangan</span><span class="info-value">{{ $item->keterangan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- NILAI INVENTARIS --}}
    <div class="section">
        <div class="section-title">Nilai Inventaris</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Total Nilai Stok</span><span class="info-value font-bold" style="color:#059669;">Rp {{ number_format(($item->stok ?? 0) * ($item->harga_beli ?? 0), 0, ',', '.') }}</span></div>
                <div class="info-item"><span class="info-label">Potensi Pendapatan</span><span class="info-value font-bold" style="color:#2563eb;">Rp {{ number_format(($item->stok ?? 0) * ($item->harga_jual ?? 0), 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['tersedia' => 'badge-success', 'stok_habis' => 'badge-danger', 'kadaluarsa' => 'badge-danger', 'rusak' => 'badge-danger', 'nonaktif' => 'badge-secondary'];
        $history = $item->statusHistories->sortByDesc('created_at');
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
