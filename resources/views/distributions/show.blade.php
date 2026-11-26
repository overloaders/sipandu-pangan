@extends('adminlte::page')
@section('title', 'Detail Distribusi: ' . ($distribution->kode_distribusi ?? ''))
@section('header_actions')
    <a href="{{ route('reports.distributions-detail', array_merge([$distribution->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    @if(!in_array($distribution->status, ['diterima', 'dibatalkan']))
    <a href="{{ route('distributions.edit', $distribution) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
    @endif
    <button onclick="confirmDelete('{{ route('distributions.destroy', $distribution) }}','{{ $distribution->kode_distribusi }}')" class="btn btn-danger btn-sm"><i class="fas fa-trash mr-1"></i> Hapus</button>
    <a href="{{ route('distributions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-truck mr-2"></i>Info Distribusi</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Kode</th><td><code>{{ $distribution->kode_distribusi }}</code></td></tr>
                    <tr><th>Penerima</th><td>{{ $distribution->penerima_nama ?? '-' }}</td></tr>
                    <tr><th>Tipe</th><td><span class="badge badge-info">{{ str_replace('_', ' ', ucfirst($distribution->tipe_distribusi)) }}</span></td></tr>
                    <tr><th>Tanggal</th><td>{{ $distribution->tanggal_distribusi->format('d/m/Y') }}</td></tr>
                    <tr><th>Total Nilai</th><td>Rp {{ number_format($distribution->total_nilai ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-{{ $distribution->status=='diterima'?'success':'warning' }}">{{ ucfirst($distribution->status) }}</span></td></tr>
                    <tr><th>Keterangan</th><td>{{ $distribution->keterangan ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-boxes mr-2"></i>Item Distribusi</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#itemModal" data-mode="add"><i class="fas fa-plus mr-1"></i> Tambah</button>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped">
                    <thead><tr><th>Item</th><th>Jumlah</th><th>Satuan</th><th>Nilai</th><th width="80">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($distribution->items as $item)
                        <tr>
                            <td>{{ $item->item->nama_item ?? '-' }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#itemModal" data-mode="edit"
                                        data-url="{{ route('distributions.update-item', [$distribution, $item]) }}"
                                        data-item="{{ $item->item_id }}" data-jumlah="{{ $item->jumlah }}"
                                        data-harga="{{ $item->harga_satuan }}" data-satuan="{{ $item->satuan }}" data-ket="{{ $item->keterangan }}"
                                        title="Edit"><i class="fas fa-edit"></i></button>
                                    <form action="{{ route('distributions.destroy-item', [$distribution, $item]) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">Belum ada item — klik <strong>Tambah</strong>.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ITEM DISTRIBUSI --}}
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="itemForm" action="{{ route('distributions.store-item', $distribution) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemModalTitle"><i class="fas fa-boxes"></i> Tambah Item Distribusi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Item *</strong></label>
                        <select name="item_id" class="form-control" required>
                            <option value="">-- Pilih Item --</option>
                            @foreach($items as $it)
                            <option value="{{ $it->id }}">{{ $it->nama_item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Jumlah *</strong></label><input type="number" step="0.01" name="jumlah" class="form-control" required></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Satuan</strong></label><input type="text" name="satuan" class="form-control" maxlength="30" placeholder="kg"></div></div>
                    </div>
                    <div class="form-group"><label><strong>Harga Satuan (Rp)</strong></label><input type="number" step="0.01" name="harga_satuan" class="form-control" min="0" value="0"></div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                    <small class="text-muted">Nilai (subtotal) dihitung otomatis: jumlah × harga satuan.</small>
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
@if(!in_array($distribution->status, ['diterima', 'dibatalkan']))
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'dikirim' => 'info', 'diterima' => 'success', 'dibatalkan' => 'danger'] as $st => $color)
                @if($st !== $distribution->status)
                <form action="{{ route('distributions.status', $distribution) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Distribusi dengan status <strong>diterima / dibatalkan</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.distributions-detail', $distribution) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['draft' => 'secondary', 'dikirim' => 'info', 'diterima' => 'success', 'dibatalkan' => 'danger']; @endphp
        <table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($distribution->statusHistories->sortByDesc('created_at') as $h)
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
        </table>
    </div>
</div>
@stop

@section('extra_js')
<script>
$('#itemModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    var title = $(this).find('.modal-title');
    if (btn.data('mode') === 'edit') {
        form.attr('action', btn.data('url'));
        form.find('[name=_method]').val('PUT');
        title.html('<i class="fas fa-edit"></i> Edit Item Distribusi');
        form.find('[name=item_id]').val(btn.data('item'));
        form.find('[name=jumlah]').val(btn.data('jumlah'));
        form.find('[name=harga_satuan]').val(btn.data('harga') || '');
        form.find('[name=satuan]').val(btn.data('satuan') || '');
        form.find('[name=keterangan]').val(btn.data('ket') || '');
    } else {
        form.attr('action', '{{ route('distributions.store-item', $distribution) }}');
        form.find('[name=_method]').val('');
        title.html('<i class="fas fa-boxes"></i> Tambah Item Distribusi');
        form.trigger('reset');
    }
});
</script>
@endsection
