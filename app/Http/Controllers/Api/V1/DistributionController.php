<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Distribution;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Distribusi")
 */
class DistributionController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/distributions",
     *     tags={"Distribusi"},
     *     summary="List Distribusi",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = Distribution::query()
            ->with(['foodProgram', 'items.item', 'creator']);
        $result = $this->cachedPaginate($query, 'distributions', 5, ['kode_distribusi'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/distributions",
     *     tags={"Distribusi"},
     *     summary="Create Distribusi",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_distribusi","tipe_distribusi","unit_id","tanggal_distribusi","penerima_nama","status"},
     *             @OA\Property(property="kode_distribusi", type="string", maxLength=30),
     *             @OA\Property(property="tipe_distribusi", type="string", maxLength=50),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_distribusi", type="string", format="date"),
     *             @OA\Property(property="total_nilai", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="jumlah_penerima", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="penerima_nama", type="string", maxLength=200),
     *             @OA\Property(property="penerima_kontak", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="alamat_penerima", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"direncanakan"}),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *             @OA\Property(property="items", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_distribusi', 'tipe_distribusi', 'unit_id', 'tanggal_distribusi', 'total_nilai', 'jumlah_penerima', 'penerima_nama', 'penerima_kontak', 'alamat_penerima', 'status', 'created_by_user_id', 'keterangan']);
        $distribution = Distribution::create($validated);
        return $this->success($distribution, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/distributions/{id}",
     *     tags={"Distribusi"},
     *     summary="Get Distribusi detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Distribution $distribution)
    {
        return $this->success($distribution);
    }
    /**
     * @OA\Put(
     *     path="/distributions/{id}",
     *     tags={"Distribusi"},
     *     summary="Update Distribusi",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tipe_distribusi", type="string", maxLength=50),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_distribusi", type="string", format="date"),
     *             @OA\Property(property="total_nilai", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="jumlah_penerima", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="penerima_nama", type="string", maxLength=200),
     *             @OA\Property(property="penerima_kontak", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="alamat_penerima", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"direncanakan"}),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, Distribution $distribution)
    {
        $validated = $request->only(['kode_distribusi', 'tipe_distribusi', 'unit_id', 'tanggal_distribusi', 'total_nilai', 'jumlah_penerima', 'penerima_nama', 'penerima_kontak', 'alamat_penerima', 'status', 'created_by_user_id', 'keterangan']);
        $distribution->update($validated);
        return $this->success($distribution->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/distributions/{id}",
     *     tags={"Distribusi"},
     *     summary="Delete Distribusi",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Distribution $distribution)
    {
        $distribution->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
