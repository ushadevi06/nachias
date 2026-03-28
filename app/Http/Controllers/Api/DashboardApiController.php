<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Exception;

class DashboardApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get Purchase Executive Dashboard Stats",
     *     description="Returns summary statistics and recent orders for the authenticated purchase executive.",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user_name", type="string", example="John Doe"),
     *                 @OA\Property(property="stats", type="object",
     *                     @OA\Property(property="total_orders", type="integer", example=10),
     *                     @OA\Property(property="pending_orders", type="integer", example=7),
     *                     @OA\Property(property="completed_orders", type="integer", example=3),
     *                     @OA\Property(property="revenue", type="number", format="float", example=50000.00)
     *                 ),
     *                 @OA\Property(property="recent_orders", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="po_number", type="string", example="PO-001"),
     *                         @OA\Property(property="po_date", type="string", format="date", example="2024-03-27"),
     *                         @OA\Property(property="status", type="string", example="Approved"),
     *                         @OA\Property(property="total_amount", type="number", format="float", example=5000.00)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $userId = $user->id;
            $baseQuery = PurchaseOrder::where('purchase_executive_id', $userId);

            $stats = [
                'total_orders' => (clone $baseQuery)->count(),
                'pending_orders' => (clone $baseQuery)->where('status', '!=', 'Received')->count(),
                'completed_orders' => (clone $baseQuery)->where('status', 'Received')->count(),
                'revenue' => round((clone $baseQuery)->sum('total_amount'), 2),
            ];

            $recentOrders = (clone $baseQuery)->orderBy('created_at', 'desc')->take(3)->select(['id', 'po_number', 'po_date', 'status', 'total_amount'])->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'user_name' => $user->name,
                    'stats' => $stats,
                    'recent_orders' => $recentOrders
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
