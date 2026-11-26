@extends('adminlte::page')
@section('title', 'Checklist: ' . ($record->nama_sertifikasi ?? $record->judul ?? 'Komplians'))
@section('header_actions')
    <a href="{{ route('compliance.show', $record) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection
@section('content')
@php
    $statusList = ['memenuhi' => 'Memenuhi', 'tidak_memenuhi' => 'Tidak Memenuhi', 'sebagian' => 'Sebagian', 'tidak_diterapkan' => 'Tidak Diterapkan'];
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-list-check mr-2"></i>Tambah Item Checklist</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('compliance.add-detail', $record) }}" method="POST" class="form-inline">
            @csrf
            <input type="text" name="item_pemeriksaan" class="form-control form-control-sm mr-2" placeholder="Item pemeriksaan" required style="width:300px">
            <select name="status_pemeriksaan" class="form-control form-control-sm mr-2">
                @foreach($statusList as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="catatan" class="form-control form-control-sm mr-2" placeholder="Catatan (opsional)">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah</button>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead><tr><th>#</th><th>Item</th><th>Status</th><th>Catatan</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($record->details as $i => $d)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $d->item_pemeriksaan }}</td>
                    <td>
                        @if($d->status_pemeriksaan==='memenuhi')<span class="badge badge-success"><i class="fas fa-check"></i> Memenuhi</span>
                        @elseif($d->status_pemeriksaan==='sebagian')<span class="badge badge-warning"><i class="fas fa-adjust"></i> Sebagian</span>
                        @elseif($d->status_pemeriksaan==='tidak_diterapkan')<span class="badge badge-secondary"><i class="fas fa-ban"></i> Tidak Diterapkan</span>
                        @else<span class="badge badge-danger"><i class="fas fa-times"></i> Tidak Memenuhi</span>
                        @endif
                    </td>
                    <td>{{ $d->catatan ?? '-' }}</td>
                    <td style="white-space:nowrap;">
                        <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditDetail"
                            data-url="{{ route('compliance.update-detail', [$record, $d]) }}"
                            data-item="{{ $d->item_pemeriksaan }}"
                            data-status="{{ $d->status_pemeriksaan }}"
                            data-catatan="{{ $d->catatan ?? '' }}"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('compliance.remove-detail', [$record, $d]) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="button" onclick="confirmDeleteForm(this.closest('form'))" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                @empty <tr><td colspan="5" class="text-center text-muted py-3">Belum ada item</td></tr> @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL EDIT DETAIL --}}
<div class="modal fade" id="modalEditDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" method="POST">
                @csrf @method('PUT')
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Item Checklist</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Item Pemeriksaan *</strong></label>
                        <input type="text" name="item_pemeriksaan" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label><strong>Status *</strong></label>
                        <select name="status_pemeriksaan" class="form-control">
                            @foreach($statusList as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Catatan</strong></label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
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
$('#modalEditDetail').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    form.attr('action', btn.data('url'));
    form.find('[name=item_pemeriksaan]').val(btn.data('item'));
    form.find('[name=status_pemeriksaan]').val(btn.data('status'));
    form.find('[name=catatan]').val(btn.data('catatan') || '');
});
</script>
@endsection
