@extends('adminlte::master')
@section('title', 'Detail Kolam: ' . $fishPond->nama_kolam)
@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-water mr-2"></i>Detail Kolam</h5>
                <div>
                    <a href="{{ route('reports.fish-pond', array_merge([$fishPond], array_filter(request()->except('page'), fn($v) => $v !== '' && $v !== null))) }}" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
                    <a href="{{ route('fish-ponds.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-borderless" style="font-size:13px;">
                    <tr><td width="180" style="font-weight:600;color:#64748b;">Kode Kolam</td><td><span class="badge badge-primary" style="font-family:monospace;">{{ $fishPond->kode_kolam }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Nama</td><td style="font-weight:700;">{{ $fishPond->nama_kolam }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Tipe</td><td><span class="badge badge-info">{{ ucfirst(str_replace('_',' ',$fishPond->tipe_kolam)) }}</span></td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Luas</td><td>{{ number_format($fishPond->luas_m2,0,',','.') }} m²</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Sumber Air</td><td>{{ ucfirst($fishPond->sumber_air) }}</td></tr>
                    <tr><td style="font-weight:600;color:#64748b;">Status</td><td><span class="badge badge-{{ $fishPond->status=='aktif'?'success':'secondary' }}">{{ ucfirst($fishPond->status) }}</span></td></tr>
                </table></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-fish mr-2"></i>Stok Ikan</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Kode</th><th>Jenis Ikan</th><th>Jumlah</th><th>Berat</th><th>Status</th></tr></thead><tbody>
                @forelse($fishPond->fishStocks as $fs)
                <tr>
                    <td><span class="badge badge-secondary" style="font-family:monospace;font-size:11px;">{{ $fs->kode_stok_ikan }}</span></td>
                    <td>{{ $fs->jenis_ikan }}</td>
                    <td>{{ number_format($fs->jumlah) }}</td>
                    <td>{{ number_format($fs->berat_rata_gram ?? 0,1) }} g</td>
                    <td><span class="badge badge-{{ $fs->status=='hidup'?'success':'danger' }}">{{ ucfirst($fs->status) }}</span></td>
                </tr>
                @empty <tr><td colspan="5" class="text-center" style="color:#94a3b8;">Belum ada stok ikan</td></tr> @endforelse
                </tbody></table></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-tint mr-2"></i>Kualitas Air</h5></div>
            <div class="card-body">
                @forelse($fishPond->waterQualityRecords->take(5) as $wq)
                <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:12px;">
                    <div>{{ $wq->tanggal_pemeriksaan->format('d/m') }} pH {{ $wq->ph ?? '-' }}</div>
                    <span class="badge badge-{{ $wq->kondisi_air=='baik'?'success':($wq->kondisi_air=='kurang_baik'?'warning':'danger') }}">{{ ucfirst(str_replace('_', ' ', $wq->kondisi_air)) }}</span>
                </div>
                @empty <p class="text-center" style="color:#94a3b8;font-size:12px;">Belum ada pemeriksaan</p> @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
