<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FarmingPlan;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Rencana Kerja Pertanian")
 */
class FarmingPlanController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/farming-plans",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="List Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = FarmingPlan::query()
            ->with(['landPlot', 'cropType', 'foodProgram']);
        $result = $this->cachedPaginate($query, 'farming_plans', 5, ['kode_rencana'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/farming-plans",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Create Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_rencana","food_program_id","land_plot_id","crop_type_id","jumlah_tanaman","tanggal_tanam_rencana","metode_budi_daya"},
     *             @OA\Property(property="kode_rencana", type="string", maxLength=30),
     *             @OA\Property(property="food_program_id", type="string"),
     *             @OA\Property(property="land_plot_id", type="string"),
     *             @OA\Property(property="crop_type_id", type="string"),
     *             @OA\Property(property="jumlah_tanaman", type="integer", enum={"1"}),
     *             @OA\Property(property="tanggal_tanam_rencana", type="string", format="date"),
     *             @OA\Property(property="metode_budi_daya", type="string", enum={"konvensional"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_rencana', 'food_program_id', 'land_plot_id', 'crop_type_id', 'jumlah_tanaman', 'tanggal_tanam_rencana', 'tanggal_tanam_aktual', 'tanggal_panen_rencana', 'tanggal_panen_aktual', 'luas_tanam_m2', 'metode_budi_daya', 'status', 'estimasi_hasil_kg', 'estimasi_pendapatan', 'keterangan']);
        $farmingPlan = FarmingPlan::create($validated);
        return $this->success($farmingPlan, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/farming-plans/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Get Rencana Kerja Pertanian detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FarmingPlan $farmingPlan)
    {
        return $this->success($farmingPlan);
    }
    /**
     * @OA\Put(
     *     path="/farming-plans/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Update Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"direncanakan"}),
     *             @OA\Property(property="tanggal_tanam_aktual", type="string", format="date", nullable=true),
     *             @OA\Property(property="keterangan", type="string", maxLength=500, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, FarmingPlan $farmingPlan)
    {
        $validated = $request->only(['kode_rencana', 'food_program_id', 'land_plot_id', 'crop_type_id', 'jumlah_tanaman', 'tanggal_tanam_rencana', 'tanggal_tanam_aktual', 'tanggal_panen_rencana', 'tanggal_panen_aktual', 'luas_tanam_m2', 'metode_budi_daya', 'status', 'estimasi_hasil_kg', 'estimasi_pendapatan', 'keterangan']);
        $farmingPlan->update($validated);
        return $this->success($farmingPlan->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/farming-plans/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Delete Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FarmingPlan $farmingPlan)
    {
        $farmingPlan->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
