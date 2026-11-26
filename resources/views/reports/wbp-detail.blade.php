<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail WBP - {{ $wbp->nama_lengkap }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#3b82f6', 'accentDark' => '#2563eb', 'accentBg' => '#eff6ff', 'accentBorder' => '#bfdbfe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL WARGA BINAAN PEMASYARAKATAN'])

    {{-- DATA PRIBADI --}}
    <div class="section">
        <div class="section-title">Data Pribadi</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Nomor Induk</span><span class="info-value font-bold">{{ $wbp->nomor_induk }}</span></div>
                <div class="info-item"><span class="info-label">Nama Lengkap</span><span class="info-value font-bold">{{ $wbp->nama_lengkap }}</span></div>
                <div class="info-item"><span class="info-label">Jenis Kelamin</span><span class="info-value">{{ $wbp->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Lahir</span><span class="info-value">{{ $wbp->tanggal_lahir?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tempat Lahir</span><span class="info-value">{{ $wbp->tempat_lahir ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">NIK</span><span class="info-value">{{ $wbp->nik ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Agama</span><span class="info-value">{{ ucfirst($wbp->agama ?? '-') }}</span></div>
                <div class="info-item"><span class="info-label">Kebangsaan</span><span class="info-value">{{ $wbp->kebangsaan ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Pendidikan Terakhir</span><span class="info-value">{{ ucfirst($wbp->pendidikan_terakhir ?? '-') }}</span></div>
                <div class="info-item"><span class="info-label">Pekerjaan Sebelum</span><span class="info-value">{{ $wbp->pekerjaan_sebelum ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Alamat Asal</span><span class="info-value">{{ $wbp->alamat_asal ?? '-' }}, {{ $wbp->kota_asal ?? '-' }}, {{ $wbp->provinsi_asal ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Nomor HP</span><span class="info-value">{{ $wbp->nomor_hp ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- DATA PEMASYARATAN --}}
    <div class="section">
        <div class="section-title">Data Pemasyarakatan</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $wbp->status }}">{{ ucfirst($wbp->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Blok</span><span class="info-value">{{ $wbp->block?->kode_blok ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Kamar</span><span class="info-value">{{ $wbp->room?->kode_kamar ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Jenis Pidana</span><span class="info-value">{{ $wbp->jenis_pidana ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Kasus</span><span class="info-value">{{ $wbp->kasus ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Masuk</span><span class="info-value">{{ $wbp->tanggal_masuk?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Masa Hukuman Selesai</span><span class="info-value">{{ $wbp->masa_hukuman_selesai?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Remisi</span><span class="info-value">{{ $wbp->remisi_hari ?? 0 }} hari</span></div>
                <div class="info-item"><span class="info-label">Tamping</span><span class="info-value">{{ $wbp->tamping ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- KELUARGA --}}
    @if($wbp->familyMembers->count() > 0)
    <div class="section">
        <div class="section-title">Data Keluarga ({{ $wbp->familyMembers->count() }} orang)</div>
        <table>
            <thead><tr><th>Nama</th><th>Hubungan</th><th>NIK</th><th>Alamat</th><th>No. HP</th></tr></thead>
            <tbody>
                @foreach($wbp->familyMembers as $fm)
                <tr>
                    <td>{{ $fm->nama_lengkap }} @if($fm->is_wali)<span class="badge badge-warning">WALI</span>@endif</td>
                    <td>{{ ucfirst($fm->hubungan) }}</td>
                    <td>{{ $fm->nik ?? '-' }}</td>
                    <td>{{ $fm->alamat ?? '-' }}</td>
                    <td>{{ $fm->nomor_hp ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- DOKUMEN --}}
    @if($wbp->documents->count() > 0)
    <div class="section">
        <div class="section-title">Dokumen ({{ $wbp->documents->count() }})</div>
        <table>
            <thead><tr><th>Jenis</th><th>Nama Dokumen</th><th>Nomor</th><th>Tanggal</th><th>Keterangan</th></tr></thead>
            <tbody>
                @foreach($wbp->documents as $doc)
                <tr>
                    <td>{{ $doc->jenis_dokumen ?? '-' }}</td>
                    <td>{{ $doc->nama_dokumen ?? '-' }}</td>
                    <td>{{ $doc->nomor_dokumen ?? '-' }}</td>
                    <td>{{ $doc->tanggal_dokumen?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $doc->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['aktif' => 'badge-success', 'dikeluarkan' => 'badge-danger', 'pindah' => 'badge-info', 'meninggal' => 'badge-secondary', 'bebas' => 'badge-info'];
        $history = $wbp->statusHistories->sortByDesc('created_at');
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
