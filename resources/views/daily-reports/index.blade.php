@extends('adminlte::master')
@section('title', 'Laporan Harian')
@section('header_actions')
<a href="{{ route('reports.daily-reports', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a>
<a href="{{ route('daily-reports.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus mr-1"></i> Buat Laporan</a>
@endsection
@section('content')
<div class="card dash-row">
    <div class="card-header">
        <form action="{{ route('daily-reports.index') }}" method="GET" class="form-inline">
            <select name="status" class="form-control form-control-sm mr-2" style="width:auto;">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="dikirim" {{ request('status')=='dikirim'?'selected':'' }}>Dikirim</option>
                <option value="dikonfirmasi" {{ request('status')=='dikonfirmasi'?'selected':'' }}>Dikonfirmasi</option>
            </select>
            <input type="date" name="from_date" class="form-control form-control-sm mr-2" value="{{ request('from_date') }}" title="Dari tanggal">
            <input type="date" name="to_date" class="form-control form-control-sm mr-2" value="{{ request('to_date') }}" title="Sampai tanggal">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
            @if(request()->hasAny(['status','from_date','to_date']))
            <a href="{{ route('daily-reports.index') }}" class="btn btn-sm btn-default ml-1"><i class="fas fa-times"></i> Reset</a>
            @endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Kode</th><th>Tanggal</th><th>Unit</th><th>Pelapor</th><th>WBP Aktif</th><th>Panen (kg)</th><th>Status</th><th width="100">Aksi</th></tr></thead>
                <tbody>
                @forelse($reports as $report)
                <tr>
                    <td><span class="badge badge-success" style="font-family:monospace;">{{ $report->kode_laporan }}</span></td>
                    <td>{{ $report->tanggal_laporan->format('d/m/Y') }}</td>
                    <td>{{ $report->unit?->nama_unit ?? '-' }}</td>
                    <td>{{ $report->reporter?->name ?? '-' }}</td>
                    <td>{{ $report->total_wbp_aktif }}</td>
                    <td>{{ number_format($report->total_panen_hari_ini_kg ?? 0,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $report->status=='dikonfirmasi'?'success':($report->status=='dikirim'?'info':'secondary') }}">{{ ucfirst($report->status) }}</span></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('daily-reports.show', array_merge([$report], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-outline-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                            @if($report->status === 'draft')
                            <button type="button" class="btn btn-sm btn-warning" title="Kirim"
                                onclick="swalConfirmPost('{{ route('daily-reports.kirim', $report) }}', 'Kirim Laporan?', 'Laporan ini akan dikirim untuk review.', '#f59e0b', 'Ya, Kirim')">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            @endif
                            @if($report->status === 'dikirim' && auth()->user()->hasRole(['admin','super-admin','supervisor']))
                            <button type="button" class="btn btn-sm btn-success" title="Konfirmasi"
                                onclick="swalConfirmPost('{{ route('daily-reports.confirm', $report) }}', 'Konfirmasi Laporan?', 'Laporan ini akan dikonfirmasi.', '#22c55e', 'Ya, Konfirmasi')">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4"><p style="color:#94a3b8;">Belum ada laporan</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())<div class="card-footer">{{ $reports->withQueryString()->links() }}</div>@endif
</div>
@endsection
