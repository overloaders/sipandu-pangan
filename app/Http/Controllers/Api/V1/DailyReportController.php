<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\DailyReport;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Laporan Harian")
 */
class DailyReportController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/daily-reports",
     *     tags={"Laporan Harian"},
     *     summary="List Laporan Harian",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = DailyReport::query()
            ->with(['unit', 'reporter']);
        $result = $this->cachedPaginate($query, 'daily_reports', 5, ['kode_laporan'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/daily-reports",
     *     tags={"Laporan Harian"},
     *     summary="Create Laporan Harian",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"unit_id","tanggal_laporan"},
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_laporan", type="string", format="date"),
     *             @OA\Property(property="ringkasan_kegiatan", type="string", maxLength=2000, nullable=true),
     *             @OA\Property(property="catatan_khusus", type="string", maxLength=1000, nullable=true),
     *             @OA\Property(property="masalah_ditemukan", type="string", maxLength=1000, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_laporan', 'unit_id', 'tanggal_laporan', 'dilaporkan_oleh', 'dikonfirmasi_oleh', 'status', 'total_wbp_aktif', 'total_wbp_baru_masuk', 'total_wbp_keluar', 'total_panen_hari_ini_kg', 'total_pakan_hari_ini_kg', 'total_pendapatan_hari_ini', 'total_pengeluaran_hari_ini', 'ringkasan_kegiatan', 'catatan_khusus', 'masalah_ditemukan']);
        $dailyReport = DailyReport::create($validated);
        return $this->success($dailyReport, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/daily-reports/{id}",
     *     tags={"Laporan Harian"},
     *     summary="Get Laporan Harian detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(DailyReport $dailyReport)
    {
        return $this->success($dailyReport);
    }
    /**
     * @OA\Put(
     *     path="/daily-reports/{id}",
     *     tags={"Laporan Harian"},
     *     summary="Update Laporan Harian",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"draft"}),
     *             @OA\Property(property="ringkasan_kegiatan", type="string", maxLength=2000, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, DailyReport $dailyReport)
    {
        $validated = $request->only(['kode_laporan', 'unit_id', 'tanggal_laporan', 'dilaporkan_oleh', 'dikonfirmasi_oleh', 'status', 'total_wbp_aktif', 'total_wbp_baru_masuk', 'total_wbp_keluar', 'total_panen_hari_ini_kg', 'total_pakan_hari_ini_kg', 'total_pendapatan_hari_ini', 'total_pengeluaran_hari_ini', 'ringkasan_kegiatan', 'catatan_khusus', 'masalah_ditemukan']);
        $dailyReport->update($validated);
        return $this->success($dailyReport->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/daily-reports/{id}",
     *     tags={"Laporan Harian"},
     *     summary="Delete Laporan Harian",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(DailyReport $dailyReport)
    {
        $dailyReport->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
