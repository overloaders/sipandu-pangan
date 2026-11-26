<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FarmingActivity;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Rencana Kerja Pertanian")
 */
class FarmingActivityController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/farming-plans/{farmingPlan}/activities",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="List Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $activities = FarmingActivity::query()
            ->with(['picWbp'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($activities);
    }
    /**
     * @OA\Post(
     *     path="/farming-plans/{farmingPlan}/activities",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Create Rencana Kerja Pertanian",
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
        $validated = $request->only(['farming_plan_id', 'jenis_kegiatan', 'tanggal_kegiatan', 'pic_wbp_id', 'biaya', 'keterangan', 'foto']);
        $activity = FarmingActivity::create($validated);
        return $this->success($activity, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/farming-plans/{farmingPlan}/activities/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Get Rencana Kerja Pertanian detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FarmingActivity $activity)
    {
        return $this->success($activity);
    }
    /**
     * @OA\Put(
     *     path="/farming-plans/{farmingPlan}/activities/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Update Rencana Kerja Pertanian",
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
    public function update(Request $request, FarmingActivity $activity)
    {
        $validated = $request->only(['farming_plan_id', 'jenis_kegiatan', 'tanggal_kegiatan', 'pic_wbp_id', 'biaya', 'keterangan', 'foto']);
        $activity->update($validated);
        return $this->success($activity->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/farming-plans/{farmingPlan}/activities/{id}",
     *     tags={"Rencana Kerja Pertanian"},
     *     summary="Delete Rencana Kerja Pertanian",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FarmingActivity $activity)
    {
        $activity->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
