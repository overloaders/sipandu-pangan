@extends('adminlte::master')

@section('title', 'Pusat Laporan')

@section('header_actions')
<span class="text-muted" style="font-size:13px;"><i class="fas fa-info-circle mr-1"></i> Pilih laporan, atur filter, lalu cetak atau unduh PDF</span>
@endsection

@section('content')
<div class="row dash-row">
    @foreach($reports as $key => $report)
    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
        <a href="{{ route('reports.preview', $key) }}" class="text-decoration-none">
            <div class="card h-100 report-card" style="border:none;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);transition:transform .15s ease, box-shadow .15s ease;cursor:pointer;">
                <div class="card-body d-flex align-items-start">
                    <div class="mr-3" style="width:46px;height:46px;border-radius:12px;background:{{ $report['color'] }}18;color:{{ $report['color'] }};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                        <i class="fas {{ $report['icon'] }}"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1e293b;font-size:14px;">{{ $report['title'] }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.5;">{{ $report['desc'] }}</div>
                        <div style="margin-top:8px;">
                            <span class="badge badge-light" style="font-size:10px;"><i class="fas fa-sliders-h mr-1"></i>Dengan Filter</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background:transparent;border-top:1px solid #f1f5f9;padding:10px 16px;">
                    <span style="color:{{ $report['color'] }};font-size:13px;font-weight:600;"><i class="fas fa-arrow-right mr-1"></i>Buka Laporan</span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection

@section('extra_js')
<style>
.report-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,.12) !important;
}
</style>
@endsection
