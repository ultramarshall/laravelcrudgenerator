@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class='form-group row form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
@endif
    <label class='col-form-label font-weight-bold col-sm-2'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('mixtra.this_field_is_required') !!}'>*</span>
        @endif
    </label>
    <div class="{{$col_width?:'col-sm-10'}}">
        <input type='text' title="{{$form['label']}}" {{$readonly}}
               {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus datetimepicker' name="{{$name}}" id="{{$name}}"
               value='{{ $value == null ? "" : date("Y-m-d H:i:s",strtotime($value)) }}'/>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
</div>
@endif
