<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Kamar - {{ $room->kode_kamar }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#0ea5e9', 'accentDark' => '#0284c7', 'accentBg' => '#f0f9ff', 'accentBorder' => '#bae6fd', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL KAMAR'])

    {{-- INFORMASI KAMAR --}}
    <div class="section">
        <div class="section-title">Informasi Kamar</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Kamar</span><span class="info-value font-bold">{{ $room->kode_kamar }}</span></div>
                <div class="info-item"><span class="info-label">Nama Kamar</span><span class="info-value font-bold">{{ $room->nama_kamar }}</span></div>
                <div class="info-item"><span class="info-label">Blok</span><span class="info-value">{{ $room->block?->kode_blok ?? '-' }} @if($room->block?->nama_blok) ({{ $room->block->nama_blok }}) @endif</span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Kapasitas</span><span class="info-value">{{ $room->kapasitas }} orang</span></div>
                <div class="info-item"><span class="info-label">Terisi</span><span class="info-value">{{ $room->terisi }} orang</span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $room->status }}">{{ ucfirst($room->status) }}</span></span></div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['tersedia' => 'badge-success', 'penuh' => 'badge-danger', 'renovasi' => 'badge-warning', 'nonaktif' => 'badge-secondary'];
        $history = $room->statusHistories->sortByDesc('created_at');
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
