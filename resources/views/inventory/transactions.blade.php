@extends('adminlte::master')
@section('title', 'Transaksi Stok')
@section('header_actions')
<a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Catat Transaksi</h5></div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger" style="border-radius:8px;font-size:12px;">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
                @endif
                <form method="POST" action="{{ route('inventory-transactions.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Item *</label>
                        <select name="item_id" class="form-control" required style="border-radius:8px;">
                            <option value="">-- Pilih Item --</option>
                            @foreach($items as $i)
                            <option value="{{ $i->id }}">{{ $i->nama_item }} ({{ number_format($i->stok_saat_ini,0,',','.') }} {{ $i->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tipe Transaksi *</label>
                        <select name="tipe_transaksi" class="form-control" required style="border-radius:8px;">
                            <option value="masuk">Masuk (Penerimaan)</option>
                            <option value="keluar">Keluar (Pengeluaran)</option>
                            <option value="transfer">Transfer</option>
                            <option value="opname">Opname</option>
                            <option value="rusak">Rusak</option>
                            <option value="kadaluarsa">Kadaluarsa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sumber *</label>
                        <select name="sumber" class="form-control" required style="border-radius:8px;">
                            <option value="produksi">Produksi</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="konsumsi_wbp">Konsumsi WBP</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="donasi">Donasi</option>
                            <option value="konversi">Konversi</option>
                            <option value="opname">Opname</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah *</label>
                        <input type="number" name="jumlah" class="form-control" step="0.01" min="0.01" required style="border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label>Tanggal *</label>
                        <input type="date" name="tanggal_transaksi" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" style="border-radius:8px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100" style="border-radius:8px;"><i class="fas fa-save mr-1"></i> Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Transaksi</h5>
                <form action="{{ route('inventory-transactions.index') }}" method="GET" class="form-inline mt-2">
                    <select name="item_id" class="form-control form-control-sm mr-2" style="width:auto;">
                        <option value="">Semua Item</option>
                        @foreach($items as $i)
                        <option value="{{ $i->id }}" {{ request('item_id')==$i->id?'selected':'' }}>{{ $i->nama_item }}</option>
                        @endforeach
                    </select>
                    <select name="tipe_transaksi" class="form-control form-control-sm mr-2" style="width:auto;">
                        <option value="">Semua Tipe</option>
                        @foreach(['masuk','keluar','transfer','opname','rusak','kadaluarsa'] as $t)
                        <option value="{{ $t }}" {{ request('tipe_transaksi')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
                    <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
                    @if(request()->hasAny(['item_id','tipe_transaksi','from_date','to_date']))
                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
                    @endif
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Kode</th><th>Item</th><th>Tipe</th><th>Sumber</th><th>Jumlah</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                        <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $txn->kode_transaksi }}</span></td>
                            <td style="font-weight:600;">{{ $txn->item?->nama_item ?? '-' }}</td>
                            <td>
                                @php $tc = ['masuk'=>'success','keluar'=>'danger','transfer'=>'info','opname'=>'warning','rusak'=>'danger','kadaluarsa'=>'warning']; @endphp
                                <span class="badge badge-{{ $tc[$txn->tipe_transaksi] ?? 'secondary' }}">{{ ucfirst($txn->tipe_transaksi) }}</span>
                            </td>
                            <td style="font-size:12px;">{{ ucfirst(str_replace('_',' ',$txn->sumber)) }}</td>
                            <td>
                                <span class="{{ in_array($txn->tipe_transaksi,['masuk']) ? 'text-success' : 'text-danger' }}" style="font-weight:700;">
                                    {{ in_array($txn->tipe_transaksi,['masuk']) ? '+' : '-' }}{{ number_format($txn->jumlah,0,',','.') }}
                                </span>
                            </td>
                            <td style="font-size:12px;">{{ $txn->tanggal_transaksi->format('d/m/Y') }}</td>
                            <td style="white-space:nowrap;">
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditTxn"
                                    data-url="{{ route('inventory-transactions.update', $txn) }}"
                                    data-item="{{ $txn->item_id }}"
                                    data-tipe="{{ $txn->tipe_transaksi }}"
                                    data-sumber="{{ $txn->sumber }}"
                                    data-jumlah="{{ $txn->jumlah }}"
                                    data-tanggal="{{ $txn->tanggal_transaksi->format('Y-m-d') }}"
                                    data-keterangan="{{ $txn->keterangan ?? '' }}"><i class="fas fa-edit"></i></button>
                                <form action="{{ route('inventory-transactions.destroy', $txn) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Hapus transaksi {{ $txn->kode_transaksi }}? Stok akan dikembalikan otomatis.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4"><p style="color:#94a3b8;">Belum ada transaksi</p></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())<div class="card-footer">{{ $transactions->withQueryString()->links() }}</div>@endif
        </div>
    </div>
</div>

{{-- MODAL EDIT TRANSAKSI --}}
<div class="modal fade" id="modalEditTxn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Transaksi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Item *</strong></label>
                        <select name="item_id" class="form-control" required>
                            <option value="">-- Pilih Item --</option>
                            @foreach($items as $i)
                            <option value="{{ $i->id }}">{{ $i->nama_item }} ({{ number_format($i->stok_saat_ini,0,',','.') }} {{ $i->satuan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Tipe Transaksi *</strong></label>
                        <select name="tipe_transaksi" class="form-control" required>
                            <option value="masuk">Masuk (Penerimaan)</option>
                            <option value="keluar">Keluar (Pengeluaran)</option>
                            <option value="transfer">Transfer</option>
                            <option value="opname">Opname</option>
                            <option value="rusak">Rusak</option>
                            <option value="kadaluarsa">Kadaluarsa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Sumber *</strong></label>
                        <select name="sumber" class="form-control" required>
                            <option value="produksi">Produksi</option>
                            <option value="pembelian">Pembelian</option>
                            <option value="konsumsi_wbp">Konsumsi WBP</option>
                            <option value="penjualan">Penjualan</option>
                            <option value="donasi">Donasi</option>
                            <option value="konversi">Konversi</option>
                            <option value="opname">Opname</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Jumlah *</strong></label>
                        <input type="number" name="jumlah" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Tanggal *</strong></label>
                        <input type="date" name="tanggal_transaksi" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Keterangan</strong></label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('extra_js')
<script>
$('#modalEditTxn').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    form.attr('action', btn.data('url'));
    form.find('[name=item_id]').val(btn.data('item'));
    form.find('[name=tipe_transaksi]').val(btn.data('tipe'));
    form.find('[name=sumber]').val(btn.data('sumber'));
    form.find('[name=jumlah]').val(btn.data('jumlah'));
    form.find('[name=tanggal_transaksi]').val(btn.data('tanggal'));
    form.find('[name=keterangan]').val(btn.data('keterangan') || '');
});
</script>
@endsection
