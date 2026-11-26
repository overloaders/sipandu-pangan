<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Inventaris & Stok")
 */
class InventoryTransactionController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/inventory-transactions",
     *     tags={"Inventaris & Stok"},
     *     summary="List Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $inventoryTransactions = InventoryTransaction::query()
            ->with(['item', 'refUnit', 'creator'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($inventoryTransactions);
    }
    /**
     * @OA\Post(
     *     path="/inventory-transactions",
     *     tags={"Inventaris & Stok"},
     *     summary="Create Inventaris & Stok",
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
        $validated = $request->only(['kode_transaksi', 'item_id', 'tipe_transaksi', 'jumlah', 'harga_satuan', 'total_nilai', 'sumber', 'ref_unit_id', 'created_by_user_id', 'tanggal_transaksi', 'bukti_transaksi', 'keterangan']);
        $inventoryTransaction = InventoryTransaction::create($validated);
        return $this->success($inventoryTransaction, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/inventory-transactions/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Get Inventaris & Stok detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->load(['item', 'refUnit', 'creator']);
        return $this->success($inventoryTransaction);
    }
    /**
     * @OA\Put(
     *     path="/inventory-transactions/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Update Inventaris & Stok",
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
    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        $validated = $request->only(['kode_transaksi', 'item_id', 'tipe_transaksi', 'jumlah', 'harga_satuan', 'total_nilai', 'sumber', 'ref_unit_id', 'created_by_user_id', 'tanggal_transaksi', 'bukti_transaksi', 'keterangan']);
        $inventoryTransaction->update($validated);
        return $this->success($inventoryTransaction->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/inventory-transactions/{id}",
     *     tags={"Inventaris & Stok"},
     *     summary="Delete Inventaris & Stok",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
