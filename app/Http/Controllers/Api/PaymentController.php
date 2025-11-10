<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/payment/{orderId}",
     *     summary="Process payment for an order",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="transaction_id", type="string", example="TXN123456"),
     *             @OA\Property(property="payment_details", type="object")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Payment processed successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function processPayment(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required_if:payment_method,online|string',
            'payment_details' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::with('payment')->findOrFail($orderId);

        DB::beginTransaction();
        try {
            // Mock payment processing logic
            $paymentStatus = 'completed';
            $orderStatus = 'completed';

            // Simulate random success/failure for online payments (for testing)
            if ($order->payment_method === 'online') {
                // In real scenario, integrate with payment gateway
                // For demo, randomly succeed or fail
                $mockSuccess = true; // Set to false to test failure scenario

                if (!$mockSuccess) {
                    $paymentStatus = 'failed';
                    $orderStatus = 'failed';
                }
            }

            // Update payment
            $payment = $order->payment;
            $payment->status = $paymentStatus;
            $payment->transaction_id = $request->transaction_id ?? ('TXN-' . strtoupper(uniqid()));
            $payment->paid_at = now();
            $payment->payment_details = $request->payment_details ?? [];
            $payment->save();

            // Update order status
            $order->status = $orderStatus;
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'order' => $order->load(['items', 'payment']),
                    'payment_status' => $paymentStatus
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/payment/{orderId}",
     *     summary="Update payment status",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"pending","completed","failed","refunded"}, example="completed")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Payment status updated"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function updatePaymentStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::with('payment')->findOrFail($orderId);
        $payment = $order->payment;

        DB::beginTransaction();
        try {
            $payment->status = $request->status;
            if ($request->status === 'completed' && !$payment->paid_at) {
                $payment->paid_at = now();
            }
            $payment->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated',
                'data' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payment/{orderId}",
     *     summary="Get payment status",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="orderId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Payment status retrieved"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getPaymentStatus($orderId)
    {
        $order = Order::with(['payment', 'items'])->findOrFail($orderId);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
