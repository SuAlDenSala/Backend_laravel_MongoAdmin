@extends('layouts.admin')

@section('title', 'Coupons Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-ticket" style="margin-right: 0.5rem;"></i>
                Coupons & Discounts
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddCouponModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Coupon Code
                </button>
                <a href="/docs#/operations/coupons-store" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Coupon Code</th>
                    <th>Discount Type</th>
                    <th>Value</th>
                    <th>Min. Order</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td style="font-weight: 700; font-family: monospace; font-size: 1rem;">{{ $coupon->code }}</td>
                        <td style="text-transform: capitalize;">{{ $coupon->discount_type }}</td>
                        <td style="font-weight: 700;">
                            @if($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}% OFF
                            @else
                                ${{ number_format($coupon->discount_value, 2) }} OFF
                            @endif
                        </td>
                        <td>${{ number_format($coupon->min_order_amount, 2) }}</td>
                        <td>{{ $coupon->used_count ?? 0 }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                        <td>
                            @if($coupon->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Expired</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-edit" onclick='openEditCouponModal(@json($coupon))'>
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('admin.coupons.destroy', $coupon->id ?? $coupon->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
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
                        <td colspan="7" style="text-align: center; color: var(--text-muted);">No discount coupons available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($coupons->hasPages())
            <div class="pagination-wrap">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>

    <!-- Add Coupon Modal -->
    <div id="addCouponModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Create Discount Coupon</div>
                <button type="button" class="modal-close" onclick="closeAddCouponModal()">&times;</button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" name="code" class="form-control" required placeholder="e.g. SAVE20, PROMO50" style="text-transform: uppercase;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Discount Type</label>
                            <select name="discount_type" class="form-control" required>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Discount Value</label>
                            <input type="number" step="0.01" name="discount_value" class="form-control" required placeholder="20">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Min Order Amount ($)</label>
                            <input type="number" step="0.01" name="min_order_amount" class="form-control" value="0" placeholder="50.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" value="100" placeholder="100">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddCouponModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Create Coupon</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Coupon Modal -->
    <div id="editCouponModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Discount Coupon</div>
                <button type="button" class="modal-close" onclick="closeEditCouponModal()">&times;</button>
            </div>
            <form id="editCouponForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Coupon Code</label>
                        <input type="text" id="edit_code" name="code" class="form-control" required style="text-transform: uppercase;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Discount Type</label>
                            <select id="edit_discount_type" name="discount_type" class="form-control" required>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Discount Value</label>
                            <input type="number" step="0.01" id="edit_discount_value" name="discount_value" class="form-control" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Min Order Amount ($)</label>
                            <input type="number" step="0.01" id="edit_min_order_amount" name="min_order_amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Usage Limit</label>
                            <input type="number" id="edit_usage_limit" name="usage_limit" class="form-control">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <span style="font-weight: 500;">Active Coupon Status</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditCouponModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddCouponModal() {
            document.getElementById('addCouponModal').classList.add('active');
        }
        function closeAddCouponModal() {
            document.getElementById('addCouponModal').classList.remove('active');
        }
        function openEditCouponModal(coupon) {
            const couponId = coupon.id || coupon._id;
            document.getElementById('editCouponForm').action = '/admin/coupons/' + couponId;
            document.getElementById('edit_code').value = coupon.code;
            document.getElementById('edit_discount_type').value = coupon.discount_type;
            document.getElementById('edit_discount_value').value = coupon.discount_value;
            document.getElementById('edit_min_order_amount').value = coupon.min_order_amount || 0;
            document.getElementById('edit_usage_limit').value = coupon.usage_limit || 100;
            document.getElementById('edit_is_active').checked = coupon.is_active ? true : false;
            document.getElementById('editCouponModal').classList.add('active');
        }
        function closeEditCouponModal() {
            document.getElementById('editCouponModal').classList.remove('active');
        }
    </script>

@endsection
