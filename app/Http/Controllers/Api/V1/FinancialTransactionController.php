<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="Keuangan")
 */
class FinancialTransactionController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/financial-transactions",
     *     tags={"Keuangan"},
     *     summary="List Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = FinancialTransaction::query()
            ->with(['category', 'unit']);
        $result = $this->cachedPaginate($query, 'financial_transactions', 5, ['keterangan'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/financial-transactions",
     *     tags={"Keuangan"},
     *     summary="Create Keuangan",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipe","jumlah","deskripsi","sumber_dana","metode_pembayaran","unit_id","tanggal_transaksi"},
     *             @OA\Property(property="tipe", type="string", enum={"pemasukan"}),
     *             @OA\Property(property="category_id", type="string", nullable=true),
     *             @OA\Property(property="jumlah", type="integer", enum={"0.01"}),
     *             @OA\Property(property="deskripsi", type="string", maxLength=255),
     *             @OA\Property(property="sumber_dana", type="string", enum={"anggaran"}),
     *             @OA\Property(property="metode_pembayaran", type="string", enum={"tunai"}),
     *             @OA\Property(property="unit_id", type="string"),
     *             @OA\Property(property="tanggal_transaksi", type="string", format="date"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['kode_transaksi_keu', 'tipe', 'category_id', 'budget_id', 'jumlah', 'deskripsi', 'sumber_dana', 'bukti_transaksi', 'metode_pembayaran', 'unit_id', 'approved_by', 'status', 'tanggal_transaksi', 'keterangan']);
        $financialTransaction = FinancialTransaction::create($validated);
        return $this->success($financialTransaction, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/financial-transactions/{id}",
     *     tags={"Keuangan"},
     *     summary="Get Keuangan detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(FinancialTransaction $financialTransaction)
    {
        return $this->success($financialTransaction);
    }
    /**
     * @OA\Put(
     *     path="/financial-transactions/{id}",
     *     tags={"Keuangan"},
     *     summary="Update Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", enum={"draft"}),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, FinancialTransaction $financialTransaction)
    {
        $validated = $request->only(['kode_transaksi_keu', 'tipe', 'category_id', 'budget_id', 'jumlah', 'deskripsi', 'sumber_dana', 'bukti_transaksi', 'metode_pembayaran', 'unit_id', 'approved_by', 'status', 'tanggal_transaksi', 'keterangan']);
        $financialTransaction->update($validated);
        return $this->success($financialTransaction->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/financial-transactions/{id}",
     *     tags={"Keuangan"},
     *     summary="Delete Keuangan",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(FinancialTransaction $financialTransaction)
    {
        $financialTransaction->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
