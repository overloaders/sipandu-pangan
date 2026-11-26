<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\WbpDocument;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Registrasi WBP")
 */
class WbpDocumentController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/wbp/{wbp}/documents",
     *     tags={"Registrasi WBP"},
     *     summary="List Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $documents = WbpDocument::query()
            ->with(['wbp'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($documents);
    }
    /**
     * @OA\Post(
     *     path="/wbp/{wbp}/documents",
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
        $validated = $request->only(['wbp_id', 'jenis_dokumen', 'nomor_dokumen', 'nama_dokumen', 'file_path', 'tanggal_dokumen', 'keterangan']);
        $document = WbpDocument::create($validated);
        return $this->success($document, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/wbp/{wbp}/documents/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Get Registrasi WBP detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(WbpDocument $document)
    {
        $document->load(['wbp']);
        return $this->success($document);
    }
    /**
     * @OA\Put(
     *     path="/wbp/{wbp}/documents/{id}",
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
    public function update(Request $request, WbpDocument $document)
    {
        $validated = $request->only(['wbp_id', 'jenis_dokumen', 'nomor_dokumen', 'nama_dokumen', 'file_path', 'tanggal_dokumen', 'keterangan']);
        $document->update($validated);
        return $this->success($document->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/wbp/{wbp}/documents/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Delete Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(WbpDocument $document)
    {
        $document->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
