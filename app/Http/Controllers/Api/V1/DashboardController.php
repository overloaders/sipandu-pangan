<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\Wbp;
use App\Models\FoodProgram;
use App\Models\FarmingPlan;
use App\Models\Harvest;
use App\Models\LivestockInventory;
use App\Models\FishStock;
use App\Models\InventoryItem;
use App\Models\FinancialTransaction;
use App\Models\DailyReport;
use App\Models\Distribution;
use App\Models\Unit;
use App\Models\LandPlot;
use App\Models\LivestockBarn;
use App\Models\FishPond;

/**
 * @OA\Tag(name="Dashboard", description="Dashboard overview and statistics")
 */
class DashboardController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/dashboard",
     *     tags={"Dashboard"},
     *     summary="Get dashboard overview",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        $data = [
            'wbp_aktif' => Wbp::where('status', 'aktif')->count(),
            'program_aktif' => FoodProgram::where('status', 'aktif')->count(),
            'total_lahan' => LandPlot::count(),
            'total_kandang' => LivestockBarn::count(),
            'total_kolam' => FishPond::count(),
            'total_ternak' => LivestockInventory::where('status', 'hidup')->sum('jumlah'),
            'total_ikan' => FishStock::where('status', 'hidup')->sum('jumlah'),
            'total_inventaris' => InventoryItem::count(),
            'stok_menipis' => InventoryItem::whereColumn('stok_saat_ini', '<', 'stok_minimum')->count(),
            'pemasukan_bulan' => FinancialTransaction::where('tipe', 'pemasukan')
                ->whereMonth('tanggal_transaksi', now()->month)->sum('jumlah'),
            'pengeluaran_bulan' => FinancialTransaction::where('tipe', 'pengeluaran')
                ->whereMonth('tanggal_transaksi', now()->month)->sum('jumlah'),
            'laporan_hari_ini' => DailyReport::whereDate('tanggal_laporan', today())->count(),
            'total_distribusi' => Distribution::count(),
        ];

        return $this->success($data);
    }

    /**
     * @OA\Get(
     *     path="/dashboard/statistics",
     *     tags={"Dashboard"},
     *     summary="Get detailed statistics",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function statistics()
    {
        $data = [
            'harvest_by_month' => Harvest::selectRaw('month(tanggal_panen) as bulan, sum(total_berat_kg) as total')
                ->whereYear('tanggal_panen', now()->year)
                ->groupByRaw('month(tanggal_panen)')
                ->pluck('total', 'bulan'),
            'program_by_jenis' => FoodProgram::selectRaw('jenis_program, count(*) as total')
                ->groupBy('jenis_program')
                ->pluck('total', 'jenis_program'),
        ];

        return $this->success($data);
    }

    /**
     * @OA\Get(
     *     path="/dashboard/farming-summary",
     *     tags={"Dashboard"},
     *     summary="Get farming summary",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function farmingSummary()
    {
        $data = [
            'total_rencana' => FarmingPlan::count(),
            'sedang_proses' => FarmingPlan::whereIn('status', ['proses_tanam', 'tumbuh'])->count(),
            'siap_panen' => FarmingPlan::where('status', 'siap_panen')->count(),
            'total_panen_bulan' => Harvest::whereMonth('tanggal_panen', now()->month)->sum('total_berat_kg'),
        ];

        return $this->success($data);
    }

    /**
     * @OA\Get(
     *     path="/dashboard/livestock-summary",
     *     tags={"Dashboard"},
     *     summary="Get livestock summary",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function livestockSummary()
    {
        $data = [
            'total_kandang' => LivestockBarn::where('status', 'aktif')->count(),
            'total_ternak' => LivestockInventory::where('status', 'hidup')->sum('jumlah'),
        ];

        return $this->success($data);
    }

    /**
     * @OA\Get(
     *     path="/dashboard/fishery-summary",
     *     tags={"Dashboard"},
     *     summary="Get fishery summary",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function fisherySummary()
    {
        $data = [
            'total_kolam' => FishPond::where('status', 'aktif')->count(),
            'total_ikan' => FishStock::where('status', 'hidup')->sum('jumlah'),
        ];

        return $this->success($data);
    }
}
