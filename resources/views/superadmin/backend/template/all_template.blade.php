@extends('superadmin.dashboard')
@section('superadmin')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

 <div class="nk-content-inner">
     <div class="nk-content-body">
         <div class="nk-block-head nk-page-head">
             <div class="nk-block-head-between flex-wrap g-2">
                 <div class="nk-block-head-content">
                     <h2 class="display-6">Template Library</h2>
                     <p class="text-muted">Simplify your task by using templates provided</p>
                 </div>
                 
                 {{--  FILTER CONTROLS  --}}
                 <div class="nk-block-head-content mt-4">
                     <div class="d-flex gap-2 align-items-center flex-wrap">
                         {{-- Filter Button --}}
                         <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#templateFilterModal">
                             <em class="icon ni ni-filter me-1"></em>
                             <span>Filter & Search</span>
                         </button>
                         
                         {{-- Reset Button (only show when filters are active) --}}
                         @php
                             $hasFilters = request('search') || 
                                          (request('category') && request('category') !== 'all') || 
                                          (request('status') && request('status') !== 'all') || 
                                          (request('sort') && request('sort') !== 'newest');
                         @endphp
                     </div>
                 </div>
             </div>
         </div>

        {{-- ACTIVE FILTERS SECTION --}}
        @if($hasFilters)
        <div class="nk-block mt-3">
            <div class="active-filters-professional">
                <div class="filters-header-pro">
                    <div class="filters-title">
                        <em class="icon ni ni-filter-fill"></em>
                        <span>{{ collect([request('search'), request('category') != 'all' ? request('category') : null, request('status') != 'all' ? request('status') : null, request('sort') != 'newest' ? request('sort') : null])->filter()->count() }} Active Filter(s)</span>
                    </div>
                    <a href="{{ route('superadmin.template') }}" class="btn-clear-filters">
                        <em class="icon ni ni-cross-circle"></em>
                        <span>Clear All</span>
                    </a>
                </div>
                
                <div class="filters-body-pro">
                    {{-- Search Filter --}}
                    @if(request('search'))
                    <div class="filter-tag-pro">
                        <div class="filter-tag-label">Search</div>
                        <div class="filter-tag-value">{{ request('search') }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="filter-tag-remove" title="Remove search filter">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    @endif
                    
                    {{-- Category Filter --}}
                    @if(request('category') && request('category') !== 'all')
                    <div class="filter-tag-pro">
                        <div class="filter-tag-label">Category</div>
                        <div class="filter-tag-value">{{ ucfirst(request('category')) }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['category' => 'all']) }}" class="filter-tag-remove" title="Remove category filter">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    @endif
                    
                    {{-- Status Filter --}}
                    @if(request('status') && request('status') !== 'all')
                    <div class="filter-tag-pro">
                        <div class="filter-tag-label">Status</div>
                        <div class="filter-tag-value">{{ ucfirst(request('status')) }}</div>
                        <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="filter-tag-remove" title="Remove status filter">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    @endif
                    
                    {{-- Sort Filter --}}
                    @if(request('sort') && request('sort') !== 'newest')
                    <div class="filter-tag-pro">
                        <div class="filter-tag-label">Sort</div>
                        <div class="filter-tag-value">
                            @if(request('sort') === 'oldest')
                                Oldest
                            @elseif(request('sort') === 'title')
                                A-Z
                            @elseif(request('sort') === 'title-desc')
                                Z-A
                            @else
                                {{ ucfirst(request('sort')) }}
                            @endif
                        </div>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" class="filter-tag-remove" title="Remove sort filter">
                            <em class="icon ni ni-cross"></em>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
          
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

        {{-- =========================
            TEMPLATE CARDS + ACTIONS
        ========================= --}}
        <div class="row g-3 g-md-4 mt-2" id="templates-container">
            @foreach ($templates as $item)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 template-item"
                data-category="{{ strtolower($item->category) }}"
                data-title="{{ strtolower($item->title) }}"
                data-description="{{ strtolower($item->description) }}"
                data-created="{{ $item->created_at->timestamp }}">

                <div class="card h-100 shadow-sm template-card">
                    <div class="card-body p-4">
                        
                        {{-- TOP: ICON & CATEGORY --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="rounded-circle bg-light p-3">
                                <img src="{{ asset('upload/template/' . $item->icon) }}"
                                    alt="{{ $item->title }}"
                                    width="32"
                                    height="32"
                                    style="object-fit:contain;">
                            </div>
                            <span class="badge rounded-pill bg-{{ $item->category == 'Student' ? 'primary' : 'info' }}">
                                {{ $item->category }}
                            </span>
                        </div>

                        {{-- TITLE --}}
                        <h5 class="fw-bold mb-2">{{ $item->title }}</h5>

                        {{-- DESCRIPTION --}}
                        <p class="text-muted small mb-3" style="min-height: 60px;">
                            {{ Str::limit($item->description, 80) }}
                        </p>

                        {{-- STATUS & DATE --}}
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <span class="badge bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <small class="text-muted">
                                {{ $item->created_at->format('M d, Y') }}
                            </small>
                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.details.template', $item->id) }}"
                                class="btn btn-sm btn-outline-secondary flex-fill">
                                <em class="icon ni ni-eye"></em>
                            </a>

                            <a href="{{ route('superadmin.edit.template', $item->id) }}"
                                class="btn btn-sm btn-primary flex-fill">
                                <em class="icon ni ni-edit"></em>
                            </a>

                            <button type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#statusModal"
                                data-template-id="{{ $item->id }}"
                                data-template-status="{{ $item->is_active ? 1 : 0 }}">
                                <em class="icon ni ni-repeat"></em>
                            </button>

                            <button type="button"
                                class="btn btn-sm btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmDeleteModal"
                                data-template-id="{{ $item->id }}">
                                <em class="icon ni ni-trash"></em>
                            </button>
                        </div>
                    </div>
                </div>
            </div> 
            @endforeach
        </div>

        {{-- =========================
            STATUS CHANGE MODAL
        ========================= --}}
        <div class="modal fade" id="statusModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="statusForm" method="POST">
                        @csrf

                        <div class="modal-body text-center px-5 pt-5 pb-2">
                            <div class="text-warning mb-4">
                                <em class="icon ni ni-alert-circle-fill" style="font-size: 64px;"></em>
                            </div>
                            <h4 class="mb-3">Change Status?</h4>
                            <p id="statusModalText" class="text-muted mb-0"></p>
                        </div>

                        <div class="modal-footer justify-content-center pt-3 pb-4 border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
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

        {{-- HIDDEN INPUT to hold the selected Status value --}}
        <input type="hidden" id="status_hidden" name="status" value="{{ request('status', 'all') }}">

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

        {{-- NEW: STATUS FILTER --}}
        <div class="mb-4">
            <h6 class="text-muted mb-2">Status</h6>
            <div class="btn-group status-segment-control w-100" role="group" id="statusFilter">
                <button type="button" class="btn btn-outline-success segment-btn {{ request('status', 'all') == 'all' ? 'active' : '' }}" data-value="all">
                    <em class="icon ni ni-check-circle me-1"></em> All
                </button>
                <button type="button" class="btn btn-outline-success segment-btn {{ request('status') == 'active' ? 'active' : '' }}" data-value="active">
                    <em class="icon ni ni-check me-1"></em> Active
                </button>
                <button type="button" class="btn btn-outline-success segment-btn {{ request('status') == 'inactive' ? 'active' : '' }}" data-value="inactive">
                    <em class="icon ni ni-cross me-1"></em> Inactive
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

        // Apply setup to category, status, and sort filters
        setupSegmentControls('categoryFilter', 'category_hidden');
        setupSegmentControls('statusFilter', 'status_hidden');
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

/* Filter Badge Styles - High Contrast for Active Filters */
.badge.bg-primary {
    background-color: #0d6efd !important;
}

.badge.bg-info {
    background-color: #0dcaf0 !important;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
}
.badge {
    font-weight: 500;
    border-radius: 6px;
}

/* --- Other Styles --- */
.category-segment-control .btn,
.sort-segment-control .btn,
.status-segment-control .btn {
    border-radius: 8px !important;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 0.5rem 0.9rem;
    flex: 1 1 auto;
}

.category-segment-control,
.sort-segment-control,
.status-segment-control {
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

.status-segment-control .segment-btn.active {
    background-color: var(--bs-success);
    border-color: var(--bs-success);
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
    .sort-segment-control .btn,
    .status-segment-control .btn {
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
    align-items: center;
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

.template-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}

.template-card .btn {
    transition: all 0.2s ease;
}

.template-card .btn:hover {
    transform: scale(1.05);
}

/* Professional Active Filters Design */
.active-filters-professional {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    animation: slideDown 0.3s ease-out;
}

.filters-header-pro {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.filters-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.filters-title em {
    font-size: 1rem;
    color: #6366f1;
}

.btn-clear-filters {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    background: transparent;
    color: #dc2626;
    font-size: 0.8125rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid #fecaca;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.btn-clear-filters:hover {
    background: #fef2f2;
    border-color: #dc2626;
    color: #991b1b;
}

.btn-clear-filters em {
    font-size: 0.875rem;
}

.filters-body-pro {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}

.filter-tag-pro {
    display: inline-flex;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.filter-tag-pro:hover {
    border-color: #94a3b8;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.filter-tag-label {
    padding: 0.5rem 0.75rem;
    background: #e2e8f0;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    border-right: 1px solid #cbd5e1;
}

.filter-tag-value {
    padding: 0.5rem 0.75rem;
    color: #1e293b;
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.filter-tag-remove {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 100%;
    background: transparent;
    color: #94a3b8;
    border-left: 1px solid #cbd5e1;
    text-decoration: none;
    transition: all 0.2s ease;
}

.filter-tag-remove:hover {
    background: #fee2e2;
    color: #dc2626;
}

.filter-tag-remove em {
    font-size: 0.875rem;
}

/* Animation */
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

/* Responsive Design */
@media (max-width: 768px) {
    .filters-header-pro {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
    }
    
    .btn-clear-filters {
        width: 100%;
        justify-content: center;
    }
    
    .filters-body-pro {
        padding: 0.875rem 1rem 1rem;
    }
    
    .filter-tag-pro {
        width: 100%;
    }
    
    .filter-tag-value {
        flex: 1;
        max-width: none;
    }
}

/* Ensure compatibility with existing styles */
.nk-block {
    position: relative;
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
                <h5 class="fw-bold mb-4">Are you sure?</h5>
                <p class="text-muted">Do you really want to delete this template? This process cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center mt-1 border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteTemplateForm" method="POST">
                    @csrf
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

{{-- =========================
     JAVASCRIPT: wire up modals  set form actions & text
   ========================= --}}
<script>
    // STATUS modal handler
    const statusModalEl = document.getElementById('statusModal');
    if (statusModalEl) {
        statusModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const templateId = button.getAttribute('data-template-id');
            const status = button.getAttribute('data-template-status'); // "1" or "0"
            const form = document.getElementById('statusForm');

            // Set action - adjust route path if your route is different
            form.action = `/superadmin/template/toggle-status/${templateId}`;

            // Update modal text contextually
            const textEl = document.getElementById('statusModalText');
            if (String(status) === '1') {
                textEl.textContent = 'Are you sure you want to deactivate this template?';
            } else {
                textEl.textContent = 'Are you sure you want to activate this template?';
            }
        });
    }
</script>


@endsection