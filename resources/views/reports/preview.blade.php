@extends('adminlte::master')

@section('title', $def['title'])

@section('header_actions')
<a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Semua Laporan</a>
<a href="{{ route($def['route'], request()->except('preview')) }}" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf mr-1"></i> Download PDF</a>
@endsection

@section('content')
@include('reports.partials.filter-bar', [
    'route' => $def['route'],
    'formAction' => route('reports.preview', $key),
    'title' => 'Filter ' . $def['title'],
    'filters' => $filters,
    'show_date_range' => $def['show_date'],
])

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title" style="margin:0;">
            <i class="fas {{ $def['icon'] }} mr-2" style="color:{{ $def['color'] }}"></i>{{ $def['title'] }}
        </h5>
        @php
            $activeFilters = collect(request()->query())->except(['preview'])->filter()->keys();
        @endphp
        <div>
            @if($activeFilters->isNotEmpty())
            <span class="badge badge-success" style="font-size:11px;"><i class="fas fa-check mr-1"></i>{{ $activeFilters->count() }} filter aktif</span>
            @else
            <span class="badge badge-light" style="font-size:11px;color:#64748b;"><i class="fas fa-filter mr-1"></i>Semua data</span>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <iframe id="reportPreviewFrame" src="{{ $iframeSrc }}" style="width:100%;height:860px;border:0;display:block;"></iframe>
    </div>
</div>
@endsection

@section('extra_js')
<script>
// Auto-adjust iframe height to fit the report content
document.addEventListener('DOMContentLoaded', function () {
    const frame = document.getElementById('reportPreviewFrame');
    if (!frame) return;
    const resize = () => {
        try {
            const doc = frame.contentWindow.document;
            frame.style.height = Math.max(doc.body.scrollHeight, 600) + 'px';
        } catch (e) { /* cross-origin or not loaded yet */ }
    };
    frame.addEventListener('load', resize);
    setTimeout(resize, 500);
});
</script>
@endsection
