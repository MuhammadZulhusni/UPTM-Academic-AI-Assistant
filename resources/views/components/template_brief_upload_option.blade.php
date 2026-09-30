@php
    $briefUpload = (string) old('allow_brief_upload', (isset($template) && !empty($template->allow_brief_upload)) ? '1' : '0');
@endphp

<div class="col-12">
    <label class="form-label form-label-custom">How users fill this template</label>
    <div class="d-flex flex-column gap-2 mt-1">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="allow_brief_upload" id="brief_upload_off" value="0" {{ $briefUpload === '0' ? 'checked' : '' }} required>
            <label class="form-check-label" for="brief_upload_off">Type only</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="allow_brief_upload" id="brief_upload_on" value="1" {{ $briefUpload === '1' ? 'checked' : '' }}>
            <label class="form-check-label" for="brief_upload_on">Allow image or PDF upload</label>
        </div>
    </div>
</div>
