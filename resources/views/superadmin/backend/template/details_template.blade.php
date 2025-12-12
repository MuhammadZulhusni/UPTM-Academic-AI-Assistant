@extends('superadmin.dashboard')
@section('superadmin')

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
    
    /* --- Generated Content Styling --- */
    .content-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: var(--dark-color);
        padding-top: 0.5rem;
    }
    
    .content-paragraph {
        margin-bottom: 1.25rem;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    
</style>


<div class="nk-content-inner">
    <div class="nk-content-body page-container">
        
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

                    <form id="generateForm" action="{{ route('superadmin.content.generate', $template->id) }}" method="post">
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
                                class="form-control" 
                                placeholder="Provide detailed description..."
                                maxlength="50000"
                                required></textarea> 
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
                                    <li><a href="#" class="dropdown-item" id="download-txt">
                                        <em class="icon ni ni-file-text me-2"></em>Download TXT</a></li>
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
    let title = 'Generated Content';
    
    for (let [key, value] of formData.entries()) {
        const lowerKey = key.toLowerCase();
        if (lowerKey.includes('title') || lowerKey.includes('topic') || lowerKey.includes('keywords')) {
            if (value && value.trim()) {
                title = value.trim().substring(0, 100);
                break;
            }
        }
    }

    const lines = output.split('\n').filter(line => line.trim() !== '');
    
    let html = `<div class="content-wrapper">
                    <h2 class="content-title">${escapeHtml(title)}</h2>`;
    
    lines.forEach(line => {
        const trimmedLine = line.trim();
        if (trimmedLine) {
            // Simple markdown detection for headings
            if (trimmedLine.startsWith('#') || (trimmedLine.length < 80 && !trimmedLine.endsWith('.'))) {
                html += `<h3 class="mt-4">${escapeHtml(trimmedLine.replace(/^[#*-]+/, '').trim())}</h3>`;
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

window.addEventListener('beforeunload', function() {
    if (window.currentLoadingInterval) {
        clearInterval(window.currentLoadingInterval);
    }
});
</script>

@endsection