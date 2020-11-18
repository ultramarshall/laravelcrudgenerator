@push('head')
    <link rel='stylesheet' href='<?php echo asset("assets/vendor/datetimepicker/css/bootstrap-datetimepicker.css")?>'/>
@endpush

@push('bottom')
    <script src="{{ asset ('assets/vendor/datetimepicker/js/bootstrap-datetimepicker.js') }}"
        charset="UTF-8"></script>
    <script type="text/javascript">
        $(function () {
            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });
        });
    </script>
@endpush