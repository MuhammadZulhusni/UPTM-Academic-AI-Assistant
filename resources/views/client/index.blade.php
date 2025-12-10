@extends('client.client_dashboard')

@section('client')

<style>
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #ec4899;
    --success: #10b981;
    --text: #0f172a;
    --text-light: #64748b;
    --bg: #f8fafc;
    --card: #ffffff;
    --glass: rgba(255, 255, 255, 0.7);
    --shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    --shadow-lg: 0 20px 60px rgba(99, 102, 241, 0.15);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: var(--bg);
    font-family: 'Inter', -apple-system, sans-serif;
    color: var(--text);
}

.dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero with Gradient Mesh */
.hero {
    background: 
        radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.15) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(236, 72, 153, 0.1) 0px, transparent 50%),
        linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
    border-radius: 32px;
    padding: 4rem 3rem;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: var(--shadow-lg);
}

.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(45deg, transparent 40%, rgba(255, 255, 255, 0.3) 50%, transparent 60%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.hero-inner {
    position: relative;
    z-index: 1;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
}

.badge::before {
    content: '●';
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.hero h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, var(--text), var(--primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.03em;
    line-height: 1.1;
}

.hero .subtitle {
    font-size: 1.125rem;
    color: var(--text-light);
    margin-bottom: 2.5rem;
    max-width: 600px;
}

/* Glassmorphic Stats Cards */
.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.stat-card {
    background: var(--glass);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
    transform: translateX(-100%);
    transition: transform 0.5s;
}

.stat-card:hover::before {
    transform: translateX(0);
}

.stat-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 32px 64px rgba(99, 102, 241, 0.2);
    border-color: var(--primary);
}

.stat-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.stat-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
    transition: all 0.4s;
}

.stat-card:hover .stat-icon {
    transform: rotate(360deg) scale(1.1);
    box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
}

.stat-icon em {
    font-size: 2rem;
    color: white;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.stat-info {
    font-size: 0.875rem;
    color: var(--text-light);
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.zero-msg {
    font-size: 0.875rem;
    color: var(--text-light);
    background: rgba(99, 102, 241, 0.05);
    padding: 1rem;
    border-radius: 12px;
    border-left: 3px solid var(--primary);
}

/* Templates Section */
.section {
    margin-top: 4rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--text), var(--primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(99, 102, 241, 0.4);
}

.templates {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 2rem;
}

.template {
    background: white;
    border-radius: 24px;
    padding: 2.5rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.template::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(139, 92, 246, 0.05));
    opacity: 0;
    transition: opacity 0.4s;
}

.template:hover::before {
    opacity: 1;
}

.template:hover {
    transform: translateY(-8px);
    border-color: var(--primary);
    box-shadow: 0 24px 48px rgba(99, 102, 241, 0.15);
}

.template-icon {
    width: 72px;
    height: 72px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    transition: all 0.4s;
}

.template:hover .template-icon {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    transform: scale(1.1) rotate(-5deg);
}

.template-icon img {
    width: 40px;
    height: 40px;
    transition: filter 0.4s;
}

.template:hover .template-icon img {
    filter: brightness(0) invert(1);
}

.template h3 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: var(--text);
}

.template p {
    color: var(--text-light);
    line-height: 1.6;
}

/* Animations */
.fade-in {
    animation: fadeIn 0.8s ease-out forwards;
    opacity: 0;
}

@keyframes fadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in { transform: translateY(20px); }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }

@media (max-width: 768px) {
    .dashboard { padding: 1.5rem; }
    .hero { padding: 2.5rem 2rem; }
    .hero h1 { font-size: 2.5rem; }
    .stats { grid-template-columns: 1fr; }
    .section-header { flex-direction: column; gap: 1.5rem; align-items: flex-start; }
    .templates { grid-template-columns: 1fr; }
}
</style>

<div class="dashboard">
    <!-- Hero -->
    <div class="hero fade-in">
        <div class="hero-inner">
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <div class="badge" id="time-badge">
                    <span id="greeting">Good morning</span>
                </div>
                
                <div class="badge" style="background: linear-gradient(135deg, var(--accent), var(--secondary));">
                    <em class="icon ni ni-user-circle"></em>
                    <span>
                        @if($user->role == 'lecturer')
                            Lecturer
                        @else
                            Student
                        @endif
                    </span>
                </div>
            </div>
            
            <h1>Welcome, {{ $user->name }}</h1>
            
            <p class="subtitle" id="subtitle">Create amazing content with our powerful templates.</p>
            
            <div class="stats">
                <!-- Words -->
                <div class="stat-card fade-in delay-1">
                    <div class="stat-top">
                        <div>
                            @if ($totalWordsUsed > 0)
                                <div class="stat-number" data-count="{{ $totalWordsUsed }}">0</div>
                                <div class="stat-label">Words Generated</div>
                                <div class="stat-info">You're on fire! Keep creating amazing content</div>
                            @else
                                <div class="stat-label">Words Generated</div>
                                <div class="zero-msg">Ready to write your first masterpiece? Pick a template!</div>
                            @endif
                        </div>
                        <div class="stat-icon">
                            <em class="icon ni ni-pen"></em>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="stat-card fade-in delay-2">
                    <div class="stat-top">
                        <div>
                            @if ($totalDocuments > 0)
                                <div class="stat-number" data-count="{{ $totalDocuments }}">0</div>
                                <div class="stat-label">Documents</div>
                                <div class="stat-info">Your content history is building up nicely</div>
                            @else
                                <div class="stat-label">Documents</div>
                                <div class="zero-msg">Generate your first content to start building history!</div>
                            @endif
                        </div>
                        <div class="stat-icon">
                            <em class="icon ni ni-file-docs"></em>
                        </div>
                    </div>
                </div>
                
                <!-- Templates -->
                <div class="stat-card fade-in delay-3">
                    <div class="stat-top">
                        <div>
                            @if ($totalTemplates > 0)
                                <div class="stat-number" data-count="{{ $totalTemplates }}">0</div>
                                <div class="stat-label">Templates</div>
                                <div class="stat-info">Powerful templates at your fingertips</div>
                            @else
                                <div class="stat-label">Templates</div>
                                <div class="zero-msg">New templates are coming soon. Stay tuned!</div>
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

    <!-- Templates -->
    <div class="section">
        <div class="section-header">
            <h2 class="section-title">Templates</h2>
            <a href="{{ route('user.template') }}" class="btn-primary">
                <span>View All</span>
                <em class="icon ni ni-arrow-right"></em>
            </a>
        </div>

        <div class="templates">
            @foreach ($templates as $template)
            <a href="{{ route('user.details.template', $template->id) }}" class="template">
                <div class="template-icon">
                    <img src="{{ asset('upload/template/' . $template->icon) }}" alt="{{ $template->title }}">
                </div>
                <h3>{{ $template->title }}</h3>
                <p>{{ $template->description }}</p>
            </a>
            @endforeach
        </div>
    </div>
</div>

<script>
const role = '{{ $user->role }}';

function init() {
    const hour = new Date().getHours();
    const greeting = document.getElementById('greeting');
    const subtitle = document.getElementById('subtitle');
    
    let time, msg;
    
    if (hour < 12) {
        time = 'Good morning';
        msg = role === 'lecturer' 
            ? 'Create engaging content for your courses today.'
            : 'Start your learning journey with powerful templates.';
    } else if (hour < 18) {
        time = 'Good afternoon';
        msg = role === 'lecturer'
            ? 'Build quality educational materials effortlessly.'
            : 'Continue your academic success with our tools.';
    } else {
        time = 'Good evening';
        msg = role === 'lecturer'
            ? 'Prepare tomorrow\'s lessons with ease.'
            : 'Keep progressing towards your goals.';
    }
    
    greeting.textContent = time;
    subtitle.textContent = msg;
}

function formatNumber(num) {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'k';
    return num.toString();
}

function animate(el, end) {
    let start = 0;
    const duration = 2000;
    const startTime = performance.now();
    
    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3);
        const value = Math.floor(start + (end - start) * ease);
        
        el.textContent = formatNumber(value);
        
        if (progress < 1) requestAnimationFrame(step);
    }
    
    requestAnimationFrame(step);
}

document.addEventListener('DOMContentLoaded', () => {
    init();
    
    setTimeout(() => {
        document.querySelectorAll('[data-count]').forEach((el, i) => {
            setTimeout(() => animate(el, parseInt(el.dataset.count)), i * 100);
        });
    }, 400);
});
</script>

@endsection