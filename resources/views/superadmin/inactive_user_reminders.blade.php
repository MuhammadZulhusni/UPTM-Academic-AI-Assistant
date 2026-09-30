@extends('superadmin.dashboard')

@section('superadmin')
@php
    $selectedUserId = old('user_id', request('user_id'));
    $nudgeUserOptions = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'email' => $user->email,
            'idle_days' => $user->idle_days,
        ];
    })->values();
@endphp

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-content">
                <h2 class="display-6">User Reminders</h2>
                <p class="text-muted mb-4">Emails students and lecturers who have not used the assistant recently. OpenAI writes the message.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('superadmin.inactive.nudge.settings') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold mb-2" for="idleDays">Automatic</label>
                            <input type="number" name="idle_days" id="idleDays" class="form-control" min="1" max="90" required value="{{ old('idle_days', $idleDays) }}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="enabled" value="1" id="nudgeEnabled" {{ $enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="nudgeEnabled">Run every Monday at 9:00 AM</label>
                            </div>
                        </div>
                        <div class="col-md-5 d-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Save settings</button>
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">Every Monday, email anyone who has not created a document for this many days.</p>
                </form>

                <div class="mt-4">
                <label class="form-label fw-semibold">Specific user</label>
                <div class="d-flex flex-wrap gap-2 align-items-stretch">
                    <div class="nudge-search flex-grow-1">
                        <input type="text" id="nudgeUserSearch" class="form-control" placeholder="Search student or lecturer" autocomplete="off">
                        <input type="hidden" id="nudgeUserId" value="{{ $selectedUserId }}">
                        <div id="nudgeUserResults" class="nudge-search-results" hidden></div>
                    </div>
                    <button type="button" class="btn btn-light border h-100" onclick="openNudgePreview()">Preview</button>
                    <form action="{{ route('superadmin.inactive.nudge.send.user') }}" method="POST" onsubmit="return confirm('Send this reminder to the selected user now?') && copySelectedUser(this);">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
                        <button type="submit" class="btn btn-primary h-100">Send to user</button>
                    </form>
                </div>
                </div>

                <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="fw-semibold mb-0">Recent sends</p>
                    @if($nudges->isNotEmpty())
                        <button type="submit" form="clearSendsForm" class="btn btn-sm btn-outline-secondary">Clear selected</button>
                    @endif
                </div>
                @if($nudges->isEmpty())
                    <p class="text-muted mb-0">No emails sent yet.</p>
                @else
                    <form id="clearSendsForm" action="{{ route('superadmin.inactive.nudge.clear') }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th style="width: 2rem;">
                                        <input type="checkbox" id="selectAllNudges" class="form-check-input" aria-label="Select all send records">
                                    </th>
                                    <th>User</th>
                                    <th>Idle days</th>
                                    <th>Status</th>
                                    <th>Sent</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nudges as $nudge)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input nudge-clear-check" name="nudge_ids[]" value="{{ $nudge->id }}" form="clearSendsForm" aria-label="Select {{ $nudge->user->name ?? 'user' }}">
                                        </td>
                                        <td>{{ $nudge->user->name ?? 'User' }}</td>
                                        <td>{{ $nudge->days_inactive }}</td>
                                        <td>{{ $nudge->status }}</td>
                                        <td>{{ $nudge->sent_at?->format('d M Y H:i') ?? '—' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('superadmin.inactive.nudge.clear') }}" method="POST" onsubmit="return confirm('Clear this send record so this user can receive another reminder?');">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="nudge_ids[]" value="{{ $nudge->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Clear</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nudgePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title">Email preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="background:#f3f4f6;">
                <iframe id="nudgePreviewFrame" title="Email preview" style="width:100%; height:640px; border:0; background:#f3f4f6;"></iframe>
            </div>
        </div>
    </div>
</div>

<style>
.nudge-search {
    position: relative;
    min-width: 240px;
}
.nudge-search-results {
    position: absolute;
    z-index: 20;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    max-height: 260px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
}
.nudge-search-item {
    display: block;
    width: 100%;
    text-align: left;
    border: 0;
    background: #fff;
    padding: 10px 12px;
    font-size: 0.875rem;
    color: #0f172a;
}
.nudge-search-item:hover,
.nudge-search-item.is-active {
    background: #f8fafc;
}
.nudge-search-item small {
    display: block;
    color: #64748b;
    margin-top: 2px;
}
.nudge-search-empty {
    padding: 10px 12px;
    font-size: 0.875rem;
    color: #64748b;
}
</style>

<script>
const nudgeUsers = @json($nudgeUserOptions);

const searchInput = document.getElementById('nudgeUserSearch');
const userIdInput = document.getElementById('nudgeUserId');
const resultsBox = document.getElementById('nudgeUserResults');

function copySelectedUser(form) {
    if (!userIdInput.value) {
        alert('Select a user first.');
        searchInput.focus();
        return false;
    }
    form.querySelector('input[name="user_id"]').value = userIdInput.value;
    return true;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

function renderUsers(query) {
    const term = query.trim().toLowerCase();
    const matches = nudgeUsers.filter((user) => {
        const haystack = `${user.name} ${user.role} ${user.email}`.toLowerCase();
        return term === '' || haystack.includes(term);
    }).slice(0, 12);

    if (matches.length === 0) {
        resultsBox.innerHTML = '<div class="nudge-search-empty">No matching users</div>';
        resultsBox.hidden = false;
        return;
    }

    resultsBox.innerHTML = matches.map((user) => `
        <button type="button" class="nudge-search-item" data-id="${user.id}" data-name="${escapeHtml(user.name)}">
            ${escapeHtml(user.name)}
            <small>${escapeHtml(user.role)} · ${escapeHtml(user.email)} · idle ${user.idle_days}d</small>
        </button>
    `).join('');
    resultsBox.hidden = false;
}

searchInput.addEventListener('focus', () => renderUsers(searchInput.value));
searchInput.addEventListener('input', () => {
    userIdInput.value = '';
    renderUsers(searchInput.value);
});
resultsBox.addEventListener('click', (event) => {
    const item = event.target.closest('.nudge-search-item');
    if (!item) return;
    userIdInput.value = item.dataset.id;
    searchInput.value = item.dataset.name;
    resultsBox.hidden = true;
});
document.addEventListener('click', (event) => {
    if (!event.target.closest('.nudge-search')) {
        resultsBox.hidden = true;
    }
});

function openNudgePreview() {
    const frame = document.getElementById('nudgePreviewFrame');
    const modalEl = document.getElementById('nudgePreviewModal');
    const url = new URL(@json(route('superadmin.inactive.nudge.preview')), window.location.origin);
    if (userIdInput.value) {
        url.searchParams.set('user_id', userIdInput.value);
    }
    frame.src = url.toString();

    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        return;
    }
    if (window.jQuery) {
        jQuery(modalEl).modal('show');
    }
}

const selectAllNudges = document.getElementById('selectAllNudges');
const clearSendsForm = document.getElementById('clearSendsForm');
const nudgeClearChecks = () => document.querySelectorAll('.nudge-clear-check');

if (selectAllNudges) {
    selectAllNudges.addEventListener('change', () => {
        nudgeClearChecks().forEach((box) => {
            box.checked = selectAllNudges.checked;
        });
    });
}

if (clearSendsForm) {
    clearSendsForm.addEventListener('submit', (event) => {
        const selected = document.querySelectorAll('.nudge-clear-check:checked');
        if (selected.length === 0) {
            event.preventDefault();
            alert('Select at least one record.');
            return;
        }
        const label = selected.length === 1 ? 'this send record' : selected.length + ' send records';
        if (!confirm('Clear ' + label + ' so those users can receive another reminder?')) {
            event.preventDefault();
        }
    });
}
</script>
@endsection
