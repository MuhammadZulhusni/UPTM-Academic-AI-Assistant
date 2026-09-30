<button type="button" class="btn btn-outline-primary {{ $buttonClass ?? '' }}" data-bs-toggle="modal" data-bs-target="#findTemplateModal">
    <em class="icon ni ni-search"></em>
    <span class="ms-1">Find a template</span>
</button>

<div class="modal fade" id="findTemplateModal" tabindex="-1" aria-labelledby="findTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="findTemplateModalLabel">Find a template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="templateNeed" class="form-label fw-semibold">What are you looking for?</label>
                <textarea id="templateNeed" class="form-control" rows="3" maxlength="500" placeholder="Example: I need to write a reflection about my internship"></textarea>
                <div id="templateMatchStatus" class="small mt-3"></div>
                <div id="templateMatchList" class="d-flex flex-column gap-3 mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="templateMatchBtn">
                    <span class="btn-text">Suggest</span>
                    <span class="btn-loading" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Looking…
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('templateMatchBtn');
    const needInput = document.getElementById('templateNeed');
    const status = document.getElementById('templateMatchStatus');
    const list = document.getElementById('templateMatchList');
    if (!button || !needInput) {
        return;
    }

    const token = @json(csrf_token());
    const matchUrl = @json($matchUrl);

    button.addEventListener('click', async function () {
        const need = needInput.value.trim();
        status.className = 'small mt-3';
        list.innerHTML = '';

        if (need.length < 3) {
            status.textContent = 'Describe what you need in a few words.';
            status.classList.add('text-danger');
            return;
        }

        button.disabled = true;
        button.querySelector('.btn-text').style.display = 'none';
        button.querySelector('.btn-loading').style.display = 'inline-flex';
        status.textContent = '';

        try {
            const response = await fetch(matchUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ need: need })
            });
            const data = await response.json().catch(function () { return {}; });
            if (!response.ok || !data.success) {
                const validationMessage = data.errors && data.errors.need ? data.errors.need[0] : null;
                throw new Error(validationMessage || data.message || 'Suggestions are unavailable right now.');
            }

            const matches = data.matches || [];
            if (matches.length === 0) {
                status.textContent = 'No template fits that. Try describing the task in another way.';
                status.classList.add('text-muted');
                return;
            }

            list.innerHTML = matches.map(function (match) {
                return `
                    <div class="border rounded p-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <a href="${match.url}" class="fw-semibold text-decoration-none">${escapeMatchText(match.title)}</a>
                            <span class="badge bg-light text-dark border">${escapeMatchText(match.category)}</span>
                        </div>
                        <p class="small text-muted mb-2 mt-2">${escapeMatchText(match.description || '')}</p>
                        <p class="mb-0"><span class="fw-semibold">Reason:</span> ${escapeMatchText(match.reason)}</p>
                    </div>
                `;
            }).join('');
        } catch (error) {
            status.textContent = error.message || 'Suggestions are unavailable right now.';
            status.classList.add('text-danger');
        } finally {
            button.disabled = false;
            button.querySelector('.btn-text').style.display = 'inline';
            button.querySelector('.btn-loading').style.display = 'none';
        }
    });

    function escapeMatchText(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});
</script>
