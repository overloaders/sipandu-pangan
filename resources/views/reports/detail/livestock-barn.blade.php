<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Kandang - {{ $livestockBarn->kode_kandang }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#d97706', 'accentDark' => '#b45309', 'accentBg' => '#fffbeb', 'accentBorder' => '#fde68a', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL KANDANG TERNAK'])

    <h5>Informasi Kandang</h5>
    <table>
        <tr><td width="180"><strong>Kode Kandang</strong></td><td>{{ $livestockBarn->kode_kandang }}</td></tr>
        <tr><td><strong>Nama Kandang</strong></td><td>{{ $livestockBarn->nama_kandang }}</td></tr>
        <tr><td><strong>Unit</strong></td><td>{{ $livestockBarn->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td><strong>Jenis Ternak</strong></td><td>{{ ucfirst($livestockBarn->jenis_ternak) }}</td></tr>
        <tr><td><strong>Tipe Kandang</strong></td><td>{{ ucfirst(str_replace('_',' ',$livestockBarn->tipe_kandang)) }}</td></tr>
        <tr><td><strong>Kapasitas</strong></td><td>{{ $livestockBarn->terisi ?? 0 }} / {{ $livestockBarn->kapasitas }}</td></tr>
        <tr><td><strong>Luas</strong></td><td>{{ number_format($livestockBarn->luas_m2) }} m²</td></tr>
        <tr><td><strong>Status</strong></td><td>{{ ucfirst($livestockBarn->status) }}</td></tr>
    </table>

    <h5>Data Ternak ({{ $livestockBarn->inventories->count() }})</h5>
    @if($livestockBarn->inventories->count())
    <table>
        <thead><tr><th>No</th><th>Kode</th><th>Jenis</th><th>Jumlah</th><th>Berat</th><th>Tgl Masuk</th><th>Asal</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($livestockBarn->inventories as $inv)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $inv->kode_ternak }}</td>
                <td>{{ $inv->livestockType?->nama_jenis ?? '-' }}</td>
                <td>{{ $inv->jumlah }}</td>
                <td>{{ number_format($inv->berat_rata_kg ?? 0, 1) }} kg</td>
                <td>{{ $inv->tanggal_masuk ? \Carbon\Carbon::parse($inv->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                <td>{{ $inv->asal ?? '-' }}</td>
                <td>{{ ucfirst($inv->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada data ternak.</p> @endif

    <h5>Riwayat Kesehatan ({{ $livestockBarn->healthRecords->count() }})</h5>
    @if($livestockBarn->healthRecords->count())
    <table>
        <thead><tr><th>No</th><th>Tanggal</th><th>Jenis</th><th>Hasil</th><th>Diagnosa</th><th>Tindakan</th><th>Biaya</th></tr></thead>
        <tbody>
            @foreach($livestockBarn->healthRecords as $hr)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $hr->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                <td>{{ ucfirst(str_replace('_',' ',$hr->jenis_pemeriksaan)) }}</td>
                <td>{{ ucfirst($hr->hasil) }}</td>
                <td>{{ $hr->diagnosa ?? '-' }}</td>
                <td>{{ $hr->tindakan ?? '-' }}</td>
                <td>Rp {{ number_format($hr->biaya ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada riwayat kesehatan.</p> @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['aktif' => 'badge-success', 'renovasi' => 'badge-warning', 'nonaktif' => 'badge-secondary'];
        $history = $livestockBarn->statusHistories->sortByDesc('created_at');
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
