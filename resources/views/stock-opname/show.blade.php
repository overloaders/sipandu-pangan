@extends('adminlte::page')

@section('title', 'Detail Stock Opname: ' . ($opname->kode_opname ?? ''))

@section('header_actions')
    <a href="{{ route('reports.stock-opname-detail', array_merge([$opname->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
    <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-clipboard-check mr-2"></i>{{ $opname->kode_opname }}</h5>
        <div>
            @if($opname->status !== 'selesai')
            <a href="{{ route('stock-opname.edit', $opname->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Kode Opname</th><td>{{ $opname->kode_opname }}</td></tr>
                    <tr><th>Unit</th><td>{{ $opname->unit->nama_unit ?? '-' }}</td></tr>
                    <tr><th>Tanggal Opname</th><td>{{ $opname->tanggal_opname ? \Carbon\Carbon::parse($opname->tanggal_opname)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Jenis Opname</th><td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $opname->jenis_opname)) }}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th width="200">Petugas WBP</th><td>{{ $opname->petugas->nama_lengkap ?? '-' }}</td></tr>
                    <tr><th>Verifikator</th><td>{{ $opname->verifikator->name ?? '-' }}</td></tr>
                    <tr><th>Total Item Diperiksa</th><td>{{ $opname->total_item_checked ?? 0 }}</td></tr>
                    <tr><th>Item Selisih</th><td>{{ $opname->item_selisih ?? 0 }}</td></tr>
                    <tr><th>Total Selisih Nilai</th><td>Rp {{ number_format($opname->total_selisih_nilai ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($opname->status == 'selesai')
                                <span class="badge badge-success">Selesai</span>
                            @elseif($opname->status == 'dibatalkan')
                                <span class="badge badge-danger">Dibatalkan</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($opname->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @if($opname->keterangan)
        <div class="callout callout-info">
            <h5>Keterangan</h5>
            <p>{{ $opname->keterangan }}</p>
        </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;">Detail Item ({{ $opname->items->count() }})</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#itemModal" data-mode="add"><i class="fas fa-plus mr-1"></i> Tambah</button>
    </div>
    @if($opname->items && $opname->items->count())
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Item</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Nilai Selisih</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($opname->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->item->nama_item ?? '-' }}</td>
                    <td>{{ $item->stok_sistem }}</td>
                    <td>{{ $item->stok_fisik }}</td>
                    <td>
                        @if($item->selisih > 0)
                            <span class="text-danger">+{{ $item->selisih }}</span>
                        @elseif($item->selisih < 0)
                            <span class="text-warning">{{ $item->selisih }}</span>
                        @else
                            <span class="text-success">0</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->nilai_selisih ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#itemModal" data-mode="edit"
                                data-url="{{ route('stock-opname.update-item', [$opname, $item]) }}"
                                data-item="{{ $item->item_id }}" data-sistem="{{ $item->stok_sistem }}"
                                data-fisik="{{ $item->stok_fisik }}" data-ket="{{ $item->keterangan }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('stock-opname.destroy-item', [$opname, $item]) }}" method="POST" onsubmit="return confirm('Hapus item ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card-body"><p class="text-center py-3 mb-0" style="color:#94a3b8;">Belum ada item — klik <strong>Tambah</strong> untuk input stok.</p></div>
    @endif
</div>

{{-- MODAL ITEM OPNAME --}}
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="itemForm" action="{{ route('stock-opname.store-item', $opname) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemModalTitle"><i class="fas fa-clipboard-check"></i> Tambah Item Opname</h5>
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
                        <div class="col-6"><div class="form-group"><label><strong>Stok Sistem *</strong></label><input type="number" step="0.01" name="stok_sistem" class="form-control" required></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Stok Fisik *</strong></label><input type="number" step="0.01" name="stok_fisik" class="form-control" required></div></div>
                    </div>
                    <div class="form-group"><label><strong>Keterangan</strong></label><textarea name="keterangan" class="form-control" rows="2"></textarea></div>
                    <small class="text-muted">Selisih & nilai selisih dihitung otomatis dari stok fisik − stok sistem.</small>
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
@if($opname->status !== 'selesai')
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'proses' => 'info', 'selesai' => 'success', 'dibatalkan' => 'danger'] as $st => $color)
                @if($st !== $opname->status)
                <form action="{{ route('stock-opname.status', $opname) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Stock opname yang sudah <strong>selesai</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.stock-opname-detail', $opname) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['draft' => 'secondary', 'proses' => 'info', 'selesai' => 'success', 'dibatalkan' => 'danger']; @endphp
        <table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($opname->statusHistories->sortByDesc('created_at') as $h)
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
        title.html('<i class="fas fa-edit"></i> Edit Item Opname');
        form.find('[name=item_id]').val(btn.data('item'));
        form.find('[name=stok_sistem]').val(btn.data('sistem'));
        form.find('[name=stok_fisik]').val(btn.data('fisik'));
        form.find('[name=keterangan]').val(btn.data('ket') || '');
    } else {
        form.attr('action', '{{ route('stock-opname.store-item', $opname) }}');
        form.find('[name=_method]').val('');
        title.html('<i class="fas fa-clipboard-check"></i> Tambah Item Opname');
        form.trigger('reset');
    }
});
</script>
@endsection
