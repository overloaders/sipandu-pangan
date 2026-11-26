@extends('adminlte::page')
@section('title', 'Tambah Distribusi')
@section('header_actions')
    <a href="{{ route('distributions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
@endsection
@section('content')
<div class="card"><div class="card-body">
    <form action="{{ route('distributions.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-3"><div class="form-group"><label>Kode Distribusi *</label><input type="text" name="kode_distribusi" class="form-control" value="{{ old('kode_distribusi', 'DIS-'.date('Ymd')) }}" required></div></div>
            <div class="col-md-3"><div class="form-group"><label>Tipe Distribusi *</label><select name="tipe_distribusi" class="form-control" required>
                <option value="konsumsi_wbp">Konsumsi WBP</option><option value="penjualan_lokal">Penjualan Lokal</option><option value="penjualan_eksternal">Penjualan Eksternal</option><option value="donasi">Donasi</option><option value="konversi_produksi">Konversi Produksi</option>
            </select></div></div>
            <div class="col-md-3"><div class="form-group"><label>Unit *</label><select name="unit_id" class="form-control" required><option value="">-- Pilih --</option>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->nama_unit }}</option>@endforeach</select></div></div>
            <div class="col-md-3"><div class="form-group"><label>Tanggal *</label><input type="date" name="tanggal_distribusi" class="form-control" value="{{ date('Y-m-d') }}" required></div></div>
        </div>
        <div class="row">
            <div class="col-md-4"><div class="form-group"><label>Nama Penerima</label><input type="text" name="penerima_nama" class="form-control" value="{{ old('penerima_nama') }}"></div></div>
            <div class="col-md-4"><div class="form-group"><label>Total Nilai</label><input type="number" name="total_nilai" class="form-control" value="{{ old('total_nilai') }}" min="0" step="0.01"></div></div>
        </div>
        <div class="form-group"><label>Keterangan</label><textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea></div>

        <h5 class="mt-3"><i class="fas fa-box"></i> Item Distribusi</h5>
        <div id="items-container">
            <div class="row item-row mb-2">
                <div class="col-md-5"><select name="items[0][inventory_item_id]" class="form-control" required><option value="">-- Pilih Item --</option>@foreach($items as $ii)<option value="{{ $ii->id }}">{{ $ii->nama_item }} (Stok: {{ $ii->stok_saat_ini }})</option>@endforeach</select></div>
                <div class="col-md-2"><input type="number" name="items[0][jumlah]" class="form-control" placeholder="Jumlah" min="0.01" step="0.01" required></div>
                <div class="col-md-2"><input type="text" name="items[0][satuan]" class="form-control" placeholder="Satuan" value="kg" required></div>
                <div class="col-md-2"><input type="number" name="items[0][nilai_satuan]" class="form-control" placeholder="Harga" min="0" step="0.01"></div>
                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-minus"></i></button></div>
            </div>
        </div>
        <button type="button" id="add-item" class="btn btn-success btn-sm mb-3"><i class="fas fa-plus"></i> Tambah Item</button>
        <div><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button></div>
    </form>
</div></div>
@stop
@section('js')
<script>
let itemIndex = 1;
$('#add-item').click(function(){
    let html = `<div class="row item-row mb-2">
        <div class="col-md-5"><select name="items[${itemIndex}][inventory_item_id]" class="form-control" required><option value="">-- Pilih Item --</option>@foreach($items as $ii)<option value="{{ $ii->id }}">{{ $ii->nama_item }}</option>@endforeach</select></div>
        <div class="col-md-2"><input type="number" name="items[${itemIndex}][jumlah]" class="form-control" placeholder="Jumlah" min="0.01" step="0.01" required></div>
        <div class="col-md-2"><input type="text" name="items[${itemIndex}][satuan]" class="form-control" placeholder="Satuan" value="kg" required></div>
        <div class="col-md-2"><input type="number" name="items[${itemIndex}][nilai_satuan]" class="form-control" placeholder="Harga" min="0" step="0.01"></div>
        <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-minus"></i></button></div>
    </div>`;
    $('#items-container').append(html);
    itemIndex++;
});
$(document).on('click','.remove-item',function(){ $(this).closest('.item-row').remove(); });
</script>
@stop
