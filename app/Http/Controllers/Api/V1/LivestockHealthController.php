<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Models\LivestockHealthRecord;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Peternakan")
 */
class LivestockHealthController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/livestock-health-records",
     *     tags={"Peternakan"},
     *     summary="List Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $livestockHealthRecords = LivestockHealthRecord::query()
            ->with(['barn', 'picWbp', 'details'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->success($livestockHealthRecords);
    }

    /**
     * @OA\Post(
     *     path="/livestock-health-records",
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
        $validated = $request->validate([]);
        $livestockHealthRecord = LivestockHealthRecord::create($validated);

        return $this->success($livestockHealthRecord, 'Data berhasil ditambahkan', 201);
    }

    /**
     * @OA\Get(
     *     path="/livestock-health-records/{id}",
     *     tags={"Peternakan"},
     *     summary="Get Peternakan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LivestockHealthRecord $livestockHealthRecord)
    {
        $livestockHealthRecord->load(['barn', 'picWbp', 'details']);
        return $this->success($livestockHealthRecord);
    }

    /**
     * @OA\Put(
     *     path="/livestock-health-records/{id}",
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
    public function update(Request $request, LivestockHealthRecord $livestockHealthRecord)
    {
        $validated = $request->validate([]);
        $livestockHealthRecord->update($validated);

        return $this->success($livestockHealthRecord->refresh(), 'Data berhasil diperbarui');
    }

    /**
     * @OA\Delete(
     *     path="/livestock-health-records/{id}",
     *     tags={"Peternakan"},
     *     summary="Delete Peternakan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(LivestockHealthRecord $livestockHealthRecord)
    {
        $livestockHealthRecord->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
