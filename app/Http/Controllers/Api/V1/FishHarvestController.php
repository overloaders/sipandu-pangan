<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FishHarvest;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Perikanan")
 */
class FishHarvestController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/fish-harvests",
     *     tags={"Perikanan"},
     *     summary="List Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $fishHarvests = FishHarvest::query()
            ->with(['fishStock', 'pond'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($fishHarvests);
    }
    /**
     * @OA\Post(
     *     path="/fish-harvests",
     *     tags={"Perikanan"},
     *     summary="Create Perikanan",
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
        $validated = $request->only(['kode_panen_ikan', 'fish_stock_id', 'pond_id', 'tanggal_panen', 'jumlah_dipanen', 'total_berat_kg', 'ukuran_rata_cm', 'harga_jual_per_kg', 'total_pendapatan', 'total_biaya', 'kualitas', 'tujuan', 'keterangan']);
        $fishHarvest = FishHarvest::create($validated);
        return $this->success($fishHarvest, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/fish-harvests/{id}",
     *     tags={"Perikanan"},
     *     summary="Get Perikanan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FishHarvest $fishHarvest)
    {
        $fishHarvest->load(['fishStock', 'pond']);
        return $this->success($fishHarvest);
    }
    /**
     * @OA\Put(
     *     path="/fish-harvests/{id}",
     *     tags={"Perikanan"},
     *     summary="Update Perikanan",
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
    public function update(Request $request, FishHarvest $fishHarvest)
    {
        $validated = $request->only(['kode_panen_ikan', 'fish_stock_id', 'pond_id', 'tanggal_panen', 'jumlah_dipanen', 'total_berat_kg', 'ukuran_rata_cm', 'harga_jual_per_kg', 'total_pendapatan', 'total_biaya', 'kualitas', 'tujuan', 'keterangan']);
        $fishHarvest->update($validated);
        return $this->success($fishHarvest->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/fish-harvests/{id}",
     *     tags={"Perikanan"},
     *     summary="Delete Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FishHarvest $fishHarvest)
    {
        $fishHarvest->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
