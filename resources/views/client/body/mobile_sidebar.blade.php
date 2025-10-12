<div class="nk-header nk-header-fixed">
<div class="container-fluid">
    <div class="nk-header-wrap">
        <div class="nk-header-logo ms-n1">
            <div class="nk-sidebar-toggle me-1">
                <button class="btn btn-sm btn-zoom btn-icon sidebar-toggle d-sm-none">
                    <em class="icon ni ni-menu"> </em>
                </button>
                <button class="btn btn-md btn-zoom btn-icon sidebar-toggle d-none d-sm-inline-flex">
                    <em class="icon ni ni-menu"> </em>
                </button>
            </div><!-- .nk-sidebar-toggle -->
            <a href="index.html" class="logo-link">
                <div class="logo-wrap">
                    <img class="logo-img logo-light" src="images/logo.png" srcset="images/logo2x.png 2x" alt="">
                    <img class="logo-img logo-dark" src="images/logo-dark.png" srcset="images/logo-dark2x.png 2x" alt="">
                    <img class="logo-img logo-icon" src="images/logo-icon.png" srcset="images/logo-icon2x.png 2x" alt="">
                </div>
            </a>
        </div>
        @php
            $id = Auth::user()->id;
            $profileData = App\Models\User::find($id);
        @endphp
        <div class="nk-header-tools">
            <ul class="nk-quick-nav ms-2">
                <li class="dropdown d-inline-flex">
                    <a class="d-inline-flex" href="{{ route('user.profile') }}">
                        <div class="media media-md media-circle media-middle text-bg-primary">
                            <img src="{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" />
                        </div>
                    </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


                                