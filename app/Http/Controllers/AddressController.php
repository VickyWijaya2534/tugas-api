<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     summary="Get all addresses",
     *     tags={"Addresses"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(): JsonResponse
    {
        $addresses = Address::all();
        return response()->json(AddressResource::collection($addresses));
    }

    /**
     * @OA\Post(
     *     path="/api/addresses",
     *     summary="Create a new address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"street", "city", "province", "country", "postal_code"},
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="province", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="postal_code", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Address created"),
     *     @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(AddressRequest $request): JsonResponse
    {
        $address = Address::create($request->validated());
        return response()->json(new AddressResource($address), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/addresses/{id}",
     *     summary="Get address details",
     *     tags={"Addresses"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Address data"),
     *     @OA\Response(response=404, description="Address not found")
     * )
     */
    public function show($id): JsonResponse
    {
        $address = Address::findOrFail($id);
        return response()->json(new AddressResource($address));
    }

    /**
     * @OA\Put(
     *     path="/api/addresses/{id}",
     *     summary="Update an address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="province", type="string"),
     *             @OA\Property(property="country", type="string"),
     *             @OA\Property(property="postal_code", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Address updated"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(AddressRequest $request, $id): JsonResponse
    {
        $address = Address::findOrFail($id);
        $address->update($request->validated());
        return response()->json(new AddressResource($address));
    }

    /**
     * @OA\Delete(
     *     path="/api/addresses/{id}",
     *     summary="Delete an address",
     *     tags={"Addresses"},
     *     security={{"bearerAuth":{}}}, 
     *     @OA\Response(response=200, description="Address deleted"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy($id): JsonResponse
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(['message' => 'Address deleted successfully'], 200);
    }
}
