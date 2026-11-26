<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Budget;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Keuangan")
 */
class BudgetController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/budgets",
     *     tags={"Keuangan"},
     *     summary="List Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $budgets = Budget::query()
            ->with(['foodProgram', 'category', 'unit'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($budgets);
    }
    /**
     * @OA\Post(
     *     path="/budgets",
     *     tags={"Keuangan"},
     *     summary="Create Keuangan",
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
        $validated = $request->only(['kode_anggaran', 'food_program_id', 'category_id', 'unit_id', 'uraian', 'anggaran_dialokasi', 'realisasi', 'sisa', 'persentase_realisasi', 'periode', 'tahun', 'status']);
        $budget = Budget::create($validated);
        return $this->success($budget, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/budgets/{id}",
     *     tags={"Keuangan"},
     *     summary="Get Keuangan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Budget $budget)
    {
        $budget->load(['foodProgram', 'category', 'unit']);
        return $this->success($budget);
    }
    /**
     * @OA\Put(
     *     path="/budgets/{id}",
     *     tags={"Keuangan"},
     *     summary="Update Keuangan",
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
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->only(['kode_anggaran', 'food_program_id', 'category_id', 'unit_id', 'uraian', 'anggaran_dialokasi', 'realisasi', 'sisa', 'persentase_realisasi', 'periode', 'tahun', 'status']);
        $budget->update($validated);
        return $this->success($budget->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/budgets/{id}",
     *     tags={"Keuangan"},
     *     summary="Delete Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
