{{-- 
    Reusable Report Filter Bar Component
    
    Usage:
    @include('reports.partials.filter-bar', [
        'route' => 'reports.wbp',
        'title' => 'Laporan WBP',
        'filters' => [
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'dikeluarkan' => 'Dikeluarkan']],
            ['name' => 'unit_id', 'label' => 'Unit', 'type' => 'select', 'options' => $units->pluck('nama_unit', 'id')],
        ],
        'show_date_range' => true,
    ])
--}}

@php
    $filters = $filters ?? [];
    $showDateRange = $showDateRange ?? true;
    $title = $title ?? 'Laporan';
    $route = $route ?? '#';
    $formAction = $formAction ?? route($route);
    $previewRoute = $previewRoute ?? null;
@endphp

<div class="card card-outline card-primary">
    <div class="card-header">
        <h5 class="card-title" style="margin:0;">
            <i class="fas fa-filter text-primary"></i> <strong>Filter & Opsi Laporan</strong>
        </h5>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="reportFilterForm" method="GET" action="{{ $formAction }}">
            <div class="row filter-date-row">
                {{-- Date Range --}}
                @if($showDateRange)
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label><i class="fas fa-calendar text-muted"></i> Dari Tanggal</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="Semua">
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="form-group">
                        <label><i class="fas fa-calendar text-muted"></i> Sampai Tanggal</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="Semua">
                    </div>
                </div>
                @endif
                
                {{-- Dynamic Filters --}}
                @foreach($filters as $filter)
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label><i class="fas fa-tag text-muted"></i> {{ $filter['label'] }}</label>
                        @if($filter['type'] === 'select')
                            <select name="{{ $filter['name'] }}" class="form-control">
                                <option value="">Semua</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'text')
                            <input type="text" name="{{ $filter['name'] }}" class="form-control" placeholder="{{ $filter['placeholder'] ?? '' }}" value="{{ request($filter['name']) }}">
                        @endif
                    </div>
                </div>
                @endforeach
                
                {{-- Action Buttons --}}
                <div class="col-md-3 col-12">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="btn-group d-flex flex-wrap">
                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Terapkan Filter">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            
                            @if($previewRoute ?? null)
                            <a href="javascript:void(0)" onclick="previewReport()" class="btn btn-outline-info btn-sm" title="Preview">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                            @endif
                            
                            <button type="submit" class="btn btn-danger btn-sm" title="Download PDF" formaction="{{ route($route) }}">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            
                            <a href="{{ $formAction }}" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Quick Date Presets --}}
            @if($showDateRange)
            <div class="row mt-2">
                <div class="col-12">
                    <div class="btn-group btn-group-sm flex-wrap" role="group" style="gap:4px;">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Hari Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">Minggu Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">Bulan Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('quarter')">3 Bulan</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">Tahun Ini</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('all')">Semua</button>
                    </div>
                    <small class="text-muted ml-2" id="dateRangeLabel"></small>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>

@push('extra_js')
<script>
// Date range presets
function setDateRange(period) {
    const fromInput = document.querySelector('input[name="from_date"]');
    const toInput = document.querySelector('input[name="to_date"]');
    const today = new Date();
    let from, to;
    
    switch(period) {
        case 'today':
            from = to = today.toISOString().split('T')[0];
            break;
        case 'week':
            from = new Date(today.setDate(today.getDate() - today.getDay() + 1)).toISOString().split('T')[0];
            to = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            to = new Date().toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            from = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            to = new Date().toISOString().split('T')[0];
            break;
        case 'year':
            from = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            to = new Date().toISOString().split('T')[0];
            break;
        case 'all':
            from = '2020-01-01';
            to = new Date().toISOString().split('T')[0];
            break;
    }
    
    if(fromInput) fromInput.value = from;
    if(toInput) toInput.value = to;
    
    // Update label
    const label = document.getElementById('dateRangeLabel');
    if(label) label.textContent = `${from} s/d ${to}`;
    
    // Auto-submit filter
    document.getElementById('reportFilterForm').submit();
}

// Preview function (opens in new tab)
function previewReport() {
    const form = document.getElementById('reportFilterForm');
    const url = form.action + '?' + new URLSearchParams(new FormData(form)).toString();
    window.open(url + '&preview=1', '_blank');
}
</script>
@endpush
