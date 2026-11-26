@extends('adminlte::page')
@section('title', 'Rencana Kerja Pertanian')
@section('header_actions')
    <a href="{{ route('reports.farming', array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null)) }}" class="btn btn-success btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> Export PDF</a>
    <a href="{{ route('farming-plans.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Rencana</a>
@endsection
@section('content')
<div class="card dash-row">
    <div class="card-header">
        <form action="{{ route('farming-plans.index') }}" method="GET" class="form-inline">
            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari kode..." value="{{ request('search') }}">
            <select name="status" class="form-control form-control-sm mr-2"><option value="">Semua Status</option><option value="direncanakan">Direncanakan</option><option value="sedang_ditanam">Sedang Ditanam</option><option value="siap_panen">Siap Panen</option><option value="selesai_panen">Selesai Panen</option></select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead><tr><th>#</th><th>Kode</th><th>Crop</th><th>Lahan</th><th>Jml Tanaman</th><th>Tgl Tanam</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($farmingPlans as $i => $f)
                <tr>
                    <td>{{ $farmingPlans->firstItem()+$i }}</td>
                    <td><code>{{ $f->kode_rencana }}</code></td>
                    <td>{{ $f->cropType->nama_tanaman ?? '-' }}</td>
                    <td>{{ $f->landPlot->nama_lahan ?? '-' }}</td>
                    <td>{{ number_format($f->jumlah_tanaman) }}</td>
                    <td>{{ $f->tanggal_tanam_aktual?->format('d/m/Y') ?? $f->tanggal_tanam_rencana?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="badge badge-{{ $f->status=='selesai_panen'?'success':($f->status=='sedang_ditanam'?'primary':'info') }}">{{ str_replace('_',' ',ucfirst($f->status)) }}</span></td>
                    <td>
                        <a href="{{ route('farming-plans.show', array_merge([$f], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('farming-plans.edit', $f) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete('{{ route('farming-plans.destroy', $f) }}','{{ $f->kode_rencana }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                @empty <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $farmingPlans->withQueryString()->links() }}</div>
</div>
@stop
