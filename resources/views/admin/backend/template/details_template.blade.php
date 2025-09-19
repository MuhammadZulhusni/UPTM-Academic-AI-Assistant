@extends('admin.dashboard')
@section('admin')
 
<style>
    :root {
        --primary-color: #007bff;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --border-color: #dee2e6;
        --card-bg: #ffffff;
        --font-family-base: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    body {
        font-family: var(--font-family-base);
        background-color: var(--light-color);
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .nk-editor {
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        background-color: var(--card-bg);
    }

    .nk-editor-header {
        background-color: #f0f3f7;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nk-editor-main {
        padding: 1rem 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        font-weight: 600;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        transform: translateY(-2px);
    }

    .btn-light.rounded-pill {
        border-radius: 50px !important;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }
    
    .btn-light.rounded-pill:hover {
        background-color: #e9ecef;
        transform: translateY(-1px);
    }
    
    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border: none;
    }

    .dropdown-item {
        transition: background-color 0.2s ease;
    }

    .dropdown-item:active {
        background-color: var(--light-color);
        color: var(--dark-color);
    }

    /* Loading Overlay & Animations */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
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
    
    .progress-bar {
        width: 200px;
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 10px;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), #0056b3);
        width: 0%;
        animation: progressPulse 2s ease-in-out infinite;
    }
    
    @keyframes progressPulse {
        0% { width: 0%; }
        50% { width: 100%; }
        100% { width: 0%; }
    }
    
    .typing-indicator .dot {
        width: 10px;
        height: 10px;
        background-color: var(--primary-color);
        animation: typingDots 1.4s infinite ease-in-out both;
        border-radius: 50%;
    }
    
    .typing-indicator .dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator .dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-indicator .dot:nth-child(3) { animation-delay: 0s; }
    
    @keyframes typingDots {
        0%, 80%, 100% { transform: scale(0.8); }
        40% { transform: scale(1); }
    }

    /* This is the change. 
        We are targeting the placeholder content specifically
        and allowing the generated content to follow the default alignment.
    */
    .placeholder-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 300px;
        padding: 20px;
    }
    
    /* Fresh Icons */
    .ni-edit-alt {
        color: #d1d9e2 !important;
        font-size: 5rem !important;
        opacity: 0.5 !important;
        transition: all 0.3s ease;
    }
    
    .nk-editor-body:hover .ni-edit-alt {
        transform: scale(1.05);
    }
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">{{ $template->title }}</h2>
                    <p>{{ $template->description }}</p>
                </div>
            </div>
        </div>
        
        <div class="card shadow-none">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <form id="generateForm" action="{{ route('content.generate', $template->id) }}" method="post">
                            @csrf  

                            <div class="form-group">
                                <label for="language" class="form-label">Language</label>
                                <div class="form-control-wrap">
                                    <select name="language" class="form-select" id="language">
                                        <option value="English">English</option>
                                        <option value="Malay">Malay</option>
                                    </select>
                                </div>
                            </div>  
                            
                            @foreach ($template->inputFields as $field)
                            <div class="form-group mt-3">
                                <label for="{{ $field->title }}">{{ $field->title }}</label>
                               
                                @if ($field->type === 'text')
                                <input type="text" 
                                    name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    class="form-control" 
                                    maxlength="500"
                                    required>
                                
                                @elseif ($field->type === 'textarea')
                                <textarea name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    rows="5" 
                                    class="form-control" 
                                    maxlength="1000"
                                    required></textarea> 
                                @endif
                                <small>{{ $field->description }}</small>
                            </div>
                            @endforeach

                            <div class="form-group mt-3">
                                <label for="ai_model" class="form-label">AI Model</label>
                                <div class="form-control-wrap">
                                    <select name="ai_model" class="form-select" id="ai_model">
                                        <option value="gpt-3.5-turbo" selected>OpenAI | GPT-3.5-turbo (Faster)</option>
                                        <option value="gpt-4">OpenAI | GPT 4 (Higher Quality)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="result_length" class="form-label">Result Length (words)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" name="result_length" class="form-control" 
                                                   id="result_length" value="50" min="50" max="500" required>
                                            <small class="text-muted">Lower values generate faster</small>
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-3 mb-3" id="generateBtn">
                                <span class="btn-text">
                                    Generate Content
                                </span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Generating...
                                </span>
                            </button> 
                        </form>
                    </div>
                    
                    <div class="col-md-8" style="position: relative;">
                        <div class="loading-overlay" id="loadingOverlay">
                            <div class="loading-spinner">
                                <div class="spinner"></div>
                                <p><strong>AI is crafting your content...</strong></p>
                                <div class="progress-bar">
                                    <div class="progress-fill"></div>
                                </div>
                                <div class="typing-indicator">
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <span class="ms-2 text-muted" id="loadingText">Analyzing request...</span>
                                </div>
                            </div>
                        </div>

                        <div class="nk-editor">
                            <div class="nk-editor-header">
                                <div class="nk-editor-title">
                                    <h4 class="me-3 mb-0 line-clamp-1">{{ $template->title }}</h4>
                                </div>
                                <div class="nk-editor-tools d-none d-xl-flex">
                                    <ul class="d-inline-flex gap gx-3 gx-lg-4 pe-4 pe-lg-5">
                                        <li>
                                            <span class="sub-text text-nowrap">Words <span class="text-dark fw-bold" id="word-count">0</span></span>
                                        </li>
                                        <li>
                                            <span class="sub-text text-nowrap">Characters <span class="text-dark fw-bold" id="char-count">0</span></span>
                                        </li>
                                    </ul>
                                    <ul class="d-inline-flex gap gx-3">
                                        <li>
                                            <div class="dropdown">
                                                <button class="btn btn-md btn-light rounded-pill" type="button" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-download me-1"></em>
                                                    <span>Export</span>
                                                    <em class="icon ni ni-chevron-down"></em>
                                                </button> 
                                                <ul class="dropdown-menu">
                                                    <li><a href="#" class="dropdown-item" id="copy-text">
                                                        <em class="icon ni ni-copy me-2"></em>Copy Text</a></li>
                                                    <li><a href="#" class="dropdown-item" id="download-txt">
                                                        <em class="icon ni ni-file-text me-2"></em>Download TXT</a></li>
                                                </ul>          
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-editor-main">
                                <div class="nk-editor-body">
                                    <div class="wide-md h-100">
                                        <div class="js-editor nk-editor-style-clean nk-editor-full">
                                            <div id="editor-v1" style="min-height: 300px; padding: 20px;">
                                                <div class="placeholder-content text-muted">
                                                    <em class="icon ni ni-edit-alt"></em>
                                                    <p class="mt-3">Your generated content will appear here...</p>
                                                    <small>Fill out the form and click "Generate Content" to begin</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
// Optimized form submission with timeout and retry logic
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);
    const generateBtn = document.getElementById('generateBtn');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Client-side validation for better UX
    if (!validateForm(form)) {
        return;
    }

    showLoading(generateBtn, loadingOverlay);

    // Create AbortController for timeout handling
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout

    // Optimized fetch with timeout and better error handling
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
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const editor = document.getElementById('editor-v1');
            if (editor) {
                // Animate content appearance
                const formattedContent = formatContent(data.output, formData);
                editor.style.opacity = '0';
                editor.innerHTML = formattedContent;
                
                // Smooth fade-in animation
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
        
        let errorMessage = 'Generation failed. Please try again.';
        if (error.name === 'AbortError') {
            errorMessage = 'Request timed out. Please try with shorter content or check your connection.';
        }
        
        showError(errorMessage);
    })
    .finally(() => {
        hideLoading(generateBtn, loadingOverlay);
    });
});

// Enhanced loading states with progress simulation
function showLoading(btn, overlay) {
    btn.classList.add('generate-btn-loading');
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loading').style.display = 'inline-flex';
    btn.disabled = true;
    
    overlay.style.display = 'flex';
    overlay.style.opacity = '0';
    setTimeout(() => overlay.style.opacity = '1', 10);
    
    // Simulate progress with loading messages
    simulateProgress();
}

function hideLoading(btn, overlay) {
    btn.classList.remove('generate-btn-loading');
    btn.querySelector('.btn-text').style.display = 'inline';
    btn.querySelector('.btn-loading').style.display = 'none';
    btn.disabled = false;
    
    overlay.style.opacity = '0';
    setTimeout(() => overlay.style.display = 'none', 300);
}

// Progress simulation for better perceived performance
function simulateProgress() {
    const messages = [
        'Analyzing your request...',
        'Connecting to AI service...',
        'Generating content...',
        'Optimizing output...',
        'Almost ready...'
    ];
    
    let index = 0;
    const loadingText = document.getElementById('loadingText');
    
    const interval = setInterval(() => {
        if (loadingText && index < messages.length) {
            loadingText.textContent = messages[index];
            index++;
        } else {
            clearInterval(interval);
        }
    }, 2000);
    
    // Store interval ID for cleanup
    window.currentLoadingInterval = interval;
}

// Client-side validation
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

// Enhanced word/character counting with debouncing
function updateCounts() {
    const editor = document.getElementById('editor-v1');
    if (!editor) return;
    
    const content = editor.textContent || editor.innerText || '';
    const words = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
    const characters = content.length;
    
    // Animate counter updates
    animateCounter('word-count', words);
    animateCounter('char-count', characters);
}

function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const currentValue = parseInt(element.textContent) || 0;
    const increment = Math.ceil((targetValue - currentValue) / 20);
    
    if (currentValue < targetValue) {
        element.textContent = Math.min(currentValue + increment, targetValue);
        setTimeout(() => animateCounter(elementId, targetValue), 50);
    } else {
        element.textContent = targetValue;
    }
}

// Enhanced content formatting
function formatContent(output, formData) {
    let title = 'Generated Content';
    
    // Extract title from form data
    for (let [key, value] of formData.entries()) {
        if (key.toLowerCase().includes('title') || key.toLowerCase().includes('topic')) {
            title = value;
            break;
        }
    }

    const lines = output.split('\n').filter(line => line.trim() !== '');
    let html = `<div class="content-wrapper">
                    <h2 class="content-title">${escapeHtml(title)}</h2>`;
    
    lines.forEach(line => {
        const trimmedLine = line.trim();
        if (trimmedLine) {
            html += `<p class="content-paragraph">${escapeHtml(trimmedLine)}</p>`;
        }
    });
    
    html += '</div>';
    return html;
}

// Security helper
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Success/Error messaging
function showSuccessMessage() {
    // You can integrate with your existing toast/notification system
    if (typeof toastr !== 'undefined') {
        toastr.success('Content generated successfully!');
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert(message);
    }
}

// Enhanced export functionality
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

    const content = editor.textContent || editor.innerText || '';
    if (!content.trim()) {
        showError('No content to export');
        return;
    }

    const templateTitle = document.querySelector('.nk-editor-title h4')?.textContent?.trim() || 'Generated_Content';
    const timestamp = new Date().toISOString().slice(0, 19).replace(/[:]/g, '-');
    const fileName = `${templateTitle}_${timestamp}`;

    switch (action) {
        case 'copy-text':
            copyToClipboard(content);
            break;
        case 'download-txt':
            downloadFile(content, `${fileName}.txt`, 'text/plain');
            break;
    }
}

async function copyToClipboard(text) {
    try {
        if (navigator.clipboard) {
            await navigator.clipboard.writeText(text);
            showSuccessMessage();
        } else {
            // Fallback for older browsers
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

function downloadFile(content, fileName, mimeType) {
    try {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = fileName;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showSuccessMessage();
    } catch (err) {
        showError('Failed to download file');
    }
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.currentLoadingInterval) {
        clearInterval(window.currentLoadingInterval);
    }
});

// Form auto-save (optional)
function enableAutoSave() {
    const form = document.getElementById('generateForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            localStorage.setItem(`form_${this.name}`, this.value);
        });
        
        // Restore saved values
        const savedValue = localStorage.getItem(`form_${input.name}`);
        if (savedValue && !input.value) {
            input.value = savedValue;
        }
    });
}

// Initialize auto-save if needed
// enableAutoSave();
</script>

@endsection