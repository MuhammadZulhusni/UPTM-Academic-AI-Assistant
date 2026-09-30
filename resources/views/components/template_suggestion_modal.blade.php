<div class="modal fade" id="templateSuggestModal" tabindex="-1" aria-labelledby="templateSuggestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="templateSuggestModalLabel">Suggested templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Based on templates already in the system. Choose one to fill Template Details and Configuration. You can still edit before saving.</p>
                <div id="templateSuggestStatus" class="text-muted small">Loading ideas…</div>
                <div id="templateSuggestList" class="d-flex flex-column gap-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const suggestUrl = @json($suggestUrl);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let fieldIndex = 1;

    function inputFieldRow(index, canRemove) {
        const removeBtn = canRemove
            ? `<button type="button" class="btn btn-outline-danger btn-sm w-100 remove-field"><i class="bi bi-trash"></i> Remove</button>`
            : '';
        const titleId = index === 0 ? ' id="input_fields_0_title"' : '';
        const descId = index === 0 ? ' id="input_fields_0_description"' : '';

        return `
            <div class="row input-field-row g-3 ${index === 0 ? '' : 'mt-3'}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label small">Field Title (Variable Name)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="input_fields[${index}][title]"${titleId} class="form-control field-variable-title" value="topic" placeholder="e.g., topic" maxlength="30" required>
                            <button type="button" class="btn btn-outline-primary copy-variable-btn" data-copy="{topic}">Copy</button>
                        </div>
                        ${index === 0 ? '<small class="text-muted">Copy <span class="variable-preview">{topic}</span>, then paste it into the custom prompt.</small>' : ''}
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label small">Field Description (Helper Text)</label>
                        <input type="text" name="input_fields[${index}][description]"${descId} class="form-control form-control-sm" placeholder="e.g., What specific subject should the content cover?" required>
                    </div>
                </div>
                <input type="hidden" name="input_fields[${index}][type]" value="textarea">
                <input type="hidden" name="input_fields[${index}][is_required]" value="1">
                <div class="col-md-3 d-flex align-items-end">${removeBtn}</div>
            </div>
        `;
    }

    function applySuggestion(suggestion) {
        $('#template_name').val(suggestion.title || '');
        $('#template_desc').val(suggestion.description || '');
        $('#category').val(suggestion.category || 'Student');
        $('#template_icon').val(suggestion.icon || 'writing.png');
        $('#prompt').val(suggestion.prompt || '');

        const fields = Array.isArray(suggestion.input_fields) && suggestion.input_fields.length
            ? suggestion.input_fields
            : [{ title: 'topic', description: 'What specific subject should the content cover?' }];

        const $container = $('#input-fields');
        $container.empty();
        fields.forEach((field, index) => {
            $container.append(inputFieldRow(index, index > 0));
            $(`input[name="input_fields[${index}][title]"]`).val(String(field.title || 'topic').slice(0, 30)).trigger('input');
            $(`input[name="input_fields[${index}][description]"]`).val(field.description || '');
        });
        fieldIndex = fields.length;

        const modalEl = document.getElementById('templateSuggestModal');
        if (modalEl && window.bootstrap) {
            bootstrap.Modal.getInstance(modalEl)?.hide();
        }

        if (typeof toastr !== 'undefined') {
            toastr.success('Form filled from the suggested template. Review and save when ready.');
        }

        const formTop = $('#templateForm').offset();
        if (formTop) {
            $('html, body').animate({ scrollTop: formTop.top - 80 }, 400);
        }
    }

    function renderSuggestions(suggestions) {
        const $list = $('#templateSuggestList');
        $list.empty();

        suggestions.forEach((suggestion, index) => {
            const reason = suggestion.reason
                ? `<p class="small mb-0 mt-2"><span class="fw-semibold">Reason:</span> ${$('<div>').text(suggestion.reason).html()}</p>`
                : '';
            $list.append(`
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between gap-2 flex-wrap">
                        <div>
                            <h6 class="mb-1">${$('<div>').text(suggestion.title || 'Untitled').html()}</h6>
                            <span class="badge bg-light text-dark me-1">${$('<div>').text(suggestion.category || '').html()}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary use-suggested-template" data-index="${index}">Use this template</button>
                    </div>
                    <p class="small mb-0 mt-2">${$('<div>').text(suggestion.description || '').html()}</p>
                    ${reason}
                </div>
            `);
        });

        $list.find('.use-suggested-template').on('click', function() {
            const i = Number($(this).data('index'));
            applySuggestion(suggestions[i]);
        });
    }

    $('#suggestTemplatesBtn').on('click', function() {
        const modalEl = document.getElementById('templateSuggestModal');
        if (modalEl && window.bootstrap) {
            new bootstrap.Modal(modalEl).show();
        }

        $('#templateSuggestStatus').text('Loading ideas from current templates…').show();
        $('#templateSuggestList').empty();

        $.ajax({
            url: suggestUrl,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(res) {
                if (!res.success || !res.suggestions || !res.suggestions.length) {
                    $('#templateSuggestStatus').text(res.message || 'No suggestions returned.');
                    return;
                }
                $('#templateSuggestStatus').hide();
                renderSuggestions(res.suggestions);
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Could not load suggestions. Try again.';
                $('#templateSuggestStatus').text(message);
            }
        });
    });

    $(document).on('click', '.remove-field', function() {
        $(this).closest('.input-field-row').remove();
    });
});
</script>
