@extends('mitbooster::layouts.app')

@section('body-class', 'skin-default fixed-layout')

@section('title')
<title>Locked Screen : {{Session::get('appname')}}</title>
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
<section id="wrapper">
    <div class="login-register" style="background-image:url({{ MITBooster::getSetting('login_background_image')?asset(MITBooster::getSetting('login_background_image')):asset('assets/images/background/login-register.jpg') }});">
        <div class="login-box card">
            <div class="card-body">
                <form class="form-horizontal form-material" id="loginform" method='post' action="{{ route('postUnlockScreen') }}">
		            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <div class="form-group m-b-10" style="min-height: 38px;">
                        <div class="col-xs-12 text-center">
                            <div class="user-thumb text-center"> <img alt="thumbnail" class="img-circle" width="100" src="{{ MITBooster::myPhoto() }}">
                                <h3>{{ MITBooster::myName() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-b-10" style="min-height: 38px;">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" placeholder="password" name="password">
                        </div>
                    </div>
                    <div class="form-group m-b-10 text-center" style="min-height: 38px;">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
