<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Kolam - {{ $fishPond->kode_kolam }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#2563eb', 'accentDark' => '#1d4ed8', 'accentBg' => '#eff6ff', 'accentBorder' => '#bfdbfe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL KOLAM PERIKANAN'])

    <h5>Informasi Kolam</h5>
    <table>
        <tr><td width="180"><strong>Kode Kolam</strong></td><td>{{ $fishPond->kode_kolam }}</td></tr>
        <tr><td><strong>Nama Kolam</strong></td><td>{{ $fishPond->nama_kolam }}</td></tr>
        <tr><td><strong>Unit</strong></td><td>{{ $fishPond->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td><strong>Tipe Kolam</strong></td><td>{{ ucfirst(str_replace('_',' ',$fishPond->tipe_kolam)) }}</td></tr>
        <tr><td><strong>Luas</strong></td><td>{{ number_format($fishPond->luas_m2) }} m²</td></tr>
        <tr><td><strong>Kedalaman</strong></td><td>{{ $fishPond->kedalaman_m ?? '-' }} m</td></tr>
        <tr><td><strong>Volume</strong></td><td>{{ $fishPond->volume_m3 ?? '-' }} m³</td></tr>
        <tr><td><strong>Sumber Air</strong></td><td>{{ ucfirst($fishPond->sumber_air) }}</td></tr>
        <tr><td><strong>Status</strong></td><td>{{ ucfirst($fishPond->status) }}</td></tr>
    </table>

    <h5>Stok Ikan ({{ $fishPond->fishStocks->count() }})</h5>
    @if($fishPond->fishStocks->count())
    <table>
        <thead><tr><th>No</th><th>Kode</th><th>Jenis</th><th>Jumlah</th><th>Ukuran</th><th>Berat</th><th>Tgl Tebur</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($fishPond->fishStocks as $fs)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $fs->kode_stok_ikan }}</td>
                <td>{{ $fs->jenis_ikan }}</td>
                <td>{{ number_format($fs->jumlah) }}</td>
                <td>{{ $fs->ukuran_rata_cm ?? '-' }} cm</td>
                <td>{{ number_format($fs->berat_rata_gram ?? 0, 1) }} g</td>
                <td>{{ $fs->tanggal_tebur ? \Carbon\Carbon::parse($fs->tanggal_tebur)->format('d/m/Y') : '-' }}</td>
                <td>{{ ucfirst($fs->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada stok ikan.</p> @endif

    <h5>Riwayat Kualitas Air ({{ $fishPond->waterQualityRecords->count() }})</h5>
    @if($fishPond->waterQualityRecords->count())
    <table>
        <thead><tr><th>No</th><th>Tanggal</th><th>pH</th><th>Suhu</th><th>DO</th><th>Amonia</th><th>Kekeruhan</th><th>Kondisi</th></tr></thead>
        <tbody>
            @foreach($fishPond->waterQualityRecords as $wq)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $wq->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                <td>{{ $wq->ph ?? '-' }}</td>
                <td>{{ $wq->suhu_celsius ?? '-' }}°C</td>
                <td>{{ $wq->dissolved_oxygen_mgl ?? '-' }}</td>
                <td>{{ $wq->amonia_mgl ?? '-' }}</td>
                <td>{{ $wq->kekeruhan_ntu ?? '-' }} NTU</td>
                <td>{{ ucfirst($wq->kondisi_air) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada pemeriksaan kualitas air.</p> @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['aktif' => 'badge-success', 'kering' => 'badge-warning', 'renovasi' => 'badge-secondary', 'nonaktif' => 'badge-secondary'];
        $history = $fishPond->statusHistories->sortByDesc('created_at');
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
