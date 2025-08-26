@extends('admin.dashboard')

@section('admin')
{{-- jQuery from a CDN for AJAX functionality --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<div class="nk-content-inner">
    <div class="nk-content-body">
        <div class="nk-block-head nk-page-head">
            <div class="nk-block-head-between">
                <div class="nk-block-head-content">
                    <h2 class="display-6">Personal Profile</h2>
                </div>
            </div>
        </div><!-- .nk-page-head -->
        <div class="nk-block">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-head-content">
                </div>
            </div><!-- .nk-block-head -->
            <div class="card shadown-none">
                <div class="card-body">
                    {{-- Form to update the admin's profile, including file upload --}}
                    <form action="{{ route('admin.profile.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 gx-gs">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">Name </label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="name" class="form-control" value="{{ $profileData->name }}"  >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">Email </label>
                                    <div class="form-control-wrap">
                                        <input type="email" name="email" class="form-control" value="{{ $profileData->email }}"  >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">Phone </label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="phone" class="form-control" value="{{ $profileData->phone }}"  >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">Address </label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="address" class="form-control" value="{{ $profileData->address }}"  >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">Profile Image </label>
                                    <div class="form-control-wrap">
                                        <input type="file" name="photo" class="form-control" id="image" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInputText1" class="form-label">  </label>
                                    <div class="form-control-wrap">
                                        {{-- Display the current profile photo, or a default image if none exists --}}
                                        <img id="showImage" src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" class="rounded-circle avatar-xl img-thumbnail float-start" style="width: 80px; height:80px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-xl-12">
                                <button type="submit" class="btn btn-secondary">Save Changes</button> 
                            </div>
                        </div>
                    </form> 
                </div><!-- .card-body -->
            </div><!-- .card -->
        </div><!-- .nk-block -->
    </div>
</div>

{{-- JavaScript section to handle image preview functionality --}}
<script type="text/javascript">
    // Use jQuery to listen for changes on the file input field
    $(document).ready(function(){
        $('#image').change(function(e){
            // Create a new FileReader object to read the selected file
            var reader = new FileReader();
            // Set up the onload event handler for the reader
            reader.onload = function(e){
                // Set the 'src' attribute of the image element to the loaded file's data URL
                $('#showImage').attr('src',e.target.result);
            }
            // Read the selected file as a data URL
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>

@endsection

