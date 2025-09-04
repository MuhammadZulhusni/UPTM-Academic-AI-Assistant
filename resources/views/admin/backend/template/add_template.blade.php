@extends('admin.dashboard')

@section('admin')
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Add Template</h2>
                </div>
            </div>
        </div>
        
        <div class="nk-block">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-head-content">
                </div>
            </div>
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('store.template') }}" method="post" enctype="multipart/form-data">
                        @csrf   

                        <div class="row g-3 gx-gs">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_name" class="form-label fw-medium">Template Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="title" id="template_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_desc" class="form-label fw-medium">Template Description</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="description" id="template_desc" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="form-label fw-medium">Template Category</label>
                                    <div class="form-control-wrap">
                                        <select name="category" class="form-select" id="category" required>
                                            <option selected="">Select Category</option>
                                            <option value="Student">Student</option>
                                            <option value="Lecturer">Lecturer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_icon" class="form-label fw-medium">Template Icon</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="icon" id="template_icon" class="form-control" placeholder="e.g., fa-solid fa-book" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="flexCheckChecked" checked="">
                                        <label class="form-check-label fw-medium" for="flexCheckChecked">
                                            Activate Template
                                        </label>
                                        <input type="hidden" name="is_active" value="1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label fw-medium mb-3">Input Fields Configuration</label>
                                    <div class="border rounded-3 p-3 bg-light bg-opacity-50">
                                        <div id="input-fields">
                                            <div class="row input-field-row g-2">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="input_fields_0_title" class="form-label small">Input Field Title *</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" name="input_fields[0][title]" id="input_fields_0_title" class="form-control form-control-sm" placeholder="Enter Input Field Title" required>
                                                        </div>
                                                    </div> 
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="input_fields_0_description" class="form-label small">Input Field Description *</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" name="input_fields[0][description]" id="input_fields_0_description" class="form-control form-control-sm" placeholder="Enter Input Field Description" required>
                                                        </div>
                                                    </div> 
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="input_fields_0_type" class="form-label small">Field Type *</label>
                                                        <div class="form-control-wrap">
                                                            <select name="input_fields[0][type]" class="form-select form-select-sm" id="input_fields_0_type" required> 
                                                                <option value="text">Input Field</option>
                                                                <option value="textarea">Textarea Field</option> 
                                                            </select>
                                                        </div>
                                                    </div> 
                                                </div>

                                                <!-- <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label small">&nbsp;</label>
                                                        <div class="form-control-wrap">
                                                            <button type="button" class="btn btn-outline-primary btn-sm w-100" id="add-field">
                                                                + Add Field
                                                            </button>
                                                            <input type="hidden" name="input_fields[0][is_required]" value="1">
                                                        </div>
                                                    </div> 
                                                </div> -->
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mt-2">
                                    <label for="prompt" class="form-label fw-medium">Custom Prompt</label>
                                    <textarea name="prompt" id="prompt" placeholder="Add Your Prompt Code" class="form-control" rows="4" required></textarea>
                                    <small class="text-muted">Write a 400 word about {topic} with an introduction</small> 
                                </div>
                            </div>
                        </div>
 
                        <div class="col-lg-12 col-xl-12 mt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    border: none;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.75rem rgba(0, 0, 0, 0.08) !important;
}

.form-control, .form-select {
    border: 1.5px solid #e8ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.15s ease-in-out;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.1);
}

.form-control-sm, .form-select-sm {
    border-radius: 6px;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.form-label {
    color: #374151;
    margin-bottom: 0.5rem;
}

.fw-medium {
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    background-color: #4f46e5;
    border-color: #4f46e5;
    padding: 0.75rem 1.5rem;
}

.btn-primary:hover {
    background-color: #4338ca;
    border-color: #4338ca;
    transform: translateY(-1px);
}

.btn-outline-primary {
    color: #4f46e5;
    border-color: #4f46e5;
}

.btn-outline-primary:hover {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.btn-outline-secondary {
    color: #6b7280;
    border-color: #d1d5db;
}

.form-check-input:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.bg-light {
    background-color: #f8fafc !important;
}

.rounded-3 {
    border-radius: 12px !important;
}

.input-field-row {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.text-muted {
    color: #6b7280 !important;
}

.small {
    font-size: 0.875rem;
}
</style>





<!-- Testing --->
<script>
$(document).ready(function() {
    let fieldIndex = 1;
    
    // Add new input field
    $('#add-field').click(function() {
        const newField = `
            <div class="row input-field-row g-2 mt-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="input_fields_${fieldIndex}_title" class="form-label small">Input Field Title *</label>
                        <div class="form-control-wrap">
                            <input type="text" name="input_fields[${fieldIndex}][title]" id="input_fields_${fieldIndex}_title" class="form-control form-control-sm" placeholder="Enter Input Field Title" required>
                        </div>
                    </div> 
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="input_fields_${fieldIndex}_description" class="form-label small">Input Field Description *</label>
                        <div class="form-control-wrap">
                            <input type="text" name="input_fields[${fieldIndex}][description]" id="input_fields_${fieldIndex}_description" class="form-control form-control-sm" placeholder="Enter Input Field Description" required>
                        </div>
                    </div> 
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="input_fields_${fieldIndex}_type" class="form-label small">Field Type *</label>
                        <div class="form-control-wrap">
                            <select name="input_fields[${fieldIndex}][type]" class="form-select form-select-sm" id="input_fields_${fieldIndex}_type" required> 
                                <option value="text">Input Field</option>
                                <option value="textarea">Textarea Field</option> 
                            </select>
                        </div>
                    </div> 
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label small">&nbsp;</label>
                        <div class="form-control-wrap">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-field">
                                Remove
                            </button>
                            <input type="hidden" name="input_fields[${fieldIndex}][is_required]" value="1">
                        </div>
                    </div> 
                </div>
            </div>
        `;
        
        $('#input-fields').append(newField);
        fieldIndex++;
    });
    
    // Remove input field
    $(document).on('click', '.remove-field', function() {
        $(this).closest('.input-field-row').remove();
    });
});
</script>

@endsection