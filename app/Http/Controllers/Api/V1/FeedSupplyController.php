<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FeedSupply;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Peternakan")
 */
class FeedSupplyController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/feed-supplies",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $feedSupplies = FeedSupply::query()
            ->with(['feedLogs'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($feedSupplies);
    }
    /**
     * @OA\Post(
     *     path="/feed-supplies",
     *     tags={"Peternakan"},
     *     summary="Create Peternakan",
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
        $validated = $request->only(['kode_pakan', 'nama_pakan', 'jenis_pakan', 'satuan', 'stok', 'stok_minimum', 'harga_per_satuan', 'supplier', 'tanggal_kadaluarsa', 'status']);
        $feedSupply = FeedSupply::create($validated);
        return $this->success($feedSupply, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/feed-supplies/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FeedSupply $feedSupply)
    {
        $feedSupply->load(['feedLogs']);
        return $this->success($feedSupply);
    }
    /**
     * @OA\Put(
     *     path="/feed-supplies/{id}",
     *     tags={"Peternakan"},
     *     summary="Update Peternakan",
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
    public function update(Request $request, FeedSupply $feedSupply)
    {
        $validated = $request->only(['kode_pakan', 'nama_pakan', 'jenis_pakan', 'satuan', 'stok', 'stok_minimum', 'harga_per_satuan', 'supplier', 'tanggal_kadaluarsa', 'status']);
        $feedSupply->update($validated);
        return $this->success($feedSupply->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/feed-supplies/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FeedSupply $feedSupply)
    {
        $feedSupply->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
