<style>
    /*
     * The styles below are from the original file.
     * They are kept as-is, as they do not affect the icon change.
     */
    .nk-sidebar-head {
        padding: 20px 15px; /* Add some padding to give the logo breathing room */
    }

    .logo-wrap {
        width: 120px; /* Set a fixed width for the logo container */
        height: auto; /* Allow the height to adjust automatically */
        overflow: visible; /* Ensure nothing is hidden */
    }
    
    .logo-img {
        width: 100%; 
        height: auto; 
        object-fit: contain; /* Ensure the entire image is visible and not cropped */
    }
</style>

<!-- Bootstrap Icons CDN link - This is necessary for the icons to render -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<div class="nk-sidebar nk-sidebar-fixed" id="sidebar">
    <div class="nk-compact-toggle">
        <button class="btn btn-xs btn-outline-light btn-icon compact-toggle text-light bg-white rounded-3">
            <!-- Replaced ni ni-chevron-left with bi-chevron-left -->
            <em class="icon off bi bi-chevron-left"></em>
            <!-- Replaced ni ni-chevron-right with bi-chevron-right -->
            <em class="icon on bi bi-chevron-right"></em>
        </button>
    </div>
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="logo-link">
                <div class="logo-wrap">
                    <img class="logo-img logo-light" src="{{ asset('upload/uptm.png') }}"
                        srcset="{{ asset('upload/uptm.png') }}" alt="">
                    <img class="logo-img logo-dark" src="{{ asset('upload/uptm.png') }}"
                        srcset="{{ asset('upload/uptm.png') }}" alt="">
                    <img class="logo-img logo-icon" src="{{ asset('upload/uptm-icon.png') }}"
                        srcset="{{ asset('upload/uptm-icon.png') }}" alt="">
                </div>
            </a>
        </div><!-- end nk-sidebar-brand -->
    </div><!-- end nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content h-100" data-simplebar>
            <div class="nk-sidebar-menu">
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="{{ route('admin.dashboard') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                @php
                                    // Assuming a variable to check the user's role exists
                                    $role = Auth::user()->role;
                                @endphp
                                @if($role === 'admin')
                                    <!-- Replaced ni ni-layers-fill with bi-stack -->
                                    <em class="icon bi bi-houses-fill icon-outline-blue"></em>
                                @else
                                    <!-- Replaced ni ni-home-fill with bi-house-fill -->
                                    <em class="icon bi bi-house-fill icon-outline-blue"></em> 
                                @endif
                            </span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle"> 
                            <span class="nk-menu-icon">
                                @if($role === 'admin')
                                    <!-- Replaced ni ni-users-fill with bi-people-fill -->
                                    <em class="icon bi bi-person-fill-check icon-outline-blue"></em>
                                @else
                                    <!-- Replaced ni ni-user-circle-fill with bi-person-circle -->
                                    <em class="icon bi bi-person-fill-check icon-outline-blue"></em>
                                @endif
                            </span>
                            <span class="nk-menu-text">Account</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item">
                                {{-- Link to the admin profile page --}}
                                <a href="{{ route('admin.profile') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Profile</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('admin.change.password') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Change Password</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('add.template') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <!-- Replaced ni ni-user with bi-person -->
                                <em class="icon bi bi-file-earmark-plus-fill icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Add New Template</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('admin.template') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <!-- Replaced ni ni-user with bi-person -->
                                <em class="icon bi bi-stack icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">All Templates</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('admin.document') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <!-- Replaced ni ni-user with bi-person -->
                                <em class="icon bi bi-file-spreadsheet-fill icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Document</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        {{-- Link to the admin logout route --}}
                        <a href="{{ route('admin.logout') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <!-- Replaced ni ni-wallet with bi-box-arrow-right -->
                                <em class="icon bi bi-box-arrow-right icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-footer">
        <div class="nk-sidebar-footer-extended pt-3">
            <div class="border border-light rounded-3">
                {{-- <div class="px-3 py-2 bg-white border-bottom border-light rounded-top-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <h6 class="lead-text">Free Plan</h6>
                        <a class="link link-primary" href="pricing-plans.html">
                            <em class="ni ni-spark-fill icon text-warning"></em>
                            <span>Upgrade</span>
                        </a>
                    </div>
                    <div class="progress progress-md">
                        <div class="progress-bar" data-progress="25%"></div>
                    </div>
                    <h6 class="lead-text mt-2">1,360 <span class="text-light">words left</span></h6>
                </div> --}}

                {{-- Retrieve authenticated user's profile data --}}
                @php
                    $id = Auth::user()->id;
                    $profileData = App\Models\User::find($id);
                @endphp
                <a class="d-flex px-3 py-2 bg-primary bg-opacity-10 rounded-bottom-3" href="profile.html">
                    <div class="media-group">
                        <div class="media media-sm media-middle media-circle text-bg-primary">
                             {{-- Check if user has a photo, and display it or a placeholder --}}
                            <img src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" />
                        </div>
                        <div class="media-text">
                            {{-- Display the authenticated user's name --}}
                            <h6 class="fs-6 mb-0"> {{ $profileData->name }}</h6>
                            {{-- Display the authenticated user's email --}}
                            <span class="text-light fs-7">{{ $profileData->email }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div><!-- .nk-sidebar-element -->
</div><!-- .nk-sidebar -->
