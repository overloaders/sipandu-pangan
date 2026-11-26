<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Room;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Blok & Kamar")
 */
class RoomController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/blocks/{block}/rooms",
     *     tags={"Blok & Kamar"},
     *     summary="List Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = Room::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'room.phps', 5, [], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/blocks/{block}/rooms",
     *     tags={"Blok & Kamar"},
     *     summary="Create Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_kamar","nama_kamar","kapasitas"},
     *             @OA\Property(property="kode_kamar", type="string", maxLength=20),
     *             @OA\Property(property="nama_kamar", type="string", maxLength=100),
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['block_id', 'kode_kamar', 'nama_kamar', 'kapasitas', 'terisi', 'status', 'keterangan']);
        $room = Room::create($validated);
        return $this->success($room, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/blocks/{block}/rooms/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Get Blok & Kamar detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Room $room)
    {
        return $this->success($room);
    }
    /**
     * @OA\Put(
     *     path="/blocks/{block}/rooms/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Update Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="kapasitas", type="integer", enum={"1"}),
     *             @OA\Property(property="status", type="string", enum={"tersedia"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, Room $room)
    {
        $validated = $request->only(['block_id', 'kode_kamar', 'nama_kamar', 'kapasitas', 'terisi', 'status', 'keterangan']);
        $room->update($validated);
        return $this->success($room->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/blocks/{block}/rooms/{id}",
     *     tags={"Blok & Kamar"},
     *     summary="Delete Blok & Kamar",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
