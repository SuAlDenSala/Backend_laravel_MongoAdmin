<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * List user / admin orders.
     */
    public function index(Request $request)
    {
        $orders = Order::orderBy('_id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Place a new order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'coupon_code' => 'nullable|string',
        ]);

        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');
        $user = User::where('api_token', $token)->first();

        $processedItems = [];
        $totalAmount = 0;

        foreach ($validated['items'] as $itemData) {
            $product = Product::find($itemData['product_id']);
            if ($product) {
                $qty = (int) $itemData['quantity'];
                $itemTotal = $product->price * $qty;
                $totalAmount += $itemTotal;

                $processedItems[] = [
                    'product_id' => (string) $product->_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $qty,
                ];

                // Deduct stock
                $product->stock = max(0, $product->stock - $qty);
                $product->save();
            }
        }

        $discountAmount = 0;
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))
                ->where('is_active', true)
                ->first();

            if ($coupon && $totalAmount >= $coupon->min_order_amount) {
                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = ($totalAmount * $coupon->discount_value) / 100;
                } else {
                    $discountAmount = $coupon->discount_value;
                }
                $coupon->used_count += 1;
                $coupon->save();
            }
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        $order = Order::create([
            'order_number' => 'ORD-' . date('Y') . '-' . strtoupper(Str::random(6)),
            'user_id' => $user ? $user->_id : null,
            'user_name' => $user ? $user->name : 'Guest Customer',
            'user_email' => $user ? $user->email : 'guest@example.com',
            'items' => $processedItems,
            'total_amount' => round($totalAmount, 2),
            'discount_amount' => round($discountAmount, 2),
            'final_amount' => round($finalAmount, 2),
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'coupon_code' => $validated['coupon_code'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully.',
            'data' => $order
        ], 201);
    }

    /**
     * Display order details.
     */
    public function show(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (Admin function).
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,paid,shipped,completed,cancelled',
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'data' => $order
        ]);
    }

    /**
     * Cancel an active order.
     */
    public function cancel(string $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        if (in_array($order->status, ['shipped', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled as it has already been processed.'
            ], 422);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
            'data' => $order
        ]);
    }
}
