<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Komplians - {{ $record->kode_komplians }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#14b8a6', 'accentDark' => '#0d9488', 'accentBg' => '#f0fdfa', 'accentBorder' => '#99f6e4', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL CATATAN KOMPLIANS'])

    <h5>Informasi Komplians</h5>
    <table>
        <tr><td width="180"><strong>Kode Komplians</strong></td><td>{{ $record->kode_komplians }}</td></tr>
        <tr><td><strong>Judul</strong></td><td>{{ $record->judul }}</td></tr>
        <tr><td><strong>Tipe Sertifikasi</strong></td><td>{{ strtoupper($record->tipe) }}</td></tr>
        <tr><td><strong>Unit</strong></td><td>{{ $record->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td><strong>Tanggal Pemeriksaan</strong></td><td>{{ $record->tanggal_pemeriksaan ? \Carbon\Carbon::parse($record->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Berlaku Sampai</strong></td><td>{{ $record->tanggal_berlaku_sampai ? \Carbon\Carbon::parse($record->tanggal_berlaku_sampai)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Nilai Skor</strong></td><td>{{ $record->nilai_skor ?? '-' }}</td></tr>
        <tr><td><strong>Tingkat Risiko</strong></td><td>{{ ucfirst($record->tingkat_risiko ?? '-') }}</td></tr>
        <tr><td><strong>Hasil</strong></td><td>{{ $record->hasil ?? '-' }}</td></tr>
        <tr><td><strong>Inspektur</strong></td><td>{{ $record->inspektur?->name ?? '-' }}</td></tr>
        <tr><td><strong>Status</strong></td><td>{{ ucfirst($record->status) }}</td></tr>
    </table>

    @if($record->temuan)
    <h5>Temuan</h5>
    <p>{{ $record->temuan }}</p>
    @endif

    @if($record->rekomendasi)
    <h5>Rekomendasi</h5>
    <p>{{ $record->rekomendasi }}</p>
    @endif

    <h5>Detail Pemeriksaan ({{ $record->details->count() }})</h5>
    @if($record->details->count())
    <table>
        <thead><tr><th>No</th><th>Item Pemeriksaan</th><th>Status</th><th>Bobot</th><th>Nilai</th><th>Catatan</th></tr></thead>
        <tbody>
            @foreach($record->details as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->item_pemeriksaan }}</td>
                <td>{{ ucfirst(str_replace('_',' ',$d->status_pemeriksaan)) }}</td>
                <td>{{ $d->bobot ?? '-' }}</td>
                <td>{{ $d->nilai ?? '-' }}</td>
                <td>{{ $d->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada detail pemeriksaan.</p> @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['draft' => 'badge-secondary', 'aktif' => 'badge-success', 'perlu_perbaikan' => 'badge-warning', 'kedaluwarsa' => 'badge-danger', 'dibatalkan' => 'badge-danger'];
        $history = $record->statusHistories->sortByDesc('created_at');
    @endphp
    @if($history->isNotEmpty())
    <div class="section">
        <h5>Riwayat Perubahan Status</h5>
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
