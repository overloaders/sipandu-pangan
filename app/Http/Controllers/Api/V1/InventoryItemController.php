<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Inventaris & Stok")
 */
class InventoryItemController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/inventory-items",
     *     tags={"Inventaris & Stok"},
     *     summary="List Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = InventoryItem::query()
            ->with(['category', 'unit']);
        $result = $this->cachedPaginate($query, 'inventory_items', 5, ['kode_item', 'nama_item'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/inventory-items",
     *     tags={"Inventaris & Stok"},
     *     summary="Create Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_item","nama_item","unit_id","tipe_pangan","asal"},
     *             @OA\Property(property="kode_item", type="string", maxLength=30),
     *             @OA\Property(property="nama_item", type="string", maxLength=200),
     *             @OA\Property(property="category_id", type="string", nullable=true),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="satuan", type="string", maxLength=30),
     *             @OA\Property(property="stok_saat_ini", type="integer", enum={"0"}),
     *             @OA\Property(property="stok_minimum", type="integer", enum={"0"}),
     *             @OA\Property(property="harga_beli_rata", type="integer", enum={"0"}),
     *             @OA\Property(property="harga_jual", type="integer", enum={"0"}),
     *             @OA\Property(property="tipe_pangan", type="string", enum={"basah"}),
     *             @OA\Property(property="asal", type="string", enum={"produksi_sendiri"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_item', 'nama_item', 'category_id', 'unit_id', 'satuan', 'stok_saat_ini', 'stok_minimum', 'stok_maksimum', 'harga_beli_rata', 'harga_jual', 'tanggal_kadaluarsa', 'tipe_pangan', 'asal', 'status', 'lokasi_simpan', 'foto', 'keterangan']);
        $inventoryItem = InventoryItem::create($validated);
        return $this->success($inventoryItem, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/inventory-items/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Get Inventaris & Stok detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(InventoryItem $inventoryItem)
    {
        return $this->success($inventoryItem);
    }
    /**
     * @OA\Put(
     *     path="/inventory-items/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Update Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_item", type="string", maxLength=200),
     *             @OA\Property(property="stok_saat_ini", type="integer", enum={"0"}),
     *             @OA\Property(property="stok_minimum", type="integer", enum={"0"}),
     *             @OA\Property(property="harga_jual", type="integer", enum={"0"}),
     *             @OA\Property(property="status", type="string", enum={"tersedia"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->only(['kode_item', 'nama_item', 'category_id', 'unit_id', 'satuan', 'stok_saat_ini', 'stok_minimum', 'stok_maksimum', 'harga_beli_rata', 'harga_jual', 'tanggal_kadaluarsa', 'tipe_pangan', 'asal', 'status', 'lokasi_simpan', 'foto', 'keterangan']);
        $inventoryItem->update($validated);
        return $this->success($inventoryItem->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/inventory-items/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Delete Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
