<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelatihan - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#3b82f6', 'accentDark' => '#2563eb', 'accentBg' => '#eff6ff', 'accentBorder' => '#bfdbfe'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN PELATIHAN & PEMBINAAN'])

<div class="summary">
        <div class="summary-box">
            <h5>{{ $programs->count() }}</h5>
            <p>Total Program</p>
        </div>
        <div class="summary-box">
            <h5>{{ $programs->where('status', 'aktif')->count() }}</h5>
            <p>Program Aktif</p>
        </div>
        <div class="summary-box">
            <h5>{{ $summary['total_peserta'] }}</h5>
            <p>Total Peserta</p>
        </div>
        <div class="summary-box">
            <h5>{{ $summary['total_sertifikat'] }}</h5>
            <p>Sertifikat Diterbitkan</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Pelatihan</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Tanggal Mulai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($programs as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->kode_pelatihan }}</td>
                <td>{{ $p->nama_pelatihan }}</td>
                <td><span class="badge badge-info">{{ ucfirst($p->kategori) }}</span></td>
                <td>{{ ucfirst($p->tipe ?? '-') }}</td>
                <td>{{ $p->lokasi ?? '-' }}</td>
                <td>{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                <td>
                    @if($p->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($p->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
