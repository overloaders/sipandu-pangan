@extends('adminlte::master')
@section('title', 'Detail Kandang: ' . $livestockBarn->nama_kandang)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-warehouse mr-2"></i>Detail Kandang</h5>
                <div>
                    <a href="{{ route('reports.livestock-barn', array_merge([$livestockBarn], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                    <a href="{{ route('livestock-barns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Kandang</td><td><span class="badge badge-success" style="font-family:monospace;">{{ $livestockBarn->kode_kandang }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $livestockBarn->nama_kandang }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Jenis Ternak</td><td><span class="badge badge-info">{{ ucfirst($livestockBarn->jenis_ternak) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Kapasitas</td><td>{{ $livestockBarn->terisi }} / {{ $livestockBarn->kapasitas }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $livestockBarn->status=='aktif'?'success':'secondary' }}">{{ ucfirst($livestockBarn->status) }}</span></td></tr>
                </table></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-paw mr-2"></i>Ternak ({{ $livestockBarn->inventories->count() }})</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Kode</th><th>Jenis</th><th>Jumlah</th><th>Berat</th><th>Status</th></tr></thead><tbody>
                @forelse($livestockBarn->inventories as $inv)
                <tr>
                    <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $inv->kode_ternak }}</span></td>
                    <td>{{ $inv->livestockType?->nama_jenis ?? '-' }}</td>
                    <td>{{ $inv->jumlah }}</td>
                    <td>{{ number_format($inv->berat_rata_kg ?? 0, 1) }} kg</td>
                    <td><span class="badge badge-{{ $inv->status=='hidup'?'success':'danger' }}">{{ ucfirst($inv->status) }}</span></td>
                </tr>
                @empty <tr><td colspan="5" class="text-center" style="color:#94a3b8;">Belum ada ternak</td></tr> @endforelse
                </tbody></table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-heartbeat mr-2"></i>Kesehatan</h5></div>
            <div class="card-body">
                @forelse($livestockBarn->healthRecords->take(5) as $hr)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div><span class="badge badge-info">{{ ucfirst($hr->jenis_pemeriksaan) }}</span> {{ $hr->tanggal_pemeriksaan->format('d/m') }}</div>
                    <span class="badge badge-{{ $hr->hasil=='sehat'?'success':'warning' }}">{{ ucfirst($hr->hasil) }}</span>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada rekam medis</p> @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
