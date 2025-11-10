<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/checkout",
     *     summary="Create a new order",
     *     tags={"Checkout"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items","payment_method","shipping_name","shipping_address","shipping_phone","shipping_email"},
     *             @OA\Property(property="items", type="array", @OA\Items(
     *                 @OA\Property(property="item_id", type="integer", example=1),
     *                 @OA\Property(property="quantity", type="integer", example=1)
     *             )),
     *             @OA\Property(property="payment_method", type="string", enum={"cod","online"}, example="cod"),
     *             @OA\Property(property="shipping_name", type="string", example="John Doe"),
     *             @OA\Property(property="shipping_address", type="string", example="123 Main St"),
     *             @OA\Property(property="shipping_phone", type="string", example="+1234567890"),
     *             @OA\Property(property="shipping_email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="tax_percentage", type="number", example=10),
     *             @OA\Property(property="shipping_charge", type="number", example=50)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cod,online',
            'shipping_name' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_phone' => 'required|string',
            'shipping_email' => 'required|email',
            'tax_percentage' => 'sometimes|numeric|min:0|max:100',
            'shipping_charge' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            $itemIds = collect($request->items)->pluck('item_id')->unique()->values();
            $itemsById = Item::whereIn('id', $itemIds)->get()->keyBy('id');

            $subtotal = 0;
            foreach ($request->items as $inputItem) {
                $item = $itemsById->get($inputItem['item_id']);
                $subtotal += (float) $item->price * (int) $inputItem['quantity'];
            }

            $taxPercentage = $request->tax_percentage ?? 10;
            $tax = ($subtotal * $taxPercentage) / 100;
            $shippingCharge = $request->shipping_charge ?? 50;
            $totalAmount = $subtotal + $tax + $shippingCharge;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'status' => $request->payment_method === 'cod' ? 'pending' : 'processing',
                'shipping_name' => $request->shipping_name,
                'shipping_address' => $request->shipping_address,
                'shipping_phone' => $request->shipping_phone,
                'shipping_email' => $request->shipping_email,
                'payment_method' => $request->payment_method,
            ]);

            // Create order items
            foreach ($request->items as $inputItem) {
                $item = $itemsById->get($inputItem['item_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'product_name' => $item->name,
                    'product_price' => $item->price,
                    'quantity' => (int) $inputItem['quantity'],
                    'subtotal' => (float) $item->price * (int) $inputItem['quantity'],
                ]);
            }

            // Create payment record
            $paymentStatus = $request->payment_method === 'cod' ? 'pending' : 'pending';
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => $paymentStatus,
            ]);

            $order->load(['items', 'payment']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
