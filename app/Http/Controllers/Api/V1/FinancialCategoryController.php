<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FinancialCategory;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Keuangan")
 */
class FinancialCategoryController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/financial-categories",
     *     tags={"Keuangan"},
     *     summary="List Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = FinancialCategory::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'financialcategory.phps', 5, [], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/financial-categories",
     *     tags={"Keuangan"},
     *     summary="Create Keuangan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_kategori_keu","nama_kategori","tipe"},
     *             @OA\Property(property="kode_kategori_keu", type="string", maxLength=30),
     *             @OA\Property(property="nama_kategori", type="string", maxLength=100),
     *             @OA\Property(property="tipe", type="string", enum={"pemasukan"}),
     *             @OA\Property(property="parent_type", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="parent_id", type="string", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *             @OA\Property(property="is_active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_kategori_keu', 'nama_kategori', 'tipe', 'parent_type', 'parent_id', 'keterangan', 'is_active']);
        $financialCategory = FinancialCategory::create($validated);
        return $this->success($financialCategory, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/financial-categories/{id}",
     *     tags={"Keuangan"},
     *     summary="Get Keuangan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FinancialCategory $financialCategory)
    {
        return $this->success($financialCategory);
    }
    /**
     * @OA\Put(
     *     path="/financial-categories/{id}",
     *     tags={"Keuangan"},
     *     summary="Update Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_kategori", type="string", maxLength=100),
     *             @OA\Property(property="tipe", type="string", enum={"pemasukan"}),
     *             @OA\Property(property="parent_type", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="parent_id", type="string", nullable=true),
     *             @OA\Property(property="keterangan", type="string", nullable=true),
     *             @OA\Property(property="is_active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, FinancialCategory $financialCategory)
    {
        $validated = $request->only(['kode_kategori_keu', 'nama_kategori', 'tipe', 'parent_type', 'parent_id', 'keterangan', 'is_active']);
        $financialCategory->update($validated);
        return $this->success($financialCategory->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/financial-categories/{id}",
     *     tags={"Keuangan"},
     *     summary="Delete Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FinancialCategory $financialCategory)
    {
        $financialCategory->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
