@extends('mitbooster::layouts.app')

@section('title')
<title>Login Panel : {{MITBooster::getSetting('appname')}}</title>
@endsection

@section('admin_css')
<link href="{{ asset('assets/css/pages/login-register-lock.css') }}" rel="stylesheet">
@endsection

@section('admin_js')
<script type="text/javascript">
	$(function() {
	    $(".preloader").fadeOut();
	});
</script>
@endsection

@section('wrapper')
<section id="wrapper" class="login-register login-sidebar" style="background-image:url({{ MITBooster::getSetting('login_background_image')?asset(MITBooster::getSetting('login_background_image')):asset('assets/images/background/login-register.jpg') }})">
    <div class="login-box card" style="max-width: 365px;">
        <div class="card-body">
	        @if ( Session::get('message') != '' )
            <div class='alert alert-warning'>
                {{ Session::get('message') }}
            </div>
	        @endif
	        <form class="form-horizontal form-material text-center" id="loginform"  autocomplete='off' action="{{ route('postLogin') }}" method="post">
	            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <a href="javascript:void(0)" class="db">
                    <img src="{{ MITBooster::getSetting('logo_dark')?asset(MITBooster::getSetting('logo_dark')):asset('assets/images/logo-dark.png') }}" alt="Home" /><br/>
                    <!-- <img src="{{ MITBooster::getSetting('logo_text_dark')?asset(MITBooster::getSetting('logo_text_dark')):asset('assets/images/logo-text.png') }}" alt="Home" /> -->
                </a>
                <div class="form-group m-t-40 m-b-10" style="min-height: 38px;">
                    <div class="col-sm-12">
                        <input class="form-control" type="text" required="" placeholder="Username" name="username">
                    </div>
                </div>
                <div class="form-group m-b-10" style="min-height: 38px;">
                    <div class="col-sm-12">
                        <input class="form-control" type="password" required="" placeholder="Password" name="password">
                    </div>
                </div>
                <div class="form-group row m-b-10" style="min-height: 38px;">
                    <div class="col-sm-12">
                        <div class="d-flex no-block align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                <label class="custom-control-label" for="customCheck1">Remember me</label>
                            </div> 
                            <div class="ml-auto">
                                <a href="javascript:void(0)" id="to-recover" class="text-muted"><i class="fas fa-lock m-r-5"></i> Forgot pwd?</a> 
                            </div>
                        </div>   
                    </div>
                </div>
                <div class="form-group text-center m-t-20 m-b-10" style="min-height: 38px;">
                    <div class="col-sm-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase btn-rounded" type="submit">Log In</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>
@endsection
