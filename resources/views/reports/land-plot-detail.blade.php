<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Lahan - {{ $landPlot->nama_lahan }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#4ade80', 'accentDark' => '#16a34a', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL LAHAN'])

    {{-- RINGKASAN --}}
    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ number_format($landPlot->luas_m2 ?? 0, 0) }}</div>
            <div class="label">Luas (m²)</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $landPlot->farmingPlans->count() }}</div>
            <div class="label">Rencana Kerja</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ ucfirst($landPlot->status) }}</div>
            <div class="label">Status</div>
        </div>
    </div>

    {{-- INFORMASI LAHAN --}}
    <div class="section">
        <div class="section-title">Informasi Lahan</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Lahan</span><span class="info-value font-bold">{{ $landPlot->kode_lahan }}</span></div>
                <div class="info-item"><span class="info-label">Nama Lahan</span><span class="info-value font-bold">{{ $landPlot->nama_lahan }}</span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $landPlot->status }}">{{ ucfirst($landPlot->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Luas</span><span class="info-value">{{ number_format($landPlot->luas_m2 ?? 0, 2) }} m²</span></div>
                <div class="info-item"><span class="info-label">Jenis Tanah</span><span class="info-value">{{ ucfirst($landPlot->jenis_tanah ?? '-') }}</span></div>
                <div class="info-item"><span class="info-label">Irigasi</span><span class="info-value">{{ ucfirst($landPlot->irigasi ?? '-') }}</span></div>
                <div class="info-item"><span class="info-label">Koordinat</span><span class="info-value">{{ $landPlot->koordinat ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Deskripsi</span><span class="info-value">{{ $landPlot->deskripsi ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Keterangan</span><span class="info-value">{{ $landPlot->keterangan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- RENCANA KERJA --}}
    @if($landPlot->farmingPlans->count() > 0)
    <div class="section">
        <div class="section-title">Rencana Kerja di Lahan Ini ({{ $landPlot->farmingPlans->count() }} rencana)</div>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanaman</th>
                    <th>Program</th>
                    <th>Metode</th>
                    <th>Tgl Tanam</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($landPlot->farmingPlans as $fp)
                <tr>
                    <td>{{ $fp->kode_rencana }}</td>
                    <td>{{ $fp->cropType?->nama_tanaman ?? '-' }}</td>
                    <td>{{ $fp->foodProgram?->nama_program ?? '-' }}</td>
                    <td>{{ ucfirst($fp->metode_budi_daya ?? '-') }}</td>
                    <td>{{ $fp->tanggal_tanam_rencana?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="status status-{{ $fp->status }}">{{ ucfirst(str_replace('_',' ',$fp->status)) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['tersedia' => 'badge-success', 'ditanami' => 'badge-info', 'istirahat' => 'badge-warning', 'renovasi' => 'badge-secondary', 'tidak_digunakan' => 'badge-secondary'];
        $history = $landPlot->statusHistories->sortByDesc('created_at');
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
