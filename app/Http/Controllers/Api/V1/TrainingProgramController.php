<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Pelatihan & Pembinaan")
 */
class TrainingProgramController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/training-programs",
     *     tags={"Pelatihan & Pembinaan"},
     *     summary="List Pelatihan & Pembinaan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = TrainingProgram::query()
            ->with(['unit', 'participants.wbp']);
        $result = $this->cachedPaginate($query, 'training_programs', 5, ['kode_pelatihan', 'nama_pelatihan'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/training-programs",
     *     tags={"Pelatihan & Pembinaan"},
     *     summary="Create Pelatihan & Pembinaan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"kode_pelatihan","nama_pelatihan","kategori","unit_id","tanggal_mulai","status"},
     *             @OA\Property(property="kode_pelatihan", type="string", maxLength=30),
     *             @OA\Property(property="nama_pelatihan", type="string", maxLength=200),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="kategori", type="string", maxLength=50),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="instructor_user_id", type="string", nullable=true),
     *             @OA\Property(property="tanggal_mulai", type="string", format="date"),
     *             @OA\Property(property="tanggal_selesai", type="string", format="date", nullable=true),
     *             @OA\Property(property="jam_pelatihan", type="integer", enum={"1"}, nullable=true),
     *             @OA\Property(property="kapasitas_peserta", type="integer", enum={"1"}, nullable=true),
     *             @OA\Property(property="biaya", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="lokasi", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="tipe", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *             @OA\Property(property="sertifikat_diberikan", type="boolean", nullable=true),
     *             @OA\Property(property="materi_pokok", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_pelatihan', 'nama_pelatihan', 'deskripsi', 'kategori', 'unit_id', 'instructor_user_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_pelatihan', 'kapasitas_peserta', 'biaya', 'lokasi', 'tipe', 'status', 'sertifikat_diberikan', 'materi_pokok']);
        $trainingProgram = TrainingProgram::create($validated);
        return $this->success($trainingProgram, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/training-programs/{id}",
     *     tags={"Pelatihan & Pembinaan"},
     *     summary="Get Pelatihan & Pembinaan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(TrainingProgram $trainingProgram)
    {
        return $this->success($trainingProgram);
    }
    /**
     * @OA\Put(
     *     path="/training-programs/{id}",
     *     tags={"Pelatihan & Pembinaan"},
     *     summary="Update Pelatihan & Pembinaan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_pelatihan", type="string", maxLength=200),
     *             @OA\Property(property="deskripsi", type="string", nullable=true),
     *             @OA\Property(property="kategori", type="string", maxLength=50),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="instructor_user_id", type="string", nullable=true),
     *             @OA\Property(property="tanggal_mulai", type="string", format="date"),
     *             @OA\Property(property="tanggal_selesai", type="string", format="date", nullable=true),
     *             @OA\Property(property="jam_pelatihan", type="integer", enum={"1"}, nullable=true),
     *             @OA\Property(property="kapasitas_peserta", type="integer", enum={"1"}, nullable=true),
     *             @OA\Property(property="biaya", type="integer", enum={"0"}, nullable=true),
     *             @OA\Property(property="lokasi", type="string", maxLength=200, nullable=true),
     *             @OA\Property(property="tipe", type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="status", type="string", enum={"perencanaan"}),
     *             @OA\Property(property="sertifikat_diberikan", type="boolean", nullable=true),
     *             @OA\Property(property="materi_pokok", type="string", nullable=true),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, TrainingProgram $trainingProgram)
    {
        $validated = $request->only(['kode_pelatihan', 'nama_pelatihan', 'deskripsi', 'kategori', 'unit_id', 'instructor_user_id', 'tanggal_mulai', 'tanggal_selesai', 'jam_pelatihan', 'kapasitas_peserta', 'biaya', 'lokasi', 'tipe', 'status', 'sertifikat_diberikan', 'materi_pokok']);
        $trainingProgram->update($validated);
        return $this->success($trainingProgram->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/training-programs/{id}",
     *     tags={"Pelatihan & Pembinaan"},
     *     summary="Delete Pelatihan & Pembinaan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(TrainingProgram $trainingProgram)
    {
        $trainingProgram->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
