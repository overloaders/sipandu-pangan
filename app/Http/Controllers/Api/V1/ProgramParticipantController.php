<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\ProgramParticipant;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Program Ketahanan Pangan")
 */
class ProgramParticipantController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/food-programs/{foodProgram}/participants",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="List Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $participants = ProgramParticipant::query()
            ->with(['wbp'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($participants);
    }
    /**
     * @OA\Post(
     *     path="/food-programs/{foodProgram}/participants",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Create Program Ketahanan Pangan",
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
        $validated = $request->only(['food_program_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai', 'status', 'peran', 'keterangan']);
        $participant = ProgramParticipant::create($validated);
        return $this->success($participant, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/food-programs/{foodProgram}/participants/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Get Program Ketahanan Pangan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(ProgramParticipant $participant)
    {
        return $this->success($participant);
    }
    /**
     * @OA\Put(
     *     path="/food-programs/{foodProgram}/participants/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Update Program Ketahanan Pangan",
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
    public function update(Request $request, ProgramParticipant $participant)
    {
        $validated = $request->only(['food_program_id', 'wbp_id', 'tanggal_mulai', 'tanggal_selesai', 'status', 'peran', 'keterangan']);
        $participant->update($validated);
        return $this->success($participant->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/food-programs/{foodProgram}/participants/{id}",
     *     tags={"Program Ketahanan Pangan"},
     *     summary="Delete Program Ketahanan Pangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(ProgramParticipant $participant)
    {
        $participant->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
