@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')

    <!-- KPI Metric Cards Grid -->
    <div class="grid-kpi">
        <div class="card-kpi">
            <div class="kpi-head">
                <span>Total Products</span>
                <div class="kpi-icon icon-blue">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalProducts }}</div>
        </div>

        <div class="card-kpi">
            <div class="kpi-head">
                <span>Active Categories</span>
                <div class="kpi-icon icon-purple">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalCategories }}</div>
        </div>

        <div class="card-kpi">
            <div class="kpi-head">
                <span>Total Orders</span>
                <div class="kpi-icon icon-amber">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalOrders }}</div>
        </div>

        <div class="card-kpi">
            <div class="kpi-head">
                <span>Registered Users</span>
                <div class="kpi-icon icon-green">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalUsers }}</div>
        </div>
    </div>

    <!-- Recent Orders Table Card -->
    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-clock-rotate-left" style="color: #ffffff; margin-right: 0.5rem;"></i>
                Recent Mongo Orders
            </div>
            <a href="{{ route('admin.orders') }}" style="color: #ffffff; text-decoration: underline; font-size: 0.85rem; font-weight: 500;">
                View All Orders &rarr;
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td style="font-weight: 600; color: #ffffff;">{{ $order->order_number }}</td>
                        <td style="color: #ffffff;">{{ $order->user_name }}</td>
                        <td style="font-weight: 700; color: #ffffff;">${{ number_format($order->final_amount, 2) }}</td>
                        <td style="color: #a1a1aa;">{{ $order->payment_method }}</td>
                        <td>
                            @if($order->status === 'paid' || $order->status === 'completed')
                                <span class="badge badge-success">{{ $order->status }}</span>
                            @elseif($order->status === 'pending')
                                <span class="badge badge-warning">{{ $order->status }}</span>
                            @else
                                <span class="badge badge-danger">{{ $order->status }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No orders recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Featured Products Card -->
    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-bag-shopping" style="color: #ffffff; margin-right: 0.5rem;"></i>
                Featured MongoDB Catalog Products
            </div>
            <a href="{{ route('admin.products') }}" style="color: #ffffff; text-decoration: underline; font-size: 0.85rem; font-weight: 500;">
                Manage Products &rarr;
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock Quantity</th>
                    <th>SKU</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProducts as $product)
                    <tr>
                        <td style="font-weight: 600; color: #ffffff;">{{ $product->name }}</td>
                        <td><span class="badge badge-info">{{ $product->category_name }}</span></td>
                        <td style="font-weight: 700; color: #ffffff;">${{ number_format($product->price, 2) }}</td>
                        <td style="color: #ffffff;">{{ $product->stock }} units</td>
                        <td style="color: #a1a1aa; font-family: monospace;">{{ $product->sku }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
