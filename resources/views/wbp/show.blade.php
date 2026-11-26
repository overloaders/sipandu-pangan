@extends('adminlte::master')
@section('title', 'Detail WBP: ' . $wbp->nama_lengkap)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-id-card mr-2"></i>Data Diri</h5>
                <div>
                    <a href="{{ route('wbp.edit', $wbp) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('wbp.destroy', $wbp) }}','{{ $wbp->nama_lengkap }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                    <div>
                        <a href="{{ route('reports.wbp-detail', array_merge([$wbp->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                        <a href="{{ route('wbp.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="150" style="font-weight:600;color:#64748b;">Nomor Induk</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $wbp->nomor_induk }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama Lengkap</td><td style="font-weight:700;">{{ $wbp->nama_lengkap }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">NIK</td><td>{{ $wbp->nik }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jenis Kelamin</td><td><span class="badge badge-{{ $wbp->jenis_kelamin=='L'?'info':'danger' }}">{{ $wbp->jenis_kelamin }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanggal Lahir</td><td>{{ $wbp->tanggal_lahir->format('d M Y') }} ({{ $wbp->tanggal_lahir->age }} tahun)</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Agama</td><td>{{ $wbp->agama }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $wbp->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Blok / Kamar</td><td><span class="badge badge-secondary">{{ $wbp->block?->kode_blok ?? '-' }} / {{ $wbp->room?->kode_kamar ?? '-' }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td>@php $sc = ['aktif'=>'success','dikeluarkan'=>'danger','pindah'=>'warning','meninggal'=>'dark','bebas'=>'info']; @endphp <span class="badge badge-{{ $sc[$wbp->status] ?? 'secondary' }}">{{ ucfirst($wbp->status) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanggal Masuk</td><td>{{ $wbp->tanggal_masuk->format('d M Y') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Masa Hukuman</td><td>{{ number_format($wbp->masa_hukuman_hari ?? 0) }} hari (s/d {{ $wbp->masa_hukuman_selesai?->format('d M Y') ?? '-' }})</td></tr>
                </table></div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-file-alt mr-2"></i>Dokumen</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalAddDocument"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body p-0">
                @if($wbp->documents->count() > 0)
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Jenis</th><th>Nama / Nomor</th><th>Tanggal</th><th>Keterangan</th><th width="70"></th></tr></thead>
                <tbody>
                @foreach($wbp->documents as $doc)
                <tr>
                    <td><span class="badge badge-info">{{ $doc->jenis_dokumen }}</span></td>
                    <td>
                        @if($doc->file_path && Storage::disk('public')->exists($doc->file_path))
                        <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" style="font-weight:600;"><i class="fas fa-paperclip mr-1" style="font-size:11px;"></i>{{ $doc->nama_dokumen }}</a>
                        @else
                        <span style="font-weight:600;">{{ $doc->nama_dokumen }}</span>
                        @endif
                        @if($doc->nomor_dokumen)<div style="font-size:11px;color:#94a3b8;">No. {{ $doc->nomor_dokumen }}</div>@endif
                    </td>
                    <td style="white-space:nowrap;">{{ $doc->tanggal_dokumen?->format('d/m/Y') ?? '-' }}</td>
                    <td style="font-size:12px;color:#64748b;max-width:200px;">@if($doc->keterangan)<span title="{{ $doc->keterangan }}">{{ Str::limit($doc->keterangan, 60) }}</span>@else <span style="color:#cbd5e1;">-</span> @endif</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalEditDocument"
                                data-url="{{ route('wbp.update-document', [$wbp, $doc]) }}"
                                data-jenis="{{ $doc->jenis_dokumen }}"
                                data-nama="{{ $doc->nama_dokumen }}"
                                data-nomor="{{ $doc->nomor_dokumen }}"
                                data-tanggal="{{ $doc->tanggal_dokumen?->format('Y-m-d') }}"
                                data-ket="{{ $doc->keterangan }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('wbp.destroy-document', [$wbp, $doc]) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach</tbody></table></div>
                @else <p class="text-center py-3" style="color:#94a3b8;">Belum ada dokumen</p> @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Family --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-users mr-2"></i>Keluarga</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalAddFamily"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body">
                @forelse($wbp->familyMembers as $fm)
                <div class="mb-2 p-2" style="background:#f8fafc;border-radius:8px;">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mr-2" style="width:34px;height:34px;background:#dbeafe;color:#2563eb;font-size:12px;font-weight:600;">{{ strtoupper(substr($fm->nama_lengkap,0,1)) }}</div>
                        <div class="flex-grow-1">
                            <div style="font-weight:600;font-size:13px;">{{ $fm->nama_lengkap }} @if($fm->is_wali)<span class="badge badge-warning" style="font-size:9px;">WALI</span>@endif</div>
                            <div><span class="badge badge-light" style="font-size:10px;">{{ ucfirst($fm->hubungan) }}</span></div>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalEditFamily"
                                data-url="{{ route('wbp.update-family-member', [$wbp, $fm]) }}"
                                data-nama="{{ $fm->nama_lengkap }}"
                                data-hubungan="{{ $fm->hubungan }}"
                                data-nik="{{ $fm->nik }}"
                                data-hp="{{ $fm->nomor_hp }}"
                                data-alamat="{{ $fm->alamat }}"
                                data-wali="{{ $fm->is_wali ? 1 : 0 }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('wbp.destroy-family-member', [$wbp, $fm]) }}" method="POST" onsubmit="return confirm('Hapus anggota keluarga ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div style="font-size:11px;color:#475569;margin-top:6px;">
                        @if($fm->nik)<div><i class="fas fa-id-card mr-1" style="color:#94a3b8;width:14px;"></i>NIK: {{ $fm->nik }}</div>@endif
                        @if($fm->nomor_hp)<div><i class="fas fa-phone mr-1" style="color:#94a3b8;width:14px;"></i>HP: {{ $fm->nomor_hp }}</div>@endif
                        @if($fm->alamat)<div><i class="fas fa-map-marker-alt mr-1" style="color:#94a3b8;width:14px;"></i>Alamat: {{ $fm->alamat }}</div>@endif
                        @if(!$fm->nik && !$fm->nomor_hp && !$fm->alamat)<div style="color:#cbd5e1;">-</div>@endif
                    </div>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada data keluarga</p> @endforelse
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-chart-bar mr-2"></i>Statistik</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span style="font-size:12px;color:#64748b;">Program Diikuti</span><span class="badge badge-success">{{ $wbp->programParticipations->count() }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span style="font-size:12px;color:#64748b;">Pelatihan</span><span class="badge badge-info">{{ $wbp->trainingParticipations->count() }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span style="font-size:12px;color:#64748b;">Sertifikat</span><span class="badge badge-warning">{{ $wbp->certifications->count() }}</span></div>
                <div class="d-flex justify-content-between"><span style="font-size:12px;color:#64748b;">Dokumen</span><span class="badge badge-secondary">{{ $wbp->documents->count() }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH DOKUMEN --}}
<div class="modal fade" id="modalAddDocument" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('wbp.store-document', $wbp) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-file-alt"></i> Tambah Dokumen</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Jenis Dokumen *</strong></label>
                        <select name="jenis_dokumen" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(['KTP','SK','Putusan','Berita Acara','BAP','Surat','Sertifikat','Kartu','Lainnya'] as $jd)
                            <option value="{{ $jd }}" {{ old('jenis_dokumen') == $jd ? 'selected' : '' }}>{{ $jd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Nama Dokumen *</strong></label><input type="text" name="nama_dokumen" class="form-control" required placeholder="contoh: KTP a.n. Budi Santoso"></div>
                    <div class="form-group"><label><strong>Nomor Dokumen</strong></label><input type="text" name="nomor_dokumen" class="form-control" placeholder="contoh: 3174xxxxxxxxxxxx"></div>
                    <div class="form-group"><label><strong>Tanggal Dokumen</strong></label><input type="date" name="tanggal_dokumen" class="form-control" value="{{ old('tanggal_dokumen') }}"></div>
                    <div class="form-group"><label><strong>File (PDF / Gambar, opsional)</strong></label><input type="file" name="file" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png"></div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH KELUARGA --}}
<div class="modal fade" id="modalAddFamily" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('wbp.store-family-member', $wbp) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-users"></i> Tambah Anggota Keluarga</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group"><label><strong>Nama Lengkap *</strong></label><input type="text" name="nama_lengkap" class="form-control" required></div>
                    <div class="form-group">
                        <label><strong>Hubungan *</strong></label>
                        <select name="hubungan" class="form-control" required>
                            <option value="">-- Pilih Hubungan --</option>
                            @foreach(['suami','istri','ayah','ibu','anak','saudara','lainnya'] as $hb)
                            <option value="{{ $hb }}" {{ old('hubungan') == $hb ? 'selected' : '' }}>{{ ucfirst($hb) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>NIK</strong></label><input type="text" name="nik" class="form-control" maxlength="16" inputmode="numeric" placeholder="16 digit"><small class="form-text text-muted">Harus unik — tidak boleh sama dengan anggota keluarga lain.</small></div>
                    <div class="form-group"><label><strong>Nomor HP</strong></label><input type="text" name="nomor_hp" class="form-control" maxlength="16" inputmode="tel" placeholder="08xxxxxxxxxx atau +628xxxxxxxxxx"><small class="form-text text-muted">Format: 08xxxxxxxxxx / +628xxxxxxxxxx.</small></div>
                    <div class="form-group"><label><strong>Alamat</strong></label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                    <div class="form-check"><input type="checkbox" name="is_wali" value="1" class="form-check-input" id="isWali"><label class="form-check-label" for="isWali">Penanggung Jawab / Wali</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT DOKUMEN --}}
<div class="modal fade" id="modalEditDocument" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Dokumen</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Jenis Dokumen *</strong></label>
                        <select name="jenis_dokumen" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(['KTP','SK','Putusan','Berita Acara','BAP','Surat','Sertifikat','Kartu','Lainnya'] as $jd)
                            <option value="{{ $jd }}">{{ $jd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Nama Dokumen *</strong></label><input type="text" name="nama_dokumen" class="form-control" required></div>
                    <div class="form-group"><label><strong>Nomor Dokumen</strong></label><input type="text" name="nomor_dokumen" class="form-control"></div>
                    <div class="form-group"><label><strong>Tanggal Dokumen</strong></label><input type="date" name="tanggal_dokumen" class="form-control"></div>
                    <div class="form-group"><label><strong>Ganti File (PDF / Gambar, opsional)</strong></label><input type="file" name="file" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png"></div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT KELUARGA --}}
<div class="modal fade" id="modalEditFamily" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Anggota Keluarga</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group"><label><strong>Nama Lengkap *</strong></label><input type="text" name="nama_lengkap" class="form-control" required></div>
                    <div class="form-group">
                        <label><strong>Hubungan *</strong></label>
                        <select name="hubungan" class="form-control" required>
                            <option value="">-- Pilih Hubungan --</option>
                            @foreach(['suami','istri','ayah','ibu','anak','saudara','lainnya'] as $hb)
                            <option value="{{ $hb }}">{{ ucfirst($hb) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>NIK</strong></label><input type="text" name="nik" class="form-control" maxlength="16" inputmode="numeric" placeholder="16 digit"><small class="form-text text-muted">Harus unik — tidak boleh sama dengan anggota keluarga lain.</small></div>
                    <div class="form-group"><label><strong>Nomor HP</strong></label><input type="text" name="nomor_hp" class="form-control" maxlength="16" inputmode="tel" placeholder="08xxxxxxxxxx atau +628xxxxxxxxxx"><small class="form-text text-muted">Format: 08xxxxxxxxxx / +628xxxxxxxxxx.</small></div>
                    <div class="form-group"><label><strong>Alamat</strong></label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                    <div class="form-check"><input type="checkbox" name="is_wali" value="1" class="form-check-input" id="isWaliEdit"><label class="form-check-label" for="isWaliEdit">Penanggung Jawab / Wali</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan</button>
                </div>            </form>
        </div>
    </div>
</div>

{{-- UBAH STATUS --}}
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @php $wbpColors = ['aktif' => 'success', 'dikeluarkan' => 'danger', 'pindah' => 'warning', 'meninggal' => 'dark', 'bebas' => 'info']; @endphp
            @foreach($wbpColors as $st => $color)
                @if($st !== $wbp->status)
                <form action="{{ route('wbp.status', $wbp) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- RIWAYAT STATUS --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.wbp-detail', $wbp) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $wbpColors2 = ['aktif' => 'success', 'dikeluarkan' => 'danger', 'pindah' => 'warning', 'meninggal' => 'dark', 'bebas' => 'info']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($wbp->statusHistories->sortByDesc('created_at') as $h)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge badge-{{ $wbpColors2[$h->status_lama] ?? 'secondary' }}">{{ ucfirst($h->status_lama) }}</span>
                            <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                        @endif
                        <span class="badge badge-{{ $wbpColors2[$h->status_baru] ?? 'secondary' }}">{{ ucfirst($h->status_baru) }}</span>
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


@section('extra_js')
<script>
$('#modalEditDocument').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    form.attr('action', btn.data('url'));
    form.find('[name=jenis_dokumen]').val(btn.data('jenis'));
    form.find('[name=nama_dokumen]').val(btn.data('nama'));
    form.find('[name=nomor_dokumen]').val(btn.data('nomor') || '');
    form.find('[name=tanggal_dokumen]').val(btn.data('tanggal') || '');
    form.find('[name=keterangan]').val(btn.data('ket') || '');
    form.find('[name=file]').val('');
});

$('#modalEditFamily').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    form.attr('action', btn.data('url'));
    form.find('[name=nama_lengkap]').val(btn.data('nama'));
    form.find('[name=hubungan]').val(btn.data('hubungan'));
    form.find('[name=nik]').val(btn.data('nik') || '');
    form.find('[name=nomor_hp]').val(btn.data('hp') || '');
    form.find('[name=alamat]').val(btn.data('alamat') || '');
    form.find('[name=is_wali]').prop('checked', btn.data('wali') == 1);
});
</script>
@endsection
