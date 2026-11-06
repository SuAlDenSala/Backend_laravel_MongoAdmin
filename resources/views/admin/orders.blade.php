@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-cart-shopping" style="margin-right: 0.5rem;"></i>
                Orders List
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddOrderModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Order
                </button>
                <a href="/docs#/operations/orders-store" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Items Count</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="font-weight: 600;">{{ $order->order_number }}</td>
                        <td style="font-weight: 500;">{{ $order->user_name }}</td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $order->user_email }}</td>
                        <td>{{ is_array($order->items) ? count($order->items) : 0 }} items</td>
                        <td style="font-weight: 700;">${{ number_format($order->final_amount, 2) }}</td>
                        <td>
                            @if($order->status === 'paid' || $order->status === 'completed')
                                <span class="badge badge-success">{{ $order->status }}</span>
                            @elseif($order->status === 'pending' || $order->status === 'processing')
                                <span class="badge badge-warning">{{ $order->status }}</span>
                            @else
                                <span class="badge badge-danger">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $order->payment_method }}</td>
                        <td style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn-edit" onclick='openUpdateOrderModal(@json($order))'>
                                <i class="fa-solid fa-arrows-rotate"></i> Update Status
                            </button>

                            <form action="{{ route('admin.orders.destroy', $order->id ?? $order->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted);">No orders recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
            <div class="pagination-wrap">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Add Order Modal -->
    <div id="addOrderModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Create New Order</div>
                <button type="button" class="modal-close" onclick="closeAddOrderModal()">&times;</button>
            </div>
            <form action="{{ route('admin.orders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="user_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer Email</label>
                        <input type="email" name="user_email" class="form-control" required placeholder="customer@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Product</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id ?? $p->_id }}">{{ $p->name }} (${{ number_format($p->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="Credit Card">Credit Card</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash on Delivery">Cash on Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddOrderModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Place Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Order Status Modal -->
    <div id="updateOrderModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Update Order Status</div>
                <button type="button" class="modal-close" onclick="closeUpdateOrderModal()">&times;</button>
            </div>
            <form id="updateOrderForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Order Number</label>
                        <input type="text" id="order_number_display" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Select New Status</label>
                        <select id="order_status_select" name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="paid">Paid</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeUpdateOrderModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddOrderModal() {
            document.getElementById('addOrderModal').classList.add('active');
        }
        function closeAddOrderModal() {
            document.getElementById('addOrderModal').classList.remove('active');
        }

        function openUpdateOrderModal(order) {
            const orderId = order.id || order._id;
            document.getElementById('updateOrderForm').action = '/admin/orders/' + orderId + '/status';
            document.getElementById('order_number_display').value = order.order_number || '';
            document.getElementById('order_status_select').value = order.status || 'pending';

            document.getElementById('updateOrderModal').classList.add('active');
        }
        function closeUpdateOrderModal() {
            document.getElementById('updateOrderModal').classList.remove('active');
        }
    </script>

@endsection
