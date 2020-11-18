<?php
if (@$form['datatable']) {
    $raw = explode(",", $form['datatable']);
    $format = $form['datatable_format'];
    $table1 = $raw[0];
    $column1 = $raw[1];

    @$table2 = $raw[2];
    @$column2 = $raw[3];

    @$table3 = $raw[4];
    @$column3 = $raw[5];

    $selects_data = DB::table($table1)->where($table1.".id", $value);
    if ($table2 && $column2) {
        $selects_data->join($table2, $table2.'.id', '=', $table1.'.'.$column2);
    }
    if ($table3 && $column3) {
        $selects_data->join($table3, $table3.'.id', '=', $table2.'.'.$column3);
    }

    if ($table3) {
        $selects_data->select($table3.'.'.$column1);
    } elseif ($table2) {
        $selects_data->select($table2.'.'.$column1);
    } else {
        $selects_data->select($table1.'.'.$column1);
    }

    // if ($format) {
    //     $format = str_replace('&#039;', "'", $format);
    //     $selects_data->addselect(DB::raw("CONCAT($format) as label"));
    //     $selects_data = $selects_data->orderby(DB::raw("CONCAT($format)"), "asc")->get();
    // } else {
    //     $selects_data->addselect($orderby_table.'.'.$orderby_column.' as label');
    //     $selects_data = $selects_data->orderby($orderby_table.'.'.$orderby_column, "asc")->get();
    // }

    $data = $selects_data->first();
    if($data != null)
        $value = $data->{$column1};

    // dd($value);

    // dd($selects_data->toSql());
    // dd($selects_data->getBindings());
}





    //     $val = $d->id;
    //     $select = ($value == $val) ? "selected" : "";

    //     echo "<option $select value='$val'>".$d->label."</option>";
    // }
?>
@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class='form-group {{$header_group_class}} row {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
@endif
    <label class='col-form-label font-weight-bold col-sm-2'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('mixtra.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <input type='text' title="{{$form['label']}}"
               {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} {{$validation['max']?"maxlength=".$validation['max']:""}} class='form-control'
               name="{{$name}}" id="{{$name}}" value='{{$value}}'/>

        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>

    </div>
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
</div>
@endif
