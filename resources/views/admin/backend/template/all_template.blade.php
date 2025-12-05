@extends('admin.dashboard')
@section('admin')

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
          
        @if (auth()->user()->role === 'admin')
        <div class="nk-block">
            <div class="row g-3 mb-4">

                {{-- Total Templates --}}
                @if (count($templates) > 0)
                <div class="col-6 col-md-3" id="card-total-templates">
                    <div class="card border-0 bg-primary bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-primary text-white me-2 me-md-3">
                                    <em class="icon ni ni-template"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6">{{ count($templates) }}</h6>
                                    <span class="small text-muted d-block">Total Templates</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Student Templates --}}
                @php $studentCount = $templates->where('category', 'Student')->count(); @endphp
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

                {{-- Lecturer Templates --}}
                @php $lecturerCount = $templates->where('category', 'Lecturer')->count(); @endphp
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

                {{-- Showing --}}
                @if (count($templates) > 0)
                <div class="col-6 col-md-3" id="card-showing-templates">
                    <div class="card border-0 bg-success bg-opacity-10">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center">
                                <div class="media media-sm media-circle bg-success text-white me-2 me-md-3">
                                    <em class="icon ni ni-eye"></em>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 fs-6"><span id="showing-count">{{ count($templates) }}</span></h6>
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
            <div class="row g-3 g-md-4" id="templates-container">
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
                                <a href="{{ route('details.template', $item->id) }}" class="btn btn-sm btn-icon btn-light">
                                    <em class="icon ni ni-eye"></em>
                                </a>
                                <a href="{{ route('edit.template', $item->id) }}" class="btn btn-sm btn-icon btn-primary">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <button class="btn btn-sm btn-icon btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal"
                                        data-template-id="{{ $item->id }}"
                                        title="Delete Template">
                                    <em class="icon ni ni-trash"></em>
                                </button>
                            </div>
                        </div>
                    </div>
                </div> 
                @endforeach
            </div> 


        <div class="text-center py-5 d-none" id="no-results">
             <div class="media media-xl media-circle bg-light text-muted mx-auto mb-3" style="width: 100px; height: 100px;">
                 <em class="icon ni ni-search" style="font-size: 2rem;"></em>
             </div>
             <h4 class="text-muted mb-2">No templates found</h4>
             <p class="text-muted">Try adjusting your search terms or filter selection.</p>
             <button class="btn btn-outline-primary btn-sm" onclick="resetFilters()">Reset Filters</button>
         </div>

     </div>
 </div>

{{-- FILTER MODAL  --}}
<div class="modal fade" id="templateFilterModal" tabindex="-1" aria-labelledby="templateFilterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="templateFilterModalLabel">Filter Templates</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <div class="mb-4">
            <h6 class="text-muted mb-2">Search by Title or Description</h6>
             <div class="form-group mb-0">
                 <div class="form-control-wrap">
                     <div class="form-control-icon start text-light">
                         <em class="icon ni ni-search"></em>
                     </div>
                     <input type="text" class="form-control" placeholder="Search templates..." id="searchInput">
                 </div>
             </div>
        </div>

        @if (auth()->user()->role === 'admin')
        <div class="mb-4">
            <h6 class="text-muted mb-2">Category</h6>
            <div class="btn-group category-segment-control w-100" role="group" id="categoryFilter">
                <button type="button" class="btn btn-outline-primary segment-btn active" data-value="all">
                    <em class="icon ni ni-grid-sq me-1"></em> All
                </button>
                <button type="button" class="btn btn-outline-primary segment-btn" data-value="student">
                    <em class="icon ni ni-user me-1"></em> Student
                </button>
                <button type="button" class="btn btn-outline-primary segment-btn" data-value="lecturer">
                    <em class="icon ni ni-user-check me-1"></em> Lecturer
                </button>
            </div>
        </div>
        @endif

        <div class="mb-0">
             <h6 class="text-muted mb-2">Sort By</h6>
             <div class="btn-group sort-segment-control w-100" role="group" id="sortFilter">
                 <button type="button" class="btn btn-outline-info segment-btn active" data-value="newest">
                     <em class="icon ni ni-clock me-1"></em> Newest
                 </button>
                 <button type="button" class="btn btn-outline-info segment-btn" data-value="title">
                     <em class="icon ni ni-text me-1"></em> Title (A-Z)
                 </button>
                 <button type="button" class="btn btn-outline-info segment-btn" data-value="title-desc">
                     <em class="icon ni ni-text-a me-1"></em> Title (Z-A)
                 </button>
             </div>
        </div>

      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-outline-light" onclick="resetFilters()">Reset Filters</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Apply & Close</button>
      </div>
    </div>
  </div>
</div>
{{-- END FILTER MODAL  --}}


<style>
/* FIX: Ensure perfect circle and center icons for all media elements  */
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
</style>

<script>
(function() {
    'use strict';
    
    const authUserRole = "{{ auth()->user()->role }}";
    const raf = window.requestAnimationFrame || ((fn) => setTimeout(fn, 16));
    
    document.addEventListener('DOMContentLoaded', function() {
        // Retrieve the modal instance
        const filterModalElement = document.getElementById('templateFilterModal');
        const filterModal = filterModalElement ? new bootstrap.Modal(filterModalElement) : null;

        const elements = {
            categoryFilterGroup: document.getElementById('categoryFilter'),
            sortFilterGroup: document.getElementById('sortFilter'),
            searchInput: document.getElementById('searchInput'),
            noResults: document.getElementById('no-results'),
            showingCount: document.getElementById('showing-count'),
            cards: {
                total: document.getElementById('card-total-templates'),
                student: document.getElementById('card-student-templates'),
                lecturer: document.getElementById('card-lecturer-templates'),
                showing: document.getElementById('card-showing-templates')
            }
        };
        
        const templateItems = document.querySelectorAll('.template-item');
        let allTemplates = Array.from(templateItems);
        let filterTimeout = null;
        let isAnimating = false;
        
        let currentCategory = 'all';
        let currentSortBy = 'newest';

        function setupSegmentControl(groupElement, initialValue, callback) {
            groupElement.querySelectorAll('.segment-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newValue = this.getAttribute('data-value');
                    const currentStateVariable = groupElement.id === 'categoryFilter' ? currentCategory : currentSortBy;
                    if (newValue !== currentStateVariable) {
                        groupElement.querySelector('.segment-btn.active')?.classList.remove('active');
                        this.classList.add('active');
                        callback(newValue);
                    }
                });
            });
            groupElement.querySelector(`[data-value="${initialValue}"]`)?.classList.add('active');
        }

        if (elements.categoryFilterGroup) {
            setupSegmentControl(elements.categoryFilterGroup, 'all', (value) => {
                currentCategory = value;
                applyFilters(); 
            });
        }
        
        if (elements.sortFilterGroup) {
            setupSegmentControl(elements.sortFilterGroup, 'newest', (value) => {
                currentSortBy = value;
                applyFilters();
            });
        }
        
        function updateCardVisibility(category, hasResults) {
            if (authUserRole !== 'admin') return;
            const cards = elements.cards;
            const updates = {};
            if (!hasResults) {
                updates.total = false; updates.student = false; updates.lecturer = false; updates.showing = false;
            } else {
                updates.total = true; updates.showing = true;
                if (category === 'student') { updates.student = true; updates.lecturer = false; } 
                else if (category === 'lecturer') { updates.student = false; updates.lecturer = true; } 
                else { updates.student = true; updates.lecturer = true; }
            }
            raf(() => {
                Object.keys(updates).forEach(key => {
                    const card = cards[key];
                    if (card) { card.style.display = updates[key] ? '' : 'none'; }
                });
            });
        }
        
        function applyFilters() {
            if (isAnimating) return;
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                isAnimating = true;
                const category = currentCategory;
                const sortBy = currentSortBy;
                const searchTerm = elements.searchInput.value.toLowerCase().trim();
                
                let visibleTemplates = allTemplates.filter(item => {
                    const itemCategory = item.dataset.category.toLowerCase();
                    const matchesCategory = category === 'all' || itemCategory === category;
                    if (!searchTerm) return matchesCategory;
                    const itemTitle = item.dataset.title;
                    const itemDescription = item.dataset.description;
                    return matchesCategory && (itemTitle.includes(searchTerm) || itemDescription.includes(searchTerm));
                });
                
                visibleTemplates.sort((a, b) => {
                    switch(sortBy) {
                        case 'newest': return parseInt(b.dataset.created) - parseInt(a.dataset.created);
                        case 'oldest': return parseInt(a.dataset.created) - parseInt(b.dataset.created);
                        case 'title': return a.dataset.title.localeCompare(b.dataset.title);
                        case 'title-desc': return b.dataset.title.localeCompare(a.dataset.title);
                        default: return 0;
                    }
                });
                
                const hasResults = visibleTemplates.length > 0;
                
                raf(() => {
                    allTemplates.forEach(item => { item.classList.add('hiding'); });
                    setTimeout(() => {
                        raf(() => {
                            allTemplates.forEach(item => {
                                const isVisible = visibleTemplates.includes(item);
                                if (isVisible) {
                                    item.classList.remove('hiding', 'hidden');
                                    item.style.order = visibleTemplates.indexOf(item);
                                } else {
                                    item.classList.add('hidden');
                                }
                            });
                            if (elements.showingCount) { elements.showingCount.textContent = visibleTemplates.length; }
                            updateCardVisibility(category, hasResults);
                            elements.noResults.classList.toggle('d-none', hasResults);
                            if (hasResults) {
                                visibleTemplates.forEach((item, index) => {
                                    setTimeout(() => {
                                        item.classList.add('sort-complete');
                                        setTimeout(() => { item.classList.remove('sort-complete'); }, 500);
                                    }, index * 30);
                                });
                            }
                            isAnimating = false;
                        });
                    }, 300);
                });
            }, 150);
        }
        
        let searchTimeout;
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 300);
            }, { passive: true });
        }
        
        window.resetFilters = function() {
            if (elements.categoryFilterGroup) { elements.categoryFilterGroup.querySelector('[data-value="all"]').click(); }
            if (elements.sortFilterGroup) { elements.sortFilterGroup.querySelector('[data-value="newest"]').click(); }
            if (elements.searchInput) { elements.searchInput.value = ''; }
            applyFilters();
            if (filterModal) { filterModal.hide(); }
        };
        
        templateItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
        });
        
        raf(() => {
            templateItems.forEach((item, index) => {
                setTimeout(() => {
                    raf(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    });
                }, index * 50);
            });
        });
    });
})();
</script>

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
                form.action = `/admin/templates/delete/${templateId}`;
            });
        }
    });
</script>

@endsection