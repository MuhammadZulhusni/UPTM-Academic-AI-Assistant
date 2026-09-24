@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">All Users</h2>
                    <p class="text-muted mb-0 d-none d-md-block">Monitor all regular users</p>
                </div>
                <div class="nk-block-head-content">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" title="Filter Users">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <div class="d-flex gap-1 flex-wrap">
                            @if($roleFilter !== 'all')
                            <span class="badge bg-primary-subtle text-primary fw-medium">
                                @if($roleFilter === 'lecturer')
                                    Lecturers
                                @elseif($roleFilter === 'student')
                                    Students
                                @elseif($roleFilter === 'admin')
                                    Admins
                                @endif
                            </span>
                            @endif
                            @if($statusFilter !== 'all')
                            <span class="badge bg-{{ $statusFilter === 'active' ? 'success' : 'danger' }}-subtle text-{{ $statusFilter === 'active' ? 'success' : 'danger' }} fw-medium">
                                {{ ucfirst($statusFilter) }}
                            </span>
                            @endif
                            @if($roleFilter === 'all' && $statusFilter === 'all')
                            <span class="badge bg-secondary-subtle text-secondary fw-medium">
                                All Users
                            </span>
                            @endif
                        </div>
                        @if($roleFilter !== 'all' || $statusFilter !== 'all' || !empty($search))
                        <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between border-bottom border-light mt-3 mt-md-5 mb-4 pb-2 gap-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0">User Table</h5>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <form method="GET" action="{{ route('superadmin.users') }}" class="d-flex gap-2" id="searchForm">
                    <input type="hidden" name="role" value="{{ $roleFilter }}" id="hiddenRoleInput">
                    <input type="hidden" name="status" value="{{ $statusFilter }}" id="hiddenStatusInput">
                    <div class="input-group input-group-sm flex-grow-1" style="max-width: 300px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search users..." name="search" value="{{ $search }}" id="searchInput">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($users) > 0)
                <div id="user-table-container">
                    <table class="table table-sm mb-0" id="usersTable">
                        <thead class="table-light">
                            <tr>
                                <th class="tb-col d-none d-md-table-cell" style="width: 60px;">
                                    <div class="fs-13px text-base fw-semibold">Sl</div>
                                </th>
                                <th class="tb-col">
                                    <div class="fs-13px text-base fw-semibold">Name</div>
                                </th>
                                <th class="tb-col d-none d-lg-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Email</div>
                                </th>
                                <!-- <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Phone</div>
                                </th>
                                <th class="tb-col d-none d-sm-table-cell">
                                    <div class="fs-13px text-muted">Address</div>
                                </th> -->
                                <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Role</div>
                                </th>
                                <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Status</div>
                                </th>
                                <th class="tb-col text-center" style="width: 180px;">
                                    <div class="fs-13px text-base fw-semibold">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                            <tr class="user-row" data-user-role="{{ $user->role }}">
                                <td class="tb-col d-none d-md-table-cell" data-label="Sl">
                                    <div class="caption-text fw-medium">{{ $users->firstItem() + $key }}</div>
                                </td>
                                <td class="tb-col" data-label="Name">
                                    <div class="fs-6 fw-medium text-dark text-truncate">{{ $user->name }}</div>

                                    <div class="d-block d-md-none mt-1">
                                        <div class="fs-7 text-muted">
                                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                                        </div>
                                        <!-- <div class="fs-7 text-muted">
                                            <i class="bi bi-phone me-1"></i> {{ $user->phone ?? 'N/A' }}
                                        </div>
                                        <div class="fs-7 text-muted text-truncate">
                                            <i class="bi bi-geo-alt me-1"></i> {{ $user->address ?? 'N/A' }}
                                        </div> -->
                                        <div class="mt-1">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10 text-{{ $user->is_active ? 'success' : 'danger' }} rounded-pill px-2 py-1 small ms-1">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="tb-col d-none d-lg-table-cell" data-label="Email">
                                    <div class="fs-6 fw-medium text-dark text-truncate">{{ $user->email }}</div>
                                </td>
                                <!-- <td class="tb-col d-none d-md-table-cell" data-label="Phone">
                                    <div class="fs-13px text-muted">{{ $user->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="tb-col d-none d-sm-table-cell" data-label="Address">
                                    <div class="fs-13px text-muted">{{ $user->address ?? 'N/A' }}</div>
                                </td> -->
                                <td class="tb-col d-none d-md-table-cell" data-label="Role">
                                    <div class="fs-13px text-muted">{{ ucfirst($user->role) }}</div>
                                </td>
                                <td class="tb-col d-none d-md-table-cell" data-label="Status">
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="tb-col" data-label="Action">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-user-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserModal"
                                                title="Edit User">
                                            <i class="bi bi-pencil"></i>
                                            <span class="d-none d-xl-inline ms-1">Edit</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                data-user-id="{{ $user->id }}"
                                                title="Delete User">
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-xl-inline ms-1">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="no-search-results" class="text-center py-5 d-none">
                    <div class="mb-4">
                        <i class="bi bi-person-slash display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Users Found</h5>
                    <p class="text-muted mb-0">There are no users matching your current view or search query.</p>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-person-slash display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Users Found</h5>
                    <p class="text-muted mb-0">
                        @if(!empty($search) || $roleFilter !== 'all')
                            No users match your current filters. Try adjusting your search or filter criteria.
                        @else
                            There are no users to display at the moment.
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>

        @if ($users->total() > 0)
        <div class="nk-block pt-5">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-6">
                    <p class="text-muted mb-0 fs-14px">
                        Showing 
                        <span class="fw-medium text-dark">{{ $users->firstItem() }}</span> to 
                        <span class="fw-medium text-dark">{{ $users->lastItem() }}</span> of 
                        <span class="fw-medium text-dark">{{ $users->total() }}</span> users
                    </p>
                </div>

                <div class="col-12 col-md-6">
                    <nav aria-label="Users pagination">
                        <ul class="pagination pagination-minimal justify-content-md-end mb-0">
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous" {{ $users->onFirstPage() ? 'tabindex=-1' : '' }}>
                                    <em class="icon ni ni-chevron-left"></em>
                                    <span class="d-none d-sm-inline ms-1">Previous</span>
                                </a>
                            </li>

                            @php
                                $currentPage = $users->currentPage();
                                $lastPage    = $users->lastPage();
                                $start = max(1, $currentPage - 1);
                                $end   = min($lastPage, $currentPage + 1);

                                if ($currentPage <= 2) {
                                    $end = min(3, $lastPage);
                                }
                                if ($currentPage >= $lastPage - 1) {
                                    $start = max(1, $lastPage - 2);
                                }
                            @endphp

                            @if ($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $users->url(1) }}">1</a>
                                </li>
                                @if ($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $users->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next" {{ !$users->hasMorePages() ? 'tabindex=-1' : '' }}>
                                    <span class="d-none d-sm-inline me-1">Next</span>
                                    <em class="icon ni ni-chevron-right"></em>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Filter Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fs-5 fw-semibold text-muted" id="filterModalLabel">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-3">Filter users by role and account status.</p>

                {{-- Role Filter Section --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold text-muted mb-2">Filter by Role</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary role-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $roleFilter === 'all' ? 'active' : '' }}" data-role-filter="all">
                            <i class="bi bi-people me-2"></i> All Roles
                        </button>
                        <button type="button" class="btn btn-outline-secondary role-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $roleFilter === 'lecturer' ? 'active' : '' }}" data-role-filter="lecturer">
                            <i class="bi bi-person-workspace me-2"></i> Lecturers
                        </button>
                        <button type="button" class="btn btn-outline-secondary role-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $roleFilter === 'student' ? 'active' : '' }}" data-role-filter="student">
                            <i class="bi bi-person me-2"></i> Students
                        </button>
                        <button type="button" class="btn btn-outline-secondary role-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $roleFilter === 'admin' ? 'active' : '' }}" data-role-filter="admin">
                            <i class="bi bi-shield-lock me-2"></i> Admins
                        </button>
                    </div>
                </div>

                <hr class="my-3">

                {{-- Status Filter Section --}}
                <div>
                    <label class="form-label fw-semibold text-muted mb-2">Filter by Status</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary status-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $statusFilter === 'all' ? 'active' : '' }}" data-status-filter="all">
                            <i class="bi bi-circle me-2"></i> All Status
                        </button>
                        <button type="button" class="btn btn-outline-secondary status-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $statusFilter === 'active' ? 'active' : '' }}" data-status-filter="active">
                            <i class="bi bi-check-circle me-2 text-success"></i> Active Only
                        </button>
                        <button type="button" class="btn btn-outline-secondary status-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $statusFilter === 'inactive' ? 'active' : '' }}" data-status-filter="inactive">
                            <i class="bi bi-x-circle me-2 text-danger"></i> Inactive Only
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-end bg-light-subtle border-0 rounded-bottom-4 px-4 py-3">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill" id="applyFilterButton">
                    Apply Filter
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fs-5 fw-semibold" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_name" class="form-label fw-medium">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="student">Student</option>
                                <option value="lecturer">Lecturer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1" checked>
                                <label class="form-check-label fw-medium" for="edit_is_active">
                                    Account Active <small class="text-muted">(Inactive users cannot login)</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end border-0 px-4 py-3">
                    <button type="submit" class="btn btn-primary rounded-pill">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-center p-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#dc3545" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="text-muted">Do you really want to delete this user? This process cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalFilterButtons = document.querySelectorAll('.role-filter-modal-btn');
        const statusFilterButtons = document.querySelectorAll('.status-filter-modal-btn');
        const applyFilterButton = document.getElementById('applyFilterButton');
        const hiddenRoleInput = document.getElementById('hiddenRoleInput');
        const hiddenStatusInput = document.getElementById('hiddenStatusInput');
        const filterModal = document.getElementById('filterModal');
        
        let selectedRole = '{{ $roleFilter }}';
        let selectedStatus = '{{ $statusFilter }}';

        // Role Filter Button Handler
        modalFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                modalFilterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedRole = this.getAttribute('data-role-filter');
            });
        });

        // Status Filter Button Handler
        statusFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                statusFilterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedStatus = this.getAttribute('data-status-filter');
            });
        });

        // Apply Filter Button Handler
        applyFilterButton.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('role', selectedRole);
            currentUrl.searchParams.set('status', selectedStatus);
            currentUrl.searchParams.delete('page');
            currentUrl.searchParams.delete('search');
            window.location.href = currentUrl.toString();
        });

        // Modal Open Handler
        filterModal.addEventListener('show.bs.modal', function() {
            modalFilterButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-role-filter') === selectedRole);
            });
            statusFilterButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-status-filter') === selectedStatus);
            });
        });

        // Edit User Modal Handler
        const editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            
            // Fetch user data via AJAX
            fetch(`/superadmin/users/edit/${userId}`)
                .then(response => response.json())
                .then(user => {
                    document.getElementById('edit_name').value = user.name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_is_active').checked = user.is_active;
                    
                    // Set form action
                    document.getElementById('editUserForm').action = `/superadmin/users/update/${userId}`;
                })
                .catch(error => {
                    console.error('Error fetching user data:', error);
                    alert('Failed to load user data');
                });
        });

        // Delete Modal Handler
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const form = document.getElementById('deleteUserForm');
            form.action = `/superadmin/users/delete/${userId}`;
        });
    });
</script>

<style>
@media screen and (max-width: 767px) {
    #usersTable,
    #usersTable tbody,
    #usersTable tr {
        display: block;
    }

    #usersTable thead {
        display: none;
    }

    #usersTable tr {
        border: 1px solid #dee2e6;
        margin-bottom: 15px;
        border-radius: 6px;
    }

    #usersTable td {
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
        text-align: right;
        min-height: 40px;
        padding-top: 10px;
        padding-bottom: 10px;
        white-space: normal;
    }

    #usersTable td::before {
        content: attr(data-label);
        position: absolute;
        top: 10px;
        left: 10px;
        width: 45%;
        font-weight: bold;
        text-align: left;
        color: #495057;
        font-size: 0.875rem;
    }

    #usersTable td:nth-child(2)::before {
        content: none;
    }

    #usersTable td:nth-child(3),
    #usersTable td:nth-child(4),
    #usersTable td:nth-child(5),
    #usersTable td:nth-child(6),
    #usersTable td:nth-child(7) {
        display: none !important;
    }

    #usersTable td:nth-child(2) {
        text-align: left;
        padding-left: 10px;
        border-bottom: none;
        padding-bottom: 5px;
    }

    #usersTable td[data-label="Action"] {
        display: block !important;
        border-bottom: none;
        text-align: center;
        padding: 10px 0;
        padding-left: 0;
    }

    #usersTable td[data-label="Action"]::before {
        content: none;
    }

    #usersTable .user-row:hover,
    #usersTable .user-row:focus,
    #usersTable .user-row:active {
        background-color: transparent !important;
        cursor: default;
    }
}

#filterModal .role-filter-modal-btn.active,
#filterModal .status-filter-modal-btn.active {
    background-color: var(--bs-primary-bg-subtle, #e0f7fa);
    border-color: var(--bs-primary, #0d6efd) !important;
    color: var(--bs-primary, #0d6efd) !important;
    font-weight: 600;
}
</style>

@endsection