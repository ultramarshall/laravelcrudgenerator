<!-- Default CSS -->
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
<!-- Bootstrap 3.3.2 -->
<!-- <link href="{{ asset("assets/vendor/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css"/> -->
<!-- Font Awesome Icons -->
<link href="{{asset("assets/icons/font-awesome/css")}}/fontawesome-all.min.css" rel="stylesheet" type="text/css"/>

<?php
$name = Request::get('name_column');
$link = Request::get('link');
$coloms_alias = explode(',', 'ID,'.Request::get('columns_name_alias'));
if (count($coloms_alias) < 2) {
    $coloms_alias = $columns;
}
?>

<form method='get' action="">
    {!! MITBooster::getUrlParameters(['q']) !!}
    <input type="text" placeholder="{{trans('mixtra.datamodal_search_and_enter')}}" name="q" title="{{trans('mixtra.datamodal_enter_to_search')}}"
           value="{{Request::get('q')}}" class="form-control">
</form>

<table id='table_dashboard' class='table table-striped table-bordered table-condensed table-responsive' style="margin-bottom: 0px;">
    <thead>
    @foreach($coloms_alias as $col)
        <th class='small'>{{ $col }}</th>
    @endforeach
    @if(!$link)
    <th width="5%">{{trans('mixtra.datamodal_select')}}</th>
    @endif
    </thead>
    <tbody>
    @foreach($result as $row)
        <tr>
            <?php
            $select_data_result = [];
            $select_data_result['datamodal_id'] = $row->id;
            $select_data_result['datamodal_label'] = $row->{$columns[1]} == null ? $row->id : $row->{$columns[1]};
            $select_data = Request::get('select_to');
            if ($select_data) {
                $select_data = explode(',', $select_data);
                if ($select_data) {
                    foreach ($select_data as $s) {
                        $s_exp = explode(':', $s);
                        $field_name = $s_exp[0];
                        $target_field_name = $s_exp[1];
                        $select_data_result[$target_field_name] = $row->$field_name;
                    }
                }
            }
            ?>

            @foreach($columns as $key => $col)
                <?php
                $img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                $ext = pathinfo($row->$col, PATHINFO_EXTENSION);
                $float = (double)$row->$col;
                if($col == 'item_no')
                    $float = null;
                if($link && $key == $link) {
                    if ($ext && in_array($ext, $img_extension)) {
                        echo "<td class='small'><a href='".asset($row->$col)."' data-lightbox='roadtrip'><img src='".asset($row->$col)."' width='50px' height='30px'/></a></td>";
                    } elseif ($float != null) {
                        echo "<td class='small text-right'><a href='javascript:void(0)' onclick='parent.selectAdditionalData".$name."(".json_encode($select_data_result).")'>".number_format($float)."</a></td>";
                    } else {
                        echo "<td class='small'><a href='javascript:void(0)' onclick='parent.selectAdditionalData".$name."(".json_encode($select_data_result).")'>".str_limit(strip_tags($row->$col), 50)."</a></td>";
                    }
                } else {
                    if ($ext && in_array($ext, $img_extension)) {
                        echo "<td class='small'><a href='".asset($row->$col)."' data-lightbox='roadtrip'><img src='".asset($row->$col)."' width='50px' height='30px'/></a></td>";
                    } elseif ($float != null) {
                        echo "<td class='small text-right'>".number_format($float)."</td>";
                    } else {
                        echo "<td class='small'>".str_limit(strip_tags($row->$col), 50)."</td>";
                    }
                }
                ?>
            @endforeach
            @if(!$link)
            <td><a class='btn btn-primary btn-xs' href='javascript:void(0)' onclick='parent.selectAdditionalData{{$name}}({!! json_encode($select_data_result) !!})'><i
                            class='fa fa-check-circle'></i> {{trans('mixtra.datamodal_select')}}</a></td>
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
<div align="center">{!! str_replace("/?","?",$result->appends(Request::all())->render()) !!}</div>