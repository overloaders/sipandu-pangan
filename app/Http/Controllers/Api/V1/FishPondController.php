<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FishPond;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Perikanan")
 */
class FishPondController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/fish-ponds",
     *     tags={"Perikanan"},
     *     summary="List Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = FishPond::query()
            ->with(['unit', 'fishStocks']);
        $result = $this->cachedPaginate($query, 'fish_ponds', 5, ['kode_kolam', 'nama_kolam'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/fish-ponds",
     *     tags={"Perikanan"},
     *     summary="Create Perikanan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_kolam","nama_kolam","unit_id","tipe_kolam","sumber_air"},
     *             @OA\Property(property="kode_kolam", type="string", maxLength=30),
     *             @OA\Property(property="nama_kolam", type="string", maxLength=150),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tipe_kolam", type="string", enum={"tanah"}),
     *             @OA\Property(property="sumber_air", type="string", enum={"sungai"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_kolam', 'nama_kolam', 'unit_id', 'luas_m2', 'kedalaman_m', 'volume_m3', 'tipe_kolam', 'sumber_air', 'status', 'keterangan']);
        $fishPond = FishPond::create($validated);
        return $this->success($fishPond, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/fish-ponds/{id}",
     *     tags={"Perikanan"},
     *     summary="Get Perikanan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FishPond $fishPond)
    {
        return $this->success($fishPond);
    }
    /**
     * @OA\Put(
     *     path="/fish-ponds/{id}",
     *     tags={"Perikanan"},
     *     summary="Update Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kolam", type="string", maxLength=150),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, FishPond $fishPond)
    {
        $validated = $request->only(['kode_kolam', 'nama_kolam', 'unit_id', 'luas_m2', 'kedalaman_m', 'volume_m3', 'tipe_kolam', 'sumber_air', 'status', 'keterangan']);
        $fishPond->update($validated);
        return $this->success($fishPond->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/fish-ponds/{id}",
     *     tags={"Perikanan"},
     *     summary="Delete Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FishPond $fishPond)
    {
        $fishPond->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
