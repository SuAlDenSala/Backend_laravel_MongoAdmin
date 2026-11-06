<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Review;
use App\Models\Coupon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database in MongoDB.
     */
    public function run(): void
    {
        // 1. Seed Admin & Customer Users
        User::truncate();
        
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'api_token' => 'admin-secret-token-12345',
        ]);

        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'api_token' => 'customer-secret-token-67890',
        ]);

        // 2. Seed Categories
        Category::truncate();

        $catElectronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Gadgets, computers, smartphones, and accessories.',
            'icon' => 'laptop',
            'is_active' => true,
        ]);

        $catFashion = Category::create([
            'name' => 'Fashion & Apparel',
            'slug' => 'fashion-apparel',
            'description' => 'Trendy clothing, footwear, and fashion items.',
            'icon' => 'shopping-bag',
            'is_active' => true,
        ]);

        $catHome = Category::create([
            'name' => 'Home & Living',
            'slug' => 'home-living',
            'description' => 'Furniture, decor, and kitchen appliances.',
            'icon' => 'home',
            'is_active' => true,
        ]);

        $catBooks = Category::create([
            'name' => 'Books & Media',
            'slug' => 'books-media',
            'description' => 'Best-selling novels, tech books, and audiobooks.',
            'icon' => 'book',
            'is_active' => true,
        ]);

        // 3. Seed Products
        Product::truncate();

        $p1 = Product::create([
            'name' => 'ProBook Laptop 15"',
            'slug' => 'probook-laptop-15',
            'description' => 'High-performance laptop with 16GB RAM and 512GB SSD.',
            'price' => 1299.99,
            'stock' => 25,
            'category_id' => $catElectronics->_id,
            'category_name' => $catElectronics->name,
            'sku' => 'ELEC-PRO-15',
            'attributes' => [
                'RAM' => '16GB DDR5',
                'Processor' => 'Intel Core i7 13th Gen',
                'Storage' => '512GB NVMe SSD',
                'Display' => '15.6 Inch OLED'
            ],
            'is_featured' => true,
        ]);

        $p2 = Product::create([
            'name' => 'Wireless ANC Headphones',
            'slug' => 'wireless-anc-headphones',
            'description' => 'Active Noise Cancelling Bluetooth headphones with 30h battery.',
            'price' => 249.50,
            'stock' => 50,
            'category_id' => $catElectronics->_id,
            'category_name' => $catElectronics->name,
            'sku' => 'ELEC-HEAD-01',
            'attributes' => [
                'Battery' => '30 Hours',
                'Bluetooth' => 'v5.3',
                'Color' => 'Matte Black'
            ],
            'is_featured' => true,
        ]);

        $p3 = Product::create([
            'name' => 'Classic Denim Jacket',
            'slug' => 'classic-denim-jacket',
            'description' => 'Premium vintage denim jacket for all seasons.',
            'price' => 89.99,
            'stock' => 40,
            'category_id' => $catFashion->_id,
            'category_name' => $catFashion->name,
            'sku' => 'FASH-DNM-JKT',
            'attributes' => [
                'Material' => '100% Cotton Denim',
                'Size' => 'M, L, XL',
                'Care' => 'Machine Wash Cold'
            ],
            'is_featured' => false,
        ]);

        $p4 = Product::create([
            'name' => 'Ergonomic Mesh Office Chair',
            'slug' => 'ergonomic-mesh-office-chair',
            'description' => 'Adjustable lumbar support mesh chair for high productivity.',
            'price' => 199.00,
            'stock' => 15,
            'category_id' => $catHome->_id,
            'category_name' => $catHome->name,
            'sku' => 'HOME-CHR-ERG',
            'attributes' => [
                'Weight Capacity' => '150 kg',
                'Material' => 'Breathable Mesh',
                'Warranty' => '3 Years'
            ],
            'is_featured' => true,
        ]);

        // 4. Seed Reviews
        Review::truncate();

        Review::create([
            'product_id' => $p1->_id,
            'user_id' => $customer->_id,
            'user_name' => $customer->name,
            'rating' => 5,
            'comment' => 'Blazing fast laptop! Great battery life and fantastic OLED screen.',
        ]);

        Review::create([
            'product_id' => $p2->_id,
            'user_id' => $customer->_id,
            'user_name' => $customer->name,
            'rating' => 4,
            'comment' => 'Very comfortable sound isolation. Excellent bass.',
        ]);

        // 5. Seed Coupons
        Coupon::truncate();

        Coupon::create([
            'code' => 'WELCOME10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'min_order_amount' => 50,
            'usage_limit' => 100,
            'used_count' => 5,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);

        Coupon::create([
            'code' => 'SAVE25',
            'discount_type' => 'fixed',
            'discount_value' => 25,
            'min_order_amount' => 100,
            'usage_limit' => 50,
            'used_count' => 2,
            'is_active' => true,
            'expires_at' => now()->addDays(15),
        ]);

        // 6. Seed Orders
        Order::truncate();

        Order::create([
            'order_number' => 'ORD-2026-001',
            'user_id' => $customer->_id,
            'user_name' => $customer->name,
            'user_email' => $customer->email,
            'items' => [
                [
                    'product_id' => (string) $p2->_id,
                    'name' => $p2->name,
                    'price' => $p2->price,
                    'quantity' => 1
                ]
            ],
            'total_amount' => 249.50,
            'discount_amount' => 24.95,
            'final_amount' => 224.55,
            'status' => 'paid',
            'payment_method' => 'Credit Card',
            'coupon_code' => 'WELCOME10',
        ]);

        Order::create([
            'order_number' => 'ORD-2026-002',
            'user_id' => $customer->_id,
            'user_name' => $customer->name,
            'user_email' => $customer->email,
            'items' => [
                [
                    'product_id' => (string) $p1->_id,
                    'name' => $p1->name,
                    'price' => $p1->price,
                    'quantity' => 1
                ]
            ],
            'total_amount' => 1299.99,
            'discount_amount' => 0.00,
            'final_amount' => 1299.99,
            'status' => 'pending',
            'payment_method' => 'PayPal',
            'coupon_code' => null,
        ]);
    }
}
