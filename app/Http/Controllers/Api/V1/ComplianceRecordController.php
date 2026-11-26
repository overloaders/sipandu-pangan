<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\ComplianceRecord;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Sertifikasi & Komplians")
 */
class ComplianceRecordController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/compliance-records",
     *     tags={"Sertifikasi & Komplians"},
     *     summary="List Sertifikasi & Komplians",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = ComplianceRecord::query()
            ->with(['details', 'inspektur']);
        $result = $this->cachedPaginate($query, 'compliance_records', 5, ['kode_komplians'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/compliance-records",
     *     tags={"Sertifikasi & Komplians"},
     *     summary="Create Sertifikasi & Komplians",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_komplians","judul","tipe","unit_id","tanggal_pemeriksaan","status"},
     *             @OA\Property(property="kode_komplians", type="string", maxLength=30),
     *             @OA\Property(property="judul", type="string", maxLength=200),
     *             @OA\Property(property="tipe", type="string", maxLength=100),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_pemeriksaan", type="string", format="date"),
     *             @OA\Property(property="tanggal_berlaku_sampai", type="string", format="date", nullable=true),
     *             @OA\Property(property="hasil", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="nilai_skor", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="tingkat_risiko", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="inspektur_user_id", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *             @OA\Property(property="temuan", type="string", nullable=true),
     *             @OA\Property(property="rekomendasi", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_komplians', 'judul', 'tipe', 'unit_id', 'tanggal_pemeriksaan', 'tanggal_berlaku_sampai', 'hasil', 'nilai_skor', 'tingkat_risiko', 'inspektur_user_id', 'status', 'temuan', 'rekomendasi', 'dokumen_pendukung']);
        $complianceRecord = ComplianceRecord::create($validated);
        return $this->success($complianceRecord, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/compliance-records/{id}",
     *     tags={"Sertifikasi & Komplians"},
     *     summary="Get Sertifikasi & Komplians detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(ComplianceRecord $complianceRecord)
    {
        return $this->success($complianceRecord);
    }
    /**
     * @OA\Put(
     *     path="/compliance-records/{id}",
     *     tags={"Sertifikasi & Komplians"},
     *     summary="Update Sertifikasi & Komplians",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="judul", type="string", maxLength=200),
     *             @OA\Property(property="tipe", type="string", maxLength=100),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_pemeriksaan", type="string", format="date"),
     *             @OA\Property(property="tanggal_berlaku_sampai", type="string", format="date", nullable=true),
     *             @OA\Property(property="hasil", type="string", maxLength=100, nullable=true),
     *             @OA\Property(property="nilai_skor", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="tingkat_risiko", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="inspektur_user_id", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *             @OA\Property(property="temuan", type="string", nullable=true),
     *             @OA\Property(property="rekomendasi", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, ComplianceRecord $complianceRecord)
    {
        $validated = $request->only(['kode_komplians', 'judul', 'tipe', 'unit_id', 'tanggal_pemeriksaan', 'tanggal_berlaku_sampai', 'hasil', 'nilai_skor', 'tingkat_risiko', 'inspektur_user_id', 'status', 'temuan', 'rekomendasi', 'dokumen_pendukung']);
        $complianceRecord->update($validated);
        return $this->success($complianceRecord->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/compliance-records/{id}",
     *     tags={"Sertifikasi & Komplians"},
     *     summary="Delete Sertifikasi & Komplians",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(ComplianceRecord $complianceRecord)
    {
        $complianceRecord->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
