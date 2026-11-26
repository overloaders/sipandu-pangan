<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FishStock;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Perikanan")
 */
class FishStockController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/fish-stocks",
     *     tags={"Perikanan"},
     *     summary="List Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $fishStocks = FishStock::query()
            ->with(['pond'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($fishStocks);
    }
    /**
     * @OA\Post(
     *     path="/fish-stocks",
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
        $validated = $request->only(['kode_stok_ikan', 'pond_id', 'jenis_ikan', 'kategori', 'jumlah', 'ukuran_rata_cm', 'berat_rata_gram', 'tanggal_tebur', 'harga_benih', 'total_biaya_pakan', 'status', 'keterangan']);
        $fishStock = FishStock::create($validated);
        return $this->success($fishStock, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/fish-stocks/{id}",
     *     tags={"Perikanan"},
     *     summary="Get Perikanan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FishStock $fishStock)
    {
        return $this->success($fishStock);
    }
    /**
     * @OA\Put(
     *     path="/fish-stocks/{id}",
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
    public function update(Request $request, FishStock $fishStock)
    {
        $validated = $request->only(['kode_stok_ikan', 'pond_id', 'jenis_ikan', 'kategori', 'jumlah', 'ukuran_rata_cm', 'berat_rata_gram', 'tanggal_tebur', 'harga_benih', 'total_biaya_pakan', 'status', 'keterangan']);
        $fishStock->update($validated);
        return $this->success($fishStock->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/fish-stocks/{id}",
     *     tags={"Perikanan"},
     *     summary="Delete Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FishStock $fishStock)
    {
        $fishStock->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
