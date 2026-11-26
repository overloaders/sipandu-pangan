@extends('adminlte::master')
@section('title', 'Detail Item: ' . $item->nama_item)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-box mr-2"></i>Detail Item</h5>
                <div>
                    <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-edit mr-1"></i> Edit</a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('inventory.destroy', $item) }}','{{ $item->nama_item }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                    <div>
                        <a href="{{ route('reports.inventory-detail', array_merge([$item->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Item</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $item->kode_item }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama Item</td><td style="font-weight:700;">{{ $item->nama_item }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Kategori</td><td><span class="badge badge-info">{{ $item->category?->nama_kategori ?? '-' }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Satuan</td><td>{{ $item->satuan }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Stok Saat Ini</td><td style="font-weight:700;font-size:16px;color:{{ $item->stok_saat_ini > $item->stok_minimum ? '#059669' : '#dc2626' }};">{{ number_format($item->stok_saat_ini,0,',','.') }} {{ $item->satuan }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Stok Minimum</td><td>{{ number_format($item->stok_minimum,0,',','.') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Harga Beli</td><td>Rp {{ number_format($item->harga_beli_rata,0,',','.') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Harga Jual</td><td>Rp {{ number_format($item->harga_jual,0,',','.') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $item->status=='tersedia'?'success':'danger' }}">{{ ucfirst(str_replace('_',' ',$item->status)) }}</span></td></tr>
                </table></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Transaksi</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Kode</th><th>Tipe</th><th>Sumber</th><th>Jumlah</th><th>Tanggal</th></tr></thead><tbody>
                @forelse($item->transactions as $t)
                <tr>
                    <td><span class="badge badge-secondary" style="font-family:monospace;font-size:10px;">{{ $t->kode_transaksi }}</span></td>
                    <td><span class="badge badge-{{ in_array($t->tipe_transaksi,['masuk'])?'success':'danger' }}">{{ ucfirst($t->tipe_transaksi) }}</span></td>
                    <td style="font-size:12px;">{{ ucfirst(str_replace('_',' ',$t->sumber)) }}</td>
                    <td style="font-weight:700;color:{{ in_array($t->tipe_transaksi,['masuk'])?'#059669':'#dc2626' }};">{{ in_array($t->tipe_transaksi,['masuk'])?'+':'-' }}{{ number_format($t->jumlah,0,',','.') }}</td>
                    <td style="font-size:12px;">{{ $t->tanggal_transaksi->format('d/m/Y') }}</td>
                </tr>
                @empty <tr><td colspan="5" class="text-center" style="color:#94a3b8;">Belum ada transaksi</td></tr> @endforelse
                </tbody></table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card" style="margin-bottom:16px;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-label">Nilai Stok Saat Ini</div>
                    <div style="font-size:18px;font-weight:700;">Rp {{ number_format($item->stok_saat_ini * $item->harga_beli_rata,0,',','.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- UBAH STATUS --}}
<div class="card">
    <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
    <div class="card-body">
        <div class="d-flex flex-wrap" style="gap:6px;">
            @php $istColors = ['tersedia' => 'success', 'stok_habis' => 'danger', 'kadaluarsa' => 'warning', 'rusak' => 'danger', 'nonaktif' => 'secondary']; @endphp
            @foreach($istColors as $st => $color)
                @if($st !== $item->status)
                <form action="{{ route('inventory.status', $item) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Ubah status menjadi {{ ucfirst(str_replace('_',' ',$st)) }}?');">
                    @csrf
                    <input type="hidden" name="status" value="{{ $st }}">
                    <button type="submit" class="btn btn-sm btn-{{ $color }}">{{ ucfirst(str_replace('_',' ',$st)) }}</button>
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
        <a href="{{ route('reports.inventory-detail', $item) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
    <div class="card-body p-0">
        @php $invColors2 = ['tersedia' => 'success', 'stok_habis' => 'danger', 'kadaluarsa' => 'warning', 'rusak' => 'danger', 'nonaktif' => 'secondary']; @endphp
        <div class="table-responsive"><table class="table table-hover mb-0">
            <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($item->statusHistories->sortByDesc('created_at') as $h)
                <tr>
                    <td style="font-size:12px;white-space:nowrap;">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($h->status_lama)
                            <span class="badge badge-{{ $invColors2[$h->status_lama] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_lama)) }}</span>
                            <i class="fas fa-arrow-right mx-1" style="font-size:10px;color:#94a3b8;"></i>
                        @endif
                        <span class="badge badge-{{ $invColors2[$h->status_baru] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$h->status_baru)) }}</span>
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
