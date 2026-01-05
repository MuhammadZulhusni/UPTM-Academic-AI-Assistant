@extends('superadmin.dashboard')

@section('superadmin')

<style>
:root {
    --primary: #3b82f6;
    --primary-light: #60a5fa;
    --primary-dark: #2563eb;
    --success: #10b981;
    --success-light: #34d399;
    --warning: #f59e0b;
    --warning-light: #fbbf24;
    --danger: #ef4444;
    --danger-light: #f87171;
    --purple: #8b5cf6;
    --purple-light: #a78bfa;
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

.nk-content-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

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
    background: var(--success);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Stats Section */
.stats-section {
    margin-bottom: 2rem;
}

.section-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-label::before {
    content: '';
    width: 3px;
    height: 14px;
    background: var(--primary);
    border-radius: 2px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
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
    background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.stat-card:hover::before {
    transform: scaleX(1);
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
    transform: rotate(360deg) scale(1.1);
}

.stat-icon em {
    font-size: 1.5rem;
}

.stat-growth {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    background: rgba(0,0,0,0.03);
}

.stat-growth.positive {
    color: var(--success);
    background: rgba(16, 185, 129, 0.1);
}

.stat-growth.negative {
    color: var(--danger);
    background: rgba(239, 68, 68, 0.1);
}

.stat-growth.neutral {
    color: var(--text-secondary);
}

/* Card Color Variants */
.stat-card.blue {
    --card-color: var(--primary);
    --card-color-light: var(--primary-light);
}

.stat-card.blue .stat-icon {
    background: #eff6ff;
}

.stat-card.blue .stat-icon em {
    color: var(--primary);
}

.stat-card.green {
    --card-color: var(--success);
    --card-color-light: var(--success-light);
}

.stat-card.green .stat-icon {
    background: #f0fdf4;
}

.stat-card.green .stat-icon em {
    color: var(--success);
}

.stat-card.orange {
    --card-color: var(--warning);
    --card-color-light: var(--warning-light);
}

.stat-card.orange .stat-icon {
    background: #fffbeb;
}

.stat-card.orange .stat-icon em {
    color: var(--warning);
}

.stat-card.purple {
    --card-color: var(--purple);
    --card-color-light: var(--purple-light);
}

.stat-card.purple .stat-icon {
    background: #faf5ff;
}

.stat-card.purple .stat-icon em {
    color: var(--purple);
}

.stat-card.red {
    --card-color: var(--danger);
    --card-color-light: var(--danger-light);
}

.stat-card.red .stat-icon {
    background: #fef2f2;
}

.stat-card.red .stat-icon em {
    color: var(--danger);
}

/* Quick Insights Cards */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.insight-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.5rem;
    transition: var(--transition);
}

.insight-card:hover {
    box-shadow: var(--shadow-lg);
}

.insight-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.insight-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.insight-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-hover);
    border-radius: 8px;
}

.insight-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.insight-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border);
}

.insight-item:last-child {
    border-bottom: none;
}

.insight-item-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.insight-item-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
}

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

.template-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary);
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
}

.template-content h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
    line-height: 1.4;
}

.template-content p {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.6;
    margin: 0;
}

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

.animate-in {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.animate-delay-1 { animation-delay: 0.1s; }
.animate-delay-2 { animation-delay: 0.2s; }
.animate-delay-3 { animation-delay: 0.3s; }
.animate-delay-4 { animation-delay: 0.4s; }
.animate-delay-5 { animation-delay: 0.5s; }
.animate-delay-6 { animation-delay: 0.6s; }

@media (max-width: 768px) {
    .nk-content-inner {
        padding: 1.5rem 1rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .insights-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="page-header animate-in">
            <h2>Welcome back, {{ $user->name }}</h2>
            <div class="greeting-subtext">
                <span class="status-dot"></span>
                <span id="greeting-text">All systems operational</span>
            </div>
        </div>

        <!-- User Statistics Section -->
        <div class="stats-section">
            <div class="section-label">
                <span>User Metrics</span>
            </div>
            <div class="stats-grid">
                <div class="stat-card blue animate-in animate-delay-1">
                    <div class="stat-icon">
                        <em class="icon ni ni-users"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $totalUsers }}">0</span>
                        </div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-user-list"></em>
                            <span>Total users in system</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card green animate-in animate-delay-2">
                    <div class="stat-icon">
                        <em class="icon ni ni-user-add"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $newUsersCount }}">0</span>
                        </div>
                        <div class="stat-label">New Users (7 days)</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-clock"></em>
                            <span>Last week registrations</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card purple animate-in animate-delay-3">
                    <div class="stat-icon">
                        <em class="icon ni ni-activity"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $activeUsersThisWeek }}">0</span>
                        </div>
                        <div class="stat-label">Active Users (Week)</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-check-circle"></em>
                            <span>Generated content this week</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card orange animate-in animate-delay-4">
                    <div class="stat-icon">
                        <em class="icon ni ni-spark"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $activeUsersToday }}">0</span>
                        </div>
                        <div class="stat-label">Active Today</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-calendar"></em>
                            <span>Users active today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template Statistics Section -->
        <div class="stats-section">
            <div class="section-label">
                <span>Template Metrics</span>
            </div>
            <div class="stats-grid">
                <div class="stat-card blue animate-in animate-delay-1">
                    <div class="stat-icon">
                        <em class="icon ni ni-template"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $totalTemplates }}">0</span>
                        </div>
                        <div class="stat-label">Total Templates</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-file-docs"></em>
                            <span>All templates created</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card green animate-in animate-delay-2">
                    <div class="stat-icon">
                        <em class="icon ni ni-check-circle"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $activeTemplates }}">0</span>
                        </div>
                        <div class="stat-label">Active Templates</div>
                        <div class="stat-growth positive">
                            <em class="icon ni ni-done"></em>
                            <span>Currently available</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card orange animate-in animate-delay-3">
                    <div class="stat-icon">
                        <em class="icon ni ni-book-read"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $studentTemplateCount }}">0</span>
                        </div>
                        <div class="stat-label">Student Templates</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-user"></em>
                            <span>For students</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card purple animate-in animate-delay-4">
                    <div class="stat-icon">
                        <em class="icon ni ni-users"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $lecturerTemplateCount }}">0</span>
                        </div>
                        <div class="stat-label">Lecturer Templates</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-user-alt"></em>
                            <span>For lecturers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Statistics Section -->
        <div class="stats-section">
            <div class="section-label">
                <span>Document Metrics</span>
            </div>
            <div class="stats-grid">
                <div class="stat-card blue animate-in animate-delay-1">
                    <div class="stat-icon">
                        <em class="icon ni ni-file-docs"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $totalDocuments }}">0</span>
                        </div>
                        <div class="stat-label">Total Documents</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-files"></em>
                            <span>All generated documents</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card green animate-in animate-delay-2">
                    <div class="stat-icon">
                        <em class="icon ni ni-spark"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $documentsToday }}">0</span>
                        </div>
                        <div class="stat-label">Generated Today</div>
                        <div class="stat-growth positive">
                            <em class="icon ni ni-calendar"></em>
                            <span>Today's activity</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card purple animate-in animate-delay-3">
                    <div class="stat-icon">
                        <em class="icon ni ni-bar-chart"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $documentsThisWeek }}">0</span>
                        </div>
                        <div class="stat-label">This Week</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-clock"></em>
                            <span>Last 7 days</span>
                        </div>
                    </div>
                </div>

                <!-- <div class="stat-card orange animate-in animate-delay-4">
                    <div class="stat-icon">
                        <em class="icon ni ni-edit"></em>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <span data-count="{{ $avgWordsPerDocument }}">0</span>
                        </div>
                        <div class="stat-label">Avg Words/Doc</div>
                        <div class="stat-growth neutral">
                            <em class="icon ni ni-file-text"></em>
                            <span>Average content length</span>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Quick Insights -->
        <div class="stats-section">
            <div class="section-label">
                <span>Quick Insights</span>
            </div>
            <div class="insights-grid">
                <!-- Popular Templates -->
                <div class="insight-card animate-in" style="animation-delay: 0.2s;">
                    <div class="insight-header">
                        <h4 class="insight-title">Most Used Templates</h4>
                        <div class="insight-icon">
                            <em class="icon ni ni-growth" style="color: var(--primary);"></em>
                        </div>
                    </div>
                    <ul class="insight-list">
                        @forelse($popularTemplates as $template)
                        <li class="insight-item">
                            <span class="insight-item-label">{{ $template->title }}</span>
                            <span class="insight-item-value">{{ $template->generated_contents_count }} uses</span>
                        </li>
                        @empty
                        <li class="insight-item">
                            <span class="insight-item-label">No data available</span>
                        </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Top Active Users -->
                <div class="insight-card animate-in" style="animation-delay: 0.3s;">
                    <div class="insight-header">
                        <h4 class="insight-title">Most Active Users</h4>
                        <div class="insight-icon">
                            <em class="icon ni ni-user-list" style="color: var(--success);"></em>
                        </div>
                    </div>
                    <ul class="insight-list">
                        @forelse($topUsers as $topUser)
                        <li class="insight-item">
                            <span class="insight-item-label">{{ $topUser->name }}</span>
                            <span class="insight-item-value">{{ $topUser->generated_contents_count }} docs</span>
                        </li>
                        @empty
                        <li class="insight-item">
                            <span class="insight-item-label">No data available</span>
                        </li>
                        @endforelse
                    </ul>
                </div>

                <!-- System Health -->
                <!-- <div class="insight-card animate-in" style="animation-delay: 0.4s;">
                    <div class="insight-header">
                        <h4 class="insight-title">System Health</h4>
                        <div class="insight-icon">
                            <em class="icon ni ni-shield-check" style="color: var(--purple);"></em>
                        </div>
                    </div>
                    <ul class="insight-list">
                        <li class="insight-item">
                            <span class="insight-item-label">Document Retention</span>
                            <span class="insight-item-value">{{ $documentRetentionDays }} days</span>
                        </li>
                        <li class="insight-item">
                            <span class="insight-item-label">Activity Log Retention</span>
                            <span class="insight-item-value">{{ $activityLogRetentionDays }} days</span>
                        </li>
                        <li class="insight-item">
                            <span class="insight-item-label">Docs to be Deleted</span>
                            <span class="insight-item-value" style="color: {{ $documentsToBeDeleted > 0 ? 'var(--warning)' : 'var(--success)' }}">
                                {{ $documentsToBeDeleted }}
                            </span>
                        </li>
                        <li class="insight-item">
                            <span class="insight-item-label">Inactive Templates</span>
                            <span class="insight-item-value" style="color: {{ $inactiveTemplates > 0 ? 'var(--warning)' : 'var(--success)' }}">
                                {{ $inactiveTemplates }}
                            </span>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>

        <!-- Templates Section -->
        <div class="section-header animate-in" style="animation-delay: 0.5s;">
            <h3 class="section-title">Recent Templates</h3>
            <a href="{{ route('superadmin.template') }}" class="section-link">
                <span>View all</span>
                <em class="icon ni ni-arrow-right"></em>
            </a>
        </div>

        <div class="templates-grid">
            @foreach ($templates as $index => $template)
            <a href="{{ route('superadmin.details.template', $template->id) }}" 
               class="template-card animate-in" 
               style="animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                <div class="template-icon-wrapper">
                    <img src="{{ asset('upload/template/' . $template->icon) }}"
                         alt="{{ $template->title }}"
                         class="template-icon-img">
                </div>
                <div class="template-content">
                    <h3>{{ $template->title }}</h3>
                    <p>{{ Str::limit($template->description, 80) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

<script>
function animateValue(element, start, end, duration) {
    if (start === end) return;
    
    const range = end - start;
    const startTime = performance.now();
    
    const animate = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.floor(start + (range * easeOutQuart));
        
        element.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };
    
    requestAnimationFrame(animate);
}

function updateGreeting() {
    const hour = new Date().getHours();
    const greetingEl = document.getElementById('greeting-text');
    
    if (hour < 12) {
        greetingEl.textContent = 'Good morning! Dashboard shows real-time system metrics.';
    } else if (hour < 18) {
        greetingEl.textContent = 'Good afternoon! Monitor your platform performance.';
    } else {
        greetingEl.textContent = 'Good evening! All systems running smoothly.';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateGreeting();
    
    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach(element => {
            const endValue = parseInt(element.getAttribute('data-count'));
            animateValue(element, 0, endValue, 2000);
        });
    }, 300);
});

document.addEventListener('DOMContentLoaded', () => {
    updateGreeting();
    
    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach(element => {
            const endValue = parseInt(element.getAttribute('data-count'));
            animateValue(element, 0, endValue, 2000);
        });
    }, 300);
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
                        

