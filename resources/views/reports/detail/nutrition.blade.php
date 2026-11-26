<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Rencana Gizi - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#84cc16', 'accentDark' => '#65a30d', 'accentBg' => '#f7fee7', 'accentBorder' => '#d9f99d', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL RENCANA GIZI'])

    <table>
        <tr><th>Kode Rencana Gizi</th><td>{{ $plan->kode_rencana_gizi }}</td></tr>
        <tr><th>Nama Menu</th><td><strong>{{ $plan->nama_menu }}</strong></td></tr>
        <tr><th>Unit</th><td>{{ $plan->unit->nama_unit ?? '-' }}</td></tr>
        <tr><th>Waktu Makan</th><td>{{ ucfirst(str_replace('_', ' ', $plan->waktu_makan)) }}</td></tr>
        <tr><th>Tipe Porsi</th><td>{{ ucfirst($plan->tipe_porsi) }}</td></tr>
        <tr><th>Berlaku Mulai</th><td>{{ $plan->berlaku_mulai ? \Carbon\Carbon::parse($plan->berlaku_mulai)->format('d/m/Y') : '-' }}</td></tr>
        <tr><th>Berlaku Sampai</th><td>{{ $plan->berlaku_selesai ? \Carbon\Carbon::parse($plan->berlaku_selesai)->format('d/m/Y') : '-' }}</td></tr>
    </table>

    <table>
        <tr><th>Kalori Target</th><td>{{ $plan->kalori_target_kcal ?? '-' }} kcal</td></tr>
        <tr><th>Protein Target</th><td>{{ $plan->protein_target_gr ?? '-' }} gr</td></tr>
        <tr><th>Karbohidrat Target</th><td>{{ $plan->karbohidrat_target_gr ?? '-' }} gr</td></tr>
        <tr><th>Lemak Target</th><td>{{ $plan->lemak_target_gr ?? '-' }} gr</td></tr>
        <tr><th>Serat Target</th><td>{{ $plan->serat_target_gr ?? '-' }} gr</td></tr>
        <tr><th>Estimasi Biaya/Porsi</th><td>Rp {{ number_format($plan->estimasi_biaya_per_porsi ?? 0, 0, ',', '.') }}</td></tr>
        <tr><th>Sumber Bahan</th><td>{{ $plan->sumber_bahan ?? '-' }}</td></tr>
        <tr><th>Status</th>
            <td>
                @if($plan->status == 'aktif')
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-secondary">{{ ucfirst($plan->status) }}</span>
                @endif
            </td>
        </tr>
    </table>

    @if($plan->deskripsi)
    <div class="callout">
        <p><strong>Deskripsi:</strong> {{ $plan->deskripsi }}</p>
    </div>
    @endif

    @if($plan->components && $plan->components->count())
    <h5 style="color:#f59e0b;margin-top:20px;">Komposisi Bahan</h5>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Bahan</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plan->components as $comp)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $comp->item->nama_item ?? '-' }}</td>
                <td>{{ $comp->jumlah }}</td>
                <td>{{ $comp->satuan }}</td>
                <td style="text-align:right;">Rp {{ number_format($comp->harga_satuan ?? 0, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp {{ number_format(($comp->jumlah ?? 0) * ($comp->harga_satuan ?? 0), 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold;background:#fffbeb;">
                <td colspan="5">Total Estimasi Biaya</td>
                <td style="text-align:right;">Rp {{ number_format($plan->components->sum(function($c) { return ($c->jumlah ?? 0) * ($c->harga_satuan ?? 0); }), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'aktif' => 'badge-success', 'nonaktif' => 'badge-danger'];
        $history = $plan->statusHistories->sortByDesc('created_at');
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
