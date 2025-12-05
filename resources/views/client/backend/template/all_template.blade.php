@extends('client.client_dashboard')
@section('client') 

 <div class="nk-content-inner">
     <div class="nk-content-body">

        <div class="nk-block-head nk-page-head mb-4">
             <div class="nk-block-head-between flex-wrap g-2">
                 <div class="nk-block-head-content">
                     <h2 class="display-6">Template Library</h2>
                     <p class="text-muted">Simplify your task by using templates provided</p>
                 </div>
                 
                 {{-- MODAL TRIGGER BUTTON --}}
                 <div class="nk-block-head-content">
                     <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#templateFilterModal">
                         <em class="icon ni ni-filter me-1"></em>
                         <span>Filter & Search</span>
                     </button>
                 </div>
             </div>
        </div>

            <div class="row g-3 g-md-4" id="templates-container">
                @forelse ($templates as $item)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 template-item"
                    data-category="{{ strtolower($item->category) }}"
                    data-title="{{ strtolower($item->title) }}"
                    data-description="{{ strtolower($item->description) }}"
                    data-created="{{ $item->created_at->timestamp }}">

                    <div class="card h-100 border-0 shadow-sm template-card">
                        <div class="card-body p-3 p-md-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <a href="{{ route('user.details.template',$item->id) }}" class="text-decoration-none">
                                <div class="media media-md media-circle bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} bg-opacity-20 text-{{ $item->category == 'Student' ? 'primary' : 'info' }}">
                                    <img src="{{ asset('upload/template/' . $item->icon) }}"
                                        alt="Icon"
                                        class="img-fluid"
                                        style="width:22px; height:22px; object-fit:contain; border-radius:0;">
                                </div>
                                </a>
                                <span class="badge bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} badge-sm">
                                    {{ $item->category }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <a href="{{ route('user.details.template',$item->id) }}" class="text-decoration-none">
                                    <h5 class="card-title fs-6 fs-md-5 fw-medium mb-2 text-dark">{{ $item->title }}</h5>
                                    <p class="card-text text-muted small line-clamp-2 line-clamp-md-3 mb-3">{{ $item->description }}</p>
                                </a>
                            </div>
                            <div class="template-footer mt-auto">
                                <small class="text-muted d-flex align-items-center">
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <span class="d-none d-sm-inline">{{ $item->created_at->format('M d, Y') }}</span>
                                    <span class="d-sm-none">{{ $item->created_at->format('M d') }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                     <div class="text-center py-5">
                        <div class="media media-xl media-circle bg-light text-muted mx-auto mb-3" style="width: 100px; height: 100px;">
                            <em class="icon ni ni-search" style="font-size: 2rem;"></em>
                        </div>
                        <h4 class="text-muted mb-2">No templates available</h4>
                        <p class="text-muted">There are currently no templates matched for your filter or none have been created yet.</p>
                    </div>
                </div>
                @endforelse
            </div>
            
            {{-- Used for JavaScript filter results --}}
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


{{--  FILTER MODAL  --}}
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
{{--  END FILTER MODAL  --}}

<style>
.media-circle {
    border-radius: 50% !important; 
    /* Centers the content (em icon or img) */
    display: flex;
    align-items: center;
    justify-content: center;
}

.media-circle img {
    border-radius: 50%; /* Make the image itself round */
}

.media-circle .img-fluid {
    width: 100%;
    height: 100%;
    object-fit: cover; 
    border-radius: 50%;
}


.media-md .icon {
    font-size: 1.25rem; /* Adjust icon size for media-md */
}

.media-sm .icon {
    font-size: 1rem; /* Adjust icon size for media-sm (Stats Cards) */
}

/* Other Styles */
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
            if (!groupElement) return; // Safety check
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

            const cards = elements.cards;
            const updates = {};
            
            // Check if any card element exists on the page (they might be hidden by PHP if count is 0)
            const totalExists = cards.total && cards.total.style.display !== 'none';

            if (!hasResults || !totalExists) {
                // If no results, or total card was hidden by PHP, hide all dynamic cards.
                updates.total = false; updates.student = false; updates.lecturer = false; updates.showing = false;
            } else {
                updates.total = true; updates.showing = true;
                
                // Only show student/lecturer cards if the filter matches OR if 'all' is selected.
                if (category === 'student') { 
                    updates.student = true; updates.lecturer = false; 
                } 
                else if (category === 'lecturer') { 
                    updates.student = false; updates.lecturer = true; 
                } 
                else { 
                    // When 'all' is selected, check the PHP-rendered visibility
                    updates.student = cards.student && cards.student.getAttribute('data-original-display') !== 'none'; 
                    updates.lecturer = cards.lecturer && cards.lecturer.getAttribute('data-original-display') !== 'none'; 
                }
            }

            raf(() => {
                Object.keys(updates).forEach(key => {
                    const card = cards[key];
                    if (card) { card.style.display = updates[key] ? '' : 'none'; }
                });
            });
        }
        
        // Cache original display styles
        if (elements.cards.student) elements.cards.student.setAttribute('data-original-display', elements.cards.student.style.display);
        if (elements.cards.lecturer) elements.cards.lecturer.setAttribute('data-original-display', elements.cards.lecturer.style.display);


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
                            
                            // Only update card visibility based on filters if the cards were present in the first place
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
        
        // Initial fade-in animation
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

@endsection