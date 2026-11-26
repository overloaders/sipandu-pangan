<?php
namespace App\Http\Controllers\Api\V1;
use OpenApi\Annotations as OA;
use App\Models\User;
use Illuminate\Http\Request;
/**
 * @OA\Tag(name="User Management")
 */
class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"User Management"},
     *     summary="List User Management",
     *     security={"sanctum":{}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
        public function index(Request $request)
    {
        $query = User::query()
            ->with([]);
        $result = $this->cachedPaginate($query, 'users', 5, ['name', 'email'], $request);
        return $this->success($result);
    }
    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"User Management"},
     *     summary="Create User Management",
     *     security={"sanctum":{}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","roles"},
     *             @OA\Property(property="name", type="string", maxLength=150),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150),
     *             @OA\Property(property="password", type="string", enum={"8"}),
     *             @OA\Property(property="nip", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true),
     *             @OA\Property(property="unit_id", type="string", nullable=true),
     *             @OA\Property(property="roles", type="string", enum={"1"}),
     *             @OA\Property(property="is_active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->only(['name', 'email', 'password', 'nip', 'phone', 'foto', 'unit_id', 'is_active', 'two_factor_enabled']);
        $user = User::create($validated);
        return $this->success($user, 'Data berhasil ditambahkan', 201);
    }
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"User Management"},
     *     summary="Get User Management detail",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(User $user)
    {
        return $this->success($user);
    }
    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"User Management"},
     *     summary="Update User Management",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=150),
     *             @OA\Property(property="email", type="string", format="email", maxLength=150),
     *             @OA\Property(property="password", type="string", enum={"8"}, nullable=true),
     *             @OA\Property(property="nip", type="string", maxLength=30, nullable=true),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true),
     *             @OA\Property(property="unit_id", type="string", nullable=true),
     *             @OA\Property(property="roles", type="string", enum={"1"}),
     *             @OA\Property(property="is_active", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Updated"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(\Illuminate\Http\Request $request, User $user)
    {
        $validated = $request->only(['name', 'email', 'password', 'nip', 'phone', 'foto', 'unit_id', 'is_active', 'two_factor_enabled']);
        $user->update($validated);
        return $this->success($user->refresh(), 'Data berhasil diperbarui');
    }
    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"User Management"},
     *     summary="Delete User Management",
     *     security={"sanctum":{}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->success(null, 'Data berhasil dihapus');
    }
}
