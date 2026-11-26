<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\ProcessingBatch;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Pengolahan")
 */
class ProcessingBatchController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/processing-batches",
     *     tags={"Pengolahan"},
     *     summary="List Pengolahan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = ProcessingBatch::query()
            ->with(['items.inventoryItem', 'picWbp']);
        $result = $this->cachedPaginate($query, 'processing_batches', 5, ['kode_batch'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/processing-batches",
     *     tags={"Pengolahan"},
     *     summary="Create Pengolahan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_batch","nama_produk","unit_id","jenis_pengolahan","tanggal_produksi","status"},
     *             @OA\Property(property="kode_batch", type="string", maxLength=30),
     *             @OA\Property(property="nama_produk", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="jenis_pengolahan", type="string", maxLength=100),
     *             @OA\Property(property="tanggal_produksi", type="string", format="date"),
     *             @OA\Property(property="tanggal_kadaluarsa", type="string", format="date", nullable=true),
     *             @OA\Property(property="jumlah_produksi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="satuan_output", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="biaya_produksi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_satuan", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"direncanakan"}),
     *             @OA\Property(property="pic_wbp_id", type="string", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_batch', 'nama_produk', 'unit_id', 'jenis_pengolahan', 'tanggal_produksi', 'tanggal_kadaluarsa', 'jumlah_produksi', 'satuan_output', 'biaya_produksi', 'harga_jual_satuan', 'status', 'pic_wbp_id', 'keterangan']);
        $processingBatch = ProcessingBatch::create($validated);
        return $this->success($processingBatch, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/processing-batches/{id}",
     *     tags={"Pengolahan"},
     *     summary="Get Pengolahan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(ProcessingBatch $processingBatch)
    {
        return $this->success($processingBatch);
    }
    /**
     * @OA\Put(
     *     path="/processing-batches/{id}",
     *     tags={"Pengolahan"},
     *     summary="Update Pengolahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_produk", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="jenis_pengolahan", type="string", maxLength=100),
     *             @OA\Property(property="tanggal_produksi", type="string", format="date"),
     *             @OA\Property(property="tanggal_kadaluarsa", type="string", format="date", nullable=true),
     *             @OA\Property(property="jumlah_produksi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="satuan_output", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="biaya_produksi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_satuan", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"direncanakan"}),
     *             @OA\Property(property="pic_wbp_id", type="string", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, ProcessingBatch $processingBatch)
    {
        $validated = $request->only(['kode_batch', 'nama_produk', 'unit_id', 'jenis_pengolahan', 'tanggal_produksi', 'tanggal_kadaluarsa', 'jumlah_produksi', 'satuan_output', 'biaya_produksi', 'harga_jual_satuan', 'status', 'pic_wbp_id', 'keterangan']);
        $processingBatch->update($validated);
        return $this->success($processingBatch->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/processing-batches/{id}",
     *     tags={"Pengolahan"},
     *     summary="Delete Pengolahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(ProcessingBatch $processingBatch)
    {
        $processingBatch->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
