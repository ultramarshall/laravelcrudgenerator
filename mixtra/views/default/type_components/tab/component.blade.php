<!-- Nav tabs -->
<ul id="tab-{{$name}}" class="nav nav-tabs" role="tablist">
<?php
	$active = 'active';
?>
@foreach($form['tabpages'] as $tabpage)
    @if($tabpage['type'] != 'hidden')
    	<li class="nav-item"> <a class="nav-link {{$active}}" data-toggle="tab" href="#{{$tabpage['name']}}" role="tab"><span class="hidden-sm-up"><i class="{{$tabpage['image']}}"></i></span> <span class="hidden-xs-down">{{$tabpage['label']}}</span></a> </li>
	<?php
		if($active != '')
			$active = '';
	?>
    @endif
@endforeach
</ul>
<!-- Tab panes -->
<div class="tab-content tabcontent-border m-b-20">
<?php
	$active = 'active';
?>
@foreach($form['tabpages'] as $tabpage)
    @if($tabpage['type'] != 'hidden')
    <div class="tab-pane p-20 {{$active}}" id="{{$tabpage['name']}}" role="tabpanel">
    	<?php
    	$asset_already = [];
		foreach($tabpage['pages'] as $form) {
			$type = @$form['type'] ?: 'text';
			$name = $form['name'];
			if (in_array($type, $asset_already)) continue;
		?>
			@if(file_exists(base_path('/mixtra/views/default/type_components/'.$type.'/asset.blade.php')))
    			@include('mitbooster::default.type_components.'.$type.'.asset')
			@endif
		<?php
		$asset_already[] = $type;
		}

		$header_group_class = "";
		foreach($tabpage['pages'] as $form) {
			$name = $form['name'];
			@$join = $form['join'];
			@$value = (isset($form['value'])) ? $form['value'] : '';
			@$value = (isset($row->{$name})) ? $row->{$name} : $value;

			$old = old($name);
			$value = (! empty($old)) ? $old : $value;

			$validation = array();
			$validation_raw = isset($form['validation']) ? explode('|', $form['validation']) : array();
			if ($validation_raw) {
			    foreach ($validation_raw as $vr) {
			        $vr_a = explode(':', $vr);
			        if ($vr_a[1]) {
			            $key = $vr_a[0];
			            $validation[$key] = $vr_a[1];
			        } else {
			            $validation[$vr] = TRUE;
			        }
			    }
			}

			if (isset($form['callback_php'])) {
			    @eval("\$value = ".$form['callback_php'].";");
			}


			if (isset($form['callback'])) {
			    $value = call_user_func($form['callback'], $row);
			}

			if ($join && @$row) {
			    $join_arr = explode(',', $join);
			    array_walk($join_arr, 'trim');
			    $join_table = $join_arr[0];
			    $join_title = $join_arr[1];
			    $join_query_{$join_table} = DB::table($join_table)->select($join_title)->where("id", $row->{'id_'.$join_table})->first();
			    $value = @$join_query_{$join_table}->{$join_title};
			}
			$form['type'] = ($form['type']) ?: 'text';
			$type = @$form['type'];
			$required = (@$form['required']) ? "required" : "";
			$required = (@strpos($form['validation'], 'required') !== FALSE) ? "required" : $required;
			$readonly = (@$form['readonly']) ? "readonly" : "";
			$disabled = (@$form['disabled']) ? "disabled" : "";
			$placeholder = (@$form['placeholder']) ? "placeholder='".$form['placeholder']."'" : "";
			$col_width = @$form['width'] ?: "col-sm-9";

			if ($parent_field == $name && $type != 'blank') {
			    $type = 'hidden';
			    $value = $parent_id;
			}

			if ($type == 'header') {
			    $header_group_class = "header-group-$index";
			} else {
			    $header_group_class = ($header_group_class) ?: "header-group-$index";
			}
		?>
			@if(file_exists(base_path('/mixtra/views/default/type_components/'.$type.'/component.blade.php')))
			    @include('mitbooster::default.type_components.'.$type.'.component')
			@else
			    <p class='text-danger'>{{$type}} is not found in type component system</p><br/>
			@endif
		<?php
		}
		?>
    </div>
	<?php
		if($active != '')
			$active = '';
	?>
    @endif
@endforeach
</div>
