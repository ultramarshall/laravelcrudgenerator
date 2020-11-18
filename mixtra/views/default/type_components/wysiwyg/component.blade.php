@push('bottom')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#textarea_{{$name}}').summernote({
                height: ($(window).height() - 600),
                callbacks: {
                    onImageUpload: function (image) {
                        uploadImage{{$name}}(image[0]);
                    }
                }
            });

            function uploadImage{{$name}}(image) {
                var data = new FormData();
                data.append("userfile", image);
                $.ajax({
                    url: '{{MITBooster::mainpath("upload-summernote")}}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    type: "post",
                    success: function (url) {
                        var image = $('<img>').attr('src', url);
                        $('#textarea_{{$name}}').summernote("insertNode", image[0]);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        })
    </script>
@endpush
@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class='form-group {{$header_group_class}} row {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
@endif
    <label class='col-form-label font-weight-bold col-sm-2'>{{$form['label']}}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <textarea id='textarea_{{$name}}' id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} name="{{$form['name']}}" class='form-control'
                  rows='5'>{{ $value }}</textarea>
        <div class="text-danger">{{ $errors->first($name) }}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
</div>
@endif
