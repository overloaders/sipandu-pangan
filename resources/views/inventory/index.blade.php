@extends('adminlte::master')

@section('title', 'Inventaris & Stok Pangan')

@section('header_actions')
<div class="btn-group btn-group-sm">
    <a href="{{ route('reports.inventory', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info" target="_blank"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
    <a href="{{ route('inventory.create') }}" class="btn btn-success"><i class="fas fa-plus mr-1"></i> Tambah Item</a>
    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-primary"><i class="fas fa-exchange-alt mr-1"></i> Transaksi</a>
</div>
@endsection

@section('content')
{{-- Stock Status Cards (Kledo style) --}}
<div class="row mb-4 dash-row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px;height:48px;background:#d1fae5;">
                            <span style="color:#059669;font-size:20px;font-weight:700;">{{ number_format($stats['tersedia']) }}</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;">Stok Aman</div>
                    </div>
                    <div class="col">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px;height:48px;background:#fef3c7;">
                            <span style="color:#d97706;font-size:20px;font-weight:700;">{{ number_format($stats['menipis']) }}</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;">Stok Menipis</div>
                    </div>
                    <div class="col">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px;height:48px;background:#fce7f3;">
                            <span style="color:#db2777;font-size:20px;font-weight:700;">{{ number_format($stats['habis']) }}</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;">Stok Habis</div>
                    </div>
                    <div class="col">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px;height:48px;background:#fee2e2;">
                            <span style="color:#dc2626;font-size:20px;font-weight:700;">{{ number_format($stats['rusak']) }}</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;">Rusak</div>
                    </div>
                    <div class="col">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px;height:48px;background:#dbeafe;">
                            <span style="color:#2563eb;font-size:20px;font-weight:700;">{{ number_format($stats['total']) }}</span>
                        </div>
                        <div style="font-size:12px;color:#64748b;">Total Item</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Data Table --}}
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-boxes mr-2"></i>Daftar Inventaris</h5>
        <div class="d-flex">
            <form class="form-inline mr-2" method="GET">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari item..." value="{{ request('search') }}" style="border-radius:8px;width:200px;">
                <button class="btn btn-sm btn-outline-success ml-1"><i class="fas fa-search"></i></button>
            </form>
            <select class="form-control form-control-sm" style="width:120px;border-radius:8px;" onchange="filterCategory(this.value)">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input"></th>
                        <th>Nama Item</th>
                        <th>Kode</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Min.</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td style="font-weight:600;">{{ $item->nama_item }}</td>
                        <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $item->kode_item }}</span></td>
                        <td><span class="badge badge-info">{{ $item->category?->nama_kategori ?? '-' }}</span></td>
                        <td style="font-size:12px;">{{ $item->satuan }}</td>
                        <td>
                            @if($item->stok_saat_ini <= 0)
                                <span class="badge badge-danger">0</span>
                            @elseif($item->stok_saat_ini <= $item->stok_minimum)
                                <span class="badge badge-warning">{{ number_format($item->stok_saat_ini,0,',','.') }}</span>
                            @else
                                <span class="badge badge-success">{{ number_format($item->stok_saat_ini,0,',','.') }}</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#94a3b8;">{{ number_format($item->stok_minimum,0,',','.') }}</td>
                        <td style="font-size:12px;">Rp {{ number_format($item->harga_beli_rata,0,',','.') }}</td>
                        <td style="font-size:12px;">Rp {{ number_format($item->harga_jual,0,',','.') }}</td>
                        <td>
                            @php $ist = ['tersedia'=>'success','stok_habis'=>'danger','kadaluarsa'=>'warning','rusak'=>'danger','nonaktif'=>'secondary']; @endphp
                            <span class="badge badge-{{ $ist[$item->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$item->status)) }}</span>
                            @if($item->status_histories_count > 0)
                                <a href="{{ route('inventory.show', array_merge([$item], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="badge badge-light border ml-1" title="{{ $item->status_histories_count }}x perubahan status" style="font-size:11px;"><i class="fas fa-history"></i> {{ $item->status_histories_count }}</a>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventory.show', array_merge([$item], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-success"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-4">
                            <i class="fas fa-box-open fa-3x mb-3" style="color:#cbd5e1;"></i>
                            <p style="color:#94a3b8;">Belum ada item inventaris</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Menampilkan {{ $items->firstItem() }}-{{ $items->lastItem() }} dari {{ $items->total() }}</small>
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

@section('extra_js')
<script>
function filterCategory(val) {
    const url = new URL(window.location);
    if (val) url.searchParams.set('category_id', val); else url.searchParams.delete('category_id');
    window.location = url;
}
</script>
@endsection
