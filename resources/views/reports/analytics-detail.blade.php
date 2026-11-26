<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Analitik - {{ $analytic->kode_indikator }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#7c3aed', 'accentDark' => '#6d28d9', 'accentBg' => '#f5f3ff', 'accentBorder' => '#ddd6fe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL INDIKATOR ANALITIK'])

    {{-- INFORMASI INDIKATOR --}}
    <div class="section">
        <div class="section-title">Informasi Indikator</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Indikator</span><span class="info-value font-bold">{{ $analytic->kode_indikator }}</span></div>
                <div class="info-item"><span class="info-label">Nama Indikator</span><span class="info-value font-bold">{{ $analytic->nama_indikator }}</span></div>
                <div class="info-item"><span class="info-label">Kategori</span><span class="info-value">{{ ucfirst($analytic->kategori) }}</span></div>
                <div class="info-item"><span class="info-label">Satuan</span><span class="info-value">{{ $analytic->satuan }}</span></div>
                <div class="info-item"><span class="info-label">Target Nilai</span><span class="info-value">{{ number_format($analytic->target_nilai ?? 0, 2) }}</span></div>
                <div class="info-item"><span class="info-label">Batas Minimum</span><span class="info-value">{{ number_format($analytic->batas_minimum ?? 0, 2) }}</span></div>
                <div class="info-item"><span class="info-label">Batas Maksimum</span><span class="info-value">{{ number_format($analytic->batas_maksimum ?? 0, 2) }}</span></div>
                <div class="info-item"><span class="info-label">Status Aktif</span><span class="info-value">{{ $analytic->is_active ? 'Ya' : 'Tidak' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Deskripsi</span><span class="info-value">{{ $analytic->deskripsi ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Rumus Perhitungan</span><span class="info-value">{{ $analytic->rumus_perhitungan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- TARGET & REALISASI --}}
    @if($analytic->targets->count() > 0)
    <div class="section">
        <div class="section-title">Target & Realisasi per Unit ({{ $analytic->targets->count() }} data)</div>
        <table>
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Periode</th>
                    <th class="text-right">Target</th>
                    <th class="text-right">Realisasi</th>
                    <th class="text-right">Capaian</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($analytic->targets as $t)
                <tr>
                    <td>{{ $t->unit?->nama_unit ?? '-' }}</td>
                    <td>{{ $t->periode_bulan }}/{{ $t->periode_tahun }}</td>
                    <td class="text-right">{{ number_format($t->target_nilai ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($t->realisasi_nilai ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($t->persentase_capaian ?? 0, 2) }}%</td>
                    <td>
                        @if(($t->persentase_capaian ?? 0) >= 80)
                            <span class="status status-tinggi">Tinggi</span>
                        @elseif(($t->persentase_capaian ?? 0) >= 50)
                            <span class="status status-sedang">Sedang</span>
                        @else
                            <span class="status status-rendah">Rendah</span>
                        @endif
                    </td>
                    <td>{{ $t->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

        @include('reports.partials.report-footer')

</body>
</html>
