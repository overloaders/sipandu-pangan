<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;
use App\Models\User;
use App\Models\ApiClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="SIDUMA PANGAN API",
 *     version="1.0.0",
 *     description="REST API for SIDUMA PANGAN - SIDUMA PANGAN. Auth via Laravel Sanctum Bearer Token.",
 *     @OA\Contact(email="admin@sipandupangan.go.id", name="SIDUMA PANGAN Team")
 * )
 *
 * @OA\Server(url="https://pangan.asrar.my.id/api/v1", description="Production Server")
 *
 * @OA\Server(url="http://localhost:8000/api/v1", description="Development Server")
 *
 * @OA\SecurityScheme(securityScheme="sanctum", type="http", scheme="bearer", bearerFormat="Laravel Sanctum Token")
 *
 * @OA\Tag(name="Authentication", description="Login, Register, Token Management")
 * @OA\Tag(name="User Management", description="Users and Roles CRUD")
 * @OA\Tag(name="Dashboard", description="Dashboard overview and statistics")
 * @OA\Tag(name="Registrasi WBP", description="WBP registration and management")
 * @OA\Tag(name="Unit Kerja", description="Work units")
 * @OA\Tag(name="Blok & Kamar", description="Block and room management")
 * @OA\Tag(name="Laporan Harian", description="Daily reports")
 * @OA\Tag(name="Program Ketahanan Pangan", description="Food security programs")
 * @OA\Tag(name="Manajemen Lahan", description="Land and crop management")
 * @OA\Tag(name="Rencana Kerja Pertanian", description="Farming plans and activities")
 * @OA\Tag(name="Hasil Panen", description="Harvest records")
 * @OA\Tag(name="Peternakan", description="Livestock management")
 * @OA\Tag(name="Perikanan", description="Fishery management")
 * @OA\Tag(name="Inventaris & Stok", description="Inventory management")
 * @OA\Tag(name="Distribusi", description="Food distribution")
 * @OA\Tag(name="Nutrisi & Menu", description="Nutrition plans")
 * @OA\Tag(name="Pengolahan", description="Processing batches")
 * @OA\Tag(name="Keuangan", description="Financial management")
 * @OA\Tag(name="Pelatihan & Pembinaan", description="Training programs")
 * @OA\Tag(name="Sertifikasi & Komplians", description="Compliance records")
 * @OA\Tag(name="Analitik", description="Analytics indicators")
 * @OA\Tag(name="API Client Management", description="API client management")
 */
class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Authentication"},
     *     summary="Login and get Bearer token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="client_id", type="string", nullable=true),
     *             @OA\Property(property="client_secret", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'client_id' => 'nullable|string',
            'client_secret' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal', 422, $validator->errors());
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Email atau password salah', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Login berhasil');
    }

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"Authentication"},
     *     summary="Register new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Registered"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'operator';

        $user = User::create($validated);
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Registrasi berhasil', 201);
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout and revoke token",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logged out")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logout berhasil');
    }

    /**
     * @OA\Get(
     *     path="/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success($request->user());
    }

    /**
     * @OA\Put(
     *     path="/auth/profile",
     *     tags={"Authentication"},
     *     summary="Update current user profile",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="phone", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);
        $request->user()->update($validated);
        return $this->success($request->user()->fresh(), 'Profil berhasil diperbarui');
    }

    /**
     * @OA\Put(
     *     path="/auth/password",
     *     tags={"Authentication"},
     *     summary="Change password",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","password"},
     *             @OA\Property(property="current_password", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Changed")
     * )
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $request->user()->password)) {
            return $this->error('Password lama salah', 422);
        }

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success(null, 'Password berhasil diubah');
    }

    /**
     * @OA\Post(
     *     path="/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh token",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Refreshed")
     * )
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth-token')->plainTextToken;
        return $this->success(['token' => $token], 'Token berhasil direfresh');
    }

    /**
     * @OA\Delete(
     *     path="/auth/revoke",
     *     tags={"Authentication"},
     *     summary="Revoke all tokens",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Revoked")
     * )
     */
    public function revokeToken(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->success(null, 'Semua token berhasil dicabut');
    }

    /**
     * @OA\Post(
     *     path="/auth/client-token",
     *     tags={"Authentication"},
     *     summary="Generate client token for API integration",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id","client_secret"},
     *             @OA\Property(property="client_id", type="string"),
     *             @OA\Property(property="client_secret", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token generated")
     * )
     */
    public function generateClientToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validasi gagal', 422, $validator->errors());
        }

        $client = ApiClient::where('client_id', $request->client_id)
            ->where('client_secret', Hash::make($request->client_secret))
            ->first();

        if (!$client) {
            return $this->error('Client tidak valid', 401);
        }

        $token = $client->user->createToken('client-token', ['admin'])->plainTextToken;

        return $this->success(['token' => $token], 'Client token berhasil dibuat');
    }
}
