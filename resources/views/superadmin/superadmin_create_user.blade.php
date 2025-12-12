@extends('superadmin.dashboard')

@section('superadmin')
<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between flex-wrap g-2">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold">Create New User</h2>
                    <p class="text-muted mb-0">Add a new user account to the system</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="user-icon-wrapper mx-auto mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                                </svg>
                            </div>
                            <h4 class="fw-bold mb-2">User Account Information</h4>
                            <p class="text-muted">Fill in the details to create a new user account</p>
                        </div>

                        <form action="{{ route('superadmin.store.user') }}" method="POST" id="createUserForm">
                            @csrf

                            <div class="form-section mb-4">
                                <h5 class="form-section-title mb-3">
                                    <i class="bi bi-person-badge text-primary me-2"></i>
                                    Personal Information
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold">
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" 
                                               placeholder="Enter full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" 
                                               placeholder="user@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone') }}" 
                                               placeholder="+60 12-345 6789">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="role" class="form-label fw-semibold">
                                            User Role <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" name="role" required>
                                            <option value="" selected disabled>Choose role...</option>
                                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>
                                                <i class="bi bi-person"></i> Student
                                            </option>
                                            <option value="lecturer" {{ old('role') == 'lecturer' ? 'selected' : '' }}>
                                                <i class="bi bi-person-workspace"></i> Lecturer
                                            </option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                                <i class="bi bi-shield-lock"></i> Admin
                                            </option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="2" 
                                                  placeholder="Enter full address">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h5 class="form-section-title mb-3">
                                    <i class="bi bi-shield-lock text-primary me-2"></i>
                                    Security Settings
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold">
                                            Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" 
                                                   placeholder="Enter password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="bi bi-eye" id="eyeIcon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Minimum 8 characters</small>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label fw-semibold">
                                            Confirm Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" 
                                                   placeholder="Confirm password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Must match password</small>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="password-strength d-none" id="passwordStrength">
                                        <small class="text-muted d-block mb-2">Password Strength:</small>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" id="strengthBar"></div>
                                        </div>
                                        <small class="text-muted mt-1" id="strengthText"></small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mb-4">
                                <h5 class="form-section-title mb-3">
                                    <i class="bi bi-toggle-on text-primary me-2"></i>
                                    Account Status
                                </h5>
                                
                                <div class="card bg-light border-0">
                                    <div class="card-body p-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   name="is_active" value="1" checked>
                                            <label class="form-check-label fw-medium" for="is_active">
                                                Account Active
                                                <small class="text-muted d-block">
                                                    When active, user can login and access the system
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <button type="reset" class="btn btn-outline-warning px-4" id="resetFormBtn">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                                    Reset Form
                                </button>
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                    <i class="bi bi-person-plus me-2"></i>
                                    Create User Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-info-subtle text-info me-3">
                                <i class="bi bi-lightbulb fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Guidelines</h5>
                        </div>
                        
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <small>Use a valid email address for account verification</small>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <small>Choose a strong password with at least 8 characters</small>
                            </li>
                            <li class="mb-3">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <small>Select appropriate role based on user responsibilities</small>
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-check-circle text-success me-1"></i>
                                <small>Inactive accounts cannot login to the system</small>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            User Roles
                        </h5>
                        
                        <div class="role-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="role-badge bg-primary-subtle text-primary">
                                    <i class="bi bi-person"></i>
                                </div>
                                <h6 class="mb-0 ms-2 fw-semibold">Student</h6>
                            </div>
                            <small class="text-muted">Access to learning materials and content generation</small>
                        </div>

                        <div class="role-info mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="role-badge bg-success-subtle text-success">
                                    <i class="bi bi-person-workspace"></i>
                                </div>
                                <h6 class="mb-0 ms-2 fw-semibold">Lecturer</h6>
                            </div>
                            <small class="text-muted">Access to teaching materials and content generation</small>
                        </div>

                        <div class="role-info">
                            <div class="d-flex align-items-center mb-2">
                                <div class="role-badge bg-warning-subtle text-warning">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h6 class="mb-0 ms-2 fw-semibold">Admin</h6>
                            </div>
                            <small class="text-muted">Manage users and templates</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Existing Code ---

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });

        // Toggle Password Confirmation Visibility
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('password_confirmation');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');

        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.type === 'password' ? 'text' : 'password';
            passwordConfirm.type = type;
            eyeIconConfirm.classList.toggle('bi-eye');
            eyeIconConfirm.classList.toggle('bi-eye-slash');
        });

        // Password Strength Checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const passwordStrength = document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.length === 0) {
                passwordStrength.classList.add('d-none');
                return;
            }

            passwordStrength.classList.remove('d-none');
            
            let strength = 0;
            let text = '';
            let color = '';

            // Check password strength
            if (value.length >= 8) strength += 25;
            if (value.match(/[a-z]+/)) strength += 25;
            if (value.match(/[A-Z]+/)) strength += 25;
            if (value.match(/[0-9]+/)) strength += 15;
            if (value.match(/[$@#&!]+/)) strength += 10;

            // Set color and text based on strength
            if (strength < 40) {
                color = 'bg-danger';
                text = 'Weak';
            } else if (strength < 70) {
                color = 'bg-warning';
                text = 'Medium';
            } else {
                color = 'bg-success';
                text = 'Strong';
            }

            strengthBar.style.width = strength + '%';
            strengthBar.className = 'progress-bar ' + color;
            strengthText.textContent = text;
        });

        // Form Submission Loading State
        const form = document.getElementById('createUserForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
        });

        // --- New Code for Reset Button and Toastr ---

        const resetFormBtn = document.getElementById('resetFormBtn');

        resetFormBtn.addEventListener('click', function(e) {
            // The button is of type="reset", so it will clear the form fields by default.
            // We prevent the default to manually clear things up and then show the toastr
            // e.preventDefault(); 
            // form.reset(); // If it wasn't type="reset"

            // Show Toastr message after form reset
            // Check if toastr is defined before calling it
            if (typeof toastr !== 'undefined') {
                toastr.info('The form fields have been cleared and reset.', 'Form Reset', {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "3000"
                });
            } else {
                console.warn('Toastr is not defined. Please ensure the library is included.');
            }

            // Manually re-hide password strength indicator on reset
            passwordStrength.classList.add('d-none');
        });
    });
</script>

<style>
.user-icon-wrapper {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    /* REMOVED: animation: float 3s ease-in-out infinite; */
}

/* REMOVED: @keyframes float block */

.form-section {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 1.5rem;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section-title {
    color: #1f2937;
    font-size: 1.1rem;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.role-badge {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.role-info {
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.15);
}

.form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}
</style>

@endsection