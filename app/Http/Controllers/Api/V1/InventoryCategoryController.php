<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Inventaris & Stok")
 */
class InventoryCategoryController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/inventory-categories",
     *     tags={"Inventaris & Stok"},
     *     summary="List Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = InventoryCategory::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'inventorycategory.phps', 5, [], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/inventory-categories",
     *     tags={"Inventaris & Stok"},
     *     summary="Create Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_kategori","nama_kategori","tipe"},
     *             @OA\Property(property="kode_kategori", type="string", maxLength=30),
     *             @OA\Property(property="nama_kategori", type="string", maxLength=100),
     *             @OA\Property(property="tipe", type="string", enum={"pangan"}),
     *             @OA\Property(property="satuan_standar", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_kategori', 'nama_kategori', 'tipe', 'satuan_standar', 'keterangan']);
        $inventoryCategory = InventoryCategory::create($validated);
        return $this->success($inventoryCategory, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/inventory-categories/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Get Inventaris & Stok detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(InventoryCategory $inventoryCategory)
    {
        return $this->success($inventoryCategory);
    }
    /**
     * @OA\Put(
     *     path="/inventory-categories/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Update Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kategori", type="string", maxLength=100),
     *             @OA\Property(property="tipe", type="string", enum={"pangan"}),
     *             @OA\Property(property="satuan_standar", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, InventoryCategory $inventoryCategory)
    {
        $validated = $request->only(['kode_kategori', 'nama_kategori', 'tipe', 'satuan_standar', 'keterangan']);
        $inventoryCategory->update($validated);
        return $this->success($inventoryCategory->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/inventory-categories/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Delete Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(InventoryCategory $inventoryCategory)
    {
        $inventoryCategory->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
