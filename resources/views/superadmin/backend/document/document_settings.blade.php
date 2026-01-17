@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Document Cleanup</h2>
                    <p class="text-muted mb-4">Delete old user-generated documents to save database space</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary-subtle">
                                <i class="bi bi-file-earmark-text text-primary"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="text-muted mb-1 small">Total Documents</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalDocuments) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info-subtle">
                                <i class="bi bi-calendar-range text-info"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="text-muted mb-1 small">Date Range</p>
                                @if($oldestDocument && $newestDocument)
                                    <div class="small fw-semibold">
                                        <div>{{ $oldestDocument->created_at->format('M d, Y') }}</div>
                                        <div class="small fw-semibold">to {{ $newestDocument->created_at->format('M d, Y') }}</div>
                                    </div>
                                @else
                                    <p class="mb-0 text-muted small">No documents</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success-subtle">
                                <i class="bi bi-people text-success"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <p class="text-muted mb-2 small">Documents by Role</p>
                                <div class="role-stats">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small text-muted">Students</span>
                                        <span class="small fw-semibold">{{ number_format($studentDocuments) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small text-muted">Lecturers</span>
                                        <span class="small fw-semibold">{{ number_format($lecturerDocuments) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="small text-muted">Admins</span>
                                        <span class="small fw-semibold">{{ number_format($adminDocuments) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Cleanup Section -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card border-0 shadow cleanup-card">
                    <div class="card-header cleanup-header">
                        <div class="d-flex align-items-center">
                            <div class="header-icon">
                                <i class="bi bi-trash"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0 fw-bold text-white">Delete Old Documents</h5>
                                <small class="text-white-50">Cleanup tool</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('superadmin.document.manual.cleanup') }}" method="POST" id="manualDocCleanupForm">
                            @csrf
                            
                            <div class="cleanup-input-group mb-4">
                                <label class="form-label fw-semibold mb-3">
                                    <i class="bi bi-calendar-x me-2 text-danger"></i>Delete documents older than:
                                </label>
                                <div class="input-wrapper">
                                    <input 
                                        type="number" 
                                        name="days" 
                                        class="form-control form-control-lg" 
                                        value="90"
                                        min="7"
                                        max="365"
                                        required
                                        id="manualDocDays"
                                    >
                                    <span class="input-suffix me-3">days</span>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Example: Enter 90 to delete documents older than 90 days
                                </div>
                            </div>

                            <!-- Quick Selection Buttons -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-3">
                                   </i>Quick select:
                                </label>
                                <div class="quick-select-grid">
                                    <button type="button" class="quick-btn" onclick="setDocDays(30)">
                                        <div class="quick-btn-value">30</div>
                                        <div class="quick-btn-label">Days</div>
                                    </button>
                                    <button type="button" class="quick-btn active" onclick="setDocDays(90)">
                                        <div class="quick-btn-value">90</div>
                                        <div class="quick-btn-label">Days</div>
                                    </button>
                                    <button type="button" class="quick-btn" onclick="setDocDays(180)">
                                        <div class="quick-btn-value">180</div>
                                        <div class="quick-btn-label">Days</div>
                                    </button>
                                    <button type="button" class="quick-btn" onclick="setDocDays(365)">
                                        <div class="quick-btn-value">365</div>
                                        <div class="quick-btn-label">Days</div>
                                    </button>
                                </div>
                            </div>

                            <div class="warning-box mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="warning-icon-sm">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="fw-bold mb-1">Important Warning</h6>
                                        <p class="mb-0 small">Deleted documents cannot be recovered. This action is permanent and will affect all users.</p>
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="button" 
                                class="btn btn-danger btn-lg w-100 delete-btn" 
                                onclick="confirmDocCleanup()"
                            >
                                <i class="bi bi-trash me-2"></i>Delete Documents Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-5">
                <div id="modalIcon" class="mb-4"></div>
                <h4 id="modalTitle" class="fw-bold mb-3"></h4>
                <p id="modalMessage" class="text-muted mb-4"></p>
                <button type="button" class="btn btn-primary px-5" data-bs-dismiss="modal">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-5 pt-0">
                <div class="warning-icon-large mb-4">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                
                <h3 class="fw-bold mb-3 text-danger">Are You Sure?</h3>
                
                <div class="alert alert-warning border-0 mb-4">
                    <p class="mb-2"><strong>You are about to permanently delete:</strong></p>
                    <p class="mb-0">Documents older than <strong id="confirmDocDays">0</strong> days</p>
                </div>
                
                <div class="text-start mb-4">
                    <div class="alert alert-danger border-0">
                        <ul class="mb-0 ps-3 small">
                            <li>This action cannot be undone</li>
                            <li>Users will lose their content history</li>
                            <li>Deleted documents are gone forever</li>
                        </ul>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger btn-lg" id="confirmDocDeleteBtn">
                        <i class="bi bi-trash me-2"></i>Yes, Delete Permanently
                    </button>
                    <!-- <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button> -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Stat Cards */
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon i {
    font-size: 24px;
}

.role-stats {
    font-size: 0.875rem;
}

/* Cleanup Card */
.cleanup-card {
    border-radius: 16px;
    overflow: hidden;
}

.cleanup-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 1.5rem;
    border: none;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-icon i {
    font-size: 24px;
    color: white;
}

/* Input Styling */
.cleanup-input-group {
    position: relative;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper input {
    padding-right: 70px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1.25rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.input-wrapper input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.1);
}

.input-suffix {
    position: absolute;
    right: 20px;
    font-weight: 600;
    color: #6c757d;
    pointer-events: none;
}

/* Quick Select Grid */
.quick-select-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.quick-btn {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.quick-btn:hover {
    border-color: #dc3545;
    background: #fff5f5;
    transform: translateY(-2px);
}

.quick-btn.active {
    border-color: #dc3545;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.quick-btn-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #212529;
}

.quick-btn.active .quick-btn-value {
    color: white;
}

.quick-btn-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.quick-btn.active .quick-btn-label {
    color: rgba(255, 255, 255, 0.9);
}

/* Warning Box */
.warning-box {
    background: linear-gradient(135deg, #fff3cd 0%, #fff8e1 100%);
    border: 2px solid #ffc107;
    border-radius: 12px;
    padding: 1rem 1.25rem;
}

.warning-icon-sm {
    width: 36px;
    height: 36px;
    background: #ffc107;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.warning-icon-sm i {
    color: white;
    font-size: 18px;
}

/* Delete Button */
.delete-btn {
    border-radius: 12px;
    padding: 1rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.delete-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

/* Modal Styling */
.modal-content {
    border-radius: 20px;
    border: none;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
}

.success-icon i {
    font-size: 40px;
    color: white;
}

.error-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: shake 0.5s ease-out;
}

.error-icon i {
    font-size: 40px;
    color: white;
}

.info-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
}

.info-icon i {
    font-size: 40px;
    color: white;
}

.warning-icon-large {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

.warning-icon-large i {
    font-size: 50px;
    color: white;
}

/* Animations */
@keyframes scaleIn {
    0% { transform: scale(0); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(251, 191, 36, 0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .quick-select-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
    }
    
    .stat-icon i {
        font-size: 20px;
    }
}
</style>

<script>
@if(session('message'))
document.addEventListener('DOMContentLoaded', function() {
    showModal(
        '{{ session("alert-type") }}',
        '{{ session("message") }}'
    );
});
@endif

function showModal(type, message) {
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    
    if (type === 'success') {
        modalIcon.innerHTML = '<div class="success-icon"><i class="bi bi-check-lg"></i></div>';
        modalTitle.textContent = 'Success!';
        modalTitle.className = 'fw-bold mb-3 text-success';
    } else if (type === 'error') {
        modalIcon.innerHTML = '<div class="error-icon"><i class="bi bi-x-lg"></i></div>';
        modalTitle.textContent = 'Error!';
        modalTitle.className = 'fw-bold mb-3 text-danger';
    } else {
        modalIcon.innerHTML = '<div class="info-icon"><i class="bi bi-info-lg"></i></div>';
        modalTitle.textContent = 'Information';
        modalTitle.className = 'fw-bold mb-3 text-info';
    }
    
    modalMessage.textContent = message;
    modal.show();
}

function setDocDays(days) {
    document.getElementById('manualDocDays').value = days;
    
    // Update active state
    document.querySelectorAll('.quick-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.quick-btn').classList.add('active');
}

function confirmDocCleanup() {
    const days = document.getElementById('manualDocDays').value;
    document.getElementById('confirmDocDays').textContent = days;
    
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmationDocModal'));
    confirmModal.show();
}

document.getElementById('confirmDocDeleteBtn')?.addEventListener('click', function() {
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationDocModal'));
    confirmModal.hide();
    
    setTimeout(function() {
        document.getElementById('manualDocCleanupForm').submit();
    }, 300);
});
</script>
@endsection