@extends('client.client_dashboard')

@section('client')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head mb-4">
            <div class="nk-block-head-between align-items-center">
                <div class="nk-block-head-content">
                    <h2 class="display-6 fw-bold mb-1">Personal Profile</h2>
                    <p class="text-muted">Manage and update your personal details and image.</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3 profile-card">
            <div class="card-body p-4 p-md-5">
                <form id="profileForm" action="{{ route('user.profile.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-lg-3 d-flex flex-column align-items-center justify-content-center border-end">
                            <div class="mb-4 text-center">
                                <div class="position-relative">
                                    <img id="showImage" 
                                         src="{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" 
                                         alt="Profile Image" 
                                         class="rounded-circle border border-3 border-light shadow"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="mt-3">
                                    <h4 class="fw-bold mb-0">{{ $profileData->name }}</h4>
                                    <p class="text-muted small">{{ $profileData->email }}</p>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-center w-100">
                                <label for="image" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                    <em class="icon ni ni-upload-cloud"></em>
                                    <span>Upload New Photo</span>
                                </label>
                                <input type="file" name="photo" class="form-control d-none" id="image" accept="image/jpeg,image/jpg,image/png">
                                <small class="text-muted text-center mt-2" style="font-size: 0.80rem;">
                                    <strong>Accepted formats:</strong> JPG, JPEG, PNG<br>
                                    <strong>Max size:</strong> 2MB
                                </small>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="name" class="form-control" id="name" 
                                               value="{{ $profileData->name }}" placeholder="Enter full name" required>
                                        <label for="name">Full Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" name="email" class="form-control" id="email" 
                                               value="{{ $profileData->email }}" placeholder="Enter email address" required>
                                        <label for="email">Email Address</label>
                                    </div>
                                </div>
                                <!-- <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="phone" class="form-control" id="phone" 
                                               value="{{ $profileData->phone }}" placeholder="Enter phone number">
                                        <label for="phone">Phone Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" name="address" class="form-control" id="address" 
                                               value="{{ $profileData->address }}" placeholder="Enter address">
                                        <label for="address">Address</label>
                                    </div>
                                </div> -->
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary px-4" onclick="resetForm()">
                                        <em class="icon ni ni-redo me-1"></em> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <em class="icon ni ni-save me-1"></em> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-card:hover {
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

<script type="text/javascript">
    const originalImageSrc = "{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}";
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB in bytes
    const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];

    $(document).ready(function(){
        // Toastr default options
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "closeButton": true,
            "progressBar": true,
        };

        $('#image').change(function(e){
            const file = e.target.files[0];
            
            // Reset if no file selected
            if (!file) {
                return;
            }

            // Validate file type
            if (!ALLOWED_TYPES.includes(file.type)) {
                toastr.error('Invalid file format! Please upload JPG, JPEG, or PNG images only.');
                $(this).val(''); // Clear the input
                return;
            }

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                toastr.error('File size too large! Maximum allowed size is 2MB.');
                $(this).val(''); // Clear the input
                return;
            }

            // If validation passes, show preview
            let reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src', e.target.result);
                toastr.success('Image selected successfully!');
            }
            reader.onerror = function() {
                toastr.error('Failed to read the image file. Please try again.');
                $('#image').val('');
            }
            reader.readAsDataURL(file);
        });

        $('#profileForm').on('submit', function(e) {
            console.log('Form submitted');
        });
    });

    function resetForm() {
        document.getElementById("profileForm").reset();
        $('#showImage').attr('src', originalImageSrc);
        toastr.info('Profile form has been reset!');
    }
</script>

@endsection