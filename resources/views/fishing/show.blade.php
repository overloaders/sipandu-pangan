@extends('adminlte::page')

@section('title', 'Detail Kolam: ' . $fishPond->nama_kolam)

@section('content')
<div class="row">
    <div class="col-lg-8">
        {{-- DETAIL KOLAM --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-water mr-2"></i>Detail Kolam</h5>
                <div>
                    <a href="{{ route('reports.fish-pond', array_merge(['fishPond' => $fishPond->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-success" title="Export PDF"><i class="fas fa-file-pdf"></i> Export PDF</a>
                    <a href="{{ route('fish-ponds.edit', $fishPond) }}" class="btn btn-sm btn-warning" style="border-radius:8px;"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <a href="{{ route('fish-ponds.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Kolam</td><td><span class="badge badge-primary" style="font-family:monospace;">{{ $fishPond->kode_kolam }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $fishPond->nama_kolam }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $fishPond->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Program Pangan</td><td>{{ $fishPond->foodProgram?->nama_program ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tipe</td><td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$fishPond->tipe_kolam)) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Luas</td><td>{{ number_format($fishPond->luas_m2,0,',','.') }} m²</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Kedalaman</td><td>{{ $fishPond->kedalaman_m ?? '-' }} m</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Volume</td><td>{{ $fishPond->volume_m3 ?? '-' }} m³</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Sumber Air</td><td>{{ ucfirst($fishPond->sumber_air) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $fishPond->status=='aktif'?'success':'secondary' }}">{{ ucfirst($fishPond->status) }}</span></td></tr>
                    @if($fishPond->keterangan)
                    <tr><td style="font-weight:600;color:#64748b;">Keterangan</td><td>{{ $fishPond->keterangan }}</td></tr>
                    @endif
                </table></div>
            </div>
        </div>

        {{-- STOK IKAN --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-fish mr-2"></i>Stok Ikan</h5>
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalAddFish"><i class="fas fa-plus"></i> Tambah Stok</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Kode</th><th>Jenis Ikan</th><th>Jumlah</th><th>Ukuran</th><th>Berat</th><th>Tgl Tebur</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse($fishPond->fishStocks as $fs)
                        <tr>
                            <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $fs->kode_stok_ikan }}</span></td>
                            <td>{{ $fs->jenis_ikan }}</td>
                            <td>{{ number_format($fs->jumlah) }}</td>
                            <td>{{ $fs->ukuran_rata_cm ?? '-' }} cm</td>
                            <td>{{ number_format($fs->berat_rata_gram ?? 0,1) }} g</td>
                            <td>{{ $fs->tanggal_tebur ? \Carbon\Carbon::parse($fs->tanggal_tebur)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $fs->status=='hidup'?'success':'danger' }}">{{ ucfirst($fs->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center" style="color:#94a3b8;">Belum ada stok ikan</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- KUALITAS AIR --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tint mr-2"></i>Riwayat Pemeriksaan Kualitas Air</h5>
                <a href="{{ route('fish-ponds.water-quality', $fishPond) }}" class="btn btn-sm btn-info"><i class="fas fa-list"></i> Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Tanggal</th><th>Jam</th><th>pH</th><th>Suhu</th><th>DO</th><th>Amonia</th><th>Kekeruhan</th><th>Kondisi</th></tr>
                    </thead>
                    <tbody>
                        @forelse($fishPond->waterQualityRecords->take(10) as $wq)
                        <tr>
                            <td>{{ $wq->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                            <td>{{ $wq->jam_pemeriksaan ?? '-' }}</td>
                            <td>
                                @if($wq->ph)
                                    @if($wq->ph >= 6.5 && $wq->ph <= 8.5)
                                        <span class="badge badge-success">{{ $wq->ph }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $wq->ph }}</span>
                                    @endif
                                @else - @endif
                            </td>
                            <td>{{ $wq->suhu_celsius ?? '-' }}°C</td>
                            <td>{{ $wq->dissolved_oxygen_mgl ?? '-' }} mg/L</td>
                            <td>
                                @if($wq->amonia_mgl)
                                    @if($wq->amonia_mgl < 0.5)
                                        <span class="badge badge-success">{{ $wq->amonia_mgl }}</span>
                                    @elseif($wq->amonia_mgl < 1.0)
                                        <span class="badge badge-warning">{{ $wq->amonia_mgl }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $wq->amonia_mgl }}</span>
                                    @endif
                                @else - @endif
                            </td>
                            <td>{{ $wq->kekeruhan_ntu ?? '-' }} NTU</td>
                            <td>
                                @if($wq->kondisi_air == 'baik')
                                    <span class="badge badge-success">Baik</span>
                                @elseif($wq->kondisi_air == 'kurang_baik')
                                    <span class="badge badge-warning">Kurang Baik</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst(str_replace('_', ' ', $wq->kondisi_air)) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center" style="color:#94a3b8;">Belum ada pemeriksaan kualitas air</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- FORM TAMBAH KUALITAS AIR --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-flask mr-2"></i>Tambah Pemeriksaan</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('fish-ponds.store-water-quality', $fishPond) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label><strong>Tanggal *</strong></label>
                        <input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Jam</strong></label>
                        <input type="time" name="jam_pemeriksaan" class="form-control" value="{{ now()->format('H:i') }}">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>pH</strong></label>
                                <input type="number" name="ph" class="form-control" step="0.1" min="0" max="14" placeholder="6.5 - 8.5">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Suhu (°C)</strong></label>
                                <input type="number" name="suhu_celsius" class="form-control" step="0.1" placeholder="25 - 30">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>DO (mg/L)</strong></label>
                                <input type="number" name="dissolved_oxygen_mgl" class="form-control" step="0.1" placeholder="≥ 5">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Amonia (mg/L)</strong></label>
                                <input type="number" name="amonia_mgl" class="form-control" step="0.01" placeholder="< 0.5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Nitrit (mg/L)</strong></label>
                                <input type="number" name="nitrit_mgl" class="form-control" step="0.01">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Nitrat (mg/L)</strong></label>
                                <input type="number" name="nitrat_mgl" class="form-control" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Kekeruhan (NTU)</strong></label>
                                <input type="number" name="kekeruhan_ntu" class="form-control" step="0.1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><strong>Salinitas (ppt)</strong></label>
                                <input type="number" name="salinitas_ppt" class="form-control" step="0.1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong>Kondisi Air *</strong></label>
                        <select name="kondisi_air" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="baik">🟢 Baik</option>
                            <option value="kurang_baik">🟡 Kurang Baik</option>
                            <option value="buruk">🟠 Buruk</option>
                            <option value="sangat_buruk">🔴 Sangat Buruk</option>
                        </select>
                    </div>                    <div class="form-group">
                        <label><strong>Pemeriksa</strong></label>
                        <select name="pemeriksa_id" class="form-control">
                            <option value="">-- Pilih (opsional) --</option>
                            @foreach($wbps as $wbp)
                            <option value="{{ $wbp->id }}">{{ $wbp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Tindakan yang Diperlukan</strong></label><textarea name="tindakan_perlukan" class="form-control" rows="2" placeholder="Contoh: Tambah aerasi, kurangi pakan..."></textarea></div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan Pemeriksaan</button>
                </form>
            </div>
        </div>

        {{-- PETUNJUK STANDAR AIR --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-info-circle mr-2"></i>Standar Kualitas Air</h5>
            </div>
            <div class="card-body" style="font-size:12px;">
                <div class="table-responsive"><table class="table table-sm table-bordered">
                    <thead><tr><th>Parameter</th><th>Standar</th></tr></thead>
                    <tbody>
                        <tr><td>pH</td><td>6.5 - 8.5</td></tr>
                        <tr><td>Suhu</td><td>25 - 30 °C</td></tr>
                        <tr><td>DO (Oksigen Terlarut)</td><td>≥ 5 mg/L</td></tr>
                        <tr><td>Amonia (NH₃)</td><td>< 0.5 mg/L</td></tr>
                        <tr><td>Nitrit (NO₂)</td><td>< 0.1 mg/L</td></tr>
                        <tr><td>Nitrat (NO₃)</td><td>< 50 mg/L</td></tr>
                        <tr><td>Kekeruhan</td><td>< 25 NTU</td></tr>
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- PESERTA --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Peserta ({{ $fishPond->participants->count() }})</h5>
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addParticipantModal"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body">
                @forelse($fishPond->participants as $p)
                <div class="d-flex align-items-center justify-content-between mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:28px;height:28px;background:#dbeafe;color:#2563eb;font-size:10px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:12px;">{{ $p->wbp?->nama_lengkap ?? '-' }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }} · <span class="badge badge-{{ $p->status=='aktif'?'success':'secondary' }}" style="font-size:9px;">{{ ucfirst($p->status) }}</span></div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('fish-ponds.participants.destroy', [$fishPond, $p]) }}" onsubmit="return confirm('Hapus peserta ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-times"></i></button>
                    </form>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada peserta</p> @endforelse
            </div>
        </div>

        {{-- AKSI --}}
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tools mr-2"></i>Aksi</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('fish-ponds.destroy', $fishPond) }}" id="delete-form">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="btn btn-danger btn-block" style="border-radius:8px;" onclick="confirmDeleteForm('delete-form')">
                    <i class="fas fa-trash mr-1"></i> Hapus Kolam
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH STOK IKAN --}}
<div class="modal fade" id="modalAddFish" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('fish-ponds.store-fish-stock', $fishPond) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-fish"></i> Tambah Stok Ikan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jenis Ikan *</label>
                        <input type="text" name="jenis_ikan" class="form-control" required placeholder="contoh: Lele, Nila, Gurame">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Jumlah *</label>
                                <input type="number" name="jumlah" class="form-control" required min="1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Tanggal Tebur *</label>
                                <input type="date" name="tanggal_tebur" class="form-control" required value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Ukuran Rata-rata (cm)</label>
                                <input type="number" name="ukuran_rata_cm" class="form-control" step="0.1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Berat Rata-rata (gram)</label>
                                <input type="number" name="berat_rata_gram" class="form-control" step="0.1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                            <option value="benih">Benih</option>
                            <option value="pembesaran">Pembesaran</option>
                            <option value="siap_panen">Siap Panen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga Benih per Ekor</label>
                        <input type="number" name="harga_benih" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Peserta --}}
<div class="modal fade" id="addParticipantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('fish-ponds.participants.store', $fishPond) }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Peserta Kolam</h5>
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
                                <input type="text" name="peran" class="form-control" placeholder="Contoh: Pemancing, Pemberi Pakan" style="border-radius:8px;">
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

{{-- UBAH STATUS --}}
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status Kolam</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['aktif' => 'success', 'kering' => 'warning', 'renovasi' => 'secondary', 'nonaktif' => 'dark'] as $st => $color)
                @if($st !== $fishPond->status)
                <form action="{{ route('fish-ponds.status', $fishPond) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status kolam menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status Kolam</h5>
        <a href="{{ route('reports.fish-pond', $fishPond) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['aktif' => 'success', 'kering' => 'warning', 'renovasi' => 'secondary', 'nonaktif' => 'dark']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($fishPond->statusHistories->sortByDesc('created_at') as $h)
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
