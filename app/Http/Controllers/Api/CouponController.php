<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * List all coupons.
     */
    public function index()
    {
        $coupons = Coupon::all();

        return response()->json([
            'success' => true,
            'data' => $coupons
        ]);
    }

    /**
     * Create a promotional coupon code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'discount_type' => 'required|string|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0.01',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => (float) $validated['discount_value'],
            'min_order_amount' => (float) ($validated['min_order_amount'] ?? 0),
            'usage_limit' => (int) ($validated['usage_limit'] ?? 100),
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'data' => $coupon
        ], 201);
    }

    /**
     * Apply and calculate coupon discount for an order amount.
     */
    public function apply(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['code']))
            ->where('is_active', true)
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ], 404);
        }

        $totalAmount = (float) $validated['total_amount'];

        if ($totalAmount < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Order total must be at least \${$coupon->min_order_amount} to use this coupon."
            ], 422);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($totalAmount * $coupon->discount_value) / 100;
        } else {
            $discount = $coupon->discount_value;
        }

        $finalTotal = max(0, $totalAmount - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'data' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'calculated_discount' => round($discount, 2),
                'final_total' => round($finalTotal, 2)
            ]
        ]);
    }

    /**
     * Delete a coupon code.
     */
    public function destroy(string $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found.'
            ], 404);
        }

        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.'
        ]);
    }
}
