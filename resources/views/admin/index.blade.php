@extends('admin.dashboard')

@section('admin')

<style>
.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.template-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
}

.template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
    border-color: #3b82f6;
}

.template-icon {
    width: 48px;
    height: 48px;
    background: #f1f5f9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #3b82f6;
}

.template-card:hover .template-icon {
    background: #eff6ff;
}

.template-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: #1e293b;
}

.template-content p {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive Design */
@media (max-width: 768px) {
    .welcome-hero {
        text-align: center;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        text-align: left;
    }
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
             <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">Welcome, {{ $user->name }}!</h2>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-gs">
                {{-- All Users Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full border shadow-sm transition">
                        <div class="card-body text-center">
                            <em class="icon ni ni-user-alt fs-2 text-indigo mb-3"></em>
                            <h5 class="display-4 fw-bold text-dark mb-1">
                                <span id="totalUsers" data-count="{{ $totalUsers }}">0</span>
                            </h5>
                            <div class="text-muted fw-medium small">Total Users</div>
                        </div>
                    </div>
                </div>
                
                {{-- Users Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full border shadow-sm transition">
                        <div class="card-body text-center">
                            <em class="icon ni ni-book-read fs-2 text-indigo mb-3"></em>
                            <h5 class="display-4 fw-bold text-dark mb-1">
                                <span id="newUsersCount" data-count="{{ $studentTemplateCount }}">0</span>
                            </h5>
                            <div class="text-muted fw-medium small">Student Template</div>
                        </div>
                    </div>
                </div>

                {{-- Generated Output Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full border shadow-sm transition">
                        <div class="card-body text-center">
                            <em class="icon ni ni-file-docs fs-2 text-indigo mb-3"></em>
                            <h5 class="display-4 fw-bold text-dark mb-1">
                                <span id="totalDocuments" data-count="{{ $lecturerTemplateCount }}">0</span>
                            </h5>
                            <div class="text-muted fw-medium small">Lecturer Template</div>
                        </div>
                    </div>
                </div>

                {{-- Total Templates Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full border shadow-sm transition">
                        <div class="card-body text-center">
                            <em class="icon ni ni-grid-plus fs-2 text-indigo mb-3"></em>
                            <h5 class="display-4 fw-bold text-dark mb-1">
                                <span id="totalTemplates" data-count="{{ $totalTemplates }}">0</span>
                            </h5>
                            <div class="text-muted fw-medium small">Total Templates</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">Templates Available</h2>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('admin.template') }}" class="link-primary fw-medium">Explore All <em class="icon ni ni-arrow-right"></em></a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="templates-grid">
                @foreach ($templates as $template)
                <a href="{{ route('details.template',$template->id) }}" class="template-card">
                    <div class="template-icon">
                        <em class="icon ni {{ $template->icon }}"></em>
                    </div>
                    <div class="template-content">
                        <h3>{{ $template->title }}</h3>
                        <p>{{ $template->description }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        <div class="nk-block"></div>
    </div>
</div>

<style>
    /* Global Styles & New Variables */
    :root {
        --color-purple: #865dff;
        --color-blue: #5d86ff;
        --color-indigo: #5d61ff;
        --color-cyan: #5dbeff;
        --color-dark: #212529;
        --color-muted: #6c757d;
        --color-light: #f8f9fa;
    }
    .text-purple { color: var(--color-purple) !important; }
    .text-blue { color: var(--color-blue) !important; }
    .text-indigo { color: var(--color-indigo) !important; }
    .text-cyan { color: var(--color-cyan) !important; }

    /* Card Component */
    .card {
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.08) !important;
    }
    .card.border {
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    .template-card:hover .template-icon {
        color: var(--color-purple) !important;
    }

    /* Icon Circles */
    .media-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .template-card:hover .template-icon {
        color: var(--color-purple) !important;
        background-color: rgba(134, 93, 255, 0.1) !important;
    }


    /* Staggered Fade-In Animation */
    .card {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .card.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<script>
    function animateValue(id, start, end, duration) {
        if (start === end) return;
        let range = end - start;
        let current = start;
        let increment = end > start ? 1 : -1;
        let stepTime = Math.abs(Math.floor(duration / range));
        let obj = document.getElementById(id);

        let timer = setInterval(function() {
            current += increment;
            obj.textContent = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        // Animate all elements with data-count attribute on page load
        document.querySelectorAll('[data-count]').forEach(element => {
            const id = element.id;
            const endValue = parseInt(element.getAttribute('data-count'));
            animateValue(id, 0, endValue, 1000); 
        });

        // Staggered fade-in for cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('is-visible');
            }, index * 100);
        });
    });
</script>

@endsection


<!-- <div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
             <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Welcome {{ $user->name }}!</h2>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-gs">
                {{-- New Users Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full bg-purple bg-opacity-10 border-0 shadow-sm transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fs-6 text-light mb-0">New Users (7 weeks)</div>
                            </div>
                            <h5 class="fs-1"><span id="newUsersCount" data-count="{{ $newUsersCount }}">0</span><small class="fs-3"> users</small></h5>
                            <div class="fs-7 text-light mt-1">Users registered in the last 7 weeks</div>
                        </div>
                    </div>
                </div>

                {{-- All Users Card --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full bg-blue bg-opacity-10 border-0 shadow-sm transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fs-6 text-light mb-0">Total Users</div>
                            </div>
                            <h5 class="fs-1"><span id="totalUsers" data-count="{{ $totalUsers }}">0</span><small class="fs-3"> users</small></h5>
                            <div class="fs-7 text-light mt-1">Total users on the system</div>
                        </div>
                    </div>
                </div>

                {{-- History of Generated Output Card (Count Only) --}}
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full bg-indigo bg-opacity-10 border-0 shadow-sm transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fs-6 text-light mb-0">Total Generated Output</div>
                            </div>
                            <h5 class="fs-1"><span id="totalDocuments" data-count="{{ $totalDocuments }}">0</span><small class="fs-3"> documents</small></h5>
                            <div class="fs-7 text-light mt-1">Total documents generated</div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full bg-cyan bg-opacity-10 border-0 shadow-sm transition">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fs-6 text-light mb-0">Total Templates</div>
                            </div>
                            <h5 class="fs-1"><span id="totalTemplates" data-count="{{ $totalTemplates }}">0</span><small class="fs-3"> templates</small></h5>
                            <div class="fs-7 text-light mt-1"><span class="text-dark">{{ $user->created_templates_count }}</span> templates created by you</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="nk-block-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Templates Available</h2>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('admin.template') }}" class="link">Explore All</a>
                </div>
            </div>
        </div>
        <div class="nk-block">
            <div class="row g-gs">
                @foreach ($templates as $template)
                <div class="col-sm-6 col-xxl-3">
                    <div class="card card-full shadow-sm transition">
                        <div class="card-body">
                            <div class="media media-rg media-middle media-circle text-primary bg-primary bg-opacity-20 mb-3">
                                <em class="icon ni {{ $template->icon }}"></em>
                            </div>
                            <h5 class="fs-4 fw-medium">{{ $template->title }}</h5>
                            <p class="small text-light">{{ $template->description }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="nk-block"></div>
    </div>
</div>

<style>
    /* CSS for a subtle transition effect on hover */
    .card.transition {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card.transition:hover {
        transform: translateY(-5px); /* Moves card up slightly */
        box-shadow: 0 10px 20px rgba(0,0,0,0.1); /* Adds a more pronounced shadow */
    }

    /* CSS for the page load transition */
    .card {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease-out, transform 0.5s ease-out;
    }
    .card.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>

<script>
    function animateValue(id, start, end, duration) {
        if (start === end) return;
        let range = end - start;
        let current = start;
        let increment = end > start ? 1 : -1;
        let stepTime = Math.abs(Math.floor(duration / range));
        let obj = document.getElementById(id);

        let timer = setInterval(function() {
            current += increment;
            obj.textContent = current;
            if (current == end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        // Find all elements with data-count attribute
        document.querySelectorAll('[data-count]').forEach(element => {
            const id = element.id;
            const endValue = parseInt(element.getAttribute('data-count'));
            // Animate each element with a duration of 1 second
            animateValue(id, 0, endValue, 1000); 
        });

        // Add a class to each card with a staggered delay for a fade-in transition
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('is-visible');
            }, index * 100); // 100ms delay between each card
        });
    });
</script> -->
                        

