@extends('client.client_dashboard')

@section('client')

<style>
/* Root Variables - Enhanced & Elegant */
:root {
    --primary: #7c6ba1;
    --primary-light: #9b8abf;
    --primary-dark: #5d4d7a;
    --accent: #a893c9;
    --success: #6d9887;
    --warning: #be8c4e;
    --text-primary: #1a1a1a;
    --text-secondary: #4a4a4a;
    --text-muted: #757575;
    --bg-card: #ffffff;
    --bg-page: #f7f7f8;
    --bg-subtle: #ededf0;
    --bg-hover: #f5f5f7;
    --border: #d5d5d8;
    --border-soft: #e2e2e5;
    --shadow-soft: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-medium: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-large: 0 8px 32px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 12px 48px rgba(0, 0, 0, 0.12);
    --radius-sm: 12px;
    --radius-md: 16px;
    --radius-lg: 20px;
    --radius-xl: 24px;
    --transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(180deg, #f7f7f8 0%, #fafafa 100%);
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: var(--text-primary);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    font-weight: 400;
}

.nk-content-inner {
    max-width: 1320px;
    margin: 0 auto;
    padding: 3rem 2rem;
}

/* Welcome Hero Section - Enhanced */
.welcome-hero {
    background: linear-gradient(135deg, #ffffff 0%, #f9f8fc 100%);
    border-radius: var(--radius-xl);
    padding: 4rem 3.5rem;
    margin-bottom: 3rem;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-medium);
    position: relative;
    overflow: hidden;
}

.welcome-hero::before {
    content: '';
    position: absolute;
    top: -10%;
    right: -5%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(124, 107, 161, 0.12) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
    animation: float 20s ease-in-out infinite;
}

.welcome-hero::after {
    content: '';
    position: absolute;
    bottom: -15%;
    left: -8%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(155, 138, 191, 0.08) 0%, transparent 65%);
    border-radius: 50%;
    pointer-events: none;
    animation: float 15s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(1.05); }
}

.welcome-content {
    position: relative;
    z-index: 1;
}

.greeting-header {
    margin-bottom: 2rem;
}

.greeting-time {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.15em;
    margin-bottom: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
}

.greeting-time::before {
    content: '';
    width: 6px;
    height: 6px;
    background: var(--primary);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
}

.welcome-hero h1 {
    font-size: 2.75rem;
    font-weight: 300;
    margin: 0;
    color: var(--text-primary);
    letter-spacing: -0.02em;
    line-height: 1.25;
}

.welcome-hero h1 strong {
    font-weight: 600;
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    background: var(--bg-subtle);
    padding: 0.75rem 1.5rem;
    border-radius: 100px;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 600;
    margin: 1.5rem 0;
    border: 1px solid var(--border);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.role-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(124, 107, 161, 0.1), transparent);
    transition: left 0.5s ease;
}

.role-badge:hover::before {
    left: 100%;
}

.role-badge em {
    font-size: 1.1rem;
    color: var(--primary);
}

.welcome-hero p {
    font-size: 1.0625rem;
    color: var(--text-secondary);
    margin: 0 0 3rem 0;
    line-height: 1.7;
    max-width: 580px;
    font-weight: 400;
}

/* Stats Grid - Enhanced */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.75rem;
    margin-top: 3rem;
}

.stat-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    border: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(124, 107, 161, 0.02) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-light);
}

.stat-card:hover::before {
    transform: translateX(0);
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    position: relative;
    z-index: 1;
}

.stat-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-subtle);
    border-radius: 16px;
    transition: var(--transition-slow);
    border: 1px solid var(--border-soft);
    position: relative;
}

.stat-icon::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
    border-radius: 17px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.stat-icon em {
    font-size: 1.625rem;
    color: var(--primary);
    transition: var(--transition);
}

.stat-card:hover .stat-icon {
    background: var(--primary);
    border-color: var(--primary);
    transform: scale(1.08) rotate(-5deg);
}

.stat-card:hover .stat-icon::before {
    opacity: 1;
}

.stat-card:hover .stat-icon em {
    color: white;
    transform: scale(1.1);
}

.stat-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
    position: relative;
    z-index: 1;
}

.stat-card strong {
    font-size: 3rem;
    font-weight: 300;
    color: var(--text-primary);
    display: block;
    line-height: 1;
    letter-spacing: -0.03em;
    transition: var(--transition);
}

.stat-card:hover strong {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-card small {
    color: var(--text-secondary);
    font-size: 0.9375rem;
    font-weight: 600;
    letter-spacing: 0.01em;
    text-transform: uppercase;
    font-size: 0.75rem;
}

.stat-description {
    font-size: 0.8125rem;
    color: var(--text-muted);
    margin-top: 1rem;
    opacity: 0;
    transform: translateY(6px);
    transition: var(--transition);
    line-height: 1.6;
}

.stat-card:hover .stat-description {
    opacity: 1;
    transform: translateY(0);
}

/* Progress Bar */
.stat-progress {
    width: 100%;
    height: 4px;
    background: var(--bg-subtle);
    border-radius: 2px;
    margin-top: 1.25rem;
    overflow: hidden;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.stat-card:hover .stat-progress {
    opacity: 1;
}

.stat-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-light), var(--primary-dark));
    border-radius: 2px;
    width: 0;
    animation: progressFill 1.5s ease forwards;
}

@keyframes progressFill {
    to { width: 75%; }
}

/* Zero-State Messages */
.zero-state-message {
    font-size: 0.8125rem;
    color: var(--text-muted);
    background: var(--bg-subtle);
    padding: 1.25rem;
    border-radius: var(--radius-sm);
    line-height: 1.6;
    font-weight: 400;
    border: 1px solid var(--border-soft);
    margin-top: 0.5rem;
    position: relative;
    overflow: hidden;
}

.zero-state-message::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: var(--primary-light);
}

/* Templates Section - Enhanced */
.templates-section {
    margin-top: 5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 3rem;
    position: relative;
}

.section-header::after {
    content: '';
    position: absolute;
    bottom: -1rem;
    left: 0;
    width: 80px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), transparent);
    border-radius: 2px;
}

.section-header h2 {
    font-size: 2rem;
    font-weight: 400;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.02em;
    position: relative;
    display: inline-block;
}

.section-header h2::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 50%;
    transform: translateY(-50%);
    width: 6px;
    height: 6px;
    background: var(--primary);
    border-radius: 50%;
}

.explore-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.875rem 1.75rem;
    border-radius: 100px;
    background: var(--bg-subtle);
    border: 1px solid var(--border);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.explore-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.explore-link span,
.explore-link em {
    position: relative;
    z-index: 1;
}

.explore-link:hover::before {
    opacity: 1;
}

.explore-link:hover {
    color: white;
    border-color: var(--primary);
    box-shadow: var(--shadow-medium);
    transform: translateY(-2px);
}

.explore-link em {
    font-size: 0.75rem;
    transition: transform 0.3s ease;
}

.explore-link:hover em {
    transform: translateX(4px);
}

.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.template-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    text-decoration: none;
    color: inherit;
    transition: var(--transition-slow);
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    box-shadow: var(--shadow-soft);
    overflow: hidden;
}

.template-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(124, 107, 161, 0.03) 0%, transparent 60%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.template-card::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(155, 138, 191, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(50%, -50%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.template-card:hover::before,
.template-card:hover::after {
    opacity: 1;
}

.template-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
    border-color: var(--primary-light);
}

.template-icon {
    width: 64px;
    height: 64px;
    background: var(--bg-subtle);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-soft);
    transition: var(--transition-slow);
    position: relative;
    z-index: 1;
}

.template-icon::before {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
    border-radius: 19px;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.template-card:hover .template-icon {
    background: var(--bg-card);
    border-color: var(--primary-light);
    transform: scale(1.1) rotate(-5deg);
}

.template-card:hover .template-icon::before {
    opacity: 1;
}

.template-icon-img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    opacity: 0.8;
    transition: var(--transition);
}

.template-card:hover .template-icon-img {
    opacity: 1;
    transform: scale(1.1);
}

.template-content {
    position: relative;
    z-index: 1;
}

.template-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 0.75rem 0;
    color: var(--text-primary);
    line-height: 1.4;
    transition: color 0.3s ease;
    letter-spacing: -0.01em;
}

.template-card:hover .template-content h3 {
    color: var(--primary);
}

.template-content p {
    color: var(--text-secondary);
    font-size: 0.9375rem;
    line-height: 1.7;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.template-badge {
    position: absolute;
    top: 1.75rem;
    right: 1.75rem;
    background: var(--primary);
    color: white;
    font-size: 0.6875rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 100px;
    opacity: 0;
    transform: scale(0.85) translateY(-10px);
    transition: var(--transition);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(124, 107, 161, 0.3);
}

.template-card:hover .template-badge {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.template-badge.badge-new {
    background: linear-gradient(135deg, #be8c4e, #d4a66a);
}

.template-badge.badge-featured {
    background: linear-gradient(135deg, #8872a6, #9b8abf);
}

.template-badge.badge-updated {
    background: linear-gradient(135deg, #6d9887, #88b0a0);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-in {
    animation: fadeInUp 0.8s ease forwards;
    opacity: 0;
}

.animate-fade {
    animation: fadeIn 1s ease forwards;
    opacity: 0;
}

.animate-delay-1 { animation-delay: 0.15s; }
.animate-delay-2 { animation-delay: 0.25s; }
.animate-delay-3 { animation-delay: 0.35s; }

/* Responsive Design */
@media (max-width: 1024px) {
    .templates-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .nk-content-inner {
        padding: 2rem 1.5rem;
    }

    .welcome-hero {
        padding: 3rem 2rem;
    }

    .welcome-hero h1 {
        font-size: 2.25rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .stat-card {
        padding: 2rem;
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .section-header h2::before {
        display: none;
    }

    .templates-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .nk-content-inner {
        padding: 1.5rem 1rem;
    }

    .welcome-hero {
        padding: 2.5rem 1.5rem;
    }

    .welcome-hero h1 {
        font-size: 2rem;
    }

    .welcome-hero p {
        font-size: 1rem;
    }

    .stat-card strong {
        font-size: 2.5rem;
    }

    .template-card {
        padding: 2rem 1.75rem;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Selection styling */
::selection {
    background-color: var(--primary-light);
    color: white;
}

::-moz-selection {
    background-color: var(--primary-light);
    color: white;
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        
        <!-- Welcome Hero Section -->
        <div class="welcome-hero animate-fade">
            <div class="welcome-content">
                <div class="greeting-header">
                    <div class="greeting-time" id="greeting-time">Good morning</div>
                    <h1>Welcome, <strong>{{ $user->name }}</strong></h1>
                </div>
                
                <div class="role-badge">
                    <em class="icon ni ni-user-circle"></em>
                    <span>
                        @if($user->role == 'lecturer')
                            Lecturer
                        @else
                            Student
                        @endif
                    </span>
                </div>
                
                <p id="subtitle-message">Ready to explore your templates and create something great.</p>
                
                <div class="stats-grid">
                    <!-- Words Generated -->
                    <div class="stat-card animate-in animate-delay-1">
                        <div class="stat-header">
                            <div class="stat-content">
                                @if ($totalWordsUsed > 0)
                                    <strong data-count="{{ $totalWordsUsed }}">0</strong>
                                    <small>Words Generated</small>
                                    <div class="stat-description">Total content created across all documents</div>
                                    <div class="stat-progress">
                                        <div class="stat-progress-bar"></div>
                                    </div>
                                @else
                                    <small>Words Generated</small>
                                    <div class="zero-state-message">Begin by selecting a template to generate your first content.</div>
                                @endif
                            </div>
                            <div class="stat-icon">
                                <em class="icon ni ni-pen"></em>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents Created -->
                    <div class="stat-card animate-in animate-delay-2">
                        <div class="stat-header">
                            <div class="stat-content">
                                @if ($totalDocuments > 0)
                                    <strong data-count="{{ $totalDocuments }}">0</strong>
                                    <small>Documents Created</small>
                                    <div class="stat-description">Total documents you have generated</div>
                                    <div class="stat-progress">
                                        <div class="stat-progress-bar"></div>
                                    </div>
                                @else
                                    <small>Documents Created</small>
                                    <div class="zero-state-message">Your documents will appear here once you create them.</div>
                                @endif
                            </div>
                            <div class="stat-icon">
                                <em class="icon ni ni-file-docs"></em>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Templates Available -->
                    <div class="stat-card animate-in animate-delay-3">
                        <div class="stat-header">
                            <div class="stat-content">
                                @if ($totalTemplates > 0)
                                    <strong data-count="{{ $totalTemplates }}">0</strong>
                                    <small>Templates Available</small>
                                    <div class="stat-description">Ready-to-use templates for your role</div>
                                    <div class="stat-progress">
                                        <div class="stat-progress-bar"></div>
                                    </div>
                                @else
                                    <small>Templates Available</small>
                                    <div class="zero-state-message">No templates available at the moment.</div>
                                @endif
                            </div>
                            <div class="stat-icon">
                                <em class="icon ni ni-grid"></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Section -->
        <div class="templates-section">
            <div class="section-header animate-in" style="animation-delay: 0.5s;">
                <h2>Available Templates</h2>
                <a href="{{ route('user.template') }}" class="explore-link">
                    <span>View All</span>
                    <em class="icon ni ni-arrow-right"></em>
                </a>
            </div>

            <div class="templates-grid">
                @foreach ($templates as $index => $template)
                <a href="{{ route('user.details.template', $template->id) }}" class="template-card animate-in" style="animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                    <div class="template-icon">
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
</div>

<script>
// Get user role from blade
const userRole = '{{ $user->role }}';

// Dynamic greeting and messages based on time and role
function updateGreetingAndMessages() {
    const hour = new Date().getHours();
    const greetingTime = document.getElementById('greeting-time');
    const subtitleMessage = document.getElementById('subtitle-message');
    
    let timeGreeting, subtitle;
    
    // Determine time of day
    if (hour < 12) {
        timeGreeting = 'Good morning';
        if (userRole === 'lecturer') {
            subtitle = 'Access your teaching templates and create engaging content for your courses.';
        } else {
            subtitle = 'Start your learning journey with templates designed to help you succeed.';
        }
    } else if (hour < 18) {
        timeGreeting = 'Good afternoon';
        if (userRole === 'lecturer') {
            subtitle = 'Continue building quality educational materials with our template collection.';
        } else {
            subtitle = 'Discover templates that make your academic work easier and more efficient.';
        }
    } else {
        timeGreeting = 'Good evening';
        if (userRole === 'lecturer') {
            subtitle = 'Prepare tomorrow\'s materials using our comprehensive template library.';
        } else {
            subtitle = 'Keep progressing with templates that support your academic goals.';
        }
    }
    
    greetingTime.textContent = timeGreeting;
    subtitleMessage.textContent = subtitle;
}

// Smooth counter animation with easing
function animateValue(obj, start, end, duration) {
    if (start === end) return;
    
    const startTime = performance.now();
    
    const animate = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Gentle easing function
        const easeOutCubic = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(start + (end - start) * easeOutCubic);
        
        obj.textContent = current.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };
    
    requestAnimationFrame(animate);
}

document.addEventListener('DOMContentLoaded', () => {
    // Update greeting and messages
    updateGreetingAndMessages();
    
    // Animate stats with staggered timing
    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach((element, index) => {
            const endValue = parseInt(element.getAttribute('data-count'));
            setTimeout(() => {
                animateValue(element, 0, endValue, 2200);
            }, index * 200);
        });
    }, 600);
});
</script>

@endsection