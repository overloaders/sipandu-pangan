<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Models\WaterQualityRecord;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Perikanan")
 */
class WaterQualityController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/water-quality-records",
     *     tags={"Perikanan"},
     *     summary="List Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $waterQualityRecords = WaterQualityRecord::query()
            ->with(['pond'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->success($waterQualityRecords);
    }

    /**
     * @OA\Post(
     *     path="/water-quality-records",
     *     tags={"Perikanan"},
     *     summary="Create Perikanan",
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
        $waterQualityRecord = WaterQualityRecord::create($validated);

        return $this->success($waterQualityRecord, 'Data berhasil ditambahkan', 201);
    }

    /**
     * @OA\Get(
     *     path="/water-quality-records/{id}",
     *     tags={"Perikanan"},
     *     summary="Get Perikanan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(WaterQualityRecord $waterQualityRecord)
    {
        $waterQualityRecord->load(['pond']);
        return $this->success($waterQualityRecord);
    }

    /**
     * @OA\Put(
     *     path="/water-quality-records/{id}",
     *     tags={"Perikanan"},
     *     summary="Update Perikanan",
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
    public function update(Request $request, WaterQualityRecord $waterQualityRecord)
    {
        $validated = $request->validate([]);
        $waterQualityRecord->update($validated);

        return $this->success($waterQualityRecord->refresh(), 'Data berhasil diperbarui');
    }

    /**
     * @OA\Delete(
     *     path="/water-quality-records/{id}",
     *     tags={"Perikanan"},
     *     summary="Delete Perikanan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(WaterQualityRecord $waterQualityRecord)
    {
        $waterQualityRecord->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
