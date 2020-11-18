@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class='form-group {{$header_group_class}} row {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
@endif
@if($col_width == 'col-sm-10')
	<div class="col-12"><hr></div>
@else
    <label class="col-form-label font-weight-bold {{$col_width?:'col-sm-10'}}">{{$form['label']}}</label>
@endif
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
</div>
@endif
