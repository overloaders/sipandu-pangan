<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FeedLog;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Peternakan")
 */
class FeedLogController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/feed-logs",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $feedLogs = FeedLog::query()
            ->with(['barn', 'feedSupply', 'wbp'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($feedLogs);
    }
    /**
     * @OA\Post(
     *     path="/feed-logs",
     *     tags={"Peternakan"},
     *     summary="Create Peternakan",
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
        $validated = $request->only(['barn_id', 'feed_supply_id', 'wbp_id', 'tanggal_pemberian', 'waktu_pemberian', 'jumlah', 'satuan', 'biaya', 'keterangan']);
        $feedLog = FeedLog::create($validated);
        return $this->success($feedLog, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/feed-logs/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FeedLog $feedLog)
    {
        $feedLog->load(['barn', 'feedSupply', 'wbp']);
        return $this->success($feedLog);
    }
    /**
     * @OA\Put(
     *     path="/feed-logs/{id}",
     *     tags={"Peternakan"},
     *     summary="Update Peternakan",
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
    public function update(Request $request, FeedLog $feedLog)
    {
        $validated = $request->only(['barn_id', 'feed_supply_id', 'wbp_id', 'tanggal_pemberian', 'waktu_pemberian', 'jumlah', 'satuan', 'biaya', 'keterangan']);
        $feedLog->update($validated);
        return $this->success($feedLog->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/feed-logs/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FeedLog $feedLog)
    {
        $feedLog->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
