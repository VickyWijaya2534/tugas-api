<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel API Documentation",
 *     description="Dokumentasi API untuk User",
 *     @OA\Contact(
 *         name="Support",
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Register a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "name"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (User::where('username', $data['username'])->exists()) {
            return response()->json(["errors" => ["username" => ["Username already registered"]]], 400);
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     summary="Login a user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User authenticated"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(["errors" => ["message" => ["Invalid username or password"]]], 401);
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->token
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get user details",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User data"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }
        return response()->json(new UserResource($user));
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update user information",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        $data = $request->validated();
        $user->update($data);

        return response()->json(new UserResource($user));
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="User deleted"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "User not found"], 404);
        }

        $user->delete();
        return response()->json(null, 204);
    }
}
