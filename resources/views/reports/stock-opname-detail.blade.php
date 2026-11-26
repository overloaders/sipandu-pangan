<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Stock Opname - {{ $opname->kode_opname }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#0ea5e9', 'accentDark' => '#0284c7', 'accentBg' => '#f0f9ff', 'accentBorder' => '#bae6fd', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL STOCK OPNAME'])

    <div class="info-box">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Kode Opname</span>
                <span class="info-value font-bold">{{ $opname->kode_opname }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Unit</span>
                <span class="info-value">{{ $unitName }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Opname</span>
                <span class="info-value">{{ $opname->tanggal_opname->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Jenis Opname</span>
                <span class="info-value">{{ ucfirst($opname->jenis_opname ?? '-') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="status status-{{ $opname->status }}">{{ ucfirst($opname->status) }}</span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Petugas</span>
                <span class="info-value">{{ $opname->petugasWbp?->nama_lengkap ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Diverifikasi oleh</span>
                <span class="info-value">{{ $opname->verifikator?->name ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Keterangan</span>
                <span class="info-value">{{ $opname->keterangan ?? '-' }}</span>
            </div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ $opname->items->count() }}</div>
            <div class="label">Total Item Diperiksa</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $opname->items->where('selisih_qty', '!=', 0)->count() }}</div>
            <div class="label">Item Selisih</div>
        </div>
        <div class="summary-card">
            <div class="value">Rp {{ number_format($opname->total_selisih_nilai ?? 0, 0, ',', '.') }}</div>
            <div class="label">Total Selisih Nilai</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $opname->items->sum('qty_fisik') }}</div>
            <div class="label">Total Qty Fisik</div>
        </div>
    </div>

    <h5 style="margin-bottom: 10px; color: #059669;">Detail Item Stock Opname</h5>
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Kode Item</th>
                <th>Nama Item</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Qty Sistem</th>
                <th class="text-right">Qty Fisik</th>
                <th class="text-right">Selisih Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Selisih Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opname->items as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $item->item?->kode_item ?? '-' }}</td>
                <td>{{ $item->item?->nama_item ?? '-' }}</td>
                <td class="text-center">{{ $item->item?->satuan ?? '-' }}</td>
                <td class="text-right">{{ number_format($item->qty_sistem ?? 0, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($item->qty_fisik ?? 0, 2) }}</td>
                <td class="text-right" style="color: {{ ($item->selisih_qty ?? 0) != 0 ? '#dc2626' : '#059669' }};">
                    {{ number_format($item->selisih_qty ?? 0, 2) }}
                </td>
                <td class="text-right">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                <td class="text-right" style="color: {{ ($item->selisih_nilai ?? 0) != 0 ? '#dc2626' : '#059669' }};">
                    Rp {{ number_format($item->selisih_nilai ?? 0, 0, ',', '.') }}
                </td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8;">
                    Tidak ada data item
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($opname->items->count() > 0)
        <tfoot>
            <tr style="background: #f1f5f9; font-weight: 700;">
                <td colspan="4">TOTAL</td>
                <td class="text-right">{{ number_format($opname->items->sum('qty_sistem'), 2) }}</td>
                <td class="text-right">{{ number_format($opname->items->sum('qty_fisik'), 2) }}</td>
                <td class="text-right" style="color: #dc2626;">{{ number_format($opname->items->sum('selisih_qty'), 2) }}</td>
                <td></td>
                <td class="text-right" style="color: #dc2626;">Rp {{ number_format($opname->total_selisih_nilai ?? 0, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'proses' => 'badge-info', 'selesai' => 'badge-success', 'dibatalkan' => 'badge-danger'];
        $history = $opname->statusHistories->sortByDesc('created_at');
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
