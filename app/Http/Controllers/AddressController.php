<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        return response()->json(Address::all());
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
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string'
        ]);

        $address = Address::create($data);
        return response()->json($address, 201);
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
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        $address->update($request->all());
        return response()->json($address);
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
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(["message" => "Address deleted"], 200);
    }
}
