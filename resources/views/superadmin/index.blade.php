@extends('superadmin.dashboard')

@section('superadmin')

<style>
:root {
    --primary: #3b82f6;
    --primary-light: #60a5fa;
    --primary-dark: #2563eb;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --bg-card: #ffffff;
    --bg-hover: #f8fafc;
    --border: #e2e8f0;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Layout */
.nk-content-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

/* Header with Greeting Animation */
.page-header {
    margin-bottom: 3rem;
    position: relative;
}

.page-header h2 {
    font-size: 2rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    letter-spacing: -0.02em;
}

.greeting-subtext {
    color: var(--text-secondary);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.75rem;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: var(--radius);
    padding: 1px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-card:active {
    transform: translateY(-4px) scale(0.98);
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
    letter-spacing: -0.03em;
    position: relative;
    display: inline-block;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-icon {
    position: absolute;
    right: 1.5rem;
    top: 1.5rem;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-hover);
    border-radius: 12px;
    opacity: 0.6;
    transition: var(--transition);
}

.stat-card:hover .stat-icon {
    opacity: 1;
    background: #eff6ff;
    transform: rotate(360deg) scale(1.1);
}

.stat-icon em {
    font-size: 1.5rem;
    color: var(--primary);
}

/* Growth Indicator */
.stat-growth {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #10b981;
    margin-top: 0.5rem;
    opacity: 0;
    transform: translateY(10px);
    transition: var(--transition);
}

.stat-card:hover .stat-growth {
    opacity: 1;
    transform: translateY(0);
}

.stat-growth em {
    font-size: 0.875rem;
}

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.02em;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 40px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    border-radius: 2px;
}

.section-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.section-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--primary);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.section-link:hover {
    color: white;
}

.section-link:hover::before {
    opacity: 1;
}

.section-link span,
.section-link em {
    position: relative;
    z-index: 1;
}

.section-link:hover em {
    transform: translateX(4px);
}

.section-link em {
    font-size: 0.875rem;
    transition: transform 0.3s ease;
}

/* Templates Grid */
.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.template-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    text-decoration: none;
    color: inherit;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}

.template-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(96, 165, 250, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.template-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary);
}

.template-card:hover::before {
    opacity: 1;
}

.template-card:active {
    transform: translateY(-4px) scale(1);
}

.template-icon-wrapper {
    width: 56px;
    height: 56px;
    background: var(--bg-hover);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    position: relative;
    z-index: 1;
}

.template-card:hover .template-icon-wrapper {
    background: #eff6ff;
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.template-icon-img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    filter: grayscale(0.3);
    transition: filter 0.3s ease;
}

.template-card:hover .template-icon-img {
    filter: grayscale(0);
}

.template-content {
    position: relative;
    z-index: 1;
}

.template-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
    line-height: 1.4;
    transition: color 0.3s ease;
}

.template-card:hover .template-content h3 {
    color: var(--primary);
}

.template-content p {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.6;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.template-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    opacity: 0;
    transform: translateY(-10px);
    transition: var(--transition);
    z-index: 2;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.template-card:hover .template-badge {
    opacity: 1;
    transform: translateY(0);
}

/* Different badge styles */
.template-badge.badge-new {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.template-badge.badge-featured {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-in {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.animate-scale {
    animation: scaleIn 0.5s ease forwards;
    opacity: 0;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }

/* Responsive Design */
@media (max-width: 768px) {
    .nk-content-inner {
        padding: 1.5rem 1rem;
    }
    
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header h2 {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        right: 1rem;
        top: 1rem;
    }
    
    .stat-icon em {
        font-size: 1.25rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .section-title {
        font-size: 1.25rem;
    }
    
    .templates-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* Loading Skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="page-header animate-scale">
            <h2>Welcome, {{ $user->name }}</h2>
            <div class="greeting-subtext">
                <span class="status-dot"></span>
                <span id="greeting-text">All systems operational</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card animate-in animate-delay-1">
                <div class="stat-icon">
                    <em class="icon ni ni-user-alt"></em>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        <span id="totalUsers" data-count="{{ $totalUsers }}">0</span>
                    </div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-growth">
                        <em class="icon ni ni-users"></em>
                        <span>All registered users</span>
                    </div>
                </div>
            </div>

            <div class="stat-card animate-in animate-delay-2">
                <div class="stat-icon">
                    <em class="icon ni ni-book-read"></em>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        <span id="newUsersCount" data-count="{{ $studentTemplateCount }}">0</span>
                    </div>
                    <div class="stat-label">Student Templates</div>
                    <div class="stat-growth">
                        <em class="icon ni ni-file-text"></em>
                        <span>Templates for students</span>
                    </div>
                </div>
            </div>

            <div class="stat-card animate-in animate-delay-3">
                <div class="stat-icon">
                    <em class="icon ni ni-file-docs"></em>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        <span id="totalDocuments" data-count="{{ $lecturerTemplateCount }}">0</span>
                    </div>
                    <div class="stat-label">Lecturer Templates</div>
                    <div class="stat-growth">
                        <em class="icon ni ni-files"></em>
                        <span>Templates for lecturers</span>
                    </div>
                </div>
            </div>

            <div class="stat-card animate-in animate-delay-4">
                <div class="stat-icon">
                    <em class="icon ni ni-grid-plus"></em>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        <span id="totalTemplates" data-count="{{ $totalTemplates }}">0</span>
                    </div>
                    <div class="stat-label">Total Templates</div>
                    <div class="stat-growth">
                        <em class="icon ni ni-template"></em>
                        <span>All available templates</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="section-header animate-in" style="animation-delay: 0.5s;">
            <h3 class="section-title">Available Templates</h3>
            <a href="{{ route('superadmin.template') }}" class="section-link">
                <span>View all</span>
                <em class="icon ni ni-arrow-right"></em>
            </a>
        </div>

        <!-- Templates Grid -->
        <div class="templates-grid">
            @foreach ($templates as $index => $template)
            <a href="{{ route('superadmin.details.template', $template->id) }}" class="template-card animate-in" style="animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                <div class="template-icon-wrapper">
                    <img src="{{ asset('upload/template/' . $template->icon) }}"
                         alt="{{ $template->title }}"
                         class="template-icon-img">
                </div>
                <div class="template-content">
                    <h3>{{ $template->title }}</h3>
                    <p>{{ $template->description }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<script>
// Smooth Counter Animation
function animateValue(id, start, end, duration) {
    if (start === end) return;
    
    const obj = document.getElementById(id);
    const range = end - start;
    const startTime = performance.now();
    
    const animate = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.floor(start + (range * easeOutQuart));
        
        obj.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };
    
    requestAnimationFrame(animate);
}

// Dynamic Greeting Based on Time
function updateGreeting() {
    const hour = new Date().getHours();
    const greetingEl = document.getElementById('greeting-text');
    
    if (hour < 12) {
        greetingEl.textContent = 'Good morning, SuperAdmin! Let’s start creating impactful academic templates today.';
    } else if (hour < 18) {
        greetingEl.textContent = 'Good afternoon, SuperAdmin! Keep your academic templates organized.';
    } else {
        greetingEl.textContent = 'Good evening, SuperAdmin! Keep building meaningful academic content.';
    }
}

// Add Ripple Effect to Cards
function createRipple(event) {
    const card = event.currentTarget;
    const ripple = document.createElement('span');
    const rect = card.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.3);
        left: ${x}px;
        top: ${y}px;
        pointer-events: none;
        animation: ripple 0.6s ease-out;
    `;
    
    card.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
}

// Add ripple animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', () => {
    // Update greeting
    updateGreeting();
    
    // Animate counters with delay
    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach(element => {
            const endValue = parseInt(element.getAttribute('data-count'));
            animateValue(element.id, 0, endValue, 2000);
        });
    }, 300);
    
    // Add ripple effect to stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('click', createRipple);
    });
    
    // Add ripple effect to template cards
    document.querySelectorAll('.template-card').forEach(card => {
        card.addEventListener('click', createRipple);
    });
    
    // Parallax effect on mouse move
    document.addEventListener('mousemove', (e) => {
        const cards = document.querySelectorAll('.stat-card, .template-card');
        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;
        
        cards.forEach((card, index) => {
            const speed = (index % 3 + 1) * 0.5;
            const xOffset = (x - 0.5) * speed;
            const yOffset = (y - 0.5) * speed;
            
            card.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
        });
    });
});
</script>

@endsection
<!-- Backup Case -->

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
                        

