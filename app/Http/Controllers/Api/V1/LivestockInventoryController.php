<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\LivestockInventory;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Peternakan")
 */
class LivestockInventoryController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/livestock-inventories",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $livestockInventories = LivestockInventory::query()
            ->with(['barn', 'livestockType'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($livestockInventories);
    }
    /**
     * @OA\Post(
     *     path="/livestock-inventories",
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
        $validated = $request->only(['kode_ternak', 'barn_id', 'livestock_type_id', 'jumlah', 'berat_rata_kg', 'tanggal_masuk', 'asal', 'harga_pokok', 'status', 'keterangan']);
        $livestockInventory = LivestockInventory::create($validated);
        return $this->success($livestockInventory, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/livestock-inventories/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LivestockInventory $livestockInventory)
    {
        return $this->success($livestockInventory);
    }
    /**
     * @OA\Put(
     *     path="/livestock-inventories/{id}",
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
    public function update(Request $request, LivestockInventory $livestockInventory)
    {
        $validated = $request->only(['kode_ternak', 'barn_id', 'livestock_type_id', 'jumlah', 'berat_rata_kg', 'tanggal_masuk', 'asal', 'harga_pokok', 'status', 'keterangan']);
        $livestockInventory->update($validated);
        return $this->success($livestockInventory->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/livestock-inventories/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(LivestockInventory $livestockInventory)
    {
        $livestockInventory->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
