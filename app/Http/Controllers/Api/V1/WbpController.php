<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Wbp;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Registrasi WBP")
 */
class WbpController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/wbp",
     *     tags={"Registrasi WBP"},
     *     summary="List Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = Wbp::query()
            ->with(['unit', 'block', 'room']);
        $result = $this->cachedPaginate($query, 'wbp', 5, ['nomor_induk', 'nama_lengkap', 'nik'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/wbp",
     *     tags={"Registrasi WBP"},
     *     summary="Create Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nomor_induk","nama_lengkap","jenis_kelamin","tanggal_lahir","tempat_lahir","nik","agama","tanggal_masuk","masa_hukuman_hari","unit_id"},
     *             @OA\Property(property="nomor_induk", type="string", maxLength=30),
     *             @OA\Property(property="nama_lengkap", type="string", maxLength=150),
     *             @OA\Property(property="jenis_kelamin", type="string", enum={"l"}),
     *             @OA\Property(property="tanggal_lahir", type="string", format="date"),
     *             @OA\Property(property="tempat_lahir", type="string", maxLength=100),
     *             @OA\Property(property="nik", type="string", maxLength=20),
     *             @OA\Property(property="agama", type="string", maxLength=20),
     *             @OA\Property(property="tanggal_masuk", type="string", format="date"),
     *             @OA\Property(property="masa_hukuman_hari", type="integer", enum={"1"}),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="block_id", type="string", nullable=true),
     *             @OA\Property(property="room_id", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['nomor_induk', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nik', 'agama', 'kebangsaan', 'pendidikan_terakhir', 'pekerjaan_sebelum', 'alamat_asal', 'kota_asal', 'provinsi_asal', 'nomor_hp', 'foto', 'sidik_jari', 'status', 'jenis_pidana', 'kasus', 'tanggal_masuk', 'tanggal_beberapa', 'masa_hukuman_selesai', 'masa_hukuman_hari', 'remisi_hari', 'nomor_register_a', 'nomor_register_b', 'nomor_register_c', 'nomor_register_d', 'nomor_register_e', 'nomor_register_f', 'nomor_register_g', 'nomor_register_h', 'unit_id', 'block_id', 'room_id', 'tamping', 'kelompok_masa_tahanan', 'keterangan']);
        $wbp = Wbp::create($validated);
        return $this->success($wbp, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/wbp/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Get Registrasi WBP detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Wbp $wbp)
    {
        return $this->success($wbp);
    }
    /**
     * @OA\Put(
     *     path="/wbp/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Update Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_lengkap", type="string", maxLength=150),
     *             @OA\Property(property="status", type="string", enum={"aktif"}),
     *             @OA\Property(property="block_id", type="string", nullable=true),
     *             @OA\Property(property="room_id", type="string", nullable=true),
     *             @OA\Property(property="keterangan", type="string", maxLength=500, nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, Wbp $wbp)
    {
        $validated = $request->only(['nomor_induk', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nik', 'agama', 'kebangsaan', 'pendidikan_terakhir', 'pekerjaan_sebelum', 'alamat_asal', 'kota_asal', 'provinsi_asal', 'nomor_hp', 'foto', 'sidik_jari', 'status', 'jenis_pidana', 'kasus', 'tanggal_masuk', 'tanggal_beberapa', 'masa_hukuman_selesai', 'masa_hukuman_hari', 'remisi_hari', 'nomor_register_a', 'nomor_register_b', 'nomor_register_c', 'nomor_register_d', 'nomor_register_e', 'nomor_register_f', 'nomor_register_g', 'nomor_register_h', 'unit_id', 'block_id', 'room_id', 'tamping', 'kelompok_masa_tahanan', 'keterangan']);
        $wbp->update($validated);
        return $this->success($wbp->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/wbp/{id}",
     *     tags={"Registrasi WBP"},
     *     summary="Delete Registrasi WBP",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Wbp $wbp)
    {
        $wbp->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
