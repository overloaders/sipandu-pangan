<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Sertifikasi & Komplians - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#8b5cf6', 'accentDark' => '#7c3aed', 'accentBg' => '#f5f3ff', 'accentBorder' => '#ddd6fe'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN SERTIFIKASI & KOMPLIANS'])

<div class="summary">
        <div class="summary-box">
            <h5>{{ $records->count() }}</h5>
            <p>Total Catatan</p>
        </div>
        <div class="summary-box">
            <h5>{{ $records->where('status', 'aktif')->count() }}</h5>
            <p>Aktif</p>
        </div>
        <div class="summary-box">
            <h5>{{ $records->where('status', 'kedaluwarsa')->count() }}</h5>
            <p>Kedaluwarsa</p>
        </div>
        <div class="summary-box">
            <h5>{{ $records->whereNotNull('nilai_skor')->avg('nilai_skor') ? number_format($records->whereNotNull('nilai_skor')->avg('nilai_skor'), 1) : '-' }}</h5>
            <p>Rata-rata Skor</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Judul</th>
                <th>Tipe</th>
                <th>Skor</th>
                <th>Pemeriksaan</th>
                <th>Berlaku Hingga</th>
                <th>Risiko</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->kode_komplians }}</td>
                <td>{{ $r->judul }}</td>
                <td><span class="badge badge-info">{{ strtoupper($r->tipe) }}</span></td>
                <td>{{ $r->nilai_skor ?? '-' }}</td>
                <td>{{ $r->tanggal_pemeriksaan ? \Carbon\Carbon::parse($r->tanggal_pemeriksaan)->format('d/m/Y') : '-' }}</td>
                <td>{{ $r->tanggal_berlaku_sampai ? \Carbon\Carbon::parse($r->tanggal_berlaku_sampai)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($r->tingkat_risiko == 'tinggi')
                        <span class="badge badge-danger">Tinggi</span>
                    @elseif($r->tingkat_risiko == 'sedang')
                        <span class="badge badge-warning">Sedang</span>
                    @else
                        <span class="badge badge-success">{{ ucfirst($r->tingkat_risiko ?? '-') }}</span>
                    @endif
                </td>
                <td>
                    @if($r->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @elseif($r->status == 'kedaluwarsa')
                        <span class="badge badge-danger">Kadaluarsa</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($r->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
