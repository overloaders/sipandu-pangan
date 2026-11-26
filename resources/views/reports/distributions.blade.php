<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#ec4899', 'accentDark' => '#db2777', 'accentBg' => '#fdf2f8', 'accentBorder' => '#fbcfe8', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DISTRIBUSI'])

<div class="summary">
        <div class="summary-box">
            <h5>{{ $distributions->count() }}</h5>
            <p>Total Distribusi</p>
        </div>
        <div class="summary-box">
            <h5>{{ $distributions->where('status', 'diterima')->count() }}</h5>
            <p>Diterima</p>
        </div>
        <div class="summary-box">
            <h5>{{ $distributions->where('status', 'dikirim')->count() }}</h5>
            <p>Dikirim</p>
        </div>
        <div class="summary-box">
            <h5>Rp {{ number_format($distributions->sum('total_nilai'), 0, ',', '.') }}</h5>
            <p>Total Nilai</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Jumlah Penerima</th>
                <th>Total Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distributions as $d)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $d->kode_distribusi }}</td>
                <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $d->tipe_distribusi)) }}</span></td>
                <td>{{ $d->tanggal_distribusi ? \Carbon\Carbon::parse($d->tanggal_distribusi)->format('d/m/Y') : '-' }}</td>
                <td>{{ $d->penerima_nama }}</td>
                <td>{{ $d->jumlah_penerima ?? '-' }}</td>
                <td>Rp {{ number_format($d->total_nilai ?? 0, 0, ',', '.') }}</td>
                <td>
                    @if($d->status == 'diterima')
                        <span class="badge badge-success">Diterima</span>
                    @elseif($d->status == 'dikirim')
                        <span class="badge badge-warning">Dikirim</span>
                    @elseif($d->status == 'draft')
                        <span class="badge badge-secondary">Draft</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($d->status) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

        @include('reports.partials.report-footer')

</body>
</html>
