@extends('adminlte::master')
@section('title', 'Keuangan & Anggaran')
@section('header_actions')
<a href="{{ route('reports.financial', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
<a href="{{ route('financial.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Tambah Transaksi</a>
@endsection
@section('content')
<div class="row mb-4 dash-row">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #10b981;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#d1fae5;color:#059669;width:40px;height:40px;font-size:16px;"><i class="fas fa-arrow-down"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;color:#059669;">Rp {{ number_format($stats['pemasukan_bulan'],0,',','.') }}</div>
                    <div class="stat-label">Pemasukan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #ef4444;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#fee2e2;color:#dc2626;width:40px;height:40px;font-size:16px;"><i class="fas fa-arrow-up"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;color:#dc2626;">Rp {{ number_format($stats['pengeluaran_bulan'],0,',','.') }}</div>
                    <div class="stat-label">Pengeluaran Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="stat-card" style="border-left:4px solid #3b82f6;">
            <div class="d-flex align-items-center">
                <div class="stat-icon mr-3" style="background:#dbeafe;color:#2563eb;width:40px;height:40px;font-size:16px;"><i class="fas fa-balance-scale"></i></div>
                <div>
                    <div class="stat-value" style="font-size:18px;">Rp {{ number_format($stats['saldo'],0,',','.') }}</div>
                    <div class="stat-label">Saldo Total</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card dash-row">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:8px;">
        <h5 class="card-title" style="margin:0;"><i class="fas fa-wallet mr-2"></i>Transaksi Keuangan</h5>
        <form action="{{ route('financial.index') }}" method="GET" class="form-inline">
            <select name="tipe" class="form-control form-control-sm mr-2">
                <option value="">Semua Tipe</option>
                <option value="pemasukan" {{ request('tipe')=='pemasukan'?'selected':'' }}>Pemasukan</option>
                <option value="pengeluaran" {{ request('tipe')=='pengeluaran'?'selected':'' }}>Pengeluaran</option>
            </select>
            <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
            <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            @if(request()->hasAny(['tipe','from_date','to_date']))
            <a href="{{ route('financial.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Kode</th><th>Tanggal</th><th>Tipe</th><th>Deskripsi</th><th>Sumber</th><th>Jumlah</th><th>Status</th><th width="80">Aksi</th></tr></thead>
                <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td><span class="badge badge-success" style="font-family:monospace;font-size:11px;">{{ $txn->kode_transaksi_keu }}</span></td>
                    <td style="font-size:12px;">{{ $txn->tanggal_transaksi->format('d/m/Y') }}</td>
                    <td><span class="badge badge-{{ $txn->tipe=='pemasukan'?'success':'danger' }}">{{ ucfirst($txn->tipe) }}</span></td>
                    <td style="font-weight:600;">{{ $txn->deskripsi }}</td>
                    <td><span class="badge badge-secondary">{{ ucfirst(str_replace('_',' ',$txn->sumber_dana)) }}</span></td>
                    <td style="font-weight:700;color:{{ $txn->tipe=='pemasukan'?'#059669':'#dc2626' }};">{{ $txn->tipe=='pemasukan'?'+':'-' }} Rp {{ number_format($txn->jumlah,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $txn->status=='disetujui'||$txn->status=='selesai'?'success':($txn->status=='ditolak'?'danger':'warning') }}">{{ ucfirst(str_replace('_',' ',$txn->status)) }}</span></td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('financial.show', array_merge([$txn], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></a>
                        @if($txn->status !== 'disetujui')
                        <a href="{{ route('financial.edit', $txn) }}" class="btn btn-sm btn-outline-warning" title="Edit / Ubah Status"><i class="fas fa-edit"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4"><p style="color:#94a3b8;">Belum ada transaksi</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())<div class="card-footer">{{ $transactions->withQueryString()->links() }}</div>@endif
</div>
@endsection
