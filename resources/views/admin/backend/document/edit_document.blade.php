@extends('admin.dashboard')
@section('admin')

{{-- External Scripts and Styles --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    /* for the editor container */
    .nk-editor {
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
</style>

{{-- Main Page Content --}}
<div class="nk-content-inner">
    <div class="nk-content-body">
        
        {{-- Page Header --}}
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Edit Document</h2> 
                </div>
            </div>
        </div>

        {{-- Document Editor Card --}}
        <div class="card shadow-none">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        {{-- Form to update the document --}}
                        <form action="{{ route('admin.update.document',$document->id) }}" method="post" id="editDocumentForm" enctype="multipart/form-data">
                            @csrf  
                            {{-- Hidden input to store Quill editor's HTML output --}}
                            <input type="hidden" name="output" id="editor-output">

                            {{-- Editor Layout --}}
                            <div class="nk-editor"> 
                                <div class="nk-editor-header">
                                    <div class="nk-editor-title">
                                        {{-- Dynamic Title Generation based on document input data --}}
                                        <h4 class="me-3 mb-0 line-clamp-1">
                                            @php
                                                // Decode the 'input' field from the document, which is stored as a JSON string, into a PHP array.
                                                // The '??' operator provides a fallback to an empty JSON object '{}' if the 'input' field is null,
                                                // and another fallback to an empty array '[]' if the decoding fails.
                                                $inputData = json_decode($document->input ?? '{}', true) ?? [];

                                                // Set a default title for the document.
                                                $title = 'Document Editor';
                                                
                                                // Define a list of common field names that might contain a suitable title.
                                                $commonFields = ['Topic_or_Concept', 'topic', 'Thesis_or_Research_Topic', 'Research_Field_or_Area', 'title', 'subject'];
                                                
                                                // Loop through the common fields to find a title.
                                                foreach ($commonFields as $field) {
                                                    // Check if the current field exists in the input data and is not empty.
                                                    if (isset($inputData[$field]) && !empty($inputData[$field])) {
                                                        // If a suitable field is found, set it as the title and exit the loop.
                                                        $title = $inputData[$field];
                                                        break;
                                                    }
                                                }
                                                
                                                // If no common field was found, use the value of the first available field in the input data.
                                                if ($title === 'Document Editor' && !empty($inputData)) {
                                                    // Get the key of the first element in the array.
                                                    $firstKey = array_key_first($inputData);
                                                    // Check if the first key exists and its value is not empty.
                                                    if ($firstKey && isset($inputData[$firstKey]) && !empty($inputData[$firstKey])) {
                                                        // Set the title to the value of the first key.
                                                        $title = $inputData[$firstKey];
                                                    }
                                                }
                                            @endphp

                                            {{-- Display the final determined title --}}
                                            {{ $title }}
                                        </h4> 
                                    </div>

                                    {{-- Save button for the form --}}
                                    <div class="nk-editor-tools d-none d-xl-flex">
                                        <ul class="d-inline-flex gap gx-3">
                                            <li>
                                                <button type="submit" class="btn btn-md btn-primary rounded-pill">Save Changes</button>
                                            </li> 
                                        </ul> 
                                    </div> 
                                </div>

                                {{-- Quill Editor Body --}}
                                <div class="nk-editor-main"> 
                                    <div class="nk-editor-body">
                                        <div class="wide-md h-100"> 
                                            {{-- This div is where Quill.js will render the rich text editor --}}
                                            <div id="editor-v1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>      
                    </div>
                </div> 
            </div> 
        </div> 
    </div>
</div> 

{{-- JavaScript --}}
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
    // Initialize Quill editor with a toolbar
    let quill;
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
        console.log('Quill initialized successfully');
    } catch (error) {
        console.error('Quill initialization failed:', error);
    }

    // Load initial content and sync with form
    document.addEventListener('DOMContentLoaded', function() {
        if (!quill) {
            console.error('Quill not initialized');
            return;
        }

        // Set the editor's content from the document's output data
        let initialContent = `{!! $document->output ?? '' !!}`;
        if (initialContent && initialContent.trim() !== '') {
            quill.root.innerHTML = initialContent;
        }

        // On every text change, update the hidden input field
        quill.on('text-change', function() {
            const content = quill.root.innerHTML.trim();
            const outputField = document.getElementById('editor-output');
            if (outputField) {
                outputField.value = content;
            }
        });

        // On form submission, ensure the latest content is saved to the hidden input
        const form = document.getElementById('editDocumentForm');
        if (form) {
            form.addEventListener('submit', function() {
                const content = quill.root.innerHTML.trim();
                const outputField = document.getElementById('editor-output');
                if (outputField) {
                    outputField.value = content;
                }
            });
        }
    });
</script>
 
@endsection