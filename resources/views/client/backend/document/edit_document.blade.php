@extends('client.client_dashboard')
@section('client') 

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --primary-light: #818cf8;
        --secondary: #8b5cf6;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --dark: #1e293b;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--gray-50);
        color: var(--gray-900);
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 24px 24px;
        box-shadow: var(--shadow-lg);
    }

    .page-header-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .page-title {
        color: white;
        font-weight: 800;
        font-size: 2rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-title-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    /* Main Container */
    .editor-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem 2rem;
    }

    /* Editor Card */
    .editor-card {
        background: white;
        border-radius: 20px;
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        border: 1px solid var(--gray-200);
        transition: all 0.3s ease;
    }

    .editor-card:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    /* Editor Header */
    .editor-header {
        background: linear-gradient(to bottom, white, var(--gray-50));
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .editor-title-section {
        flex: 1;
        min-width: 200px;
    }

    .editor-title {
        font-weight: 700;
        color: var(--gray-900);
        font-size: 1.5rem;
        margin: 0 0 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        line-height: 1.3;
    }

    .title-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    .editor-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.875rem;
        color: var(--gray-500);
        margin-top: 0.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }

    .meta-item i {
        font-size: 0.875rem;
    }

    /* Action Buttons */
    .editor-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        font-size: 0.9375rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-back {
        background: white;
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-back:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn i {
        font-size: 1rem;
    }

    /* Quill Editor Customization */
    .editor-wrapper {
        background: white;
        position: relative;
    }

    .ql-toolbar {
        border: none !important;
        border-bottom: 2px solid var(--gray-100) !important;
        background: var(--gray-50);
        padding: 1rem 1.5rem;
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(10px);
    }

    .ql-toolbar button {
        transition: all 0.2s ease;
    }

    .ql-toolbar button:hover {
        background: white;
        border-radius: 6px;
    }

    .ql-toolbar button.ql-active {
        background: var(--primary);
        color: white;
        border-radius: 6px;
    }

    .ql-container {
        border: none !important;
        font-size: 16px;
        line-height: 1.8;
        color: var(--gray-800);
    }

    .ql-editor {
        padding: 2.5rem 2rem;
        min-height: 600px;
        font-family: 'Inter', 'Georgia', serif;
        max-width: 900px;
        margin: 0 auto;
    }

    .ql-editor:focus {
        outline: none;
    }

    .ql-editor h1,
    .ql-editor h2,
    .ql-editor h3 {
        font-weight: 700;
        color: var(--gray-900);
        margin-top: 1.5em;
        margin-bottom: 0.5em;
    }

    .ql-editor p {
        margin-bottom: 1em;
    }

    .ql-editor blockquote {
        border-left: 4px solid var(--primary);
        padding-left: 1.5rem;
        margin-left: 0;
        font-style: italic;
        color: var(--gray-600);
    }

    /* Loading State */
    .editor-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 600px;
        flex-direction: column;
        gap: 1.5rem;
        background: linear-gradient(to bottom, var(--gray-50), white);
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid var(--gray-200);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        color: var(--gray-600);
        font-size: 1rem;
        font-weight: 500;
    }

    .loading-dots {
        display: flex;
        gap: 0.5rem;
    }

    .loading-dot {
        width: 8px;
        height: 8px;
        background: var(--primary);
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out;
    }

    .loading-dot:nth-child(1) { animation-delay: -0.32s; }
    .loading-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    /* Status Bar */
    .editor-status {
        background: var(--gray-50);
        border-top: 2px solid var(--gray-100);
        padding: 1rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .status-left,
    .status-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .status-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-indicator {
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

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 0;
            border-radius: 0 0 16px 16px;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .page-title-icon {
            width: 36px;
            height: 36px;
        }

        .editor-container {
            padding: 0 1rem 1.5rem;
        }

        .editor-card {
            border-radius: 16px;
        }

        .editor-header {
            padding: 1rem 1.25rem;
            flex-direction: column;
            align-items: stretch;
        }

        .editor-title {
            font-size: 1.25rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .editor-meta {
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .editor-actions {
            width: 100%;
            flex-direction: column-reverse;
        }

        .btn {
            width: 100%;
            justify-content: center;
            padding: 0.875rem 1.25rem;
        }

        .ql-toolbar {
            padding: 0.75rem 1rem;
            overflow-x: auto;
        }

        .ql-editor {
            padding: 1.5rem 1rem;
            min-height: 400px;
        }

        .editor-status {
            padding: 0.875rem 1.25rem;
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .status-left,
        .status-right {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 1.25rem;
        }

        .editor-title {
            font-size: 1.125rem;
        }

        .title-badge {
            font-size: 0.6875rem;
            padding: 0.2rem 0.6rem;
        }

        .ql-editor {
            font-size: 15px;
            padding: 1.25rem 0.875rem;
        }

        .btn {
            font-size: 0.875rem;
        }
    }

    /* Error State */
    .editor-error {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        flex-direction: column;
        gap: 1rem;
        padding: 2rem;
        text-align: center;
    }

    .error-icon {
        width: 64px;
        height: 64px;
        background: #fee;
        color: var(--danger);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .error-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .error-message {
        color: var(--gray-600);
        margin: 0;
    }

    /* Success Toast (optional) */
    .toast-notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: var(--shadow-xl);
        border-left: 4px solid var(--success);
        display: none;
        align-items: center;
        gap: 1rem;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-notification.show {
        display: flex;
    }

    /* Print Styles */
    @media print {
        .editor-header,
        .editor-status,
        .ql-toolbar {
            display: none;
        }
        
        .editor-card {
            box-shadow: none;
            border: none;
        }
    }
</style>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">
            <div class="page-title-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            Edit Document
        </h1>
    </div>
</div>

<!-- Main Editor Container -->
<div class="editor-container">
    <form action="{{ route('user.update.document',$document->id) }}" method="post" id="editDocumentForm" enctype="multipart/form-data">
        @csrf  
        <input type="hidden" name="output" id="editor-output">

        <div class="editor-card">
            <!-- Editor Header -->
            <div class="editor-header">
                <div class="editor-title-section">
                    <h2 class="editor-title">
                        <i class="fas fa-file-lines" style="color: var(--primary);"></i>
                        <span>{{ $document->template->title ?? 'Document' }}</span>
                    </h2>
                </div>

                <div class="editor-actions">
                    <a href="javascript:history.back()" class="btn btn-back">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                    <button type="submit" class="btn btn-primary" id="saveButton">
                        <i class="fas fa-save" id="save-icon"></i>
                        <span id="save-text">Save Changes</span>
                        <div class="spinner-border spinner-border-sm d-none" role="status" id="save-spinner">
                            <span class="visually-hidden">Saving...</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Editor Body -->
            <div class="editor-wrapper">
                <!-- Loading State -->
                <div id="editor-loading" class="editor-loading">
                    <div class="loading-spinner"></div>
                    <div class="loading-text">Loading editor...</div>
                    <div class="loading-dots">
                        <div class="loading-dot"></div>
                        <div class="loading-dot"></div>
                        <div class="loading-dot"></div>
                    </div>
                </div>

                <!-- Quill Editor -->
                <div id="editor-v1" style="display: none;"></div>
            </div>

            <!-- Editor Status Bar -->
            <div class="editor-status" id="editor-status" style="display: none;">
                <div class="status-left">
                    <div class="status-item">
                        <!-- <span>Ready</span> -->
                    </div>
                </div>
                <div class="status-right">
                    <div class="status-item">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ $document->template->title ?? 'Document Template' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<script>
    let quill;
    let isEditorReady = false;
    let lastSaveTime = Date.now();

    // Initialize editor on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initializeEditor, 500);
    });

    function initializeEditor() {
        try {
            quill = new Quill('#editor-v1', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            // Hide loading, show editor and status bar
            document.getElementById('editor-loading').style.display = 'none';
            document.getElementById('editor-v1').style.display = 'block';
            document.getElementById('editor-status').style.display = 'flex';
            
            isEditorReady = true;
            console.log('✓ Editor initialized successfully');

            // Set initial content
            const initialContent = `{!! $document->output !!}`;
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            // Initial sync
            syncContent();
            updateWordCount();
            updateCharCount();

            // Listen for content changes
            quill.on('text-change', function(delta, oldDelta, source) {
                if (source === 'user') {
                    syncContent();
                    updateWordCount();
                    triggerAutoSave();
                }
            });

        } catch (error) {
            console.error('Editor initialization failed:', error);
            showError();
        }
    }

    // Sync editor content with hidden input
    function syncContent() {
        if (quill) {
            const content = quill.root.innerHTML.trim();
            const outputField = document.getElementById('editor-output');
            if (outputField) {
                outputField.value = content;
            }
        }
    }

    // Form submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editDocumentForm');
        const saveButton = document.getElementById('saveButton');

        if (form && saveButton) {
            form.addEventListener('submit', function(e) {
                if (!isEditorReady) {
                    e.preventDefault();
                    alert('Editor is still loading. Please wait a moment.');
                    return;
                }

                // Show loading state
                const saveIcon = document.getElementById('save-icon');
                const saveText = document.getElementById('save-text');
                const saveSpinner = document.getElementById('save-spinner');
                
                if (saveIcon) saveIcon.classList.add('d-none');
                if (saveText) saveText.textContent = 'Saving...';
                if (saveSpinner) saveSpinner.classList.remove('d-none');
                saveButton.disabled = true;

                // Final sync
                syncContent();
            });
        }
    });

    // Show error state
    function showError() {
        const loadingEl = document.getElementById('editor-loading');
        if (loadingEl) {
            loadingEl.innerHTML = `
                <div class="editor-error">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="error-title">Failed to load editor</h3>
                    <p class="error-message">Please refresh the page and try again. If the problem persists, contact support.</p>
                </div>
            `;
        }
    }
</script>

@endsection