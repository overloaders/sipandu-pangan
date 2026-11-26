<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\AnalyticsIndicator;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Analitik")
 */
class AnalyticsIndicatorController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/analytics-indicators",
     *     tags={"Analitik"},
     *     summary="List Analitik",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = AnalyticsIndicator::query()
            ->with(['targets']);
        $result = $this->cachedPaginate($query, 'analytics_indicators', 5, [], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/analytics-indicators",
     *     tags={"Analitik"},
     *     summary="Create Analitik",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_indikator","nama_indikator","kategori","satuan","target_nilai"},
     *             @OA\Property(property="kode_indikator", type="string", maxLength=30),
     *             @OA\Property(property="nama_indikator", type="string", maxLength=200),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="kategori", type="string", maxLength=100),
     *             @OA\Property(property="rumus_perhitungan", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="satuan", type="string", maxLength=50),
     *             @OA\Property(property="target_nilai", type="integer", enum={"0"}),
     *             @OA\Property(property="batas_minimum", type="integer", nullable=true),
     *             @OA\Property(property="batas_maksimum", type="integer", nullable=true),
     *             @OA\Property(property="unit_id", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_indikator', 'nama_indikator', 'kategori', 'deskripsi', 'rumus_perhitungan', 'satuan', 'target_nilai', 'batas_minimum', 'batas_maksimum', 'unit_id', 'is_active']);
        $analyticsIndicator = AnalyticsIndicator::create($validated);
        return $this->success($analyticsIndicator, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/analytics-indicators/{id}",
     *     tags={"Analitik"},
     *     summary="Get Analitik detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(AnalyticsIndicator $analyticsIndicator)
    {
        return $this->success($analyticsIndicator);
    }
    /**
     * @OA\Put(
     *     path="/analytics-indicators/{id}",
     *     tags={"Analitik"},
     *     summary="Update Analitik",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_indikator", type="string", maxLength=200),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="kategori", type="string", maxLength=100),
     *             @OA\Property(property="rumus_perhitungan", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="satuan", type="string", maxLength=50),
     *             @OA\Property(property="target_nilai", type="integer", enum={"0"}),
     *             @OA\Property(property="batas_minimum", type="integer", nullable=true),
     *             @OA\Property(property="batas_maksimum", type="integer", nullable=true),
     *             @OA\Property(property="unit_id", type="string", nullable=true),
     *             @OA\Property(property="is_active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, AnalyticsIndicator $analyticsIndicator)
    {
        $validated = $request->only(['kode_indikator', 'nama_indikator', 'kategori', 'deskripsi', 'rumus_perhitungan', 'satuan', 'target_nilai', 'batas_minimum', 'batas_maksimum', 'unit_id', 'is_active']);
        $analyticsIndicator->update($validated);
        return $this->success($analyticsIndicator->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/analytics-indicators/{id}",
     *     tags={"Analitik"},
     *     summary="Delete Analitik",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(AnalyticsIndicator $analyticsIndicator)
    {
        $analyticsIndicator->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
