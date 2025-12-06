@extends('admin.dashboard')
@section('admin') 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
    
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap gap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">
                        My Generated Documents
                    </h2>
                    <p class="text-muted mb-0 d-none d-md-block">View and manage all AI content you’ve created.</p>
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

                <!-- Sort Button -->
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortModal">
                    <i class="bi bi-funnel"></i> Sort
                </button>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($document) > 0) {{-- Check if there are any documents --}}
                <div id="document-table-container">
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
                                    <!-- <th class="tb-col d-none d-lg-table-cell">
                                        <div class="fs-13px text-base fw-semibold">User</div>
                                    </th> -->
                                    <!-- <th class="tb-col d-none d-md-table-cell">
                                        <div class="fs-13px text-base fw-semibold">Category</div>
                                    </th> -->
                                    <!-- <th class="tb-col d-none d-sm-table-cell">
                                        <div class="fs-13px text-base fw-semibold">Words</div>
                                    </th> -->
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
                                        <div class="caption-text fw-medium">{{ $document->firstItem() + $loop->index }}</div>
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
                                    <!-- <td class="tb-col d-none d-lg-table-cell">
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                // Get the currently authenticated user's ID
                                                $id = Auth::user()->id;
                                                // Retrieve the user's profile data from the database using the User model
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
                                    </td> -->
                                    <!-- <td class="tb-col d-none d-md-table-cell">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
                                            {{ $item->template->category }}
                                        </span>
                                    </td> -->
                                    <!-- <td class="tb-col d-none d-sm-table-cell">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge text-bg-success-soft rounded-pill px-3 py-2 fs-6 lh-sm fw-medium">
                                                {{ number_format($item->word_count) }}
                                            </span>
                                            @if($item->word_count > 1000)
                                                <i class="bi bi-star-fill text-warning d-none d-md-inline" title="High word count"></i>
                                            @endif
                                        </div>
                                    </td> -->
                                    <td class="tb-col d-none d-lg-table-cell">
                                        <div class="fs-7 text-muted">
                                            {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="tb-col">
                                        <div class="d-flex justify-content-center gap-1">
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
                                            <a href="{{ route('edit.admin.document', $item->id) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Edit Document"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil-square"></i>
                                                <span class="d-none d-xl-inline ms-1">Edit</span>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#confirmDeleteModal" 
                                                    data-document-id="{{ $item->id }}"
                                                    data-delete-url="{{ route('delete.admin.document', $item->id) }}"
                                                    title="Delete Document">
                                                <i class="bi bi-trash"></i>
                                                <span class="d-none d-xl-inline ms-1">Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="no-search-results" class="text-center py-5 d-none">
                    <div class="mb-4">
                        <i class="bi bi-file-earmark-slash display-1 text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted mb-2">No Documents Found</h5>
                    <p class="text-muted mb-0">There are no documents matching your search query.</p>
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

        @if($document->lastPage() > 1)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
            <div class="text-muted order-2 order-md-1">
                Showing {{ $document->firstItem() }} to {{ $document->lastItem() }} of {{ $document->total() }} entries
            </div>
            <nav class="order-1 order-md-2">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    <li class="page-item {{ ($document->onFirstPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $document->previousPageUrl() }}">Previous</a>
                    </li>

                    {{-- Pagination Elements --}}
                    @foreach ($document->getUrlRange(1, $document->lastPage()) as $page => $url)
                        <li class="page-item {{ ($document->currentPage() == $page) ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    {{-- Next Page Link --}}
                    <li class="page-item {{ ($document->currentPage() == $document->lastPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $document->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title" id="documentTitle">Document Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body bg-light">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-file-text text-primary"></i>
                        Document Content
                    </h6>

                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="copyContent()">
                            <i class="bi bi-copy me-1"></i>Copy
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="downloadContent()">
                            <i class="bi bi-download me-1"></i>Download TXT
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="downloadPDF()">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                        </button>
                    </div>
                </div>

                <!-- Cleaner Content Viewer -->
                <div class="document-viewer border rounded shadow-sm"
                     style="background:white; padding:20px; border-radius:10px; height:420px; overflow-y:auto;">

                    <div id="documentContent" class="document-text">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-3 text-muted">Loading document content...</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
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

<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sortModalLabel">Sort Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sortForm" method="GET" action="{{ route('admin.document') }}">
                <div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="sort" id="sortNewest" value="newest" 
                        {{ request('sort') == 'newest' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sortNewest">Show latest documents first</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="sort" id="sortOldest" value="oldest"
                        {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                        <label class="form-check-label" for="sortOldest">Show oldest documents first</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// This function listens for input in the search bar.
// It iterates through each document row and hides rows that don't match the search term.
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.document-row');
    let foundDocuments = false;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isMatch = text.includes(searchTerm);
        row.style.display = isMatch ? '' : 'none';
        if (isMatch) {
            foundDocuments = true;
        }
    });

    const documentTableContainer = document.getElementById('document-table-container');
    const noResultsMessage = document.getElementById('no-search-results');

    if (foundDocuments) {
        documentTableContainer.classList.remove('d-none');
        noResultsMessage.classList.add('d-none');
    } else {
        documentTableContainer.classList.add('d-none');
        noResultsMessage.classList.remove('d-none');
    }
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

function loadDocumentContent(id, title, userName, category, wordCount, outputData) {
    // Only update the elements that still exist
    const docTitleEl = document.getElementById('documentTitle');
    if (docTitleEl) docTitleEl.textContent = title;

    const docMetaEl = document.getElementById('documentMeta');
    if (docMetaEl) docMetaEl.textContent = `By ${userName}`;

    const contentDiv = document.getElementById('documentContent');

    // Loader first
    contentDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading...</p>
        </div>
    `;

    // Load content smoothly
    setTimeout(() => {
        contentDiv.innerHTML = `
            <div class="document-content">
                <div class="bg-white p-4 border rounded shadow-sm" 
                    style="max-height: 350px; overflow-y: auto;">

                    <div id="documentContentText"
                        style="
                            font-size: 0.95rem;
                            line-height: 1.7;
                            white-space: pre-wrap;
                            word-break: break-word;
                            font-family: 'Inter', sans-serif;
                        ">
                        ${formatDocument(outputData)}
                    </div>
                </div>
            </div>
        `;
    }, 50);
}

function formatDocument(text) {
    if (!text) return "";

    // Clean unnecessary whitespace
    text = text.trim();
    text = text.replace(/\r\n/g, "\n");

    // Remove extra blank lines, max 1
    text = text.replace(/\n{3,}/g, "\n\n");

    // Convert markdown-style headings
    text = text.replace(/^#\s?(.*)$/gm, "<h2 class='doc-h2'>$1</h2>");
    text = text.replace(/^##\s?(.*)$/gm, "<h3 class='doc-h3'>$1</h3>");
    text = text.replace(/^###\s?(.*)$/gm, "<h4 class='doc-h4'>$1</h4>");

    // Bullet points
    text = text.replace(/^\s*[-*]\s+(.*)$/gm, "<li>$1</li>");
    text = text.replace(/^\s*\d+\.\s+(.*)$/gm, "<li>$1</li>");

    // Wrap list items
    text = text.replace(/(<li>[\s\S]*?<\/li>)/g, "<ul class='doc-list'>$1</ul>");

    // Bold + italic
    text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    text = text.replace(/\*(.*?)\*/g, "<em>$1</em>");

    // Split paragraphs
    let parts = text.split(/\n{2,}/);

    // Remove empty paragraphs + trim
    parts = parts
        .map(p => p.trim())
        .filter(p => p.length > 0);

    // Wrap in <p>
    return parts
        .map(p => `<p class="doc-paragraph">${p}</p>`)
        .join("");
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
    const titleElement = document.getElementById('documentTitle'); // Correct ID
    const contentElement = document.getElementById('documentContentText');

    if (!contentElement || !titleElement) {
        showToast('No content available to download', 'error');
        return;
    }

    const title = titleElement.textContent || 'document';
    const content = contentElement.innerText || contentElement.textContent;

    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Downloading...</span>';

    try {
        const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${title.replace(/[^a-z0-9\s\-_.]/gi, '_').replace(/\s+/g, '_').toLowerCase()}.txt`;
        document.body.appendChild(a);
        a.click();
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


function downloadPDF() {
    const titleElement = document.getElementById('documentTitle');
    const contentElement = document.getElementById('documentContentText');

    if (!contentElement || !titleElement) {
        showToast('No content available to download', 'error');
        return;
    }

    const title = titleElement.textContent || 'document';
    const content = contentElement.innerText || contentElement.textContent;

    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Generating PDF...</span>';

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 15;
        const maxLineWidth = pageWidth - margin * 2;

        const lines = doc.splitTextToSize(content, maxLineWidth);
        doc.text(lines, margin, 20);

        doc.save(`${title.replace(/[^a-z0-9\s\-_.]/gi, '_').replace(/\s+/g, '_').toLowerCase()}.pdf`);
        showToast('PDF downloaded successfully!', 'success');
    } catch (error) {
        console.error('PDF download failed:', error);
        showToast('Failed to download PDF', 'error');
    }

    resetButton(btn, originalHTML);
}

</script>

<style>
.document-text p {
    margin-top: 0;
    margin-bottom: 12px;
}

.document-text p:last-child {
    margin-bottom: 0 !important;
}

.document-viewer {
    line-height: 1.6;
    font-size: 0.95rem;
}
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


<!-- MODAL Delete Confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content text-center p-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#dc3545" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
                <h5 class="fw-bold">Are you sure?</h5>
                <p class="text-muted">Do you really want to delete this document? This process cannot be undone.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteDocumentForm" method="POST">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- <script>
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const deleteUrl = button.getAttribute('data-delete-url'); 
        const form = document.getElementById('deleteDocumentForm');
        form.action = deleteUrl; 
    });
</script> -->

<script>
    // Get the delete confirmation modal element by its ID
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');

    // Add an event listener that triggers when the modal is about to be shown
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        // Get the button that opened the modal
        const button = event.relatedTarget;

        // Retrieve the delete URL from the button's data attribute
        const deleteUrl = button.getAttribute('data-delete-url'); 

        // Find the delete form inside the modal
        const form = document.getElementById('deleteDocumentForm');

        // Set the form's action attribute to the specific delete URL
        form.action = deleteUrl; 
    });
</script>



@endsection
