@extends('adminlte::page')

@section('title', 'Detail Batch Pengolahan: ' . ($batch->nama_produk ?? ''))

@section('header_actions')
    <a href="{{ route('reports.processing-detail', array_merge(['batch' => $batch->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-success btn-sm" title="Export PDF"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('processing.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-industry mr-2"></i>{{ $batch->nama_produk }}</h5>
        <div>
            @if(!in_array($batch->status, ['selesai', 'dibatalkan']))
            <a href="{{ route('processing.edit', $batch->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Kode Batch</th><td>{{ $batch->kode_batch }}</td></tr>
                    <tr><th>Nama Produk</th><td>{{ $batch->nama_produk }}</td></tr>
                    <tr><th>Jenis Pengolahan</th><td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $batch->jenis_pengolahan)) }}</span></td></tr>
                    <tr><th>Unit</th><td>{{ $batch->unit->nama_unit ?? '-' }}</td></tr>
                    <tr><th>PIC WBP</th><td>{{ $batch->picWbp->nama_lengkap ?? '-' }}</td></tr>
                </table></div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Tanggal Produksi</th><td>{{ $batch->tanggal_produksi ? \Carbon\Carbon::parse($batch->tanggal_produksi)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Tanggal Kadaluarsa</th><td>{{ $batch->tanggal_kadaluarsa ? \Carbon\Carbon::parse($batch->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Jumlah Produksi</th><td>{{ $batch->jumlah_produksi }} {{ $batch->satuan_output }}</td></tr>
                    <tr><th>Biaya Produksi</th><td>Rp {{ number_format($batch->biaya_produksi ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Harga Jual/Satuan</th><td>Rp {{ number_format($batch->harga_jual_satuan ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($batch->status == 'proses')
                                <span class="badge badge-warning">Diproses</span>
                            @elseif($batch->status == 'selesai')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($batch->status == 'gagal')
                                <span class="badge badge-danger">Gagal</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($batch->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table></div>
            </div>
        </div>
        @if($batch->keterangan)
        <div class="callout callout-info">
            <h5>Keterangan</h5>
            <p>{{ $batch->keterangan }}</p>
        </div>
        @endif
    </div>
</div>

{{-- BAHAN BAKU --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-box-open mr-2"></i>Bahan Baku ({{ $batch->items->count() }})</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#itemModal" data-mode="add"><i class="fas fa-plus mr-1"></i> Tambah</button>
    </div>
    <div class="card-body p-0">
        @if($batch->items->count() > 0)
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Bahan</th><th>Jumlah</th><th>Satuan</th><th>Nilai Bahan</th><th width="80"></th></tr></thead>
            <tbody>
                @foreach($batch->items as $bi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600;">{{ $bi->item->nama_item ?? '-' }}</td>
                    <td>{{ number_format($bi->jumlah_digunakan, 0, ',', '.') }}</td>
                    <td>{{ $bi->satuan }}</td>
                    <td>Rp {{ number_format($bi->nilai_bahan ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#itemModal" data-mode="edit"
                                data-url="{{ route('processing.update-item', [$batch, $bi]) }}"
                                data-item="{{ $bi->item_id }}" data-jumlah="{{ $bi->jumlah_digunakan }}"
                                data-satuan="{{ $bi->satuan }}" data-nilai="{{ $bi->nilai_bahan }}" data-ket="{{ $bi->keterangan }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('processing.destroy-item', [$batch, $bi]) }}" method="POST" onsubmit="return confirm('Hapus bahan ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        @else <p class="text-center py-3" style="color:#94a3b8;">Belum ada bahan baku</p> @endif
    </div>
</div>

{{-- MODAL BAHAN BAKU --}}
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="itemForm" action="{{ route('processing.store-item', $batch) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemModalTitle"><i class="fas fa-box-open"></i> Tambah Bahan Baku</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Bahan / Item *</strong></label>
                        <select name="item_id" class="form-control" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($items as $it)
                            <option value="{{ $it->id }}">{{ $it->nama_item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Jumlah Digunakan *</strong></label><input type="number" step="0.01" name="jumlah_digunakan" class="form-control" required></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Satuan</strong></label><input type="text" name="satuan" class="form-control" maxlength="30" placeholder="kg"></div></div>
                    </div>
                    <div class="form-group"><label><strong>Nilai Bahan (Rp)</strong></label><input type="number" step="0.01" name="nilai_bahan" class="form-control" min="0" value="0"></div>
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

{{-- UBAH STATUS --}}
@if(!in_array($batch->status, ['selesai', 'gagal']))
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'proses' => 'info', 'selesai' => 'success', 'gagal' => 'danger'] as $st => $color)
                @if($st !== $batch->status)
                <form action="{{ route('processing.status', $batch) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Batch dengan status <strong>selesai / gagal</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.processing-detail', $batch) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['draft' => 'secondary', 'proses' => 'info', 'selesai' => 'success', 'gagal' => 'danger']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($batch->statusHistories->sortByDesc('created_at') as $h)
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
$('#itemModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    var title = $(this).find('.modal-title');
    if (btn.data('mode') === 'edit') {
        form.attr('action', btn.data('url'));
        form.find('[name=_method]').val('PUT');
        title.html('<i class="fas fa-edit"></i> Edit Bahan Baku');
        form.find('[name=item_id]').val(btn.data('item'));
        form.find('[name=jumlah_digunakan]').val(btn.data('jumlah'));
        form.find('[name=satuan]').val(btn.data('satuan') || '');
        form.find('[name=nilai_bahan]').val(btn.data('nilai') || '');
        form.find('[name=keterangan]').val(btn.data('ket') || '');
    } else {
        form.attr('action', '{{ route('processing.store-item', $batch) }}');
        form.find('[name=_method]').val('');
        title.html('<i class="fas fa-box-open"></i> Tambah Bahan Baku');
        form.trigger('reset');
    }
});
</script>
@endsection
