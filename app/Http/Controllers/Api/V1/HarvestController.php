<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Harvest;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Hasil Panen")
 */
class HarvestController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/harvests",
     *     tags={"Hasil Panen"},
     *     summary="List Hasil Panen",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $harvests = Harvest::query()
            ->with(['farmingPlan.cropType'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($harvests);
    }
    /**
     * @OA\Post(
     *     path="/harvests",
     *     tags={"Hasil Panen"},
     *     summary="Create Hasil Panen",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(@OA\Property(property="data", type="object"))
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->only(['kode_panen', 'farming_plan_id', 'tanggal_panen', 'total_berat_kg', 'total_pendapatan', 'kualitas', 'harga_jual_per_kg', 'keterangan', 'foto']);
        $harvest = Harvest::create($validated);
        return $this->success($harvest, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/harvests/{id}",
     *     tags={"Hasil Panen"},
     *     summary="Get Hasil Panen detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Harvest $harvest)
    {
        return $this->success($harvest);
    }
    /**
     * @OA\Put(
     *     path="/harvests/{id}",
     *     tags={"Hasil Panen"},
     *     summary="Update Hasil Panen",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(@OA\Property(property="data", type="object"))
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Harvest $harvest)
    {
        $validated = $request->only(['kode_panen', 'farming_plan_id', 'tanggal_panen', 'total_berat_kg', 'total_pendapatan', 'kualitas', 'harga_jual_per_kg', 'keterangan', 'foto']);
        $harvest->update($validated);
        return $this->success($harvest->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/harvests/{id}",
     *     tags={"Hasil Panen"},
     *     summary="Delete Hasil Panen",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Harvest $harvest)
    {
        $harvest->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
