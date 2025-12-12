@extends('client.client_dashboard')
@section('client')

<div class="nk-content-inner">
    <div class="nk-content-body">
        
        {{-- Header with Gradient --}}
        <div class="nk-block-head nk-page-head mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <h2 class="display-6 mb-1 animate-fade-in">Template Library</h2>
                    <p class="text-muted mb-0">Browse and select from our curated collection</p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <em class="icon ni ni-filter"></em>
                        <span class="ms-2">Filters</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Cards (Admin Only) --}}
        @if(auth()->user()->role === 'admin')
        @php
            $stats = [
                ['count' => $templates->total(), 'label' => 'Total', 'icon' => 'ni-template', 'color' => 'primary'],
                ['count' => $templates->where('category', 'Student')->count(), 'label' => 'Student', 'icon' => 'ni-user', 'color' => 'info'],
                ['count' => $templates->where('category', 'Lecturer')->count(), 'label' => 'Lecturer', 'icon' => 'ni-user-check', 'color' => 'warning']
            ];
        @endphp
        
        <div class="row g-3 mb-4 animate-slide-up">
            @foreach($stats as $stat)
            @if($stat['count'] > 0)
            <div class="col-6 col-lg-4">
                <div class="stat-card stat-card-{{ $stat['color'] }}">
                    <div class="stat-icon">
                        <em class="icon ni {{ $stat['icon'] }}"></em>
                    </div>
                    <div class="stat-content">
                        <h3>{{ $stat['count'] }}</h3>
                        <span>{{ $stat['label'] }}</span>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Template Grid --}}
        @if($templates->total() > 0)
        <div class="row g-3 g-lg-4 template-grid">
            @foreach($templates as $item)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <a href="{{ route('user.details.template', $item->id) }}" class="template-card-link">
                    <div class="template-card">
                        <div class="template-card-header">
                            <div class="template-icon template-icon-{{ strtolower($item->category) }}">
                                <img src="{{ asset('upload/template/' . $item->icon) }}" alt="{{ $item->title }}">
                            </div>
                            <span class="template-badge badge-{{ strtolower($item->category) }}">
                                {{ $item->category }}
                            </span>
                        </div>
                        <div class="template-card-body">
                            <h5>{{ $item->title }}</h5>
                            <p>{{ Str::limit($item->description, 80) }}</p>
                        </div>
                        <div class="template-card-footer">
                            <small>
                                <em class="icon ni ni-calendar"></em>
                                {{ $item->created_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            <div class="pagination-info">
                Showing {{ $templates->firstItem() }}-{{ $templates->lastItem() }} of {{ $templates->total() }}
            </div>
            <nav>
                <ul class="modern-pagination">
                    <li class="{{ $templates->onFirstPage() ? 'disabled' : '' }}">
                        <a href="{{ $templates->previousPageUrl() }}">
                            <em class="icon ni ni-chevron-left"></em>
                        </a>
                    </li>
                    
                    @foreach(range(1, $templates->lastPage()) as $page)
                        @if($page == 1 || $page == $templates->lastPage() || abs($page - $templates->currentPage()) <= 1)
                        <li class="{{ $page == $templates->currentPage() ? 'active' : '' }}">
                            <a href="{{ $templates->url($page) }}">{{ $page }}</a>
                        </li>
                        @elseif(abs($page - $templates->currentPage()) == 2)
                        <li class="disabled"><span>...</span></li>
                        @endif
                    @endforeach
                    
                    <li class="{{ !$templates->hasMorePages() ? 'disabled' : '' }}">
                        <a href="{{ $templates->nextPageUrl() }}">
                            <em class="icon ni ni-chevron-right"></em>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        @else
        {{-- Empty State --}}
        <div class="empty-state">
            <div class="empty-icon">
                <em class="icon ni ni-search"></em>
            </div>
            <h4>No templates found</h4>
            <p>Try adjusting your filters or search terms</p>
            <a href="{{ route('user.template') }}" class="btn btn-primary">Reset Filters</a>
        </div>
        @endif

    </div>
</div>

{{-- Filter Modal --}}
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <form action="{{ route('user.template') }}" method="GET">
                <div class="modal-header">
                    <h5>Filter and Sort</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    {{-- Search --}}
                    <div class="filter-group">
                        <label>Search</label>
                        <div class="search-input">
                            <em class="icon ni ni-search"></em>
                            <input type="text" name="search" placeholder="Search templates..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Category (Admin) --}}
                    @if(auth()->user()->role === 'admin')
                    <div class="filter-group">
                        <label>Category</label>
                        <div class="button-group">
                            @foreach(['all' => 'All', 'student' => 'Student', 'lecturer' => 'Lecturer'] as $val => $label)
                            <label class="btn-radio">
                                <input type="radio" name="category" value="{{ $val }}" {{ request('category', 'all') == $val ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Sort --}}
                    <div class="filter-group mb-0">
                        <label>Sort By</label>
                        <div class="button-group">
                            @foreach(['newest' => 'Newest', 'title' => 'A-Z', 'title-desc' => 'Z-A'] as $val => $label)
                            <label class="btn-radio">
                                <input type="radio" name="sort" value="{{ $val }}" {{ request('sort', 'newest') == $val ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <a href="{{ route('user.template') }}" class="btn btn-light">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #6366f1;
    --info: #06b6d4;
    --warning: #f59e0b;
    --success: #10b981;
    --text: #1e293b;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --bg: #f8fafc;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fadeIn 0.5s ease; }
.animate-slide-up { animation: slideUp 0.6s ease; }

/* Stats Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-card-primary .stat-icon { background: #ede9fe; color: var(--primary); }
.stat-card-info .stat-icon { background: #cffafe; color: var(--info); }
.stat-card-warning .stat-icon { background: #fef3c7; color: var(--warning); }

.stat-content h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: var(--text);
}

.stat-content span {
    color: var(--text-muted);
    font-size: 0.875rem;
}

/* Template Cards */
.template-card-link {
    text-decoration: none;
    display: block;
    height: 100%;
}

.template-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}

.template-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--info));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.template-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    border-color: var(--primary);
}

.template-card:hover::before {
    transform: scaleX(1);
}

.template-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.template-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
}

.template-icon-student { background: #ede9fe; }
.template-icon-lecturer { background: #cffafe; }

.template-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.template-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-student {
    background: #ede9fe;
    color: var(--primary);
}

.badge-lecturer {
    background: #cffafe;
    color: var(--info);
}

.template-card-body {
    flex: 1;
    margin-bottom: 1rem;
}

.template-card-body h5 {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.template-card-body p {
    color: var(--text-muted);
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
}

.template-card-footer {
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.template-card-footer small {
    color: var(--text-muted);
    font-size: 0.8125rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    color: var(--text-muted);
    font-size: 0.875rem;
}

.modern-pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.modern-pagination li a,
.modern-pagination li span {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 0.75rem;
    border-radius: 10px;
    background: white;
    border: 1px solid var(--border);
    color: var(--text);
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.modern-pagination li a:hover {
    background: var(--bg);
    border-color: var(--primary);
    transform: translateY(-2px);
}

.modern-pagination li.active a {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

.modern-pagination li.disabled a,
.modern-pagination li.disabled span {
    opacity: 0.4;
    pointer-events: none;
}

/* Modal */
.modern-modal {
    border-radius: 20px;
    border: none;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.modern-modal .modal-header {
    border: none;
    padding: 1.5rem;
}

.modern-modal .modal-body {
    padding: 0 1.5rem 1.5rem;
}

.modern-modal .modal-footer {
    border: none;
    padding: 1.5rem;
    gap: 0.75rem;
}

.filter-group {
    margin-bottom: 1.5rem;
}

.filter-group label {
    display: block;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
}

.search-input {
    position: relative;
}

.search-input em {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.search-input input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
}

.search-input input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.button-group {
    display: flex;
    gap: 0.5rem;
}

.btn-radio {
    flex: 1;
    margin: 0;
    cursor: pointer;
}

.btn-radio input {
    display: none;
}

.btn-radio span {
    display: block;
    padding: 0.625rem 1rem;
    background: var(--bg);
    border: 2px solid var(--border);
    border-radius: 10px;
    text-align: center;
    font-weight: 500;
    font-size: 0.875rem;
    color: var(--text);
    transition: all 0.2s ease;
}

.btn-radio input:checked + span {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--text-muted);
}

.empty-state h4 {
    color: var(--text);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .stat-card {
        padding: 1.25rem;
    }
    
    .template-card {
        padding: 1.25rem;
    }
    
    .pagination-wrapper {
        flex-direction: column;
        text-align: center;
    }
    
    .modern-pagination li:not(.active):not(:first-child):not(:last-child) {
        display: none;
    }
}
</style>

@endsection