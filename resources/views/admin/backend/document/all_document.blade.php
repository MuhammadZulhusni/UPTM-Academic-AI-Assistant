@extends('admin.dashboard')
@section('admin') 

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
    
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap gap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6 mb-1">My Generated Documents</h2>
                    <p class="text-muted mb-1">View and manage all AI content you've created.</p>
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
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortModal">
                    <i class="bi bi-funnel"></i> <span class="d-none d-md-inline ms-1">Sort</span>
                </button>
            </div>
        </div>
        
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(count($document) > 0)
                <div id="document-table-container">

                    {{-- DESKTOP TABLE VIEW --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-middle table-hover mb-0" id="documentsTableDesktop">
                            <thead class="table-light">
                                <tr>
                                    <th class="tb-col" style="width: 60px;">
                                        <div class="fs-13px text-base fw-semibold">Sl</div>
                                    </th>
                                    <th class="tb-col">
                                        <div class="fs-13px text-base fw-semibold">Document</div>
                                    </th>
                                    <th class="tb-col">
                                        <div class="fs-13px text-base fw-semibold">Created (Date/Time)</div>
                                    </th>
                                    <th class="tb-col text-center">
                                        <div class="fs-13px text-base fw-semibold">Action</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($document as $key => $item)  
                                <tr class="document-row">
                                    <td class="tb-col">
                                        <div class="caption-text fw-medium">{{ $document->firstItem() + $loop->index }}</div>
                                    </td>
                                    <td class="tb-col">
                                        <div class="fs-6 fw-medium text-dark text-truncate" style="max-width: 350px;">{{ $item->template->title }}</div>
                                    </td>
                                    <td class="tb-col">
                                        <div class="fs-7 text-muted">
                                            {{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}
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
                                                    data-output="{{ base64_encode($item->output) }}"
                                                    onclick="loadDocumentContentFromData(this)">
                                                <i class="bi bi-eye"></i>
                                                <span class="d-none d-xl-inline ms-1">View</span>
                                            </button>
                                            <a href="{{ route('edit.admin.document', $item->id) }}" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="Edit Document">
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

                    {{-- MOBILE CARD VIEW --}}
                    <div class="d-md-none p-3 pt-0">
                        @foreach ($document as $key => $item)
                        <div class="card document-row shadow-sm mb-3 border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 text-dark fw-bold text-truncate" title="{{ $item->template->title }}">
                                            {{ $document->firstItem() + $loop->index }}. {{ $item->template->title }}
                                        </h6>
                                    </div>
                                    <span class="badge bg-light text-muted fw-normal ms-3">
                                        {{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-outline-info btn-sm"
                                            title="View Document"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewDocumentModal"
                                            data-document-id="{{ $item->id }}"
                                            data-document-title="{{ $item->template->title }}"
                                            data-output="{{ base64_encode($item->output) }}"
                                            onclick="loadDocumentContentFromData(this)">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <a href="{{ route('edit.user.document', $item->id) }}"
                                       class="btn btn-outline-primary btn-sm"
                                       title="Edit Document">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDeleteModal"
                                            data-document-id="{{ $item->id }}"
                                            data-delete-url="{{ route('delete.user.document', $item->id) }}"
                                            title="Delete Document">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
                    <li class="page-item {{ ($document->onFirstPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $document->previousPageUrl() }}">Previous</a>
                    </li>

                    @foreach ($document->getUrlRange(1, $document->lastPage()) as $page => $url)
                        <li class="page-item {{ ($document->currentPage() == $page) ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    <li class="page-item {{ ($document->currentPage() == $document->lastPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $document->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

{{-- VIEW DOCUMENT MODAL --}}
<div class="modal fade" id="viewDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title" id="documentTitle">Document Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-file-text text-primary"></i>
                        Document Content
                    </h6>

                    <div class="btn-group btn-group-sm">
                        <!-- <button type="button" class="btn btn-outline-secondary" onclick="copyContent()">
                            <i class="bi bi-copy me-1"></i>Copy
                        </button> -->
                        <button type="button" class="btn btn-outline-danger" onclick="downloadPDF()">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                        </button>
                    </div>
                </div>

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

            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- SORT MODAL --}}
<div class="modal fade" id="sortModal" tabindex="-1" aria-labelledby="sortModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header border-0 pb-0 pt-3 px-4">
                <h5 class="modal-title fs-5 text-secondary" id="sortModalLabel">Sort Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="sortForm" method="GET" action="{{ route('admin.document') }}">
                <div class="modal-body px-4 py-3">
                    <p class="text-muted small mb-3">Choose the order for viewing the document list.</p>

                    <div class="d-grid gap-2">
                        <div class="form-check p-0 mb-2">
                            <input class="form-check-input visually-hidden" type="radio" name="sort" id="sortNewest" value="newest"
                            {{ request('sort') == 'newest' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary w-100 text-start rounded-2 px-3 py-2 sort-option-btn" for="sortNewest">
                                <i class="bi bi-clock-fill me-2"></i> Show latest documents first
                            </label>
                        </div>
                        <div class="form-check p-0">
                            <input class="form-check-input visually-hidden" type="radio" name="sort" id="sortOldest" value="oldest"
                            {{ request('sort') == 'oldest' ? 'checked' : '' }}>
                            <label class="btn btn-outline-secondary w-100 text-start rounded-2 px-3 py-2 sort-option-btn" for="sortOldest">
                                <i class="bi bi-clock me-2"></i> Show oldest documents first
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end bg-light border-0 rounded-bottom-3 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-2">Apply Sort</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
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
                <form id="deleteDocumentForm" method="POST">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Document formatting classes */
.doc-h2 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #2c3e50;
}

.doc-h3 {
    font-size: 1.25rem;
    font-weight: bold;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    color: #34495e;
}

.doc-h4 {
    font-size: 1.1rem;
    font-weight: bold;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    color: #34495e;
}

.doc-list {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
}

.doc-list li {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.doc-paragraph {
    margin-bottom: 1rem;
    line-height: 1.7;
}

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

/* Sort modal styles */
#sortModal input[type="radio"]:checked + .sort-option-btn {
    background-color: var(--bs-primary-bg-subtle);
    border-color: var(--bs-primary) !important;
    color: var(--bs-primary) !important;
}

#sortModal input[type="radio"]:checked + .sort-option-btn i {
    color: var(--bs-primary) !important;
}

#sortModal .sort-option-btn:hover {
    background-color: var(--bs-light);
}

/* Utility classes */
.min-width-0 {
    min-width: 0;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Toast styles */
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

/* Responsive styles */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 10px;
    }
    
    .modal-xl {
        max-width: calc(100vw - 20px);
    }
    
    .custom-toast {
        right: 10px;
        left: 10px;
        max-width: none;
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

<script>
// ==========================================
// DOCUMENT CONTENT LOADING
// ==========================================
function loadDocumentContentFromData(button) {
    const id = button.getAttribute('data-document-id');
    const title = button.getAttribute('data-document-title');
    const outputData = atob(button.getAttribute('data-output'));
    
    loadDocumentContent(id, title, outputData);
}

function loadDocumentContent(id, title, outputData) {
    const docTitleEl = document.getElementById('documentTitle');
    if (docTitleEl) docTitleEl.textContent = title;

    const contentDiv = document.getElementById('documentContent');

    contentDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Loading...</p>
        </div>
    `;

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

    text = text.trim().replace(/\r\n/g, "\n").replace(/\n{3,}/g, "\n\n");

    // Convert markdown headings
    text = text.replace(/^#\s?(.*)$/gm, "<h2 class='doc-h2'>$1</h2>");
    text = text.replace(/^##\s?(.*)$/gm, "<h3 class='doc-h3'>$1</h3>");
    text = text.replace(/^###\s?(.*)$/gm, "<h4 class='doc-h4'>$1</h4>");

    // Convert lists
    text = text.replace(/^\s*[-*]\s+(.*)$/gm, "<li>$1</li>");
    text = text.replace(/^\s*\d+\.\s+(.*)$/gm, "<li>$1</li>");
    text = text.replace(/(<li>[\s\S]*?<\/li>)/g, "<ul class='doc-list'>$1</ul>");

    // Bold and italic
    text = text.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    text = text.replace(/\*(.*?)\*/g, "<em>$1</em>");

    // Create paragraphs
    let parts = text.split(/\n{2,}/)
        .map(p => p.trim())
        .filter(p => p.length > 0);

    return parts.map(p => `<p class="doc-paragraph">${p}</p>`).join("");
}

// ==========================================
// COPY CONTENT FUNCTION - IMPROVED
// ==========================================
// ==========================================
// COPY CONTENT FUNCTION - IMPROVED
// ==========================================
function copyContent() {
    const contentElement = document.getElementById('documentContentText');
    if (!contentElement) {
        showToast('No content available to copy', 'error');
        return;
    }
    
    // Get clean text content by creating a temporary div
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = contentElement.innerHTML;
    
    // Extract text with proper formatting
    let textContent = '';
    
    // Process each child element
    Array.from(tempDiv.children).forEach((element, index) => {
        if (index > 0) textContent += '\n';
        
        if (element.tagName === 'H2') {
            textContent += '\n' + element.textContent.trim() + '\n';
        } else if (element.tagName === 'H3') {
            textContent += '\n' + element.textContent.trim() + '\n';
        } else if (element.tagName === 'H4') {
            textContent += '\n' + element.textContent.trim() + '\n';
        } else if (element.tagName === 'UL') {
            const items = element.querySelectorAll('li');
            items.forEach(li => {
                textContent += '• ' + li.textContent.trim() + '\n';
            });
        } else if (element.tagName === 'P') {
            textContent += element.textContent.trim() + '\n';
        } else {
            textContent += element.textContent.trim() + '\n';
        }
    });
    
    // Fallback to simple text extraction if no formatted content
    if (!textContent.trim()) {
        textContent = tempDiv.textContent || tempDiv.innerText || '';
    }
    
    // Clean up extra whitespace
    textContent = textContent.trim();
    
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Copying...</span>';
    
    // Modern Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textContent)
            .then(() => {
                showToast('Content copied to clipboard successfully!', 'success');
            })
            .catch(err => {
                console.error('Failed to copy:', err);
                fallbackCopy(textContent);
            })
            .finally(() => {
                resetButton(btn, originalHTML);
            });
    } else {
        // Fallback method
        fallbackCopy(textContent);
        resetButton(btn, originalHTML);
    }
}

// Fallback copy method for older browsers
function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    textArea.style.top = '0';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showToast('Content copied to clipboard!', 'success');
        } else {
            showToast('Failed to copy content', 'error');
        }
    } catch (err) {
        console.error('Copy error:', err);
        showToast('Copy not supported in this browser', 'error');
    }
    
    document.body.removeChild(textArea);
}

// ==========================================
// DOWNLOAD PDF FUNCTION
// ==========================================
function downloadPDF() {
    const titleElement = document.getElementById('documentTitle');
    const contentElement = document.getElementById('documentContentText');

    if (!contentElement || !titleElement) {
        showToast('No content available to download', 'error');
        return;
    }

    const title = titleElement.textContent || 'document';
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span><span class="d-none d-sm-inline">Generating PDF...</span>';

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const margin = 25;
        const maxWidth = pageWidth - (margin * 2);
        let yPosition = margin;

        const normalLineHeight = 6;
        const paragraphGap = 6;
        const h2Gap = 12;
        const h2BottomGap = 8;
        const h3Gap = 10;
        const h3BottomGap = 6;
        const h4Gap = 8;
        const h4BottomGap = 5;
        const listItemGap = 3;
        const listBottomGap = 6;

        // TITLE PAGE
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        
        const titleLines = doc.splitTextToSize(title, maxWidth - 20);
        const titleStartY = pageHeight / 3;
        
        titleLines.forEach((line, index) => {
            const lineWidth = doc.getTextWidth(line);
            const xPos = (pageWidth - lineWidth) / 2;
            doc.text(line, xPos, titleStartY + (index * 10));
        });
        
        const dividerY = titleStartY + (titleLines.length * 10) + 12;
        doc.setLineWidth(0.5);
        doc.line(margin + 20, dividerY, pageWidth - margin - 20, dividerY);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(11);
        const generatedDate = new Date().toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const dateText = `Generated on ${generatedDate}`;
        const dateWidth = doc.getTextWidth(dateText);
        doc.text(dateText, (pageWidth - dateWidth) / 2, dividerY + 15);
        
        doc.setFontSize(10);
        const systemText = 'UPTM Academic AI Assistant';
        const systemWidth = doc.getTextWidth(systemText);
        doc.text(systemText, (pageWidth - systemWidth) / 2, pageHeight - margin - 10);
        
        doc.addPage();
        yPosition = margin;
        
        // CONTENT PAGES
        const contentElements = contentElement.querySelectorAll('.doc-paragraph, .doc-h2, .doc-h3, .doc-h4, .doc-list');
        
        if (contentElements.length === 0) {
            const allText = contentElement.innerText || contentElement.textContent || '';
            if (!allText.trim()) {
                showToast('No content to export', 'error');
                resetButton(btn, originalHTML);
                return;
            }
            
            const paragraphs = allText.split('\n\n').filter(p => p.trim());
            doc.setFont("helvetica", "normal");
            doc.setFontSize(11);
            
            paragraphs.forEach((para, index) => {
                const paraText = para.trim();
                if (index > 0) yPosition += paragraphGap;
                
                const lines = doc.splitTextToSize(paraText, maxWidth);
                const estimatedHeight = lines.length * normalLineHeight;
                
                if (yPosition + estimatedHeight > pageHeight - margin) {
                    doc.addPage();
                    yPosition = margin;
                }
                
                doc.text(lines, margin, yPosition, { align: 'left', maxWidth: maxWidth });
                yPosition += lines.length * normalLineHeight;
            });
        } else {
            let isFirstElement = true;
            contentElements.forEach((element) => {
                const text = element.textContent.trim();
                if (!text) return;
                
                const isH2 = element.classList.contains('doc-h2');
                const isH3 = element.classList.contains('doc-h3');
                const isH4 = element.classList.contains('doc-h4');
                const isList = element.classList.contains('doc-list');
                const isParagraph = element.classList.contains('doc-paragraph');
                
                // H2 Heading
                if (isH2) {
                    if (!isFirstElement && yPosition > margin) yPosition += h2Gap;
                    if (yPosition > pageHeight - margin - 40) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(14);
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    yPosition += headingLines.length * 7 + h2BottomGap;
                    isFirstElement = false;
                }
                // H3 Heading
                else if (isH3) {
                    if (!isFirstElement) yPosition += h3Gap;
                    if (yPosition > pageHeight - margin - 35) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(12);
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    yPosition += headingLines.length * 6.5 + h3BottomGap;
                    isFirstElement = false;
                }
                // H4 Heading
                else if (isH4) {
                    if (!isFirstElement) yPosition += h4Gap;
                    if (yPosition > pageHeight - margin - 30) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.setFont("helvetica", "bold");
                    doc.setFontSize(11);
                    const headingLines = doc.splitTextToSize(text, maxWidth);
                    doc.text(headingLines, margin, yPosition);
                    yPosition += headingLines.length * 6 + h4BottomGap;
                    isFirstElement = false;
                }
                // List Items
                else if (isList) {
                    const listItems = element.querySelectorAll('li');
                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(11);
                    
                    const bulletX = margin + 5;
                    const textX = margin + 12;
                    const listWidth = maxWidth - 12;
                    
                    listItems.forEach((li, idx) => {
                        const itemText = li.textContent.trim();
                        const itemLines = doc.splitTextToSize(itemText, listWidth);
                        const estimatedHeight = itemLines.length * normalLineHeight + listItemGap;
                        
                        if (yPosition + estimatedHeight > pageHeight - margin) {
                            doc.addPage();
                            yPosition = margin;
                        }
                        
                        doc.circle(bulletX, yPosition - 2, 0.8, 'F');
                        doc.text(itemLines, textX, yPosition, { align: 'left', maxWidth: listWidth });
                        yPosition += itemLines.length * normalLineHeight;
                        
                        if (idx < listItems.length - 1) {
                            yPosition += listItemGap;
                        }
                    });
                    
                    yPosition += listBottomGap;
                    isFirstElement = false;
                }
                // Regular Paragraph
                else if (isParagraph) {
                    if (!isFirstElement) yPosition += paragraphGap;
                    
                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(11);
                    const paraLines = doc.splitTextToSize(text, maxWidth);
                    const estimatedHeight = paraLines.length * normalLineHeight;
                    
                    if (yPosition + estimatedHeight > pageHeight - margin) {
                        doc.addPage();
                        yPosition = margin;
                    }
                    
                    doc.text(paraLines, margin, yPosition, { align: 'left', maxWidth: maxWidth });
                    yPosition += paraLines.length * normalLineHeight;
                    isFirstElement = false;
                }
            });
        }
        
        // PAGE NUMBERS AND HEADERS
        const totalPages = doc.internal.getNumberOfPages();
        
        for (let i = 1; i <= totalPages; i++) {
            doc.setPage(i);
            if (i === 1) continue;
            
            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);
            doc.setTextColor(100);
            
            const shortTitle = title.length > 80 ? title.substring(0, 77) + '...' : title;
            doc.text(shortTitle, margin, 15);
            
            doc.setLineWidth(0.3);
            doc.line(margin, 17, pageWidth - margin, 17);
            
            const pageNum = `${i - 1}`;
            const pageNumWidth = doc.getTextWidth(pageNum);
            doc.text(pageNum, (pageWidth - pageNumWidth) / 2, pageHeight - 15);
        }
        
        doc.setTextColor(0);
        
        const sanitizedFileName = title.replace(/[^a-z0-9\s\-_.]/gi, '_').replace(/\s+/g, '_').toLowerCase();
        doc.save(`${sanitizedFileName}.pdf`);
        
        showToast('PDF downloaded successfully!', 'success');
        
    } catch (error) {
        console.error('PDF generation error:', error);
        showToast('Failed to generate PDF: ' + error.message, 'error');
    }

    resetButton(btn, originalHTML);
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================
function resetButton(btn, originalHTML) {
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }, 800);
}

function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.custom-toast');
    if (existingToast) existingToast.remove();
    
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
    
    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }, 3000);
}

// ==========================================
// SEARCH FUNCTIONALITY
// ==========================================
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.document-row');
    let foundDocuments = false;

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isMatch = text.includes(searchTerm);
        row.style.display = isMatch ? '' : 'none';
        if (isMatch) foundDocuments = true;
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

// ==========================================
// BOOTSTRAP TOOLTIPS INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ==========================================
// DELETE MODAL HANDLER
// ==========================================
const confirmDeleteModal = document.getElementById('confirmDeleteModal');
confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const deleteUrl = button.getAttribute('data-delete-url');
    const form = document.getElementById('deleteDocumentForm');
    form.action = deleteUrl;
});
</script>

@endsection