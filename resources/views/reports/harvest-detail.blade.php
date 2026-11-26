<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Panen - {{ $harvest->kode_panen }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#f97316', 'accentDark' => '#ea580c', 'accentBg' => '#fff7ed', 'accentBorder' => '#fed7aa', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL HASIL PANEN'])

    {{-- RINGKASAN --}}
    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ number_format($harvest->total_berat_kg, 2) }}</div>
            <div class="label">Total Berat (kg)</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ ucfirst($harvest->kualitas) }}</div>
            <div class="label">Kualitas</div>
        </div>
        <div class="summary-card">
            <div class="value">Rp {{ number_format($harvest->harga_jual_per_kg ?? 0, 0, ',', '.') }}</div>
            <div class="label">Harga/kg</div>
        </div>
        <div class="summary-card">
            <div class="value" style="color:#059669;">Rp {{ number_format($harvest->total_pendapatan, 0, ',', '.') }}</div>
            <div class="label">Total Pendapatan</div>
        </div>
    </div>

    {{-- INFORMASI PANEN --}}
    <div class="section">
        <div class="section-title">Informasi Panen</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Panen</span><span class="info-value font-bold">{{ $harvest->kode_panen }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Panen</span><span class="info-value">{{ $harvest->tanggal_panen->format('d M Y') }}</span></div>
                <div class="info-item"><span class="info-label">Rencana Kerja</span><span class="info-value">{{ $harvest->farmingPlan?->kode_rencana ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tanaman</span><span class="info-value">{{ $harvest->farmingPlan?->cropType?->nama_tanaman ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Lahan</span><span class="info-value">{{ $harvest->farmingPlan?->landPlot?->nama_lahan ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Metode Budi Daya</span><span class="info-value">{{ ucfirst($harvest->farmingPlan?->metode_budi_daya ?? '-') }}</span></div>
                <div class="info-item info-full"><span class="info-label">Keterangan</span><span class="info-value">{{ $harvest->keterangan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- DETAL PANEN --}}
    @if(isset($harvest->details) && $harvest->details->count() > 0)
    <div class="section">
        <div class="section-title">Detail Hasil Panen</div>
        <table>
            <thead><tr><th>No</th><th>Item</th><th>Jumlah</th><th>Satuan</th><th>Harga</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($harvest->details as $idx => $d)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $d->nama_item ?? '-' }}</td>
                    <td>{{ number_format($d->jumlah ?? 0, 2) }}</td>
                    <td>{{ $d->satuan ?? 'kg' }}</td>
                    <td>Rp {{ number_format($d->harga ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->total ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

        @include('reports.partials.report-footer')

</body>
</html>
