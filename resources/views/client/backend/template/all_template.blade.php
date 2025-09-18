@extends('client.client_dashboard')
@section('client') 

 <div class="nk-content-inner">
     <div class="nk-content-body">
         <div class="nk-block-head nk-page-head">
             <div class="nk-block-head-between flex-wrap g-2">
                 <div class="nk-block-head-content">
                     <h2 class="display-6">Template Library</h2>
                     <p class="text-muted">Simplify your task by using templates provided</p>
                 </div>
                 <div class="nk-block-head-content">
                     <button class="btn btn-outline-light d-lg-none mb-3" type="button" id="mobileFilterToggle">
                         <em class="icon ni ni-filter"></em>
                         <span>Filters & Search</span>
                     </button>
                     
                     <div class="filters-container" id="filtersContainer">
                         <div class="row g-3">
                             <div class="col-12 col-md-4 col-lg-auto">
                                 <div class="form-group mb-0">
                                     <select class="form-select form-select-sm" id="categoryFilter">
                                         <option value="all">All Categories</option>
                                         <option value="student">Student Templates</option>
                                         <option value="lecturer">Lecturer Templates</option>
                                     </select>
                                 </div>
                             </div>
                             
                             <div class="col-12 col-md-4 col-lg-auto">
                                 <div class="form-group mb-0">
                                     <select class="form-select form-select-sm" id="sortFilter">
                                         <option value="newest">Newest First</option>
                                         <option value="oldest">Oldest First</option>
                                         <option value="title">Title A-Z</option>
                                         <option value="title-desc">Title Z-A</option>
                                         <option value="category">Category</option>
                                     </select>
                                 </div>
                             </div>

                             <div class="col-12 col-md-4 col-lg-auto">
                                 <div class="form-group mb-0">
                                     <div class="form-control-wrap">
                                         <div class="form-control-icon start sm text-light">
                                             <em class="icon ni ni-search"></em>
                                         </div>
                                         <input type="text" class="form-control form-control-sm" placeholder="Search templates..." id="searchInput">
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
          
         <div class="nk-block">
             <div class="row g-3 mb-4">
                 <div class="col-6 col-md-3">
                     <div class="card border-0 bg-primary bg-opacity-10">
                         <div class="card-body py-3 px-3">
                             <div class="d-flex align-items-center">
                                 <div class="media media-sm media-circle bg-primary text-white me-2 me-md-3">
                                     <em class="icon ni ni-template"></em>
                                 </div>
                                 <div class="flex-grow-1 min-w-0">
                                     <h6 class="mb-0 fs-6" id="total-count">{{ count($templates) }}</h6>
                                     <span class="small text-muted d-block text-truncate">Total Templates</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-6 col-md-3">
                     <div class="card border-0 bg-info bg-opacity-10">
                         <div class="card-body py-3 px-3">
                             <div class="d-flex align-items-center">
                                 <div class="media media-sm media-circle bg-info text-white me-2 me-md-3">
                                     <em class="icon ni ni-user"></em>
                                 </div>
                                 <div class="flex-grow-1 min-w-0">
                                     <h6 class="mb-0 fs-6" id="student-count">{{ $templates->where('category', 'Student')->count() }}</h6>
                                     <span class="small text-muted d-block text-truncate">Student</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-6 col-md-3">
                     <div class="card border-0 bg-warning bg-opacity-10">
                         <div class="card-body py-3 px-3">
                             <div class="d-flex align-items-center">
                                 <div class="media media-sm media-circle bg-warning text-white me-2 me-md-3">
                                     <em class="icon ni ni-user-check"></em>
                                 </div>
                                 <div class="flex-grow-1 min-w-0">
                                     <h6 class="mb-0 fs-6" id="lecturer-count">{{ $templates->where('category', 'Lecturer')->count() }}</h6>
                                     <span class="small text-muted d-block text-truncate">Lecturer</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="col-6 col-md-3">
                     <div class="card border-0 bg-success bg-opacity-10">
                         <div class="card-body py-3 px-3">
                             <div class="d-flex align-items-center">
                                 <div class="media media-sm media-circle bg-success text-white me-2 me-md-3">
                                     <em class="icon ni ni-eye"></em>
                                 </div>
                                 <div class="flex-grow-1 min-w-0">
                                     <h6 class="mb-0 fs-6" id="showing-count">{{ count($templates) }}</h6>
                                     <span class="small text-muted d-block text-truncate">Showing</span>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

            <div class="row g-3 g-md-4" id="templates-container">
                @foreach ($templates as $item)
                <!-- Individual template card column with responsive sizing -->
                <!-- Store template metadata for filtering/searching via JavaScript -->
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 template-item"
                    data-category="{{ strtolower($item->category) }}"
                    data-title="{{ strtolower($item->title) }}"
                    data-description="{{ strtolower($item->description) }}"
                    data-created="{{ $item->created_at->timestamp }}">

                    <div class="card h-100 border-0 shadow-sm template-card">
                        <div class="card-body p-3 p-md-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <!-- Link to template details page -->
                                <a href="{{ route('user.details.template',$item->id) }}" class="text-decoration-none">
                                    <!-- Icon with dynamic background and text color based on category -->
                                    <div class="media media-md media-circle bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} bg-opacity-20 text-{{ $item->category == 'Student' ? 'primary' : 'info' }}">
                                        <em class="{{ $item->icon ?? 'ni ni-template' }}"></em>
                                    </div>
                                </a>

                                <!-- Category badge with dynamic color -->
                                <span class="badge bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} badge-sm">
                                    {{ $item->category }}
                                </span>
                            </div>

                            <!-- Main content: title and description -->
                            <div class="flex-grow-1">
                                <a href="{{ route('user.details.template',$item->id) }}" class="text-decoration-none">
                                    <h5 class="card-title fs-6 fs-md-5 fw-medium mb-2 text-dark">{{ $item->title }}</h5>
                                    <p class="card-text text-muted small line-clamp-2 line-clamp-md-3 mb-3">{{ $item->description }}</p>
                                </a>
                            </div>
                            <!-- Footer section: creation date -->
                            <div class="template-footer mt-auto">
                                <small class="text-muted d-flex align-items-center">
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <!-- Full date for larger screens, short date for small screens -->
                                    <span class="d-none d-sm-inline">{{ $item->created_at->format('M d, Y') }}</span>
                                    <span class="d-sm-none">{{ $item->created_at->format('M d') }}</span>
                                </small>
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
 </div>

<style>
/* ================================
   General Styles & Animations
================================ */

/* Template card default appearance */
.template-item {
    transition: all 0.3s ease;
    opacity: 1;
    transform: scale(1);
}

/* Animation for hiding template cards */
.template-item.hiding {
    opacity: 0;
    transform: scale(0.9);
    pointer-events: none;
}

/* Completely hide template cards */
.template-item.hidden {
    display: none !important;
}

/* Card base styling */
.card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

/* Card hover effect */
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

/* Form controls default styling */
.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

/* Form controls focus effect */
.form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Clamp text to 2 lines */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Centered circular media icon */
.media {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* Footer styling for template cards */
.template-footer {
    border-top: 1px solid #f1f1f1;
    padding-top: 12px;
    margin-top: auto;
}

/* ================================
   Stats Cards Hover Effects
================================ */

/* Subtle hover effect for colored cards */
.card.bg-primary.bg-opacity-10:hover,
.card.bg-info.bg-opacity-10:hover,
.card.bg-warning.bg-opacity-10:hover,
.card.bg-success.bg-opacity-10:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

/* ================================
   Badge Animations
================================ */

/* Smooth transition for badges */
.badge {
    transition: all 0.3s ease;
}

/* Slight scale-up on hover */
.template-item:hover .badge {
    transform: scale(1.05);
}

/* ================================
   Filter Controls Styling
================================ */

/* Ensure filter controls don't shrink */
.nk-block-head-content .d-flex > div {
    min-width: fit-content;
}

/* Label styling */
.form-label {
    font-weight: 500;
    margin-bottom: 4px;
}

/* Transition for filter container */
.filters-container {
    transition: all 0.3s ease;
}

/* Hide filters on small screens */
@media (max-width: 991.98px) {
    .filters-container {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
    }

    /* Show filters when toggled */
    .filters-container.show {
        max-height: 500px;
        opacity: 1;
        margin-bottom: 1rem;
    }

    /* Mobile filter toggle button */
    #mobileFilterToggle {
        width: 100%;
        border-radius: 8px;
    }
}

/* Always show filters on large screens */
@media (min-width: 992px) {
    .filters-container {
        max-height: none !important;
        opacity: 1 !important;
    }

    /* Hide toggle button on desktop */
    #mobileFilterToggle {
        display: none !important;
    }
}

/* Header layout adjustments */
.nk-block-head-between {
    flex-wrap: wrap;
    gap: 1rem;
}

/* Stack header items on small screens */
@media (max-width: 767.98px) {
    .nk-block-head-between {
        flex-direction: column;
        align-items: stretch;
    }

    /* Smaller heading font */
    .display-6 {
        font-size: 1.5rem;
    }
}

/* Stats card layout tweaks for mobile */
@media (max-width: 575.98px) {
    .row.g-3.mb-4 .col-6 {
        margin-bottom: 0.5rem;
    }

    .card-body.py-3.px-3 {
        padding: 0.75rem 0.5rem !important;
    }

    .media.media-sm {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }

    .fs-6 {
        font-size: 0.9rem !important;
    }
}

/* Responsive template card visibility */
.template-item {
    transition: all 0.3s ease;
    opacity: 1;
    transform: scale(1);
}

.template-item.hiding {
    opacity: 0;
    transform: scale(0.9);
    pointer-events: none;
}

.template-item.hidden {
    display: none !important;
}

/* Grid layout for different screen sizes */
@media (max-width: 575.98px) {
    /* 1 column layout */
    .template-item {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .template-card {
        margin-bottom: 1rem;
    }

    .card-body.p-3 {
        padding: 1rem !important;
    }
}

@media (min-width: 576px) and (max-width: 991.98px) {
    /* 2 column layout */
    .template-item {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
    /* 3 column layout */
    .template-item {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
}

@media (min-width: 1200px) {
    /* 4 column layout */
    .template-item {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

/* Enhanced card styling */
.template-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    height: 100%;
}

/* Card hover effect */
.template-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

/* Reduced hover effect on mobile */
@media (max-width: 767.98px) {
    .template-card:hover {
        transform: translateY(-4px);
    }
}

/* Responsive form controls */
.form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    width: 100%;
}

@media (max-width: 767.98px) {
    .form-select, .form-control {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
}

/* Clamp text to 3 lines on medium screens */
@media (min-width: 768px) {
    .line-clamp-md-3 {
        -webkit-line-clamp: 3;
    }
}

/* Responsive media icon sizing */
@media (max-width: 575.98px) {
    .media-md {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}

/* No results section styling */
#no-results {
    padding: 2rem 1rem;
}

@media (max-width: 767.98px) {
    #no-results {
        padding: 1.5rem 0.5rem;
    }

    #no-results .media-xl {
        width: 60px !important;
        height: 60px !important;
    }

    #no-results h4 {
        font-size: 1.1rem;
    }
}

/* ================================
   Utility Classes
================================ */

/* Prevent element from growing */
.min-w-0 {
    min-width: 0;
}

/* Truncate overflowing text */
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Disable hover effects on touch devices */
@media (hover: none) and (pointer: coarse) {
    .template-card:hover {
        transform: none;
    }

    .template-card:active {
        transform: scale(0.98);
    }

    .btn:hover {
        transform: none;
    }
}
</style>

 <script>
 document.addEventListener('DOMContentLoaded', function() {
     // JS: Get all necessary DOM elements.
     const categoryFilter = document.getElementById('categoryFilter');
     const sortFilter = document.getElementById('sortFilter');
     const searchInput = document.getElementById('searchInput');
     const templateItems = document.querySelectorAll('.template-item');
     const noResults = document.getElementById('no-results');
     const showingCount = document.getElementById('showing-count');
     const mobileFilterToggle = document.getElementById('mobileFilterToggle');
     const filtersContainer = document.getElementById('filtersContainer');
      
     // JS: Convert NodeList to an array for easier manipulation.
     let allTemplates = Array.from(templateItems);
      
     // JS: Handles the mobile filter toggle button.
     if (mobileFilterToggle && filtersContainer) {
         mobileFilterToggle.addEventListener('click', function() {
             filtersContainer.classList.toggle('show');
             this.classList.toggle('active');
         });
          
         // JS: Closes filters when clicking outside on mobile.
         document.addEventListener('click', function(e) {
             if (window.innerWidth <= 991 &&
                 !mobileFilterToggle.contains(e.target) &&
                 !filtersContainer.contains(e.target)) {
                 filtersContainer.classList.remove('show');
                 mobileFilterToggle.classList.remove('active');
             }
         });
     }
      
     // JS: The main function to filter and sort the templates.
     function applyFilters() {
         const category = categoryFilter.value;
         const sortBy = sortFilter.value;
         const searchTerm = searchInput.value.toLowerCase().trim();
          
         console.log('Applying filters:', { category, sortBy, searchTerm });
          
         // JS: Filters templates based on category and search term.
         let visibleTemplates = allTemplates.filter(item => {
             const itemCategory = item.dataset.category;
             const itemTitle = item.dataset.title;
             const itemDescription = item.dataset.description;
              
             const matchesCategory = category === 'all' || itemCategory === category;
             const matchesSearch = !searchTerm ||
                                 itemTitle.includes(searchTerm) ||
                                 itemDescription.includes(searchTerm);
              
             return matchesCategory && matchesSearch;
         });
          
         // JS: Sorts the filtered templates based on the selected sort option.
         visibleTemplates.sort((a, b) => {
             switch(sortBy) {
                 case 'newest':
                     return parseInt(b.dataset.created) - parseInt(a.dataset.created);
                 case 'oldest':
                     return parseInt(a.dataset.created) - parseInt(b.dataset.created);
                 case 'title':
                     return a.dataset.title.localeCompare(b.dataset.title);
                 case 'title-desc':
                     return b.dataset.title.localeCompare(a.dataset.title);
                 case 'category':
                     return a.dataset.category.localeCompare(b.dataset.category);
                 default:
                     return 0;
             }
         });
          
         // JS: Hides all templates with a transition.
         allTemplates.forEach(item => {
             item.classList.add('hiding');
             setTimeout(() => {
                 item.classList.add('hidden');
             }, 150);
         });
          
         // JS: Shows the filtered and sorted templates with a staggered animation.
         setTimeout(() => {
             visibleTemplates.forEach((item, index) => {
                 item.classList.remove('hidden', 'hiding');
                 item.style.order = index;
                  
                 // JS: Staggers the animation for a ripple effect.
                 setTimeout(() => {
                     item.style.opacity = '1';
                     item.style.transform = 'scale(1)';
                 }, index * 100);
             });
              
             // JS: Updates the "Showing" count.
             showingCount.textContent = visibleTemplates.length;
              
             // JS: Toggles the "no results" message based on the number of visible templates.
             if (visibleTemplates.length === 0) {
                 noResults.classList.remove('d-none');
             } else {
                 noResults.classList.add('d-none');
             }
         }, 200);
     }
      
     // JS: Attaches event listeners to the filter dropdowns to trigger `applyFilters`.
     categoryFilter.addEventListener('change', applyFilters);
     sortFilter.addEventListener('change', applyFilters);
      
     // JS: Debounces the search input to avoid running the filter too often while the user is typing.
     let searchTimeout;
     searchInput.addEventListener('input', () => {
         clearTimeout(searchTimeout);
         searchTimeout = setTimeout(applyFilters, 300);
     });
      
     // JS: Function to reset all filters to their default values.
     window.resetFilters = function() {
         categoryFilter.value = 'all';
         sortFilter.value = 'newest';
         searchInput.value = '';
         applyFilters();
     };
      
     // JS: Adds keyboard shortcuts for accessibility and quick actions.
     document.addEventListener('keydown', (e) => {
         // Alt + 1,2,3 for category filters
         if (e.altKey && e.key >= '1' && e.key <= '3') {
             e.preventDefault();
             const options = ['all', 'student', 'lecturer'];
             categoryFilter.value = options[parseInt(e.key) - 1];
             applyFilters();
         }
          
         // Ctrl + F for search focus
         if (e.ctrlKey && e.key === 'f') {
             e.preventDefault();
             searchInput.focus();
         }
          
         // Escape to reset
         if (e.key === 'Escape') {
             resetFilters();
         }
     });
      
     // JS: Applies an initial staggered animation to all template cards when the page loads.
     templateItems.forEach((item, index) => {
         item.style.opacity = '0';
         item.style.transform = 'translateY(30px)';
         setTimeout(() => {
             item.style.opacity = '1';
             item.style.transform = 'translateY(0)';
         }, index * 100);
     });
 });
 </script>






@endsection