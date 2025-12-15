@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <!-- Page Header -->
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6">
                        Activity Log Settings
                    </h2>
                    <p class="text-muted mb-4">Automatically delete old admin activity logs to save database space</p>
                </div>
                <div class="nk-block-head-content mb-4">
                    <a href="{{ route('superadmin.admin.activities') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Activities
                    </a>
                </div>
            </div>
        </div>

        <!-- Simple Explanation Banner -->
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-start">
                <i class="bi bi-lightbulb-fill fs-3 text-primary me-3"></i>
                <div>
                    <h5 class="fw-bold mb-2">What does this page do?</h5>
                    <p class="mb-2">This page helps you automatically clean up old admin activity logs to keep your database organized.</p>
                    <p class="mb-0"><strong>Example:</strong> If you set "30 days", any activity logs older than 30 days will be automatically deleted every night at midnight.</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards with Simple Explanations -->
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-primary-subtle">
                                    <i class="bi bi-database text-primary fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Total Logs</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($totalLogs) }}</h3>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            All activity logs currently in your database
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-warning-subtle">
                                    <i class="bi bi-trash text-warning fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Will Be Deleted</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($logsToDelete) }}</h3>
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Logs older than {{ $retentionDays }} days that will be removed
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <div class="icon-circle bg-info-subtle">
                                    <i class="bi bi-calendar-range text-info fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="text-muted mb-1">Date Range</h6>
                                @if($oldestLog && $newestLog)
                                    <div class="small">
                                        <strong>From:</strong> {{ $oldestLog->created_at->format('M d, Y') }}<br>
                                        <strong>To:</strong> {{ $newestLog->created_at->format('M d, Y') }}
                                    </div>
                                @else
                                    <p class="mb-0 text-muted small">No logs available</p>
                                @endif
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            The oldest and newest log dates in your database
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- OPTION 1: Automatic Cleanup (Recommended) -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold text-white">
                            Option 1: Automatic Cleanup (Set and Forget)
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Step-by-Step Guide -->
                        <div class="alert alert-success border-0 mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-question-circle me-2"></i>
                                How to Use (2 Simple Steps):
                            </h6>
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">1. Set how many days to keep logs (e.g., 30 days)</li>
                                <li class="mb-0">2. Click "Save Settings" - Done! System will clean up automatically every night</li>
                            </ol>
                        </div>

                        <form action="{{ route('superadmin.activity.settings.update') }}" method="POST">
                            @csrf
                            
                            <!-- Step 1: Choose Days -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <!-- <span class="badge bg-primary me-2">Step 1</span> -->
                                    Keep Logs For How Many Days?
                                </label>
                                <input 
                                    type="number" 
                                    name="retention_days" 
                                    class="form-control form-control-lg" 
                                    value="{{ $retentionDays }}"
                                    min="1"
                                    max="365"
                                    required
                                >
                                <div class="form-text">
                                    <!-- <i class="bi bi-lightbulb text-warning me-1"></i> -->
                                    <strong>Example:</strong> If you choose "30", logs older than 30 days will be deleted
                                </div>
                            </div>

                            <!-- Quick Selection Buttons -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Quick Select:</label>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary" onclick="setRetention(7)">
                                        <i class="bi bi-calendar-week me-2"></i>7 Days (Keep 1 Week)
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setRetention(30)">
                                        <i class="bi bi-calendar-month me-2"></i>30 Days (Keep 1 Month) - Recommended
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setRetention(90)">
                                        <i class="bi bi-calendar3 me-2"></i>90 Days (Keep 3 Months)
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Save (removed toggle) -->
                            <div class="alert alert-info border-0 mb-4">
                                <!-- <i class="bi bi-robot me-2"></i> -->
                                <strong>How it works:</strong> Once you save the settings, old logs will be automatically deleted every night at midnight based on the retention period you set.
                            </div>

                            <!-- Important Note -->
                            <!-- <div class="alert alert-warning border-0">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> Make sure your server's cron job is running. Ask your developer if you're not sure.
                            </div> -->

                            <!-- Step 2: Save -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-save me-2"></i>
                                <!-- <span class="badge bg-white text-primary me-2">Step 2</span> -->
                                Save Settings & Enable Auto-Cleanup
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- OPTION 2: Manual Cleanup -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0 fw-bold text-white">
                            Option 2: Manual Cleanup (Clean Now)
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- When to Use This -->
                        <div class="alert alert-info border-0 mb-4">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-question-circle me-2"></i>
                                When to Use This?
                            </h6>
                            <p class="mb-0">Use this if you want to delete old logs RIGHT NOW instead of waiting for automatic cleanup.</p>
                        </div>

                        <p class="text-muted mb-4">
                            <strong>What happens:</strong> Click the button below and old logs will be deleted immediately (not automatically every night).
                        </p>

                        <form action="{{ route('superadmin.activity.manual.cleanup') }}" method="POST" id="manualCleanupForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-x me-2"></i>
                                    Delete Logs Older Than How Many Days?
                                </label>
                                <input 
                                    type="number" 
                                    name="days" 
                                    class="form-control form-control-lg" 
                                    value="{{ $retentionDays }}"
                                    min="1"
                                    max="365"
                                    required
                                    id="manualDays"
                                >
                                <div class="form-text">
                                    <!-- <i class="bi bi-lightbulb text-warning me-1"></i> -->
                                    <strong>Example:</strong> If you type "30", it will delete logs older than 30 days
                                </div>
                            </div>

                            <!-- Preview Box -->
                            <div class="preview-box mb-4">
                                <h6 class="fw-semibold mb-2">
                                    <i class="bi bi-eye me-2"></i>Preview:
                                </h6>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span id="previewText">
                                        This will delete <strong>{{ number_format($logsToDelete) }}</strong> log(s)
                                    </span>
                                </div>
                            </div>

                            <!-- Warning Box -->
                            <div class="alert alert-danger border-0 mb-4">
                                <!-- <i class="bi bi-shield-exclamation me-2"></i> -->
                                <strong>Warning:</strong> Once deleted, you cannot recover these logs. Make sure to export important data first!
                            </div>

                            <button 
                                type="button" 
                                class="btn btn-danger btn-lg w-100" 
                                onclick="confirmCleanup()"
                            >
                                <i class="bi bi-trash me-2"></i>Delete Old Logs Now
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <!-- <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-question-circle-fill text-info me-2"></i>
                            Need Help?
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Q: Which option should I use?</strong>
                            <p class="text-muted mb-0 small">Use <strong>Option 1 (Automatic)</strong> - it's easier and works automatically.</p>
                        </div>
                        <div class="mb-3">
                            <strong>Q: What's the recommended setting?</strong>
                            <p class="text-muted mb-0 small">Keep logs for <strong>30 days</strong> - this balances storage and history.</p>
                        </div>
                        <div class="mb-0">
                            <strong>Q: Can I undo after deletion?</strong>
                            <p class="text-muted mb-0 small"><strong>No</strong> - deleted logs are gone forever. Export important logs first!</p>
                        </div>
                    </div>
                </div> -->
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
                    OK, Got It!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Manual Cleanup -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-5 pt-0">
                <!-- Warning Icon -->
                <div class="warning-icon-large mb-4">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                
                <!-- Title -->
                <h3 class="fw-bold mb-3 text-danger">Are You Sure?</h3>
                
                <!-- Message -->
                <div class="alert alert-warning border-0 mb-4">
                    <p class="mb-2"><strong>You are about to permanently delete:</strong></p>
                    <h4 class="text-danger mb-2" id="confirmLogsCount">0 log(s)</h4>
                    <p class="mb-0 small">Logs older than <strong id="confirmDays">0</strong> days</p>
                </div>
                
                <!-- Warning List -->
                <div class="text-start mb-4">
                    <div class="alert alert-danger border-0">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-shield-exclamation me-2"></i>Important:
                        </h6>
                        <ul class="mb-0 ps-3 small">
                            <li>This action <strong>CANNOT be undone</strong></li>
                            <li>Deleted logs are <strong>gone forever</strong></li>
                            <li>Consider exporting logs before deletion</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger btn-lg" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-2"></i>
                        Yes, Delete Permanently
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>
                        No, Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Final Confirmation Modal -->
<div class="modal fade" id="finalConfirmationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg border-danger" style="border: 3px solid #dc3545 !important;">
            <div class="modal-body text-center p-4">
                <!-- Pulsing Warning Icon -->
                <div class="danger-icon-pulse mb-3">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                </div>
                
                <h4 class="fw-bold text-danger mb-3">FINAL WARNING!</h4>
                <p class="mb-4">This is your last chance to cancel. Are you absolutely sure?</p>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-danger" id="finalConfirmBtn">
                        <i class="bi bi-check-circle me-2"></i>
                        Yes, I'm Sure
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-box {
    background: #fff9e6;
    border: 2px dashed #ffc107;
    border-radius: 10px;
    padding: 1rem;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-input {
    width: 3rem;
    height: 1.5rem;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

.modal-content {
    border-radius: 20px;
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

@keyframes scaleIn {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-10px);
    }
    75% {
        transform: translateX(10px);
    }
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

.danger-icon-pulse {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: pulse 1.5s infinite;
    margin: 0 auto;
}

.danger-icon-pulse i {
    font-size: 40px;
    color: white;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
}
</style>

<script>
// Show modal on page load if there's a message
@if(session('message'))
document.addEventListener('DOMContentLoaded', function() {
    showModal(
        '{{ session("alert-type") }}',
        '{{ session("message") }}'
    );
});
@endif

// Function to show modal
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

// Set retention days for both automatic and manual
function setRetention(days) {
    document.querySelector('input[name="retention_days"]').value = days;
    document.querySelector('input[name="days"]').value = days;
    updatePreview();
}

// Update preview when manual days change
function updatePreview() {
    const days = document.getElementById('manualDays')?.value;
    if (days) {
        document.getElementById('previewText').innerHTML = 
            `This will delete logs older than <strong>${days}</strong> days`;
    }
}

// Confirm before manual cleanup - Now using beautiful modal
function confirmCleanup() {
    const days = document.getElementById('manualDays').value;
    const logsCount = {{ $logsToDelete }};
    
    // Update modal content
    document.getElementById('confirmDays').textContent = days;
    document.getElementById('confirmLogsCount').textContent = logsCount.toLocaleString() + ' log(s)';
    
    // Show first confirmation modal
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    confirmModal.show();
}

// Handle first confirmation button click
document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
    // Hide first modal
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmationModal'));
    confirmModal.hide();
    
    // Show final confirmation modal after a short delay
    setTimeout(function() {
        const finalModal = new bootstrap.Modal(document.getElementById('finalConfirmationModal'));
        finalModal.show();
    }, 300);
});

// Handle final confirmation button click
document.getElementById('finalConfirmBtn')?.addEventListener('click', function() {
    // Hide modal
    const finalModal = bootstrap.Modal.getInstance(document.getElementById('finalConfirmationModal'));
    finalModal.hide();
    
    // Submit form
    setTimeout(function() {
        document.getElementById('manualCleanupForm').submit();
    }, 300);
});

// Update preview when manual days input changes
document.getElementById('manualDays')?.addEventListener('input', updatePreview);
</script>
@endsection