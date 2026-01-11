@extends('client.client_dashboard')
@section('client') 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
    :root {
        --primary-color: #007bff;
        --primary-hover: #0056b3;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --dark-color: #343a40;
        --border-color: #dee2e6;
        --card-bg: #ffffff;
        --panel-bg: #f8f9fa;
        --font-family-base: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* --- Base Resets & Typography --- */
    body {
        font-family: var(--font-family-base);
        background-color: var(--panel-bg);
        color: #495057;
    }

    .display-6 {
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.25rem;
    }

    /* --- Back Button Styles --- */
    .back-button-wrapper {
        margin-bottom: 1.5rem;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #6c757d;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .btn-back:hover {
        color: var(--primary-color);
        background: #f8f9fa;
        border-color: var(--primary-color);
        transform: translateX(-3px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
        text-decoration: none;
    }
    
    .btn-back .icon {
        font-size: 1.1rem;
        transition: transform 0.2s ease;
    }
    
    .btn-back:hover .icon {
        transform: translateX(-3px);
    }
    
    
    /* --- Main Structure & Layout --- */
    
    .page-container {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .main-content-card {
        border: none;
        border-radius: 16px; 
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        background-color: var(--card-bg);
        display: flex;
        flex-wrap: wrap;
    }

    /* --- Control Panel (Left Side - Form) --- */
    .control-panel {
        background-color: var(--panel-bg); 
        border-radius: 16px 0 0 16px; 
        padding: 30px;
        border-right: 1px solid var(--border-color);
        position: relative;
    }

    @media (min-width: 992px) {
        .control-panel {
            min-height: 100%;
        }
        .form-sticky-wrap {
            position: sticky;
            top: 2rem;
        }
    }


    /* --- Editor Panel (Right Side) --- */
    .editor-panel-wrap {
        padding: 30px;
        flex-grow: 1;
        background-color: var(--card-bg);
        border-radius: 0 16px 16px 0;
    }

    .nk-editor {
        position: relative; 
        border: 1px solid var(--border-color);
        border-radius: 12px; 
        overflow: hidden;
        background-color: var(--card-bg);
        height: 100%; 
        display: flex;
        flex-direction: column;
        min-height: 600px;
    }

    .nk-editor-header {
        background-color: #f0f3f7;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nk-editor-title h4 {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 1.15rem;
    }

    .nk-editor-main {
        flex-grow: 1;
        overflow-y: auto; 
    }

    .nk-editor-body {
        padding: 1.5rem; 
        line-height: 1.7;
    }

    /* --- Form Elements --- */
    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        box-shadow: none;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.25);
    }

    /* --- Buttons & Export --- */
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        font-weight: 600;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
        transition: all 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
    }
    
    /* --- Loading Overlay & Animations (Organized Position) --- */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 12px; 
        background: rgba(255, 255, 255, 0.98); 
        backdrop-filter: blur(3px); 
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    /* *** REFINED LOADING SPINNER CONTAINER *** */
    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px; /* Increased vertical spacing between elements */
        text-align: center;
    }

    .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* *** REFINED PROGRESS BAR & TEXT *** */
    .loading-spinner > p {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0;
    }
    
    .progress-bar {
        position: relative;
        overflow: hidden;
        width: 250px; /* Increased width for better visual impact */
        height: 8px; /* Slightly thicker */
        background: #e9ecef;
        border-radius: 4px;
        margin-top: 10px;
    }

    .progress-fill {
        position: absolute;
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), #0056b3);
        width: 0%;
        left: 0;
        animation: progressLoad 2s linear infinite;
    }
    
    @keyframes progressLoad {
        0% { width: 0%; left: -100%; }
        50% { width: 100%; left: 0%; }
        100% { width: 0%; left: 100%; }
    }
    
    .typing-indicator {
        margin-top: 15px; /* Added spacing below the progress bar */
        display: flex;
        align-items: center;
        color: var(--secondary-color);
    }
    
    .typing-indicator .dot {
        width: 8px; /* Slightly smaller dots */
        height: 8px;
        background-color: var(--primary-color);
        animation: typingDots 1.4s infinite ease-in-out both;
        border-radius: 50%;
        margin: 0 4px;
    }
    
    .typing-indicator .dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator .dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-indicator .dot:nth-child(3) { animation-delay: 0s; }
    
    @keyframes typingDots {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }


    /* --- Placeholder & Icons --- */
    .placeholder-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 400px;
        padding: 20px;
        color: #adb5bd; 
    }
    
    .ni-edit-alt {
        color: #d1d9e2 !important;
        font-size: 6rem !important; 
        opacity: 0.6 !important;
    }
    
    /* --- Generated Content Styling (PROFESSIONAL DOCUMENT LAYOUT) --- */

    /* AGGRESSIVE: Force normal weight on EVERYTHING in editor */
    #editor-v1,
    #editor-v1 *,
    #editor-v1 p,
    #editor-v1 div,
    #editor-v1 span,
    #editor-v1 strong,
    #editor-v1 b,
    #editor-v1 em,
    #editor-v1 i,
    #editor-v1 h1,
    #editor-v1 h2,
    #editor-v1 h3,
    #editor-v1 h4,
    #editor-v1 h5,
    #editor-v1 h6,
    .nk-editor-body,
    .nk-editor-body *,
    .content-wrapper,
    .content-wrapper *,
    .content-paragraph,
    .content-paragraph *,
    .content-heading {
        font-weight: 400 !important; /* FORCE normal weight everywhere */
        color: #2c3e50 !important; /* Professional dark gray */
    }

    /* Content wrapper - professional document styling */
    .content-wrapper {
        max-width: 100%;
        padding: 2rem 2.5rem;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Paragraphs - organized document style */
    .content-paragraph {
        margin-bottom: 1.5rem;
        line-height: 1.75;
        font-size: 1rem !important;
        font-weight: 400 !important;
        color: #2c3e50 !important;
        text-align: justify;
        text-justify: inter-word;
    }

    /* First paragraph - no top margin */
    .content-wrapper > .content-paragraph:first-child,
    .content-wrapper > p:first-child {
        margin-top: 0;
    }

    /* Last paragraph - reduced bottom margin */
    .content-wrapper > .content-paragraph:last-child,
    .content-wrapper > p:last-child {
        margin-bottom: 0;
    }

    /* Headings - subtle distinction while maintaining uniformity */
    .content-heading,
    #editor-v1 h1,
    #editor-v1 h2,
    #editor-v1 h3,
    #editor-v1 h4,
    #editor-v1 h5,
    #editor-v1 h6 {
        font-size: 1rem !important;
        font-weight: 400 !important;
        color: #2c3e50 !important;
        margin-top: 2rem;
        margin-bottom: 1rem;
        line-height: 1.6;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e8ecef; /* Subtle separator */
    }

    /* First heading - no top margin */
    .content-wrapper > .content-heading:first-child,
    .content-wrapper > h1:first-child,
    .content-wrapper > h2:first-child,
    .content-wrapper > h3:first-child {
        margin-top: 0;
    }

    /* Professional spacing between sections */
    .content-paragraph + .content-heading,
    p + h1, p + h2, p + h3, p + h4 {
        margin-top: 2.5rem;
    }

    /* Lists (if AI generates them) */
    #editor-v1 ul,
    #editor-v1 ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
        line-height: 1.75;
    }

    #editor-v1 li {
        margin-bottom: 0.5rem;
        font-size: 1rem !important;
        color: #2c3e50 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 1.5rem 1.25rem;
        }
        
        .content-paragraph {
            font-size: 0.95rem !important;
            text-align: left; /* Remove justify on mobile */
        }
    }
    
    /* NEW: AI Suggestion Styles */
    .ai-suggestion-wrapper {
        position: relative;
        margin-top: 0.5rem;
    }

    .ai-suggest-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .ai-suggest-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.15);
    }

    .ai-suggest-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .ai-suggest-btn .spinner-border {
        width: 14px;
        height: 14px;
        border-width: 2px;
    }

    .suggestions-dropdown {
        margin-top: 0.75rem;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 0.75rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .suggestion-item {
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: start;
        gap: 0.75rem;
    }

    .suggestion-item:hover {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-color: var(--primary-color);
        transform: translateX(4px);
    }

    .suggestion-item:last-child {
        margin-bottom: 0;
    }

    .suggestion-icon {
        color: var(--primary-color);
        font-size: 1.1rem;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .suggestion-text {
        flex-grow: 1;
        color: #495057;
        font-size: 0.95rem;
        line-height: 1.5;
    }
</style>


<div class="nk-content-inner">
    <div class="nk-content-body page-container">

        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="{{ route('user.template') }}" class="btn-back">
                <em class="icon ni ni-arrow-left"></em>
                <span>Back to All Templates</span>
            </a>
        </div>
        
        <div class="nk-block-head nk-page-head mb-4">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">{{ $template->title }}</h2>
                    <p class="text-muted">{{ $template->description }}</p>
                </div>
            </div>
        </div>
        
        <div class="main-content-card row gx-0">
            
            <div class="col-lg-4 control-panel">
                <div class="form-sticky-wrap">

                    <form id="generateForm" action="{{ route('user.content.generate', $template->id) }}" method="post">
                        @csrf  

                        <div class="form-group mb-4">
                            <label for="language" class="form-label">Language</label>
                            <select name="language" class="form-select" id="language" required>
                                <option value="">Select Language</option>
                                <option value="English">English</option>
                                <option value="Bahasa Melayu">Bahasa Melayu</option>
                            </select>
                        </div>  
                        
                        @foreach ($template->inputFields as $field)
                        <div class="form-group mb-4">
                            <label for="{{ $field->title }}" class="form-label">{{ $field->title }}</label>
                            @if ($field->type === 'text')
                                <input type="text" 
                                    name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    class="form-control" 
                                    placeholder="Enter {{ $field->title }}"
                                    maxlength="10000"
                                    required>
                            @elseif ($field->type === 'textarea')
                                <textarea name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    rows="4" 
                                    class="form-control ai-textarea" 
                                    placeholder="Provide detailed description..."
                                    data-field-name="{{ $field->title }}"
                                    maxlength="50000"
                                    required></textarea>
                                
                                <!-- NEW: AI Suggestion Button & Container -->
                                <div class="ai-suggestion-wrapper">
                                    <button type="button" 
                                            class="ai-suggest-btn" 
                                            data-target="{{ $field->title }}"
                                            disabled>
                                        <span class="btn-text">Get AI Suggestions</span>
                                        <span class="btn-loading" style="display: none;">
                                            <span class="spinner-border" role="status"></span>
                                        </span>
                                    </button>
                                    <div class="suggestions-dropdown" 
                                         id="suggestions-{{ str_replace(' ', '-', $field->title) }}" 
                                         style="display: none;">
                                    </div>
                                </div>
                            @endif
                            <small class="form-text text-muted">{{ $field->description }}</small>
                        </div>
                        @endforeach

                        <div class="form-group mb-4">
                            <label for="ai_model" class="form-label">AI Model</label>
                            <select name="ai_model" class="form-select" id="ai_model">
                                <option value="gpt-3.5-turbo" selected>OpenAI | GPT-3.5-turbo (Faster)</option>
                                <option value="gpt-4">OpenAI | GPT 4 (Higher Quality)</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label for="result_length" class="form-label">Result Length (words)</label>
                            <input type="number" name="result_length" class="form-control" 
                                   id="result_length" value="100" min="1" max="5000" required>
                            <small class="form-text text-muted">Set the desired length for the output.</small>
                        </div>
                        
                        <div class="d-grid gap-2 pt-2 pb-4">
                            <button type="submit" class="btn btn-primary" id="generateBtn">
                                <span class="btn-text">Generate Content</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Generating...
                                </span>
                            </button> 
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-8 editor-panel-wrap">
                <div class="nk-editor">
                    
                    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                            <p>AI is crafting your content...</p>
                            
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            
                            <div class="typing-indicator">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <span class="ms-3 text-dark fw-bold" id="loadingText">Analyzing request...</span>
                            </div>
                        </div>
                    </div>

                    <div class="nk-editor-header">
                        <div class="nk-editor-title">
                            <h4 class="me-3 mb-0 line-clamp-1">{{ $template->title }} Output</h4>
                        </div>
                        <div class="nk-editor-tools d-flex align-items-center mt-2 mt-sm-0">
                            <ul class="d-inline-flex gap gx-3 gx-lg-4 pe-4 pe-lg-5 mb-0 list-unstyled">
                                <li>
                                    <span class="sub-text text-nowrap">Words <span class="text-dark fw-bold" id="word-count">0</span></span>
                                </li>
                                <li>
                                    <span class="sub-text text-nowrap">Characters <span class="text-dark fw-bold" id="char-count">0</span></span>
                                </li>
                            </ul>
                            <div class="dropdown">
                                <button class="btn btn-md btn-light rounded-pill" type="button" data-bs-toggle="dropdown">
                                    <em class="icon ni ni-download me-1"></em>
                                    <span>Export</span>
                                    <em class="icon ni ni-chevron-down ms-1"></em>
                                </button> 
                                <ul class="dropdown-menu dropdown-menu-end mt-2">
                                    <li><a href="#" class="dropdown-item" id="copy-text">
                                        <em class="icon ni ni-copy me-2"></em>Copy Text</a></li>
                                        <li><a href="#" class="dropdown-item" id="download-pdf">
                                            <em class="icon ni ni-file-pdf me-2"></em>Download PDF</a></li>
                                </ul>          
                            </div>
                        </div>
                    </div>

                    <div class="nk-editor-main">
                        <div class="nk-editor-body">
                            <div id="editor-v1">
                                <div class="placeholder-content">
                                    <em class="icon ni ni-edit-alt"></em>
                                    <p class="mt-3 h5">Your generated content will appear here...</p>
                                    <small class="text-muted">Fill out the control panel form and click "Generate Content" to begin</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 


<script>
// --- JAVASCRIPT LOGIC (FIXED) ---

document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const generateBtn = document.getElementById('generateBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (!validateForm(form)) {
        return;
    }

    showLoading(generateBtn, loadingOverlay);

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 60000); 

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.message || `HTTP ${response.status}`); });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const editor = document.getElementById('editor-v1');
            if (editor) {
                const formattedContent = formatContent(data.output, formData);
                editor.style.opacity = '0';
                editor.innerHTML = formattedContent;
                
                setTimeout(() => {
                    editor.style.transition = 'opacity 0.5s ease';
                    editor.style.opacity = '1';
                    updateCounts();
                    showSuccessMessage();
                }, 100);
            }
        } else {
            showError(data.message || 'Failed to generate content.');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Generation error:', error);
        
        let errorMessage = error.message || 'Generation failed. Please try again.';
        if (error.name === 'AbortError') {
            errorMessage = 'Request timed out. Please try with shorter content or check your connection.';
        }
        
        showError(errorMessage);
    })
    .finally(() => {
        hideLoading(generateBtn, loadingOverlay);
    });
});

function showLoading(btn, overlay) {
    if (window.currentLoadingInterval) {
        clearInterval(window.currentLoadingInterval);
    }
    
    btn.classList.add('generate-btn-loading');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loading').style.display = 'inline-flex';
    btn.disabled = true;
    
    const progressBar = overlay.querySelector('.progress-fill');
    progressBar.style.animation = 'none';
    progressBar.offsetHeight; 
    progressBar.style.animation = 'progressLoad 2s linear infinite';

    overlay.style.display = 'flex';
    overlay.style.opacity = '0';
    setTimeout(() => overlay.style.opacity = '1', 10);
    
    simulateProgress();
}

function hideLoading(btn, overlay) {
    btn.classList.remove('generate-btn-loading');
    btn.querySelector('.btn-text').style.display = 'inline';
    btn.querySelector('.btn-loading').style.display = 'none';
    btn.disabled = false;
    
    if (window.currentLoadingInterval) {
        clearInterval(window.currentLoadingInterval);
    }
    
    overlay.style.opacity = '0';
    setTimeout(() => overlay.style.display = 'none', 300);
}

function simulateProgress() {
    const messages = [
        'Analyzing your request...',
        'Connecting to AI service...',
        'Generating content...',
        'Optimizing output...',
        'Finalizing and spell checking...'
    ];
    
    let index = 0;
    const loadingText = document.getElementById('loadingText');
    
    const interval = setInterval(() => {
        if (loadingText && index < messages.length) {
            loadingText.textContent = messages[index];
            index++;
        } else if (loadingText) {
            loadingText.textContent = messages[messages.length - 1]; 
        }
    }, 2000);
    
    window.currentLoadingInterval = interval;
}

function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

function updateCounts() {
    const editor = document.getElementById('editor-v1');
    if (!editor) return;
    
    // Use innerText to avoid counting HTML tags if content is complex
    const content = editor.innerText || editor.textContent || ''; 
    const words = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
    const characters = content.length;
    
    animateCounter('word-count', words);
    animateCounter('char-count', characters);
}

/**
 * FIX: Corrected counter animation logic to prevent non-stop incrementing.
 */
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    let currentValue = parseInt(element.textContent) || 0;
    
    // Check if we have reached the target
    if (currentValue === targetValue) {
        return; 
    }

    const diff = targetValue - currentValue;
    const increment = Math.ceil(Math.abs(diff) / 10); // Use a percentage of the difference for smooth speed

    if (diff > 0) {
        currentValue = Math.min(currentValue + increment, targetValue);
    } else if (diff < 0) {
        currentValue = Math.max(currentValue - increment, targetValue);
    }

    element.textContent = currentValue;

    // Continue animation only if the target is not reached
    if (currentValue !== targetValue) {
        setTimeout(() => animateCounter(elementId, targetValue), 10); 
    }
}

function formatContent(output, formData) {
    // Clean the output first: remove ALL markdown formatting
    let cleanedOutput = output
        .replace(/\*\*(.+?)\*\*/g, '$1')  // Remove **bold**
        .replace(/\*(.+?)\*/g, '$1')      // Remove *italic*
        .replace(/__(.+?)__/g, '$1')      // Remove __bold__
        .replace(/_(.+?)_/g, '$1')        // Remove _italic_
        .replace(/`(.+?)`/g, '$1')        // Remove `code`
        .replace(/~~(.+?)~~/g, '$1');     // Remove ~~strikethrough~~
    
    // Split into lines and filter empty ones
    const lines = cleanedOutput.split('\n').filter(line => line.trim() !== '');
    
    // Start with just the wrapper, NO TITLE
    let html = `<div class="content-wrapper">`;
    
    lines.forEach(line => {
        const trimmedLine = line.trim();
        if (trimmedLine) {
            // Detect if it's a heading (starts with # or is short without period)
            const isHeading = trimmedLine.startsWith('#') || 
                            (trimmedLine.length < 80 && !trimmedLine.endsWith('.') && 
                             !trimmedLine.endsWith(',') && !trimmedLine.endsWith(';'));
            
            if (isHeading) {
                // Remove markdown heading markers
                const headingText = trimmedLine.replace(/^[#*-]+\s*/, '').trim();
                html += `<h3 class="content-heading">${escapeHtml(headingText)}</h3>`;
            } else {
                html += `<p class="content-paragraph">${escapeHtml(trimmedLine)}</p>`;
            }
        }
    });
    
    html += '</div>';
    return html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showSuccessMessage() {
    if (typeof toastr !== 'undefined') {
        toastr.success('Content generated successfully!');
    } else {
        console.log('Success: Content generated successfully!');
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert(message);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const exportButtons = document.querySelectorAll('.dropdown-menu .dropdown-item');
    
    exportButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            handleExport(this.id);
        });
    });
});

function handleExport(action) {
    const editor = document.getElementById('editor-v1');
    if (!editor) {
        showError('No content to export');
        return;
    }

    const content = editor.innerText || editor.textContent || ''; 
    if (!content.trim().length || editor.querySelector('.placeholder-content')) {
        showError('No generated content to export');
        return;
    }

    const templateTitle = document.querySelector('.nk-editor-title h4')?.textContent?.trim() || 'Generated_Content';
    const timestamp = new Date().toISOString().slice(0, 19).replace(/[:]/g, '-');
    const fileName = `${templateTitle}_${timestamp}`;

    switch (action) {
        case 'copy-text':
            copyToClipboard(content);
            break;
        case 'download-pdf':
            downloadPDF(editor, fileName);
            break;
    }
}

// NEW: Download as organized PDF function
async function downloadPDF(editor, fileName) {
    try {
        // Check if jsPDF is loaded
        if (typeof window.jspdf === 'undefined') {
            showError('PDF library not loaded. Please refresh the page.');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        // PDF Configuration
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 20;
        const maxWidth = pageWidth - (margin * 2);
        let yPosition = margin;
        const lineHeight = 7;
        const paragraphSpacing = 10;
        const headingSpacing = 12;

        // Title from template
        const templateTitle = document.querySelector('.nk-editor-title h4')?.textContent?.trim() || 'Generated Content';
        
        // Add header with title
        doc.setFontSize(18);
        doc.setFont(undefined, 'normal');
        const titleLines = doc.splitTextToSize(templateTitle, maxWidth);
        doc.text(titleLines, margin, yPosition);
        yPosition += (titleLines.length * 8) + 5;

        // Add date and time
        doc.setFontSize(9);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(100);
        const generatedDate = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        doc.text(`Generated on: ${generatedDate}`, margin, yPosition);
        yPosition += 8;

        // Add separator line
        doc.setDrawColor(200);
        doc.line(margin, yPosition, pageWidth - margin, yPosition);
        yPosition += 10;

        // Reset text color for content
        doc.setTextColor(0);

        // Process content - get all paragraphs and headings
        const contentWrapper = editor.querySelector('.content-wrapper');
        if (!contentWrapper) {
            showError('No content to export');
            return;
        }

        const elements = contentWrapper.children;

        for (let i = 0; i < elements.length; i++) {
            const element = elements[i];
            const text = element.textContent.trim();

            if (!text) continue;

            // Check if we need a new page
            if (yPosition > pageHeight - margin - 20) {
                doc.addPage();
                yPosition = margin;
            }

            if (element.classList.contains('content-heading') || element.tagName.match(/^H[1-6]$/)) {
                // Handle headings
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const headingLines = doc.splitTextToSize(text, maxWidth);
                doc.text(headingLines, margin, yPosition);
                yPosition += (headingLines.length * lineHeight) + headingSpacing;
                
            } else {
                // Handle paragraphs
                doc.setFontSize(11);
                doc.setFont(undefined, 'normal');
                
                const paragraphLines = doc.splitTextToSize(text, maxWidth);
                
                // Check if paragraph will fit on current page
                const paragraphHeight = paragraphLines.length * lineHeight + paragraphSpacing;
                if (yPosition + paragraphHeight > pageHeight - margin) {
                    doc.addPage();
                    yPosition = margin;
                }
                
                doc.text(paragraphLines, margin, yPosition, {
                    align: 'justify',
                    maxWidth: maxWidth
                });
                yPosition += paragraphHeight;
            }
        }

        // Add footer on each page
        const totalPages = doc.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.setFont(undefined, 'normal');
            
            // Page number
            const pageText = `Page ${i} of ${totalPages}`;
            const pageTextWidth = doc.getTextWidth(pageText);
            doc.text(pageText, pageWidth - margin - pageTextWidth, pageHeight - 10);
            
            // System name on left
            doc.text('UPTM Academic AI Assistant', margin, pageHeight - 10);
        }

        // Save the PDF
        doc.save(`${fileName}.pdf`);
        
        if (typeof toastr !== 'undefined') {
            toastr.success('PDF downloaded successfully!');
        }
        
    } catch (error) {
        console.error('PDF generation error:', error);
        showError('Failed to generate PDF. Please try again.');
    }
}

// Keep existing functions unchanged
async function copyToClipboard(text) {
    try {
        if (navigator.clipboard) {
            await navigator.clipboard.writeText(text);
            showSuccessMessage();
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showSuccessMessage();
        }
    } catch (err) {
        showError('Failed to copy to clipboard');
    }
}

function showSuccessMessage() {
    if (typeof toastr !== 'undefined') {
        toastr.success('Content copied successfully!');
    } else {
        console.log('Success: Content copied successfully!');
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert(message);
    }
}
</script>

<script>
// NEW: AI Suggestion Feature (OPTIMIZED & BUG-FIXED)
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('.ai-textarea');
    
    textareas.forEach(textarea => {
        const fieldName = textarea.dataset.fieldName;
        const suggestBtn = document.querySelector(`.ai-suggest-btn[data-target="${fieldName}"]`);
        
        if (!suggestBtn) {
            console.warn(`Suggestion button not found for field: ${fieldName}`);
            return;
        }
        
        // Enable/disable suggest button based on input length
        textarea.addEventListener('input', function() {
            const minLength = 3;
            if (this.value.trim().length >= minLength) {
                suggestBtn.disabled = false;
            } else {
                suggestBtn.disabled = true;
            }
        });
        
        // Handle suggestion button click
        suggestBtn.addEventListener('click', async function(e) {
            e.preventDefault(); // Prevent any default behavior
            
            const currentInput = textarea.value.trim();
            const language = document.getElementById('language').value;
            
            if (!language) {
                showError('Please select a language first');
                return;
            }
            
            if (currentInput.length < 3) {
                showError('Please enter at least 3 characters');
                return;
            }
            
            await fetchAISuggestions(fieldName, currentInput, language);
        });
    });
});

async function fetchAISuggestions(fieldName, currentInput, language) {
    const suggestBtn = document.querySelector(`.ai-suggest-btn[data-target="${fieldName}"]`);
    const suggestionsContainer = document.getElementById(`suggestions-${fieldName.replace(/ /g, '-')}`);
    
    if (!suggestBtn || !suggestionsContainer) {
        console.error('Required elements not found');
        return;
    }
    
    // Show loading state
    suggestBtn.disabled = true;
    const btnText = suggestBtn.querySelector('.btn-text');
    const btnLoading = suggestBtn.querySelector('.btn-loading');
    
    if (btnText) btnText.style.display = 'none';
    if (btnLoading) btnLoading.style.display = 'inline-flex';
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        const response = await fetch('/user/ai/suggestion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                field_name: fieldName,
                current_input: currentInput,
                language: language,
                template_context: document.querySelector('.display-6')?.textContent || ''
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success && data.suggestions && Array.isArray(data.suggestions)) {
            // Clean suggestions: remove unwanted quotes and numbering
            const cleanedSuggestions = data.suggestions.map(s => {
                return s
                    .replace(/^["'`]+|["'`]+$/g, '') // Remove quotes at start/end
                    .replace(/^\d+[\.\)]\s*/, '')    // Remove numbering like "1. " or "1) "
                    .trim();
            }).filter(s => s.length > 0); // Remove empty strings
            
            displaySuggestions(fieldName, cleanedSuggestions);
        } else {
            showError(data.message || 'Failed to get suggestions');
            suggestionsContainer.style.display = 'none';
        }
        
    } catch (error) {
        console.error('Suggestion error:', error);
        showError('Failed to fetch suggestions. Please try again.');
        suggestionsContainer.style.display = 'none';
    } finally {
        // Reset button state
        suggestBtn.disabled = false;
        if (btnText) btnText.style.display = 'inline';
        if (btnLoading) btnLoading.style.display = 'none';
    }
}

/**
 * FIXED: Store suggestions in memory instead of data attributes
 * to avoid issues with special characters and HTML encoding
 */
function displaySuggestions(fieldName, suggestions) {
    const container = document.getElementById(`suggestions-${fieldName.replace(/ /g, '-')}`);
    const textarea = document.getElementById(fieldName);
    
    if (!container || !textarea) {
        console.error('Container or textarea not found');
        return;
    }
    
    // Clear previous suggestions
    container.innerHTML = '';
    
    if (!suggestions || suggestions.length === 0) {
        container.innerHTML = '<p class="text-muted text-center p-3 mb-0">No suggestions available</p>';
        container.style.display = 'block';
        return;
    }
    
    // Store suggestions in a Map for safe retrieval
    const suggestionMap = new Map();
    
    suggestions.forEach((suggestion, index) => {
        const suggestionId = `suggestion-${fieldName.replace(/ /g, '-')}-${index}`;
        suggestionMap.set(suggestionId, suggestion);
        
        // Create suggestion element
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'suggestion-item';
        suggestionDiv.id = suggestionId;
        
        const suggestionText = document.createElement('span');
        suggestionText.className = 'suggestion-text';
        suggestionText.textContent = suggestion; // Use textContent to avoid XSS
        
        suggestionDiv.appendChild(suggestionText);
        container.appendChild(suggestionDiv);
        
        // Add click handler directly to element
        suggestionDiv.addEventListener('click', function() {
            applySuggestion(textarea, container, suggestionId, suggestionMap);
        });
    });
    
    // Show container with animation
    container.style.display = 'block';
}

/**
 * FIXED: Apply suggestion - REPLACES current text (not append)
 */
function applySuggestion(textarea, container, suggestionId, suggestionMap) {
    try {
        const suggestion = suggestionMap.get(suggestionId);
        
        if (!suggestion) {
            console.error('Suggestion not found in map');
            showError('Failed to apply suggestion');
            return;
        }
        
        // REPLACE current text with suggestion (don't append)
        textarea.value = suggestion;
        
        // Hide suggestions after successful application
        container.style.display = 'none';
        
        // Focus back on textarea and move cursor to end
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        
        // Trigger input event to update button states and character count
        const inputEvent = new Event('input', { bubbles: true });
        textarea.dispatchEvent(inputEvent);
        
        // Show success feedback
        if (typeof toastr !== 'undefined') {
            toastr.success('Suggestion applied successfully!');
        } else {
            console.log('Suggestion applied:', suggestion);
        }
        
    } catch (error) {
        console.error('Error applying suggestion:', error);
        showError('Failed to apply suggestion');
    }
}

// Helper function to close suggestions when clicking outside
document.addEventListener('click', function(e) {
    const isClickInsideSuggestion = e.target.closest('.suggestions-dropdown');
    const isClickOnButton = e.target.closest('.ai-suggest-btn');
    
    if (!isClickInsideSuggestion && !isClickOnButton) {
        document.querySelectorAll('.suggestions-dropdown').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});

</script>

@endsection