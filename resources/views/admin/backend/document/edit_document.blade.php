@extends('admin.dashboard')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    /* Simplified and Clean Styling */
    .nk-editor {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .nk-editor-header {
        background: #f8fafc;
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .nk-editor-title h4 {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.5rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .nk-editor-tools {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .btn-back {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 9999px; /* Rounded pill shape */
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.2s;
    }

    .btn-back:hover {
        background-color: #e2e8f0;
    }

    .btn-primary {
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 9999px; /* Rounded pill shape */
        color: white;
        transition: background-color 0.2s;
    }

    .btn-primary:hover {
        background-color: #3b82f6;
    }

    .nk-editor-main {
        background: #ffffff;
        min-height: 600px;
    }

    .nk-editor-body {
        padding: 2rem;
    }

    /* Quill Editor Styling */
    .ql-toolbar {
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-radius: 0;
    }

    .ql-container {
        border: none !important;
        font-size: 16px;
        line-height: 1.7;
        color: #374151;
    }

    .ql-editor {
        padding: 2rem 1.5rem;
        min-height: 500px;
        font-family: 'Inter', sans-serif;
    }

    .ql-editor:focus {
        outline: none;
    }

    /* Page Header */
    .nk-block-head {
        margin-bottom: 2rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .nk-block-head h2 {
        color: #1e293b;
        font-weight: 800;
        font-size: 2.25rem;
        margin: 0;
    }

    /* Card Enhancement */
    .card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-body {
        padding: 0;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .nk-editor-header {
            padding: 1rem;
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .nk-editor-tools {
            width: 100%;
            justify-content: center;
        }

        .btn-primary {
            padding: 0.75rem 1.5rem;
            width: 100%;
        }

        .nk-editor-body {
            padding: 1rem;
        }

        .ql-toolbar {
            padding: 0.75rem;
        }

        .ql-editor {
            padding: 1rem;
            min-height: 400px;
        }

        .nk-block-head {
            padding: 1.5rem;
        }
    }

    /* Loading State */
    .editor-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        flex-direction: column;
        gap: 1rem;
    }

    .editor-loading .spinner-border {
        color: #3b82f6;
    }
</style>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<div class="nk-content-inner">
<div class="nk-content-body">
    
    <div class="nk-block-head nk-page-head">
        <div class="nk-block-head-between">
            <div class="nk-block-head-content">
                <h2 class="display-6"> Edit Document </h2> 
            </div>
        </div>
    </div><div class="card shadow-none">
        <div class="card-body">
            <div class="row">
     
 {{-- Right Sidebar  --}}
    <div class="col-md-12">
 <form action="{{ route('admin.update.document',$document->id) }}" method="post" id="editDocumentForm" enctype="multipart/form-data">
    @csrf  

    <input type="hidden" name="output" id="editor-output">

<div class="nk-editor"> 
<div class="nk-editor-header">
    <div class="nk-editor-title">
        <h4 class="me-3 mb-0 line-clamp-1">
            {{ json_decode($document->input,true)['Article_Title'] ?? json_decode($document->input,true)['Topic'] ?? 'Document' }}
        </h4> 
    </div>

    <div class="nk-editor-tools d-none d-xl-flex">
        <ul class="d-inline-flex gap gx-3">
            <li>
                <a href="javascript:history.back()" class="btn-back">
                    <em class="icon ni ni-arrow-left"></em>
                    <span>Back</span>
                </a>
            </li>
            <li>
                <button type="submit" class="btn btn-md btn-primary rounded-pill" id="saveButton">
                    <span class="btn-text">Save Changes</span>
                    <span class="btn-spinner d-none">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </span>
                </button>
            </li> 
        </ul> 
    </div> 
</div>
<div class="nk-editor-main"> 
    <div class="nk-editor-body">
        <div class="wide-md h-100"> 
            <div id="editor-loading" class="editor-loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p style="color: #6b7280; margin: 0;">Loading editor...</p>
            </div>
            <div id="editor-v1" style="display: none;">
                 </div>
                 </div>
    </div></div></div>

 </form>      
    </div>
  {{-- End Right Sidebar  --}} 
            </div> 
        </div> 
    </div> 
</div>
</div> 

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>

<script>
    // Initialize Quill editor with error handling
    let quill;
    let isEditorReady = false;

    // Show loading state initially
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate loading time for better UX
        setTimeout(initializeEditor, 500);
    });

    function initializeEditor() {
        try {
            quill = new Quill('#editor-v1', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'direction': 'rtl' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            // Hide loading, show editor
            document.getElementById('editor-loading').style.display = 'none';
            document.getElementById('editor-v1').style.display = 'block';
            
            console.log('Quill initialized successfully');
            isEditorReady = true;

            // Set initial content
            const initialContent = `{!! $document->output !!}`;
            console.log('Initial Content:', initialContent);
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            } else {
                console.warn('No initial content found');
            }

            // Sync editor content with hidden input on change
            quill.on('text-change', function(delta, oldDelta, source) {
                if (source === 'user') {
                    const content = quill.root.innerHTML.trim();
                    const outputField = document.getElementById('editor-output');
                    if (outputField) {
                        outputField.value = content;
                        console.log('Synced Output:', content);
                    } else {
                        console.error('Output Field not found');
                    }
                }
            });

            // Initial sync
            const content = quill.root.innerHTML.trim();
            const outputField = document.getElementById('editor-output');
            if (outputField) {
                outputField.value = content;
            }

        } catch (error) {
            console.error('Quill initialization failed:', error);
            document.getElementById('editor-loading').innerHTML = `
                <div style="color: #ef4444; text-align: center;">
                    <p style="font-size: 1.25rem; margin-bottom: 0.5rem;">Editor failed to load</p>
                    <p style="color: #6b7280; margin: 0;">Please refresh the page and try again.</p>
                </div>
            `;
        }
    }

    // Enhanced form submission with loading states
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editDocumentForm');
        const saveButton = document.getElementById('saveButton');

        if (form && saveButton) {
            form.addEventListener('submit', function(e) {
                if (!isEditorReady) {
                    e.preventDefault();
                    alert('Editor is still loading. Please wait a moment and try again.');
                    return;
                }

                // Show loading state
                const btnText = saveButton.querySelector('.btn-text');
                const btnSpinner = saveButton.querySelector('.btn-spinner');
                
                if (btnText && btnSpinner) {
                    btnText.style.display = 'none';
                    btnSpinner.classList.remove('d-none');
                    saveButton.disabled = true;
                }

                // Sync content one final time
                if (quill) {
                    const content = quill.root.innerHTML.trim();
                    const outputField = document.getElementById('editor-output');
                    if (outputField) {
                        outputField.value = content;
                        console.log('Form Submission - Output Value:', content);
                    } else {
                        console.error('Output Field not found on submit');
                    }
                }
            });
        } else {
            console.error('Form or save button not found');
        }
    });

    // Auto-save functionality (optional enhancement)
    let autoSaveTimeout;
    function autoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            if (quill && isEditorReady) {
                const content = quill.root.innerHTML.trim();
                // Here you could implement auto-save to server
                console.log('Auto-save triggered:', content);
            }
        }, 10000); // Auto-save every 10 seconds
    }

    // Trigger auto-save on content change
    if (typeof quill !== 'undefined') {
        document.addEventListener('DOMContentLoaded', function() {
            if (quill) {
                quill.on('text-change', autoSave);
            }
        });
    }
</script>

@endsection