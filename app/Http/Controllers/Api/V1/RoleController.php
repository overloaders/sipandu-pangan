<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\Role;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="User Management")
 */
class RoleController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"User Management"},
     *     summary="List User Management",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request)
    {
        $roles = Role::query()
            ->orderByDesc('created_at')
            ->paginate(15);
        return $this->success($roles);
    }
    /**
     * @OA\Post(
     *     path="/roles",
     *     tags={"User Management"},
     *     summary="Create User Management",
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
        $validated = $request->only(['name', 'slug', 'description']);
        $role = Role::create($validated);
        return $this->success($role, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/roles/{id}",
     *     tags={"User Management"},
     *     summary="Get User Management detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Role $role)
    {
        return $this->success($role);
    }
    /**
     * @OA\Put(
     *     path="/roles/{id}",
     *     tags={"User Management"},
     *     summary="Update User Management",
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
    public function update(Request $request, Role $role)
    {
        $validated = $request->only(['name', 'slug', 'description']);
        $role->update($validated);
        return $this->success($role->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/roles/{id}",
     *     tags={"User Management"},
     *     summary="Delete User Management",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
