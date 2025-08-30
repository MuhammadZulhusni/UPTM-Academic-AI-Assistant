@extends('admin.dashboard')
@section('admin')
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- CSS Untuk loading spinner -->
<style>
    .nk-editor {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    
    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .generate-btn-loading {
        opacity: 0.7;
        pointer-events: none;
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
        </div><div class="card shadow-none">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            </div>

                        <form id="generateForm" action="{{ route('content.generate', $template->id) }}" method="post" enctype="multipart/form-data">
                            @csrf  

                            <div class="form-group">
                                <label for="language" class="form-label">Language</label>
                                <div class="form-control-wrap">
                                    <select name="language" class="form-select" id="language">
                                        <option value="English">English</option>
                                        <option value="Malay">Malay</option>
                                        <option value="Mandarin">Mandarin</option>
                                        <option value="Hindi">Hindi</option>
                                    </select>
                                </div>
                            </div>  
                            
                            <!--
                            Loops through each input field associated with the current template.
                            This allows for dynamic form generation based on the template's configuration in the database.
                            -->
                            @foreach ($template->inputFields as $field)
                            <div class="form-group mt-3">
                                <label for="{{ $field->title }}">{{ $field->title }}</label>
                               
                                @if ($field->type === 'text')
                                <!-- Replaces spaces with underscores for a valid field name. -->
                                <input type="text" 
                                    name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    class="form-control" 
                                    required>
                                
                                @elseif ($field->type === 'textarea')
                                <!-- Replaces spaces with underscores for a valid field name. -->
                                <textarea name="{{ str_replace(' ', '_', $field->title) }}" 
                                    id="{{ $field->title }}" 
                                    rows="5" 
                                    class="form-control" 
                                    required></textarea> 
                                @endif
                                <small>{{ $field->description }}</small>
                            </div>
                            @endforeach

                            <div class="form-group mt-3">
                                <label for="ai_model" class="form-label">AI Model</label>
                                <div class="form-control-wrap">
                                    <select name="ai_model" class="form-select" id="ai_model">
                                        <option value="gpt-4">OpenAI | GPT 4</option>
                                        <option value="gpt-3.5-turbo">OpenAI | GPT-3.5-turbo</option> 
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="result_length" class="form-label">Estimated Result Length</label>
                                        <div class="form-control-wrap">
                                            <input type="number" name="result_length" class="form-control" id="result_length" value="200" min="1" max="1000" required>
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                            
                            <button type="submit" class="btn btn-primary mt-3" id="generateBtn">
                                <span class="btn-text">Generate</span>
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
                                <p><strong>Generating content...</strong></p>
                                <small>This may take a few moments</small>
                            </div>
                        </div>

                        <div class="nk-editor">
                            <div class="nk-editor-header">
                                <div class="nk-editor-title">
                                    <h4 class="me-3 mb-0 line-clamp-1">{{ $template->title }}</h4>
                                    <ul class="d-inline-flex align-item-center">
                                        <li>
                                            <button class="btn btn-sm btn-icon btn-zoom">
                                                <em class="icon ni ni-star"></em>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="nk-editor-tools d-none d-xl-flex">
                                    <ul class="d-inline-flex gap gx-3 gx-lg-4 pe-4 pe-lg-5">
                                        <li>
                                            <span class="sub-text text-nowrap">Words <span class="text-dark" id="word-count">0</span></span>
                                        </li>
                                        <li>
                                            <span class="sub-text text-nowrap">Characters <span class="text-dark" id="char-count">0</span></span>
                                        </li>
                                    </ul>
                                    <ul class="d-inline-flex gap gx-3">
                                        <li>
                                            <div class="dropdown">
                                                <button class="btn btn-md btn-light rounded-pill" type="button" data-bs-toggle="dropdown">
                                                    <span>Export</span>
                                                    <em class="icon ni ni-chevron-down"></em>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <div class="dropdown-content">
                                                        <ul class="link-list link-list-hover-bg-primary link-list-md">
                                                            <li>
                                                                <a href="#" id="copy-text"><em class="icon ni ni-file-doc"></em><span>Copy Text</span></a>
                                                            </li>
                                                            <li>
                                                                <a href="#"><em class="icon ni ni-file-text"></em><span>Text</span></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <button class="btn btn-md btn-primary rounded-pill" type="button">Save</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-editor-main">
                                <div class="nk-editor-body">
                                    <div class="wide-md h-100">
                                        <div class="js-editor nk-editor-style-clean nk-editor-full" data-menubar="false">
                                            <div id="editor-v1">
                                                <p class="text-muted">Generated content will appear here...</p>
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
    // This part handles the form submission using AJAX to prevent a full page reload.
    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevents the browser from submitting the form normally.

        const form = this;
        const formData = new FormData(form);
        const generateBtn = document.getElementById('generateBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');

        // Calls a function to show the loading states on the button and overlay.
        showLoading(generateBtn, loadingOverlay);

        // Uses the Fetch API to send the form data to the server.
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                // These headers are crucial for Laravel to recognize this as an AJAX request.
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            // Checks if the server response was successful. If not, it throws an error.
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response Data:', data);
            if (data.success) {
                const editor = document.getElementById('editor-v1');
                if (editor) {
                    // Formats the generated content and injects it into the editor.
                    const formattedContent = formatContent(data.output, formData);
                    editor.innerHTML = formattedContent;
                    // Updates the word and character counts.
                    updateCounts();
                } else {
                    console.error('Editor element not found');
                }
            } else {
                // Displays an alert if the server returns a failure message.
                alert(data.message || 'Failed to generate content.');
            }
        })
        .catch(error => {
            // Catches and handles any network or server-side errors.
            console.error('Error:', error);
            let errorMessage = 'An error occurred while generating content.';
            
            // Provides specific error messages for different HTTP status codes.
            if (error.message.includes('status: 422')) {
                errorMessage = 'Validation error. Please check your input fields.';
            } else if (error.message.includes('status: 400')) {
                errorMessage = 'Word limit exceeded. Please try with fewer words.';
            } else if (error.message.includes('status: 500')) {
                errorMessage = 'Server error. Please try again later.';
            }
            
            alert(errorMessage);
        })
        .finally(() => {
            // Always hides the loading states, regardless of success or failure.
            hideLoading(generateBtn, loadingOverlay);
        });
    });

    // Function to handle showing the loading UI.
    function showLoading(btn, overlay) {
        btn.classList.add('generate-btn-loading');
        btn.querySelector('.btn-text').style.display = 'none';
        btn.querySelector('.btn-loading').style.display = 'inline-flex';
        btn.disabled = true;
        
        overlay.style.display = 'flex';
    }

    // Function to handle hiding the loading UI.
    function hideLoading(btn, overlay) {
        btn.classList.remove('generate-btn-loading');
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loading').style.display = 'none';
        btn.disabled = false;
        
        overlay.style.display = 'none';
    }

    // Function to count and display words and characters.
    function updateCounts() {
        const editor = document.getElementById('editor-v1');
        if (editor) {
            const content = editor.textContent || editor.innerText;
            const words = content.trim() === '' ? 0 : content.trim().split(/\s+/).length;
            const characters = content.length;
            document.getElementById('word-count').textContent = words;
            document.getElementById('char-count').textContent = characters;
        }
    }  

    // Function to format the plain text output from the AI into structured HTML.
    function formatContent(output, formData) {
        let title = 'Generated Content';
        // Tries to find a suitable title from the user's input fields.
        for (let [key, value] of formData.entries()) {
            if (key === 'Article_Title' || key === 'Topic') {
                title = value;
                break;
            }
        }

        const lines = output.split('\n').filter(line => line.trim() !== '');
        let html = `<h2>${title}</h2>`; 

        // Iterates through each line of the AI's output and wraps it in a paragraph tag.
        for (let i = 0; i < lines.length; i++) {
            html += `<p>${lines[i]}</p>`;
            // Inserts a horizontal rule after every 3 paragraphs for visual separation.
            if ((i + 1) % 3 === 0 && i + 1 < lines.length) {
                html += '<hr>';
            }
        }
        return html;
    }
</script>

@endsection