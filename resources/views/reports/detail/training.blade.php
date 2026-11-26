<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Pelatihan - {{ $program->kode_pelatihan }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#6366f1', 'accentDark' => '#4f46e5', 'accentBg' => '#eef2ff', 'accentBorder' => '#c7d2fe', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'DETAIL PROGRAM PELATIHAN'])

    <h5>Informasi Program</h5>
    <table>
        <tr><td width="180"><strong>Kode Pelatihan</strong></td><td>{{ $program->kode_pelatihan }}</td></tr>
        <tr><td><strong>Nama Pelatihan</strong></td><td>{{ $program->nama_pelatihan }}</td></tr>
        <tr><td><strong>Kategori</strong></td><td>{{ ucfirst($program->kategori) }}</td></tr>
        <tr><td><strong>Tipe</strong></td><td>{{ ucfirst($program->tipe ?? '-') }}</td></tr>
        <tr><td><strong>Unit</strong></td><td>{{ $program->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td><strong>Lokasi</strong></td><td>{{ $program->lokasi ?? '-' }}</td></tr>
        <tr><td><strong>Tanggal Mulai</strong></td><td>{{ $program->tanggal_mulai ? \Carbon\Carbon::parse($program->tanggal_mulai)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Tanggal Selesai</strong></td><td>{{ $program->tanggal_selesai ? \Carbon\Carbon::parse($program->tanggal_selesai)->format('d/m/Y') : '-' }}</td></tr>
        <tr><td><strong>Jam Pelatihan</strong></td><td>{{ $program->jam_pelatihan ?? '-' }} jam</td></tr>
        <tr><td><strong>Kapasitas</strong></td><td>{{ $program->kapasitas_peserta ?? '-' }} orang</td></tr>
        <tr><td><strong>Biaya</strong></td><td>Rp {{ number_format($program->biaya ?? 0) }}</td></tr>
        <tr><td><strong>Sertifikat</strong></td><td>{{ $program->sertifikat_diberikan ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td><strong>Status</strong></td><td>{{ ucfirst($program->status) }}</td></tr>
    </table>

    @if($program->deskripsi)
    <h5>Deskripsi</h5>
    <p>{{ $program->deskripsi }}</p>
    @endif

    @if($program->materi_pokok)
    <h5>Materi Pokok</h5>
    <p>{{ $program->materi_pokok }}</p>
    @endif

    <h5>Peserta ({{ $program->participants->count() }})</h5>
    @if($program->participants->count())
    <table>
        <thead><tr><th>No</th><th>Nama WBP</th><th>Tgl Daftar</th><th>Status</th><th>Nilai Akhir</th></tr></thead>
        <tbody>
            @foreach($program->participants as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->wbp?->nama_lengkap ?? '-' }}</td>
                <td>{{ $p->tanggal_daftar ? \Carbon\Carbon::parse($p->tanggal_daftar)->format('d/m/Y') : '-' }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>{{ $p->nilai_akhir ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else <p style="color:#999;">Belum ada peserta terdaftar.</p> @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['perencanaan' => 'badge-warning', 'terbuka' => 'badge-info', 'berlangsung' => 'badge-primary', 'selesai' => 'badge-success', 'dibatalkan' => 'badge-danger'];
        $history = $program->statusHistories->sortByDesc('created_at');
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
