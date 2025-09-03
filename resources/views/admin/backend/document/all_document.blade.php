@extends('admin.dashboard')
@section('admin') 

<div class="nk-content-inner">
    <div class="nk-content-body">
    
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap gap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">
                        All Admin Document
                    </h2>
                    <p class="text-muted mb-0 d-none d-md-block">Manage and monitor all document</p>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between border-bottom border-light mt-3 mt-md-5 mb-4 pb-2 gap-3">
            <div class="d-flex align-items-center gap-3">
                <h5 class="mb-0">All Documents</h5>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-sm flex-grow-1" style="max-width: 300px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search documents..." id="searchInput">
                </div>
                </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($document) > 0)
                <div class="table-responsive">
                    <table class="table table-middle table-hover mb-0" id="documentsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="tb-col d-none d-md-table-cell" style="width: 60px;">
                                    <div class="fs-13px text-base fw-semibold">Sl</div>
                                </th>
                                <th class="tb-col">
                                    <div class="fs-13px text-base fw-semibold">Document</div>
                                </th>
                                <th class="tb-col d-none d-lg-table-cell">
                                    <div class="fs-13px text-base fw-semibold">User</div>
                                </th>
                                <th class="tb-col d-none d-md-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Category</div>
                                </th>
                                <th class="tb-col d-none d-sm-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Words</div>
                                </th>
                                <th class="tb-col d-none d-lg-table-cell">
                                    <div class="fs-13px text-base fw-semibold">Created</div>
                                </th>
                                <th class="tb-col text-center">
                                    <div class="fs-13px text-base fw-semibold">Action</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($document as $key => $item)  
                            <tr class="document-row">
                                <td class="tb-col d-none d-md-table-cell">
                                    <div class="caption-text fw-medium">{{ $key + 1 }}</div>
                                </td>
                                <td class="tb-col">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="fs-6 fw-medium text-dark text-truncate">{{ $item->template->title }}</div>
                                            <div class="d-block d-lg-none">
                                                <small class="text-muted d-block">By {{ $item->user->name }}</small>
                                                <div class="d-flex gap-2 mt-1">
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small">
                                                        {{ $item->template->category }}
                                                    </span>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small d-sm-none">
                                                        {{ number_format($item->word_count) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="tb-col d-none d-lg-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $id = Auth::user()->id;
                                            $profileData = App\Models\User::find($id);
                                        @endphp
                                        <div class="bg-info bg-opacity-10 rounded-circle overflow-hidden" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                            <img src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" 
                                                 class="w-100 h-100 object-fit-cover" alt="User"/>
                                        </div>
                                        <div class="min-width-0">
                                            <div class="fs-6 fw-medium text-dark text-truncate">{{ $item->user->name }}</div>
                                            <small class="text-muted text-truncate d-block">{{ $item->user->email ?? 'No email' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="tb-col d-none d-md-table-cell">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                        {{ $item->template->category }}
                                    </span>
                                </td>
                                <td class="tb-col d-none d-sm-table-cell">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge text-bg-success-soft rounded-pill px-3 py-2 fs-6 lh-sm fw-medium">
                                            {{ number_format($item->word_count) }}
                                        </span>
                                        @if($item->word_count > 1000)
                                            <i class="bi bi-star-fill text-warning d-none d-md-inline" title="High word count"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="tb-col d-none d-lg-table-cell">
                                    <div class="fs-7 text-muted">
                                        {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="tb-col">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('edit.admin.document', $item->id) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Edit Document"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                            <span class="d-none d-xl-inline ms-1">Edit</span>
                                        </a>
                                        <button class="btn btn-outline-info btn-sm" 
                                                title="View Document"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewDocumentModal"
                                                data-document-id="{{ $item->id }}"
                                                data-document-title="{{ $item->template->title }}"
                                                data-user-name="{{ $item->user->name }}"
                                                data-category="{{ $item->template->category }}"
                                                data-word-count="{{ $item->word_count }}"
                                                data-created-date="{{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}"
                                                data-output="{{ base64_encode($item->output) }}"
                                                onclick="loadDocumentContentFromData(this)">
                                            <i class="bi bi-eye"></i>
                                            <span class="d-none d-xl-inline ms-1">View</span>
                                        </button>
                                        <a href="{{ route('delete.admin.document', $item->id) }}" 
                                           class="btn btn-outline-danger btn-sm" 
                                           id="delete"
                                           title="Delete Document"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-trash"></i>
                                            <span class="d-none d-xl-inline ms-1">Delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-file-text display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Documents Found</h5>
                    <p class="text-muted mb-0">There are no documents to display at the moment.</p>
                </div>
                @endif
            </div>
        </div>

        @if(count($document) > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <div class="text-muted order-2 order-md-1">
                Showing {{ count($document) }} of {{ count($document) }} entries
            </div>
            <nav class="order-1 order-md-2">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Next</span>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title" id="documentTitle">Document Details</h5>
                    <small class="text-muted" id="documentMeta">Document information</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-12">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    Document Information
                                </h6>
                                <hr class="my-3">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted small">DOCUMENT TITLE</label>
                                    <p class="mb-0" id="modalDocTitle">-</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted small">USER</label>
                                    <p class="mb-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-person-circle text-info"></i>
                                        <span id="modalUserName">-</span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted small">CATEGORY</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1" id="modalCategory">-</span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-muted small">WORD COUNT</label>
                                    <p class="mb-0 d-flex align-items-center gap-2">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1" id="modalWordCount">-</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h6 class="card-title d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-text text-primary"></i>
                                        Document Content
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyContent()">
                                            <i class="bi bi-copy me-1"></i> <span class="d-none d-sm-inline">Copy</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" onclick="downloadContent()">
                                            <i class="bi bi-download me-1"></i> <span class="d-none d-sm-inline">Download</span>
                                        </button>
                                    </div>
                                </h6>
                                <hr class="my-3">
                                <div class="bg-white border rounded p-3 p-md-4" style="height: 350px; overflow-y: auto;">
                                    <div id="documentContent" class="text-muted">
                                        <div class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Loading document content...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary order-2 order-sm-1" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="copySuccessToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                Content copied to clipboard successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    
    <div id="downloadSuccessToast" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-download me-2"></i>
                Document downloaded successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    
    <div id="copyErrorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Failed to copy content. Please try again.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-4">
                <div id="successIcon" class="mb-3">
                    </div>
                <h5 id="successTitle" class="mb-2">Success!</h5>
                <p id="successMessage" class="text-muted mb-3">Operation completed successfully.</p>
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>


<script>
// This function listens for input in the search bar.
// It iterates through each document row and hides rows that don't match the search term.
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.document-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// This function initializes Bootstrap tooltips on elements that have the 'data-bs-toggle="tooltip"' attribute.
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// This function is triggered when a user clicks the 'View' button.
// It extracts data from the button's HTML data attributes and passes it to the main `loadDocumentContent` function.
function loadDocumentContentFromData(button) {
    // Get data from button attributes
    const id = button.getAttribute('data-document-id');
    const title = button.getAttribute('data-document-title');
    const userName = button.getAttribute('data-user-name');
    const category = button.getAttribute('data-category');
    const wordCount = parseInt(button.getAttribute('data-word-count'));
    const outputData = atob(button.getAttribute('data-output')); // Decodes the base64-encoded output data
    
    // Call the original function
    loadDocumentContent(id, title, userName, category, wordCount, outputData);
}

// This function takes document data and populates the view modal with it.
function loadDocumentContent(id, title, userName, category, wordCount,outputData) {
    // Update static modal information immediately
    document.getElementById('modalDocTitle').textContent = title;
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalCategory').textContent = category;
    document.getElementById('modalWordCount').textContent = wordCount.toLocaleString();
    document.getElementById('documentTitle').textContent = title;
    document.getElementById('documentMeta').textContent = `By ${userName}`;
    
    const contentDiv = document.getElementById('documentContent');
    
    // HTML escapes the output data to prevent cross-site scripting (XSS) attacks.
    const escapedOutput = outputData.replace(/&/g, '&amp;')
                                   .replace(/</g, '&lt;')
                                   .replace(/>/g, '&gt;')
                                   .replace(/"/g, '&quot;')
                                   .replace(/'/g, '&#039;');
    
    // Sets the inner HTML of the document content area with the escaped output.
    contentDiv.innerHTML = `
        <div class="document-content">
            <h6 class="mb-3">Generated Output</h6>
            <p id="documentContentText" class="text-dark bg-white p-3 border rounded" style="white-space: pre-wrap; word-wrap: break-word; overflow-x: auto;">${escapedOutput}</p>
        </div>
    `;
}

// This function handles the logic for copying the document content to the clipboard.
function copyContent() {
    const contentElement = document.getElementById('documentContentText');
    if (!contentElement) {
        showToast('No content available to copy', 'error');
        return;
    }
    
    const content = contentElement.innerText || contentElement.textContent;
    
    // Shows a loading state on the button while the copy operation is in progress.
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Copying...</span>';
    
    // Uses the modern Clipboard API if available and the context is secure (HTTPS).
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(content)
            .then(() => {
                showToast('Content copied to clipboard successfully!', 'success');
            })
            .catch(err => {
                console.error('Failed to copy:', err);
                fallbackCopy(content);
            })
            .finally(() => {
                resetButton(btn, originalHTML);
            });
    } else {
        // Falls back to a traditional method for older browsers.
        fallbackCopy(content);
        resetButton(btn, originalHTML);
    }
}

// This function handles the logic for downloading the document content as a text file.
function downloadContent() {
    const titleElement = document.getElementById('modalDocTitle');
    const contentElement = document.getElementById('documentContentText');
    
    if (!contentElement || !titleElement) {
        showToast('No content available to download', 'error');
        return;
    }
    
    const title = titleElement.textContent || 'document';
    const content = contentElement.innerText || contentElement.textContent;
    
    // Shows a loading state on the button.
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Downloading...</span>';
    
    try {
        // Creates a Blob (binary large object) from the text content.
        const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
        // Creates a URL for the Blob.
        const url = window.URL.createObjectURL(blob);
        // Creates a temporary anchor element to trigger the download.
        const a = document.createElement('a');
        a.href = url;
        // Sets the download file name by sanitizing the document title.
        a.download = `${title.replace(/[^a-z0-9\s\-_.]/gi, '_').replace(/\s+/g, '_').toLowerCase()}.txt`;
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        // Cleans up the temporary elements and URL.
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showToast('Document downloaded successfully!', 'success');
        
    } catch (error) {
        console.error('Download failed:', error);
        showToast('Failed to download content', 'error');
    }
    
    resetButton(btn, originalHTML);
}

// This is the fallback copy method for browsers that do not support the modern Clipboard API.
function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Content copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy content', 'error');
        }
    } catch (err) {
        showToast('Copy not supported in this browser', 'error');
    }
    
    document.body.removeChild(textArea);
}
// Resets a button's state after a loading period.
function resetButton(btn, originalHTML) {
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }, 800);
}

// This function creates and displays a custom toast notification on the screen.
function showToast(message, type = 'success') {
    // Removes any existing toasts to avoid stacking.
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Creates the toast element and sets its classes based on the message type.
    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
    const bgColor = type === 'success' ? '#198754' : '#dc3545';
    
    toast.innerHTML = `
        <div class="toast-content">
            <i class="bi ${icon}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Applies inline styles for positioning and animation.
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 9999;
        font-size: 14px;
        font-weight: 500;
        max-width: 400px;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
    `;
    
    // Appends the toast to the body of the document.
    document.body.appendChild(toast);
    
    // Animates the toast to slide in from the right.
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Automatically removes the toast after a delay.
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// This creates and appends a style block to the document head to define necessary CSS classes for the spinner and custom toasts.
const style = document.createElement('style');
style.textContent = `
    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
        border-width: 0.1em;
    }
    
    .custom-toast .toast-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .custom-toast i {
        font-size: 16px;
    }
    
    @media (max-width: 576px) {
        .custom-toast {
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
`;
document.head.appendChild(style);

// This function reloads the current page, effectively refreshing the data.
function refreshData() {
    location.reload();
}
</script>

<style>
.min-width-0 {
    /* Prevents the element from having a minimum width, allowing it to shrink as needed in a flexible layout. */
    min-width: 0;
}

.text-truncate {
    /* Hides overflowing text and replaces it with an ellipsis (...) to indicate it has been cut off. */
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.object-fit-cover {
    /* Scales an image or video to fill its container while preserving its aspect ratio. Content may be cropped. */
    object-fit: cover;
}

@media (max-width: 576px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-xl {
        max-width: calc(100vw - 20px);
    }
}

@media (max-width: 768px) {
    .nk-block-head-content h2 {
        font-size: 1.5rem;
    }
    
    .table td, .table th {
        padding: 0.5rem 0.75rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
}
</style>

@endsection