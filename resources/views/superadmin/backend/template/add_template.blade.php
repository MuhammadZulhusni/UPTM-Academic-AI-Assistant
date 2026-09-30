@extends('superadmin.dashboard')

@section('superadmin')
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Custom Styles for Form Appearance -->
<style>
.card-form {
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
}

.form-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
}

.form-label-custom {
    font-weight: 600;
    color: #495057;
}

.form-control, .form-select {
    border-radius: 0.5rem;
}

.btn-add-field, .btn-remove-field {
    border-radius: 0.5rem;
}

.input-field-row {
    transition: all 0.3s ease;
    animation: fadeIn 0.5s ease-out; 
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-guide {
    background-color: #f0f8ff;
    border: 1px solid #b3e0ff;
}

.guide-tip {
    font-weight: 600;
    color: #007bff;
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2 align-items-start">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Add Template</h2>
                    <p class="text-muted mb-0">Create a new template for the content generation tool.</p>
                </div>
                <div class="nk-block-head-content">
                    <button type="button" class="btn btn-outline-primary" id="suggestTemplatesBtn">
                        <i class="bi bi-lightbulb me-1"></i> Suggest templates
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main Form Card -->
        <div class="card card-form shadow-lg mt-5">
            <div class="card-header form-header py-4 px-4">
                <h5 class="mb-0 text-dark fw-bold">Template Details and Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('superadmin.store.template') }}" method="post" enctype="multipart/form-data" id="templateForm">
                    @csrf   

                    <!-- Section 1: Template Metadata -->
                    <div class="mb-5 pb-3 border-bottom">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">1. Basic Information</h6>
                        <div class="row g-4">
                            <!-- Template Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_name" class="form-label form-label-custom">Template Name</label>
                                    <input type="text" name="title" id="template_name" class="form-control form-control-lg" placeholder="e.g., Blog Post Outline Generator" required>
                                </div>
                            </div>
                            <!-- Template Description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_desc" class="form-label form-label-custom">Template Description</label>
                                    <input type="text" name="description" id="template_desc" class="form-control form-control-lg" placeholder="A brief explanation of what this template does" required>
                                </div>
                            </div>
                            <!-- Template Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="form-label form-label-custom">Template Category</label>
                                    <select name="category" class="form-select form-control-lg" id="category" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <option value="Student">Student</option>
                                        <option value="Lecturer">Lecturer</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Template Icon -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_icon" class="form-label form-label-custom">Template Icon</label>
                                    <select name="icon" id="template_icon" class="form-select form-control-lg" required>
                                        <option value="" selected disabled>-- Select Template Icon --</option>
                                        <option value="writing.png">Writing</option>
                                        <option value="teaching.png">Teaching</option>
                                        <option value="learning.png">Learning</option>
                                    </select>
                                </div>
                            </div>
                            @include('components.template_brief_upload_option')
                        </div>
                    </div>
                    
                    <!-- Section 2: Input Fields Configuration -->
                    <div class="mb-5 pb-3 border-bottom">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">2. User Input Fields</h6>
                        <div class="card bg-light border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0 fs-6">Define Input Parameters</label>
                            </div>
                            <small class="text-muted mb-3">These fields capture user data that will be inserted into your custom prompt code.</small>

                            <div id="input-fields" class="space-y-3">
                                <!-- Default/Initial Field (Index 0) -->
                                <div class="row input-field-row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="input_fields_0_title" class="form-label small">Field Title (Variable Name)</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="input_fields[0][title]" id="input_fields_0_title" class="form-control field-variable-title" value="topic" placeholder="e.g., topic" maxlength="30" required>
                                                <button type="button" class="btn btn-outline-primary copy-variable-btn" data-copy="{topic}">Copy</button>
                                            </div>
                                            <small class="text-muted">Copy <span class="variable-preview">{topic}</span>, then paste it into the custom prompt.</small>
                                        </div> 
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="input_fields_0_description" class="form-label small">Field Description (Helper Text)</label>
                                            <input type="text" name="input_fields[0][description]" id="input_fields_0_description" class="form-control form-control-sm" placeholder="e.g., What specific subject should the content cover?" required>
                                        </div> 
                                    </div>

                                    <!-- Hidden fields for fixed type and required status -->
                                    <input type="hidden" name="input_fields[0][type]" value="textarea">
                                    <input type="hidden" name="input_fields[0][is_required]" value="1">

                                    <div class="col-md-3 d-flex align-items-end">
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>

                    <!-- NEW Section 3: Custom Prompt Guide -->
                    <div class="mb-5 pb-3 border-bottom">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">3. Guide to Writing Custom Prompts</h6>
                        <div class="card card-guide border-0 p-4">
                            <p class="lead mb-3">The Custom Prompt Code is the precise instruction you give the AI model. The better your prompt, the higher the quality of the generated content will be.</p>
                            
                            <ul class="list-unstyled space-y-3">
                                <li class="d-flex align-items-start mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <span class="guide-tip">Use Variables:</span> Insert the user input from Section 2 into your prompt using curly braces `{}`. The variable name MUST exactly match the Field Title (Variable Name) you defined (e.g., if you created a field named `topic`, use `{topic}`).
                                    </div>
                                </li>
                                <li class="d-flex align-items-start mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <span class="guide-tip">Define a Role:</span> Start your prompt by giving the AI a persona or role to ensure the output is tailored (e.g., "Act as a professional copywriter...").
                                    </div>
                                </li>
                                <li class="d-flex align-items-start mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                    <div>
                                        <span class="guide-tip">Set Constraints:</span> Specify the desired output format, structure, and length. This is crucial for consistency (e.g., "Write in a friendly tone, using exactly 5 bullet points.").
                                    </div>
                                </li>
                            </ul>

                            <p class="mt-3 fw-bold">Example of a High-Quality Prompt (assuming `topic` variables are defined):</p>
                            <div class="p-3 bg-white border rounded small">
                                Act as an experienced university lecturer. Suggest 3 interactive classroom activities for teaching the topic {topic}. Each activity must be simple, time-efficient (under 15 minutes), and suitable for a university-level class. Format the output with clear headings for Activity Name, Instructions, and Learning Outcome. 
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 4: Custom Prompt -->
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">4. Generation Prompt Code</h6>
                        <div class="card bg-light border-0 p-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="prompt" class="form-label fw-bold mb-0">Custom Prompt Code</label>
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#writePromptModal">Write with AI</button>
                                </div>
                                <textarea name="prompt" id="prompt" placeholder="Add your prompt code here..." class="form-control" rows="8" required></textarea>
                                <small class="text-muted mt-2 d-block">
                                    Reference the guide above (Section 3) for instructions on creating effective prompts and using variables.
                                </small> 
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Submission -->
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-md px-3 elegant-bt" id="resetBtn">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Form
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-save me-1"></i> Save Template
                        </button>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="writePromptModal" tabindex="-1" aria-labelledby="writePromptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="writePromptModalLabel">Write with AI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Describe how you want the custom prompt to work. AI fills Custom Prompt Code and keeps your variable names.</p>
                <label for="promptWish" class="form-label">How should this prompt work?</label>
                <textarea id="promptWish" class="form-control" rows="4" maxlength="500" placeholder="Example: Suggest 3 classroom activities with headings for Activity Name, Instructions, and Learning Outcome."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="writePromptBtn">Write with AI</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- JavaScript for Dynamic Fields and Reset -->
<script>    
$(document).ready(function() {
    let fieldIndex = 1; 

    // Logic to Add a New Input Field
    $('#add-field').click(function() {
        const newFieldHtml = `
            <div class="row input-field-row g-3 mt-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label small">Field Title (Variable Name)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="input_fields[${fieldIndex}][title]" class="form-control field-variable-title" value="topic" placeholder="e.g., topic" maxlength="30" required>
                            <button type="button" class="btn btn-outline-primary copy-variable-btn" data-copy="{topic}">Copy</button>
                        </div>
                    </div> 
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label small">Field Description (Helper Text)</label>
                        <input type="text" name="input_fields[${fieldIndex}][description]" class="form-control form-control-sm" placeholder="e.g., How long should the content be?" required>
                    </div> 
                </div>

                <!-- Hidden fields for fixed type and required status -->
                <input type="hidden" name="input_fields[${fieldIndex}][type]" value="textarea">
                <input type="hidden" name="input_fields[${fieldIndex}][is_required]" value="1">

                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-field">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        $('#input-fields').append(newFieldHtml);
        fieldIndex++;
    });

    function syncVariableCopy(input) {
        const name = (input.value || '').trim();
        const token = name ? '{' + name + '}' : '';
        const group = input.closest('.form-group');
        if (!group) return;
        const button = group.querySelector('.copy-variable-btn');
        const preview = group.querySelector('.variable-preview');
        if (button) button.setAttribute('data-copy', token);
        if (preview) preview.textContent = token || '{topic}';
    }

    function copyToken(token) {
        const holder = document.createElement('textarea');
        holder.value = token;
        holder.setAttribute('readonly', '');
        holder.style.position = 'fixed';
        holder.style.left = '-9999px';
        document.body.appendChild(holder);
        holder.select();
        document.execCommand('copy');
        holder.remove();
    }

    $(document).on('input', '.field-variable-title', function () {
        syncVariableCopy(this);
    });

    function clearModalBackdrop() {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
        document.querySelectorAll('.modal-backdrop').forEach(function (el) {
            el.remove();
        });
    }

    function closeWritePromptModal() {
        const modalEl = document.getElementById('writePromptModal');
        if (modalEl && window.bootstrap) {
            const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
            modalEl.addEventListener('hidden.bs.modal', clearModalBackdrop, { once: true });
            instance.hide();
        }
        setTimeout(clearModalBackdrop, 350);
    }

    $('#writePromptBtn').on('click', function () {
        const wish = ($('#promptWish').val() || '').trim();
        const fields = [];

        $('.field-variable-title').each(function () {
            const name = ($(this).val() || '').trim();
            if (!name) return;
            const helper = ($(this).closest('.input-field-row').find('input[name$="[description]"]').val() || '').trim();
            fields.push({ title: name, description: helper });
        });

        if (wish.length < 10) {
            toastr.warning('Describe how you want the prompt to work.');
            return;
        }
        if (!fields.length) {
            toastr.warning('Enter a variable name first.');
            return;
        }

        const promptBox = document.getElementById('prompt');
        if (promptBox.value.trim() && !window.confirm('Replace the current custom prompt?')) {
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Writing...');

        $.ajax({
            url: @json(route('superadmin.template.write-prompt')),
            method: 'POST',
            timeout: 60000,
            headers: {
                'X-CSRF-TOKEN': $('#templateForm input[name="_token"]').val(),
                'Accept': 'application/json'
            },
            data: {
                wish: wish,
                title: ($('#template_name').val() || '').trim(),
                description: ($('#template_desc').val() || '').trim(),
                category: $('#category').val() || '',
                fields: fields
            },
            success: function (res) {
                if (!res.success || !res.prompt) {
                    toastr.error(res.message || 'Could not write the prompt.');
                    return;
                }
                promptBox.value = res.prompt;
                closeWritePromptModal();
                toastr.success('Custom prompt is ready. You can still edit it.');
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message
                    || (xhr.status === 422 ? 'Check the description and variable names, then try again.' : 'Could not write the prompt. Please try again.');
                toastr.error(message);
            },
            complete: function () {
                button.prop('disabled', false).text('Write with AI');
            }
        });
    });

    $(document).on('click', '.copy-variable-btn', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const input = this.closest('.input-group')?.querySelector('.field-variable-title');
        if (input) syncVariableCopy(input);
        const token = this.getAttribute('data-copy') || '';
        if (!token) {
            toastr.warning('Enter a variable name first.');
            return;
        }
        copyToken(token);
        const button = this;
        button.textContent = 'Copied';
        setTimeout(function () { button.textContent = 'Copy'; }, 1200);
    });

    // Logic to Remove an Input Field (Event delegation required)
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.input-field-row').remove();
    });

    // Reset Button Logic
    $('#resetBtn').click(function() {
        // Reset the form
        $('#templateForm')[0].reset();
        
        // Remove all dynamically added fields (keep only the first default field)
        $('.input-field-row').not(':first').remove();
        
        // Clear the first field's inputs
        $('#input_fields_0_title').val('topic').trigger('input');
        $('#input_fields_0_description').val('');
        
        // Reset fieldIndex counter
        fieldIndex = 1;
        
        // Show toastr notification
        toastr.info("Form has been reset. Please fill in the template details again.");
        
        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('#templateForm').offset().top - 100
        }, 500);
    });
});
</script>

@include('components.template_suggestion_modal', ['suggestUrl' => route('superadmin.template.suggest')])

@endsection