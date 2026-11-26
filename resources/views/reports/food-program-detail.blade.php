<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Program Pangan - {{ $program->kode_program }}</title>
    @include('reports.partials.pdf-styles', ['accent' => '#10b981', 'accentDark' => '#059669', 'accentBg' => '#f0fdf4', 'accentBorder' => '#bbf7d0', 'accentRow' => '#f8fafc'])
</head>
<body>
        @include('reports.partials.letterhead', ['title' => 'LAPORAN DETAIL PROGRAM KETAHANAN PANGAN'])

    {{-- RINGKASAN --}}
    @php
        $farmCount = $program->farmingPlans->count();
        $liveCount = $program->livestockBarns->count();
        $fishCount = $program->fishPonds->count();
        $farmPeserta = $program->farmingPlans->sum(fn($fp) => $fp->participants->count());
        $livePeserta = $program->livestockBarns->sum(fn($b) => $b->participants->count());
        $fishPeserta = $program->fishPonds->sum(fn($p) => $p->participants->count());
    @endphp
    <div class="summary-cards">
        <div class="summary-card">
            <div class="value">{{ $farmCount + $liveCount + $fishCount }}</div>
            <div class="label">Total Unit Usaha</div>
        </div>
        <div class="summary-card">
            <div class="value">{{ $totalPeserta }}</div>
            <div class="label">Total Peserta</div>
        </div>
        <div class="summary-card">
            <div class="value">Rp {{ number_format($program->anggaran ?? 0, 0, ',', '.') }}</div>
            <div class="label">Anggaran</div>
        </div>
        <div class="summary-card">
            <div class="value">Rp {{ number_format($program->realisasi_anggaran ?? 0, 0, ',', '.') }}</div>
            <div class="label">Realisasi</div>
        </div>
    </div>

    {{-- INFORMASI PROGRAM --}}
    <div class="section">
        <div class="section-title"><i class="fas fa-info-circle"></i> Informasi Program</div>
        <div class="info-box">
            <div class="info-grid">
                <div class="info-item"><span class="info-label">Kode Program</span><span class="info-value font-bold">{{ $program->kode_program }}</span></div>
                <div class="info-item"><span class="info-label">Nama Program</span><span class="info-value font-bold">{{ $program->nama_program }}</span></div>
                <div class="info-item"><span class="info-label">Jenis Program</span><span class="info-value"><span class="badge badge-{{ $program->jenis_program }}">{{ ucfirst($program->jenis_program) }}</span></span></div>
                <div class="info-item"><span class="info-label">Status</span><span class="info-value"><span class="status status-{{ $program->status }}">{{ ucfirst($program->status) }}</span></span></div>
                <div class="info-item"><span class="info-label">Unit</span><span class="info-value">{{ $unitName }}</span></div>
                <div class="info-item"><span class="info-label">Dibuat oleh</span><span class="info-value">{{ $program->creator?->name ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Mulai</span><span class="info-value">{{ $program->tanggal_mulai?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-item"><span class="info-label">Tanggal Selesai Rencana</span><span class="info-value">{{ $program->tanggal_rencana_selesai?->format('d M Y') ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Deskripsi</span><span class="info-value">{{ $program->deskripsi ?? '-' }}</span></div>
                <div class="info-item info-full"><span class="info-label">Keterangan</span><span class="info-value">{{ $program->keterangan ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- DAFTAR PESERTA LENGKAP --}}
    <div class="section">
        <div class="section-title"><i class="fas fa-users"></i> Daftar Peserta Lengkap ({{ $totalPeserta }} orang)</div>
        
        @if($farmPeserta > 0)
        <p style="font-size:10px;color:#64748b;margin-bottom:5px;"><strong>Pertanian</strong> ({{ $farmPeserta }} orang)</p>
        <table>
            <thead>
                <tr>
                    <th width="25">No</th>
                    <th>Nama WBP</th>
                    <th>Nomor Induk</th>
                    <th>Posisi / Lokasi</th>
                    <th>Peran</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($program->farmingPlans as $fp)
                    @foreach($fp->participants as $p)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $p->wbp?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $p->wbp?->nomor_induk ?? '-' }}</td>
                        <td><span class="badge badge-pertanian">{{ $fp->kode_rencana }}</span> {{ $fp->cropType?->nama_tanaman ?? '-' }}</td>
                        <td>{{ $p->peran ?? '-' }}</td>
                        <td>{{ $p->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif

        @if($livePeserta > 0)
        <p style="font-size:10px;color:#64748b;margin-bottom:5px;margin-top:10px;"><strong>Peternakan</strong> ({{ $livePeserta }} orang)</p>
        <table>
            <thead>
                <tr>
                    <th width="25">No</th>
                    <th>Nama WBP</th>
                    <th>Nomor Induk</th>
                    <th>Posisi / Lokasi</th>
                    <th>Peran</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($program->livestockBarns as $barn)
                    @foreach($barn->participants as $p)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $p->wbp?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $p->wbp?->nomor_induk ?? '-' }}</td>
                        <td><span class="badge badge-peternakan">{{ $barn->kode_kandang }}</span> {{ $barn->nama_kandang }} ({{ ucfirst($barn->jenis_ternak) }})</td>
                        <td>{{ $p->peran ?? '-' }}</td>
                        <td>{{ $p->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif

        @if($fishPeserta > 0)
        <p style="font-size:10px;color:#64748b;margin-bottom:5px;margin-top:10px;"><strong>Perikanan</strong> ({{ $fishPeserta }} orang)</p>
        <table>
            <thead>
                <tr>
                    <th width="25">No</th>
                    <th>Nama WBP</th>
                    <th>Nomor Induk</th>
                    <th>Posisi / Lokasi</th>
                    <th>Peran</th>
                    <th>Tanggal Mulai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($program->fishPonds as $pond)
                    @foreach($pond->participants as $p)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $p->wbp?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $p->wbp?->nomor_induk ?? '-' }}</td>
                        <td><span class="badge badge-perikanan">{{ $pond->kode_kolam }}</span> {{ $pond->nama_kolam }} ({{ $pond->tipe_kolam }})</td>
                        <td>{{ $p->peran ?? '-' }}</td>
                        <td>{{ $p->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        @endif

        @if($totalPeserta == 0)
        <p style="text-align:center;color:#94a3b8;padding:15px;">Belum ada peserta terdaftar</p>
        @endif
    </div>

    {{-- RENCANA KERJA PERTANIAN --}}
    @if($program->farmingPlans->count() > 0)
    <div class="section">
        <div class="section-title"><i class="fas fa-seedling"></i> Rencana Kerja Pertanian ({{ $farmCount }} rencana)</div>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanaman</th>
                    <th>Lahan</th>
                    <th>Luas</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="text-center">Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->farmingPlans as $fp)
                <tr>
                    <td>{{ $fp->kode_rencana }}</td>
                    <td>{{ $fp->cropType?->nama_tanaman ?? '-' }}</td>
                    <td>{{ $fp->landPlot?->nama_lahan ?? '-' }}</td>
                    <td>{{ number_format($fp->luas_tanam_m2 ?? 0, 0) }} m²</td>
                    <td>{{ number_format($fp->jumlah_tanaman ?? 0) }}</td>
                    <td>{{ ucfirst($fp->metode_budi_daya ?? '-') }}</td>
                    <td><span class="status status-{{ $fp->status }}">{{ ucfirst(str_replace('_',' ',$fp->status)) }}</span></td>
                    <td class="text-center">{{ $fp->participants->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- PETURNAKAN --}}
    @if($program->livestockBarns->count() > 0)
    <div class="section">
        <div class="section-title"><i class="fas fa-paw"></i> Peternakan ({{ $liveCount }} kandang)</div>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kandang</th>
                    <th>Jenis Ternak</th>
                    <th>Kapasitas</th>
                    <th>Terisi</th>
                    <th>Luas</th>
                    <th>Status</th>
                    <th class="text-center">Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->livestockBarns as $b)
                <tr>
                    <td>{{ $b->kode_kandang }}</td>
                    <td>{{ $b->nama_kandang }}</td>
                    <td>{{ ucfirst($b->jenis_ternak) }}</td>
                    <td>{{ number_format($b->kapasitas ?? 0) }} ekor</td>
                    <td>{{ number_format($b->terisi ?? 0) }} ekor</td>
                    <td>{{ number_format($b->luas_m2 ?? 0, 0) }} m²</td>
                    <td><span class="status status-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                    <td class="text-center">{{ $b->participants->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- PERIKANAN --}}
    @if($program->fishPonds->count() > 0)
    <div class="section">
        <div class="section-title"><i class="fas fa-fish"></i> Perikanan ({{ $fishCount }} kolam)</div>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Kolam</th>
                    <th>Tipe</th>
                    <th>Luas</th>
                    <th>Kedalaman</th>
                    <th>Volume</th>
                    <th>Sumber Air</th>
                    <th>Status</th>
                    <th class="text-center">Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($program->fishPonds as $p)
                <tr>
                    <td>{{ $p->kode_kolam }}</td>
                    <td>{{ $p->nama_kolam }}</td>
                    <td>{{ $p->tipe_kolam }}</td>
                    <td>{{ number_format($p->luas_m2 ?? 0, 0) }} m²</td>
                    <td>{{ $p->kedalaman_m ?? '-' }} m</td>
                    <td>{{ number_format($p->volume_m3 ?? 0, 0) }} m³</td>
                    <td>{{ ucfirst($p->sumber_air ?? '-') }}</td>
                    <td><span class="status status-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    <td class="text-center">{{ $p->participants->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- RIWAYAT PERUBAHAN STATUS --}}
    @php
        $statusBadge = ['perencanaan' => 'badge-secondary', 'aktif' => 'badge-success', 'selesai' => 'badge-success', 'dibatalkan' => 'badge-danger'];
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
