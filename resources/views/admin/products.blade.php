@extends('layouts.admin')

@section('title', 'Products Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-box" style="margin-right: 0.5rem;"></i>
                MongoDB Products Inventory
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddProductModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Product
                </button>
                <a href="/docs#/operations/products-store" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>SKU</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td style="font-weight: 600;">{{ $product->name }}</td>
                        <td><span class="badge badge-info">{{ $product->category_name }}</span></td>
                        <td style="font-weight: 700;">${{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->stock > 10)
                                <span class="badge badge-success">{{ $product->stock }} in stock</span>
                            @elseif($product->stock > 0)
                                <span class="badge badge-warning">{{ $product->stock }} low stock</span>
                            @else
                                <span class="badge badge-danger">Out of stock</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; color: var(--text-muted);">{{ $product->sku }}</td>
                        <td style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn-edit" 
                                onclick='openEditProductModal(@json($product))'>
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>

                            <form action="{{ route('admin.products.destroy', $product->id ?? $product->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                        <td colspan="6" style="text-align: center; color: var(--text-muted);">No products registered in database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($products->hasPages())
            <div class="pagination-wrap">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Add New Product</div>
                <button type="button" class="modal-close" onclick="closeAddProductModal()">&times;</button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Wireless Gaming Mouse">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id ?? $cat->_id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required placeholder="49.99">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock" class="form-control" required placeholder="50">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU (Optional)</label>
                        <input type="text" name="sku" class="form-control" placeholder="ELEC-MSE-001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Product details..."></textarea>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                        <input type="checkbox" name="is_featured" id="is_featured_add" value="1">
                        <label for="is_featured_add" class="form-label" style="margin-bottom: 0; cursor: pointer;">Mark as Featured Product</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddProductModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Product</div>
                <button type="button" class="modal-close" onclick="closeEditProductModal()">&times;</button>
            </div>
            <form id="editProductForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select id="edit_category_id" name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id ?? $cat->_id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Price ($)</label>
                            <input type="number" step="0.01" id="edit_price" name="price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" id="edit_stock" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" id="edit_sku" name="sku" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
                        <input type="checkbox" name="is_featured" id="edit_is_featured" value="1">
                        <label for="edit_is_featured" class="form-label" style="margin-bottom: 0; cursor: pointer;">Featured Product</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditProductModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddProductModal() {
            document.getElementById('addProductModal').classList.add('active');
        }
        function closeAddProductModal() {
            document.getElementById('addProductModal').classList.remove('active');
        }

        function openEditProductModal(product) {
            const productId = product.id || product._id;
            document.getElementById('editProductForm').action = '/admin/products/' + productId;
            document.getElementById('edit_name').value = product.name || '';
            document.getElementById('edit_category_id').value = product.category_id || '';
            document.getElementById('edit_price').value = product.price || '';
            document.getElementById('edit_stock').value = product.stock || 0;
            document.getElementById('edit_sku').value = product.sku || '';
            document.getElementById('edit_description').value = product.description || '';
            document.getElementById('edit_is_featured').checked = !!product.is_featured;

            document.getElementById('editProductModal').classList.add('active');
        }
        function closeEditProductModal() {
            document.getElementById('editProductModal').classList.remove('active');
        }
    </script>

@endsection
