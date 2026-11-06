@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-layer-group" style="margin-right: 0.5rem;"></i>
                Categories List
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddCategoryModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Category
                </button>
                <a href="/docs#/operations/categories-store" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td style="font-size: 1.2rem;">
                            <i class="fa-solid fa-{{ $category->icon ?? 'tag' }}"></i>
                        </td>
                        <td style="font-weight: 600;">{{ $category->name }}</td>
                        <td style="font-family: monospace; color: var(--text-muted);">{{ $category->slug }}</td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;">{{ $category->description }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Disabled</span>
                            @endif
                        </td>
                        <td style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn-edit" onclick='openEditCategoryModal(@json($category))'>
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>

                            <form action="{{ route('admin.categories.destroy', $category->id ?? $category->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
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
                        <td colspan="6" style="text-align: center; color: var(--text-muted);">No categories available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
            <div class="pagination-wrap">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Add New Category</div>
                <button type="button" class="modal-close" onclick="closeAddCategoryModal()">&times;</button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Smart Home">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icon (FontAwesome Name)</label>
                        <input type="text" name="icon" class="form-control" placeholder="e.g. house-signal, laptop, tag">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Category summary..."></textarea>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="is_active" id="is_active_add" value="1" checked>
                        <label for="is_active_add" class="form-label" style="margin-bottom: 0; cursor: pointer;">Active Category</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddCategoryModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Category</div>
                <button type="button" class="modal-close" onclick="closeEditCategoryModal()">&times;</button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" id="edit_cat_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icon</label>
                        <input type="text" id="edit_cat_icon" name="icon" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea id="edit_cat_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" name="is_active" id="edit_cat_is_active" value="1">
                        <label for="edit_cat_is_active" class="form-label" style="margin-bottom: 0; cursor: pointer;">Active Category</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditCategoryModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Update Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddCategoryModal() {
            document.getElementById('addCategoryModal').classList.add('active');
        }
        function closeAddCategoryModal() {
            document.getElementById('addCategoryModal').classList.remove('active');
        }

        function openEditCategoryModal(category) {
            const categoryId = category.id || category._id;
            document.getElementById('editCategoryForm').action = '/admin/categories/' + categoryId;
            document.getElementById('edit_cat_name').value = category.name || '';
            document.getElementById('edit_cat_icon').value = category.icon || '';
            document.getElementById('edit_cat_description').value = category.description || '';
            document.getElementById('edit_cat_is_active').checked = !!category.is_active;

            document.getElementById('editCategoryModal').classList.add('active');
        }
        function closeEditCategoryModal() {
            document.getElementById('editCategoryModal').classList.remove('active');
        }
    </script>

@endsection
