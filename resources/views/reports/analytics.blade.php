<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Analitik & Indikator - SIDUMA PANGAN</title>
    @include('reports.partials.pdf-styles', ['accent' => '#06b6d4', 'accentDark' => '#0891b2', 'accentBg' => '#ecfeff', 'accentBorder' => '#a5f3fc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN ANALITIK & INDIKATOR'])

    @php
        $allTargets = $indicators->flatMap->targets;
        $totalTarget = $allTargets->sum('target_nilai');
        $totalRealisasi = $allTargets->sum('realisasi_nilai');
    @endphp

    <div class="summary">
        <div class="summary-box">
            <h5>{{ $indicators->count() }}</h5>
            <p>Total Indikator</p>
        </div>
        <div class="summary-box">
            <h5>{{ number_format($totalTarget, 0, ',', '.') }}</h5>
            <p>Total Target</p>
        </div>
        <div class="summary-box">
            <h5>{{ number_format($totalRealisasi, 0, ',', '.') }}</h5>
            <p>Total Realisasi</p>
        </div>
        <div class="summary-box">
            <h5>{{ $totalTarget > 0 ? number_format(($totalRealisasi / $totalTarget) * 100, 1) : 0 }}%</h5>
            <p>Pencapaian</p>
        </div>
    </div>

    @foreach($indicators as $ind)
    <table>
        <thead>
            <tr>
                <th colspan="5" style="background:#0891b2;font-size:13px;">{{ $ind->nama_indikator }} — {{ ucfirst($ind->kategori) }}</th>
            </tr>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:15%;">Unit</th>
                <th style="width:25%;">Satuan</th>
                <th style="width:25%;text-align:right;">Target</th>
                <th style="width:25%;text-align:right;">Realisasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ind->targets as $target)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $target->unit->nama_unit ?? '-' }}</td>
                <td>{{ $ind->satuan }}</td>
                <td style="text-align:right;">{{ number_format($target->target_nilai, 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($target->realisasi_nilai, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#999;">Tidak ada data target</td>
            </tr>
            @endforelse
            @if($ind->targets->count() > 1)
            <tr style="font-weight:bold;background:#f0f9ff;">
                <td colspan="3">Total</td>
                <td style="text-align:right;">{{ number_format($ind->targets->sum('target_nilai'), 0, ',', '.') }}</td>
                <td style="text-align:right;">{{ number_format($ind->targets->sum('realisasi_nilai'), 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>
    @endforeach

        @include('reports.partials.report-footer')

</body>
</html>
