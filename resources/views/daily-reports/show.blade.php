@extends('adminlte::master')
@section('title', 'Detail Laporan: ' . $dailyReport->kode_laporan)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-clipboard-list mr-2"></i>Detail Laporan Harian</h5>
                <div class="d-flex align-items-center">
                    {{-- Status Action Buttons --}}
                    @if($dailyReport->status === 'draft')
                    <button type="button" class="btn btn-sm btn-warning mr-1" title="Kirim untuk Review"
                        onclick="swalConfirmPost('{{ route('daily-reports.kirim', $dailyReport) }}', 'Kirim Laporan?', 'Laporan ini akan dikirim untuk review supervisor.', '#f59e0b', 'Ya, Kirim')">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim
                    </button>
                    @endif

                    @if($dailyReport->status === 'dikirim' && auth()->user()->hasRole(['admin','super-admin','supervisor']))
                    <button type="button" class="btn btn-sm btn-success mr-1" title="Konfirmasi"
                        onclick="swalConfirmPost('{{ route('daily-reports.confirm', $dailyReport) }}', 'Konfirmasi Laporan?', 'Laporan ini akan dikonfirmasi sebagai selesai.', '#22c55e', 'Ya, Konfirmasi')">
                        <i class="fas fa-check mr-1"></i> Konfirmasi
                    </button>
                    <button type="button" class="btn btn-sm btn-danger mr-1" title="Tolak"
                        onclick="swalConfirmPost('{{ route('daily-reports.reject', $dailyReport) }}', 'Tolak Laporan?', 'Laporan akan dikembalikan ke draft untuk diperbaiki.', '#ef4444', 'Ya, Tolak')">
                        <i class="fas fa-times mr-1"></i> Tolak
                    </button>
                    @endif

                    {{-- Other Actions --}}
                    <div class="ml-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('daily-reports.destroy', $dailyReport) }}','{{ $dailyReport->kode_laporan }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                        <a href="{{ route('reports.daily-reports-detail', array_merge([$dailyReport->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                        <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Laporan</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $dailyReport->kode_laporan }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanggal</td><td>{{ $dailyReport->tanggal_laporan->format('d M Y') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $dailyReport->unit?->nama_unit ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Pelapor</td><td>{{ $dailyReport->reporter?->name ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $dailyReport->status=='dikonfirmasi'?'success':($dailyReport->status=='dikirim'?'info':'secondary') }}">{{ ucfirst($dailyReport->status) }}</span></td></tr>
                    @if($dailyReport->status==='dikonfirmasi')
                    <tr><td style="font-weight:600;color:#64748b;">Dikonfirmasi Oleh</td><td>{{ $dailyReport->confirmer?->name ?? '-' }} @if($dailyReport->updated_at)<span style="color:#94a3b8;font-size:11px;">{{ $dailyReport->updated_at->format('d M Y H:i') }}</span>@endif</td></tr>
                    @endif
                    <tr><td style="font-weight:600;color:#64748b;">WBP Aktif</td><td>{{ $dailyReport->total_wbp_aktif }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Panen Hari Ini</td><td>{{ number_format($dailyReport->total_panen_hari_ini_kg ?? 0,0,',','.') }} kg</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Ringkasan</td><td>{{ $dailyReport->ringkasan_kegiatan ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Masalah</td><td>{{ $dailyReport->masalah_ditemukan ?? '-' }}</td></tr>
                </table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-list mr-2"></i>Detail</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#detailModal" data-mode="add"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body">
                @forelse($dailyReport->details as $d)
                <div class="p-2 mb-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-info">{{ ucfirst($d->kategori_laporan) }}</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#detailModal" data-mode="edit"
                                data-url="{{ route('daily-reports.update-detail', [$dailyReport, $d]) }}"
                                data-kategori="{{ $d->kategori_laporan }}" data-judul="{{ $d->judul_detail }}"
                                data-deskripsi="{{ $d->deskripsi }}" data-nilai="{{ $d->nilai_angka }}" data-satuan="{{ $d->satuan }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('daily-reports.destroy-detail', [$dailyReport, $d]) }}" method="POST" onsubmit="return confirm('Hapus detail ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div style="font-weight:600;">{{ $d->judul_detail }}</div>
                    <div style="color:#64748b;">{{ $d->deskripsi }}</div>
                    @if($d->nilai_angka !== null)<div style="color:#2563eb;font-weight:600;">{{ number_format($d->nilai_angka, 0, ',', '.') }} {{ $d->satuan ?? '' }}</div>@endif
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada detail</p> @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL LAPORAN --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="detailForm" action="{{ route('daily-reports.store-detail', $dailyReport) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalTitle"><i class="fas fa-list"></i> Tambah Detail Laporan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Kategori *</strong></label>
                        <select name="kategori_laporan" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach(['pertanian','peternakan','perikanan','inventaris','keuangan','keamanan','kesehatan','pelatihan','lainnya'] as $kt)
                            <option value="{{ $kt }}">{{ ucfirst($kt) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Judul *</strong></label><input type="text" name="judul_detail" class="form-control" required maxlength="200"></div>
                    <div class="form-group"><label><strong>Deskripsi *</strong></label><textarea name="deskripsi" class="form-control" rows="3" required></textarea></div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Nilai Angka</strong></label><input type="number" step="0.01" name="nilai_angka" class="form-control"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Satuan</strong></label><input type="text" name="satuan" class="form-control" maxlength="30" placeholder="kg, liter, ekor..."></div></div>
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

{{-- UBAH STATUS --}}
@if($dailyReport->status !== 'dikonfirmasi')
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'dikirim' => 'info', 'dikonfirmasi' => 'success', 'dikembalikan' => 'warning'] as $st => $color)
                @if($st !== $dailyReport->status)
                <form action="{{ route('daily-reports.status', $dailyReport) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Laporan yang sudah <strong>dikonfirmasi</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.daily-reports-detail', $dailyReport) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['draft' => 'secondary', 'dikirim' => 'info', 'dikonfirmasi' => 'success', 'dikembalikan' => 'warning']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($dailyReport->statusHistories->sortByDesc('created_at') as $h)
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

@section('extra_js')
<script>
$('#detailModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    var title = $(this).find('.modal-title');
    if (btn.data('mode') === 'edit') {
        form.attr('action', btn.data('url'));
        form.find('[name=_method]').val('PUT');
        title.html('<i class="fas fa-edit"></i> Edit Detail Laporan');
        form.find('[name=kategori_laporan]').val(btn.data('kategori'));
        form.find('[name=judul_detail]').val(btn.data('judul'));
        form.find('[name=deskripsi]').val(btn.data('deskripsi'));
        form.find('[name=nilai_angka]').val(btn.data('nilai') || '');
        form.find('[name=satuan]').val(btn.data('satuan') || '');
    } else {
        form.attr('action', '{{ route('daily-reports.store-detail', $dailyReport) }}');
        form.find('[name=_method]').val('');
        title.html('<i class="fas fa-list"></i> Tambah Detail Laporan');
        form.trigger('reset');
    }
});
</script>
@endsection
