@extends('client.client_dashboard')

@section('client')
{{-- External Libraries --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >


<div class="nk-content-inner">
    <div class="nk-content-body">
        
        <div class="nk-block-head nk-page-head mb-5 text-center">
            <div class="nk-block-head-content">
                <h2 class="display-6 fw-bold mb-1">Security Settings</h2>
                <p class="text-muted">Keep your account safe with a quick password update.</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-lg border-0 rounded-4 minimal-password-card">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="card-title text-center mb-4 pb-2 border-bottom">Update Credentials</h4>

                        <form id="passwordForm" action="{{ route('user.password.update') }}" method="post">
                            @csrf
                            <div class="row g-4">
                                
                                <div class="col-12">
                                    <label for="old_password" class="form-label text-muted">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" name="old_password" id="old_password"
                                            class="form-control form-control-lg @error('old_password') is-invalid @enderror"
                                            placeholder="Enter current password" required>
                                        <span class="input-group-text toggle-password" data-target="old_password" title="Toggle password visibility">
                                            <em class="icon ni ni-eye-off"></em>
                                        </span>
                                    </div>
                                    @error('old_password')
                                        <div class="text-danger mt-1 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12"><hr class="my-3 text-light"></div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="new_password" class="form-label text-muted mb-0">New Password</label>
                                        <button type="button" onclick="openRequirementsModal()" 
                                            class="btn btn-link btn-sm p-0 text-primary text-decoration-none">
                                            <em class="icon ni ni-info me-1"></em> Requirements
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="new_password"
                                            class="form-control form-control-lg @error('new_password') is-invalid @enderror"
                                            placeholder="Enter new password" 
                                            oninput="validatePassword()"
                                            required>
                                        <span class="input-group-text toggle-password" data-target="new_password" title="Toggle password visibility">
                                            <em class="icon ni ni-eye-off"></em>
                                        </span>
                                    </div>

                                    <!-- Real-time validation preview -->
                                    <div id="validation-preview" class="mt-2 p-3 bg-light rounded border d-none">
                                        <p class="small fw-semibold text-secondary mb-2">Password Requirements:</p>
                                        <ul class="list-unstyled small mb-0">
                                            <li id="req-length" class="mb-1 requirement-unmet">
                                                <em class="icon ni ni-cross-circle me-1"></em>
                                                <span>Must be 8-64 characters long</span>
                                            </li>
                                            <li id="req-uppercase" class="mb-1 requirement-unmet">
                                                <em class="icon ni ni-cross-circle me-1"></em>
                                                <span>At least one uppercase letter (A-Z)</span>
                                            </li>
                                            <li id="req-lowercase" class="mb-1 requirement-unmet">
                                                <em class="icon ni ni-cross-circle me-1"></em>
                                                <span>At least one lowercase letter (a-z)</span>
                                            </li>
                                            <li id="req-number" class="mb-1 requirement-unmet">
                                                <em class="icon ni ni-cross-circle me-1"></em>
                                                <span>At least one number (0-9)</span>
                                            </li>
                                            <li id="req-special" class="requirement-unmet">
                                                <em class="icon ni ni-cross-circle me-1"></em>
                                                <span>At least one special character (@$!%*#?&)</span>
                                            </li>
                                        </ul>
                                    </div>

                                    @error('new_password')
                                        <div class="text-danger mt-1 small">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="new_password_confirmation" class="form-label text-muted">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                            class="form-control form-control-lg" 
                                            placeholder="Confirm new password" 
                                            oninput="validatePasswordMatch()"
                                            required>
                                        <span class="input-group-text toggle-password" data-target="new_password_confirmation" title="Toggle password visibility">
                                            <em class="icon ni ni-eye-off"></em>
                                        </span>
                                    </div>

                                    <!-- Password match indicator -->
                                    <div id="match-indicator" class="mt-2 d-none">
                                        <p class="small text-danger mb-0">
                                            <em class="icon ni ni-cross-circle me-1"></em>
                                            <span>Password confirmation must match</span>
                                        </p>
                                    </div>
                                    <div id="match-success" class="mt-2 d-none">
                                        <p class="small text-success mb-0">
                                            <em class="icon ni ni-check-circle me-1"></em>
                                            <span>Password confirmation must match</span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-5 d-flex justify-content-center justify-content-md-end gap-3">
                                     <button type="reset" class="btn btn-outline-secondary btn-md px-3 elegant-btn">
                                        <em class="icon ni ni-redo me-2"></em> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-md px-4 elegant-btn-primary">
                                        <em class="icon ni ni-save-fill me-2"></em> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Requirements Modal -->
<div class="modal fade" id="requirementsModal" tabindex="-1" aria-labelledby="requirementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <em class="icon ni ni-shield-check text-primary" style="font-size: 28px;"></em>
                    </div>
                    <h5 class="modal-title fw-bold" id="requirementsModalLabel">Password Requirements</h5>
                    <p class="text-muted small mb-0">Your password must meet the following criteria</p>
                </div>

                <div class="bg-light rounded p-3 mb-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            Must be <strong>8-64 characters</strong> long
                        </li>
                        <li class="mb-2">
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            At least <strong>one uppercase</strong> letter (A-Z)
                        </li>
                        <li class="mb-2">
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            At least <strong>one lowercase</strong> letter (a-z)
                        </li>
                        <li class="mb-2">
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            At least <strong>one number</strong> (0-9)
                        </li>
                        <li class="mb-2">
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            At least <strong>one special character</strong> (@$!%*#?&)
                        </li>
                        <li>
                            <em class="icon ni ni-check-circle text-success me-2"></em>
                            Password confirmation <strong>must match</strong>
                        </li>
                    </ul>
                </div>

                <div class="alert alert-info mb-0">
                    <p class="small mb-1"><strong>Example of a strong password:</strong></p>
                    <code class="text-primary">MyP@ssw0rd2024!</code>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Got it!</button>
            </div>
        </div>
    </div>
</div>

<style>
    .minimal-password-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: box-shadow 0.3s ease;
    }
    .minimal-password-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    
    .input-group-text.toggle-password {
        cursor: pointer;
        background-color: #e9ecef;
        border-left: 0;
        color: #6c757d;
        font-size: 1.2rem;
        padding: 0.5rem 1rem;
        transition: background-color 0.2s, color 0.2s;
    }
    .input-group-text.toggle-password:hover {
        background-color: #ced4da;
        color: #495057;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    .input-group > .form-control:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .elegant-btn, .elegant-btn-primary {
        font-weight: 500;
        border-radius: 0.3rem;
        transition: all 0.2s ease;
    }
    .elegant-btn:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }
    .elegant-btn-primary {
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    .elegant-btn-primary:hover {
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
        transform: translateY(-1px);
    }

    .requirement-met {
        color: #198754;
    }
    .requirement-unmet {
        color: #dc3545;
    }
</style>

<script>
$(document).ready(function() {

    // Password visibility toggle
    $('.toggle-password').on('click', function() {
        var targetId = $(this).data('target');
        var passwordField = $('#' + targetId);
        var icon = $(this).find('em');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('ni-eye-off').addClass('ni-eye');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('ni-eye').addClass('ni-eye-off');
        }
    });

    // --- Specific Toastr Errors ---
    @if($errors->has('old_password'))
        toastr.error("{{ $errors->first('old_password') }}");
    @endif

    @if($errors->has('new_password'))
        toastr.error("{{ $errors->first('new_password') }}");
    @endif

    @if($errors->has('new_password_confirmation'))
        toastr.error("{{ $errors->first('new_password_confirmation') }}");
    @endif

    // --- Reset Feedback ---
    $('#passwordForm').on('reset', function() {
        $('#old_password, #new_password, #new_password_confirmation')
            .attr('type', 'password');

        $('.toggle-password em')
            .removeClass('ni-eye')
            .addClass('ni-eye-off');

        // Hide validation previews
        $('#validation-preview').addClass('d-none');
        $('#match-indicator').addClass('d-none');
        $('#match-success').addClass('d-none');

        toastr.info("Form fields have been reset. Please enter your credentials again.");
    });

});

// Modal functions
function openRequirementsModal() {
    var modal = new bootstrap.Modal(document.getElementById('requirementsModal'));
    modal.show();
}

// Password validation
function validatePassword() {
    const password = document.getElementById('new_password').value;
    const preview = document.getElementById('validation-preview');
    
    // Show preview when user starts typing
    if (password.length > 0) {
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
        return;
    }

    // Validation checks
    const checks = {
        length: password.length >= 8 && password.length <= 64,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[@$!%*#?&]/.test(password)
    };

    // Update UI for each requirement
    updateRequirement('req-length', checks.length);
    updateRequirement('req-uppercase', checks.uppercase);
    updateRequirement('req-lowercase', checks.lowercase);
    updateRequirement('req-number', checks.number);
    updateRequirement('req-special', checks.special);

    // Validate password match if confirmation field has value
    validatePasswordMatch();
}

function updateRequirement(elementId, isMet) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector('em');
    
    if (isMet) {
        element.classList.remove('requirement-unmet');
        element.classList.add('requirement-met');
        icon.classList.remove('ni-cross-circle');
        icon.classList.add('ni-check-circle');
    } else {
        element.classList.remove('requirement-met');
        element.classList.add('requirement-unmet');
        icon.classList.remove('ni-check-circle');
        icon.classList.add('ni-cross-circle');
    }
}

function validatePasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirmation = document.getElementById('new_password_confirmation').value;
    const matchIndicator = document.getElementById('match-indicator');
    const matchSuccess = document.getElementById('match-success');

    if (confirmation.length === 0) {
        matchIndicator.classList.add('d-none');
        matchSuccess.classList.add('d-none');
        return;
    }

    if (password === confirmation) {
        matchIndicator.classList.add('d-none');
        matchSuccess.classList.remove('d-none');
    } else {
        matchIndicator.classList.remove('d-none');
        matchSuccess.classList.add('d-none');
    }
}
</script>

@endsection