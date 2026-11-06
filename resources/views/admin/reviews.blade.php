@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('content')

    <div class="card-table">
        <div class="table-head-row">
            <div class="table-title">
                <i class="fa-solid fa-star" style="margin-right: 0.5rem;"></i>
                Product Customer Reviews
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn-add" onclick="openAddReviewModal()">
                    <i class="fa-solid fa-plus"></i>
                    Add Review
                </button>
                <a href="/docs#/operations/review-store" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    API Docs
                </a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Reviewer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr>
                        <td style="font-family: monospace; font-size: 0.85rem;">{{ Str::limit($review->product_id, 12) }}</td>
                        <td style="font-weight: 600;">{{ $review->user_name }}</td>
                        <td style="color: #eab308; font-weight: 700;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                            <span style="color: var(--text-main); margin-left: 0.25rem;">({{ $review->rating }}/5)</span>
                        </td>
                        <td style="font-size: 0.88rem;">{{ $review->comment }}</td>
                        <td style="display: flex; gap: 0.5rem;">
                            <button type="button" class="btn-edit" onclick='openEditReviewModal(@json($review))'>
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('admin.reviews.destroy', $review->id ?? $review->_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
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
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">No reviews submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($reviews->hasPages())
            <div class="pagination-wrap">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- Add Review Modal -->
    <div id="addReviewModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Submit Product Review</div>
                <button type="button" class="modal-close" onclick="closeAddReviewModal()">&times;</button>
            </div>
            <form action="{{ route('admin.reviews.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Target Product</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product to Review</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id ?? $p->_id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rating Star (1 to 5)</label>
                        <select name="rating" class="form-control" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Good)</option>
                            <option value="3">⭐⭐⭐ (3 - Average)</option>
                            <option value="2">⭐⭐ (2 - Below Average)</option>
                            <option value="1">⭐ (1 - Poor)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Review Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Write review comment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddReviewModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-check"></i> Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div id="editReviewModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">Edit Product Review</div>
                <button type="button" class="modal-close" onclick="closeEditReviewModal()">&times;</button>
            </div>
            <form id="editReviewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Rating Star (1 to 5)</label>
                        <select id="edit_rating" name="rating" class="form-control" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 - Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 - Good)</option>
                            <option value="3">⭐⭐⭐ (3 - Average)</option>
                            <option value="2">⭐⭐ (2 - Below Average)</option>
                            <option value="1">⭐ (1 - Poor)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Review Comment</label>
                        <textarea id="edit_comment" name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditReviewModal()">Cancel</button>
                    <button type="submit" class="btn-add"><i class="fa-solid fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddReviewModal() {
            document.getElementById('addReviewModal').classList.add('active');
        }
        function closeAddReviewModal() {
            document.getElementById('addReviewModal').classList.remove('active');
        }
        function openEditReviewModal(review) {
            const reviewId = review.id || review._id;
            document.getElementById('editReviewForm').action = '/admin/reviews/' + reviewId;
            document.getElementById('edit_rating').value = review.rating;
            document.getElementById('edit_comment').value = review.comment;
            document.getElementById('editReviewModal').classList.add('active');
        }
        function closeEditReviewModal() {
            document.getElementById('editReviewModal').classList.remove('active');
        }
    </script>

@endsection
