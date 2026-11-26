<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\LandPlot;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Manajemen Lahan")
 */
class LandPlotController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/land-plots",
     *     tags={"Manajemen Lahan"},
     *     summary="List Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = LandPlot::query()
            ->with(['unit']);
        $result = $this->cachedPaginate($query, 'land_plots', 5, ['nama_lahan'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/land-plots",
     *     tags={"Manajemen Lahan"},
     *     summary="Create Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_lahan","nama_lahan","unit_id","status"},
     *             @OA\Property(property="kode_lahan", type="string", maxLength=30),
     *             @OA\Property(property="nama_lahan", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="jenis_tanah", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="sistem_irigasi", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"tersedia"}),
     *             @OA\Property(property="latitude", type="integer", nullable=true),
     *             @OA\Property(property="longitude", type="integer", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_lahan', 'nama_lahan', 'unit_id', 'luas_m2', 'jenis_tanah', 'sistem_irigasi', 'status', 'latitude', 'longitude', 'keterangan']);
        $landPlot = LandPlot::create($validated);
        return $this->success($landPlot, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/land-plots/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Get Manajemen Lahan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LandPlot $landPlot)
    {
        return $this->success($landPlot);
    }
    /**
     * @OA\Put(
     *     path="/land-plots/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Update Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_lahan", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="jenis_tanah", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="sistem_irigasi", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"tersedia"}),
     *             @OA\Property(property="latitude", type="integer", nullable=true),
     *             @OA\Property(property="longitude", type="integer", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, LandPlot $landPlot)
    {
        $validated = $request->only(['kode_lahan', 'nama_lahan', 'unit_id', 'luas_m2', 'jenis_tanah', 'sistem_irigasi', 'status', 'latitude', 'longitude', 'keterangan']);
        $landPlot->update($validated);
        return $this->success($landPlot->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/land-plots/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Delete Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(LandPlot $landPlot)
    {
        $landPlot->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
