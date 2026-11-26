<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Unit;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Unit Kerja")
 */
class UnitController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/units",
     *     tags={"Unit Kerja"},
     *     summary="List Unit Kerja",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = Unit::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'units', 5, ['kode_unit', 'nama_unit'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/units",
     *     tags={"Unit Kerja"},
     *     summary="Create Unit Kerja",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_unit","nama_unit","tipe_unit","status"},
     *             @OA\Property(property="kode_unit", type="string", maxLength=30),
     *             @OA\Property(property="nama_unit", type="string", maxLength=200),
     *             @OA\Property(property="tipe_unit", type="string", enum={"pusat"}),
     *             @OA\Property(property="parent_id", type="string", nullable=true),
     *             @OA\Property(property="provinsi", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="kota_kab", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="alamat", type="string", maxLength=500, nullable=true),
     *             @OA\Property(property="telepon", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150, nullable=true),
     *             @OA\Property(property="kapasitas_wbp", type="string", maxLength=10, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_unit', 'nama_unit', 'tipe_unit', 'parent_id', 'provinsi', 'kota_kab', 'alamat', 'telepon', 'email', 'kapasitas_wbp', 'status']);
        $unit = Unit::create($validated);
        return $this->success($unit, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/units/{id}",
     *     tags={"Unit Kerja"},
     *     summary="Get Unit Kerja detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Unit $unit)
    {
        return $this->success($unit);
    }
    /**
     * @OA\Put(
     *     path="/units/{id}",
     *     tags={"Unit Kerja"},
     *     summary="Update Unit Kerja",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_unit", type="string", maxLength=200),
     *             @OA\Property(property="tipe_unit", type="string", enum={"pusat"}),
     *             @OA\Property(property="parent_id", type="string", nullable=true),
     *             @OA\Property(property="provinsi", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="kota_kab", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="alamat", type="string", maxLength=500, nullable=true),
     *             @OA\Property(property="telepon", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150, nullable=true),
     *             @OA\Property(property="kapasitas_wbp", type="string", maxLength=10, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, Unit $unit)
    {
        $validated = $request->only(['kode_unit', 'nama_unit', 'tipe_unit', 'parent_id', 'provinsi', 'kota_kab', 'alamat', 'telepon', 'email', 'kapasitas_wbp', 'status']);
        $unit->update($validated);
        return $this->success($unit->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/units/{id}",
     *     tags={"Unit Kerja"},
     *     summary="Delete Unit Kerja",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
