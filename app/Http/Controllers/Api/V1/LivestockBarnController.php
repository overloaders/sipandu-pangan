<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\LivestockBarn;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Peternakan")
 */
class LivestockBarnController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/livestock-barns",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = LivestockBarn::query()
            ->with(['unit', 'inventories.livestockType']);
        $result = $this->cachedPaginate($query, 'livestock_barns', 5, ['kode_kandang', 'nama_kandang'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/livestock-barns",
     *     tags={"Peternakan"},
     *     summary="Create Peternakan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_kandang","nama_kandang","unit_id","jenis_ternak","kapasitas","tipe_kandang"},
     *             @OA\Property(property="kode_kandang", type="string", maxLength=30),
     *             @OA\Property(property="nama_kandang", type="string", maxLength=150),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="jenis_ternak", type="string", enum={"ayam"}),
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *             @OA\Property(property="tipe_kandang", type="string", enum={"kandang_baterai"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_kandang', 'nama_kandang', 'unit_id', 'jenis_ternak', 'kapasitas', 'terisi', 'luas_m2', 'tipe_kandang', 'status', 'keterangan']);
        $livestockBarn = LivestockBarn::create($validated);
        return $this->success($livestockBarn, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/livestock-barns/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LivestockBarn $livestockBarn)
    {
        return $this->success($livestockBarn);
    }
    /**
     * @OA\Put(
     *     path="/livestock-barns/{id}",
     *     tags={"Peternakan"},
     *     summary="Update Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kandang", type="string", maxLength=150),
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, LivestockBarn $livestockBarn)
    {
        $validated = $request->only(['kode_kandang', 'nama_kandang', 'unit_id', 'jenis_ternak', 'kapasitas', 'terisi', 'luas_m2', 'tipe_kandang', 'status', 'keterangan']);
        $livestockBarn->update($validated);
        return $this->success($livestockBarn->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/livestock-barns/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(LivestockBarn $livestockBarn)
    {
        $livestockBarn->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
