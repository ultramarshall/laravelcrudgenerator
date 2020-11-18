<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-dark">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        <div class="navbar-header" style="text-align: center;">
            <a class="navbar-brand" href="{{url(config('mixtra.ADMIN_PATH'))}}" title="{{Session::get('appname')}}">
                <!-- Logo icon -->
                <b>
                    <!-- Light Logo icon -->
                    <img src="{{ MITBooster::getSetting('logo_light')?asset(MITBooster::getSetting('logo_light')):asset('assets/images/logo-light.png') }}" alt="homepage" class="light-logo" />
                </b>
                <!--End Logo icon -->
                <!-- Logo text -->
                @if(MITBooster::getSetting('logo_light_text') != null)
                <span>
                    <!-- Light Logo text -->    
                    <img src="{{ asset(MITBooster::getSetting('logo_light_text')) }}" class="light-logo" alt="homepage" />
                </span> 
                @endif
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler d-block d-md-none waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                <li class="nav-item"> <a class="nav-link sidebartoggler d-none d-lg-block d-md-block waves-effect waves-dark" href="javascript:void(0)"><i class="icon-menu"></i></a> </li>
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                <!-- ============================================================== -->
                <!-- Comment -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ti-email"></i>
                        <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown">
                        <ul>
                            <li>
                                <div class="drop-title">{{trans("mixtra.text_no_notification")}}</div>
                            </li>
                            <li>
                                 <em>{{trans("mixtra.text_no_notification")}}</em>
                            </li>
                            <li>
                                <a class="nav-link text-center link" href="{{route('NotificationsControllerGetIndex')}}"><strong>{{trans("mixtra.text_view_all_notification")}}</strong> <i class="fa fa-angle-right"></i></a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- User Profile -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown u-pro">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark profile-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ MITBooster::myPhoto() }}" alt="user" class=""> <span class="hidden-md-down">{{ MITBooster::myName() }}&nbsp;<i class="fa fa-angle-down"></i></span> </a>
                    <div class="dropdown-menu dropdown-menu-right animated flipInY">
                        <a href="{{ route('UsersControllerGetProfile') }}" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('getLockScreen') }}" class="dropdown-item"><i class="fa fa-key"></i> Lock Screen</a>
                        <a href="{{ route('getLogout') }}" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- End User Profile -->
                <!-- ============================================================== -->
            </ul>
        </div>
    </nav>
</header>
