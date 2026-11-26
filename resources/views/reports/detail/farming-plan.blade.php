<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Rencana Pertanian - {{ $farmingPlan->kode_rencana }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#f59e0b', 'accentDark' => '#d97706', 'accentBg' => '#fffbeb', 'accentBorder' => '#fde68a', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL RENCANA KERJA PERTANIAN'])

    <h5>Informasi Rencana</h5>
    <table>
        <tr><td width="180"><strong>Kode Rencana</strong></td><td>{{ $farmingPlan->kode_rencana }}</td></tr>
        <tr><td><strong>Program</strong></td><td>{{ $farmingPlan->foodProgram?->nama_program ?? '-' }}</td></tr>
        <tr><td><strong>Lahan</strong></td><td>{{ $farmingPlan->landPlot?->nama_lahan ?? '-' }} ({{ number_format($farmingPlan->landPlot?->luas_m2 ?? 0) }} m²)</td></tr>
        <tr><td><strong>Tanaman</strong></td><td>{{ $farmingPlan->cropType?->nama_tanaman ?? '-' }}</td></tr>
        <tr><td><strong>Jumlah Tanaman</strong></td><td>{{ number_format($farmingPlan->jumlah_tanaman) }}</td></tr>
        <tr><td><strong>Metode Budi Daya</strong></td><td>{{ ucfirst($farmingPlan->metode_budi_daya) }}</td></tr>
        <tr><td><strong>Status</strong></td><td><span class="badge badge-success">{{ ucfirst(str_replace('_',' ',$farmingPlan->status)) }}</span></td></tr>
        <tr><td><strong>Tgl Tanam Rencana</strong></td><td>{{ $farmingPlan->tanggal_tanam_rencana?->format('d/m/Y') ?? '-' }}</td></tr>
        <tr><td><strong>Tgl Tanam Aktual</strong></td><td>{{ $farmingPlan->tanggal_tanam_aktual?->format('d/m/Y') ?? '-' }}</td></tr>
        <tr><td><strong>Tgl Panen Rencana</strong></td><td>{{ $farmingPlan->tanggal_panen_rencana?->format('d/m/Y') ?? '-' }}</td></tr>
        <tr><td><strong>Estimasi Hasil</strong></td><td>{{ number_format($farmingPlan->estimasi_hasil_kg ?? 0) }} kg</td></tr>
        <tr><td><strong>Estimasi Pendapatan</strong></td><td>Rp {{ number_format($farmingPlan->estimasi_pendapatan ?? 0) }}</td></tr>
    </table>

    <h5>Log Kegiatan ({{ $farmingPlan->activities->count() }})</h5>
    @if($farmingPlan->activities->count())
    <table>
        <thead><tr><th>No</th><th>Tanggal</th><th>Kegiatan</th><th>PIC</th><th>Biaya</th><th>Keterangan</th></tr></thead>
        <tbody>
            @foreach($farmingPlan->activities as $a)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $a->tanggal_kegiatan->format('d/m/Y') }}</td>
                <td>{{ ucfirst(str_replace('_',' ',$a->jenis_kegiatan)) }}</td>
                <td>{{ $a->picWbp?->nama_lengkap ?? '-' }}</td>
                <td>Rp {{ number_format($a->biaya ?? 0) }}</td>
                <td>{{ $a->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada kegiatan tercatat.</p> @endif

    <h5>Data Panen ({{ $farmingPlan->harvests->count() }})</h5>
    @if($farmingPlan->harvests->count())
    <table>
        <thead><tr><th>No</th><th>Kode</th><th>Tanggal</th><th>Berat</th><th>Kualitas</th><th>Harga/kg</th><th>Total</th></tr></thead>
        <tbody>
            @foreach($farmingPlan->harvests as $h)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $h->kode_panen }}</td>
                <td>{{ $h->tanggal_panen->format('d/m/Y') }}</td>
                <td>{{ number_format($h->total_berat_kg, 1) }} kg</td>
                <td>{{ ucfirst($h->kualitas) }}</td>
                <td>Rp {{ number_format($h->harga_jual_per_kg ?? 0) }}</td>
                <td><strong>Rp {{ number_format($h->total_pendapatan) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Total Berat:</strong> {{ number_format($farmingPlan->harvests->sum('total_berat_kg'), 1) }} kg | <strong>Total Pendapatan:</strong> Rp {{ number_format($farmingPlan->harvests->sum('total_pendapatan')) }}</p>
    @else <p style="color:#999;">Belum ada data panen.</p> @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['direncanakan' => 'badge-secondary', 'proses_tanam' => 'badge-info', 'tumbuh' => 'badge-success', 'siap_panen' => 'badge-warning', 'panen' => 'badge-info', 'selesai' => 'badge-success'];
        $history = $farmingPlan->statusHistories->sortByDesc('created_at');
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
