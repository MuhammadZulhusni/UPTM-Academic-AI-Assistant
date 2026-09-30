@if (!empty($template->allow_brief_upload))
<style>
    .brief-upload-box {
        border: 1px dashed #c5d0dc;
        border-radius: 12px;
        background: #f8fafc;
        padding: 1rem 1.1rem;
        margin-bottom: 1.5rem;
    }
    .brief-upload-box .brief-upload-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
</style>
<div class="brief-upload-box">
    <div class="brief-upload-title">
        <em class="icon ni ni-upload"></em>
        Upload assignment brief
    </div>
    <p class="text-muted small mb-3">
        Upload a photo or PDF of the question, then click Generate Content. The result is written from that file. The fields below are optional.
    </p>
    <input type="file" name="brief" id="assignmentBriefFile" class="form-control" style="max-width: 28rem;" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp,application/pdf,.pdf">
    <div id="briefUploadStatus" class="small mt-2"></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('assignmentBriefFile');
    const status = document.getElementById('briefUploadStatus');
    const form = document.getElementById('generateForm');
    if (!fileInput || !form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        const file = fileInput.files && fileInput.files[0];
        status.className = 'small mt-2';
        if (!file) {
            event.preventDefault();
            event.stopImmediatePropagation();
            status.textContent = 'Upload a JPG, PNG, WEBP, or PDF, then generate.';
            status.classList.add('text-danger');
            return;
        }
        if (file.size > 8 * 1024 * 1024) {
            event.preventDefault();
            event.stopImmediatePropagation();
            status.textContent = 'The file must be 8 MB or smaller.';
            status.classList.add('text-danger');
        }
    }, true);
});
</script>
@endif
