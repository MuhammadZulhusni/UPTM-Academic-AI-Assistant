@extends('superadmin.dashboard')
@section('superadmin')

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

                        <form id="passwordForm" action="{{ route('superadmin.password.update') }}" method="post">
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
                                    <label for="new_password" class="form-label text-muted">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" id="new_password"
                                            class="form-control form-control-lg @error('new_password') is-invalid @enderror"
                                            placeholder="Enter new password" required>
                                        <span class="input-group-text toggle-password" data-target="new_password" title="Toggle password visibility">
                                            <em class="icon ni ni-eye-off"></em>
                                        </span>
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
                                            class="form-control form-control-lg" placeholder="Confirm new password" required>
                                        <span class="input-group-text toggle-password" data-target="new_password_confirmation" title="Toggle password visibility">
                                            <em class="icon ni ni-eye-off"></em>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-12 mt-5 d-flex justify-content-center justify-content-md-end gap-3">
                                     {{-- FIX: Changed type="button" and removed onclick to trigger the jQuery 'reset' event --}}
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

        toastr.info("Form fields have been reset. Please enter your credentials again.");
    });

});
</script>

@endsection