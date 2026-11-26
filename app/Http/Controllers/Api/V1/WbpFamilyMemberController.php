<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\WbpFamilyMember;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Registrasi WBP")
 */
class WbpFamilyMemberController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/wbp/{wbp}/family-members",
     *     tags={"Registrasi WBP"},
     *     summary="List Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $familyMembers = WbpFamilyMember::query()
            ->with(['wbp'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($familyMembers);
    }
    /**
     * @OA\Post(
     *     path="/wbp/{wbp}/family-members",
     *     tags={"Registrasi WBP"},
     *     summary="Create Registrasi WBP",
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
        $validated = $request->only(['wbp_id', 'nama_lengkap', 'hubungan', 'nik', 'nomor_hp', 'alamat', 'is_wali']);
        $familyMember = WbpFamilyMember::create($validated);
        return $this->success($familyMember, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/wbp/{wbp}/family-members/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Get Registrasi WBP detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(WbpFamilyMember $familyMember)
    {
        $familyMember->load(['wbp']);
        return $this->success($familyMember);
    }
    /**
     * @OA\Put(
     *     path="/wbp/{wbp}/family-members/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Update Registrasi WBP",
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
    public function update(Request $request, WbpFamilyMember $familyMember)
    {
        $validated = $request->only(['wbp_id', 'nama_lengkap', 'hubungan', 'nik', 'nomor_hp', 'alamat', 'is_wali']);
        $familyMember->update($validated);
        return $this->success($familyMember->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/wbp/{wbp}/family-members/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Delete Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(WbpFamilyMember $familyMember)
    {
        $familyMember->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
