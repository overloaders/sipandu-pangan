@extends('adminlte::page')

@section('title', 'Detail Rencana Gizi: ' . ($plan->nama_menu ?? ''))

@section('header_actions')
    <a href="{{ route('reports.nutrition-detail', array_merge([$plan->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    <a href="{{ route('reports.nutrition', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF Semua</a>
    <a href="{{ route('nutrition.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-utensils mr-2"></i>{{ $plan->nama_menu }}</h5>
        <div>
            @if($plan->status !== 'nonaktif')
            <a href="{{ route('nutrition.edit', $plan->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit mr-1"></i> Edit</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Kode Rencana Gizi</th><td>{{ $plan->kode_rencana_gizi }}</td></tr>
                    <tr><th>Nama Menu</th><td>{{ $plan->nama_menu }}</td></tr>
                    <tr><th>Unit</th><td>{{ $plan->unit->nama_unit ?? '-' }}</td></tr>
                    <tr><th>Waktu Makan</th><td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $plan->waktu_makan)) }}</span></td></tr>
                    <tr><th>Tipe Porsi</th><td><span class="badge badge-primary">{{ ucfirst($plan->tipe_porsi) }}</span></td></tr>
                    <tr><th>Berlaku Mulai</th><td>{{ $plan->berlaku_mulai ? \Carbon\Carbon::parse($plan->berlaku_mulai)->format('d/m/Y') : '-' }}</td></tr>
                    <tr><th>Berlaku Sampai</th><td>{{ $plan->berlaku_selesai ? \Carbon\Carbon::parse($plan->berlaku_selesai)->format('d/m/Y') : '-' }}</td></tr>
                </table></div>
            </div>
            <div class="col-md-6">
                <div class="table-responsive"><table class="table table-bordered">
                    <tr><th width="200">Kalori Target</th><td>{{ $plan->kalori_target_kcal ?? '-' }} kcal</td></tr>
                    <tr><th>Protein Target</th><td>{{ $plan->protein_target_gr ?? '-' }} gr</td></tr>
                    <tr><th>Karbohidrat Target</th><td>{{ $plan->karbohidrat_target_gr ?? '-' }} gr</td></tr>
                    <tr><th>Lemak Target</th><td>{{ $plan->lemak_target_gr ?? '-' }} gr</td></tr>
                    <tr><th>Serat Target</th><td>{{ $plan->serat_target_gr ?? '-' }} gr</td></tr>
                    <tr><th>Estimasi Biaya/Porsi</th><td>Rp {{ number_format($plan->estimasi_biaya_per_porsi ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Sumber Bahan</th><td>{{ $plan->sumber_bahan ?? '-' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($plan->status == 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($plan->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table></div>
            </div>
        </div>
        @if($plan->deskripsi)
        <div class="callout callout-info">
            <h5>Deskripsi</h5>
            <p>{{ $plan->deskripsi }}</p>
        </div>
        @endif
    </div>
</div>

{{-- KOMPONEN MENU --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-carrot mr-2"></i>Komponen Menu ({{ $plan->components->count() }})</h5>
        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#componentModal" data-mode="add"><i class="fas fa-plus mr-1"></i> Tambah</button>
    </div>
    <div class="card-body p-0">
        @if($plan->components->count() > 0)
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Bahan</th><th>Jumlah</th><th>Kalori</th><th>Protein</th><th>Karbo</th><th>Lemak</th><th width="80"></th></tr></thead>
            <tbody>
                @foreach($plan->components->sortBy('urutan') as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600;">{{ $c->item->nama_item ?? '-' }}</td>
                    <td>{{ number_format($c->jumlah, 0, ',', '.') }} {{ $c->satuan }}</td>
                    <td>{{ $c->kalori_kcal ?? '-' }}</td>
                    <td>{{ $c->protein_gr ?? '-' }}</td>
                    <td>{{ $c->karbohidrat_gr ?? '-' }}</td>
                    <td>{{ $c->lemak_gr ?? '-' }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#componentModal" data-mode="edit"
                                data-url="{{ route('nutrition.update-component', [$plan, $c]) }}"
                                data-item="{{ $c->item_id }}" data-jumlah="{{ $c->jumlah }}" data-satuan="{{ $c->satuan }}"
                                data-kalori="{{ $c->kalori_kcal }}" data-protein="{{ $c->protein_gr }}"
                                data-karbo="{{ $c->karbohidrat_gr }}" data-lemak="{{ $c->lemak_gr }}" data-ket="{{ $c->keterangan }}"
                                title="Edit"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('nutrition.destroy-component', [$plan, $c]) }}" method="POST" onsubmit="return confirm('Hapus komponen ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        @else <p class="text-center py-3" style="color:#94a3b8;">Belum ada komponen menu</p> @endif
    </div>
</div>

{{-- MODAL KOMPONEN MENU --}}
<div class="modal fade" id="componentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="componentForm" action="{{ route('nutrition.store-component', $plan) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="componentModalTitle"><i class="fas fa-carrot"></i> Tambah Komponen Menu</h5>
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
                        <div class="col-6"><div class="form-group"><label><strong>Jumlah *</strong></label><input type="number" step="0.01" name="jumlah" class="form-control" required></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Satuan</strong></label><input type="text" name="satuan" class="form-control" maxlength="30" placeholder="gram"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Kalori (kcal)</strong></label><input type="number" step="0.01" name="kalori_kcal" class="form-control"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Protein (gr)</strong></label><input type="number" step="0.01" name="protein_gr" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Karbohidrat (gr)</strong></label><input type="number" step="0.01" name="karbohidrat_gr" class="form-control"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Lemak (gr)</strong></label><input type="number" step="0.01" name="lemak_gr" class="form-control"></div></div>
                    </div>
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
@if($plan->status !== 'nonaktif')
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @foreach(['draft' => 'secondary', 'aktif' => 'success', 'nonaktif' => 'danger'] as $st => $color)
                @if($st !== $plan->status)
                <form action="{{ route('nutrition.status', $plan) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst($st) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst($st) }}</button>
                </form>
                @endif
            @endforeach
        </div>
        <small class="form-text text-muted mt-2">Rencana gizi yang sudah <strong>nonaktif</strong> tidak dapat diubah lagi.</small>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.nutrition-detail', $plan) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $sc = ['draft' => 'secondary', 'aktif' => 'success', 'nonaktif' => 'danger']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($plan->statusHistories->sortByDesc('created_at') as $h)
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
$('#componentModal').on('show.bs.modal', function (e) {
    var btn = $(e.relatedTarget);
    var form = $(this).find('form');
    var title = $(this).find('.modal-title');
    if (btn.data('mode') === 'edit') {
        form.attr('action', btn.data('url'));
        form.find('[name=_method]').val('PUT');
        title.html('<i class="fas fa-edit"></i> Edit Komponen Menu');
        form.find('[name=item_id]').val(btn.data('item'));
        form.find('[name=jumlah]').val(btn.data('jumlah'));
        form.find('[name=satuan]').val(btn.data('satuan') || '');
        form.find('[name=kalori_kcal]').val(btn.data('kalori') || '');
        form.find('[name=protein_gr]').val(btn.data('protein') || '');
        form.find('[name=karbohidrat_gr]').val(btn.data('karbo') || '');
        form.find('[name=lemak_gr]').val(btn.data('lemak') || '');
        form.find('[name=keterangan]').val(btn.data('ket') || '');
    } else {
        form.attr('action', '{{ route('nutrition.store-component', $plan) }}');
        form.find('[name=_method]').val('');
        title.html('<i class="fas fa-carrot"></i> Tambah Komponen Menu');
        form.trigger('reset');
    }
});
</script>
@endsection
