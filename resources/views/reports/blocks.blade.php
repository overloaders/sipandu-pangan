<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Blok & Kamar</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
    @include('reports.partials.letterhead', ['title' => 'LAPORAN BLOK & KAMAR'])

    <div class="summary">
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_blok']) }}</div>
            <div class="label">Total Blok</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['total_kamar']) }}</div>
            <div class="label">Total Kamar</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['kapasitas']) }}</div>
            <div class="label">Kapasitas Total</div>
        </div>
        <div class="summary-box">
            <div class="value">{{ number_format($summary['terisi']) }}</div>
            <div class="label">Terisi</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Blok</th>
                <th>Nama Blok</th>
                <th>Jenis</th>
                <th>Kapasitas</th>
                <th>Terisi</th>
                <th>Status</th>
                <th>Unit</th>
                <th>Jumlah Kamar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($blocks as $i => $block)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $block->kode_blok }}</strong></td>
                <td>{{ $block->nama_blok }}</td>
                <td>{{ ucfirst($block->jenis_blok) }}</td>
                <td>{{ number_format($block->kapasitas) }}</td>
                <td>{{ number_format($block->terisi) }}</td>
                <td>
                    @if($block->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($block->status) }}</span>
                    @endif
                </td>
                <td>{{ $block->unit?->nama_unit ?? '-' }}</td>
                <td>{{ $block->rooms->count() }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#999;">Belum ada blok tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($blocks->isNotEmpty())
    <h5>Rincian Kamar per Blok</h5>
    @foreach($blocks as $block)
        <div class="sub-section">
            <strong>{{ $block->kode_blok }} — {{ $block->nama_blok }}</strong>
            @if($block->rooms->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kamar</th>
                        <th>Nama Kamar</th>
                        <th>Kapasitas</th>
                        <th>Terisi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($block->rooms as $j => $room)
                    <tr>
                        <td>{{ $j + 1 }}</td>
                        <td><strong>{{ $room->kode_kamar }}</strong></td>
                        <td>{{ $room->nama_kamar }}</td>
                        <td>{{ number_format($room->kapasitas) }}</td>
                        <td>{{ number_format($room->terisi) }}</td>
                        <td>
                            @if($room->status == 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($room->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color:#999;">Belum ada kamar pada blok ini.</p>
            @endif
        </div>
    @endforeach
    @endif

    @include('reports.partials.report-footer')
</body>
</html>
