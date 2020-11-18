@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class='bootstrap-timepicker'>
    <div class='form-group {{$header_group_class}} row {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
@endif
        <label class='col-form-label font-weight-bold col-sm-2'>{{$form['label']}}
            @if($required)
                <span class='text-danger' title='{!! trans('mixtra.this_field_is_required') !!}'>*</span>
            @endif
        </label>

        <div class="{{$col_width?:'col-sm-10'}}">
            <div class="input-group">
                <input type='text' title="{{$form['label']}}"
                       {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus timepicker' name="{{$name}}" id="{{$name}}"
                       readonly value='{{$value}}'/>
            </div>
            <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
            <p class='help-block'>{{ @$form['help'] }}</p>
        </div>
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
    </div>
</div>
@endif

@push('bottom')
<script type="text/javascript">
    $('#{{$name}}').timepicker({
        minuteStep: 1,
        showMeridian: false,
        defaultTime: false,
    });
</script>
@endpush
