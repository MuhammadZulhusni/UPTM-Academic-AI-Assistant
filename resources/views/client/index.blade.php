@extends('client.client_dashboard')

@section('client')

<style>
body {
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
    color: #334155;
}

.welcome-hero {
    background: #fff;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.welcome-hero h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: #1e293b;
}

.welcome-hero p {
    font-size: 1rem;
    color: #64748b;
    margin: 0 0 2rem 0;
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 1.5rem 1rem;
    text-align: center;
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); /* Added shadow to stat cards */
}

.stat-icon {
    font-size: 1.75rem;
    color: #3b82f6; /* Soft blue color */
    margin-bottom: 0.5rem;
}

.stat-card strong {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    display: block;
    margin-bottom: 0.25rem;
}

.stat-card small {
    color: #64748b;
    font-size: 0.85rem;
}

/* Zero-State Messages */
.zero-state-message {
    font-size: 0.8rem;
    color: #4b5563;
    background-color: #f1f5f9;
    padding: 0.75rem;
    border-radius: 8px;
    line-height: 1.4;
    font-weight: 500;
}

/* Templates Section */
.templates-section {
    margin-top: 3rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.explore-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.explore-link:hover {
    color: #2563eb;
    transform: translateX(2px);
}

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
        
        <!-- Welcome Hero Section -->
        <div class="welcome-hero">
            <h1>Welcome back, {{ $user->name }}! 🎓</h1>
            <p>Your academic journey, simplified.</p>
            
            <div class="stats-grid">
                <!-- Words Generated -->
                <div class="stat-card">
                    <em class="icon ni ni-pen stat-icon"></em>
                    @if ($totalWordsUsed > 0)
                        <strong data-count="{{ $totalWordsUsed }}">0</strong>
                        <small>Words Generated</small>
                    @else
                        <div class="zero-state-message">Start generating your first document to see your words here.</div>
                    @endif
                </div>
                
                <!-- Documents Created -->
                <div class="stat-card">
                    <em class="icon ni ni-file-docs stat-icon"></em>
                    @if ($totalDocuments > 0)
                        <strong data-count="{{ $totalDocuments }}">0</strong>
                        <small>Documents Created</small>
                    @else
                        <div class="zero-state-message">You haven't created any documents yet.</div>
                    @endif
                </div>
                
                <!-- Templates Available -->
                <div class="stat-card">
                    <em class="icon ni ni-grid stat-icon"></em>
                    @if ($totalTemplates > 0)
                        <strong data-count="{{ $totalTemplates }}">0</strong>
                        <small>Templates Available</small>
                    @else
                        <div class="zero-state-message">No templates available.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Templates Section -->
        <div class="templates-section">
            <div class="section-header">
                <h2>Academic Templates</h2>
                <a href="{{ route('user.template') }}" class="explore-link">
                    View All Templates <em class="icon ni ni-arrow-right"></em>
                </a>
            </div>

            <div class="templates-grid">
                @foreach ($templates as $template)
                <a href="{{ route('user.details.template',$template->id) }}" class="template-card">
                    <div class="template-icon">
                        <img src="{{ asset('upload/template/' . $template->icon) }}"
                            alt="Template Icon"
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
    // Smooth number animation
    function animateValue(obj, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Animate stats with staggered timing
        document.querySelectorAll('[data-count]').forEach((element, index) => {
            const endValue = parseInt(element.getAttribute('data-count'));
            setTimeout(() => {
                animateValue(element, 0, endValue, 1000);
            }, index * 200);
        });
    });
</script>

@endsection
