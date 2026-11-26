<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\ApiClient;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="API Client Management")
 */
class ApiClientController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api-clients",
     *     tags={"API Client Management"},
     *     summary="List API Client Management",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $apiClients = ApiClient::query()
            ->with(['user'])
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($apiClients);
    }
    /**
     * @OA\Post(
     *     path="/api-clients",
     *     tags={"API Client Management"},
     *     summary="Create API Client Management",
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
        $validated = $request->only(['name', 'client_id', 'client_secret_hash', 'user_id', 'description', 'allowed_ips', 'rate_limit_per_minute', 'rate_limit_per_day', 'is_active', 'last_access_at', 'scopes']);
        $apiClient = ApiClient::create($validated);
        return $this->success($apiClient, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/api-clients/{id}",
     *     tags={"API Client Management"},
     *     summary="Get API Client Management detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(ApiClient $apiClient)
    {
        $apiClient->load(['user']);
        return $this->success($apiClient);
    }
    /**
     * @OA\Put(
     *     path="/api-clients/{id}",
     *     tags={"API Client Management"},
     *     summary="Update API Client Management",
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
    public function update(Request $request, ApiClient $apiClient)
    {
        $validated = $request->only(['name', 'client_id', 'client_secret_hash', 'user_id', 'description', 'allowed_ips', 'rate_limit_per_minute', 'rate_limit_per_day', 'is_active', 'last_access_at', 'scopes']);
        $apiClient->update($validated);
        return $this->success($apiClient->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/api-clients/{id}",
     *     tags={"API Client Management"},
     *     summary="Delete API Client Management",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(ApiClient $apiClient)
    {
        $apiClient->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
