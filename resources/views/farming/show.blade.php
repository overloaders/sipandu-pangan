@extends('adminlte::page')

@section('title', 'Detail Rencana: ' . $farmingPlan->kode_rencana)

@section('content')
<div class="row">
    <div class="col-lg-8">
        {{-- DETAIL RENCANA --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tractor mr-2"></i>Detail Rencana Kerja</h5>
                <div>
                    <a href="{{ route('reports.farming-plan', array_merge(['farmingPlan' => $farmingPlan->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-success" title="Export PDF"><i class="fas fa-file-pdf"></i> Export PDF</a>
                    <a href="{{ route('farming-plans.edit', $farmingPlan) }}" class="btn btn-sm btn-warning" style="border-radius:8px;"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <a href="{{ route('farming-plans.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Rencana</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $farmingPlan->kode_rencana }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Program</td><td>{{ $farmingPlan->foodProgram?->nama_program ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Lahan</td><td>{{ $farmingPlan->landPlot?->nama_lahan ?? '-' }} ({{ number_format($farmingPlan->landPlot?->luas_m2 ?? 0,0,',','.') }} m²)</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanaman</td><td style="font-weight:700;">{{ $farmingPlan->cropType?->nama_tanaman ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jumlah Tanaman</td><td>{{ number_format($farmingPlan->jumlah_tanaman) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Metode</td><td><span class="badge badge-info">{{ ucfirst($farmingPlan->metode_budi_daya) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td>
                        <td>
                            @if($farmingPlan->status == 'proses_tanam')
                                <span class="badge badge-warning">Proses Tanam</span>
                            @elseif($farmingPlan->status == 'tumbuh')
                                <span class="badge badge-success">Tumbuh</span>
                            @elseif($farmingPlan->status == 'siap_panen')
                                <span class="badge badge-primary">Siap Panen</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$farmingPlan->status)) }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tgl Tanam Rencana</td><td>{{ $farmingPlan->tanggal_tanam_rencana?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tgl Tanam Aktual</td><td>{{ $farmingPlan->tanggal_tanam_aktual?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tgl Panen Rencana</td><td>{{ $farmingPlan->tanggal_panen_rencana?->format('d M Y') ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Luas Tanam</td><td>{{ number_format($farmingPlan->luas_tanam_m2 ?? 0,0,',','.') }} m²</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Estimasi Hasil</td><td>{{ number_format($farmingPlan->estimasi_hasil_kg ?? 0,0,',','.') }} kg</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Estimasi Pendapatan</td><td>Rp {{ number_format($farmingPlan->estimasi_pendapatan ?? 0,0,',','.') }}</td></tr>
                </table></div>
            </div>
        </div>

        {{-- LOG KEGIATAN --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-list mr-2"></i>Log Kegiatan ({{ $farmingPlan->activities->count() }})</h5>
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalAddActivity"><i class="fas fa-plus"></i> Tambah Kegiatan</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Tanggal</th><th>Kegiatan</th><th>PIC</th><th>Biaya</th><th>Keterangan</th></tr></thead>
                    <tbody>
                        @forelse($farmingPlan->activities->sortByDesc('tanggal_kegiatan') as $a)
                        <tr>
                            <td>{{ $a->tanggal_kegiatan->format('d/m/Y') }}</td>
                            <td>
                                @if($a->jenis_kegiatan == 'pengolahan_tanah')
                                    <span class="badge badge-warning">🚜 Pengolahan Tanah</span>
                                @elseif($a->jenis_kegiatan == 'penanaman')
                                    <span class="badge badge-success">🌱 Penanaman</span>
                                @elseif($a->jenis_kegiatan == 'pemupukan')
                                    <span class="badge badge-info">🧪 Pemupukan</span>
                                @elseif($a->jenis_kegiatan == 'penyiraman')
                                    <span class="badge badge-primary">💧 Penyiraman</span>
                                @elseif($a->jenis_kegiatan == 'penyiangan')
                                    <span class="badge badge-secondary">🌿 Penyiangan</span>
                                @elseif($a->jenis_kegiatan == 'hama_panen')
                                    <span class="badge badge-danger">🐛 Pengendalian Hama</span>
                                @else
                                    <span class="badge badge-dark">📋 {{ ucfirst(str_replace('_',' ',$a->jenis_kegiatan)) }}</span>
                                @endif
                            </td>
                            <td>{{ $a->picWbp?->nama_lengkap ?? '-' }}</td>
                            <td>Rp {{ number_format($a->biaya ?? 0,0,',','.') }}</td>
                            <td style="font-size:11px;">{{ Str::limit($a->keterangan, 50) ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center" style="color:#94a3b8;padding:30px;">
                            <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                            Belum ada kegiatan.<br>Klik tombol <strong>"Tambah Kegiatan"</strong> untuk mencatat.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- DATA PANEN --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-apple-alt mr-2"></i>Data Panen ({{ $farmingPlan->harvests->count() }})</h5>
                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddHarvest"><i class="fas fa-plus"></i> Catat Panen</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Kode</th><th>Tanggal</th><th>Berat</th><th>Kualitas</th><th>Harga/kg</th><th>Total</th></tr></thead>
                    <tbody>
                        @forelse($farmingPlan->harvests as $h)
                        <tr>
                            <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $h->kode_panen }}</span></td>
                            <td>{{ $h->tanggal_panen->format('d/m/Y') }}</td>
                            <td><strong>{{ number_format($h->total_berat_kg,1) }} kg</strong></td>
                            <td>
                                @if($h->kualitas == 'premium')
                                    <span class="badge badge-success">⭐ Premium</span>
                                @elseif($h->kualitas == 'baik')
                                    <span class="badge badge-info">👍 Baik</span>
                                @elseif($h->kualitas == 'cukup')
                                    <span class="badge badge-warning">Cukup</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($h->kualitas) }}</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($h->harga_jual_per_kg ?? 0,0,',','.') }}</td>
                            <td><strong style="color:#059669;">Rp {{ number_format($h->total_pendapatan,0,',','.') }}</strong></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center" style="color:#94a3b8;padding:30px;">
                            <i class="fas fa-seedling fa-2x mb-2 d-block"></i>
                            Belum ada data panen.<br>Klik tombol <strong>"Catat Panen"</strong> untuk menambah.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- RINGKASAN --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-chart-bar mr-2"></i>Ringkasan</h5></div>
            <div class="card-body">
                @php
                    $totalKegiatan = $farmingPlan->activities->count();
                    $totalBiaya = $farmingPlan->activities->sum('biaya');
                    $totalPanen = $farmingPlan->harvests->count();
                    $totalBerat = $farmingPlan->harvests->sum('total_berat_kg');
                    $totalPendapatan = $farmingPlan->harvests->sum('total_pendapatan');
                    $keuntungan = $totalPendapatan - $totalBiaya;
                @endphp
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#dbeafe;"><i class="fas fa-clipboard-list text-primary"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Kegiatan</span>
                        <span class="info-box-number">{{ $totalKegiatan }}</span>
                    </div>
                </div>
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#fee2e2;"><i class="fas fa-money-bill-wave text-danger"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Biaya</span>
                        <span class="info-box-number">Rp {{ number_format($totalBiaya,0,',','.') }}</span>
                    </div>
                </div>
                <hr>
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#d1fae5;"><i class="fas fa-apple-alt text-success"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Panen</span>
                        <span class="info-box-number">{{ $totalPanen }}x | {{ number_format($totalBerat,1) }} kg</span>
                    </div>
                </div>
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#d1fae5;"><i class="fas fa-dollar-sign text-success"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pendapatan</span>
                        <span class="info-box-number">Rp {{ number_format($totalPendapatan,0,',','.') }}</span>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>{{ $keuntungan >= 0 ? '💵 Keuntungan' : '📉 Rugi' }}</strong>
                    <strong style="color:{{ $keuntungan >= 0 ? '#059669' : '#dc2626' }};">Rp {{ number_format(abs($keuntungan),0,',','.') }}</strong>
                </div>
            </div>
        </div>

        {{-- PESERTA --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Peserta ({{ $farmingPlan->participants->count() }})</h5>
                @if(!in_array($farmingPlan->status, ['selesai', 'panen']))
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addParticipantModal"><i class="fas fa-plus mr-1"></i> Tambah</button>
                @endif
            </div>
            <div class="card-body">
                @forelse($farmingPlan->participants as $p)
                <div class="d-flex align-items-center justify-content-between mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:28px;height:28px;background:#dbeafe;color:#2563eb;font-size:10px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:12px;">{{ $p->wbp?->nama_lengkap ?? '-' }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }} · <span class="badge badge-{{ $p->status=='aktif'?'success':'secondary' }}" style="font-size:9px;">{{ ucfirst($p->status) }}</span></div>
                        </div>
                    </div>
                    @if(!in_array($farmingPlan->status, ['selesai', 'panen']))
                    <form method="POST" action="{{ route('farming-plans.participants.destroy', [$farmingPlan, $p]) }}" onsubmit="return confirm('Hapus peserta ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-times"></i></button>
                    </form>
                    @endif
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada peserta</p> @endforelse
            </div>
        </div>

        {{-- AKSI --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-tools mr-2"></i>Aksi</h5></div>
            <div class="card-body">
                <button class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#modalAddActivity">
                    <i class="fas fa-plus"></i> Tambah Kegiatan Baru
                </button>
                <button class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#modalAddHarvest">
                    <i class="fas fa-plus"></i> Catat Panen Baru
                </button>
                <hr>
                <form method="POST" action="{{ route('farming-plans.destroy', $farmingPlan) }}" id="delete-form">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="btn btn-danger btn-block" onclick="confirmDeleteForm('delete-form')">
                    <i class="fas fa-trash mr-1"></i> Hapus Rencana
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MODAL TAMBAH KEGIATAN ==================== --}}
<div class="modal fade" id="modalAddActivity" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('farming-plans.store-activity', $farmingPlan) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-clipboard-list"></i> Tambah Kegiatan Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Tanggal Kegiatan *</strong></label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Jenis Kegiatan *</strong></label>
                        <select name="jenis_kegiatan" class="form-control" required>
                            <option value="">-- Pilih Jenis Kegiatan --</option>
                            <option value="pengolahan_tanah">🚜 Pengolahan Tanah</option>
                            <option value="penanaman">🌱 Penanaman</option>
                            <option value="pemupukan">🧪 Pemupukan / Pupuk</option>
                            <option value="penyiraman">💧 Penyiraman</option>
                            <option value="penyiangan">🌿 Penyiangan / Gulma</option>
                            <option value="hama_panen">🐛 Pengendalian Hama & Penyakit</option>
                            <option value="perawatan_lain">📋 Perawatan Lainnya</option>
                        </select>
                    </div>                    <div class="form-group"><label><strong>Biaya (Rp)</strong></label><input type="number" name="biaya" class="form-control" step="0.01" value="0" min="0"></div>
                    <div class="form-group">
                        <label><strong>PIC / Penanggung Jawab (WBP)</strong></label>
                        <select name="pic_wbp_id" class="form-control">
                            <option value="">-- Pilih WBP (opsional) --</option>
                            @foreach($wbps as $wbp)
                            <option value="{{ $wbp->id }}">{{ $wbp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Keterangan / Catatan</strong></label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pupuk urea 50kg, pupuk NPK 30kg. Dilakukan 2x seminggu."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== MODAL CATAT PANEN ==================== --}}
<div class="modal fade" id="modalAddHarvest" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('farming-plans.store-harvest', $farmingPlan) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-seedling"></i> Catat Hasil Panen</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Tanggal Panen *</strong></label>
                        <input type="date" name="tanggal_panen" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Total Berat (kg) *</strong></label>
                        <input type="number" name="total_berat_kg" class="form-control" step="0.01" required min="0.01" placeholder="contoh: 150.5">
                    </div>
                    <div class="form-group">
                        <label><strong>Harga Jual per kg (Rp)</strong></label>
                        <input type="number" name="harga_jual_per_kg" class="form-control" step="0.01" min="0" placeholder="contoh: 5000">
                        <small class="text-muted">Total pendapatan otomatis = Berat × Harga/kg</small>
                    </div>
                    <div class="form-group">
                        <label><strong>Kualitas Hasil *</strong></label>
                        <select name="kualitas" class="form-control" required>
                            <option value="">-- Pilih Kualitas --</option>
                            <option value="premium">⭐ Premium (Kualitas Terbaik)</option>
                            <option value="baik">👍 Baik</option>
                            <option value="cukup">👌 Cukup</option>
                            <option value="rendah">📉 Rendah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Keterangan</strong></label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Kondisi panen, cuaca, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Panen</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- UBAH STATUS --}}
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['direncanakan' => 'secondary', 'proses_tanam' => 'info', 'tumbuh' => 'success', 'siap_panen' => 'warning', 'panen' => 'dark', 'selesai' => 'success'] as $st => $color)
                @if($st !== $farmingPlan->status)
                <form action="{{ route('farming-plans.status', $farmingPlan) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst(str_replace('_',' ',$st)) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst(str_replace('_',' ',$st)) }}</button>
                </form>
                @endif
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.farming-plan', $farmingPlan) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['direncanakan' => 'secondary', 'proses_tanam' => 'info', 'tumbuh' => 'success', 'siap_panen' => 'warning', 'panen' => 'dark', 'selesai' => 'success']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($farmingPlan->statusHistories->sortByDesc('created_at') as $h)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge badge-{{ $sc[$h->status_lama] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_lama)) }}</span>
                            <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                        @endif
                        <span class="badge badge-{{ $sc[$h->status_baru] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_baru)) }}</span>
                    </td>
                    <td style="font-size:12px;">{{ $h->changedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center py-3 text-muted">Belum ada perubahan status.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection

{{-- ==================== MODAL TAMBAH PESERTA ==================== --}}
@if(!in_array($farmingPlan->status, ['selesai', 'panen']))
<div class="modal fade" id="addParticipantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('farming-plans.participants.store', $farmingPlan) }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Peserta Rencana Kerja</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>WBP *</strong></label>
                        <select name="wbp_id" class="form-control" required style="border-radius:8px;">
                            <option value="">-- Pilih WBP --</option>
                            @foreach($wbps as $wbp)
                            <option value="{{ $wbp->id }}">{{ $wbp->nomor_induk }} - {{ $wbp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Tanggal Mulai *</strong></label>
                                <input type="date" name="tanggal_mulai" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Tanggal Selesai</strong></label>
                                <input type="date" name="tanggal_selesai" class="form-control" style="border-radius:8px;">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Status *</strong></label>
                                <select name="status" class="form-control" required style="border-radius:8px;">
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Peran</strong></label>
                                <input type="text" name="peran" class="form-control" placeholder="Contoh: Petani, Panen" style="border-radius:8px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong>Keterangan</strong></label>
                        <textarea name="keterangan" class="form-control" rows="2" style="border-radius:8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
