<?php

namespace App\Http\Controllers;

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
use App\Models\LandPlot;
use App\Models\DailyReport;
use App\Models\Distribution;
use App\Models\NutritionPlan;
use App\Models\ProcessingBatch;
use App\Models\TrainingProgram;
use App\Models\TrainingParticipant;
use App\Models\ComplianceRecord;
use App\Models\AnalyticsIndicator;
use App\Models\StockOpname;
use App\Traits\UnitScope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use UnitScope;

    /**
     * Get unit_id for the current user (null for super-admin = no filter).
     */
    private function getUnitId(\Illuminate\Http\Request $request): ?int
    {
        $user = auth()->user();
        if ($user && $user->hasRole('super-admin')) {
            // Super-admin: check if unit filter is set
            if ($request->filled('unit_id')) {
                return (int) $request->unit_id;
            }
            return null; // no filter = all units
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
     * Apply unit scope to a query if unit_id is set.
     * Handles models with or without direct unit_id column.
     */
    private function scopeQuery($query, ?int $unitId)
    {
        if (!$unitId) return $query;

        $table = $query->getModel()->getTable();

        if ($this->tableHasUnitId($table)) {
            return $query->where("{$table}.unit_id", $unitId);
        }

        return $query;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $unitId = $this->getUnitId($request);

        // Load units for super-admin filter
        $units = null;
        if (auth()->user()->hasRole('super-admin')) {
            $units = \App\Models\Unit::orderBy('nama_unit')->get();
        }

        // === DATE RANGE ===
        $fromDate = $request->input('from_date', now()->subMonths(11)->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', now()->format('Y-m-d'));
        $from = \Carbon\Carbon::parse($fromDate);
        $to = \Carbon\Carbon::parse($toDate);

        // === STAT CARDS ===
        $wbpAktif = $this->scopeQuery(Wbp::where('status', 'aktif'), $unitId)->count();
        $programAktif = $this->scopeQuery(FoodProgram::where('status', 'aktif'), $unitId)->count();
        
        $panenBulanIni = $this->scopeQuery(Harvest::whereBetween('tanggal_panen', [$from, $to]), $unitId)->sum('total_berat_kg');
        
        $pendapatanBulan = $this->scopeQuery(
            FinancialTransaction::where('tipe', 'pemasukan')->whereBetween('tanggal_transaksi', [$from, $to]),
            $unitId
        )->sum('jumlah');

        // === FLOW CARDS ===
        $lahanTersedia = $this->scopeQuery(LandPlot::where('status', 'tersedia'), $unitId)->count();
        $aktifTanam = $this->scopeQuery(FarmingPlan::whereIn('status', ['proses_tanam', 'tumbuh']), $unitId)->count();
        $totalIkan = $this->scopeQuery(FishStock::where('status', 'hidup'), $unitId)->sum('jumlah');
        $totalTernak = $this->scopeQuery(LivestockInventory::where('status', 'hidup'), $unitId)->sum('jumlah');

        // === CHART DATA: Panen by Date Range ===
        $harvestMonths = [];
        $harvestData = [];
        $months = $from->diffInMonths($to);
        if ($months < 1) $months = 1;
        if ($months > 24) $months = 24;
        $chartFrom = $to->copy()->subMonths($months)->startOfMonth();
        $harvestByMonth = $this->scopeQuery(
            Harvest::select(DB::raw("DATE_FORMAT(tanggal_panen, '%Y-%m') as bulan"), DB::raw('SUM(total_berat_kg) as total'))
                ->whereBetween('tanggal_panen', [$chartFrom, $to])
                ->groupBy('bulan'),
            $unitId
        )->pluck('total', 'bulan');
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $harvestMonths[] = $date->format('M Y');
            $harvestData[] = (float) ($harvestByMonth[$date->format('Y-m')] ?? 0);
        }

        // === CHART DATA: Distribusi Program ===
        $programDistribution = $this->scopeQuery(
            FoodProgram::select('jenis_program', DB::raw('count(*) as total'))->groupBy('jenis_program'),
            $unitId
        )->pluck('total', 'jenis_program');

        // === INVENTORY STATS ===
        $totalItems = $this->scopeQuery(InventoryItem::query(), $unitId)->count();
        $stokMenipis = $this->scopeQuery(InventoryItem::whereColumn('stok_saat_ini', '<', 'stok_minimum'), $unitId)->count();

        // === FINANCE STATS (within date range) ===
        // Catatan: pemasukan bulan ini sudah dihitung sebagai $pendapatanBulan di atas (query identik),
        // jadi cukup alias di sini untuk menghindari query duplikat.
        $pemasukanBulan = $pendapatanBulan;

        $pengeluaranBulan = $this->scopeQuery(
            FinancialTransaction::where('tipe', 'pengeluaran')->whereBetween('tanggal_transaksi', [$from, $to]),
            $unitId
        )->sum('jumlah');

        // === MONTH-OVER-MONTH COMPARISONS ===
        $prevFrom = $from->copy()->subMonth();
        $prevTo = $to->copy()->subMonth();

        $panenBulanLalu = $this->scopeQuery(Harvest::whereBetween('tanggal_panen', [$prevFrom, $prevTo]), $unitId)->sum('total_berat_kg');
        $panenChange = $panenBulanLalu > 0 ? round(($panenBulanIni - $panenBulanLalu) / $panenBulanLalu * 100) : ($panenBulanIni > 0 ? 100 : 0);

        $pendapatanBulanLalu = $this->scopeQuery(
            FinancialTransaction::where('tipe', 'pemasukan')->whereBetween('tanggal_transaksi', [$prevFrom, $prevTo]),
            $unitId
        )->sum('jumlah');
        $pendapatanChange = $pendapatanBulanLalu > 0 ? round(($pendapatanBulan - $pendapatanBulanLalu) / $pendapatanBulanLalu * 100) : ($pendapatanBulan > 0 ? 100 : 0);

        $pemasukanBulanLalu = $pendapatanBulanLalu;
        $pemasukanChange = $pendapatanChange;

        $pengeluaranBulanLalu = $this->scopeQuery(
            FinancialTransaction::where('tipe', 'pengeluaran')->whereBetween('tanggal_transaksi', [$prevFrom, $prevTo]),
            $unitId
        )->sum('jumlah');
        $pengeluaranChange = $pengeluaranBulanLalu > 0 ? round(($pengeluaranBulan - $pengeluaranBulanLalu) / $pengeluaranBulanLalu * 100) : ($pengeluaranBulan > 0 ? 100 : 0);

        // === LIVESTOCK SUMMARY BY TYPE ===
        $livestockQuery = LivestockInventory::join('livestock_types', 'livestock_inventory.livestock_type_id', '=', 'livestock_types.id')
            ->select('livestock_types.nama_jenis', DB::raw('sum(livestock_inventory.jumlah) as total'))
            ->where('livestock_inventory.status', 'hidup')
            ->groupBy('livestock_types.nama_jenis');
        $livestockByType = $this->scopeQuery($livestockQuery, $unitId)->pluck('total', 'livestock_types.nama_jenis');

        // === FARMING STATUS ===
        $farmingStatus = $this->scopeQuery(
            FarmingPlan::select('status', DB::raw('count(*) as total'))->groupBy('status'),
            $unitId
        )->pluck('total', 'status');

        // === DAILY REPORTS ===
        $laporanHariIni = $this->scopeQuery(DailyReport::whereDate('tanggal_laporan', now()), $unitId)->count();
        $laporanMenunggu = $this->scopeQuery(DailyReport::where('status', 'draft'), $unitId)->count();

        $laporanBulanLalu = $this->scopeQuery(DailyReport::whereBetween('tanggal_laporan', [$prevFrom, $prevTo]), $unitId)->count();
        $laporanChange = $laporanBulanLalu > 0 ? round(($laporanHariIni - $laporanBulanLalu) / $laporanBulanLalu * 100) : ($laporanHariIni > 0 ? 100 : 0);

        // === DISTRIBUTION STATS ===
        $totalDistribusi = $this->scopeQuery(Distribution::query(), $unitId)->count();
        $distribusiBulan = $this->scopeQuery(Distribution::whereBetween('tanggal_distribusi', [$from, $to]), $unitId)->count();
        $distribusiByTipe = $this->scopeQuery(
            Distribution::select('tipe_distribusi', DB::raw('count(*) as total'))->whereBetween('tanggal_distribusi', [$from, $to])->groupBy('tipe_distribusi'),
            $unitId
        )->pluck('total', 'tipe_distribusi');

        $distribusiBulanLalu = $this->scopeQuery(Distribution::whereBetween('tanggal_distribusi', [$prevFrom, $prevTo]), $unitId)->count();
        $distribusiChange = $distribusiBulanLalu > 0 ? round(($distribusiBulan - $distribusiBulanLalu) / $distribusiBulanLalu * 100) : ($distribusiBulan > 0 ? 100 : 0);

        // === NUTRITION STATS ===
        $totalMenu = $this->scopeQuery(NutritionPlan::where('status', 'aktif'), $unitId)->count();
        $menuByTipe = $this->scopeQuery(
            NutritionPlan::select('tipe_porsi', DB::raw('count(*) as total'))->where('status', 'aktif')->groupBy('tipe_porsi'),
            $unitId
        )->pluck('total', 'tipe_porsi');
        $avgKalori = $this->scopeQuery(NutritionPlan::where('status', 'aktif'), $unitId)->avg('kalori_target_kcal');

        // === PROCESSING STATS ===
        $totalBatch = $this->scopeQuery(ProcessingBatch::query(), $unitId)->count();
        $batchAktif = $this->scopeQuery(ProcessingBatch::where('status', 'proses'), $unitId)->count();
        $batchSelesai = $this->scopeQuery(ProcessingBatch::where('status', 'selesai'), $unitId)->count();
        $batchByTipe = $this->scopeQuery(
            ProcessingBatch::select('jenis_pengolahan', DB::raw('count(*) as total'))->groupBy('jenis_pengolahan'),
            $unitId
        )->pluck('total', 'jenis_pengolahan');

        // === TRAINING STATS ===
        $totalProgramPelatihan = $this->scopeQuery(TrainingProgram::query(), $unitId)->count();
        $programPelatihanAktif = $this->scopeQuery(TrainingProgram::where('status', 'aktif'), $unitId)->count();
        $totalPeserta = $this->scopeQuery(TrainingParticipant::query(), $unitId)->count();
        $pesertaLulus = $this->scopeQuery(TrainingParticipant::where('status', 'lulus'), $unitId)->count();
        $pelatihanByTipe = $this->scopeQuery(
            TrainingProgram::select('tipe', DB::raw('count(*) as total'))->groupBy('tipe'),
            $unitId
        )->pluck('total', 'tipe');

        // === COMPLIANCE STATS ===
        $totalSertifikasi = $this->scopeQuery(ComplianceRecord::query(), $unitId)->count();
        $sertifikasiAktif = $this->scopeQuery(ComplianceRecord::where('status', 'aktif'), $unitId)->count();
        $sertifikasiKadaluarsa = $this->scopeQuery(ComplianceRecord::where('status', 'kedaluwarsa'), $unitId)->count();
        $sertifikasiByTipe = $this->scopeQuery(
            ComplianceRecord::select('tipe', DB::raw('count(*) as total'))->groupBy('tipe'),
            $unitId
        )->pluck('total', 'tipe');

        // === ANALYTICS STATS ===
        $totalIndikator = $this->scopeQuery(AnalyticsIndicator::where('is_active', true), $unitId)->count();
        $indikatorByKategori = $this->scopeQuery(
            AnalyticsIndicator::select('kategori', DB::raw('count(*) as total'))->where('is_active', true)->groupBy('kategori'),
            $unitId
        )->pluck('total', 'kategori');

        // === STOCK OPNAME STATS ===
        $totalOpname = $this->scopeQuery(StockOpname::query(), $unitId)->count();
        $opnameBulan = $this->scopeQuery(
            StockOpname::whereMonth('tanggal_opname', now()->month)->whereYear('tanggal_opname', now()->year),
            $unitId
        )->count();

        // === CHART DATA: Financial Trend ===
        $financeMonths = [];
        $incomeData = [];
        $expenseData = [];
        $financeByMonth = $this->scopeQuery(
            FinancialTransaction::select(DB::raw("DATE_FORMAT(tanggal_transaksi, '%Y-%m') as bulan"), 'tipe', DB::raw('SUM(jumlah) as total'))
                ->whereBetween('tanggal_transaksi', [$chartFrom, $to])
                ->groupBy('bulan', 'tipe')
                ->get(),
            $unitId
        )->groupBy('bulan')->mapWithKeys(function ($rows) {
            $income = $rows->where('tipe', 'pemasukan')->sum('total');
            $expense = $rows->where('tipe', 'pengeluaran')->sum('total');
            return [$rows->first()->bulan => ['income' => (float) $income, 'expense' => (float) $expense]];
        });
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $financeMonths[] = $date->format('M Y');
            $incomeData[] = $financeByMonth[$date->format('Y-m')]['income'] ?? 0;
            $expenseData[] = $financeByMonth[$date->format('Y-m')]['expense'] ?? 0;
        }

        // === CHART DATA: Distribusi by Date Range ===
        $dailyDistDates = [];
        $dailyDistData = [];
        $days = $from->diffInDays($to);
        if ($days > 90) $days = 90;
        $distChartFrom = $to->copy()->subDays($days)->startOfDay();
        $distByDay = $this->scopeQuery(
            Distribution::select(DB::raw("DATE(tanggal_distribusi) as tgl"), DB::raw('COUNT(*) as total'))
                ->whereBetween('tanggal_distribusi', [$distChartFrom, $to])
                ->groupBy('tgl'),
            $unitId
        )->pluck('total', 'tgl');
        for ($i = $days; $i >= 0; $i--) {
            $date = $to->copy()->subDays($i);
            $dailyDistDates[] = $date->format('d M Y');
            $dailyDistData[] = (int) ($distByDay[$date->format('Y-m-d')] ?? 0);
        }

        // === CHART DATA: Food Program Trend ===
        $programTrendMonths = [];
        $programTrendData = [];
        $programByMonth = $this->scopeQuery(
            FoodProgram::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$chartFrom, $to])
                ->groupBy('bulan'),
            $unitId
        )->pluck('total', 'bulan');
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $programTrendMonths[] = $date->format('M Y');
            $programTrendData[] = (int) ($programByMonth[$date->format('Y-m')] ?? 0);
        }

        // === CHART DATA: Nutrition + Processing Combined ===
        $nutritionByTipe = $this->scopeQuery(
            NutritionPlan::select('tipe_porsi', DB::raw('count(*) as total'))->groupBy('tipe_porsi'),
            $unitId
        )->pluck('total', 'tipe_porsi');

        // === CHART DATA: Analytics by Kategori ===
        $analyticsByKategori = $this->scopeQuery(
            AnalyticsIndicator::select('kategori', DB::raw('count(*) as total'))->where('is_active', true)->groupBy('kategori'),
            $unitId
        )->pluck('total', 'kategori');

        return view('dashboard', compact(
            'wbpAktif', 'programAktif', 'panenBulanIni', 'pendapatanBulan',
            'lahanTersedia', 'aktifTanam', 'totalIkan', 'totalTernak',
            'harvestMonths', 'harvestData',
            'programDistribution',
            'totalItems', 'stokMenipis', 'pemasukanBulan', 'pengeluaranBulan',
            'panenChange', 'pendapatanChange', 'pemasukanChange', 'pengeluaranChange',
            'distribusiChange', 'laporanChange',
            'livestockByType', 'farmingStatus',
            'laporanHariIni', 'laporanMenunggu',
            'totalDistribusi', 'distribusiBulan', 'distribusiByTipe',
            'totalMenu', 'menuByTipe', 'avgKalori',
            'totalBatch', 'batchAktif', 'batchSelesai', 'batchByTipe',
            'totalProgramPelatihan', 'programPelatihanAktif', 'totalPeserta', 'pesertaLulus', 'pelatihanByTipe',
            'totalSertifikasi', 'sertifikasiAktif', 'sertifikasiKadaluarsa', 'sertifikasiByTipe',
            'totalIndikator', 'indikatorByKategori',
            'totalOpname', 'opnameBulan',
            'financeMonths', 'incomeData', 'expenseData',
            'dailyDistDates', 'dailyDistData',
            'programTrendMonths', 'programTrendData',
            'nutritionByTipe', 'analyticsByKategori',
            'fromDate', 'toDate', 'units'
        ));
    }

    /**
     * Data untuk semua chart dashboard (dipakai index() dan stats()).
     * Perhitungan harus sama persis dengan blok chart di index().
     */
    private function buildChartData(Carbon $from, Carbon $to, ?int $unitId): array
    {
        $months = $from->diffInMonths($to);
        if ($months < 1) $months = 1;
        if ($months > 24) $months = 24;
        $chartFrom = $to->copy()->subMonths($months)->startOfMonth();

        // === CHART: Panen per Bulan ===
        $harvestMonths = [];
        $harvestData = [];
        $harvestByMonth = $this->scopeQuery(
            Harvest::select(DB::raw("DATE_FORMAT(tanggal_panen, '%Y-%m') as bulan"), DB::raw('SUM(total_berat_kg) as total'))
                ->whereBetween('tanggal_panen', [$chartFrom, $to])
                ->groupBy('bulan'),
            $unitId
        )->pluck('total', 'bulan');
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $harvestMonths[] = $date->format('M Y');
            $harvestData[] = (float) ($harvestByMonth[$date->format('Y-m')] ?? 0);
        }

        // === CHART: Distribusi Program ===
        $programDistribution = $this->scopeQuery(
            FoodProgram::select('jenis_program', DB::raw('count(*) as total'))->groupBy('jenis_program'),
            $unitId
        )->pluck('total', 'jenis_program');

        // === CHART: Peternakan per Jenis ===
        $livestockQuery = LivestockInventory::join('livestock_types', 'livestock_inventory.livestock_type_id', '=', 'livestock_types.id')
            ->select('livestock_types.nama_jenis', DB::raw('sum(livestock_inventory.jumlah) as total'))
            ->where('livestock_inventory.status', 'hidup')
            ->groupBy('livestock_types.nama_jenis');
        $livestockByType = $this->scopeQuery($livestockQuery, $unitId)->pluck('total', 'livestock_types.nama_jenis');

        // === CHART: Status Rencana Kerja ===
        $farmingStatus = $this->scopeQuery(
            FarmingPlan::select('status', DB::raw('count(*) as total'))->groupBy('status'),
            $unitId
        )->pluck('total', 'status');

        // === CHART: Pelatihan per Tipe ===
        $pelatihanByTipe = $this->scopeQuery(
            TrainingProgram::select('tipe', DB::raw('count(*) as total'))->groupBy('tipe'),
            $unitId
        )->pluck('total', 'tipe');

        // === CHART: Sertifikasi per Tipe ===
        $sertifikasiByTipe = $this->scopeQuery(
            ComplianceRecord::select('tipe', DB::raw('count(*) as total'))->groupBy('tipe'),
            $unitId
        )->pluck('total', 'tipe');

        // === CHART: Distribusi per Tipe (dalam rentang tanggal) ===
        $distribusiByTipe = $this->scopeQuery(
            Distribution::select('tipe_distribusi', DB::raw('count(*) as total'))->whereBetween('tanggal_distribusi', [$from, $to])->groupBy('tipe_distribusi'),
            $unitId
        )->pluck('total', 'tipe_distribusi');

        // === CHART: Tren Keuangan ===
        $financeMonths = [];
        $incomeData = [];
        $expenseData = [];
        $financeByMonth = $this->scopeQuery(
            FinancialTransaction::select(DB::raw("DATE_FORMAT(tanggal_transaksi, '%Y-%m') as bulan"), 'tipe', DB::raw('SUM(jumlah) as total'))
                ->whereBetween('tanggal_transaksi', [$chartFrom, $to])
                ->groupBy('bulan', 'tipe')
                ->get(),
            $unitId
        )->groupBy('bulan')->mapWithKeys(function ($rows) {
            $income = $rows->where('tipe', 'pemasukan')->sum('total');
            $expense = $rows->where('tipe', 'pengeluaran')->sum('total');
            return [$rows->first()->bulan => ['income' => (float) $income, 'expense' => (float) $expense]];
        });
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $financeMonths[] = $date->format('M Y');
            $incomeData[] = $financeByMonth[$date->format('Y-m')]['income'] ?? 0;
            $expenseData[] = $financeByMonth[$date->format('Y-m')]['expense'] ?? 0;
        }

        // === CHART: Distribusi Harian ===
        $dailyDistDates = [];
        $dailyDistData = [];
        $days = $from->diffInDays($to);
        if ($days > 90) $days = 90;
        $distChartFrom = $to->copy()->subDays($days)->startOfDay();
        $distByDay = $this->scopeQuery(
            Distribution::select(DB::raw("DATE(tanggal_distribusi) as tgl"), DB::raw('COUNT(*) as total'))
                ->whereBetween('tanggal_distribusi', [$distChartFrom, $to])
                ->groupBy('tgl'),
            $unitId
        )->pluck('total', 'tgl');
        for ($i = $days; $i >= 0; $i--) {
            $date = $to->copy()->subDays($i);
            $dailyDistDates[] = $date->format('d M Y');
            $dailyDistData[] = (int) ($distByDay[$date->format('Y-m-d')] ?? 0);
        }

        // === CHART: Tren Program Pangan ===
        $programTrendMonths = [];
        $programTrendData = [];
        $programByMonth = $this->scopeQuery(
            FoodProgram::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"), DB::raw('COUNT(*) as total'))
                ->whereBetween('created_at', [$chartFrom, $to])
                ->groupBy('bulan'),
            $unitId
        )->pluck('total', 'bulan');
        for ($i = $months; $i >= 0; $i--) {
            $date = $to->copy()->subMonths($i);
            $programTrendMonths[] = $date->format('M Y');
            $programTrendData[] = (int) ($programByMonth[$date->format('Y-m')] ?? 0);
        }

        // === CHART: Nutrisi + Pengolahan (menu aktif + semua rencana gizi) ===
        $menuByTipe = $this->scopeQuery(
            NutritionPlan::select('tipe_porsi', DB::raw('count(*) as total'))->where('status', 'aktif')->groupBy('tipe_porsi'),
            $unitId
        )->pluck('total', 'tipe_porsi');
        $nutritionByTipe = $this->scopeQuery(
            NutritionPlan::select('tipe_porsi', DB::raw('count(*) as total'))->groupBy('tipe_porsi'),
            $unitId
        )->pluck('total', 'tipe_porsi');

        // === CHART: Analitik per Kategori ===
        $analyticsByKategori = $this->scopeQuery(
            AnalyticsIndicator::select('kategori', DB::raw('count(*) as total'))->where('is_active', true)->groupBy('kategori'),
            $unitId
        )->pluck('total', 'kategori');

        return compact(
            'harvestMonths', 'harvestData', 'programDistribution',
            'livestockByType', 'farmingStatus', 'pelatihanByTipe', 'sertifikasiByTipe', 'distribusiByTipe',
            'financeMonths', 'incomeData', 'expenseData',
            'dailyDistDates', 'dailyDistData',
            'programTrendMonths', 'programTrendData',
            'menuByTipe', 'nutritionByTipe', 'analyticsByKategori'
        );
    }

    /**
     * AJAX endpoint for auto-refresh dashboard stats.
     */
    public function stats(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $unitId = $this->getUnitId($request);

        // Load units for super-admin filter
        $units = null;
        if (auth()->user()->hasRole('super-admin')) {
            $units = \App\Models\Unit::orderBy('nama_unit')->get();
        }
        $now = now();
        $fromDate = $request->input('from_date', $now->copy()->subMonths(11)->startOfMonth()->format('Y-m-d'));
        $toDate = $request->input('to_date', $now->format('Y-m-d'));
        $from = \Carbon\Carbon::parse($fromDate);
        $to = \Carbon\Carbon::parse($toDate);
        $prevFrom = $from->copy()->subMonth();
        $prevTo = $to->copy()->subMonth();

        $panenBulanIni = $this->scopeQuery(Harvest::whereBetween('tanggal_panen', [$from, $to]), $unitId)->sum('total_berat_kg');
        $pendapatanBulan = $this->scopeQuery(FinancialTransaction::where('tipe', 'pemasukan')->whereBetween('tanggal_transaksi', [$from, $to]), $unitId)->sum('jumlah');
        $pengeluaranBulan = $this->scopeQuery(FinancialTransaction::where('tipe', 'pengeluaran')->whereBetween('tanggal_transaksi', [$from, $to]), $unitId)->sum('jumlah');
        $distribusiBulan = $this->scopeQuery(Distribution::whereBetween('tanggal_distribusi', [$from, $to]), $unitId)->count();
        $laporanHariIni = $this->scopeQuery(DailyReport::whereDate('tanggal_laporan', now()), $unitId)->count();

        $panenBulanLalu = $this->scopeQuery(Harvest::whereBetween('tanggal_panen', [$prevFrom, $prevTo]), $unitId)->sum('total_berat_kg');
        $pendapatanBulanLalu = $this->scopeQuery(FinancialTransaction::where('tipe', 'pemasukan')->whereBetween('tanggal_transaksi', [$prevFrom, $prevTo]), $unitId)->sum('jumlah');
        $pengeluaranBulanLalu = $this->scopeQuery(FinancialTransaction::where('tipe', 'pengeluaran')->whereBetween('tanggal_transaksi', [$prevFrom, $prevTo]), $unitId)->sum('jumlah');
        $distribusiBulanLalu = $this->scopeQuery(Distribution::whereBetween('tanggal_distribusi', [$prevFrom, $prevTo]), $unitId)->count();
        $laporanBulanLalu = $this->scopeQuery(DailyReport::whereBetween('tanggal_laporan', [$prevFrom, $prevTo]), $unitId)->count();

        $calcChange = fn($current, $previous) => $previous > 0 ? round(($current - $previous) / $previous * 100) : ($current > 0 ? 100 : 0);

        // Data chart (sama dengan index())
        $charts = $this->buildChartData($from, $to, $unitId);

        return response()->json([
            'wbp_aktif' => $this->scopeQuery(Wbp::where('status', 'aktif'), $unitId)->count(),
            'program_aktif' => $this->scopeQuery(FoodProgram::where('status', 'aktif'), $unitId)->count(),
            'panen_bulan_ini' => (float) $panenBulanIni,
            'pendapatan_bulan' => (float) $pendapatanBulan,
            'pemasukan_bulan' => (float) $pendapatanBulan,
            'pengeluaran_bulan' => (float) $pengeluaranBulan,
            'distribusi_bulan' => $distribusiBulan,
            'laporan_hari_ini' => $laporanHariIni,
            'total_items' => $this->scopeQuery(InventoryItem::query(), $unitId)->count(),
            'stok_menipis' => $this->scopeQuery(InventoryItem::whereColumn('stok_saat_ini', '<', 'stok_minimum'), $unitId)->count(),
            'lahan_tersedia' => $this->scopeQuery(LandPlot::where('status', 'tersedia'), $unitId)->count(),
            'aktif_tanam' => $this->scopeQuery(FarmingPlan::whereIn('status', ['proses_tanam', 'tumbuh']), $unitId)->count(),
            'total_ikan' => (float) $this->scopeQuery(FishStock::where('status', 'hidup'), $unitId)->sum('jumlah'),
            'total_ternak' => (float) $this->scopeQuery(LivestockInventory::where('status', 'hidup'), $unitId)->sum('jumlah'),
            'panen_change' => $calcChange($panenBulanIni, $panenBulanLalu),
            'pendapatan_change' => $calcChange($pendapatanBulan, $pendapatanBulanLalu),
            'pemasukan_change' => $calcChange($pendapatanBulan, $pendapatanBulanLalu),
            'pengeluaran_change' => $calcChange($pengeluaranBulan, $pengeluaranBulanLalu),
            'distribusi_change' => $calcChange($distribusiBulan, $distribusiBulanLalu),
            'laporan_change' => $calcChange($laporanHariIni, $laporanBulanLalu),
            'timestamp' => $now->format('H:i:s'),
            'charts' => $charts,
        ]);
    }
}
