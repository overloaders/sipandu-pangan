@extends('adminlte::page')

@section('title', 'Detail Kandang: ' . $livestockBarn->nama_kandang)

@section('content')
<div class="row">
    <div class="col-lg-8">
        {{-- DETAIL KANDANG --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-warehouse mr-2"></i>Detail Kandang</h5>
                <div>
                    <a href="{{ route('reports.livestock-barn', array_merge(['livestockBarn' => $livestockBarn->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-success" title="Export PDF"><i class="fas fa-file-pdf"></i> Export PDF</a>
                    <a href="{{ route('livestock-barns.edit', $livestockBarn) }}" class="btn btn-sm btn-warning" style="border-radius:8px;"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <a href="{{ route('livestock-barns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Kandang</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $livestockBarn->kode_kandang }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $livestockBarn->nama_kandang }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $livestockBarn->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Program Pangan</td><td>{{ $livestockBarn->foodProgram?->nama_program ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jenis Ternak</td><td><span class="badge badge-info">{{ ucfirst($livestockBarn->jenis_ternak) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tipe Kandang</td><td>{{ ucfirst(str_replace('_',' ',$livestockBarn->tipe_kandang)) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Kapasitas</td><td>{{ $livestockBarn->terisi ?? 0 }} / {{ $livestockBarn->kapasitas }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Luas</td><td>{{ number_format($livestockBarn->luas_m2,0,',','.') }} m²</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $livestockBarn->status=='aktif'?'success':'secondary' }}">{{ ucfirst($livestockBarn->status) }}</span></td></tr>
                </table></div>
            </div>
        </div>

        {{-- TERNAK --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Ternak ({{ $livestockBarn->inventories->count() }})</h5>
                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalAddLivestock"><i class="fas fa-plus"></i> Tambah Ternak</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Kode</th><th>Jenis</th><th>Jumlah</th><th>Berat</th><th>Tgl Masuk</th><th>Asal</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($livestockBarn->inventories as $inv)
                        <tr>
                            <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $inv->kode_ternak }}</span></td>
                            <td>{{ $inv->livestockType?->nama_jenis ?? '-' }}</td>
                            <td>{{ $inv->jumlah }}</td>
                            <td>{{ number_format($inv->berat_rata_kg ?? 0,1) }} kg</td>
                            <td>{{ $inv->tanggal_masuk ? \Carbon\Carbon::parse($inv->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $inv->asal ?? '-' }}</td>
                            <td><span class="badge badge-{{ $inv->status=='hidup'?'success':'danger' }}">{{ ucfirst($inv->status) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center" style="color:#94a3b8;padding:30px;">
                            <i class="fas fa-paw fa-2x mb-2 d-block"></i>
                            Belum ada ternak.<br>Klik <strong>"Tambah Ternak"</strong> untuk menambah.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- RIWAYAT KESEHATAN --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-heartbeat mr-2"></i>Riwayat Kesehatan ({{ $livestockBarn->healthRecords->count() }})</h5>
                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalAddHealth"><i class="fas fa-plus"></i> Tambah Rekam Medis</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Tanggal</th><th>Jenis</th><th>Hasil</th><th>Diagnosa</th><th>Tindakan</th><th>Biaya</th></tr></thead>
                    <tbody>
                        @forelse($livestockBarn->healthRecords as $hr)
                        <tr>
                            <td>{{ $hr->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                            <td>
                                @if($hr->jenis_pemeriksaan == 'vaksinasi')
                                    <span class="badge badge-info">💉 Vaksinasi</span>
                                @elseif($hr->jenis_pemeriksaan == 'pengobatan')
                                    <span class="badge badge-warning">💊 Pengobatan</span>
                                @elseif($hr->jenis_pemeriksaan == 'inspeksi')
                                    <span class="badge badge-primary">🔍 Inspeksi</span>
                                @elseif($hr->jenis_pemeriksaan == 'obat_cacing')
                                    <span class="badge badge-secondary">🐛 Obat Cacing</span>
                                @else
                                    <span class="badge badge-dark">🧬 {{ ucfirst(str_replace('_',' ',$hr->jenis_pemeriksaan)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($hr->hasil == 'sehat')
                                    <span class="badge badge-success">🟢 Sehat</span>
                                @elseif($hr->hasil == 'sakit')
                                    <span class="badge badge-danger">🔴 Sakit</span>
                                @elseif($hr->hasil == 'meninggal')
                                    <span class="badge badge-dark">⚫ Meninggal</span>
                                @else
                                    <span class="badge badge-warning">🟡 {{ ucfirst(str_replace('_',' ',$hr->hasil)) }}</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($hr->diagnosa, 40) ?? '-' }}</td>
                            <td>{{ Str::limit($hr->tindakan, 40) ?? '-' }}</td>
                            <td>Rp {{ number_format($hr->biaya ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center" style="color:#94a3b8;padding:30px;">
                            <i class="fas fa-heartbeat fa-2x mb-2 d-block"></i>
                            Belum ada rekam medis.<br>Klik <strong>"Tambah Rekam Medis"</strong> untuk mencatat.
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
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-chart-pie mr-2"></i>Ringkasan</h5></div>
            <div class="card-body">
                @php
                    $sehat = $livestockBarn->healthRecords->where('hasil', 'sehat')->count();
                    $sakit = $livestockBarn->healthRecords->where('hasil', 'sakit')->count();
                    $meninggal = $livestockBarn->healthRecords->where('hasil', 'meninggal')->count();
                    $totalHR = $livestockBarn->healthRecords->count();
                    $totalTernak = $livestockBarn->inventories->sum('jumlah');
                @endphp
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#dbeafe;"><i class="fas fa-paw text-primary"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Ternak</span>
                        <span class="info-box-number">{{ $totalTernak }} ekor</span>
                    </div>
                </div>
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#d1fae5;"><i class="fas fa-heart text-success"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sehat</span>
                        <span class="info-box-number">{{ $sehat }}</span>
                    </div>
                </div>
                <div class="info-box mb-2">
                    <span class="info-box-icon" style="background:#fee2e2;"><i class="fas fa-heart-broken text-danger"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sakit / Meninggal</span>
                        <span class="info-box-number">{{ $sakit + $meninggal }}</span>
                    </div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: {{ $totalHR > 0 ? ($sehat/$totalHR)*100 : 0 }}%"></div>
                </div>
                <small class="text-muted mt-1 d-block">{{ $totalHR }} pemeriksaan tercatat</small>
            </div>
        </div>

        {{-- PESERTA --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Peserta ({{ $livestockBarn->participants->count() }})</h5>
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addParticipantModal"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body">
                @forelse($livestockBarn->participants as $p)
                <div class="d-flex align-items-center justify-content-between mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:28px;height:28px;background:#dbeafe;color:#2563eb;font-size:10px;font-weight:600;">{{ strtoupper(substr($p->wbp->nama_lengkap ?? 'X',0,1)) }}</div>
                        <div>
                            <div style="font-weight:600;font-size:12px;">{{ $p->wbp?->nama_lengkap ?? '-' }}</div>
                            <div style="font-size:10px;color:#94a3b8;">{{ ucfirst($p->peran ?? '-') }} · <span class="badge badge-{{ $p->status=='aktif'?'success':'secondary' }}" style="font-size:9px;">{{ ucfirst($p->status) }}</span></div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('livestock-barns.participants.destroy', [$livestockBarn, $p]) }}" onsubmit="return confirm('Hapus peserta ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-times"></i></button>
                    </form>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada peserta</p> @endforelse
            </div>
        </div>

        {{-- AKSI --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-tools mr-2"></i>Aksi</h5></div>
            <div class="card-body">
                <button class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#modalAddLivestock">
                    <i class="fas fa-plus"></i> Tambah Ternak Baru
                </button>
                <button class="btn btn-danger btn-block mb-2" data-toggle="modal" data-target="#modalAddHealth">
                    <i class="fas fa-plus"></i> Tambah Rekam Medis
                </button>
                <hr>
                <form method="POST" action="{{ route('livestock-barns.destroy', $livestockBarn) }}" id="delete-form">
                    @csrf @method('DELETE')
                </form>
                <button type="button" class="btn btn-outline-danger btn-block" onclick="confirmDeleteForm('delete-form')">
                    <i class="fas fa-trash mr-1"></i> Hapus Kandang
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== MODAL TAMBAH TERNAK ==================== --}}
<div class="modal fade" id="modalAddLivestock" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('livestock-barns.store-inventory', $livestockBarn) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-paw"></i> Tambah Ternak Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Jenis Ternak *</strong></label>
                        <select name="livestock_type_id" class="form-control" required>
                            <option value="">-- Pilih Jenis Ternak --</option>
                            @foreach($livestockTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group"><label><strong>Jumlah *</strong></label><input type="number" name="jumlah" class="form-control" required min="1" placeholder="contoh: 50"></div>
                        </div>
                        <div class="col-6">
                            <div class="form-group"><label><strong>Berat Rata-rata (kg)</strong></label><input type="number" name="berat_rata_kg" class="form-control" step="0.1" placeholder="contoh: 2.5"></div>
                        </div>
                    </div>
                    <div class="form-group"><label><strong>Tanggal Masuk *</strong></label><input type="date" name="tanggal_masuk" class="form-control" required value="{{ now()->format('Y-m-d') }}"></div>
                    <div class="form-group"><label><strong>Asal</strong></label><input type="text" name="asal" class="form-control" placeholder="contoh: Pembibitan Sendiri, Toko Pakan"></div>
                    <div class="form-group"><label><strong>Harga Pokok (Rp)</strong></label><input type="number" name="harga_pokok" class="form-control" step="0.01" min="0"></div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan..."></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== MODAL TAMBAH REKAM MEDIS ==================== --}}
<div class="modal fade" id="modalAddHealth" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('livestock-barns.store-health-record', $livestockBarn) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-stethoscope"></i> Tambah Rekam Medis</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group"><label><strong>Tanggal *</strong></label><input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
                        </div>
                        <div class="col-6">
                            <div class="form-group"><label><strong>Biaya (Rp)</strong></label><input type="number" name="biaya" class="form-control" step="0.01" value="0"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong>Jenis Pemeriksaan *</strong></label>
                        <select name="jenis_pemeriksaan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="vaksinasi">💉 Vaksinasi</option>
                            <option value="pengobatan">💊 Pengobatan</option>
                            <option value="inspeksi">🔍 Inspeksi Rutin</option>
                            <option value="obat_cacing">🐛 Obat Cacing</option>
                            <option value="vitamin">🧬 Vitamin / Suplemen</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Hasil *</strong></label>
                        <select name="hasil" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="sehat">🟢 Sehat</option>
                            <option value="sakit">🔴 Sakit</option>
                            <option value="perlu_perawatan">🟡 Perlu Perawatan</option>
                            <option value="meninggal">⚫ Meninggal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>PIC / Dokter Hewan / Penanggung Jawab</strong></label>
                        <select name="wbp_pic_id" class="form-control">
                            <option value="">-- Pilih WBP (opsional) --</option>
                            @foreach($wbps as $wbp)
                            <option value="{{ $wbp->id }}">{{ $wbp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Diagnosa</strong></label><textarea name="diagnosa" class="form-control" rows="2" placeholder="Kondisi / penyakit yang ditemukan..."></textarea></div>
                    <div class="form-group"><label><strong>Tindakan</strong></label><textarea name="tindakan" class="form-control" rows="2" placeholder="Tindakan medis yang dilakukan..."></textarea></div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><input type="text" name="keterangan" class="form-control" placeholder="Catatan tambahan"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Peserta --}}
<div class="modal fade" id="addParticipantModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('livestock-barns.participants.store', $livestockBarn) }}">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Tambah Peserta Kandang</h5>
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
                                <input type="text" name="peran" class="form-control" placeholder="Contoh: Peternak, Pemberi Pakan" style="border-radius:8px;">
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
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status Kandang</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['aktif' => 'success', 'renovasi' => 'warning', 'nonaktif' => 'secondary'] as $st => $color)
                @if($st !== $livestockBarn->status)
                <form action="{{ route('livestock-barns.status', $livestockBarn) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status kandang menjadi {{ ucfirst($st) }}?');">
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
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status Kandang</h5>
        <a href="{{ route('reports.livestock-barn', $livestockBarn) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['aktif' => 'success', 'renovasi' => 'warning', 'nonaktif' => 'secondary']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($livestockBarn->statusHistories->sortByDesc('created_at') as $h)
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
