<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FoodProgram;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Program Ketahanan Pangan")
 */
class FoodProgramController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/food-programs",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="List Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = FoodProgram::query()
            ->with(['unit', 'participants']);
        $result = $this->cachedPaginate($query, 'food_programs', 5, ['kode_program', 'nama_program'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/food-programs",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Create Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_program","nama_program","jenis_program","unit_id","tanggal_mulai","anggaran","target_peserta"},
     *             @OA\Property(property="kode_program", type="string", maxLength=30),
     *             @OA\Property(property="nama_program", type="string", maxLength=200),
     *             @OA\Property(property="jenis_program", type="string", enum={"pertanian"}),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_mulai", type="string", format="date"),
     *             @OA\Property(property="anggaran", type="integer", enum={"0"}),
     *             @OA\Property(property="target_peserta", type="integer", enum={"1"}),
     *             @OA\Property(property="deskripsi", type="string", maxLength=1000, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_program', 'nama_program', 'deskripsi', 'jenis_program', 'status', 'tanggal_mulai', 'tanggal_rencana_selesai', 'tanggal_selesai', 'unit_id', 'created_by', 'anggaran', 'realisasi_anggaran', 'target_peserta', 'keterangan']);
        $foodProgram = FoodProgram::create($validated);
        return $this->success($foodProgram, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/food-programs/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Get Program Ketahanan Pangan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FoodProgram $foodProgram)
    {
        return $this->success($foodProgram);
    }
    /**
     * @OA\Put(
     *     path="/food-programs/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Update Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_program", type="string", maxLength=200),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *             @OA\Property(property="deskripsi", type="string", maxLength=1000, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, FoodProgram $foodProgram)
    {
        $validated = $request->only(['kode_program', 'nama_program', 'deskripsi', 'jenis_program', 'status', 'tanggal_mulai', 'tanggal_rencana_selesai', 'tanggal_selesai', 'unit_id', 'created_by', 'anggaran', 'realisasi_anggaran', 'target_peserta', 'keterangan']);
        $foodProgram->update($validated);
        return $this->success($foodProgram->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/food-programs/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Delete Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FoodProgram $foodProgram)
    {
        $foodProgram->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
