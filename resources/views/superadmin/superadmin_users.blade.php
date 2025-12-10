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
                {{-- NEW: Filter Button Placement --}}
                <div class="nk-block-head-content">
                    <div class="d-flex align-items-center gap-2">
                        {{-- Filter Button (triggers Modal) --}}
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal" title="Filter Users">
                            <i class="bi bi-funnel"></i>
                        </button>
                        {{-- Filter Status Label --}}
                        <span class="badge bg-primary-subtle text-primary fw-medium" id="currentFilterLabel">
                            @if($roleFilter === 'lecturer')
                                Lecturers Only
                            @elseif($roleFilter === 'student')
                                Students Only
                            @elseif($roleFilter === 'admin')
                                Admins Only
                            @else
                                All Users
                            @endif
                        </span>
                        {{-- Clear Filters --}}
                        @if($roleFilter !== 'all' || !empty($search))
                        <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{--
            Search bar, section header (Now cleaner as filters moved to header).
        --}}
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between border-bottom border-light mt-3 mt-md-5 mb-4 pb-2 gap-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0">User Table</h5>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('superadmin.users') }}" class="d-flex gap-2" id="searchForm">
                    <input type="hidden" name="role" value="{{ $roleFilter }}" id="hiddenRoleInput">
                    <div class="input-group input-group-sm flex-grow-1" style="max-width: 300px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search users..." name="search" value="{{ $search }}" id="searchInput">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                </form>
            </div>
        </div>

        {{-- Main card containing the user table. --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($users) > 0)
                <div id="user-table-container">
                    {{-- The user table --}}
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
                                <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Phone</div>
                                </th>
                                <th class="tb-col d-none d-sm-table-cell">
                                    <div class="fs-13px text-muted">Address</div>
                                </th>
                                {{-- ROLE COLUMN --}}
                                <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Role</div>
                                </th>
                                <th class="tb-col text-center" style="width: 120px;">
                                    <div class="fs-13px text-base fw-semibold">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                            {{-- Added 'data-user-role' for JS filtering --}}
                            <tr class="user-row" data-user-role="{{ $user->role }}">
                                <td class="tb-col d-none d-md-table-cell" data-label="Sl">
                                    <div class="caption-text fw-medium">{{ $users->firstItem() + $key }}</div>
                                </td>
                                <td class="tb-col" data-label="Name">
                                    <div class="fs-6 fw-medium text-dark text-truncate">{{ $user->name }}</div>

                                    {{-- Mobile Details Block --}}
                                    <div class="d-block d-md-none mt-1">
                                        <div class="fs-7 text-muted">
                                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                                        </div>
                                        <div class="fs-7 text-muted">
                                            <i class="bi bi-phone me-1"></i> {{ $user->phone ?? 'N/A' }}
                                        </div>
                                        <div class="fs-7 text-muted text-truncate">
                                            <i class="bi bi-geo-alt me-1"></i> {{ $user->address ?? 'N/A' }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="tb-col d-none d-lg-table-cell" data-label="Email">
                                    <div class="fs-6 fw-medium text-dark text-truncate">{{ $user->email }}</div>
                                </td>
                                <td class="tb-col d-none d-md-table-cell" data-label="Phone">
                                    <div class="fs-13px text-muted">{{ $user->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="tb-col d-none d-sm-table-cell" data-label="Address">
                                    <div class="fs-13px text-muted">{{ $user->address ?? 'N/A' }}</div>
                                </td>
                                {{-- ROLE DATA --}}
                                <td class="tb-col d-none d-md-table-cell" data-label="Role">
                                    <div class="fs-13px text-muted">{{ ucfirst($user->role) }}</div>
                                </td>
                                <td class="tb-col" data-label="Action">
                                    <div class="d-flex justify-content-center gap-1">
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
                {{-- No search results message --}}
                <div id="no-search-results" class="text-center py-5 d-none">
                    <div class="mb-4">
                        <i class="bi bi-person-slash display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Users Found</h5>
                    <p class="text-muted mb-0">There are no users matching your current view or search query.</p>
                </div>
                @else
                {{-- Message shown when the $users array is empty --}}
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
        {{-- MODERN MINIMALIST PAGINATION (USERS) --}}
        @if ($users->total() > 0)
        <div class="nk-block pt-5">
            <div class="row g-3 align-items-center">

                {{-- Pagination Summary --}}
                <div class="col-12 col-md-6">
                    <p class="text-muted mb-0 fs-14px">
                        Showing 
                        <span class="fw-medium text-dark">{{ $users->firstItem() }}</span> to 
                        <span class="fw-medium text-dark">{{ $users->lastItem() }}</span> of 
                        <span class="fw-medium text-dark">{{ $users->total() }}</span> users
                    </p>
                </div>

                {{-- Pagination Controls --}}
                <div class="col-12 col-md-6">
                    <nav aria-label="Users pagination">
                        <ul class="pagination pagination-minimal justify-content-md-end mb-0">

                            {{-- Previous Button --}}
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link"
                                href="{{ $users->previousPageUrl() }}"
                                aria-label="Previous"
                                {{ $users->onFirstPage() ? 'tabindex=-1' : '' }}>
                                    <em class="icon ni ni-chevron-left"></em>
                                    <span class="d-none d-sm-inline ms-1">Previous</span>
                                </a>
                            </li>

                            {{-- Smart Page Number Logic --}}
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

                            {{-- First Page --}}
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

                            {{-- Page Range --}}
                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            {{-- Last Page --}}
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

                            {{-- Next Button --}}
                            <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link"
                                href="{{ $users->nextPageUrl() }}"
                                aria-label="Next"
                                {{ !$users->hasMorePages() ? 'tabindex=-1' : '' }}>
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

{{--
    ======================================
    Filter Modal (Soft Color & Minimalist Design)
    ======================================
--}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            {{-- Modal Header: Minimalist Design --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fs-5 fw-semibold text-muted" id="filterModalLabel">Filter Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-3">Select the role you wish to view.</p>

                {{-- Soft, Minimalist Button Group for Roles --}}
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary role-filter-modal-btn rounded-pill text-start px-3 py-2 {{ $roleFilter === 'all' ? 'active' : '' }}" data-role-filter="all">
                        <i class="bi bi-people me-2"></i> All Users
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

            {{-- Modal Footer: Soft Background and Outline Buttons --}}
            <div class="modal-footer justify-content-end bg-light-subtle border-0 rounded-bottom-4 px-4 py-3">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill" id="applyFilterButton">
                    Apply Filter
                </button>
            </div>
        </div>
    </div>
</div>

{{--
    Delete Confirmation Modal
--}}
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                {{-- Form for deleting a user --}}
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{--
    JavaScript for interactive features
--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalFilterButtons = document.querySelectorAll('.role-filter-modal-btn');
        const applyFilterButton = document.getElementById('applyFilterButton');
        const hiddenRoleInput = document.getElementById('hiddenRoleInput');
        const filterModal = document.getElementById('filterModal');
        
        let selectedRole = '{{ $roleFilter }}';

        // 1. Filter Modal Button Handler (updates selected role)
        modalFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Clear active state from all buttons
                modalFilterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Set active state on the clicked button
                this.classList.add('active');
                
                // Update the selected role
                selectedRole = this.getAttribute('data-role-filter');
            });
        });

        // 2. Apply Filter Button Handler (submits form to server)
        applyFilterButton.addEventListener('click', function() {
            // Update hidden input and redirect to filtered page
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('role', selectedRole);
            currentUrl.searchParams.delete('page'); // Reset to first page
            currentUrl.searchParams.delete('search'); // Clear search when filtering
            window.location.href = currentUrl.toString();
        });

        // 3. Modal Open Handler (syncs selected role with current filter)
        filterModal.addEventListener('show.bs.modal', function() {
            // Sync the visual active state in the modal
            modalFilterButtons.forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-role-filter') === selectedRole);
            });
        });

        // 4. Delete Modal Handler
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const form = document.getElementById('deleteUserForm');
            form.action = `/admin/users/delete/${userId}`;
        });
    });
</script>

<style>
/* --- RESPONSIVE TABLE CSS (Card View) --- */
@media screen and (max-width: 767px) { /* Applies to screens smaller than the 'md' breakpoint */

    /* Force table elements to display as block elements */
    #usersTable,
    #usersTable tbody,
    #usersTable tr {
        display: block;
    }

    /* Hide traditional table headers */
    #usersTable thead {
        display: none;
    }

    /* Make each row look like a "card" */
    #usersTable tr {
        border: 1px solid #dee2e6;
        margin-bottom: 15px;
        border-radius: 6px;
    }

    /* Cells act like block elements with specific styling */
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

    /* Create the label using the data-label attribute */
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

    /* Remove the generated label for the Name cell */
    #usersTable td:nth-child(2)::before {
        content: none;
    }

    /* Hide the duplicated cells (Email, Phone, Address, Role) */
    #usersTable td:nth-child(3),
    #usersTable td:nth-child(4),
    #usersTable td:nth-child(5),
    #usersTable td:nth-child(6) {
        display: none !important;
    }

    /* Ensure the Name column's content is displayed correctly */
    #usersTable td:nth-child(2) {
        text-align: left;
        padding-left: 10px;
        border-bottom: none;
        padding-bottom: 5px;
    }

    /* Fix for the action button block */
    #usersTable td[data-label="Action"] {
        display: block !important;
        border-bottom: none;
        text-align: center;
        padding: 10px 0;
        padding-left: 0;
    }

    /* Hide the generated label for the action column */
    #usersTable td[data-label="Action"]::before {
        content: none;
    }

    /* Remove hover/active highlight */
    #usersTable .user-row:hover,
    #usersTable .user-row:focus,
    #usersTable .user-row:active {
        background-color: transparent !important;
        cursor: default;
    }
}

/* Custom style for the minimalist active state */
#filterModal .role-filter-modal-btn.active {
    background-color: var(--bs-primary-bg-subtle, #e0f7fa); /* Soft color for active state */
    border-color: var(--bs-primary, #0d6efd) !important;
    color: var(--bs-primary, #0d6efd) !important;
    font-weight: 600;
}
</style>

@endsection