<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/subscriptions",
     *     operationId="storeSubscription",
     *     tags={"Subscriptions"},
     *     summary="Create a new subscription",
     *     description="Creates a new email subscription with unique email validation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 description="Email address for the subscription"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Subscription created successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request: Email is missing or invalid format, or email already exists"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error: An error occurred while processing the request"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:subscriptions',
            ]);
            $subscription = Subscription::create($request->all());
            return response()->json($subscription, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your request', 'message' => $e->getMessage()], 500);
        }
    }
}
