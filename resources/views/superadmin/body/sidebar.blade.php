<style>
    .nk-sidebar-head {
        padding: 20px 15px;
    }

    .logo-wrap {
        width: 150px;
        height: auto;
        overflow: visible;
    }
    
    .logo-img {
        width: 100%; 
        height: auto; 
        object-fit: contain;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="nk-sidebar nk-sidebar-fixed" id="sidebar">
    <div class="nk-compact-toggle">
        <button class="btn btn-xs btn-outline-light btn-icon compact-toggle text-light bg-white rounded-3">
            <em class="icon off bi bi-chevron-left"></em>
            <em class="icon on bi bi-chevron-right"></em>
        </button>
    </div>
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="{{ route('superadmin.dashboard') }}" class="logo-link">
                <div class="logo-wrap">
                    <img class="logo-img logo-light" src="{{ asset('upload/uptm.png') }}"
                        srcset="{{ asset('upload/uptm.png') }}" alt="">
                    <img class="logo-img logo-dark" src="{{ asset('upload/uptm.png') }}"
                        srcset="{{ asset('upload/uptm.png') }}" alt="">
                    <img class="logo-img logo-icon" src="{{ asset('upload/uptm-icon.png') }}"
                        srcset="{{ asset('upload/uptm-icon.png') }}" alt="">
                </div>
            </a>
        </div>
    </div>
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content h-100" data-simplebar>
            <div class="nk-sidebar-menu">
                <ul class="nk-menu">
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                @php
                                    $role = Auth::user()->role;
                                @endphp
                                @if($role === 'superadmin')
                                    <em class="icon bi bi-houses-fill icon-outline-blue"></em>
                                @else
                                    <em class="icon bi bi-house-fill icon-outline-blue"></em> 
                                @endif
                            </span>
                            <span class="nk-menu-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nk-menu-item has-sub">
                        <a href="#" class="nk-menu-link nk-menu-toggle"> 
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-person-fill-check icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Account</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <li class="nk-menu-item">
                                <a href="{{ route('superadmin.profile') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Profile</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('superadmin.change.password') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Change Password</span>
                                </a>
                            </li>
                            <li class="nk-menu-item">
                                <a href="{{ route('superadmin.users') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Manage All Users</span>
                                </a>
                            </li>
                            <!-- <li class="nk-menu-item">
                                <a href="{{ route('superadmin.reset.password') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Reset User Password</span>
                                </a>
                            </li> -->
                            <li class="nk-menu-item">
                                <a href="{{ route('superadmin.create.user') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">Add New User</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.add.template') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-file-earmark-plus-fill icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Add New Template</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.template') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-stack icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">All Templates</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.admin.activities') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-activity icon-outline-purple"></em>
                            </span>
                            <span class="nk-menu-text">Admin Activities</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.document.settings') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-file-earmark-text icon-outline-purple"></em>
                            </span>
                            <span class="nk-menu-text">Document Cleanup</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.document') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-file-spreadsheet-fill icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Document</span>
                        </a>
                    </li>
                    <li class="nk-menu-item">
                        <a href="{{ route('superadmin.logout') }}" class="nk-menu-link">
                            <span class="nk-menu-icon">
                                <em class="icon bi bi-box-arrow-right icon-outline-blue"></em>
                            </span>
                            <span class="nk-menu-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="nk-sidebar-element nk-sidebar-footer">
        <div class="nk-sidebar-footer-extended pt-3">
        </div>
    </div>
</div>
