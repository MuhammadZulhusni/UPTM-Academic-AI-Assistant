@extends('admin.dashboard')

@section('admin') 

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Template Library</h2>
                    <p class="text-muted">Manage and organize templates</p>
                </div>
                <div class="nk-block-head-content">
                    <!-- Mobile Toggle Button -->
                    <button class="btn btn-outline-light d-lg-none mb-3" type="button" id="mobileFilterToggle">
                        <em class="icon ni ni-filter"></em>
                        <span>Filters & Search</span>
                    </button>
                    
                    <!-- Filters Container -->
                    <div class="filters-container" id="filtersContainer">
                        <div class="row g-3">
                            <!-- Category Filter -->
                            <div class="col-12 col-md-4 col-lg-auto">
                                <div class="form-group mb-0">
                                    <!-- <label class="form-label small text-muted mb-1">Filter by Category</label> -->
                                    <select class="form-select form-select-sm" id="categoryFilter">
                                        <option value="all">All Categories</option>
                                        <option value="student">Student Templates</option>
                                        <option value="lecturer">Lecturer Templates</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Sort Dropdown -->
                            <div class="col-12 col-md-4 col-lg-auto">
                                <div class="form-group mb-0">
                                    <!-- <label class="form-label small text-muted mb-1">Sort by</label> -->
                                    <select class="form-select form-select-sm" id="sortFilter">
                                        <option value="newest">Newest First</option>
                                        <option value="oldest">Oldest First</option>
                                        <option value="title">Title A-Z</option>
                                        <option value="title-desc">Title Z-A</option>
                                        <option value="category">Category</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search -->
                            <div class="col-12 col-md-4 col-lg-auto">
                                <div class="form-group mb-0">
                                    <!-- <label class="form-label small text-muted mb-1">Search</label> -->
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
            <!-- Summary Stats -->
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
                                    <span class="small text-muted d-block text-truncate">Total</span>
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
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3 template-item" 
                     data-category="{{ strtolower($item->category) }}" 
                     data-title="{{ strtolower($item->title) }}" 
                     data-description="{{ strtolower($item->description) }}"
                     data-created="{{ $item->created_at->timestamp }}">
                    <div class="card h-100 border-0 shadow-sm template-card">
                        <div class="card-body p-3 p-md-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <a href="{{ route('details.template',$item->id) }}" class="text-decoration-none">
                                    <div class="media media-md media-circle bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} bg-opacity-20 text-{{ $item->category == 'Student' ? 'primary' : 'info' }}">
                                        <em class="{{ $item->icon ?? 'ni ni-template' }}"></em>
                                    </div>
                                </a>
                                <span class="badge bg-{{ $item->category == 'Student' ? 'primary' : 'info' }} badge-sm">
                                    {{ $item->category }}
                                </span>
                            </div>
                            
                            <div class="flex-grow-1">
                                <a href="{{ route('edit.template',$item->id) }}" class="text-decoration-none">
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
                @endforeach      
            </div>

            <!-- No results message -->
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

    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }

    .form-select, .form-control {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
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

    .media {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .template-footer {
        border-top: 1px solid #f1f1f1;
        padding-top: 12px;
        margin-top: auto;
    }

    /* Stats cards hover effect */
    .card.bg-primary.bg-opacity-10:hover,
    .card.bg-info.bg-opacity-10:hover,
    .card.bg-warning.bg-opacity-10:hover,
    .card.bg-success.bg-opacity-10:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    /* Badge animations */
    .badge {
        transition: all 0.3s ease;
    }

    .template-item:hover .badge {
        transform: scale(1.05);
    }

    /* Filter controls styling */
    .nk-block-head-content .d-flex > div {
        min-width: fit-content;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 4px;
    }

    /* ======================
       RESPONSIVE STYLES
    ====================== */
    
    /* Mobile Filters Toggle */
    .filters-container {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 991.98px) {
        .filters-container {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        
        .filters-container.show {
            max-height: 500px;
            opacity: 1;
            margin-bottom: 1rem;
        }
        
        #mobileFilterToggle {
            width: 100%;
            border-radius: 8px;
        }
    }
    
    @media (min-width: 992px) {
        .filters-container {
            max-height: none !important;
            opacity: 1 !important;
        }
        
        #mobileFilterToggle {
            display: none !important;
        }
    }

    /* Header Responsive */
    .nk-block-head-between {
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    @media (max-width: 767.98px) {
        .nk-block-head-between {
            flex-direction: column;
            align-items: stretch;
        }
        
        .display-6 {
            font-size: 1.5rem;
        }
    }

    /* Stats Cards Responsive */
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

    /* Template Cards Responsive */
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

    /* Card Grid Responsive Breakpoints */
    @media (max-width: 575.98px) {
        /* Mobile: 1 column */
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
        /* Tablet: 2 columns */
        .template-item {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (min-width: 992px) and (max-width: 1199.98px) {
        /* Desktop: 3 columns */
        .template-item {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (min-width: 1200px) {
        /* Large Desktop: 4 columns */
        .template-item {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }

    /* Enhanced Card Styling */
    .template-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .template-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    }

    @media (max-width: 767.98px) {
        .template-card:hover {
            transform: translateY(-4px);
        }
    }

    /* Form Controls Responsive */
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

    /* Text Responsive */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @media (min-width: 768px) {
        .line-clamp-md-3 {
            -webkit-line-clamp: 3;
        }
    }

    /* Media Icons Responsive */
    .media {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }

    @media (max-width: 575.98px) {
        .media-md {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
    }

    /* No Results Responsive */
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

    /* Utility Classes */
    .min-w-0 {
        min-width: 0;
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Touch Improvements for Mobile */
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
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const searchInput = document.getElementById('searchInput');
    const templateItems = document.querySelectorAll('.template-item');
    const noResults = document.getElementById('no-results');
    const showingCount = document.getElementById('showing-count');
    const mobileFilterToggle = document.getElementById('mobileFilterToggle');
    const filtersContainer = document.getElementById('filtersContainer');
    
    let allTemplates = Array.from(templateItems);
    
    // Mobile filter toggle
    if (mobileFilterToggle && filtersContainer) {
        mobileFilterToggle.addEventListener('click', function() {
            filtersContainer.classList.toggle('show');
            this.classList.toggle('active');
        });
        
        // Close filters when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 991 && 
                !mobileFilterToggle.contains(e.target) && 
                !filtersContainer.contains(e.target)) {
                filtersContainer.classList.remove('show');
                mobileFilterToggle.classList.remove('active');
            }
        });
    }
    
    // Main filter function
    function applyFilters() {
        const category = categoryFilter.value;
        const sortBy = sortFilter.value;
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        console.log('Applying filters:', { category, sortBy, searchTerm });
        
        // Filter by category and search
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
        
        // Sort templates
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
        
        // Hide all items first
        allTemplates.forEach(item => {
            item.classList.add('hiding');
            setTimeout(() => {
                item.classList.add('hidden');
            }, 150);
        });
        
        // Show filtered items with animation
        setTimeout(() => {
            visibleTemplates.forEach((item, index) => {
                item.classList.remove('hidden', 'hiding');
                item.style.order = index;
                
                // Stagger the animation
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'scale(1)';
                }, index * 100);
            });
            
            // Update counter
            showingCount.textContent = visibleTemplates.length;
            
            // Show/hide no results
            if (visibleTemplates.length === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }, 200);
    }
    
    // Event listeners
    categoryFilter.addEventListener('change', applyFilters);
    sortFilter.addEventListener('change', applyFilters);
    
    // Debounced search
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });
    
    // Reset filters function
    window.resetFilters = function() {
        categoryFilter.value = 'all';
        sortFilter.value = 'newest';
        searchInput.value = '';
        applyFilters();
    };
    
    // Keyboard shortcuts
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
    
    // Initialize with animation
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