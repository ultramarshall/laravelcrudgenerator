<?php
$name = str_slug($form['label'], '');
?>
@push('bottom')
    <script type="text/javascript">
        $(function () {
            $('#form-group-{{$name}} .select2').select2();
        })
    </script>
@endpush
<div class='form-group {{$header_group_class}} row' id='form-group-{{$name}}'>

    @if($form['columns'])
        <div class="col-sm-12">

            <div id='panel-form-{{$name}}' class="card card-default">
                <div class="card-header">
                    <i class='fa fa-bars'></i> {{$form['label']}}
                    <div class="card-actions">
                        @if ($form['buttons'])
                            @foreach($form['buttons'] as $but)
                                <a class="" title="{{$but['label']}}" onclick="{{$but['action']}}"><i class="{{$but['icon']}}"></i></a>
                            @endforeach
                        @endif
                        <a class="" data-action="collapse"><i class="ti-minus"></i></a>
                        <a class="btn-minimize" data-action="expand"><i class="mdi mdi-arrow-expand"></i></a>
                    </div>
                </div>
                <div class="card-body collapse show">

                    @push('bottom')

                        <script type="text/javascript">
                            var currentRow = null;
                            var isLoad = false;

                            function resetForm{{$name}}() {
                                $('#panel-form-{{$name}}').find("input[type=text],input[type=number],select,textarea").val('');
                                $('#panel-form-{{$name}}').find(".select2").val('').trigger('change');
                            }

                            function deleteRow{{$name}}(t) {

                                if (confirm("{{trans('mixtra.delete_title_confirm')}}")) {
                                    $(t).parent().parent().remove();
                                    if ($('#table-{{$name}} tbody tr').length == 0) {
                                        var colspan = $('#table-{{$name}} thead tr th').length;
                                        $('#table-{{$name}} tbody').html("<tr class='trNull'><td colspan='" + colspan + "' align='center'>{{trans('mixtra.table_data_not_found')}}</td></tr>");
                                    }
                                }
                            }

                            function editRow{{$name}}(t) {
                                var p = $(t).parent().parent(); //parentTR
                                currentRow = p;
                                isLoad = true;
                                p.addClass('warning');
                                $('#btn-add-table-{{$name}}').val('{{trans("mixtra.button_save")}}');
                                @foreach($form['columns'] as $c)
                                @if($c['type']=='select')
                                    $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val()).trigger("change");
                                @elseif($c['type']=='radio')
                                    var v = p.find(".{{$c['name']}} input").val();
                                    $('.{{$name.$c["name"]}}[value=' + v + ']').prop('checked', true);
                                @elseif($c['type']=='number')
                                    var v = p.find(".{{$c['name']}} input").val();
                                    setNumberFormat{{$name}}{{$c['name']}}(v, $('#{{$name.$c["name"]}}'));
                                @elseif($c['type']=='datamodal')
                                    $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} .td-label").text());
                                    $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                @elseif($c['type']=='upload')
                                    @if($c['upload_type']=='image')
                                        $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} img").data('label'));
                                    @else
                                        $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} a").data('label'));
                                    @endif
                                    $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
                                @elseif($c['type']=='hidden')
                                    $('#{{$name.$c["name"]}}').val(p.find(".{{$name}}-{{$c['name']}}").val());
                                @else
                                    $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val());
                                @endif
                                @endforeach
                            }

                            function validateForm{{$name}}() {
                                var is_false = 0;
                                $('#panel-form-{{$name}} .required').each(function () {
                                    var v = $(this).val();
                                    if (v == '') {
                                        swal("{{trans('mixtra.alert_warning')}}", "{{trans('mixtra.please_complete_the_form')}}", "warning");
                                        is_false += 1;
                                    }
                                })

                                if (is_false == 0) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }

                            function addToTable{{$name}}() {

                                if (validateForm{{$name}}() == false) {
                                    return false;
                                }

                                var trRow = '<tr class="items-row">';
                                @foreach($form['columns'] as $c)
                                    <?php
                                    $text_right = '';
                                    if($c['type'] == "number") {
                                        $text_right = ' text-right';
                                    }
                                    ?>
                                    @if($c['type']=='select')
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'>" + $('#{{$name.$c["name"]}} option:selected').text() +
                                        "<input type='hidden' class='{{$c['name']}}{{$text_right}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                        "</td>";
                                    @elseif($c['type']=='radio')
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'><span class='td-label'>" + $('.{{$name.$c["name"]}}:checked').val() + "</span>" +
                                        "<input type='hidden' class='{{$c['name']}}{{$text_right}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('.{{$name.$c["name"]}}:checked').val() + "'/>" +
                                        "</td>";
                                    @elseif($c['type']=='datamodal')
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'><span class='td-label'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</span>" +
                                        "<input type='hidden' class='{{$c['name']}}{{$text_right}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                        "</td>";
                                    @elseif($c['type']=='upload')
                                        @if($c['upload_type']=='image')
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'>" +
                                        "<a data-lightbox='roadtrip' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'><img data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' src='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "' width='50px' height='50px'/></a>" +
                                        "<input type='hidden' class='{{$c['name']}}{{$text_right}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                        "</td>";
                                        @else
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'><a data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</a>" +
                                        "<input type='hidden' class='{{$c['name']}}{{$text_right}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                                        "</td>";
                                        @endif
                                    @elseif($c['type']=='hidden')
                                        trRow += "<input type='hidden' class='{{$name}}-{{$c['name']}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>";
                                    @else
                                        trRow += "<td class='{{$c['name']}}{{$text_right}}'><span class='{{$name}}-span-{{$c['name']}}'>" + $('#{{$name.$c["name"]}}').val() + "</span><input type='hidden' class='{{$name}}-{{$c['name']}}' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                                        "</td>";
                                    @endif
                                @endforeach
                                    trRow += "<td width='90px'>" +
                                    "<a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-primary btn-sm'><i class='fas fa-pencil-alt'></i></a> " +
                                    "<a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a></td>";
                                trRow += '</tr>';
                                $('#table-{{$name}} tbody .trNull').remove();
                                if (currentRow == null) {
                                    $("#table-{{$name}} tbody").append(trRow);
                                } else {
                                    currentRow.removeClass('warning');
                                    currentRow.replaceWith(trRow);
                                    currentRow = null;
                                }
                                $('#btn-add-table-{{$name}}').val('+');
                                resetForm{{$name}}();
                            }
                        </script>
                    @endpush

                    <table id='table-{{$name}}' class='table table-striped table-bordered table-responsive'>
                        <thead>
                        <tr>
                            @foreach($form['columns'] as $col)
                                @if($col['type'] != 'hidden')
                                <?php 
                                $minwidth = '';
                                if($col['width'])
                                    $minwidth = 'min-width: '.$col['width'].'px;';
                                ?>
                                <th class="{{$col['type'] == 'number' ? 'text-right': ''}}" style="{{$minwidth}}">{{$col['label']}}</th>
                                @endif
                            @endforeach
                            <th style="min-width:120px;">{{trans('mixtra.action_label')}}</th>
                        </tr>

                        <tr>
                        @foreach($form['columns'] as $col)
                            <?php $name_column = $name.$col['name'];?>
                                @if($col['type']=='radio')
                                <td>
                                    <?php
                                    if($col['dataenum']):
                                    $dataenum = $col['dataenum'];
                                    if (strpos($dataenum, ';') !== false) {
                                        $dataenum = explode(";", $dataenum);
                                    } else {
                                        $dataenum = [$dataenum];
                                    }
                                    array_walk($dataenum, 'trim');
                                    foreach($dataenum as $e=>$enum):
                                    $enum = explode('|', $enum);
                                    if (count($enum) == 2) {
                                        $radio_value = $enum[0];
                                        $radio_label = $enum[1];
                                    } else {
                                        $radio_value = $radio_label = $enum[0];
                                    }
                                    ?>
                                    <label class="radio-inline">
                                        <input type="radio" name="child-{{$col['name']}}"
                                               class='{{ ($e==0 && $col['required'])?"required":""}} {{$name_column}}'
                                               value="{{$radio_value}}"> {{$radio_label}}
                                    </label>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                </td>
                                @elseif($col['type']=='datamodal')
                                <td>
                                    <div id='{{$name_column}}' class="input-group">
                                        <input type="hidden" class="input-id">
                                        <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-sm" onclick="showModal{{$name_column}}()" type="button">
                                                <i class='fa fa-search'></i>
                                            </button>
                                        </div>
                                    </div><!-- /input-group -->

                                    @push('bottom')
                                        <script type="text/javascript">
                                            var url_{{$name_column}} = "{{MITBooster::mainpath('modal-data')}}?table={{$col['datamodal_table']}}&columns=id,{{$col['datamodal_columns']}}&name_column={{$name_column}}&where={{urlencode($col['datamodal_where'])}}&select_to={{ urlencode($col['datamodal_select_to']) }}&columns_name_alias={{urlencode($col['datamodal_columns_alias'])}}&orderby={{ urlencode($col['datamodal_orderby']) }}";
                                            var url_is_setted_{{$name_column}} = false;

                                            function showModal{{$name_column}}() {
                                                if (url_is_setted_{{$name_column}} == false) {
                                                    url_is_setted_{{$name_column}} = true;
                                                    //alert(url_{{$name_column}});
                                                    $('#iframe-modal-{{$name_column}}').attr('src', url_{{$name_column}});
                                                }
                                                $('#modal-datamodal-{{$name_column}}').modal('show');
                                            }

                                            function hideModal{{$name_column}}() {
                                                $('#modal-datamodal-{{$name_column}}').modal('hide');
                                            }

                                            function selectAdditionalData{{$name_column}}(select_to_json) {
                                                $.each(select_to_json, function (key, val) {
                                                    //alert(val);
                                                    //console.log('#' + key + ' = ' + val);
                                                    if (key == 'datamodal_id') {
                                                        $('#{{$name_column}} .input-id').val(val);
                                                    }
                                                    if (key == 'datamodal_label') {
                                                        $('#{{$name_column}} .input-label').val(val);
                                                    }
                                                    $('#{{$name}}' + key).val(val).trigger('change');
                                                })
                                                if (typeof defaultValue !== "undefined") { 
                                                    defaultValue();
                                                }
                                                hideModal{{$name_column}}();
                                            }
                                        </script>
                                    @endpush

                                    <div id='modal-datamodal-{{$name_column}}' class="modal fade" tabindex="-1" role="dialog">
                                        <div class="modal-dialog {{ $col['datamodal_size']=='large'?'modal-lg':'' }} " role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"><i class='fa fa-search'></i> {{trans('mixtra.datamodal_browse_data')}} {{$col['label']}}</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe id='iframe-modal-{{$name_column}}' style="border:0;height: 430px;width: 100%"
                                                            src=""></iframe>
                                                </div>

                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </td>
                                @elseif($col['type']=='number')
                                <td>
                                    <input id='{{$name_column}}' type='text'
                                           {{ ($col['min'])?"min='".$col['min']."'":"" }} {{ ($col['max'])?"max='$col[max]'":"" }} name='child-{{$col["name"]}}'
                                           class='form-control decimal text-right {{$col['required']?"required":""}}'
                                            {{($col['readonly']===true)?"readonly":""}}
                                    />
                                    
                                    @push('bottom')
                                    <script type="text/javascript">
                                        @if($readonly == '')
                                            $('#{{$name_column}}').blur(function()
                                            {
                                                var value = $(this).val();
                                                $(this).val(numberToString(value, {{$col['decimals']}}));
                                            });

                                            function setNumberFormat{{$name_column}}(value, object)
                                            {
                                                object.val(numberToString(value, {{$col['decimals']}}));
                                            }
                                        @endif
                                    </script>
                                    @endpush
                                </td>
                                @elseif($col['type']=='textarea')
                                <td>
                                    <textarea id='{{$name_column}}' name='child-{{$col["name"]}}'
                                              class='form-control {{$col['required']?"required":""}}' {{($col['readonly']===true)?"readonly":""}} ></textarea>
                                </td>
                                @elseif($col['type']=='upload')
                                <td>
                                    <div id='{{$name_column}}' class="input-group">
                                        <input type="hidden" class="input-id">
                                        <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" id="btn-upload-{{$name_column}}" onclick="showFakeUpload{{$name_column}}()" type="button">
                                                <i class='fa fa-search'></i> 
                                            </button>
                                        </span>
                                    </div><!-- /input-group -->

                                    <div id="loading-{{$name_column}}" class='text-info' style="display: none">
                                        <i class='fa fa-spin fa-spinner'></i> {{trans('mixtra.text_loading')}}
                                    </div>

                                    <input type="file" id='fake-upload-{{$name_column}}' style="display: none">
                                    @push('bottom')
                                        <script type="text/javascript">
                                            var file;
                                            var filename;
                                            var is_uploading = false;

                                            function showFakeUpload{{$name_column}}() {
                                                if (is_uploading) {
                                                    return false;
                                                }

                                                $('#fake-upload-{{$name_column}}').click();
                                            }

                                            // Add events
                                            $('#fake-upload-{{$name_column}}').on('change', prepareUpload{{$name_column}});

                                            // Grab the files and set them to our variable
                                            function prepareUpload{{$name_column}}(event) {
                                                var max_size = {{ ($col['max'])?:2000 }};
                                                file = event.target.files[0];

                                                var filesize = Math.round(parseInt(file.size) / 1024);

                                                if (filesize > max_size) {
                                                    sweetAlert('{{trans("mixtra.alert_warning")}}', '{{trans("mixtra.your_file_size_is_too_big")}}', 'warning');
                                                    return false;
                                                }

                                                filename = $('#fake-upload-{{$name_column}}').val().replace(/C:\\fakepath\\/i, '');
                                                var extension = filename.split('.').pop().toLowerCase();
                                                var img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                                var available_extension = "{{config('mixtra.UPLOAD_TYPES')}}".split(",");
                                                var is_image_only = {{ ($col['upload_type'] == 'image')?"true":"false" }};

                                                if (is_image_only) {
                                                    if ($.inArray(extension, img_extension) == -1) {
                                                        sweetAlert('{{trans("mixtra.alert_warning")}}', '{{trans("mixtra.your_file_extension_is_not_allowed")}}', 'warning');
                                                        return false;
                                                    }
                                                } else {
                                                    if ($.inArray(extension, available_extension) == -1) {
                                                        sweetAlert('{{trans("mixtra.alert_warning")}}', '{{trans("mixtra.your_file_extension_is_not_allowed")}}!', 'warning');
                                                        return false;
                                                    }
                                                }


                                                $('#{{$name_column}} .input-label').val(filename);

                                                $('#loading-{{$name_column}}').fadeIn();
                                                $('#btn-add-table-{{$name}}').addClass('disabled');
                                                $('#btn-upload-{{$name_column}}').addClass('disabled');
                                                is_uploading = true;

                                                //Upload File To Server
                                                uploadFiles{{$name_column}}(event);
                                            }

                                            function uploadFiles{{$name_column}}(event) {
                                                event.stopPropagation(); // Stop stuff happening
                                                event.preventDefault(); // Totally stop stuff happening

                                                // START A LOADING SPINNER HERE

                                                // Create a formdata object and add the files
                                                var data = new FormData();
                                                data.append('userfile', file);

                                                $.ajax({
                                                    url: '{{MITBooster::mainpath("upload-file")}}',
                                                    type: 'POST',
                                                    data: data,
                                                    cache: false,
                                                    processData: false, // Don't process the files
                                                    contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                                    success: function (data, textStatus, jqXHR) {
                                                        console.log(data);
                                                        $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                        $('#loading-{{$name_column}}').hide();
                                                        $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                        is_uploading = false;

                                                        var basename = data.split('/').reverse()[0];
                                                        $('#{{$name_column}} .input-label').val(basename);

                                                        $('#{{$name_column}} .input-id').val(data);
                                                    },
                                                    error: function (jqXHR, textStatus, errorThrown) {
                                                        $('#btn-add-table-{{$name}}').removeClass('disabled');
                                                        $('#btn-upload-{{$name_column}}').removeClass('disabled');
                                                        is_uploading = false;
                                                        // Handle errors here
                                                        console.log('ERRORS: ' + textStatus);
                                                        // STOP LOADING SPINNER
                                                        $('#loading-{{$name_column}}').hide();
                                                    }
                                                });
                                            }

                                        </script>
                                    @endpush
                                </td>
                                @elseif($col['type']=='select')
                                <td>
                                    @if($col['parent_select'])
                                        @push('bottom')
                                            <script type="text/javascript">
                                                $(function () {
                                                    $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2("destroy");

                                                    $('#{{$name.$col['parent_select']}}, input:radio[name={{$name.$col['parent_select']}}]').change(function () {
                                                        var $current = $("#{{$name.$col['name']}}");
                                                        var parent_id = $(this).val();
                                                        var fk_name = "{{$col['parent_select']}}";
                                                        var fk_value = $('#{{$name.$col['parent_select']}}').val();
                                                        var datatable = "{{$col['datatable']}}".split(',');
                                                        var datatableWhere = "{{$col['datatable_where']}}";
                                                        var table = datatable[0].trim('');
                                                        var label = datatable[1].trim('');
                                                        var value = "{{$value}}";

                                                        if (fk_value != '') {
                                                            $current.html("<option value=''>{{trans('mixtra.text_loading')}} {{$col['label']}}");
                                                            $.get("{{MITBooster::mainpath('data-table')}}?table=" + table + "&label=" + label + "&fk_name=" + fk_name + "&fk_value=" + fk_value + "&datatable_where=" + encodeURI(datatableWhere), function (response) {
                                                                if (response) {
                                                                    $current.html("<option value=''>{{$default}}");
                                                                    $.each(response, function (i, obj) {
                                                                        var selected = (value && value == obj.select_value) ? "selected" : "";
                                                                        $("<option " + selected + " value='" + obj.select_value + "'>" + obj.select_label + "</option>").appendTo("#{{$name.$col['name']}}");
                                                                    });
                                                                    $current.trigger('change');
                                                                }
                                                            });
                                                        } else {
                                                            $current.html("<option value=''>{{$default}}");
                                                        }
                                                    });

                                                    $('#{{$name.$col['parent_select']}}').trigger('change');
                                                    $("#{{$name.$col['name']}}").trigger('change');

                                                    $("#{{$name.$col['parent_select']}} , #{{$name.$col['name']}}").select2();

                                                })
                                            </script>
                                        @endpush
                                    @endif

                                    <select id='{{$name_column}}' name='child-{{$col["name"]}}'
                                            class='form-control select2 {{$col['required']?"required":""}}'
                                            {{($col['readonly']===true)?"readonly":""}}
                                    >
                                        <?php
                                        if (empty($col['cleardefault'])) {
                                        ?>
                                            <option value=''>{{trans('mixtra.text_prefix_option')}} {{$col['label']}}</option>
                                        <?php
                                        }else{
                                            $dflt = explode('|', $col['cleardefault']);
                                        ?>
                                            <option value='{{$dflt[0]}}'>{{$dflt[1]}}</option>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if ($col['datatable']) {
                                            $tableJoin = explode(',', $col['datatable'])[0];
                                            $titleField = explode(',', $col['datatable'])[1];
                                            if (! $col['datatable_where']) {
                                                $data = MITBooster::get($tableJoin, NULL, "$titleField ASC");
                                            } else {
                                                $data = MITBooster::get($tableJoin, $col['datatable_where'], "$titleField ASC");
                                            }
                                            foreach ($data as $d) {
                                                echo "<option value='$d->id'>".$d->$titleField."</option>";
                                            }
                                        } else {
                                            $data = $col['dataenum'];
                                            foreach ($data as $d) {
                                                $enum = explode('|', $d);
                                                if (count($enum) == 2) {
                                                    $opt_value = $enum[0];
                                                    $opt_label = $enum[1];
                                                } else {
                                                    $opt_value = $opt_label = $enum[0];
                                                }
                                                echo "<option value='$opt_value'>$opt_label</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                @elseif($col['type']=='hidden')
                                    <input type="{{$col['type']}}" id="{{$name.$col["name"]}}" name="child-{{$name.$col["name"]}}"
                                           value="{{$col["value"]}}">
                               @elseif($col['type']=='date')
                                <td>
                                    <input id='{{$name_column}}' type='text' {{($col['readonly']===true)?"readonly":""}}
                                           {{ ($col['max'])?"maxlength='".$col['max']."'":"" }} name='child-{{$col["name"]}}'
                                           class='form-control  not-focus input_date {{$col['required']?"required":""}}'
                                    />
                                </td>
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
                                @elseif($col['type']=='datetime')
                                <td>
                                    
                                    <input id='{{$name_column}}' type='text' {{($col['readonly']===true)?"readonly":""}} 
                                           {{ ($col['max'])?"maxlength='".$col['max']."'":"" }} name='child-{{$col["name"]}}'
                                           class='form-control  not-focus datetimepicker {{$col['required']?"required":""}}'
                                    />
                                </td>
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
                               @else
                                <td>
                                    <input id='{{$name_column}}' type='text'
                                           {{ ($col['max'])?"maxlength='".$col['max']."'":"" }} name='child-{{$col["name"]}}'
                                           class='form-control {{$col['required']?"required":""}}'
                                            {{($col['readonly']===true)?"readonly":""}}
                                    />
                                </td>
                                @endif
                        @endforeach
                            <td>
                                <input type='button' id='btn-add-table-{{$name}}' class='btn btn-primary' onclick="addToTable{{$name}}()"
                                           value='+'/>

                            </td>
                        </tr>

                        </thead>
                        <tbody>

                        <?php
                        $columns_tbody = [];
                        $data_child = DB::table($form['table'])->where($form['foreign_key'], $id);
                        foreach ($form['columns'] as $i => $c) {
                            $data_child->addselect($form['table'].'.'.$c['name']);

                            if ($c['type'] == 'datamodal') {
                                $datamodal_title = explode(',', $c['datamodal_columns'])[0];
                                $datamodal_table = $c['datamodal_table'];
                                $data_child->join($c['datamodal_table'], $c['datamodal_table'].'.id', '=', $c['name']);
                                $data_child->addselect($c['datamodal_table'].'.'.$datamodal_title.' as '.$datamodal_table.'_'.$datamodal_title);
                            } elseif ($c['type'] == 'select') {
                                if ($c['datatable']) {
                                    $join_table = explode(',', $c['datatable'])[0];
                                    $join_field = explode(',', $c['datatable'])[1];
                                    $data_child->leftjoin($join_table, $join_table.'.id', '=', $form['table'].'.'.$c['name']);
                                    $data_child->addselect($join_table.'.'.$join_field.' as '.$join_table.'_'.$join_field);
                                }
                            }
                        }

                        $data_child = $data_child->orderby($form['table'].'.id', 'asc');
                        $data_child = $data_child->get();
                        foreach($data_child as $d):
                        ?>
                        <tr class='{{$name}}-row'>
                            @foreach($form['columns'] as $col)
                                    <?php
                                    if ($col['type'] == 'select') {
                                        echo "<td class='".$col['name']."'>";
                                        if ($col['datatable']) {
                                            $join_table = explode(',', $col['datatable'])[0];
                                            $join_field = explode(',', $col['datatable'])[1];
                                            echo "<span class='td-label'>";
                                            echo $d->{$join_table.'_'.$join_field};
                                            echo "</span>";
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        }
                                        if ($col['dataenum']) {
                                            echo "<span class='td-label'>";
                                            echo $d->{$col['name']};
                                            echo "</span>";
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        }
                                        echo "</td>";
                                    } elseif ($col['type'] == 'datamodal') {
                                        echo "<td class='".$col['name']."'>";
                                        $datamodal_title = explode(',', $col['datamodal_columns'])[0];
                                        $datamodal_table = $col['datamodal_table'];
                                        echo "<span class='td-label'>";
                                        echo $d->{$datamodal_table.'_'.$datamodal_title};
                                        echo "</span>";
                                        echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        echo "</td>";
                                    } elseif ($col['type'] == 'upload') {
                                        echo "<td class='".$col['name']."'>";
                                        $filename = basename($d->{$col['name']});
                                        if ($col['upload_type'] == 'image') {
                                            echo "<a href='".asset($d->{$col['name']})."' data-lightbox='roadtrip'><img data-label='$filename' src='".asset($d->{$col['name']})."' width='50px' height='50px'/></a>";
                                            echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        } else {
                                            echo "<a data-label='$filename' href='".asset($d->{$col['name']})."'>$filename</a>";
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        }
                                        echo "</td>";
                                    } elseif ($col['type'] == 'hidden') {
                                        echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                    } elseif ($col['type'] == 'date') {
                                        echo "<td class='".$col['name']."'>";
                                        echo "<span class='".$name."-span-".$col['name']."'>";
                                        if($d->{$col['name']} != null)
                                            echo date('Y-m-d',strtotime($d->{$col['name']}));
                                        echo "</span>";
                                        if($d->{$col['name']} != null)
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".date('Y-m-d',strtotime($d->{$col['name']}))."'/>";
                                        else
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value=''/>";
                                        echo "</td>";
                                    } elseif ($col['type'] == 'datetime') {
                                        echo "<td class='".$col['name']."'>";
                                        echo "<span class='".$name."-span-".$col['name']."'>";
                                        if($d->{$col['name']} != null)
                                            echo date('Y-m-d H:i:s',strtotime($d->{$col['name']}));
                                        echo "</span>";
                                        if($d->{$col['name']} != null)
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".date('Y-m-d H:i:s',strtotime($d->{$col['name']}))."'/>";
                                        else
                                            echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value=''/>";
                                        echo "</td>";
                                    } else {
                                        if($col['type'] == 'number') {
                                        echo "<td class='".$col['name']." text-right'>";
                                        }else{
                                        echo "<td class='".$col['name']."'>";
                                        }   
                                        echo "<span class='td-label'>";
                                        if($col['type'] == 'number') {
                                            echo number_format($d->{$col['name']}, $col['decimal']);
                                        }else{
                                            echo $d->{$col['name']};
                                        }
                                        echo "</span>";
                                        echo "<input type='hidden' class='".$name."-".$col['name']."' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
                                        echo "</td>";
                                    }
                                    ?>
                            @endforeach
                            <td width="100px">
                                <a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-primary btn-sm'><i
                                            class='fas fa-pencil-alt'></i></a>
                                <a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-sm'><i
                                            class='fa fa-trash'></i></a>
                            </td>
                        </tr>

                        <?php endforeach;?>

                        @if(count($data_child)==0)
                            <tr class="trNull">
                                <td colspan="{{count($form['columns'])+1}}" align="center">{{trans('mixtra.table_data_not_found')}}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>


        </div>


    @else

        <div style="border:1px dashed #c41300;padding:20px;margin:20px">
            <span style="background: yellow;color: black;font-weight: bold">CHILD {{$name}} : COLUMNS ATTRIBUTE IS MISSING !</span>
            <p>You need to set the "columns" attribute manually</p>
        </div>
    @endif
</div>
