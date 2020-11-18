@extends('mitbooster::layouts.admin')
@section('content')
	<form id='form-filter' method='get' action='{{ MITBooster::mainPath("allocation") }}' class="form-horizontal m-b-10">
		<div class="form-group row">
			<label class="col-form-label font-weight-bold col-sm-2 text-right">Start Date:</label>
			<div class="col-sm-4">
				<input type="text" name="start_date" id="start_date" value="{{ $param['start_date'] ?? date('Y/m/d',strtotime($param['start_date'])) }}" class="form-control form-control-sm notfocus datetimepicker" />
			</div>
            <label class="col-form-label font-weight-bold col-sm-2 text-right">Cell:</label>
			<div class="col-sm-4">
				<select class='form-control form-control-sm' id="cell" name="cell">
                    <option value='ALL' <?php if($param['cell'] == 'ALL') echo 'selected' ?>>ALL</option>
					<option value='CELL 1' <?php if($param['cell'] == 'CELL 1') echo 'selected' ?>>CELL 1</option>
					<option value='CELL 2' <?php if($param['cell'] == 'CELL 2') echo 'selected' ?>>CELL 2</option>
					<option value='CELL 3' <?php if($param['cell'] == 'CELL 3') echo 'selected' ?>>CELL 3</option>
				</select>
			</div>
        </div>

        <div class="form-group row">
            <label class="col-form-label font-weight-bold col-sm-2 text-right">End Date:</label>
			<div class="col-sm-4">
				<input type="text" name="end_date" id="end_date" value="{{ $param['end_date'] ?? date('Y/m/d',strtotime($param['end_date'])) }}" class="form-control form-control-sm notfocus datetimepicker" />
			</div>
            <label class="col-form-label font-weight-bold col-sm-2 text-right">Scale:</label>
            <div class="col-sm-4">
                <select class='select2 form-control-sm custom-select' id="scale" name="scale">
                    <option value='1' <?php if($param['scale'] == '1') echo 'selected' ?>>DAILY</option>
                    <option value='2' <?php if($param['scale'] == '2') echo 'selected' ?>>WEEKLY</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-6">
                <input type="submit" class="btn btn-astari btn-sm" value="Search">
                @if($param['cell'] != 'ALL' && MITBooster::myPrivilegeId() != 7 && MITBooster::myPrivilegeId() != 10 )
                <input type="button" class="btn btn-primary btn-sm" id="btnAllocation" value="Allocation" onclick="refresh();">
                @endif
                @if($param['type'] == 1)
                <input type="button" class="btn btn-secondary btn-sm" value="Close All" onclick="closeAll()">
                <input type="button" class="btn btn-secondary btn-sm" value="Open All" onclick="openAll()">
                @endif
            </div>
            <label class="col-form-label font-weight-bold col-sm-2 text-right">Type:</label>
            <div class="col-sm-4">
                <select class='select2 form-control-sm custom-select' id="type" name="type">
                    <option value='1' <?php if($param['type'] == '1') echo 'selected' ?>>DETAIL</option>
                    <option value='2' <?php if($param['type'] == '2') echo 'selected' ?>>SUMMARY</option>
                </select>
            </div>
        </div>
    </form>
	@include("capacity.gantt")
@endsection

@push('head')
    <!-- <link rel='stylesheet' href='<?php echo asset("assets/vendor/datepicker/css/bootstrap-datepicker.min.css")?>'/> -->
    <link rel='stylesheet' href='<?php echo asset("assets/vendor/datetimepicker/css/bootstrap-datetimepicker.css")?>'/>
    <link rel='stylesheet' href='<?php echo asset("assets/vendor/select2/dist/css/select2.min.css")?>'/>
    <style type="text/css">
        .select2-container .select2-selection--single {
            height: 28px
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 1px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }
        .btn {
            min-width: 80px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3c8dbc !important;
            border-color: #367fa9 !important;
            color: #fff !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
        }
    </style>
@endpush

@push('bottom')
    <script src='<?php echo asset("assets/vendor/select2/dist/js/select2.full.min.js")?>'></script>
	<!-- @if (App::getLocale() != 'en')
        <script src="{{ asset ('assets/vendor/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
            charset="UTF-8"></script>
    @else
        <script src="{{ asset ('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"
            charset="UTF-8"></script>
    @endif -->
    <script src='{{asset("/assets/js/jquery-sortable-min.js")}}'></script>

    <script src="{{ asset ('assets/vendor/datetimepicker/js/bootstrap-datetimepicker.js') }}" charset="UTF-8"></script>

	<script>
		var lang = '{{App::getLocale()}}';
        $(function () {
            $('.inputMoney').priceFormat({!! json_encode(array_merge(array(
		            'prefix' 				=> '',
		            'thousandsSeparator'    => ',',
                    'centsSeparator'        => '.',
		            'centsLimit'          	=> '0',
		            'clearOnEmpty'         	=> false,
		        )
			)) !!});

            $('#cell').select2();
            $('#scale').select2();
            $('#type').select2();

            $('.datetimepicker').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });
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

            $('.open-datetimepicker').click(function () {
                $(this).next('.input_date').datepicker('show');
            });

            $(".draggable-menu").sortable();
        });

        function parseStringFloat(text) {
            if(text != null) {
                text = text.replace(/,/g, '');
                return parseFloat(text);
            }
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

		function downtime($id) {
        	$('#form-down-time').attr('action','{{MITBooster::mainPath("start-down-time")}}/'+$id);
            $('#down-time').modal('show');
		}

		function stopAssembly($id) {
        	$('#form-assembly').attr('action','{{MITBooster::mainPath("stop-all")}}/'+$id);
            $('#assembly').modal('show');
		}

		function breakAssembly($id) {
        	$('#form-assembly').attr('action','{{MITBooster::mainPath("break-all")}}/'+$id);
            $('#assembly').modal('show');
		}

        function refresh() {
            var item_rows = $('.gantt_row').map(function() {
                return $(this);
            }).get();

            var id = null;
            item_rows.forEach(function(row) {
                var col1 = row.find("[data-column-index='0'] .gantt_tree_content").text();
                if(!col1.startsWith("PO")) {
                    if(id != null) {
                        // console.log(col1);
                        $.ajax({
                            url: "{{MITBooster::mainpath('latest')}}",
                            type: 'GET',
                            async: false,
                            data: {
                                'id_before': id,
                                'id': row.attr('task_id')
                            },
                            contentType: 'application/json',
                            success: function (data, textStatus, jqXHR) {
                                // console.log(data);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log('ERRORS: ' + textStatus);
                                                                
                            }
                        });
                    }
                    id = row.attr('task_id');
                }
            });

            $('#form-filter').submit();
        }
    </script>
@endpush