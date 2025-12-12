@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">Reset User Password</h2>
                    <p class="text-muted mb-0">Send password reset link to users via email</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-primary-subtle text-primary me-3">
                                <i class="bi bi-info-circle fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">How It Works</h5>
                        </div>
                        
                        <div class="instructions-list">
                            <div class="instruction-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h6 class="fw-semibold mb-1">Search User</h6>
                                    <p class="text-muted small mb-0">Type name or email to find user</p>
                                </div>
                            </div>
                            
                            <div class="instruction-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h6 class="fw-semibold mb-1">Send Link</h6>
                                    <p class="text-muted small mb-0">Click "Send Reset Link" button</p>
                                </div>
                            </div>
                            
                            <div class="instruction-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h6 class="fw-semibold mb-1">User Receives Email</h6>
                                    <p class="text-muted small mb-0">User gets email with reset link</p>
                                </div>
                            </div>
                            
                            <div class="instruction-item">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h6 class="fw-semibold mb-1">Password Reset</h6>
                                    <p class="text-muted small mb-0">User creates new password</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 mt-4 mb-0">
                            <i class="bi bi-shield-check me-2"></i>
                            <small>Reset links are valid for 60 minutes and can only be used once.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="reset-icon-wrapper mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2M5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-2">Send Password Reset Link</h4>
                            <p class="text-muted">Search for a user and send them a secure password reset link</p>
                        </div>

                        <form action="{{ route('superadmin.send.reset.link') }}" method="POST" id="resetPasswordForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="user_search" class="form-label fw-semibold">
                                    Search User <span class="text-danger">*</span>
                                </label>
                                <div class="user-search-wrapper position-relative">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control border-start-0 ps-2" 
                                               id="user_search" 
                                               placeholder="Type name or email to search..."
                                               autocomplete="off">
                                        <button class="btn btn-outline-secondary" type="button" id="clearSearchBtn" style="display: none;">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="user_id" id="user_id" required>
                                    
                                    <div class="search-results-dropdown" id="searchResultsDropdown">
                                        <div class="search-results-content" id="searchResultsContent">
                                            <div class="text-center text-muted py-3">
                                                <i class="bi bi-search fs-3"></i>
                                                <p class="mb-0 small mt-2">Start typing to search users...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text">
                                    Search by name or email address
                                </div>
                            </div>

                            <div id="userInfoPreview" class="user-info-preview d-none mb-4">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-semibold mb-0">Selected User</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="clearSelectionBtn">
                                                <i class="bi bi-x-lg"></i> Clear
                                            </button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle text-primary me-2 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Name</small>
                                                        <span class="fw-medium" id="previewName">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-envelope text-primary me-2 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Email</small>
                                                        <span class="fw-medium" id="previewEmail">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-shield-check text-primary me-2 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Role</small>
                                                        <span class="badge bg-secondary-subtle text-secondary" id="previewRole">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="sendResetBtn" disabled>
                                    <i class="bi bi-send me-2"></i>
                                    Send Password Reset Link
                                </button>
                                <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-people me-2"></i>
                                    View All Users
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-clock-history text-primary me-2"></i>
                            Important Notes
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Reset links expire after 60 minutes
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Users can only use each link once
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                Email will be sent to the registered email address
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.instructions-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.instruction-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
}

.step-content h6 {
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.reset-icon-wrapper {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    /* REMOVED: animation: float 3s ease-in-out infinite; */
}

/* REMOVED: @keyframes float block */

.user-info-preview {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-control:focus, .input-group:focus-within .form-control {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.15);
}

.input-group:focus-within .input-group-text {
    border-color: #6366f1;
}

/* Search Dropdown Styles */
.user-search-wrapper {
    position: relative;
}

.search-results-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e5e9f2;
    border-radius: 8px;
    margin-top: 8px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    display: none;
}

.search-results-dropdown.show {
    display: block;
}

.search-results-content {
    padding: 8px;
}

.search-result-item {
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 4px;
}

.search-result-item:hover {
    background: #f5f6fa;
}

.search-result-item:last-child {
    margin-bottom: 0;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #6366f1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    flex-shrink: 0;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    color: #364a63;
    margin-bottom: 2px;
}

.user-email {
    font-size: 13px;
    color: #8094ae;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role-badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 4px;
    background: #e5e9f2;
    color: #526484;
    white-space: nowrap;
}

.search-loading {
    text-align: center;
    padding: 20px;
    color: #8094ae;
}

.search-no-results {
    text-align: center;
    padding: 30px 20px;
    color: #8094ae;
}

.search-no-results i {
    font-size: 32px;
    color: #e5e9f2;
    margin-bottom: 8px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user_search');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const searchDropdown = document.getElementById('searchResultsDropdown');
    const searchContent = document.getElementById('searchResultsContent');
    const userIdInput = document.getElementById('user_id');
    const userInfoPreview = document.getElementById('userInfoPreview');
    const previewName = document.getElementById('previewName');
    const previewEmail = document.getElementById('previewEmail');
    const previewRole = document.getElementById('previewRole');
    const submitBtn = document.getElementById('sendResetBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const form = document.getElementById('resetPasswordForm');

    let searchTimeout;
    let selectedUser = null;

    // Search input handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length > 0) {
            clearSearchBtn.style.display = 'block';
        } else {
            clearSearchBtn.style.display = 'none';
            searchDropdown.classList.remove('show');
            return;
        }

        // Clear previous timeout
        clearTimeout(searchTimeout);

        // Show loading
        searchContent.innerHTML = `
            <div class="search-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 small mt-2">Searching...</p>
            </div>
        `;
        searchDropdown.classList.add('show');

        // Debounce search
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 300);
    });

    // Clear search button
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';
        searchDropdown.classList.remove('show');
        searchInput.focus();
    });

    // Clear selection button
    clearSelectionBtn.addEventListener('click', function() {
        clearSelection();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-search-wrapper')) {
            searchDropdown.classList.remove('show');
        }
    });

    // Perform AJAX search
    function performSearch(query) {
        // NOTE: This route is assumed to be defined in your Laravel application
        fetch(`{{ route('superadmin.users.search') }}?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            displayResults(data);
        })
        .catch(error => {
            console.error('Search error:', error);
            searchContent.innerHTML = `
                <div class="search-no-results">
                    <i class="bi bi-exclamation-triangle"></i>
                    <p class="mb-0">Error searching users</p>
                </div>
            `;
        });
    }

    // Display search results
    function displayResults(users) {
        if (users.length === 0) {
            searchContent.innerHTML = `
                <div class="search-no-results">
                    <i class="bi bi-inbox"></i>
                    <p class="mb-0">No users found</p>
                </div>
            `;
            return;
        }

        let html = '';
        users.forEach(user => {
            const initials = getInitials(user.name);
            const avatarColor = getAvatarColor(user.name);
            
            html += `
                <div class="search-result-item" data-user-id="${user.id}" data-user-name="${escapeHtml(user.name)}" data-user-email="${escapeHtml(user.email)}" data-user-role="${escapeHtml(user.role)}">
                    <div class="user-avatar" style="background: ${avatarColor}">
                        ${initials}
                    </div>
                    <div class="user-details">
                        <div class="user-name">${escapeHtml(user.name)}</div>
                        <div class="user-email">${escapeHtml(user.email)}</div>
                    </div>
                    <span class="user-role-badge">${escapeHtml(user.role)}</span>
                </div>
            `;
        });

        searchContent.innerHTML = html;

        // Add click handlers to results
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                selectUser({
                    id: this.dataset.userId,
                    name: this.dataset.userName,
                    email: this.dataset.userEmail,
                    role: this.dataset.userRole
                });
            });
        });
    }

    // Select a user
    function selectUser(user) {
        selectedUser = user;
        userIdInput.value = user.id;
        searchInput.value = user.name;
        
        // Update preview
        previewName.textContent = user.name;
        previewEmail.textContent = user.email;
        previewRole.textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);
        
        // Show preview
        userInfoPreview.classList.remove('d-none');
        
        // Enable submit button
        submitBtn.disabled = false;
        
        // Hide dropdown
        searchDropdown.classList.remove('show');
        clearSearchBtn.style.display = 'none';
    }

    // Clear selection
    function clearSelection() {
        selectedUser = null;
        userIdInput.value = '';
        searchInput.value = '';
        userInfoPreview.classList.add('d-none');
        submitBtn.disabled = true;
        clearSearchBtn.style.display = 'none';
        searchInput.focus();
    }

    // Helper functions
    function getInitials(name) {
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    function getAvatarColor(name) {
        const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6'];
        const index = name.length % colors.length;
        return colors[index];
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Form submission with loading state
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    });
});
</script>

@endsection