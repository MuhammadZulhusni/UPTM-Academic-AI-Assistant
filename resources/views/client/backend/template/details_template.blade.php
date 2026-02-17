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
        max-width: 1400px;
        margin: 0 auto;
    }

    /* --- Input Panel (Top Section) --- */
    .input-panel-card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .input-panel-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--panel-bg);
    }

    .input-panel-header .icon {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .input-panel-header h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
    }

    /* Form Grid Layout */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    /* --- Output Panel (Bottom Section) --- */
    .output-panel-card {
        background-color: var(--card-bg);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .nk-editor {
        position: relative;
        background-color: var(--card-bg);
        display: flex;
        flex-direction: column;
        min-height: 700px;
    }

    .nk-editor-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .nk-editor-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .nk-editor-title .icon {
        font-size: 1.5rem;
        color: var(--primary-color);
    }

    .nk-editor-title h4 {
        margin: 0;
        font-weight: 600;
        color: var(--dark-color);
        font-size: 1.15rem;
    }

    .nk-editor-main {
        flex-grow: 1;
        overflow-y: auto;
        background-color: #fafbfc;
    }

    .nk-editor-body {
        padding: 2rem;
        min-height: 500px;
    }

    /* --- Form Elements --- */
    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label .badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e5e7eb;
        box-shadow: none;
        transition: all 0.2s ease-in-out;
        background-color: #fafbfc;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        background-color: #ffffff;
    }

    /* --- Buttons & Actions --- */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 2px solid var(--panel-bg);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        border: none;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.85rem 2rem;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.25);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.35);
    }

    .btn-outline-secondary {
        border: 1.5px solid #dee2e6;
        color: #6c757d;
        font-weight: 600;
        border-radius: 10px;
        padding: 0.85rem 1.5rem;
        background: transparent;
        transition: all 0.2s ease;
    }

    .btn-outline-secondary:hover {
        background: #f8f9fa;
        border-color: #6c757d;
        color: #495057;
    }
    
    /* --- Loading Overlay --- */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(5px);
        display: flex;
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
        gap: 20px;
        text-align: center;
    }

    .spinner {
        width: 56px;
        height: 56px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s cubic-bezier(0.68, -0.55, 0.27, 1.55) infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .loading-spinner > p {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0;
    }
    
    .progress-bar {
        position: relative;
        overflow: hidden;
        width: 280px;
        height: 8px;
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
        margin-top: 15px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--secondary-color);
    }
    
    .typing-indicator .dot {
        width: 8px;
        height: 8px;
        background-color: var(--primary-color);
        animation: typingDots 1.4s infinite ease-in-out both;
        border-radius: 50%;
    }
    
    .typing-indicator .dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-indicator .dot:nth-child(2) { animation-delay: -0.16s; }
    .typing-indicator .dot:nth-child(3) { animation-delay: 0s; }
    
    @keyframes typingDots {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }

    /* --- Placeholder --- */
    .placeholder-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 450px;
        padding: 20px;
        color: #adb5bd;
    }
    
    .placeholder-content .icon {
        font-size: 5rem;
        color: #d1d9e2;
        opacity: 0.5;
        margin-bottom: 1.5rem;
    }

    .placeholder-content h5 {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .placeholder-content p {
        color: #adb5bd;
        font-size: 0.95rem;
    }
    
    /* ========================================
       DYNAMIC CONTENT STYLING
       Same text size and weight, balanced spacing
       ======================================== */
    
    .content-wrapper {
        max-width: 100%;
        padding: 2.5rem;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        font-family: var(--font-family-base);
        color: #2c3e50;
        line-height: 1.7;
    }

    /* All content types - same size and weight */
    .content-main-title,
    .content-section-header,
    .content-subtopic,
    .content-heading,
    .content-label,
    .content-paragraph,
    .content-list-item {
        font-size: 1rem;
        font-weight: 400;
        color: #2c3e50;
        line-height: 1.7;
        padding: 0;
        border: none;
        text-align: left;
    }

    /* Balanced margins - not too short, not too long */
    .content-main-title {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }

    .content-section-header {
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .content-subtopic {
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .content-heading {
        margin-top: 1rem;
        margin-bottom: 0.75rem;
    }

    .content-label {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .content-paragraph {
        margin-bottom: 0.875rem;
    }

    .content-list-item {
        margin-bottom: 0.625rem;
    }

    /* Spacers for organized sections */
    .content-spacer {
        height: 1rem;
    }

    .content-spacer-large {
        height: 1.5rem;
    }

    /* Remove margins from first/last elements */
    .content-wrapper > *:first-child {
        margin-top: 0 !important;
    }

    .content-wrapper > *:last-child {
        margin-bottom: 0 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 1.75rem 1.25rem;
        }
        
        .content-paragraph,
        .content-main-title,
        .content-section-header,
        .content-subtopic,
        .content-heading,
        .content-label,
        .content-list-item {
            font-size: 0.95rem;
        }
    }

    /* --- Statistics Bar --- */
    .stats-bar {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 0.75rem 0;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .stat-item .icon {
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    .stat-value {
        font-weight: 700;
        color: var(--dark-color);
        font-size: 1rem;
    }

    /* ========================================
       AI SUGGESTION STYLES
       ======================================== */
    .ai-suggest-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.65rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        background: linear-gradient(135deg, #fafbfc 0%, #f3f4f6 100%);
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .ai-suggest-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(147, 197, 253, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .ai-suggest-btn:hover:not(:disabled)::before {
        left: 100%;
    }

    .ai-suggest-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        border-color: #93c5fd;
        color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.15);
    }

    .ai-suggest-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .suggestions-dropdown {
        margin-top: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 1.25rem;
        animation: slideDownFade 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        max-height: 650px;
        overflow-y: auto;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-16px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .bloom-suggestion {
        padding: 1.25rem 1.5rem;
        margin-bottom: 0.875rem;
        background: #ffffff;
        border: 1.5px solid #f3f4f6;
        border-left: 4px solid #d1d5db;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .bloom-suggestion:hover {
        transform: translateX(8px) translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: currentColor;
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
    }

    .suggestion-content {
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
    }

    .suggestion-text {
        color: #374151;
        font-size: 0.9375rem;
        line-height: 1.65;
        font-weight: 500;
    }

    .bloom-badge {
        display: inline-flex;
        align-items: center;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0.375rem 0.875rem;
        background: currentColor;
        color: #ffffff;
        border-radius: 20px;
        align-self: flex-start;
        opacity: 0.9;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .bloom-suggestion:hover .bloom-badge {
        opacity: 1;
        transform: scale(1.05);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons button {
            width: 100%;
        }

        .stats-bar {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .nk-editor-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="nk-content-inner">
    <div class="nk-content-body page-container">

            <!-- Back Button (Top Left) -->
            <div class="mb-3">
                <a href="{{ route('user.template') }}"
                class="btn btn-outline-primary d-inline-flex align-items-center px-3 py-2">
                    <i class="bi bi-arrow-left me-2"></i>
                    Back to All Templates
                </a>
            </div>

            <!-- Page Title & Description -->
            <div class="nk-block-head nk-page-head">
                <div class="nk-block-head-content">
                    <h2 class="display-6 mb-1 fw-semibold">
                        {{ $template->title }}
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $template->description }}
                    </p>
                </div>
            </div>

        </div>
    </div>
        
        <!-- INPUT PANEL (TOP) -->
        <div class="input-panel-card">
            <div class="input-panel-header">
                <em class="icon ni ni-edit-fill"></em>
                <h3>Input Configuration</h3>
            </div>

            <form id="generateForm" action="{{ route('user.content.generate', $template->id) }}" method="post">
                @csrf

                <div class="form-grid">
                    <!-- Language Selection -->
                    <div class="form-group">
                        <label for="language" class="form-label">
                            <em class="icon ni ni-globe"></em>
                            Language
                            <span class="badge bg-primary">Required</span>
                        </label>
                        <select name="language" class="form-select" id="language" required>
                            <option value="">Select Language</option>
                            <option value="English">English</option>
                            <option value="Bahasa Melayu">Bahasa Melayu</option>
                        </select>
                    </div>

                    <!-- AI Model Selection -->
                    <div class="form-group">
                        <label for="ai_model" class="form-label">
                            <em class="icon ni ni-cpu"></em>
                            AI Model
                        </label>
                        <select name="ai_model" class="form-select" id="ai_model">
                            <option value="gpt-3.5-turbo" selected>OpenAI GPT-3.5 Turbo (Faster)</option>
                            <option value="gpt-4">OpenAI GPT-4 (Higher Quality)</option>
                        </select>
                    </div>

                    <!-- Result Length -->
                    <!-- <div class="form-group">
                        <label for="result_length" class="form-label">
                            <em class="icon ni ni-text"></em>
                            Result Length (words)
                        </label>
                        <input type="number" name="result_length" class="form-control" 
                               id="result_length" value="100" min="1" max="5000" required>
                        <small class="form-text text-muted">Recommended: 100-500 words</small>
                    </div> -->
                </div>

                <!-- Dynamic Template Fields -->
                @foreach ($template->inputFields as $field)
                <div class="form-group form-group-full">
                    <label for="{{ $field->title }}" class="form-label">
                        {{ $field->title }}
                        <span class="badge bg-primary">Required</span>
                    </label>
                    
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
                            rows="5" 
                            class="form-control ai-textarea" 
                            placeholder="Provide detailed description..."
                            data-field-name="{{ $field->title }}"
                            maxlength="50000"
                            required></textarea>
                        
                        <!-- AI Suggestion Button -->
                        <div class="ai-suggestion-wrapper mt-3">
                            <button type="button" 
                                    class="ai-suggest-btn" 
                                    data-target="{{ $field->title }}"
                                    disabled>
                                <em class="icon ni ni-spark-fill"></em>
                                <span class="btn-text">Get AI Suggestions</span>
                                <span class="btn-loading" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
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

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('generateForm').reset();">
                        Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary" id="generateBtn">
                        <span class="btn-text">
                            Generate Content
                        </span>
                        <span class="btn-loading" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-2 mt-1" role="status"></span>
                            Generating...
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- OUTPUT PANEL (BOTTOM) -->
        <div class="output-panel-card">
            <div class="nk-editor">
                
                <!-- Loading Overlay -->
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

                <!-- Editor Header -->
                <div class="nk-editor-header">
                    <div class="nk-editor-title">
                        <em class="icon ni ni-file-text"></em>
                        <h4>{{ $template->title }} Output</h4>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- Statistics -->
                        <div class="stats-bar">
                            <div class="stat-item">
                                <em class="icon ni ni-text"></em>
                                <span>Words: <span class="stat-value" id="word-count">0</span></span>
                            </div>
                            <div class="stat-item">
                                <em class="icon ni ni-edit"></em>
                                <span>Characters: <span class="stat-value" id="char-count">0</span></span>
                            </div>
                        </div>

                        <!-- Export Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-md btn-light rounded-pill" type="button" data-bs-toggle="dropdown">
                                <em class="icon ni ni-download"></em>
                                <span>Export</span>
                                <em class="icon ni ni-chevron-down"></em>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="#" class="dropdown-item" id="copy-text">
                                        <em class="icon ni ni-copy"></em>
                                        Copy Text
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item" id="download-pdf">
                                        <em class="icon ni ni-file-pdf"></em>
                                        Download PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Editor Body -->
                <div class="nk-editor-main">
                    <div class="nk-editor-body">
                        <div id="editor-v1">
                            <div class="placeholder-content">
                                <em class="icon ni ni-edit-alt"></em>
                                <h5>Ready to Generate Content</h5>
                                <p>Fill out the form above and click "Generate Content" to begin</p>
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

// UPDATED formatContent function to match Documents modal format
function formatContent(output, formData) {
    if (!output) return "";

    // Step 1: Clean the text
    let text = output.trim();
    text = text.replace(/\r\n/g, "\n");
    
    // Remove all markdown formatting
    text = text
        .replace(/\*\*\*(.+?)\*\*\*/g, '$1')
        .replace(/\*\*(.+?)\*\*/g, '$1')
        .replace(/\*(.+?)\*/g, '$1')
        .replace(/__(.+?)__/g, '$1')
        .replace(/_(.+?)_/g, '$1')
        .replace(/`(.+?)`/g, '$1')
        .replace(/~~(.+?)~~/g, '$1')
        .replace(/^#{1,6}\s+/gm, '')
        .replace(/\t/g, ' ')
        .replace(/ {3,}/g, ' ')
        .trim();
    
    // Step 2: Remove extra blank lines (max 2 consecutive)
    text = text.replace(/\n{3,}/g, "\n\n");
    
    // Step 3: Convert markdown-style headings
    text = text.replace(/^#\s?(.*)$/gm, "<h2 class='doc-h2'>$1</h2>");
    text = text.replace(/^##\s?(.*)$/gm, "<h3 class='doc-h3'>$1</h3>");
    text = text.replace(/^###\s?(.*)$/gm, "<h4 class='doc-h4'>$1</h4>");
    
    // Step 4: Handle bullet points and numbered lists
    text = text.replace(/^\s*[-•]\s+(.*)$/gm, "<li>$1</li>");
    text = text.replace(/^\s*\d+\.\s+(.*)$/gm, "<li>$1</li>");
    
    // Step 5: Wrap consecutive list items in ul tags
    text = text.replace(/(<li>[\s\S]*?<\/li>)/g, "<ul class='doc-list'>$1</ul>");
    
    // Step 6: Handle bold and italic (if any remain)
    text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    text = text.replace(/\*(.*?)\*/g, "<em>$1</em>");
    
    // Step 7: Split into paragraphs by double newlines
    let parts = text.split(/\n{2,}/);
    
    // Step 8: Clean and filter paragraphs
    parts = parts
        .map(p => p.trim())
        .filter(p => p.length > 0);
    
    // Step 9: Wrap in paragraph tags (skip if already has HTML tags)
    let html = parts
        .map(p => {
            // Don't wrap if it's already a heading or list
            if (p.startsWith('<h') || p.startsWith('<ul')) {
                return p;
            }
            return `<p class="doc-paragraph">${p}</p>`;
        })
        .join("");
    
    return html;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/* ========================================
   DOCUMENT CONTENT STYLES (Match Documents Modal)
   ======================================== */
const documentStyles = `
<style>
    /* Match the exact styles from Documents modal */
    #editor-v1 .doc-paragraph {
        margin-top: 0;
        margin-bottom: 12px;
        font-size: 0.95rem;
        line-height: 1.7;
        white-space: pre-wrap;
        word-break: break-word;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #2c3e50;
    }
    
    #editor-v1 .doc-paragraph:last-child {
        margin-bottom: 0 !important;
    }
    
    #editor-v1 .doc-h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a202c;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    
    #editor-v1 .doc-h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-top: 1.25rem;
        margin-bottom: 0.625rem;
        line-height: 1.3;
    }
    
    #editor-v1 .doc-h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #4a5568;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    #editor-v1 .doc-list {
        margin: 0.75rem 0;
        padding-left: 1.5rem;
        list-style-type: disc;
    }
    
    #editor-v1 .doc-list li {
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        line-height: 1.7;
        color: #2c3e50;
    }
    
    #editor-v1 .doc-list li:last-child {
        margin-bottom: 0;
    }
    
    /* Remove the old content-wrapper styles */
    #editor-v1 .content-wrapper {
        max-width: 100%;
        padding: 2rem;
        background: transparent;
        border-radius: 0;
        box-shadow: none;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #2c3e50;
        line-height: 1.7;
    }
</style>
`;

// Inject the styles into the document
if (!document.getElementById('document-content-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'document-content-styles';
    styleElement.innerHTML = documentStyles;
    document.head.appendChild(styleElement.querySelector('style'));
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

        // PDF Configuration - Clean Margins
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 25;              // 2.5cm margins all around
        const maxWidth = pageWidth - (margin * 2);
        let yPosition = margin;

        // Closer spacing - more compact but still readable
        const normalLineHeight = 6;
        const paragraphGap = 6;         // Gap between paragraphs
        const h2Gap = 12;               // Gap before H2
        const h2BottomGap = 8;          // Gap after H2
        const h3Gap = 10;               // Gap before H3
        const h3BottomGap = 6;          // Gap after H3
        const h4Gap = 8;                // Gap before H4
        const h4BottomGap = 5;          // Gap after H4
        const listItemGap = 3;          // Gap between list items
        const listBottomGap = 6;        // Gap after list

        // Extract template title
        const templateTitle = document.querySelector('.nk-editor-title h4')?.textContent?.trim() || 'Generated Content';
        
        // ==========================================
        // TITLE PAGE
        // ==========================================
        
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        
        const titleLines = doc.splitTextToSize(templateTitle, maxWidth - 20);
        const titleStartY = pageHeight / 3;
        
        titleLines.forEach((line, index) => {
            const lineWidth = doc.getTextWidth(line);
            const xPos = (pageWidth - lineWidth) / 2;
            doc.text(line, xPos, titleStartY + (index * 10));
        });
        
        const dividerY = titleStartY + (titleLines.length * 10) + 12;
        doc.setLineWidth(0.5);
        doc.line(margin + 20, dividerY, pageWidth - margin - 20, dividerY);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);
        const generatedDate = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const dateText = `Generated on ${generatedDate}`;
        const dateWidth = doc.getTextWidth(dateText);
        doc.text(dateText, (pageWidth - dateWidth) / 2, dividerY + 15);
        
        doc.setFontSize(10);
        const systemText = 'UPTM Academic AI Assistant';
        const systemWidth = doc.getTextWidth(systemText);
        doc.text(systemText, (pageWidth - systemWidth) / 2, pageHeight - margin - 10);
        
        // Start new page for content
        doc.addPage();
        yPosition = margin;
        
        // ==========================================
        // CONTENT PAGES - CLEAN LAYOUT
        // ==========================================
        
        const contentElements = editor.querySelectorAll('.doc-paragraph, .doc-h2, .doc-h3, .doc-h4, .doc-list');
        
        if (contentElements.length === 0) {
            // Fallback: get all text content
            const allText = editor.innerText || editor.textContent || '';
            if (!allText.trim()) {
                showError('No content to export');
                return;
            }
            
            const paragraphs = allText.split('\n\n').filter(p => p.trim());
            
            doc.setFont("helvetica", "normal");
            doc.setFontSize(11);
            
            paragraphs.forEach((para, index) => {
                const paraText = para.trim();
                
                // Add gap between paragraphs
                if (index > 0) {
                    yPosition += paragraphGap;
                }
                
                // Check page break
                const lines = doc.splitTextToSize(paraText, maxWidth);
                const estimatedHeight = lines.length * normalLineHeight;
                
                if (yPosition + estimatedHeight > pageHeight - margin) {
                    doc.addPage();
                    yPosition = margin;
                }
                
                // Print paragraph (left-aligned, no indent)
                doc.text(lines, margin, yPosition, {
                    align: 'left',
                    maxWidth: maxWidth
                });
                
                yPosition += lines.length * normalLineHeight;
            });
            
        } else {
            // Process structured content
            let isFirstElement = true;
            
            contentElements.forEach((element, index) => {
                const text = element.textContent.trim();
                if (!text) return;
                
                const isH2 = element.classList.contains('doc-h2');
                const isH3 = element.classList.contains('doc-h3');
                const isH4 = element.classList.contains('doc-h4');
                const isList = element.classList.contains('doc-list');
                const isParagraph = element.classList.contains('doc-paragraph');
                
                // H2 Heading
                if (isH2) {
                    // Add gap before (except first element)
                    if (!isFirstElement && yPosition > margin) {
                        yPosition += h2Gap;
                    }
                    
                    // Check page break
                    if (yPosition > pageHeight - margin - 40) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(14);
                    
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    
                    yPosition += headingLines.length * 7 + h2BottomGap;
                    isFirstElement = false;
                }
                
                // H3 Heading
                else if (isH3) {
                    if (!isFirstElement) {
                        yPosition += h3Gap;
                    }
                    
                    if (yPosition > pageHeight - margin - 35) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(12);
                    
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    
                    yPosition += headingLines.length * 6.5 + h3BottomGap;
                    isFirstElement = false;
                }
                
                // H4 Heading
                else if (isH4) {
                    if (!isFirstElement) {
                        yPosition += h4Gap;
                    }
                    
                    if (yPosition > pageHeight - margin - 30) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(11);
                    
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    
                    yPosition += headingLines.length * 6 + h4BottomGap;
                    isFirstElement = false;
                }
                
                // List Items
                else if (isList) {
                    const listItems = element.querySelectorAll('li');
                    
                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(11);
                    
                    const bulletX = margin + 5;
                    const textX = margin + 12;
                    const listWidth = maxWidth - 12;
                    
                    listItems.forEach((li, idx) => {
                        const itemText = li.textContent.trim();
                        const itemLines = doc.splitTextToSize(itemText, listWidth);
                        
                        // Check page break
                        const estimatedHeight = itemLines.length * normalLineHeight + listItemGap;
                        if (yPosition + estimatedHeight > pageHeight - margin) {
                            doc.addPage();
                            yPosition = margin;
                        }
                        
                        // Draw bullet
                        doc.circle(bulletX, yPosition - 2, 0.8, 'F');
                        
                        // Print text
                        doc.text(itemLines, textX, yPosition, {
                            align: 'left',
                            maxWidth: listWidth
                        });
                        
                        yPosition += itemLines.length * normalLineHeight;
                        
                        // Gap between items
                        if (idx < listItems.length - 1) {
                            yPosition += listItemGap;
                        }
                    });
                    
                    yPosition += listBottomGap;
                    isFirstElement = false;
                }
                
                // Regular Paragraph
                else if (isParagraph) {
                    // Add gap before paragraph
                    if (!isFirstElement) {
                        yPosition += paragraphGap;
                    }
                    
                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(11);
                    
                    const paraLines = doc.splitTextToSize(text, maxWidth);
                    
                    // Check page break
                    const estimatedHeight = paraLines.length * normalLineHeight;
                    if (yPosition + estimatedHeight > pageHeight - margin) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    // Print paragraph (left-aligned, no indent)
                    doc.text(paraLines, margin, yPosition, {
                        align: 'left',
                        maxWidth: maxWidth
                    });
                    
                    yPosition += paraLines.length * normalLineHeight;
                    isFirstElement = false;
                }
            });
        }
        
        // ==========================================
        // PAGE NUMBERS AND HEADERS
        // ==========================================
        const totalPages = doc.internal.getNumberOfPages();
        
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            
            // Skip title page
            if (i === 1) continue;
            
            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);
            doc.setTextColor(100);
            
            // Header
            const shortTitle = templateTitle.length > 80 
                ? templateTitle.substring(0, 77) + '...' 
                : templateTitle;
            doc.text(shortTitle, margin, 15);
            
            // Header line
            doc.setLineWidth(0.3);
            doc.line(margin, 17, pageWidth - margin, 17);
            
            // Page number (centered)
            const pageNum = `${i - 1}`;
            const pageNumWidth = doc.getTextWidth(pageNum);
            doc.text(pageNum, (pageWidth - pageNumWidth) / 2, pageHeight - 15);
        }
        
        doc.setTextColor(0);
        
        // Save PDF
        const sanitizedFileName = fileName.replace(/[^a-z0-9\s\-_.]/gi, '_').replace(/\s+/g, '_');
        doc.save(`${sanitizedFileName}.pdf`);
        
        if (typeof toastr !== 'undefined') {
            toastr.success('PDF downloaded successfully!');
        }
        
    } catch (error) {
        console.error('PDF generation error:', error);
        showError('Failed to generate PDF: ' + error.message);
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

// ========================================
// RESET BUTTON FUNCTIONALITY
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const resetBtn = document.querySelector('.btn-outline-secondary');
    const generateForm = document.getElementById('generateForm');
    
    if (resetBtn && generateForm) {
        resetBtn.addEventListener('click', function() {
            // Reset the form
            generateForm.reset();
            
            // Clear the output editor
            const editor = document.getElementById('editor-v1');
            if (editor) {
                editor.innerHTML = `
                    <div class="placeholder-content">
                        <em class="icon ni ni-edit-alt"></em>
                        <h5>Ready to Generate Content</h5>
                        <p>Fill out the form above and click "Generate Content" to begin</p>
                    </div>
                `;
            }
            
            // Reset word and character counts
            const wordCount = document.getElementById('word-count');
            const charCount = document.getElementById('char-count');
            if (wordCount) wordCount.textContent = '0';
            if (charCount) charCount.textContent = '0';
            
            // Clear any validation errors
            const invalidFields = generateForm.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => field.classList.remove('is-invalid'));
            
            // Hide all AI suggestion dropdowns
            document.querySelectorAll('.suggestions-dropdown').forEach(dropdown => {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
            });
            
            // Disable all AI suggestion buttons (they need input to enable)
            document.querySelectorAll('.ai-suggest-btn').forEach(btn => {
                btn.disabled = true;
            });
            
            // Show toastr notification
            if (typeof toastr !== 'undefined') {
                toastr.info('Form has been reset. Please fill in the template details again.');
            }
            
            // Scroll to top of form
            const inputPanel = document.querySelector('.input-panel-card');
            if (inputPanel) {
                inputPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
});
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
        // Get CSRF token from meta tag
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) {
            throw new Error('CSRF token not found in page');
        }
        
        const response = await fetch('{{ route("user.ai.suggestion") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfMeta.getAttribute('content'), 
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
 * Display suggestions with aesthetic soft color design
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
    
    // Soft Aesthetic Color Palette for Bloom's Taxonomy
    const bloomLevels = {
        'REMEMBER': { 
            color: '#60a5fa', // Soft blue
            bg: '#eff6ff',
            border: '#bfdbfe',
            label: 'Level 1: Remember',
            labelMalay: 'Tahap 1: Ingat'
        },
        'INGAT': { 
            color: '#60a5fa',
            bg: '#eff6ff',
            border: '#bfdbfe',
            label: 'Tahap 1: Ingat',
            labelMalay: 'Tahap 1: Ingat'
        },
        'UNDERSTAND': { 
            color: '#a78bfa', // Soft purple
            bg: '#f5f3ff',
            border: '#ddd6fe',
            label: 'Level 2: Understand',
            labelMalay: 'Tahap 2: Faham'
        },
        'FAHAM': { 
            color: '#a78bfa',
            bg: '#f5f3ff',
            border: '#ddd6fe',
            label: 'Tahap 2: Faham',
            labelMalay: 'Tahap 2: Faham'
        },
        'APPLY': { 
            color: '#34d399', // Soft green
            bg: '#ecfdf5',
            border: '#a7f3d0',
            label: 'Level 3: Apply',
            labelMalay: 'Tahap 3: Aplikasi'
        },
        'APLIKASI': { 
            color: '#34d399',
            bg: '#ecfdf5',
            border: '#a7f3d0',
            label: 'Tahap 3: Aplikasi',
            labelMalay: 'Tahap 3: Aplikasi'
        },
        'ANALYZE': { 
            color: '#fbbf24', // Soft amber
            bg: '#fffbeb',
            border: '#fde68a',
            label: 'Level 4: Analyze',
            labelMalay: 'Tahap 4: Analisis'
        },
        'ANALISIS': { 
            color: '#fbbf24',
            bg: '#fffbeb',
            border: '#fde68a',
            label: 'Tahap 4: Analisis',
            labelMalay: 'Tahap 4: Analisis'
        },
        'EVALUATE': { 
            color: '#f87171', // Soft red
            bg: '#fef2f2',
            border: '#fecaca',
            label: 'Level 5: Evaluate',
            labelMalay: 'Tahap 5: Nilai'
        },
        'NILAI': { 
            color: '#f87171',
            bg: '#fef2f2',
            border: '#fecaca',
            label: 'Tahap 5: Nilai',
            labelMalay: 'Tahap 5: Nilai'
        },
        'CREATE': { 
            color: '#2dd4bf', // Soft teal
            bg: '#f0fdfa',
            border: '#99f6e4',
            label: 'Level 6: Create',
            labelMalay: 'Tahap 6: Cipta'
        },
        'CIPTA': { 
            color: '#2dd4bf',
            bg: '#f0fdfa',
            border: '#99f6e4',
            label: 'Tahap 6: Cipta',
            labelMalay: 'Tahap 6: Cipta'
        }
    };
    
    // Store suggestions in a Map for safe retrieval
    const suggestionMap = new Map();
    
    suggestions.forEach((suggestion, index) => {
        const suggestionId = `suggestion-${fieldName.replace(/ /g, '-')}-${index}`;
        suggestionMap.set(suggestionId, suggestion);
        
        // Extract Bloom's level from suggestion [LEVEL: NAME] or [TAHAP: NAME]
        const levelMatch = suggestion.match(/\[(LEVEL|TAHAP):\s*(REMEMBER|UNDERSTAND|APPLY|ANALYZE|EVALUATE|CREATE|INGAT|FAHAM|APLIKASI|ANALISIS|NILAI|CIPTA)\]/i);
        const bloomLevel = levelMatch ? levelMatch[2].toUpperCase() : null;
        const suggestionText = levelMatch ? suggestion.replace(/\s*\[(LEVEL|TAHAP):[^\]]+\]$/i, '').trim() : suggestion;
        
        // Get Bloom's configuration
        const bloomConfig = bloomLevel && bloomLevels[bloomLevel] ? bloomLevels[bloomLevel] : null;
        
        // Create suggestion element
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'bloom-suggestion';
        suggestionDiv.id = suggestionId;
        
        // Apply aesthetic Bloom's level styling
        if (bloomConfig) {
            suggestionDiv.style.background = bloomConfig.bg;
            suggestionDiv.style.borderLeftColor = bloomConfig.color;
            suggestionDiv.style.borderColor = bloomConfig.border;
            suggestionDiv.style.color = bloomConfig.color;
        }
        
        // Create content wrapper
        const contentWrapper = document.createElement('div');
        contentWrapper.className = 'suggestion-content';
        
        // Main suggestion text
        const mainText = document.createElement('div');
        mainText.className = 'suggestion-text';
        mainText.textContent = suggestionText;
        mainText.style.color = '#374151'; // Override to maintain text readability
        
        // Aesthetic Bloom's level badge
        if (bloomConfig) {
            const badge = document.createElement('div');
            badge.className = 'bloom-badge';
            badge.style.background = bloomConfig.color;
            badge.textContent = bloomConfig.label;
            
            contentWrapper.appendChild(mainText);
            contentWrapper.appendChild(badge);
        } else {
            contentWrapper.appendChild(mainText);
        }
        
        suggestionDiv.appendChild(contentWrapper);
        container.appendChild(suggestionDiv);
        
        // Add click handler with smooth feedback
        suggestionDiv.addEventListener('click', function() {
            // Add click animation
            this.style.transform = 'translateX(6px) translateY(-1px) scale(0.99)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            applySuggestion(textarea, container, suggestionId, suggestionMap);
        });
    });
    
    // Show container with animation
    container.style.display = 'block';
}

/**
 * Apply suggestion, REPLACES current text 
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