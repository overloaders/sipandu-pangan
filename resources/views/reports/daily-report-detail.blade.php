<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Harian - {{ $report->kode_laporan }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#06b6d4', 'accentDark' => '#0891b2', 'accentBg' => '#ecfeff', 'accentBorder' => '#a5f3fc', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL KEGIATAN HARIAN'])

    {{-- INFORMASI LAPORAN --}}
    <div class="section">
        <div class="section-title">Informasi Laporan</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Laporan</span><span class="info-value font-bold">{{ $report->kode_laporan }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal</span><span class="info-value">{{ $report->tanggal_laporan->format('d M Y') }}</span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $report->status }}">{{ ucfirst($report->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Pelapor</span><span class="info-value">{{ $report->reporter?->name ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Dikonfirmasi oleh</span><span class="info-value">{{ $report->confirmer?->name ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Konfirmasi</span><span class="info-value">{{ $report->tanggal_konfirmasi?->format('d M Y H:i') ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- DETAIL KEGIATAN --}}
    @if($report->details->count() > 0)
    <div class="section">
        <div class="section-title">Detail Kegiatan ({{ $report->details->count() }} item)</div>
        <table>
            <thead>
                <tr>
                    <th width="25">No</th>
                    <th>Jenis Kegiatan</th>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->details as $idx => $d)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $d->jenis_kegiatan ?? '-')) }}</td>
                    <td>{{ $d->deskripsi ?? '-' }}</td>
                    <td>{{ number_format($d->jumlah ?? 0, 2) }}</td>
                    <td>{{ $d->satuan ?? '-' }}</td>
                    <td>{{ $d->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- KETERANGAN UMUM --}}
    @if($report->keterangan)
    <div class="section">
        <div class="section-title">Keterangan Umum</div>
        <div class="info-box">
            <p>{{ $report->keterangan }}</p>
        </div>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'dikirim' => 'badge-info', 'dikonfirmasi' => 'badge-success', 'dikembalikan' => 'badge-warning'];
        $history = $report->statusHistories->sortByDesc('created_at');
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
