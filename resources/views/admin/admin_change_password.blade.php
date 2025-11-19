@extends('admin.dashboard')
@section('admin')
<!-- jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-page-head mb-4">
                <div class="nk-block-head-between align-items-center">
                    <div class="nk-block-head-content">
                        <h2 class="display-6 fw-bold mb-1">Change Password</h2>
                        <p class="text-muted">Update your account password to keep it secure.</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-3 password-card">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.password.update') }}" method="post">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="old_password" id="old_password"
                                        class="form-control @error('old_password') is-invalid @enderror"
                                        placeholder="Old Password">
                                    <label for="old_password">Old Password</label>
                                </div>
                                @error('old_password')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="new_password" id="new_password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        placeholder="New Password">
                                    <label for="new_password">New Password</label>
                                </div>
                                @error('new_password')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                        class="form-control" placeholder="Confirm New Password">
                                    <label for="new_password_confirmation">Confirm New Password</label>
                                </div>
                            </div>
                            <div class="col-12 mt-5 text-end">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <em class="icon ni ni-shield-check me-1"></em> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .password-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .password-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.08) !important;
        }
        .form-floating label {
            color: #6c757d;
            font-weight: 500;
        }
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            opacity: 0.65;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
        }
    </style>
@endsection