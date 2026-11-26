@extends('adminlte::page')

@section('title', 'Kualitas Air - ' . $fishPond->nama_kolam)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title" style="margin:0;"><i class="fas fa-tint mr-2"></i>Riwayat Pemeriksaan Kualitas Air</h5>
                <a href="{{ route('fish-ponds.show', $fishPond) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Kolam</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Tanggal</th><th>Jam</th><th>pH</th><th>Suhu</th><th>DO</th><th>Amonia</th><th>Nitrit</th><th>Nitrat</th><th>Kekeruhan</th><th>Salinitas</th><th>Kondisi</th><th>Pemeriksa</th></tr>
                    </thead>
                    <tbody>
                        @forelse($fishPond->waterQualityRecords as $wq)
                        <tr>
                            <td>{{ $wq->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                            <td>{{ $wq->jam_pemeriksaan ?? '-' }}</td>
                            <td>
                                @if($wq->ph)
                                    @if($wq->ph >= 6.5 && $wq->ph <= 8.5)
                                        <span class="badge badge-success">{{ $wq->ph }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $wq->ph }}</span>
                                    @endif
                                @else - @endif
                            </td>
                            <td>{{ $wq->suhu_celsius ?? '-' }}°C</td>
                            <td>{{ $wq->dissolved_oxygen_mgl ?? '-' }} mg/L</td>
                            <td>
                                @if($wq->amonia_mgl)
                                    @if($wq->amonia_mgl < 0.5)
                                        <span class="badge badge-success">{{ $wq->amonia_mgl }}</span>
                                    @elseif($wq->amonia_mgl < 1.0)
                                        <span class="badge badge-warning">{{ $wq->amonia_mgl }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $wq->amonia_mgl }}</span>
                                    @endif
                                @else - @endif
                            </td>
                            <td>{{ $wq->nitrit_mgl ?? '-' }}</td>
                            <td>{{ $wq->nitrat_mgl ?? '-' }}</td>
                            <td>{{ $wq->kekeruhan_ntu ?? '-' }} NTU</td>
                            <td>{{ $wq->salinitas_ppt ?? '-' }} ppt</td>
                            <td>
                                @if($wq->kondisi_air == 'baik')
                                    <span class="badge badge-success">🟢 Baik</span>
                                @elseif($wq->kondisi_air == 'kurang_baik')
                                    <span class="badge badge-warning">🟡 Kurang Baik</span>
                                @elseif($wq->kondisi_air == 'buruk')
                                    <span class="badge badge-danger">🟠 Buruk</span>
                                @else
                                    <span class="badge badge-dark">🔴 Sangat Buruk</span>
                                @endif
                            </td>
                            <td>{{ $wq->pemeriksa_id ?? '-' }}</td>
                        </tr>
                        @if($wq->tindakan_perlukan)
                        <tr><td colspan="12" style="font-size:11px;color:#64748b;background:#f8fafc;"><i class="fas fa-exclamation-triangle text-warning"></i> Tindakan: {{ $wq->tindakan_perlukan }}</td></tr>
                        @endif
                        @empty
                        <tr><td colspan="12" class="text-center" style="color:#94a3b8;">Belum ada pemeriksaan kualitas air</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        {{-- CHART GRAFIK pH DAN AMONIA --}}
        @if($fishPond->waterQualityRecords->count() >= 2)
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-chart-line mr-2"></i>Grafik Tren Kualitas Air</h5></div>
            <div class="card-body"><canvas id="chartWaterQuality" height="300"></canvas></div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- FORM TAMBAH --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-flask mr-2"></i>Tambah Pemeriksaan</h5></div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>{{ session('success') }}</div>
                @endif
                <form action="{{ route('fish-ponds.store-water-quality', $fishPond) }}" method="POST">
                    @csrf
                    <div class="form-group"><label><strong>Tanggal *</strong></label><input type="date" name="tanggal_pemeriksaan" class="form-control" value="{{ now()->format('Y-m-d') }}" required></div>
                    <div class="form-group"><label><strong>Jam</strong></label><input type="time" name="jam_pemeriksaan" class="form-control" value="{{ now()->format('H:i') }}"></div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>pH</strong></label><input type="number" name="ph" class="form-control" step="0.1" min="0" max="14"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Suhu (°C)</strong></label><input type="number" name="suhu_celsius" class="form-control" step="0.1"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>DO (mg/L)</strong></label><input type="number" name="dissolved_oxygen_mgl" class="form-control" step="0.1"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Amonia (mg/L)</strong></label><input type="number" name="amonia_mgl" class="form-control" step="0.01"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Nitrit</strong></label><input type="number" name="nitrit_mgl" class="form-control" step="0.01"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Nitrat</strong></label><input type="number" name="nitrat_mgl" class="form-control" step="0.01"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-6"><div class="form-group"><label><strong>Kekeruhan</strong></label><input type="number" name="kekeruhan_ntu" class="form-control" step="0.1"></div></div>
                        <div class="col-6"><div class="form-group"><label><strong>Salinitas</strong></label><input type="number" name="salinitas_ppt" class="form-control" step="0.1"></div></div>
                    </div>
                    <div class="form-group"><label><strong>Kondisi Air *</strong></label>
                        <select name="kondisi_air" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="baik">🟢 Baik</option>
                            <option value="kurang_baik">🟡 Kurang Baik</option>
                            <option value="buruk">🟠 Buruk</option>
                            <option value="sangat_buruk">🔴 Sangat Buruk</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><strong>Pemeriksa</strong></label>
                        <select name="pemeriksa_id" class="form-control">
                            <option value="">-- Pilih (opsional) --</option>
                            @foreach($wbps as $wbp)
                            <option value="{{ $wbp->id }}">{{ $wbp->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label><strong>Tindakan</strong></label><textarea name="tindakan_perlukan" class="form-control" rows="2" placeholder="Tambahan aerasi, kurangi pakan..."></textarea></div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>

        {{-- STANDAR --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title" style="margin:0;"><i class="fas fa-info-circle mr-2"></i>Standar Kualitas Air</h5></div>
            <div class="card-body" style="font-size:12px;">
                <div class="table-responsive"><table class="table table-sm table-bordered mb-0">
                    <thead><tr><th>Parameter</th><th>Standar</th></tr></thead>
                    <tbody>
                        <tr><td>pH</td><td>6.5 - 8.5</td></tr>
                        <tr><td>Suhu</td><td>25 - 30 °C</td></tr>
                        <tr><td>DO</td><td>≥ 5 mg/L</td></tr>
                        <tr><td>Amonia</td><td>< 0.5 mg/L</td></tr>
                        <tr><td>Nitrit</td><td>< 0.1 mg/L</td></tr>
                        <tr><td>Nitrat</td><td>< 50 mg/L</td></tr>
                        <tr><td>Kekeruhan</td><td>< 25 NTU</td></tr>
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($fishPond->waterQualityRecords->count() >= 2)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const records = {!! json_encode($fishPond->waterQualityRecords->sortBy('tanggal_pemeriksaan')->values()->map(fn($r) => [
    'date' => $r->tanggal_pemeriksaan->format('d/m'),
    'ph' => $r->ph,
    'suhu' => $r->suhu_celsius,
    'do2' => $r->dissolved_oxygen_mgl,
    'amonia' => $r->amonia_mgl,
    'kekeruhan' => $r->kekeruhan_ntu,
])->toArray()) !!};

new Chart(document.getElementById('chartWaterQuality'), {
    type: 'line',
    data: {
        labels: records.map(r => r.date),
        datasets: [
            { label: 'pH', data: records.map(r => r.ph), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3 },
            { label: 'Amonia (mg/L)', data: records.map(r => r.amonia), borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', fill: true, tension: 0.3 },
            { label: 'DO (mg/L)', data: records.map(r => r.do2), borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.3 },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@endif
@endpush
