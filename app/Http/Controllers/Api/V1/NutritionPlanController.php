<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\NutritionPlan;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Nutrisi & Menu")
 */
class NutritionPlanController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/nutrition-plans",
     *     tags={"Nutrisi & Menu"},
     *     summary="List Nutrisi & Menu",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = NutritionPlan::query()
            ->with(['components.item']);
        $result = $this->cachedPaginate($query, 'nutrition_plans', 5, ['kode_menu', 'nama_menu'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/nutrition-plans",
     *     tags={"Nutrisi & Menu"},
     *     summary="Create Nutrisi & Menu",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_rencana_gizi","nama_menu","unit_id","waktu_makan","tipe_porsi","status"},
     *             @OA\Property(property="kode_rencana_gizi", type="string", maxLength=30),
     *             @OA\Property(property="nama_menu", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="waktu_makan", type="string", maxLength=50),
     *             @OA\Property(property="berlaku_mulai", type="string", format="date", nullable=true),
     *             @OA\Property(property="berlaku_selesai", type="string", format="date", nullable=true),
     *             @OA\Property(property="kalori_target_kcal", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="protein_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="karbohidrat_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="lemak_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="serat_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="tipe_porsi", type="string", maxLength=50),
     *             @OA\Property(property="estimasi_biaya_per_porsi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="sumber_bahan", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_rencana_gizi', 'nama_menu', 'unit_id', 'waktu_makan', 'berlaku_mulai', 'berlaku_selesai', 'kalori_target_kcal', 'protein_target_gr', 'karbohidrat_target_gr', 'lemak_target_gr', 'serat_target_gr', 'tipe_porsi', 'estimasi_biaya_per_porsi', 'sumber_bahan', 'deskripsi', 'status']);
        $nutritionPlan = NutritionPlan::create($validated);
        return $this->success($nutritionPlan, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/nutrition-plans/{id}",
     *     tags={"Nutrisi & Menu"},
     *     summary="Get Nutrisi & Menu detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(NutritionPlan $nutritionPlan)
    {
        return $this->success($nutritionPlan);
    }
    /**
     * @OA\Put(
     *     path="/nutrition-plans/{id}",
     *     tags={"Nutrisi & Menu"},
     *     summary="Update Nutrisi & Menu",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_menu", type="string", maxLength=200),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="waktu_makan", type="string", maxLength=50),
     *             @OA\Property(property="berlaku_mulai", type="string", format="date", nullable=true),
     *             @OA\Property(property="berlaku_selesai", type="string", format="date", nullable=true),
     *             @OA\Property(property="kalori_target_kcal", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="protein_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="karbohidrat_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="lemak_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="serat_target_gr", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="tipe_porsi", type="string", maxLength=50),
     *             @OA\Property(property="estimasi_biaya_per_porsi", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="sumber_bahan", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, NutritionPlan $nutritionPlan)
    {
        $validated = $request->only(['kode_rencana_gizi', 'nama_menu', 'unit_id', 'waktu_makan', 'berlaku_mulai', 'berlaku_selesai', 'kalori_target_kcal', 'protein_target_gr', 'karbohidrat_target_gr', 'lemak_target_gr', 'serat_target_gr', 'tipe_porsi', 'estimasi_biaya_per_porsi', 'sumber_bahan', 'deskripsi', 'status']);
        $nutritionPlan->update($validated);
        return $this->success($nutritionPlan->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/nutrition-plans/{id}",
     *     tags={"Nutrisi & Menu"},
     *     summary="Delete Nutrisi & Menu",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(NutritionPlan $nutritionPlan)
    {
        $nutritionPlan->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
