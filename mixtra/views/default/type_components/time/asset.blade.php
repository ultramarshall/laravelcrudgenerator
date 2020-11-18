@push('head')
    <!--Timepicker -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        .bootstrap-timepicker .dropdown-menu {
            left: 185px !important;
            box-shadow: 0px 0px 20px #aaaaaa;
        }
    </style>
@endpush

@push('bottom')
    <!--Timepicker -->
    <script src="{{ asset('assets/vendor/timepicker/bootstrap-timepicker.min.js') }}"></script>
@endpush