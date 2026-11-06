@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-users" style="margin-right: 0.5rem;"></i>
                Registered Accounts
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddUserModal()">
                    <i class="fa-solid fa-user-plus"></i>
                    Add User Account
                </button>
                <a href="/docs#/operations/auth-register" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>API Token</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="font-weight: 600;">{{ $user->name }}</td>
                        <td style="color: var(--text-muted);">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-warning"><i class="fa-solid fa-shield-halved" style="margin-right: 0.2rem;"></i> {{ $user->role }}</span>
                            @else
                                <span class="badge badge-info">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; color: var(--text-muted); font-size: 0.8rem;">
                            {{ Str::limit($user->api_token, 20) }}
                        </td>
                        <td style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn-edit" onclick='openEditUserModal(@json($user))'>
                                <i class="fa-solid fa-user-pen"></i> Edit
                            </button>

                            @if(($user->id ?? $user->_id) !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id ?? $user->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            @else
                                <span style="font-size: 0.75rem; color: var(--text-muted); padding: 0.35rem 0.5rem;">(You)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Add New User Account</div>
                <button type="button" class="modal-close" onclick="closeAddUserModal()">&times;</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Jane Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="jane@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Role</label>
                        <select name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddUserModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit User Account</div>
                <button type="button" class="modal-close" onclick="closeEditUserModal()">&times;</button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" id="edit_user_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" id="edit_user_email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Account Role</label>
                        <select id="edit_user_role" name="role" class="form-control" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditUserModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Update User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
        }
        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
        }

        function openEditUserModal(user) {
            const userId = user.id || user._id;
            document.getElementById('editUserForm').action = '/admin/users/' + userId;
            document.getElementById('edit_user_name').value = user.name || '';
            document.getElementById('edit_user_email').value = user.email || '';
            document.getElementById('edit_user_role').value = user.role || 'customer';

            document.getElementById('editUserModal').classList.add('active');
        }
        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.remove('active');
        }
    </script>

@endsection
