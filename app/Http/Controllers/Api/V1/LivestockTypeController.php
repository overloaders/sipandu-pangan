<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\LivestockType;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Peternakan")
 */
class LivestockTypeController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/livestock-types",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = LivestockType::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'livestocktype.phps', 5, [], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/livestock-types",
     *     tags={"Peternakan"},
     *     summary="Create Peternakan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_jenis","nama_jenis","kategori"},
     *             @OA\Property(property="kode_jenis", type="string", maxLength=30),
     *             @OA\Property(property="nama_jenis", type="string", maxLength=100),
     *             @OA\Property(property="kategori", type="string", enum={"unggas"}),
     *             @OA\Property(property="berat_rata_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_beli_per_ekor", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_per_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="masa_pemeliharaan_hari", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="pakan_utama", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_jenis', 'nama_jenis', 'kategori', 'berat_rata_kg', 'harga_beli_per_ekor', 'harga_jual_per_kg', 'masa_pemeliharaan_hari', 'pakan_utama', 'keterangan']);
        $livestockType = LivestockType::create($validated);
        return $this->success($livestockType, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/livestock-types/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LivestockType $livestockType)
    {
        return $this->success($livestockType);
    }
    /**
     * @OA\Put(
     *     path="/livestock-types/{id}",
     *     tags={"Peternakan"},
     *     summary="Update Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_jenis", type="string", maxLength=100),
     *             @OA\Property(property="kategori", type="string", enum={"unggas"}),
     *             @OA\Property(property="berat_rata_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_beli_per_ekor", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_per_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="masa_pemeliharaan_hari", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="pakan_utama", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, LivestockType $livestockType)
    {
        $validated = $request->only(['kode_jenis', 'nama_jenis', 'kategori', 'berat_rata_kg', 'harga_beli_per_ekor', 'harga_jual_per_kg', 'masa_pemeliharaan_hari', 'pakan_utama', 'keterangan']);
        $livestockType->update($validated);
        return $this->success($livestockType->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/livestock-types/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(LivestockType $livestockType)
    {
        $livestockType->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
