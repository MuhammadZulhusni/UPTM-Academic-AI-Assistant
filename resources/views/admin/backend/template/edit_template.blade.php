@extends('admin.dashboard')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Edit Template</h3>
                    <div class="nk-block-des text-soft">
                        <p>Update the details and settings for your custom template.</p>
                    </div>
                </div>
            </div>
        </div><div class="nk-block">
            <div class="card card-bordered shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('update.template',$template->id) }}" method="post" enctype="multipart/form-data">
                        @csrf   

                        <div class="nk-block-head nk-block-head-sm">
                            <h5 class="nk-block-title">Template Details</h5>
                            <p class="mb-4">Basic information about your template.</p>
                        </div>
                        <div class="row g-4 gx-5 mb-5">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="templateName" class="form-label">Template Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="title" id="templateName" class="form-control form-control-lg" value="{{ $template->title }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="templateDescription" class="form-label">Template Description</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="description" id="templateDescription" class="form-control form-control-lg" value="{{ $template->description }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="templateCategory" class="form-label">Template Category</label>
                                    <div class="form-control-wrap">
                                        <select name="category" class="form-select form-control-lg" id="templateCategory" data-search="true">
                                            <option value="">Select Category</option>
                                            <option value="Student" {{ $template->category == 'Student' ? 'selected' : '' }}>Student</option>
                                            <option value="Lecturer" {{ $template->category == 'Lecturer' ? 'selected' : '' }}>Lecturer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_icon" class="form-label form-label-custom">Template Icon</label>

                                    <select name="icon" id="template_icon" class="form-control form-control-lg" required>
                                        <option value="" selected disabled>-- Select Template Icon --</option>
                                        <option value="writing.png">Writing</option>
                                        <option value="teaching.png">Teaching</option>
                                        <option value="learning.png">Learning</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="nk-block-head nk-block-head-sm">
                            <h5 class="nk-block-title">Input Fields</h5>
                            <p class="mb-4">Configure the custom fields for this template.</p>
                        </div>
                        <div id="input-fields" class="mb-5">
                            @foreach ($template->inputFields as $field)
                                <div class="card card-bordered mb-3">
                                    <div class="card-body p-4">
                                        <div class="row g-3 gx-4">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="input_fields_{{ $loop->index }}_title" class="form-label">Input Title</label>
                                                    <input type="text" name="input_fields[{{ $loop->index }}][title]" id="input_fields_{{ $loop->index }}_title" class="form-control" value="{{ $field->title }}" required>
                                                </div> 
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="input_fields_{{ $loop->index }}_description" class="form-label">Description</label>
                                                    <input type="text" name="input_fields[{{ $loop->index }}][description]" id="input_fields_{{ $loop->index }}_description" class="form-control" value="{{ $field->description }}" required>
                                                </div> 
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="input_fields_{{ $loop->index }}_type" class="form-label">Field Type</label>
                                                    <select name="input_fields[{{ $loop->index }}][type]" class="form-select" id="input_fields_{{ $loop->index }}_type"> 
                                                        <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Input Field</option>
                                                        <option value="textarea" {{ $field->type == 'textarea' ? 'selected' : '' }}>Textarea Field</option> 
                                                    </select>
                                                </div> 
                                            </div>
                                            <input type="hidden" name="input_fields[{{ $loop->index }}][is_required]" value="1">
                                        </div> 
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="nk-block-head nk-block-head-sm">
                            <h5 class="nk-block-title">Custom Prompt</h5>
                            <p class="mb-4">The core instruction code for your template.</p>
                        </div>
                        <div class="form-group mb-5">
                            <div class="form-control-wrap">
                                <textarea name="prompt" id="promptCode" placeholder="Add your prompt code here..." class="form-control form-control-lg" rows="5">{{ $template->prompt }}</textarea>
                            </div>
                            <div class="form-text text-soft small mt-1">Example: "Write a 400-word article about {topic} with an introduction."</div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn btn-primary btn-lg end">
                                <span>Save Changes</span>
                            </button> 
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>

@endsection