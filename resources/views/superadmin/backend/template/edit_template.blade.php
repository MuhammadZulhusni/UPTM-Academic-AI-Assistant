@extends('superadmin.dashboard')

@section('superadmin')
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Custom Styles for Form Appearance (Copied from Create Page) -->
<style>

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

.input-field-row {
    transition: all 0.3s ease;
    animation: fadeIn 0.5s ease-out; 
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<div class="nk-content-inner">
    <div class="nk-content-body">

        <!-- Back Button -->
        <div class="back-button-wrapper">
            <a href="{{ route('superadmin.template') }}" class="btn-back">
                <em class="icon ni ni-arrow-left"></em>
                <span>Back to All Templates</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-content">
                <h2 class="display-6">Edit Template: {{ $template->title }}</h2>
                <p class="text-muted">Update the details and settings for your custom template.</p>
            </div>
        </div>
        
        <!-- Main Form Card -->
        <div class="card card-form shadow-lg mt-5">
            <div class="card-header form-header py-4 px-4">
                <h5 class="mb-0 text-dark fw-bold">Template Details and Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('superadmin.update.template', $template->id) }}" method="post" enctype="multipart/form-data">
                    @csrf   

                    <!-- Section 1: Template Metadata -->
                    <div class="mb-5 pb-3 border-bottom">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">1. Basic Information</h6>
                        <div class="row g-4">
                            <!-- Template Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="templateName" class="form-label form-label-custom">Template Name</label>
                                    <input type="text" name="title" id="templateName" class="form-control form-control-lg" value="{{ $template->title }}" required>
                                </div>
                            </div>
                            <!-- Template Description -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="templateDescription" class="form-label form-label-custom">Template Description</label>
                                    <input type="text" name="description" id="templateDescription" class="form-control form-control-lg" value="{{ $template->description }}" required>
                                </div>
                            </div>
                            <!-- Template Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="templateCategory" class="form-label form-label-custom">Template Category</label>
                                    <select name="category" class="form-select form-control-lg" id="templateCategory" required>
                                        <option value="" disabled>Select Category</option>
                                        <option value="Student" {{ $template->category == 'Student' ? 'selected' : '' }}>Student</option>
                                        <option value="Lecturer" {{ $template->category == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Template Icon -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_icon" class="form-label form-label-custom">Template Icon</label>
                                    <select name="icon" id="template_icon" class="form-select form-control-lg" required>
                                        <option value="" disabled>-- Select Template Icon --</option>
                                        <option value="writing.png" {{ $template->icon == 'writing.png' ? 'selected' : '' }}>Writing</option>
                                        <option value="teaching.png" {{ $template->icon == 'teaching.png' ? 'selected' : '' }}>Teaching</option>
                                        <option value="learning.png" {{ $template->icon == 'learning.png' ? 'selected' : '' }}>Learning</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section 2: Input Fields Configuration (Single Field Only) -->
                    <div class="mb-5 pb-3 border-bottom">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">2. User Input Field Configuration</h6>
                        <div class="card bg-light border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0 fs-6">Define Single Input Parameter</label>
                            </div>
                            <small class="text-muted mb-3">This field defines the single variable used in your Custom Prompt (e.g., `{topic}`).</small>

                            @php
                                // Safely retrieve the first input field for editing
                                $field = data_get($template->inputFields, '0', (object)['title' => '', 'description' => '', 'type' => 'textarea', 'is_required' => 1]);
                            @endphp

                            <div id="input-fields" class="space-y-3">
                                <div class="row input-field-row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="input_fields_0_title" class="form-label small">Field Title (Variable Name)</label>
                                            <input type="text" name="input_fields[0][title]" id="input_fields_0_title" class="form-control form-control-sm" placeholder="e.g., topic" value="{{ $field->title }}" required>
                                        </div> 
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="input_fields_0_description" class="form-label small">Field Description (Helper Text)</label>
                                            <input type="text" name="input_fields[0][description]" id="input_fields_0_description" class="form-control form-control-sm" placeholder="e.g., What specific subject should the content cover?" value="{{ $field->description }}" required>
                                        </div> 
                                    </div>

                                    <!-- Hidden fields for fixed type and required status -->
                                    <input type="hidden" name="input_fields[0][type]" value="{{ $field->type ?? 'textarea' }}">
                                    <input type="hidden" name="input_fields[0][is_required]" value="{{ $field->is_required ?? 1 }}">

                                    <div class="col-md-3"></div>
                                </div> 
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Generation Prompt -->
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold text-primary mb-3">3. Generation Prompt</h6>
                        <div class="card bg-light border-0 p-4">
                            <div class="form-group">
                                <label for="promptCode" class="form-label fw-bold mb-2">Custom Prompt Code</label>
                                <textarea name="prompt" id="promptCode" placeholder="Add your prompt code here..." class="form-control form-control-lg" rows="8" required>{{ $template->prompt }}</textarea>
                                <small class="text-muted mt-2 d-block">
                                    Use variables defined in Section 2 (e.g., {topic} ) inside curly braces.
                                </small> 
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Submission -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>

@endsection