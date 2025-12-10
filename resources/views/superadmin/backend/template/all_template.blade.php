@extends('superadmin.dashboard')
@section('superadmin')

 <div class="nk-content-inner">
     <div class="nk-content-body">
         <div class="nk-block-head nk-page-head">
             <div class="nk-block-head-between flex-wrap g-2">
                 <div class="nk-block-head-content">
                     <h2 class="display-6">Template Library</h2>
                     <p class="text-muted">Simplify your task by using templates provided</p>
                 </div>
                 
                 {{--  MODAL TRIGGER BUTTON  --}}
                 <div class="nk-block-head-content mt-4">
                     <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#templateFilterModal">
                         <em class="icon ni ni-filter me-1"></em>
                         <span>Filter & Search</span>
                     </button>
                 </div>
             </div>
         </div>
          
        @if (auth()->user()->role === 'superadmin')
        <div class="nk-block">
            <div class="row g-3 mb-4">

                {{-- Total Templates (uses $templates->total() for the full count) --}}
                @if ($totalTemplates > 0)
                <div class="col-6 col-md-3" id="card-total-templates">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-primary text-white me-2 me-md-3">
                                    <em class="icon ni ni-template"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6">{{ $totalTemplates }}</h6>
                                    <span class="small text-muted d-block">Total Templates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Student Templates (Count is for the current page) --}}
                @if ($studentCount > 0)
                <div class="col-6 col-md-3" id="card-student-templates">
                    <div class="card border-0 bg-info bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-info text-white me-2 me-md-3">
                                    <em class="icon ni ni-user"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6">{{ $studentCount }}</h6>
                                    <span class="small text-muted d-block">Student</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Lecturer Templates (Count is for the current page) --}}
                @if ($lecturerCount > 0)
                <div class="col-6 col-md-3" id="card-lecturer-templates">
                    <div class="card border-0 bg-warning bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-warning text-white me-2 me-md-3">
                                    <em class="icon ni ni-user-check"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6">{{ $lecturerCount }}</h6>
                                    <span class="small text-muted d-block">Lecturer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Showing (uses $templates->count() for items on current page) --}}
                @if ($templates->count() > 0)
                <div class="col-6 col-md-3" id="card-showing-templates">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-success text-white me-2 me-md-3">
                                    <em class="icon ni ni-eye"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6"><span id="showing-count">{{ $templates->count() }}</span></h6>
                                    <span class="small text-muted d-block">Showing</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        @endif
        {{-- END STATS CARD BLOCK --}}

        {{-- TEMPLATE CARDS --}}
            <div class="row g-3 g-md-4 mt-2" id="templates-container">
                @foreach ($templates as $item)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 template-item"
                    data-category="{{ strtolower($item->category) }}"
                    data-title="{{ strtolower($item->title) }}"
                    data-description="{{ strtolower($item->description) }}"
                    data-created="{{ $item->created_at->timestamp }}">

                    <div class="card h-100 border-0 shadow-sm template-card">
                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="media media-md media-circle bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} bg-opacity-20 text-{{ $item->category == 'Student' ? 'primary' : 'info' }}">
                                    <img src="{{ asset('upload/template/' . $item->icon) }}"
                                        alt="Icon"
                                        class="img-fluid"
                                        style="width:22px; height:22px; object-fit:contain; border-radius:0;">
                                </div>
                                <span class="badge bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} badge-sm">
                                    {{ $item->category }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title fs-6 fw-medium mb-1">{{ $item->title }}</h5>
                                <p class="card-text text-muted small line-clamp-2 line-clamp-md-3 mb-2">{{ $item->description }}</p>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted d-flex align-items-center">
                                <em class="icon ni ni-calendar me-1"></em>
                                <span class="d-none d-sm-inline">{{ $item->created_at->format('M d, Y') }}</span>
                                <span class="d-sm-none">{{ $item->created_at->format('M d') }}</span>
                            </small>

                            <div class="d-flex gap-1">
                                <a href="{{ route('superadmin.details.template', $item->id) }}" class="btn btn-sm btn-icon btn-light" title="View" data-bs-toggle="tooltip">
                                    <em class="icon ni ni-eye"></em>
                                </a>
                                <a href="{{ route('superadmin.edit.template', $item->id) }}" class="btn btn-sm btn-icon btn-primary" title="Edit" data-bs-toggle="tooltip">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <button class="btn btn-sm btn-icon btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-template-id="{{ $item->id }}"
                                        title="Delete Template">
                                    <em class="icon ni ni-trash" title="Delete" data-bs-toggle="tooltip"></em>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> 
                @endforeach
            </div> 
            {{-- MODERN MINIMALIST PAGINATION --}}
            @if ($totalTemplates > 0)
            <div class="nk-block pt-5">
                <div class="row g-3 align-items-center">
                    {{-- Pagination Summary --}}
                    <div class="col-12 col-md-6">
                        <p class="text-muted mb-0 fs-14px">
                            Showing <span class="fw-medium text-dark">{{ $templates->firstItem() }}</span> to 
                            <span class="fw-medium text-dark">{{ $templates->lastItem() }}</span> of 
                            <span class="fw-medium text-dark">{{ $templates->total() }}</span> templates
                        </p>
                    </div>
                    
                    {{-- Pagination Controls --}}
                    <div class="col-12 col-md-6">
                        <nav aria-label="Template pagination">
                            <ul class="pagination pagination-minimal justify-content-md-end mb-0">
                                {{-- Previous Button --}}
                                <li class="page-item {{ $templates->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" 
                                    href="{{ $templates->previousPageUrl() }}" 
                                    aria-label="Previous"
                                    {{ $templates->onFirstPage() ? 'tabindex=-1' : '' }}>
                                        <em class="icon ni ni-chevron-left"></em>
                                        <span class="d-none d-sm-inline ms-1">Previous</span>
                                    </a>
                                </li>

                                {{-- Page Numbers (Smart Display) --}}
                                @php
                                    $currentPage = $templates->currentPage();
                                    $lastPage = $templates->lastPage();
                                    $start = max(1, $currentPage - 1);
                                    $end = min($lastPage, $currentPage + 1);
                                    
                                    // Adjust if at edges
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
                                        <a class="page-link" href="{{ $templates->url(1) }}">1</a>
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
                                        <a class="page-link" href="{{ $templates->url($page) }}">{{ $page }}</a>
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
                                        <a class="page-link" href="{{ $templates->url($lastPage) }}">{{ $lastPage }}</a>
                                    </li>
                                @endif

                                {{-- Next Button --}}
                                <li class="page-item {{ !$templates->hasMorePages() ? 'disabled' : '' }}">
                                    <a class="page-link" 
                                    href="{{ $templates->nextPageUrl() }}" 
                                    aria-label="Next"
                                    {{ !$templates->hasMorePages() ? 'tabindex=-1' : '' }}>
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
            {{-- NO RESULTS BLOCK (Only show if there are NO results) --}}
            @if ($templates->total() === 0)
            <div class="text-center py-5" id="no-results">
                <div class="media media-xl media-circle bg-light text-muted mx-auto mb-3" style="width: 100px; height: 100px;">
                    <em class="icon ni ni-search" style="font-size: 2rem;"></em>
                </div>
                <h4 class="text-muted mb-2">No templates found</h4>
                <p class="text-muted">Try adjusting your search terms or filter selection.</p>
                {{-- 'resetFilters()' button is now a link/redirect to the clean base route --}}
                <a href="{{ route('superadmin.template') }}" class="btn btn-outline-primary btn-sm">Reset Filters</a>
            </div>
            @endif
        </div> 
    </div> 

{{-- FILTER MODAL (MODIFIED FOR BACKEND PAGINATION/FILTERING) --}}
<div class="modal fade" id="templateFilterModal" tabindex="-1" aria-labelledby="templateFilterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      {{-- 1. WRAP IN GET FORM: Action points to the base route (replace with your actual route name) --}}
      <form id="templateFilterForm" action="{{ route('superadmin.template') }}" method="GET">
      <div class="modal-header">
        <h5 class="modal-title" id="templateFilterModalLabel">Filter Templates</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        {{-- HIDDEN INPUT to hold the selected Category value --}}
        <input type="hidden" id="category_hidden" name="category" value="{{ request('category', 'all') }}">

        {{-- HIDDEN INPUT to hold the selected Sort value --}}
        <input type="hidden" id="sort_hidden" name="sort" value="{{ request('sort', 'newest') }}">
        
        <div class="mb-4">
            <h6 class="text-muted mb-2">Search by Title or Description</h6>
             <div class="form-group mb-0">
                 <div class="form-control-wrap">
                     <div class="form-control-icon start text-light">
                         <em class="icon ni ni-search"></em>
                     </div>
                     {{-- 2. ADD NAME ATTRIBUTE AND SET CURRENT VALUE --}}
                     <input type="text" 
                            class="form-control" 
                            placeholder="Search templates..." 
                            id="searchInput" 
                            name="search" 
                            value="{{ request('search') }}">
                 </div>
             </div>
        </div>

        @if (auth()->user()->role === 'superadmin')
        <div class="mb-4">
            <h6 class="text-muted mb-2">Category</h6>
            {{-- Category Buttons --}}
            <div class="btn-group category-segment-control w-100" role="group" id="categoryFilter">
                {{-- 3. APPLY ACTIVE CLASS BASED ON CURRENT REQUEST --}}
                <button type="button" class="btn btn-outline-primary segment-btn {{ request('category', 'all') == 'all' ? 'active' : '' }}" data-value="all">
                    <em class="icon ni ni-grid-sq me-1"></em> All
                </button>
                <button type="button" class="btn btn-outline-primary segment-btn {{ request('category') == 'student' ? 'active' : '' }}" data-value="student">
                    <em class="icon ni ni-user me-1"></em> Student
                </button>
                <button type="button" class="btn btn-outline-primary segment-btn {{ request('category') == 'lecturer' ? 'active' : '' }}" data-value="lecturer">
                    <em class="icon ni ni-user-check me-1"></em> Lecturer
                </button>
            </div>
        </div>
        @endif

        <div class="mb-0">
             <h6 class="text-muted mb-2">Sort By</h6>
             {{-- Sort Buttons --}}
             <div class="btn-group sort-segment-control w-100" role="group" id="sortFilter">
                 {{-- 3. APPLY ACTIVE CLASS BASED ON CURRENT REQUEST --}}
                 <button type="button" class="btn btn-outline-info segment-btn {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}" data-value="newest">
                     <em class="icon ni ni-clock me-1"></em> Newest
                 </button>
                 <button type="button" class="btn btn-outline-info segment-btn {{ request('sort') == 'title' ? 'active' : '' }}" data-value="title">
                     <em class="icon ni ni-text me-1"></em> Title (A-Z)
                 </button>
                 <button type="button" class="btn btn-outline-info segment-btn {{ request('sort') == 'title-desc' ? 'active' : '' }}" data-value="title-desc">
                     <em class="icon ni ni-text-a me-1"></em> Title (Z-A)
                 </button>
             </div>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        {{-- Reset button now redirects to the clean base route --}}
        <button type="button" class="btn btn-outline-light" onclick="window.location.href = '{{ route('superadmin.template') }}'">Reset Filters</button>
        {{-- Changed to submit button to activate the form --}}
        <button type="submit" class="btn btn-primary">Apply & View</button>
      </div>
      </form>
    </div>
  </div>
</div>
{{-- END FILTER MODAL --}}

{{-- REQUIRED JAVASCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to handle segment button clicks and update the hidden input
        function setupSegmentControls(controlId, hiddenInputId) {
            const controls = document.getElementById(controlId);
            const hiddenInput = document.getElementById(hiddenInputId);

            if (!controls || !hiddenInput) return;

            controls.addEventListener('click', function(e) {
                const target = e.target.closest('.segment-btn');
                if (target) {
                    // Remove 'active' from all siblings
                    controls.querySelectorAll('.segment-btn').forEach(btn => btn.classList.remove('active'));
                    // Add 'active' to the clicked button
                    target.classList.add('active');
                    // Update the value of the hidden input
                    hiddenInput.value = target.dataset.value;
                }
            });
        }

        // Apply setup to both category and sort filters
        setupSegmentControls('categoryFilter', 'category_hidden');
        setupSegmentControls('sortFilter', 'sort_hidden');
    });
</script>

<style>
.media-circle {
    border-radius: 50% !important; 
    display: flex;
    align-items: center;
    justify-content: center;
}

.media-md .icon {
    font-size: 1.25rem; 
}

.media-sm .icon {
    font-size: 1rem;
}

/* --- Other Styles --- */
.category-segment-control .btn,
.sort-segment-control .btn {
    border-radius: 8px !important;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 0.5rem 0.9rem;
    flex: 1 1 auto;
}

.category-segment-control,
.sort-segment-control {
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.category-segment-control .segment-btn.active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.sort-segment-control .segment-btn.active {
    background-color: var(--bs-info);
    border-color: var(--bs-info);
    color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.btn-group > .btn.segment-btn:not(:last-child):not(.dropdown-toggle),
.btn-group > .btn.segment-btn:not(:first-child) {
    border-radius: 0;
    margin-left: -1px;
}

.btn-group > .btn.segment-btn:first-child {
    border-top-left-radius: 8px !important;
    border-bottom-left-radius: 8px !important;
}
.btn-group > .btn.segment-btn:last-child {
    border-top-right-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
}

.modal-content {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.modal-header {
    border-bottom: none;
    padding: 1.5rem;
}

.modal-body {
    padding: 0 1.5rem;
}

.modal-footer {
    border-top: none;
    padding: 1.5rem;
}

.template-item {
    transition: opacity 0.3s ease, transform 0.3s ease;
    opacity: 1;
    transform: translateZ(0) scale(1);
    will-change: opacity, transform;
}

.template-item.hiding {
    opacity: 0;
    transform: translateZ(0) scale(0.95);
    pointer-events: none;
}

.template-item.hidden {
    display: none !important;
}

@keyframes sortComplete {
    0% { transform: translateZ(0) scale(1) translateY(0); }
    50% { transform: translateZ(0) scale(1.02) translateY(-4px); }
    100% { transform: translateZ(0) scale(1) translateY(0); }
}

.template-item.sort-complete {
    animation: sortComplete 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.card {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 12px;
    overflow: hidden;
    transform: translateZ(0);
}

.card:hover {
    transform: translateZ(0) translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.card.bg-primary.bg-opacity-10,
.card.bg-info.bg-opacity-10,
.card.bg-warning.bg-opacity-10,
.card.bg-success.bg-opacity-10 {
    transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.3s ease;
    will-change: transform, opacity;
}

.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 767.98px) {
    .category-segment-control .btn,
    .sort-segment-control .btn {
        padding: 0.5rem 0.5rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}

/* Modern Minimalist Pagination Styles */
.pagination-minimal {
    gap: 0.375rem;
}

.pagination-minimal .page-link {
    border: 1px solid #e5e9f2;
    color: #526484;
    padding: 0.5rem 0.875rem;
    border-radius: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background-color: #fff;
    display: flex;
    align-items-center;
}

.pagination-minimal .page-link:hover {
    background-color: #f5f6fa;
    border-color: #d4dae6;
    color: #364a63;
    transform: translateY(-1px);
}

.pagination-minimal .page-item.active .page-link {
    background-color: #6576ff;
    border-color: #6576ff;
    color: #fff;
    box-shadow: 0 2px 6px rgba(101, 118, 255, 0.3);
}

.pagination-minimal .page-item.disabled .page-link {
    background-color: transparent;
    border-color: #e5e9f2;
    color: #c4cefe;
    opacity: 0.6;
}

.pagination-minimal .page-link em {
    font-size: 1rem;
}

/* Responsive adjustments */
@media (max-width: 575.98px) {
    .pagination-minimal {
        gap: 0.25rem;
    }
    
    .pagination-minimal .page-link {
        padding: 0.425rem 0.625rem;
        font-size: 0.8125rem;
    }
    
    .pagination-minimal .page-item:not(:first-child):not(:last-child):not(.active) {
        display: none;
    }
}

/* Smooth transitions */
.pagination-minimal .page-link,
.pagination-minimal .page-item.active .page-link {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

{{-- DELETE CONFIRMATION MODAL --}}

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
                <p class="text-muted">Do you really want to delete this template? This process cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTemplateForm" method="POST">
                    @csrf
                    {{-- Use GET method for simplicity, or change route to POST/DELETE --}}
                    {{-- @method('DELETE') --}}
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get the delete confirmation modal element
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');

        // Check if the modal exists on the page
        if (confirmDeleteModal) {
            // Add a listener for when the modal is about to be shown
            confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
                // Get the button that triggered the modal
                const button = event.relatedTarget;
                
                // Extract the template ID from the button's data attribute
                const templateId = button.getAttribute('data-template-id');
                
                // Get the form element inside the modal
                const form = document.getElementById('deleteTemplateForm');
                
                // Update the form's action URL with the correct template ID
                // Make sure this route matches your web.php file
                form.action = `/superadmin/templates/delete/${templateId}`;
            });
        }
    });
</script>

@endsection