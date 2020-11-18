<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="shortcut icon"
        href="{{ MITBooster::getSetting('favicon')?asset(MITBooster::getSetting('favicon')):asset('assets/images/favicon.png') }}">
    @yield('title')
    
    @include('mitbooster::layouts.app_css')

    @stack('head')
</head>
<body class="@php echo (Session::get('theme_color'))?:'skin-blue'; echo ' '; echo config('mixtra.ADMIN_LAYOUT'); @endphp @yield('body-class') ">



    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Mixtra Admin</p>
        </div>
    </div> -->

    <!-- ============================================================== -->
    <!-- Main wrapper -->
    <!-- ============================================================== -->
    @yield('wrapper')
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    
    @include('mitbooster::layouts.app_js')

	@stack('bottom')
</body>

</html>