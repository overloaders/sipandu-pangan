<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\CropType;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Manajemen Lahan")
 */
class CropTypeController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/crop-types",
     *     tags={"Manajemen Lahan"},
     *     summary="List Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = CropType::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'crop_types', 5, ['nama_tanaman'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/crop-types",
     *     tags={"Manajemen Lahan"},
     *     summary="Create Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_tanaman","nama_tanaman","kategori"},
     *             @OA\Property(property="kode_tanaman", type="string", maxLength=30),
     *             @OA\Property(property="nama_tanaman", type="string", maxLength=100),
     *             @OA\Property(property="nama_latin", type="string", maxLength=150, nullable=true),
     *             @OA\Property(property="kategori", type="string", enum={"pangan"}),
     *             @OA\Property(property="masa_tanam_hari", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_per_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="musim_tanam", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_tanaman', 'nama_tanaman', 'nama_latin', 'kategori', 'masa_tanam_hari', 'hasil_per_m2_kg', 'harga_jual_per_kg', 'musim_tanam', 'keterangan']);
        $cropType = CropType::create($validated);
        return $this->success($cropType, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/crop-types/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Get Manajemen Lahan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(CropType $cropType)
    {
        return $this->success($cropType);
    }
    /**
     * @OA\Put(
     *     path="/crop-types/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Update Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_tanaman", type="string", maxLength=100),
     *             @OA\Property(property="nama_latin", type="string", maxLength=150, nullable=true),
     *             @OA\Property(property="kategori", type="string", enum={"pangan"}),
     *             @OA\Property(property="masa_tanam_hari", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="harga_jual_per_kg", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="musim_tanam", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, CropType $cropType)
    {
        $validated = $request->only(['kode_tanaman', 'nama_tanaman', 'nama_latin', 'kategori', 'masa_tanam_hari', 'hasil_per_m2_kg', 'harga_jual_per_kg', 'musim_tanam', 'keterangan']);
        $cropType->update($validated);
        return $this->success($cropType->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/crop-types/{id}",
     *     tags={"Manajemen Lahan"},
     *     summary="Delete Manajemen Lahan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(CropType $cropType)
    {
        $cropType->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
