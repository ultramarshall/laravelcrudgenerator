    <script type="text/javascript">
        var site_url = "{{url('/')}}";
        var sidebar = "{{$sidebar_mode}}";
    </script>
	<!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="{{ asset('assets/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!-- Moments -->
    <script src="{{ asset('assets/vendor/moment/moment.js') }}"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('assets/vendor/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
   
    <!-- Admin JavaScript -->
    @yield('admin_js')

    @if($load_js)
	    @foreach($load_js as $js)
	        <script src="{{$js}}"></script>
	    @endforeach
	@endif
    
    <script type="text/javascript">
        @if($script_js)
            {!! $script_js !!}
        @endif
    </script>