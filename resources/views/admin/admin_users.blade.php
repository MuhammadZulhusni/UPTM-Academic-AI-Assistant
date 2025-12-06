@extends('admin.dashboard')

@section('admin') 
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">All Users</h2>
                    <p class="text-muted mb-0 d-none d-md-block">Monitor all regular users</p>
                </div>
            </div>
        </div>

        {{--
            Search bar and section header.
        --}}
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between border-bottom border-light mt-3 mt-md-5 mb-4 pb-2 gap-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0">All Users</h5>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                {{-- Search input field. The 'id="searchInput"' is crucial for the JavaScript search logic. --}}
                <div class="input-group input-group-sm flex-grow-1" style="max-width: 300px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
                </div>
            </div>
        </div>

        {{--
            Main card containing the user table.
            The `@if` directive checks if there are any users to display.
        --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($users) > 0)
                <div id="user-table-container"> 
                    {{-- The user table. `id="usersTable"` is used by the JavaScript to hide the table if no results are found. 
                        Removed 'table-hover' class to prevent highlighting on click/hover. --}}
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
                                {{-- NEW ROLE COLUMN --}}
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
                            <tr class="user-row">
                                <td class="tb-col d-none d-md-table-cell" data-label="Sl">
                                    <div class="caption-text fw-medium">{{ $key + 1 }}</div>
                                </td>
                                <td class="tb-col" data-label="Name">
                                    <div class="fs-6 fw-medium text-dark text-truncate">{{ $user->name }}</div>
                                    
                                    {{-- Mobile Details Block (Visible on small screens) --}}
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
                                {{-- NEW ROLE DATA --}}
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
                {{-- No search results message. `d-none` is the initial state, shown by JavaScript if no match is found. --}}
                <div id="no-search-results" class="text-center py-5 d-none">
                    <div class="mb-4">
                        <i class="bi bi-person-slash display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Users Found</h5>
                    <p class="text-muted mb-0">There are no users matching your search query.</p>
                </div>
                @else
                {{-- Message shown when the `$users` array is empty from the start (e.g., no users in the database). --}}
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-person-circle display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Users Found</h5>
                    <p class="text-muted mb-0">There are no users to display at the moment.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Pagination info. This is a static placeholder for "Showing X of Y entries" and a disabled pagination control. --}}
        @if(count($users) > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <div class="text-muted order-2 order-md-1">
                Showing {{ count($users) }} of {{ count($users) }} entries
            </div>
            <nav class="order-1 order-md-2">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

{{--
    Delete Confirmation Modal.
    The JavaScript will open it when the "Delete" button is clicked and dynamically update the form's action URL.
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
                {{-- Form for deleting a user. The action URL is empty initially and will be filled by JavaScript. --}}
                <form id="deleteUserForm" method="POST">
                    @csrf 
                    @method('DELETE') {{-- Specifies the HTTP method as DELETE for the form submission. --}}
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{--
    JavaScript for interactive features.
--}}
<script>
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.user-row');
        let foundUsers = false;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isMatch = text.includes(searchTerm);
            row.style.display = isMatch ? '' : 'none';
            if (isMatch) {
                foundUsers = true;
            }
        });

        const usersTable = document.getElementById('usersTable');
        const noResultsMessage = document.getElementById('no-search-results');
        const tableContainer = document.getElementById('user-table-container'); // Use the ID added for search control

        if (foundUsers) {
            if (tableContainer) tableContainer.classList.remove('d-none');
            noResultsMessage.classList.add('d-none');
        } else {
             if (tableContainer) tableContainer.classList.add('d-none');
            noResultsMessage.classList.remove('d-none');
        }
    });

    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that triggered the modal
        const button = event.relatedTarget;
        // Extract the user ID from the data-user-id attribute
        const userId = button.getAttribute('data-user-id');
        // Get the form element
        const form = document.getElementById('deleteUserForm');
        // Update the form's action URL
        form.action = `/admin/users/delete/${userId}`;
    });
</script>

<style>
/* --- RESPONSIVE TABLE CSS (Card View) --- */
@media screen and (max-width: 767px) { /* Applies to screens smaller than the 'md' breakpoint */
    
    /* 1. Force table elements to display as block elements */
    #usersTable, 
    #usersTable tbody, 
    #usersTable tr { 
        display: block; 
    }
    
    /* 2. Hide traditional table headers */
    #usersTable thead {
        display: none;
    }
    
    /* 3. Make each row look like a "card" */
    #usersTable tr { 
        border: 1px solid #dee2e6; 
        margin-bottom: 15px; /* Spacing between "cards" */
        border-radius: 6px;
    }
    
    /* 4. Cells act like block elements with specific styling */
    #usersTable td { 
        border: none;
        border-bottom: 1px solid #eee; /* Light separator between fields */
        position: relative;
        padding-left: 50%; /* Give space on the left for the label */
        text-align: right; /* Align the actual data to the right */
        min-height: 40px; /* Ensure space even for empty cells */
        padding-top: 10px;
        padding-bottom: 10px;
        white-space: normal; /* Allow text to wrap */
    }

    /* 5. Create the label using the data-label attribute */
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
    
    /* --- HIDE FIELDS THAT ARE ALREADY SHOWN IN THE 'NAME' BLOCK --- */
    
    /* Remove the generated label for the Name cell since it holds all the info */
    #usersTable td:nth-child(2)::before {
        content: none;
    }
    
    /* Hide the cells (Email, Phone, Address, Role) that are now duplicated inside the Name cell block */
    #usersTable td:nth-child(3), /* Email */
    #usersTable td:nth-child(4), /* Phone */
    #usersTable td:nth-child(5), /* Address */
    #usersTable td:nth-child(6) { /* Role */
        display: none !important;
    }
    
    /* Ensure the Name column's content is displayed correctly */
    #usersTable td:nth-child(2) {
        text-align: left;
        padding-left: 10px;
        border-bottom: none;
        padding-bottom: 5px; /* Adjust spacing */
    }

    /* 6. Fix for the action button block */
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

    /* --- REMOVE HOVER/ACTIVE HIGHLIGHT --- */
    #usersTable .user-row:hover,
    #usersTable .user-row:focus,
    #usersTable .user-row:active {
        background-color: transparent !important;
        cursor: default;
    }
}
</style>

@endsection