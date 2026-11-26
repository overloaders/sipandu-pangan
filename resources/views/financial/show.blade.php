@extends('adminlte::master')
@section('title', 'Detail Transaksi Keuangan')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-wallet mr-2"></i>Detail Transaksi</h5>
                <div>
                    @if($transaction->status !== 'disetujui')
                    <a href="{{ route('financial.edit', $transaction) }}" class="btn btn-sm btn-warning" title="Edit / Ubah Status"><i class="fas fa-edit mr-1"></i> Edit</a>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('financial.destroy', $transaction) }}','{{ $transaction->deskripsi }}')" title="Hapus"><i class="fas fa-trash"></i></button>
                    <div>
                        <a href="{{ route('reports.financial-detail', array_merge([$transaction->id], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                        <a href="{{ route('financial.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Transaksi</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $transaction->kode_transaksi_keu }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tipe</td><td><span class="badge badge-{{ $transaction->tipe=='pemasukan'?'success':'danger' }}">{{ ucfirst($transaction->tipe) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Deskripsi</td><td style="font-weight:700;">{{ $transaction->deskripsi }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jumlah</td><td style="font-size:18px;font-weight:700;color:{{ $transaction->tipe=='pemasukan'?'#059669':'#dc2626' }};">{{ $transaction->tipe=='pemasukan'?'+':'-' }} Rp {{ number_format($transaction->jumlah,0,',','.') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Sumber Dana</td><td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$transaction->sumber_dana)) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Metode Bayar</td><td>{{ ucfirst($transaction->metode_pembayaran) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tanggal</td><td>{{ $transaction->tanggal_transaksi->format('d M Y') }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $transaction->status=='disetujui'||$transaction->status=='selesai'?'success':($transaction->status=='ditolak'?'danger':'warning') }}">{{ ucfirst(str_replace('_',' ',$transaction->status)) }}</span></td></tr>
                    @if($transaction->approved_by)<tr><td style="font-weight:600;color:#64748b;">Disetujui Oleh</td><td>{{ optional($transaction->approver)->name ?? '-' }}</td></tr>@endif
                    <tr><td style="font-weight:600;color:#64748b;">Kategori</td><td>{{ $transaction->category?->nama_kategori ?? '-' }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Unit</td><td>{{ $transaction->unit?->nama_unit ?? '-' }}</td></tr>
                </table></div>
            </div>
        </div>
        @if($transaction->status !== 'disetujui')
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-exchange-alt mr-2"></i>Ubah Status</h5></div>
            <div class="card-body">
                <div class="d-flex flex-wrap" style="gap:6px;">
                    @foreach(['draft' => 'secondary', 'pending_approval' => 'info', 'disetujui' => 'success', 'ditolak' => 'danger', 'selesai' => 'dark'] as $st => $color)
                        @if($st !== $transaction->status)
                        <form action="{{ route('financial.status', $transaction) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Ubah status menjadi {{ ucfirst(str_replace('_',' ',$st)) }}?');">
                            @csrf
                            <input type="hidden" name="status" value="{{ $st }}">
                            <button type="submit" class="btn btn-sm btn-{{ $color }}">
                                <i class="fas fa-{{ $st=='disetujui'?'check':($st=='ditolak'?'times':($st=='selesai'?'flag':'clock')) }} mr-1"></i>
                                {{ ucfirst(str_replace('_',' ',$st)) }}
                            </button>
                        </form>
                        @endif
                    @endforeach
                </div>
                <small class="form-text text-muted mt-2">Transaksi yang sudah <strong>disetujui</strong> terkunci dan tidak dapat diubah lagi.</small>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-history mr-2"></i>Riwayat Status</h5>
        <a href="{{ route('reports.financial-detail', $transaction) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Buka PDF detail"><i class="fas fa-file-pdf mr-1"></i> PDF Detail</a>
    </div>
            <div class="card-body p-0">
                @php $sc = ['draft' => 'secondary', 'pending_approval' => 'info', 'disetujui' => 'success', 'ditolak' => 'danger', 'selesai' => 'dark']; @endphp
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead><tr><th>Waktu</th><th>Perubahan</th><th>Oleh</th></tr></thead>
                    <tbody>
                    @forelse($transaction->statusHistories->sortByDesc('created_at') as $h)
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
    </div>
</div>
@endsection
