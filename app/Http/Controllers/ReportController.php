<?php

namespace App\Http\Controllers;

use App\Traits\UnitScope;
use App\Models\Wbp;
use App\Models\FoodProgram;
use App\Models\FarmingPlan;
use App\Models\Harvest;
use App\Models\LivestockBarn;
use App\Models\LivestockInventory;
use App\Models\FishPond;
use App\Models\FishStock;
use App\Models\InventoryItem;
use App\Models\FinancialTransaction;
use App\Models\FinancialCategory;
use App\Models\DailyReport;
use App\Models\Unit;
use App\Models\InventoryCategory;
use App\Models\CropType;
use App\Models\LivestockType;
use App\Models\Distribution;
use App\Models\DistributionItem;
use App\Models\NutritionPlan;
use App\Models\TrainingProgram;
use App\Models\TrainingParticipant;
use App\Models\TrainingCertification;
use App\Models\ComplianceRecord;
use App\Models\AnalyticsIndicator;
use App\Models\ProcessingBatch;
use App\Models\StockOpname;
use App\Models\LandPlot;
use App\Models\Block;
use App\Models\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportController extends Controller
{
    use UnitScope;

    /**
     * Get unit_id for scoping queries.
     * Super-admin with no filter = null (all units).
     * Non-super-admin = user's own unit_id.
     */
    private function getUnitId(Request $request): ?int
    {
        $user = auth()->user();
        if ($user && $user->hasRole('super-admin')) {
            if ($request->filled('unit_id')) {
                return (int) $request->unit_id;
            }
            return null;
        }
        return $user->unit_id ?? null;
    }

    /**
     * Cache daftar kolom per tabel (SHOW COLUMNS) agar tidak query schema berulang kali.
     */
    protected static array $unitIdColumnCache = [];

    /**
     * Cek apakah tabel punya kolom unit_id (dengan cache per request).
     */
    private function tableHasUnitId(string $table): bool
    {
        if (!array_key_exists($table, self::$unitIdColumnCache)) {
            self::$unitIdColumnCache[$table] = in_array(
                'unit_id',
                \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing($table)
            );
        }
        return self::$unitIdColumnCache[$table];
    }

    /**
     * Apply unit scoping to query.
     */
    private function scopeByUnit($query, ?int $unitId)
    {
        if (!$unitId) return $query;

        $table = $query->getModel()->getTable();

        if ($this->tableHasUnitId($table)) {
            return $query->where("{$table}.unit_id", $unitId);
        }
        return $query;
    }

    /**
     * Get unit name for display.
     */
    private function getUnitName(?int $unitId): string
    {
        if (!$unitId) return 'Semua Unit';
        return Unit::find($unitId)?->nama_unit ?? 'Semua Unit';
    }

    /**
     * Render PDF or preview HTML based on request parameter.
     */
    protected function renderOrDownload($view, array $data, string $filename, $paper = 'a4', $orientation = 'portrait')
    {
        // Auto-inject unit (objek lengkap: logo, alamat, dll) & unitName untuk kop surat
        if (!isset($data['unit'])) {
            $request = request();
            $unitId = $this->getUnitId($request);
            if (isset($data['unitName']) && $data['unitName'] && $data['unitName'] !== 'Semua Unit') {
                // Detail report yang hanya mengirim unitName: resolve unit dari nama
                $data['unit'] = Unit::where('nama_unit', $data['unitName'])->first();
            } else {
                $data['unit'] = $unitId ? Unit::find($unitId) : null;
                $data['unitName'] = $this->getUnitName($unitId);
            }
        } elseif (!isset($data['unitName'])) {
            $data['unitName'] = $data['unit']?->nama_unit ?? 'Semua Unit';
        }

        $isPreview = request()->has('preview') || request()->input('preview') === '1';
        
        if ($isPreview) {
            $data['previewMode'] = true;
            return view($view, $data);
        }

        // Build filename with date range if filters are set
        $fromDate = $data['fromDate'] ?? null;
        $toDate = $data['toDate'] ?? null;
        if ($fromDate && $toDate) {
            $dateSuffix = '-' . $fromDate . '-sd-' . $toDate;
        } elseif ($fromDate) {
            $dateSuffix = '-dari-' . $fromDate;
        } elseif ($toDate) {
            $dateSuffix = '-sampai-' . $toDate;
        } else {
            $dateSuffix = '-' . now()->format('Y-m-d');
        }

        // Page margins ditangani via @page CSS di reports/partials/pdf-styles.
        // (Universal selector '*' di CSS akan membatalkan @page margin di dompdf — hindari.)
        // Render dompdf adalah bagian terberat (100ms-1s+); cache binary PDF per
        // (view + ukuran + orientasi + filter + unit efektif) dengan TTL 5 menit
        // agar permintaan berulang tidak render ulang.
        $cacheKey = $this->pdfCacheKey($view, $data, $paper, $orientation);
        // Binary PDF di-base64 agar aman disimpan di kolom text cache (DB store)
        $pdfBase64 = Cache::remember($cacheKey, 300, function () use ($view, $data, $paper, $orientation) {
            return base64_encode(Pdf::loadView($view, $data)->setPaper($paper, $orientation)->output());
        });
        return response(base64_decode($pdfBase64), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . $dateSuffix . '.pdf"',
        ]);
    }

    /**
     * Kunci cache PDF: view + ukuran + orientasi + URI request + unit efektif.
     * URI penuh (path + query) dipakai — bukan hanya query — karena route detail
     * membawa ID record di path (mis. /reports/financial/{transaction}); tanpa itu
     * semua PDF detail satu unit berbagi cache yang sama (PDF transaksi pertama
     * tersaji untuk semua transaksi). Unit efektif disertakan agar user
     * non-super-admin tidak saling menimpa cache (getUnitId() memaksa unit milik
     * user walau URL membawa unit_id lain).
     */
    private function pdfCacheKey(string $view, array $data, string $paper, string $orientation): string
    {
        $request = request();
        $payload = serialize([
            'uri' => $request->getRequestUri(),
            'unitId' => $this->getUnitId($request),
            'unitName' => $data['unitName'] ?? null,
        ]);
        return 'report-pdf:' . $view . ':' . $paper . ':' . $orientation . ':' . md5($payload);
    }

    /**
     * Laporan WBP (Registrasi Warga Binaan)
     */
    public function wbp(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = Wbp::with(['unit', 'block', 'room']);
        
        if ($search = $request->input('search')) $query->search($search);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $wbp = $query->orderBy('nama_lengkap')->get();
        
        $summary = [
            'total' => $wbp->count(),
            'aktif' => $wbp->where('status', 'aktif')->count(),
            'dikeluarkan' => $wbp->where('status', 'dikeluarkan')->count(),
            'pindah' => $wbp->where('status', 'pindah')->count(),
        ];
        
        return $this->renderOrDownload('reports.wbp', compact('wbp', 'summary'), 'laporan-wbp', 'a4', 'landscape');
    }

    /**
     * Laporan Program Pangan
     */
    public function foodPrograms(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = FoodProgram::with(['unit', 'participants']);
        
        if ($search = $request->input('search')) $query->where('nama_program', 'LIKE', "%{$search}%");
        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_program) $query->where('jenis_program', $request->jenis_program);
        $this->scopeByUnit($query, $unitId);
        
        $programs = $query->orderByDesc('created_at')->get();
        
        $summary = [
            'total' => $programs->count(),
            'aktif' => $programs->where('status', 'aktif')->count(),
            'perencanaan' => $programs->where('status', 'perencanaan')->count(),
            'selesai' => $programs->where('status', 'selesai')->count(),
            'total_anggaran' => $programs->sum('anggaran'),
        ];
        
        return $this->renderOrDownload('reports.food-programs', compact('programs', 'summary'), 'laporan-program-pangan', 'a4', 'portrait');
    }

    /**
     * Laporan Pertanian
     */
    public function farming(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = FarmingPlan::with(['landPlot', 'cropType', 'foodProgram']);
        
        if ($search = $request->input('search')) $query->where('kode_rencana', 'LIKE', "%{$search}%");
        if ($request->status) $query->where('status', $request->status);
        if ($request->program_id) $query->where('food_program_id', $request->program_id);
        $this->scopeByUnit($query, $unitId);
        
        $plans = $query->orderByDesc('created_at')->get();
        
        $summary = [
            'total' => $plans->count(),
            'direncanakan' => $plans->where('status', 'direncanakan')->count(),
            'proses_tanam' => $plans->where('status', 'proses_tanam')->count(),
            'tumbuh' => $plans->where('status', 'tumbuh')->count(),
            'siap_panen' => $plans->where('status', 'siap_panen')->count(),
            'panen' => $plans->where('status', 'panen')->count(),
        ];
        
        return $this->renderOrDownload('reports.farming', compact('plans', 'summary'), 'laporan-pertanian', 'a4', 'landscape');
    }

    /**
     * Laporan Panen
     */
    public function harvest(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = Harvest::with(['farmingPlan.cropType', 'farmingPlan.foodProgram']);
        
        if ($search = $request->input('search')) $query->where('kode_panen', 'LIKE', "%{$search}%");
        if ($request->kualitas) $query->where('kualitas', $request->kualitas);
        if ($request->from_date) $query->where('tanggal_panen', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_panen', '<=', $request->to_date);
        $this->scopeByUnit($query, $unitId);
        
        $harvests = $query->orderByDesc('tanggal_panen')->get();
        
        $summary = [
            'total' => $harvests->count(),
            'total_berat' => $harvests->sum('total_berat_kg'),
            'total_pendapatan' => $harvests->sum('total_pendapatan'),
        ];
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return $this->renderOrDownload('reports.harvest', compact('harvests', 'summary', 'fromDate', 'toDate'), 'laporan-panen', 'a4', 'landscape');
    }

    /**
     * Laporan Peternakan
     */
    public function livestock(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = LivestockBarn::with(['unit', 'inventories.livestockType']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kandang', 'LIKE', "%{$search}%")->orWhere('kode_kandang', 'LIKE', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $barns = $query->get();

        // Summary ternak harus ber-scope unit (hindari bocor lintas unit)
        $ternakQuery = LivestockInventory::where('status', 'hidup');
        $this->scopeByUnit($ternakQuery, $unitId);
        $summary = [
            'total_kandang' => $barns->count(),
            'kandang_aktif' => $barns->where('status', 'aktif')->count(),
            'total_ternak' => $ternakQuery->sum('jumlah'),
        ];
        
        return $this->renderOrDownload('reports.livestock', compact('barns', 'summary'), 'laporan-peternakan', 'a4', 'landscape');
    }

    /**
     * Laporan Perikanan
     */
    public function fishery(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = FishPond::with(['unit', 'fishStocks']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kolam', 'LIKE', "%{$search}%")->orWhere('kode_kolam', 'LIKE', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $ponds = $query->get();

        // Summary ikan harus ber-scope unit (hindari bocor lintas unit)
        $ikanQuery = FishStock::where('status', 'hidup');
        $this->scopeByUnit($ikanQuery, $unitId);
        $summary = [
            'total_kolam' => $ponds->count(),
            'kolam_aktif' => $ponds->where('status', 'aktif')->count(),
            'total_ikan' => $ikanQuery->sum('jumlah'),
        ];
        
        return $this->renderOrDownload('reports.fishery', compact('ponds', 'summary'), 'laporan-perikanan', 'a4', 'landscape');
    }

    /**
     * Laporan Inventaris
     */
    public function inventory(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = InventoryItem::with(['category', 'unit']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_item', 'LIKE', "%{$search}%")->orWhere('kode_item', 'LIKE', "%{$search}%");
            });
        }
        if ($request->category_id) $query->where('category_id', $request->category_id);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $items = $query->orderBy('nama_item')->get();
        
        $summary = [
            'total_item' => $items->count(),
            'tersedia' => $items->where('status', 'tersedia')->count(),
            'stok_habis' => $items->where('status', 'stok_habis')->count(),
            'stok_menipis' => $items->filter(fn($item) => $item->stok_saat_ini < $item->stok_minimum)->count(),
            'total_nilai' => $items->sum(fn($item) => $item->stok_saat_ini * $item->harga_beli_rata),
        ];
        
        return $this->renderOrDownload('reports.inventory', compact('items', 'summary'), 'laporan-inventaris', 'a4', 'landscape');
    }

    /**
     * Laporan Keuangan
     */
    public function financial(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = FinancialTransaction::with(['category', 'unit']);
        
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->category_id) $query->where('category_id', $request->category_id);
        $this->scopeByUnit($query, $unitId);
        if ($request->from_date) $query->where('tanggal_transaksi', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_transaksi', '<=', $request->to_date);
        
        $transactions = $query->orderByDesc('tanggal_transaksi')->get();
        
        $summary = [
            'total_transaksi' => $transactions->count(),
            'total_pemasukan' => $transactions->where('tipe', 'pemasukan')->sum('jumlah'),
            'total_pengeluaran' => $transactions->where('tipe', 'pengeluaran')->sum('jumlah'),
            'selisih' => $transactions->where('tipe', 'pemasukan')->sum('jumlah') - $transactions->where('tipe', 'pengeluaran')->sum('jumlah'),
        ];
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return $this->renderOrDownload('reports.financial', compact('transactions', 'summary', 'fromDate', 'toDate'), 'laporan-keuangan', 'a4', 'landscape');
    }

    /**
     * Laporan Harian
     */
    public function dailyReports(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = DailyReport::with(['unit', 'reporter']);
        
        if ($request->from_date) $query->where('tanggal_laporan', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_laporan', '<=', $request->to_date);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $reports = $query->orderByDesc('tanggal_laporan')->get();
        
        $summary = [
            'total' => $reports->count(),
            'draft' => $reports->where('status', 'draft')->count(),
            'dikirim' => $reports->where('status', 'dikirim')->count(),
            'dikonfirmasi' => $reports->where('status', 'dikonfirmasi')->count(),
        ];
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return $this->renderOrDownload('reports.daily-reports', compact('reports', 'summary', 'fromDate', 'toDate'), 'laporan-harian', 'a4', 'portrait');
    }

    /**
     * Laporan Distribusi
     */
    public function distributions(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = Distribution::with(['foodProgram', 'items.item', 'creator']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_distribusi', 'LIKE', "%{$search}%")->orWhere('penerima_nama', 'LIKE', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->tipe_distribusi) $query->where('tipe_distribusi', $request->tipe_distribusi);
        if ($request->from_date) $query->where('tanggal_distribusi', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_distribusi', '<=', $request->to_date);
        $this->scopeByUnit($query, $unitId);
        
        $distributions = $query->orderByDesc('tanggal_distribusi')->get();
        
        $summary = [
            'total' => $distributions->count(),
            'diterima' => $distributions->where('status', 'diterima')->count(),
            'proses' => $distributions->where('status', 'proses')->count(),
            'ditolak' => $distributions->where('status', 'ditolak')->count(),
            'total_nilai' => $distributions->sum('total_nilai'),
            'by_tipe' => $distributions->groupBy('tipe_distribusi')->map(fn($items) => $items->count()),
        ];
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return $this->renderOrDownload('reports.distributions', compact('distributions', 'summary', 'fromDate', 'toDate'), 'laporan-distribusi', 'a4', 'landscape');
    }

    /**
     * Laporan Nutrisi & Menu
     */
    public function nutrition(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = NutritionPlan::with(['components.item']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_rencana_gizi', 'LIKE', "%{$search}%")->orWhere('nama_menu', 'LIKE', "%{$search}%");
            });
        }
        if ($request->tipe_porsi) $query->where('tipe_porsi', $request->tipe_porsi);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $plans = $query->orderByDesc('created_at')->get();
        
        $summary = [
            'total' => $plans->count(),
            'aktif' => $plans->where('status', 'aktif')->count(),
            'nonaktif' => $plans->where('status', 'nonaktif')->count(),
            'avg_kalori' => $plans->where('status', 'aktif')->avg('kalori_target_kcal'),
            'avg_protein' => $plans->where('status', 'aktif')->avg('protein_target_gr'),
            'by_tipe' => $plans->groupBy('tipe_porsi')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.nutrition', compact('plans', 'summary'), 'laporan-nutrisi', 'a4', 'portrait');
    }

    /**
     * Laporan Pelatihan & Pembinaan
     */
    public function training(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = TrainingProgram::with(['unit', 'participants.wbp', 'certifications']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_pelatihan', 'LIKE', "%{$search}%")->orWhere('nama_pelatihan', 'LIKE', "%{$search}%");
            });
        }
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $programs = $query->orderByDesc('tanggal_mulai')->get();
        
        $summary = [
            'total' => $programs->count(),
            'aktif' => $programs->where('status', 'aktif')->count(),
            'selesai' => $programs->where('status', 'selesai')->count(),
            'total_peserta' => TrainingParticipant::count(),
            'peserta_lulus' => TrainingParticipant::where('status', 'lulus')->count(),
            'total_sertifikat' => TrainingCertification::count(),
            'by_tipe' => $programs->groupBy('tipe')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.training', compact('programs', 'summary'), 'laporan-pelatihan', 'a4', 'landscape');
    }

    /**
     * Laporan Sertifikasi & Komplians
     */
    public function compliance(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = ComplianceRecord::with(['details', 'inspektur']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_komplians', 'LIKE', "%{$search}%")->orWhere('judul', 'LIKE', "%{$search}%");
            });
        }
        if ($request->tipe) $query->where('tipe', $request->tipe);
        if ($request->status) $query->where('status', $request->status);
        $this->scopeByUnit($query, $unitId);
        
        $records = $query->orderByDesc('tanggal_pemeriksaan')->get();
        
        $summary = [
            'total' => $records->count(),
            'aktif' => $records->where('status', 'aktif')->count(),
            'kedaluwarsa' => $records->where('status', 'kedaluwarsa')->count(),
            'dibatalkan' => $records->where('status', 'dibatalkan')->count(),
            'avg_skor' => $records->whereNotNull('nilai_skor')->avg('nilai_skor'),
            'by_tipe' => $records->groupBy('tipe')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.compliance', compact('records', 'summary'), 'laporan-sertifikasi', 'a4', 'portrait');
    }

    /**
     * Laporan Analitik & Indikator
     */
    public function analytics(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = AnalyticsIndicator::with(['targets.unit']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_indikator', 'LIKE', "%{$search}%")->orWhere('nama_indikator', 'LIKE', "%{$search}%");
            });
        }
        if ($request->kategori) $query->where('kategori', $request->kategori);
        if ($request->status) $query->where('is_active', $request->status === 'aktif');
        
        $indicators = $query->orderByDesc('created_at')->get();

        // Filter targets by unit (analytics_indicators has no unit_id, targets do)
        if ($unitId) {
            foreach ($indicators as $indicator) {
                $indicator->setRelation(
                    'targets',
                    $indicator->targets->filter(fn($t) => $t->unit_id == $unitId)
                );
            }
        }
        
        $summary = [
            'total' => $indicators->count(),
            'aktif' => $indicators->where('is_active', true)->count(),
            'nonaktif' => $indicators->where('is_active', false)->count(),
            'by_kategori' => $indicators->groupBy('kategori')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.analytics', compact('indicators', 'summary'), 'laporan-analitik', 'a4', 'portrait');
    }

    /**
     * Laporan Pengolahan Hasil
     */
    public function processing(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = ProcessingBatch::with(['items.item', 'picWbp']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_batch', 'LIKE', "%{$search}%")->orWhere('nama_produk', 'LIKE', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_pengolahan) $query->where('jenis_pengolahan', $request->jenis_pengolahan);
        $this->scopeByUnit($query, $unitId);
        
        $batches = $query->orderByDesc('tanggal_produksi')->get();
        
        $summary = [
            'total' => $batches->count(),
            'proses' => $batches->where('status', 'proses')->count(),
            'selesai' => $batches->where('status', 'selesai')->count(),
            'gagal' => $batches->where('status', 'gagal')->count(),
            'total_produksi' => $batches->sum('jumlah_produksi'),
            'total_biaya' => $batches->sum('biaya_produksi'),
            'by_tipe' => $batches->groupBy('jenis_pengolahan')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.processing', compact('batches', 'summary'), 'laporan-pengolahan', 'a4', 'landscape');
    }

    /**
     * Laporan Stock Opname
     */
    public function stockOpname(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = StockOpname::with(['items.item', 'unit']);
        
        if ($search = $request->input('search')) $query->where('kode_opname', 'LIKE', "%{$search}%");
        if ($request->status) $query->where('status', $request->status);
        if ($request->from_date) $query->where('tanggal_opname', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_opname', '<=', $request->to_date);
        $this->scopeByUnit($query, $unitId);
        
        $opnames = $query->orderByDesc('tanggal_opname')->get();
        
        $summary = [
            'total' => $opnames->count(),
            'draft' => $opnames->where('status', 'draft')->count(),
            'selesai' => $opnames->where('status', 'selesai')->count(),
            'dibatalkan' => $opnames->where('status', 'dibatalkan')->count(),
            'total_selisih' => $opnames->sum('total_selisih_nilai'),
        ];
        
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        return $this->renderOrDownload('reports.stock-opname', compact('opnames', 'summary', 'fromDate', 'toDate'), 'laporan-stock-opname', 'a4', 'landscape');
    }

    /**
     * Terapkan filter rentang tanggal (from_date/to_date) ke query sub-list detail.
     */
    protected function applyDateRange($query, string $column)
    {
        $request = request();
        if ($request->from_date) $query->where($column, '>=', $request->from_date);
        if ($request->to_date) $query->where($column, '<=', $request->to_date);
        return $query;
    }

    /**
     * Laporan Detail Stock Opname
     */
    public function stockOpnameDetail($opnameId)
    {
        $opname = \App\Models\StockOpname::with(['items.item', 'unit', 'petugasWbp', 'verifikator', 'statusHistories.changedBy'])->findOrFail($opnameId);
        $unitName = $this->getUnitName($opname->unit_id);
        return $this->renderOrDownload('reports.stock-opname-detail', compact('opname', 'unitName'), 'laporan-stock-opname-' . $opname->kode_opname, 'a4', 'portrait');
    }

    // === DETAIL METHODS ===
    
    public function landPlotDetail($id)
    {
        $landPlot = \App\Models\LandPlot::with([
            'unit', 'statusHistories.changedBy',
            'farmingPlans' => fn ($q) => $this->applyDateRange($q, 'tanggal_tanam_rencana'),
            'farmingPlans.cropType',
        ])->findOrFail($id);
        $unitName = $this->getUnitName($landPlot->unit_id);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.land-plot-detail', compact('landPlot', 'unitName', 'fromDate', 'toDate'), 'laporan-lahan-' . $landPlot->kode_lahan, 'a4', 'portrait');
    }

    public function analyticsDetail($id)
    {
        $analytic = \App\Models\AnalyticsIndicator::with(['targets.unit'])->findOrFail($id);
        return $this->renderOrDownload('reports.analytics-detail', compact('analytic'), 'laporan-analitik-' . $analytic->kode_indikator, 'a4', 'portrait');
    }

    public function dailyReportDetail($id)
    {
        $report = \App\Models\DailyReport::with(['unit', 'reporter', 'confirmer', 'details', 'statusHistories.changedBy'])->findOrFail($id);
        $unitName = $this->getUnitName($report->unit_id);
        return $this->renderOrDownload('reports.daily-report-detail', compact('report', 'unitName'), 'laporan-harian-' . $report->kode_laporan, 'a4', 'portrait');
    }

    public function distributionDetail($id)
    {
        $distribution = \App\Models\Distribution::with(['foodProgram', 'items.item', 'creator', 'statusHistories.changedBy'])->findOrFail($id);
        $unitName = $this->getUnitName($distribution->unit_id ?? null);
        return $this->renderOrDownload('reports.distribution-detail', compact('distribution', 'unitName'), 'laporan-distribusi-' . $distribution->kode_distribusi, 'a4', 'portrait');
    }

    public function financialDetail($id)
    {
        $transaction = \App\Models\FinancialTransaction::with(['category', 'unit', 'statusHistories.changedBy'])->findOrFail($id);
        $unitName = $this->getUnitName($transaction->unit_id);
        return $this->renderOrDownload('reports.financial-detail', compact('transaction', 'unitName'), 'laporan-keuangan-' . ($transaction->kode_transaksi_keu ?? $transaction->id), 'a4', 'portrait');
    }

    public function harvestDetail($id)
    {
        $harvest = \App\Models\Harvest::with(['farmingPlan.cropType', 'farmingPlan.landPlot', 'details'])->findOrFail($id);
        return $this->renderOrDownload('reports.harvest-detail', compact('harvest'), 'laporan-panen-' . $harvest->kode_panen, 'a4', 'portrait');
    }

    public function inventoryDetail($id)
    {
        $item = \App\Models\InventoryItem::with(['category', 'unit', 'statusHistories.changedBy'])->findOrFail($id);
        $unitName = $this->getUnitName($item->unit_id ?? null);
        return $this->renderOrDownload('reports.inventory-detail', compact('item', 'unitName'), 'laporan-inventaris-' . $item->kode_item, 'a4', 'portrait');
    }

    public function wbpDetail($id)
    {
        $wbp = \App\Models\Wbp::with([
            'unit', 'block', 'room', 'familyMembers', 'statusHistories.changedBy',
            'documents' => fn ($q) => $this->applyDateRange($q, 'tanggal_dokumen'),
        ])->findOrFail($id);
        $unitName = $this->getUnitName($wbp->unit_id);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.wbp-detail', compact('wbp', 'unitName', 'fromDate', 'toDate'), 'laporan-wbp-' . $wbp->nomor_induk, 'a4', 'portrait');
    }

    public function foodProgramDetail($id)
    {
        $program = \App\Models\FoodProgram::with([
            'unit', 'creator', 'statusHistories.changedBy',
            'farmingPlans' => fn ($q) => $this->applyDateRange($q, 'tanggal_tanam_rencana'),
            'farmingPlans.cropType', 'farmingPlans.landPlot', 'farmingPlans.participants.wbp',
            'livestockBarns' => fn ($q) => $this->applyDateRange($q, 'created_at'),
            'livestockBarns.participants.wbp',
            'fishPonds' => fn ($q) => $this->applyDateRange($q, 'created_at'),
            'fishPonds.participants.wbp',
        ])->findOrFail($id);
        $unitName = $this->getUnitName($program->unit_id);
        $totalPeserta = $program->total_participants;
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.food-program-detail', compact('program', 'unitName', 'totalPeserta', 'fromDate', 'toDate'), 'laporan-program-' . $program->kode_program, 'a4', 'portrait');
    }

    /**
     * Laporan Manajemen Lahan
     */
    public function landPlots(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = LandPlot::with(['unit', 'farmingPlans']);
        
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_lahan', 'LIKE', "%{$search}%")->orWhere('nama_lahan', 'LIKE', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->jenis_tanah) $query->where('jenis_tanah', $request->jenis_tanah);
        $this->scopeByUnit($query, $unitId);
        
        $landPlots = $query->orderBy('nama_lahan')->get();
        
        $summary = [
            'total' => $landPlots->count(),
            'tersedia' => $landPlots->where('status', 'tersedia')->count(),
            'ditanami' => $landPlots->where('status', 'ditanami')->count(),
            'istirahat' => $landPlots->where('status', 'istirahat')->count(),
            'total_luas' => $landPlots->sum('luas_m2'),
            'by_tipe' => $landPlots->groupBy('jenis_tanah')->map(fn($items) => $items->count()),
        ];
        
        return $this->renderOrDownload('reports.land-plots', compact('landPlots', 'summary'), 'laporan-lahan', 'a4', 'landscape');
    }

    /**
     * Laporan Blok & Kamar
     */
    public function blocks(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $query = Block::with(['unit', 'rooms']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_blok', 'LIKE', "%{$search}%")->orWhere('nama_blok', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('unit_id')) $query->where('unit_id', $request->unit_id);
        $this->scopeByUnit($query, $unitId);

        $blocks = $query->orderBy('kode_blok')->get();
        $totalKamar = $blocks->sum(fn ($b) => $b->rooms->count());

        $summary = [
            'total_blok' => $blocks->count(),
            'total_kamar' => $totalKamar,
            'kapasitas' => $blocks->sum('kapasitas'),
            'terisi' => $blocks->sum('terisi'),
        ];

        return $this->renderOrDownload('reports.blocks', compact('blocks', 'summary'), 'laporan-blok-kamar', 'a4', 'landscape');
    }

    /**
     * Laporan Data Master (Unit)
     */
    public function masterUnits(Request $request)
    {
        $query = Unit::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_unit', 'LIKE', "%{$search}%")->orWhere('kode_unit', 'LIKE', "%{$search}%");
            });
        }
        $units = $query->orderBy('kode_unit')->get();
        return $this->renderOrDownload('reports.master-units', compact('units'), 'laporan-master-unit', 'a4', 'landscape');
    }

    /**
     * Laporan Data Master (Jenis Tanaman)
     */
    public function masterCropTypes(Request $request)
    {
        $query = CropType::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_tanaman', 'LIKE', "%{$search}%")->orWhere('kode_tanaman', 'LIKE', "%{$search}%");
            });
        }
        $types = $query->orderBy('kode_tanaman')->get();
        return $this->renderOrDownload('reports.master-crop-types', compact('types'), 'laporan-master-jenis-tanaman', 'a4', 'landscape');
    }

    /**
     * Laporan Data Master (Kategori Keuangan)
     */
    public function masterFinancialCategories(Request $request)
    {
        $query = FinancialCategory::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'LIKE', "%{$search}%")->orWhere('kode_kategori_keu', 'LIKE', "%{$search}%");
            });
        }
        $categories = $query->orderBy('kode_kategori_keu')->get();
        return $this->renderOrDownload('reports.master-financial-categories', compact('categories'), 'laporan-master-kategori-keuangan', 'a4', 'landscape');
    }

    /**
     * Laporan Data Master (Kategori Inventaris)
     */
    public function masterInventoryCategories(Request $request)
    {
        $query = InventoryCategory::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'LIKE', "%{$search}%")->orWhere('kode_kategori', 'LIKE', "%{$search}%");
            });
        }
        $categories = $query->orderBy('kode_kategori')->get();
        return $this->renderOrDownload('reports.master-inventory-categories', compact('categories'), 'laporan-master-kategori-inventaris', 'a4', 'landscape');
    }

    /**
     * Laporan Data Master (Jenis Ternak)
     */
    public function masterLivestockTypes(Request $request)
    {
        $query = LivestockType::query();
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_jenis', 'LIKE', "%{$search}%")->orWhere('kode_jenis', 'LIKE', "%{$search}%");
            });
        }
        $types = $query->orderBy('kode_jenis')->get();
        return $this->renderOrDownload('reports.master-livestock-types', compact('types'), 'laporan-master-jenis-ternak', 'a4', 'landscape');
    }

    /**
     * Laporan Gabungan / Ringkasan
     */
    public function summary(Request $request)
    {
        $unitId = $this->getUnitId($request);
        $unitName = $this->getUnitName($unitId);

        $summary = [
            'wbp' => [
                'total' => $this->scopeByUnit(Wbp::query(), $unitId)->count(),
                'aktif' => $this->scopeByUnit(Wbp::where('status', 'aktif'), $unitId)->count(),
            ],
            'programs' => [
                'total' => $this->scopeByUnit(FoodProgram::query(), $unitId)->count(),
                'aktif' => $this->scopeByUnit(FoodProgram::where('status', 'aktif'), $unitId)->count(),
            ],
            'farming' => [
                'total' => $this->scopeByUnit(FarmingPlan::query(), $unitId)->count(),
                'aktif' => $this->scopeByUnit(FarmingPlan::whereIn('status', ['proses_tanam', 'tumbuh']), $unitId)->count(),
            ],
            'livestock' => [
                'total_kandang' => $this->scopeByUnit(LivestockBarn::query(), $unitId)->count(),
                'total_ternak' => $this->scopeByUnit(LivestockInventory::where('status', 'hidup'), $unitId)->sum('jumlah'),
            ],
            'fishery' => [
                'total_kolam' => $this->scopeByUnit(FishPond::query(), $unitId)->count(),
                'total_ikan' => $this->scopeByUnit(FishStock::where('status', 'hidup'), $unitId)->sum('jumlah'),
            ],
            'inventory' => [
                'total_item' => $this->scopeByUnit(InventoryItem::query(), $unitId)->count(),
                'stok_menipis' => $this->scopeByUnit(InventoryItem::whereColumn('stok_saat_ini', '<', 'stok_minimum'), $unitId)->count(),
            ],
            'financial' => [
                'pemasukan_bulan' => $this->scopeByUnit(
                    FinancialTransaction::where('tipe', 'pemasukan')
                        ->whereMonth('tanggal_transaksi', now()->month)
                        ->whereYear('tanggal_transaksi', now()->year),
                    $unitId
                )->sum('jumlah'),
                'pengeluaran_bulan' => $this->scopeByUnit(
                    FinancialTransaction::where('tipe', 'pengeluaran')
                        ->whereMonth('tanggal_transaksi', now()->month)
                        ->whereYear('tanggal_transaksi', now()->year),
                    $unitId
                )->sum('jumlah'),
            ],
            'reports' => [
                'total' => $this->scopeByUnit(DailyReport::query(), $unitId)->count(),
                'pending' => $this->scopeByUnit(DailyReport::where('status', 'draft'), $unitId)->count(),
            ],
        ];
        
        $data = compact('summary');
        if ($unitName) $data['unitName'] = $unitName;
        
        return $this->renderOrDownload('reports.summary', $data, 'laporan-ringkasan', 'a4', 'portrait');
    }

    // === DETAIL PDF EXPORTS ===

    public function nutritionDetail(NutritionPlan $plan)
    {
        $plan->load(['unit', 'components.item', 'statusHistories.changedBy']);
        return $this->renderOrDownload('reports.detail.nutrition', compact('plan'), 'detail-gizi-' . $plan->kode_rencana_gizi, 'a4', 'portrait');
    }

    public function farmingPlanDetail(FarmingPlan $farmingPlan)
    {
        $farmingPlan->load([
            'landPlot', 'cropType', 'foodProgram', 'statusHistories.changedBy',
            'activities' => fn ($q) => $this->applyDateRange($q, 'tanggal_kegiatan'),
            'activities.picWbp',
            'harvests' => fn ($q) => $this->applyDateRange($q, 'tanggal_panen'),
        ]);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.detail.farming-plan', compact('farmingPlan', 'fromDate', 'toDate'), 'detail-rencana-' . $farmingPlan->kode_rencana, 'a4', 'portrait');
    }

    public function livestockBarnDetail(LivestockBarn $livestockBarn)
    {
        $livestockBarn->load([
            'unit', 'statusHistories.changedBy',
            'inventories' => fn ($q) => $this->applyDateRange($q, 'tanggal_masuk'),
            'inventories.livestockType',
            'healthRecords' => fn ($q) => $this->applyDateRange($q, 'tanggal_pemeriksaan'),
            'healthRecords.picWbp',
        ]);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.detail.livestock-barn', compact('livestockBarn', 'fromDate', 'toDate'), 'detail-kandang-' . $livestockBarn->kode_kandang, 'a4', 'portrait');
    }

    public function fishPondDetail(FishPond $fishPond)
    {
        $fishPond->load([
            'unit', 'statusHistories.changedBy',
            'fishStocks' => fn ($q) => $this->applyDateRange($q, 'tanggal_tebur'),
            'waterQualityRecords' => fn ($q) => $this->applyDateRange($q, 'tanggal_pemeriksaan'),
        ]);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.detail.fish-pond', compact('fishPond', 'fromDate', 'toDate'), 'detail-kolam-' . $fishPond->kode_kolam, 'a4', 'portrait');
    }

    public function processingDetail(ProcessingBatch $batch)
    {
        $batch->load(['unit', 'picWbp', 'statusHistories.changedBy']);
        return $this->renderOrDownload('reports.detail.processing', compact('batch'), 'detail-batch-' . $batch->kode_batch, 'a4', 'portrait');
    }

    public function trainingDetail(TrainingProgram $program)
    {
        $program->load([
            'unit', 'statusHistories.changedBy',
            'participants' => fn ($q) => $this->applyDateRange($q, 'tanggal_daftar'),
            'participants.wbp',
        ]);
        $fromDate = request('from_date');
        $toDate = request('to_date');
        return $this->renderOrDownload('reports.detail.training', compact('program', 'fromDate', 'toDate'), 'detail-pelatihan-' . $program->kode_pelatihan, 'a4', 'portrait');
    }

    public function roomDetail(Room $room)
    {
        $room->load(['block', 'statusHistories.changedBy']);
        $unitName = $this->getUnitName($room->block?->unit_id);
        return $this->renderOrDownload('reports.room-detail', compact('room', 'unitName'), 'laporan-kamar-' . $room->kode_kamar, 'a4', 'portrait');
    }

    public function complianceDetail(ComplianceRecord $record)
    {
        $record->load(['details', 'unit', 'inspektur', 'statusHistories.changedBy']);
        return $this->renderOrDownload('reports.detail.compliance', compact('record'), 'detail-komplians-' . $record->kode_komplians, 'a4', 'portrait');
    }

    // === PUSAT LAPORAN (index + preview dengan filter) ===

    /**
     * Halaman pusat laporan: daftar semua laporan.
     */
    public function index()
    {
        $reports = $this->reportDefinitions();
        return view('reports.index', compact('reports'));
    }

    /**
     * Halaman preview laporan: filter bar + iframe berisi laporan yang difilter.
     */
    public function preview($key)
    {
        $definitions = $this->reportDefinitions();

        if (! isset($definitions[$key])) {
            abort(404);
        }

        $def = $definitions[$key];
        $filters = $this->buildReportFilters($def['filters'] ?? []);

        $query = request()->query();
        $iframeSrc = route($def['route'], array_merge($query, ['preview' => 1]));

        return view('reports.preview', compact('def', 'filters', 'iframeSrc', 'key'));
    }

    /**
     * Definisi semua laporan: judul, deskripsi, ikon, warna, route & konfigurasi filter.
     */
    protected function reportDefinitions(): array
    {
        return [
            'summary' => [
                'title' => 'Ringkasan Keseluruhan',
                'desc' => 'Rekap seluruh sektor: WBP, program, pertanian, peternakan, perikanan, inventaris & keuangan.',
                'icon' => 'fa-tachometer-alt',
                'color' => '#10b981',
                'route' => 'reports.summary',
                'show_date' => false,
                'filters' => [],
            ],
            'financial' => [
                'title' => 'Laporan Keuangan',
                'desc' => 'Rekap pemasukan & pengeluaran lengkap dengan ringkasan total dan selisih.',
                'icon' => 'fa-money-bill-wave',
                'color' => '#059669',
                'route' => 'reports.financial',
                'show_date' => true,
                'filters' => [
                    ['name' => 'tipe', 'label' => 'Tipe Transaksi', 'type' => 'select', 'options' => ['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran']],
                    ['name' => 'category_id', 'label' => 'Kategori', 'type' => 'select', 'options' => 'financial_categories'],
                    ['name' => 'unit_id', 'label' => 'Unit / Lapas', 'type' => 'select', 'options' => 'units'],
                ],
            ],
            'wbp' => [
                'title' => 'Data WBP',
                'desc' => 'Data warga binaan pemasyarakatan per unit dan status.',
                'icon' => 'fa-users',
                'color' => '#3b82f6',
                'route' => 'reports.wbp',
                'show_date' => false,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'dikeluarkan' => 'Dikeluarkan', 'pindah' => 'Pindah', 'bebas' => 'Bebas', 'meninggal' => 'Meninggal']],
                    ['name' => 'unit_id', 'label' => 'Unit / Lapas', 'type' => 'select', 'options' => 'units'],
                ],
            ],
            'food-programs' => [
                'title' => 'Program Pangan',
                'desc' => 'Program ketahanan pangan per jenis program dan status.',
                'icon' => 'fa-seedling',
                'color' => '#10b981',
                'route' => 'reports.food-programs',
                'show_date' => false,
                'filters' => [
                    ['name' => 'jenis_program', 'label' => 'Jenis Program', 'type' => 'select', 'options' => ['pertanian' => 'Pertanian', 'peternakan' => 'Peternakan', 'perikanan' => 'Perikanan', 'integrated' => 'Integrated', 'pengolahan' => 'Pengolahan']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['perencanaan' => 'Perencanaan', 'aktif' => 'Aktif', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']],
                ],
            ],
            'farming' => [
                'title' => 'Pertanian / Rencana Kerja',
                'desc' => 'Rencana tanam, tanaman, lahan dan status budi daya.',
                'icon' => 'fa-tractor',
                'color' => '#f59e0b',
                'route' => 'reports.farming',
                'show_date' => false,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['direncanakan' => 'Direncanakan', 'proses_tanam' => 'Proses Tanam', 'tumbuh' => 'Tumbuh', 'siap_panen' => 'Siap Panen', 'panen' => 'Panen', 'selesai' => 'Selesai']],
                    ['name' => 'program_id', 'label' => 'Program', 'type' => 'select', 'options' => 'food_programs'],
                ],
            ],
            'harvest' => [
                'title' => 'Hasil Panen',
                'desc' => 'Rekap hasil panen dengan total berat dan pendapatan.',
                'icon' => 'fa-apple-alt',
                'color' => '#f97316',
                'route' => 'reports.harvest',
                'show_date' => true,
                'filters' => [
                    ['name' => 'kualitas', 'label' => 'Kualitas', 'type' => 'select', 'options' => ['A' => 'Kualitas A', 'B' => 'Kualitas B', 'C' => 'Kualitas C', 'D' => 'Kualitas D']],
                ],
            ],
            'livestock' => [
                'title' => 'Peternakan',
                'desc' => 'Data kandang dan populasi ternak per status.',
                'icon' => 'fa-paw',
                'color' => '#d97706',
                'route' => 'reports.livestock',
                'show_date' => false,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status Kandang', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'renovasi' => 'Renovasi', 'nonaktif' => 'Nonaktif']],
                ],
            ],
            'fishery' => [
                'title' => 'Perikanan',
                'desc' => 'Data kolam dan populasi ikan per status.',
                'icon' => 'fa-fish',
                'color' => '#2563eb',
                'route' => 'reports.fishery',
                'show_date' => false,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status Kolam', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'kering' => 'Kering', 'renovasi' => 'Renovasi', 'nonaktif' => 'Nonaktif']],
                ],
            ],
            'inventory' => [
                'title' => 'Inventaris & Stok',
                'desc' => 'Rekap stok barang per kategori dan status.',
                'icon' => 'fa-boxes',
                'color' => '#8b5cf6',
                'route' => 'reports.inventory',
                'show_date' => false,
                'filters' => [
                    ['name' => 'category_id', 'label' => 'Kategori', 'type' => 'select', 'options' => 'inventory_categories'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['tersedia' => 'Tersedia', 'stok_habis' => 'Stok Habis', 'nonaktif' => 'Nonaktif']],
                ],
            ],
            'daily-reports' => [
                'title' => 'Laporan Harian',
                'desc' => 'Rekap laporan harian per unit dan status.',
                'icon' => 'fa-clipboard-list',
                'color' => '#06b6d4',
                'route' => 'reports.daily-reports',
                'show_date' => true,
                'filters' => [
                    ['name' => 'unit_id', 'label' => 'Unit / Lapas', 'type' => 'select', 'options' => 'units'],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'dikirim' => 'Dikirim', 'dikonfirmasi' => 'Dikonfirmasi', 'dikembalikan' => 'Dikembalikan']],
                ],
            ],
            'distributions' => [
                'title' => 'Distribusi',
                'desc' => 'Rekap distribusi hasil per tipe dan status.',
                'icon' => 'fa-truck',
                'color' => '#ec4899',
                'route' => 'reports.distributions',
                'show_date' => true,
                'filters' => [
                    ['name' => 'tipe_distribusi', 'label' => 'Tipe Distribusi', 'type' => 'select', 'options' => ['konsumsi_wbp' => 'Konsumsi WBP', 'penjualan_lokal' => 'Penjualan Lokal', 'penjualan_eksternal' => 'Penjualan Eksternal', 'donasi' => 'Donasi', 'konversi_produksi' => 'Konversi Produksi']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'dikirim' => 'Dikirim', 'diterima' => 'Diterima', 'dibatalkan' => 'Dibatalkan']],
                ],
            ],
            'nutrition' => [
                'title' => 'Nutrisi & Menu',
                'desc' => 'Rekap menu nutrisi per tipe porsi dan status.',
                'icon' => 'fa-utensils',
                'color' => '#84cc16',
                'route' => 'reports.nutrition',
                'show_date' => false,
                'filters' => [
                    ['name' => 'tipe_porsi', 'label' => 'Tipe Porsi', 'type' => 'select', 'options' => ['standar' => 'Standar', 'diet_khusus' => 'Diet Khusus', 'vegan' => 'Vegan', 'bayi' => 'Bayi', 'lansia' => 'Lansia']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']],
                ],
            ],
            'training' => [
                'title' => 'Pelatihan & Keterampilan',
                'desc' => 'Rekap program pelatihan, peserta dan sertifikat.',
                'icon' => 'fa-graduation-cap',
                'color' => '#6366f1',
                'route' => 'reports.training',
                'show_date' => false,
                'filters' => [
                    ['name' => 'tipe', 'label' => 'Tipe Pelatihan', 'type' => 'select', 'options' => ['offline' => 'Offline', 'online' => 'Online', 'hybrid' => 'Hybrid']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['perencanaan' => 'Perencanaan', 'terbuka' => 'Terbuka', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']],
                ],
            ],
            'compliance' => [
                'title' => 'Sertifikasi & Komplians',
                'desc' => 'Rekap sertifikasi produk, food safety dan inspeksi.',
                'icon' => 'fa-certificate',
                'color' => '#14b8a6',
                'route' => 'reports.compliance',
                'show_date' => false,
                'filters' => [
                    ['name' => 'tipe', 'label' => 'Tipe Pemeriksaan', 'type' => 'select', 'options' => ['food_safety' => 'Food Safety', 'sertifikasi_produk' => 'Sertifikasi Produk', 'standar_operasional' => 'Standar Operasional', 'lingkungan' => 'Lingkungan', 'keamanan_kerja' => 'Keamanan Kerja']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'aktif' => 'Aktif', 'perlu_perbaikan' => 'Perlu Perbaikan', 'kedaluwarsa' => 'Kedaluwarsa', 'dibatalkan' => 'Dibatalkan']],
                ],
            ],
            'analytics' => [
                'title' => 'Analitik & Indikator',
                'desc' => 'Rekap indikator kinerja dan capaian target.',
                'icon' => 'fa-chart-line',
                'color' => '#7c3aed',
                'route' => 'reports.analytics',
                'show_date' => false,
                'filters' => [
                    ['name' => 'kategori', 'label' => 'Kategori', 'type' => 'select', 'options' => ['pangan' => 'Pangan', 'ekonomi' => 'Ekonomi', 'kesehatan' => 'Kesehatan', 'sosial' => 'Sosial', 'lingkungan' => 'Lingkungan', 'edukasi' => 'Edukasi']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']],
                ],
            ],
            'processing' => [
                'title' => 'Pengolahan Hasil',
                'desc' => 'Rekap batch pengolahan hasil per jenis dan status.',
                'icon' => 'fa-industry',
                'color' => '#db2777',
                'route' => 'reports.processing',
                'show_date' => false,
                'filters' => [
                    ['name' => 'jenis_pengolahan', 'label' => 'Jenis Pengolahan', 'type' => 'select', 'options' => ['pengeringan' => 'Pengeringan', 'pengalengan' => 'Pengalengan', 'pembekuan' => 'Pembekuan', 'fermentasi' => 'Fermentasi', 'penggilingan' => 'Penggilingan', 'pengepakan' => 'Pengepakan', 'pengolahan_umum' => 'Pengolahan Umum']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'proses' => 'Proses', 'selesai' => 'Selesai', 'gagal' => 'Gagal']],
                ],
            ],
            'stock-opname' => [
                'title' => 'Stock Opname',
                'desc' => 'Rekap stock opname per status dan periode.',
                'icon' => 'fa-clipboard-check',
                'color' => '#0ea5e9',
                'route' => 'reports.stock-opname',
                'show_date' => true,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'proses' => 'Proses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']],
                ],
            ],
            'land-plots' => [
                'title' => 'Manajemen Lahan',
                'desc' => 'Rekap lahan per status dan jenis tanah.',
                'icon' => 'fa-map',
                'color' => '#4ade80',
                'route' => 'reports.land-plots',
                'show_date' => false,
                'filters' => [
                    ['name' => 'status', 'label' => 'Status Lahan', 'type' => 'select', 'options' => ['tersedia' => 'Tersedia', 'ditanami' => 'Ditanami', 'istirahat' => 'Istirahat', 'renovasi' => 'Renovasi', 'tidak_digunakan' => 'Tidak Digunakan']],
                    ['name' => 'jenis_tanah', 'label' => 'Jenis Tanah', 'type' => 'select', 'options' => ['tanah_liat' => 'Tanah Liat', 'tanah_berpasir' => 'Tanah Berpasir', 'tanah_laterit' => 'Tanah Laterit', 'tanah_gambut' => 'Tanah Gambut', 'tanah_volkanik' => 'Tanah Volkanik', 'campuran' => 'Campuran']],
                ],
            ],
            'blocks' => [
                'title' => 'Blok & Kamar',
                'desc' => 'Rekap blok dan kamar beserta kapasitas dan keterisiannya.',
                'icon' => 'fa-th-large',
                'color' => '#10b981',
                'route' => 'reports.blocks',
                'show_date' => false,
                'filters' => [
                    ['name' => 'unit_id', 'label' => 'Unit / Lapas', 'type' => 'select', 'options' => 'units'],
                ],
            ],
            'master-units' => [
                'title' => 'Master Unit',
                'desc' => 'Daftar unit / lapas beserta tipe, alamat, dan kapasitas.',
                'icon' => 'fa-building',
                'color' => '#10b981',
                'route' => 'reports.master-units',
                'show_date' => false,
                'filters' => [],
            ],
            'master-crop-types' => [
                'title' => 'Master Jenis Tanaman',
                'desc' => 'Daftar jenis tanaman beserta masa tanam dan harga jual.',
                'icon' => 'fa-seedling',
                'color' => '#22c55e',
                'route' => 'reports.master-crop-types',
                'show_date' => false,
                'filters' => [],
            ],
            'master-financial-categories' => [
                'title' => 'Master Kategori Keuangan',
                'desc' => 'Daftar kategori pemasukan & pengeluaran.',
                'icon' => 'fa-wallet',
                'color' => '#059669',
                'route' => 'reports.master-financial-categories',
                'show_date' => false,
                'filters' => [],
            ],
            'master-inventory-categories' => [
                'title' => 'Master Kategori Inventaris',
                'desc' => 'Daftar kategori inventaris beserta satuan standar.',
                'icon' => 'fa-tags',
                'color' => '#0ea5e9',
                'route' => 'reports.master-inventory-categories',
                'show_date' => false,
                'filters' => [],
            ],
            'master-livestock-types' => [
                'title' => 'Master Jenis Ternak',
                'desc' => 'Daftar jenis ternak beserta harga dan masa pemeliharaan.',
                'icon' => 'fa-paw',
                'color' => '#8b5cf6',
                'route' => 'reports.master-livestock-types',
                'show_date' => false,
                'filters' => [],
            ],
        ];
    }

    /**
     * Siapkan filter: ubah sumber opsi DB menjadi daftar nilai.
     */
    protected function buildReportFilters(array $filters): array
    {
        foreach ($filters as &$filter) {
            if (is_string($filter['options'] ?? null)) {
                $filter['options'] = $this->filterOptions($filter['options']);
            }
        }

        return $filters;
    }

    protected function filterOptions(string $source): array
    {
        return match ($source) {
            // Non-super-admin hanya boleh memfilter unit sendiri (aman & tidak menyesatkan)
            'units' => $this->allowedUnitsQuery()->pluck('nama_unit', 'id')->toArray(),
            'financial_categories' => FinancialCategory::orderBy('nama_kategori')->pluck('nama_kategori', 'id')->toArray(),
            'inventory_categories' => InventoryCategory::orderBy('nama_kategori')->pluck('nama_kategori', 'id')->toArray(),
            'food_programs' => FoodProgram::orderBy('nama_program')->pluck('nama_program', 'id')->toArray(),
            'crop_types' => CropType::orderBy('nama_tanaman')->pluck('nama_tanaman', 'id')->toArray(),
            'livestock_types' => LivestockType::orderBy('nama_jenis')->pluck('nama_jenis', 'id')->toArray(),
            default => [],
        };
    }
}
