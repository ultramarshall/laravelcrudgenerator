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

<div class='form-group {{$header_group_class}} row' id='form-group-{{$name}}'>
    @if($form['columns'])
    <div class="col-sm-12">
        <table id='table-{{$name}}' class='table table-striped table-bordered'>
            <thead>
            <tr>
                <th style="width:2%;" class="text-center">{{trans('mixtra.no')}}</th>
                
                @foreach($form['columns'] as $col)
                    @if($col['type'] != 'hidden')
                    <?php 
                    $minwidth = '';
                    if($col['width'])
                        $minwidth = 'width: '.$col['width'].'%;';
                    ?>
                    <th class="{{$col['type'] == 'number' ? 'text-right': ''}}" style="{{$minwidth}}">{{$col['label']}}</th>
                    @endif
                @endforeach
            </tr>
            </thead>
            <tbody>
            <?php
                $parent = db::table($form['table'])->where('id', $id)->first();
                if($parent != null) {
                    $data_child = DB::table($form['table'])->where($form['foreign_key'], $parent->{$form['foreign_key']});
                    $data_child = $data_child->orderby($form['table'].'.id', 'asc');
                    $data_child = $data_child->get();
                    $i = 0;
                    foreach ($data_child as $d) {
                        echo "<tr class='".$name."-row'>";
                        echo "<input type='hidden' name='".$name."-id[]' value='".$d->id."' />";
                        echo "  <td class='text-center'>".($i+1)."</td>";
                        foreach ($form['columns'] as $col) {
                            $name_column = $name.'_'.$col['name'];
                            $readonly = $col['readonly']?"readonly":"";
                                
                            echo "<td class='".$col['name']."'>";
    
                            if ($col['type'] == 'date') {
                                echo "<input type='text' class='form-control ".$name."-".$col['name']." input_date' {$readonly} 
                                    name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."' />";
                            } else {
                                $text = '';
                                if ($col['type'] == 'number') {
                                    $text = 'decimal text-right';
                                }
                                echo "<input type='text' class='form-control ".$name."-".$col['name']." {$text}' {$readonly} 
                                    name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."' />";
                            }

                            echo "</td>";
                        }
                        echo "</tr>";
                        $i++;
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
    @endif
</div>