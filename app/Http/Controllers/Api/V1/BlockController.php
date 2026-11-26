<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Block;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Blok & Kamar")
 */
class BlockController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/blocks",
     *     tags={"Blok & Kamar"},
     *     summary="List Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = Block::query()
            ->with(['rooms']);
        $result = $this->cachedPaginate($query, 'blocks', 5, ['kode_blok', 'nama_blok'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/blocks",
     *     tags={"Blok & Kamar"},
     *     summary="Create Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_blok","nama_blok","jenis_blok","kapasitas","unit_id"},
     *             @OA\Property(property="kode_blok", type="string", maxLength=20),
     *             @OA\Property(property="nama_blok", type="string", maxLength=100),
     *             @OA\Property(property="jenis_blok", type="string", enum={"umum"}),
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *             @OA\Property(property="unit_id", type="string"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['unit_id', 'kode_blok', 'nama_blok', 'jenis_blok', 'kapasitas', 'terisi', 'status', 'keterangan']);
        $block = Block::create($validated);
        return $this->success($block, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/blocks/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Get Blok & Kamar detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Block $block)
    {
        return $this->success($block);
    }
    /**
     * @OA\Put(
     *     path="/blocks/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Update Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_blok", type="string", maxLength=100),
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, Block $block)
    {
        $validated = $request->only(['unit_id', 'kode_blok', 'nama_blok', 'jenis_blok', 'kapasitas', 'terisi', 'status', 'keterangan']);
        $block->update($validated);
        return $this->success($block->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/blocks/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Delete Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Block $block)
    {
        $block->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
