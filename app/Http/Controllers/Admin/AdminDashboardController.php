<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    /**
     * Admin Overview Dashboard
     */
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalOrders = Order::count();
        $totalUsers = User::count();
        $totalRevenue = Order::where('status', 'paid')->sum('final_amount');
        
        $recentOrders = Order::orderBy('_id', 'desc')->take(5)->get();
        $recentProducts = Product::orderBy('_id', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalOrders',
            'totalUsers',
            'totalRevenue',
            'recentOrders',
            'recentProducts'
        ));
    }

    /**
     * Product Management
     */
    public function products()
    {
        $products = Product::orderBy('_id', 'desc')->paginate(10);
        $categories = Category::where('is_active', true)->get();
        return view('admin.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|string',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($validated['category_id']);

        Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? '',
            'price' => (float) $validated['price'],
            'stock' => (int) $validated['stock'],
            'category_id' => $validated['category_id'],
            'category_name' => $category ? $category->name : 'Uncategorized',
            'sku' => $validated['sku'] ?? 'SKU-' . strtoupper(Str::random(6)),
            'attributes' => [],
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully!');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|string',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($validated['category_id']);

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? '',
            'price' => (float) $validated['price'],
            'stock' => (int) $validated['stock'],
            'category_id' => $validated['category_id'],
            'category_name' => $category ? $category->name : $product->category_name,
            'sku' => $validated['sku'] ?? $product->sku,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully!');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');
    }

    /**
     * Category Management
     */
    public function categories()
    {
        $categories = Category::orderBy('_id', 'desc')->paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? '',
            'icon' => $validated['icon'] ?? 'tag',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? '',
            'icon' => $validated['icon'] ?? $category->icon,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully!');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully!');
    }

    /**
     * Order Management
     */
    public function orders()
    {
        $orders = Order::orderBy('_id', 'desc')->paginate(10);
        $products = Product::all();
        $users = User::all();
        return view('admin.orders', compact('orders', 'products', 'users'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email',
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'status' => 'required|string|in:pending,paid,processing,completed,cancelled',
        ]);

        $product = Product::find($validated['product_id']);
        $totalAmount = $product ? ($product->price * $validated['quantity']) : 100.00;

        Order::create([
            'order_number' => 'ORD-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'user_id' => (string) auth()->id(),
            'user_name' => $validated['user_name'],
            'user_email' => $validated['user_email'],
            'items' => [
                [
                    'product_id' => $validated['product_id'],
                    'name' => $product ? $product->name : 'Custom Item',
                    'price' => $product ? $product->price : 100.00,
                    'quantity' => (int) $validated['quantity']
                ]
            ],
            'total_amount' => (float) $totalAmount,
            'discount_amount' => 0.00,
            'final_amount' => (float) $totalAmount,
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'],
            'coupon_code' => null,
        ]);

        return redirect()->route('admin.orders')->with('success', 'New Order created successfully!');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,paid,processing,completed,cancelled',
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.orders')->with('success', 'Order status updated to ' . $validated['status'] . '!');
    }

    public function destroyOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'Order deleted successfully!');
    }

    /**
     * User Management
     */
    public function users()
    {
        $users = User::orderBy('_id', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,customer',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'api_token' => Str::random(40),
        ]);

        return redirect()->route('admin.users')->with('success', 'User account created successfully!');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|string|in:admin,customer',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'User account updated successfully!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        if (($user->id ?? $user->_id) === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete your own logged-in admin account!');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User account deleted successfully!');
    }

    /**
     * Coupon Management
     */
    public function coupons()
    {
        $coupons = Coupon::orderBy('_id', 'desc')->paginate(10);
        return view('admin.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'discount_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        Coupon::create([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => (float) $validated['discount_value'],
            'min_order_amount' => (float) ($validated['min_order_amount'] ?? 0),
            'usage_limit' => (int) ($validated['usage_limit'] ?? 100),
            'used_count' => 0,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon code created successfully!');
    }

    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'discount_type' => 'required|string|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => (float) $validated['discount_value'],
            'min_order_amount' => (float) ($validated['min_order_amount'] ?? 0),
            'usage_limit' => (int) ($validated['usage_limit'] ?? 100),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully!');
    }

    public function destroyCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons')->with('success', 'Coupon deleted successfully!');
    }

    /**
     * Review Management
     */
    public function reviews()
    {
        $reviews = Review::orderBy('_id', 'desc')->paginate(10);
        $products = Product::all();
        return view('admin.reviews', compact('reviews', 'products'));
    }

    public function storeReview(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|string',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string',
        ]);

        Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => (string) auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin User',
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('admin.reviews')->with('success', 'Review added successfully!');
    }

    public function updateReview(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string',
        ]);

        $review->update([
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('admin.reviews')->with('success', 'Review updated successfully!');
    }

    public function destroyReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews')->with('success', 'Review deleted successfully!');
    }
}
