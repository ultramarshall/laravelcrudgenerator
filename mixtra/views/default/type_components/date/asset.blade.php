@push('head')
    <link rel='stylesheet' href='<?php echo asset("assets/vendor/datepicker/css/bootstrap-datepicker.min.css")?>'/>
@endpush
@push('bottom')

    @if (App::getLocale() != 'en')
        <script src="{{ asset ('assets/vendor/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
            charset="UTF-8"></script>
    @else
        <script src="{{ asset ('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"
            charset="UTF-8"></script>
    @endif
    <script type="text/javascript">
        var lang = '{{App::getLocale()}}';
        $(function () {
            $('.input_date').datepicker({
                format: 'yyyy/mm/dd',
                @if (in_array(App::getLocale(), ['ar', 'fa']))
                rtl: true,
                @endif
                language: lang
            });

            $('.open-datetimepicker').click(function () {
                $(this).next('.input_date').datepicker('show');
            });

        });

    </script>
@endpush