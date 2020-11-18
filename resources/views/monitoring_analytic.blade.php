@extends('mitbooster::layouts.app')
@section('body-class','monitoring')

@section('title')
    <title>
    {{ ($page_title)?MITBooster::getSetting('appname').' : '.strip_tags($page_title) : "Admin Area" }}
    </title>
@endsection

@section('admin_css')
    <!--JQuery UI -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/jqueryui/jquery-ui.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">
    <style>
        html, body {
            padding: 0px;
        }

        .multiselect-container {
            width: 350px;
        }

        .monitoring {
            padding-left: 10px;
            padding-right: 10px;

        }
        .title {
            font-size: 20px;
        }
        .title {
            font-size: 18px;
        }
        .card-body {
            padding: 10px;
        }

        .prod_img {
            width: 100%;
            height: 100%;
            margin: auto;
            background-size:cover;
        }

        .total {
            border-top: 1px solid #d9d9d9;
        }

        .header {
            border-bottom: 1px solid #d9d9d9;
        }

        .right-border {
            border-right: 1px solid #d9d9d9;
        }

        .fixedElement {
            position: fixed;
            width: 100%;
            z-index: 100;
            padding-right: 20px;
            background-color: #edf1f5;
        }

        .danger {
            background-color: #a80e19;
            color: #ffffff; 
            padding: 1px 8px 1px 8px;
            border-radius: 25px;
        }

        .warning {
            background-color: #cf7825;
            color: #ffffff; 
            padding: 1px 8px 1px 8px;
            border-radius: 25px;
        }

        .success {
            background-color: #00642c;
            color: #ffffff; 
            padding: 1px 8px 1px 8px;
            border-radius: 25px;
        }

        .btn-status {
            padding: 2px;
            width: 50px;
        }

        .p-8 {
            padding: 8px;
        }

        @media (max-width:1173px) {
            .workorder-chart {
                display: none !important;
            }
            
        }

        @media (max-width:1293px) {
            .order-chart {
                display: none !important;
            }
        }

    </style>
@endsection

@section('admin_js')
    <script src="{{ asset('assets/vendor/jqueryui/jquery-ui.js') }}"></script>
    <script src="{{ asset ('assets/vendor/Chart.js/Chart.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset ('assets/vendor/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js') }}" charset="UTF-8"></script>
    <script src="{{ asset ('assets/vendor/jquery-sparkline/jquery.sparkline.min.js') }}" charset="UTF-8"></script>
    
    <!-- Multiselect -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

    <script type="text/javascript">
        var lang = '{{App::getLocale()}}';

        $('.input_date').datepicker({
            format: 'yyyy/mm/dd',
            language: lang
        });

        $('.open-datetimepicker').click(function () {
            $(this).next('.input_date').datepicker('show');
        });


        var reloading;

        function toggleAutoRefresh(cb) {
            reloading = cb.checked;
            if(reloading) {
                $('.date-filter').hide();
                $('#start_date').val('{{$start_date}}');
                $('#end_date').val('{{$end_date}}');
            }else
                $('.date-filter').show();
        }

        $(function () {
            reloading = true;
            $('.date-filter').hide();
            setInterval(function() {
                if (reloading && !loading) {
                    refreshData();
                }
            },10000);
        });

        $(document).ready(function() {
            refreshData();
            $('#rejectProblems').multiselect();
            $('#assProblems').multiselect();
            $('#dtProblems').multiselect();
        });

        function refreshData() {
            totalEffective();
            weeklyStatus();
            dailyStatus();
            productionPlan(); 
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        function numberToString(text, decimals) {
            text = text.toString().replace(/,/g, '');
            var number = parseFloat(Math.round(text * 100) / 100).toFixed(decimals);
            
            var parts = number.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    		return parts.join(".");
            
            // return $.number(number, 2, '.', ',');
            // return number.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }

        function totalEffective() {
            var our_lines_id = {{$our_lines_id}};
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('total-effective')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': our_lines_id,
                },
                success:function(data){
                    // console.log(data);

                    var result = "";
                    if(data.effective < 0)
                        result = '<h2 class="text-danger">'+formatNumber(data.effective)+' <i class="fa fa-sort-amount-down"></i></span></h2>';
                    else
                        result = '<h2 class="text-success">'+formatNumber(data.effective)+' <i class="fa fa-sort-amount-up"></i></span></h2>';
                    $('.effective').html(result);

                    $('.final-po').html(formatNumber(data.final_po));
                    $('.final-qty').html(formatNumber(data.final_qty));
                    //$('.final-date').html(formatNumber(data.final_date));

                    $('.confirm-po').html(formatNumber(data.confirm_po));
                    $('.confirm-qty').html(formatNumber(data.confirm_qty));
                    //$('.confirm-date').html(formatNumber(data.confirm_date));

                    $('.booking-po').html(formatNumber(data.booking_po));
                    $('.booking-qty').html(formatNumber(data.booking_qty));
                    //$('.booking-date').html(formatNumber(data.booking_date));

                    $('.total-po').html(formatNumber(data.total_po));
                    $('.total-qty').html(formatNumber(data.total_qty));
                    //$('.total-date').html(formatNumber(data.total_date));
                },
            });
        }

        function weeklyStatus() {
            var our_lines_id = {{$our_lines_id}};
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('weekly-status')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': our_lines_id,
                },
                success:function(data){
                    // console.log(data);

                    var i = 0;
                    var com_plan = data[0].plan;
                    var com_act = data[0].actual;
                    var com_cot = data[0].cot;

                    for(var i=0;i<4;i++) {
                        $('.week'+(i+1)+'-name').html('WEEK #'+data[4-i].name);
                        $('.week'+(i+1)+'-plan').html(formatNumber(data[4-i].plan));
                        $('.week'+(i+1)+'-act').html(formatNumber(data[4-i].actual));
                        $('.week'+(i+1)+'-cot').html(formatNumber(data[4-i].cot));
                        $('.week'+(i+1)+'-perc1').html(formatNumber(data[4-i].perc1));
                        $('.week'+(i+1)+'-perc2').html(formatNumber(data[4-i].perc2));

                        com_plan += data[4-i].plan;
                        com_act += data[4-i].actual;
                        com_cot += data[4-i].cot;
                        com_perc1 = com_plan == 0 ? 0:com_act/com_plan*100;
                        com_perc2 = com_act == 0 ? 0:com_cot/com_act*100;
                        // console.log(com_perc1);

                        $('.week'+(i+1)+'-com-plan').html(formatNumber(com_plan));
                        $('.week'+(i+1)+'-com-act').html(formatNumber(com_act));
                        $('.week'+(i+1)+'-com-cot').html(formatNumber(com_cot));
                        $('.week'+(i+1)+'-com-perc1').html(numberToString(com_perc1,2));
                        $('.week'+(i+1)+'-com-perc2').html(numberToString(com_perc2,2));
                    }

                    $('.week5-plan').html(formatNumber(data[5].plan));
                    $('.week5-act').html(formatNumber(data[5].actual));
                    $('.week5-perc1').html(formatNumber(data[5].perc1));
                    $('.week5-cot').html(formatNumber(data[5].cot));
                    $('.week5-perc2').html(formatNumber(data[5].perc2));
                },
            });
        }

        function dailyStatus() {
            var our_lines_id = {{$our_lines_id}};
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('daily-status')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': our_lines_id,
                },
                success:function(data){
                    // console.log(data);

                    var i = 0;
                    for(var i=0;i<5;i++) {
                        $('.day'+(i+1)+'-name').html('WEEK #'+data[i].name);
                        $('.day'+(i+1)+'-plan').html(formatNumber(data[i].plan));
                        $('.day'+(i+1)+'-act').html(formatNumber(data[i].actual));
                        $('.day'+(i+1)+'-perc1').html(formatNumber(data[i].perc1));
                        $('.day'+(i+1)+'-cot').html(formatNumber(data[i].cot));
                        $('.day'+(i+1)+'-perc2').html(formatNumber(data[i].perc2));

                        $('.day'+(i+1)+'-com-plan').html(formatNumber(data[i].com_plan));
                        $('.day'+(i+1)+'-com-act').html(formatNumber(data[i].com_act));
                        $('.day'+(i+1)+'-com-perc1').html(formatNumber(data[i].com_perc1));
                        $('.day'+(i+1)+'-com-cot').html(formatNumber(data[i].com_cot));
                        $('.day'+(i+1)+'-com-perc2').html(formatNumber(data[i].com_perc2));
                    }

                    $('.day6-plan').html(formatNumber(data[5].plan));
                    $('.day6-act').html(formatNumber(data[5].actual));
                    $('.day6-perc1').html(formatNumber(data[5].perc1));
                    $('.day6-cot').html(formatNumber(data[5].cot));
                    $('.day6-perc2').html(formatNumber(data[5].perc2));
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('ERRORS: ' + textStatus);

                }
            });
		}

        function productionPlan() {
            var our_lines_id = {{$our_lines_id}};
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('production-plan')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': our_lines_id,
                    'start_date': start_date,
                    'end_date': end_date,
                },
                success:function(data){
                    // console.log(data);

                    $("#production-body").empty();
                    var complete = $('#complete').prop('checked');

                    data.forEach(function(item, index) {
                        if(item.qty <= item.result && complete) return;
                        
                        var customer_po = item.customer_po;
                        if(item.retailer_po != null)
                            customer_po  = item.retailer_po;
                        var qty = "&nbsp;";
                        if(item.result > 0)
                            qty = formatNumber(parseFloat(item.result));
                        if(item.rejected > 0)
                            qty = qty + ' <span class="danger">'+formatNumber(parseFloat(item.rejected))+'</span>';

                        var speed = "&nbsp;";
                        if(item.speed3 > 0) {
                            if(parseFloat(item.speed3) > parseFloat(item.speed))
                                speed = '<span class="success">'+formatNumber(Math.round(item.speed3))+'</span>';
                            else if(parseFloat(item.speed3) < parseFloat(item.speed))
                                speed = '<span class="danger">'+formatNumber(Math.round(item.speed3))+'</span>';
                            else
                                speed = Math.round(item.speed3);
                        }

                        var work_hour = "&nbsp;";     
                        if(item.work_hour3 > 0) {
                            if(parseFloat(item.work_hour3).toFixed(2) < parseFloat(item.work_hour).toFixed(2)) {
                                work_hour = '<span class="success">'+parseFloat(item.work_hour3).toFixed(2)+'</span>';
                                if(item.actual_end != null)
                                    work_hour = '<span class="success">'+parseFloat(item.work_hour3).toFixed(2)+'</span>';
                            }
                            else if(parseFloat(item.work_hour3).toFixed(2) > parseFloat(item.work_hour).toFixed(2)) {
                                work_hour = '<span class="text-danger">'+parseFloat(item.work_hour3).toFixed(2)+'</span>';
                                if(item.actual_end != null)
                                    work_hour = '<span class="danger">'+parseFloat(item.work_hour3).toFixed(2)+'</span>';
                            }
                            else
                                work_hour = parseFloat(item.work_hour3).toFixed(2);
                        }

                        var change_over = "&nbsp;";     
                        if(item.co_hour > 0) {
                            if(parseFloat(item.co_hour).toFixed(2) > 0.2)
                                change_over = '<span class="text-danger">'+parseFloat(item.co_hour).toFixed(2)+'</span>';
                            else
                                change_over = parseFloat(item.co_hour).toFixed(2);
                        }

                        var down_time = "&nbsp;";     
                        if(item.dt_hour > 0)
                            down_time = '<span class="text-danger">'+parseFloat(item.dt_hour).toFixed(2)+'</span>';
                        
                        var av = "";
                        if(item.av>90) av = "success";
                        else if(item.av>85) av = "warning";
                        else if(item.av>0) av = "danger";

                        var ar = "";
                        if(item.ar>90) ar = "success";
                        else if(item.ar>85) ar = "warning";
                        else if(item.ar>0) ar = "danger";

                        var rft = "#FFFFFF";
                        if(item.rft>98) rft = "success";
                        else if(item.rft>95) rft = "warning";
                        else if(item.rft>0) rft = "danger";

                        var oee = "#FFFFFF";
                        if(item.oee>90) oee = "success";
                        else if(item.oee>85) oee = "warning";
                        else if(item.oee>0) oee = "danger";

                        var mould = "<i class='fa fa-check'></i>";
                        if(item.molding_start == null)
                            mould = '<a href="javascript:" class="btn btn-success btn-sm btn-status" onclick="setStatus(\'moulding\',\'start\','+item.id+',this)"><i class="fa fa-play"></i><br/>START</a>';
                        else if(item.molding_end == null)
                            mould = '<a href="javascript:" class="btn btn-danger btn-sm btn-status" onclick="setStatus(\'moulding\',\'stop\','+item.id+',this)"><i class="fa fa-stop"></i><br/>STOP</a>';
                        
                        var back = "<i class='fa fa-check'></i>";
                        if(item.backboard_start == null)
                            back = '<a href="javascript:" class="btn btn-success btn-sm btn-status" onclick="setStatus(\'backboard\',\'start\','+item.id+',this)"><i class="fa fa-play"></i><br/>START</a>';
                        else if(item.backboard_end == null)
                            back = '<a href="javascript:" class="btn btn-danger btn-sm btn-status" onclick="setStatus(\'backboard\',\'stop\','+item.id+',this)"><i class="fa fa-stop"></i><br/>STOP</a>';
                        
                        var glass = "<i class='fa fa-check'></i>";
                        if(item.glass_start == null)
                            glass = '<a href="javascript:" class="btn btn-success btn-sm btn-status" onclick="setStatus(\'glass\',\'start\','+item.id+',this)"><i class="fa fa-play"></i><br/>START</a>';
                        else if(item.glass_end == null)
                            glass = '<a href="javascript:" class="btn btn-danger btn-sm btn-status" onclick="setStatus(\'glass\',\'stop\','+item.id+',this)"><i class="fa fa-stop"></i><br/>STOP</a>';

                        var acce = "<i class='fa fa-check'></i>";
                        if(item.accessories_start == null)
                            acce = '<a href="javascript:" class="btn btn-warning btn-sm btn-status" onclick="setStatus(\'accessories\',\'start\','+item.id+',this)"><i class="fa fa-play"></i><br/>START</a>';

                        var status = item.ass_date != null ? '' : item.type;
                        var asse = '';
                        if(item.reason != null && item.reason != '' && item.reason.substring(0,5) == 'BREAK')
                            status = 'BREAK';
                        
                        if (item.result >= item.qty) {
                            asse = "<i class='fa fa-check'></i>";
                        } else {
                            buttons = [];
                            colors = [];
                            icons = [];
                            texts = [];
                            width = "";
                            if(status == 'CHANGE OVER' || status == 'ASSEMBLY' || status == 'DOWN TIME') {
                                asse = item.type + '<br/>';
                                if(status == 'ASSEMBLY')
                                    buttons = ['assembly-break','assembly-stop'];
                                else
                                    buttons = ['break','stop'];
                                colors = ['btn-warning','btn-danger'];
                                icons = ['fa-pause','fa-stop'];
                                texts = [' BREAK',' STOP'];
                                width = "width: 70px;"
                            } else if(status == 'BREAK') {
                                asse = item.type + ' (BREAK) <br/>';
                                if (item.type == 'CHANGE OVER')
                                    buttons = ['changeover'];
                                else if (item.type == 'ASSEMBLY')
                                    buttons = ['assembly'];
                                else
                                    buttons = ['continue'];
                                icons = ['fa-play'];
                                colors = ['btn-success'];
                                texts = [' CONTINUE'];
                                width = "width: 120px;"
                            } else {
                                buttons = ['changeover', 'assembly', 'downtime'];
                                colors = ['btn-info','btn-cyan','btn-danger'];
                                icons = ['fa-play', 'fa-play', 'fa-play'];
                                texts = ['<br/>C/O', '<br/>ASM', '<br/>D/T'];
                            }
                            for(var i = 0; i < buttons.length; i++) {
                                asse += '<a href="javascript:" class="btn '+colors[i]+' btn-sm btn-status" style="margin-right: 2px;'+width+'" onclick="setStatus(\'assembly\',\''+buttons[i]+'\','+item.id+',this)"><i class="fa '+icons[i]+'"></i>'+texts[i]+'</a>';
                            }
                        }

                        var url = "{{MITBooster::adminPath('assemblies')}}?return_url={{urlencode(Request::fullUrl())}}&parent_table=our_bookings_detail&parent_columns=item_no,description,qty&parent_columns_alias=&parent_id="+item.id+"&foreign_key=our_bookings_detail_id&label={{$title}}";
                        var id_tag = (qty == '&nbsp;') ? '&nbsp;' : '<a href="{{MITBooster::mainpath('print-moulding-complete')}}?id='+item.id+'" class="btn btn-primary btn-sm btn-status"><i class="fa fa-print"></i><br/>PRINT</a>'
                        var row = 
                            '<tr style="border-bottom:1px solid #d9d9d9;">'+
                                '<td class="text-center right-border font-weight-bold">'+formatNumber(index+1)+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+customer_po+'<br/>'+item.production_order+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+item.item_no+'<br/>'+item.sku+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+
                                    '<a href="'+url+'" style="color: #000000;">'+item.description+'</a><br/>'+
                                '</td>'+
                                '<td class="text-center right-border font-weight-bold">'+mould+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+back+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+glass+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+acce+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+asse+'</td>'+
                                // '<td class="text-center right-border font-weight-bold">'+id_tag+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+item.prod_plan+'<br/>'+item.prod_act+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+item.inspect1+'<br/>'+item.inspect2+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+item.loading_plan+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+formatNumber(parseFloat(item.qty))+'<br/>'+qty+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+formatNumber(parseFloat(item.speed))+'<br/>'+speed+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+parseFloat(item.work_hour).toFixed(2)+'<br/>'+work_hour+'</td>'+
                                '<td class="text-center right-border font-weight-bold">'+change_over+'<br/>'+down_time+'</td>'+
                                '<td class="text-center font-weight-bold"><span id="'+item.production_order+'">&nbsp;</span></td>'+
                                // '<td class="text-center right-border font-weight-bold"><span class="'+av+' p-8">'+parseFloat(item.av).toFixed(2)+'</span></td>'+
                                // '<td class="text-center right-border font-weight-bold"><span class="'+ar+' p-8">'+parseFloat(item.ar).toFixed(2)+'</span></td>'+
                                // '<td class="text-center right-border font-weight-bold"><span class="'+rft+' p-8">'+parseFloat(item.rft).toFixed(2)+'</span></td>'+
                                // '<td class="text-center font-weight-bold"><span class="'+oee+' p-8">'+parseFloat(item.oee).toFixed(2)+'</span></td>'+
                            '</tr>';
                        $("#production-body").append(row);
                    });
                    effDetail();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('ERRORS: ' + textStatus);

                }
            });
        }

        function effDetail() {
            var our_lines_id = {{$our_lines_id}};
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('eff-detail')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': our_lines_id,
                },
                success:function(data){
                    // console.log(data);

                    data.minus.forEach(function(item, index) {
                        var name = "#"+item.production_order;
                        var value = $(name).html();
                        var class_text = "danger";
                        if(item.eff > 0)
                            class_text = "success";
                        value = value + "<span class='"+class_text+"'>"+item.eff+"</span>";
                        $(name).html(value);
                    });

                    data.plus.forEach(function(item, index) {
                        var name = "#"+item.production_order;
                        var value = $(name).html();
                        var class_text = "danger";
                        if(item.eff > 0)
                            class_text = "success";
                        value = value + "<span class='"+class_text+"'>"+item.eff+"</span>";
                        $(name).html(value);
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('ERRORS: ' + textStatus);

                }
            });
		}
        
        var current_id = 0;
        var current_t = null;
        var loading = false;
        var status_assembly = '';

        function downtime() {
            var reason = $('#dt_reason').val();

            var problems = new Array();//storing the selected values inside an array
            $('#dtProblems :selected').each(function(i, selected) {
                problems[i] = $(selected).val();
            });

            if (problems.length == 0) {
                alert('Alasan tidak diperbolehkan kosong (Pilih salah satu)');
                return;
            }

            // if (reason == "") {
            //     alert('Keterangan tidak diperbolehkan kosong');
            //     return;
            // }

            setStatus('assembly', 'downtime', current_id, current_t, reason, problems);
            $('#down-time').modal('hide');
        }

        function cancel() {
            current_id = 0;
            current_t = null;
        }

        function assembly_stop() {
            var qty_prod = $('#qty_prod').val();
            var qty_reject = $('#qty_reject').val();
            var qty = $('#qty').val();
            var reason = $('#ass_reason').val();
            
            var problems = new Array();//storing the selected values inside an array
            $('#assProblems :selected').each(function(i, selected) {
                problems[i] = $(selected).val();
            });
            // $('#rejectProblems :selected').each(function(i, selected) {
            //     problems[i] = $(selected).val();
            // });

            if (problems.length == 0) {
                alert('Alasan tidak diperbolehkan kosong (Pilih salah satu)');
                return;
            }

            if(status_assembly == 'assembly-break')
                setStatus('assembly', 'assembly_break', current_id, current_t, reason, problems, qty, qty_prod, qty_reject);
            else
                setStatus('assembly', 'assembly_stop', current_id, current_t, reason, problems, qty, qty_prod, qty_reject);

            $('#assembly-stop').modal('hide');
        }

        function assembly_break() {
            var qty_prod = $('#qty_prod').val();
            var qty_reject = $('#qty_reject').val();
            var qty = $('#qty').val();
            var reason = $('#ass_reason').val();
            
            var problems = new Array();//storing the selected values inside an array
            $('#assProblems :selected').each(function(i, selected) {
                problems[i] = $(selected).val();
            });
            // $('#rejectProblems :selected').each(function(i, selected) {
            //     problems[i] = $(selected).val();
            // });

            if (problems.length == 0) {
                alert('Alasan tidak diperbolehkan kosong (Pilih salah satu)');
                return;
            }

            setStatus('assembly', 'assembly_break', current_id, current_t, reason, problems, qty, qty_prod, qty_reject);
            $('#assembly-stop').modal('hide');
        }

        $("#assembly-stop").on('hide.bs.modal', function(){
            current_id = 0;
            current_t = null;
            status_assembly = '';
        });

        $("#down-time").on('hide.bs.modal', function(){
            current_id = 0;
            current_t = null;
            status_assembly = '';
        });

        $('#qty_prod').blur(calculate);
        $('#qty_reject').blur(calculate);

        function calculate() {
            var qty = 0;
            qty += $('#qty_prod').val();
            qty -= $('#qty_reject').val();
            $('#qty').val(qty);
        }

        function setStatus(type, status, id, t, reason = null, problems = null, qty = 0, qty_prod = 0, qty_reject = 0) {
            if(current_t == null && type == 'assembly' && status == 'downtime') {
                current_id = id;
                current_t = t;
                loading = true;
                $('#down-time').modal('show');
                return;
            }

            if(current_t == null && type == 'assembly' && status == 'assembly-stop') {
                current_id = id;
                current_t = t;
                loading = true;
                $('#assembly-stop').modal('show');
                return;
            }

            if(current_t == null && type == 'assembly' && status == 'assembly-break') {
                current_id = id;
                current_t = t;
                loading = true;
                status_assembly = 'assembly-break';
                $('#assembly-stop').modal('show');
                return;
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('set-status')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'type': type,
                    'status': status,
                    'id': id,
                    'reason': reason,
                    'problems': problems,
                    'qty': qty,
                    'qty_prod': qty_prod,
                    'qty_reject': qty_reject,
                },
                success:function(data){
                    // console.log(data);

                    var result = "";
                    if(type == "") {
                        if(status == 'stop')
                            result = "<i class='fa fa-check'></i>";
                        if(status == 'start')
                            result = '<a href="javascript:" class="btn btn-danger btn-sm btn-status" onclick="setStatus(\''+type+'\',\'stop\','+id+',this)"><i class="fa fa-stop"></i><br/>STOP</a>';
                        if(status == 'start' && type == 'accessories')
                            result = "<i class='fa fa-check'></i>";
                    } else {
                        item = data.item;
                        // console.log(item);
                        var status = item.ass_date != null ? '' : item.type;
                        var asse = '';
                        if(item.reason != null && item.reason != '' && item.reason.substring(0,5) == 'BREAK')
                            status = 'BREAK';
                        if (item.result >= item.qty) {
                            result = "<i class='fa fa-check'></i>";
                        } else {
                            buttons = [];
                            colors = [];
                            icons = [];
                            texts = [];
                            width = "";
                            if(status == 'CHANGE OVER' || status == 'ASSEMBLY' || status == 'DOWN TIME') {
                                result = item.type + '<br/>';
                                if(status == 'ASSEMBLY')
                                    buttons = ['assembly-break','assembly-stop'];
                                else
                                    buttons = ['break','stop'];
                                colors = ['btn-warning','btn-danger'];
                                icons = ['fa-pause','fa-stop'];
                                texts = [' BREAK',' STOP'];
                                width = "width: 70px;"
                            } else if(status == 'BREAK') {
                                result = item.type + ' (BREAK) <br/>';
                                if (item.type == 'CHANGE OVER')
                                    buttons = ['changeover'];
                                else if (item.type == 'ASSEMBLY')
                                    buttons = ['assembly'];
                                else
                                    buttons = ['continue'];
                                colors = ['btn-success'];
                                icons = ['fa-play'];
                                texts = [' CONTINUE'];
                                width = "width: 120px;"
                            } else {
                                buttons = ['changeover', 'assembly', 'downtime'];
                                colors = ['btn-info','btn-cyan','btn-danger'];
                                icons = ['fa-play', 'fa-play', 'fa-play'];
                                texts = ['<br/>C/O', '<br/>ASM', '<br/>D/T'];
                            }
                            // console.log(colors);
                            for(var i = 0; i < buttons.length; i++) {
                                result += '<a href="javascript:" class="btn '+colors[i]+' btn-sm btn-status" style="margin-right: 2px;'+width+'" onclick="setStatus(\'assembly\',\''+buttons[i]+'\','+item.id+',this)"><i class="fa '+icons[i]+'"></i>'+texts[i]+'</a>';
                            }
                        }

                    }
                    // console.log($(t).parent());
                    $(t).parent().html(result);
                    loading = false;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('ERRORS: ' + textStatus);

                }
            });
        }

    </script>
@endsection

@section('wrapper')
<div id="main-wrapper">
    <div class="fixedElement">
        <div class="title font-weight-bold m-t-10">Monitoring: {{$title}} </div>
        <div class="m-b-10">
            <div>
                <input type="checkbox" id="complete" name="complete"/> Hide Complete |
                <input type="checkbox" id="refresh" name="refresh" checked onclick="toggleAutoRefresh(this);"/> Auto Refresh
            </div>
            <div class="date-filter m-t-5">
                Periods :
                <input type="text" id="start_date" name="start_date" class="input_date" value="{{$start_date}}" style="max-width: 80px;"/> to
                <input type="text" id="end_date" name="end_date" class="input_date" value="{{$end_date}}" style="max-width: 80px;"/>
                <input class="btn btn-primary btn-sm" type="button" value="Refresh" onclick="productionPlan()">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL EFFECTIVE</h5>
                        <div class="effective">
                            <h2 class="text-success">0 <i class="fa fa-sort-amount-up"></i></span></h2>
                        </div>
                        <table style="width:100%;">
                            <tr>
                                <td style="min-width: 65px;width: 20%;" valign="top" class="text-right header">&nbsp;</td>
                                <td style="width: 20%;" class="text-right header">P/O</td>
                                <td style="width: 30%;" class="text-right header">QTY</td>
                                <td style="width: 30%;" class="text-right header">DATE</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">FINAL :</td>
                                <td class="text-right font-weight-bold final-po">0</td>
                                <td class="text-right font-weight-bold final-qty">0</td>
                                <td class="text-right font-weight-bold final-date"></td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">CONFIRM :</td>
                                <td class="text-right font-weight-bold confirm-po">0</td>
                                <td class="text-right font-weight-bold confirm-qty">0</td>
                                <td class="text-right font-weight-bold confirm-date"></td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">BOOKING :</td>
                                <td class="text-right font-weight-bold booking-po">0</td>
                                <td class="text-right font-weight-bold booking-qty">0</td>
                                <td class="text-right font-weight-bold booking-date"></td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right total">TOTAL :</td>
                                <td class="text-right font-weight-bold total total-po">0</td>
                                <td class="text-right font-weight-bold total total-qty">0</td>
                                <td class="text-right font-weight-bold total total-date"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">WEEKLY PRODUCTION STATUS (LAST 4 WEEKS)</h5>
                        <table style="width:100%;" class="m-t-10">
                            <tr>
                                <td style="min-width: 50px;width: 14%;" valign="top" class="text-right header" rowspan="2">&nbsp;</td>
                                <td class="text-center header right-border" colspan="5">WEEKLY P/O</td>
                                <td class="text-center header" colspan="5">ACCUMULATION</td>
                            </tr>
                            <tr>
                                <td style="min-width: 30px;width: 8%;" class="text-center header">PLAN</td>
                                <td style="min-width: 30px;width: 8%;" class="text-center header">ACT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                                <td style="min-width: 30px;width: 7%;" class="text-center header">COT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header right-border">%</td>

                                <td style="min-width: 30px;width: 8%;" class="text-center header">PLAN</td>
                                <td style="min-width: 30px;width: 8%;" class="text-center header">ACT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                                <td style="min-width: 30px;width: 7%;" class="text-center header">COT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right week1-name"></td>
                                <td class="text-center font-weight-bold week1-plan">0</td>
                                <td class="text-center font-weight-bold week1-act">0</td>
                                <td class="text-center font-weight-bold week1-perc1">0</td>
                                <td class="text-center font-weight-bold week1-cot">0</td>
                                <td class="text-center font-weight-bold right-border week1-perc2">0</td>

                                <td class="text-center font-weight-bold week1-com-plan">0</td>
                                <td class="text-center font-weight-bold week1-com-act">0</td>
                                <td class="text-center font-weight-bold week1-com-perc1">0</td>
                                <td class="text-center font-weight-bold week1-com-cot">0</td>
                                <td class="text-center font-weight-bold week1-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right week2-name"></td>
                                <td class="text-center font-weight-bold week2-plan">0</td>
                                <td class="text-center font-weight-bold week2-act">0</td>
                                <td class="text-center font-weight-bold week2-perc1">0</td>
                                <td class="text-center font-weight-bold week2-cot">0</td>
                                <td class="text-center font-weight-bold right-border week2-perc2">0</td>

                                <td class="text-center font-weight-bold week2-com-plan">0</td>
                                <td class="text-center font-weight-bold week2-com-act">0</td>
                                <td class="text-center font-weight-bold week2-com-perc1">0</td>
                                <td class="text-center font-weight-bold week2-com-cot">0</td>
                                <td class="text-center font-weight-bold week2-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right week3-name"></td>
                                <td class="text-center font-weight-bold week3-plan">0</td>
                                <td class="text-center font-weight-bold week3-act">0</td>
                                <td class="text-center font-weight-bold week3-perc1">0</td>
                                <td class="text-center font-weight-bold week3-cot">0</td>
                                <td class="text-center font-weight-bold right-border week3-perc2">0</td>

                                <td class="text-center font-weight-bold week3-com-plan">0</td>
                                <td class="text-center font-weight-bold week3-com-act">0</td>
                                <td class="text-center font-weight-bold week3-com-perc1">0</td>
                                <td class="text-center font-weight-bold week3-com-cot">0</td>
                                <td class="text-center font-weight-bold week3-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right week4-name"></td>
                                <td class="text-center font-weight-bold week4-plan">0</td>
                                <td class="text-center font-weight-bold week4-act">0</td>
                                <td class="text-center font-weight-bold week4-perc1">0</td>
                                <td class="text-center font-weight-bold week4-cot">0</td>
                                <td class="text-center font-weight-bold right-border week4-perc2">0</td>

                                <td class="text-center font-weight-bold week4-com-plan">0</td>
                                <td class="text-center font-weight-bold week4-com-act">0</td>
                                <td class="text-center font-weight-bold week4-com-perc1">0</td>
                                <td class="text-center font-weight-bold week4-com-cot">0</td>
                                <td class="text-center font-weight-bold week4-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right total">YTD :</td>
                                <td class="text-center total font-weight-bold week5-plan">0</td>
                                <td class="text-center total font-weight-bold week5-act">0</td>
                                <td class="text-center total font-weight-bold week5-perc1">0</td>
                                <td class="text-center total font-weight-bold week5-cot">0</td>
                                <td class="text-center total font-weight-bold right-border week5-perc2">0</td>

                                <td class="text-center total font-weight-bold week5-com-plan"></td>
                                <td class="text-center total font-weight-bold week5-com-act"></td>
                                <td class="text-center total font-weight-bold week5-com-perc1"></td>
                                <td class="text-center total font-weight-bold week5-com-cot"></td>
                                <td class="text-center total font font-weight-bold week5-com-perc2"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title ">DAILY PRODUCTION STATUS (THIS WEEK)</h5>
                        <table style="width:100%;" class="m-t-10">
                            <tr>
                                <td style="min-width: 30px;width: 20%;" valign="top" class="text-right header">&nbsp;</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">PLAN</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">ACT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">COT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header right-border">%</td>

                                <td style="min-width: 30px;width: 10%;" class="text-center header">PLAN</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">ACT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">COT</td>
                                <td style="min-width: 30px;width: 10%;" class="text-center header">%</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">MON :</td>
                                <td class="text-center font-weight-bold day1-plan">0</td>
                                <td class="text-center font-weight-bold day1-act">0</td>
                                <td class="text-center font-weight-bold day1-perc1">0</td>
                                <td class="text-center font-weight-bold day1-cot">0</td>
                                <td class="text-center font-weight-bold right-border day1-perc2">0</td>

                                <td class="text-center font-weight-bold day1-com-plan">0</td>
                                <td class="text-center font-weight-bold day1-com-act">0</td>
                                <td class="text-center font-weight-bold day1-com-perc1">0</td>
                                <td class="text-center font-weight-bold day1-com-cot">0</td>
                                <td class="text-center font-weight-bold day1-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">TUE :</td>
                                <td class="text-center font-weight-bold day2-plan">0</td>
                                <td class="text-center font-weight-bold day2-act">0</td>
                                <td class="text-center font-weight-bold day2-perc1">0</td>
                                <td class="text-center font-weight-bold day2-cot">0</td>
                                <td class="text-center font-weight-bold right-border day2-perc2">0</td>

                                <td class="text-center font-weight-bold day2-com-plan">0</td>
                                <td class="text-center font-weight-bold day2-com-act">0</td>
                                <td class="text-center font-weight-bold day2-com-perc1">0</td>
                                <td class="text-center font-weight-bold day2-com-cot">0</td>
                                <td class="text-center font-weight-bold day2-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">WED :</td>
                                <td class="text-center font-weight-bold day3-plan">0</td>
                                <td class="text-center font-weight-bold day3-act">0</td>
                                <td class="text-center font-weight-bold day3-perc1">0</td>
                                <td class="text-center font-weight-bold day3-cot">0</td>
                                <td class="text-center font-weight-bold right-border day3-perc2">0</td>

                                <td class="text-center font-weight-bold day3-com-plan">0</td>
                                <td class="text-center font-weight-bold day3-com-act">0</td>
                                <td class="text-center font-weight-bold day3-com-perc1">0</td>
                                <td class="text-center font-weight-bold day3-com-cot">0</td>
                                <td class="text-center font-weight-bold day3-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">THU :</td>
                                <td class="text-center font-weight-bold day4-plan">0</td>
                                <td class="text-center font-weight-bold day4-act">0</td>
                                <td class="text-center font-weight-bold day4-perc1">0</td>
                                <td class="text-center font-weight-bold day4-cot">0</td>
                                <td class="text-center font-weight-bold right-border day4-perc2">0</td>

                                <td class="text-center font-weight-bold day4-com-plan">0</td>
                                <td class="text-center font-weight-bold day4-com-act">0</td>
                                <td class="text-center font-weight-bold day4-com-perc1">0</td>
                                <td class="text-center font-weight-bold day4-com-cot">0</td>
                                <td class="text-center font-weight-bold day4-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right">FRI :</td>
                                <td class="text-center font-weight-bold day5-plan">0</td>
                                <td class="text-center font-weight-bold day5-act">0</td>
                                <td class="text-center font-weight-bold day5-perc1">0</td>
                                <td class="text-center font-weight-bold day5-cot">0</td>
                                <td class="text-center font-weight-bold right-border day5-perc2">0</td>

                                <td class="text-center font-weight-bold day5-com-plan">0</td>
                                <td class="text-center font-weight-bold day5-com-act">0</td>
                                <td class="text-center font-weight-bold day5-com-perc1">0</td>
                                <td class="text-center font-weight-bold day5-com-cot">0</td>
                                <td class="text-center font-weight-bold day5-com-perc2">0</td>
                            </tr>
                            <tr>
                                <td valign="top" class="text-right total">TOTAL :</td>
                                <td class="text-center total font-weight-bold day6-plan">0</td>
                                <td class="text-center total font-weight-bold day6-act">0</td>
                                <td class="text-center total font-weight-bold day6-perc1">0</td>
                                <td class="text-center total font-weight-bold day6-cot">0</td>
                                <td class="text-center total font-weight-bold right-border day6-perc2">0</td>

                                <td class="text-center total font-weight-bold day6-com-plan"></td>
                                <td class="text-center total font-weight-bold day6-com-act"></td>
                                <td class="text-center total font-weight-bold day6-com-perc1"></td>
                                <td class="text-center total font-weight-bold day6-com-cot"></td>
                                <td class="text-center total font-weight-bold day6-com-perc2"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" style="margin-top: 310px;">
        <div class="card-body">
            <div class="table-responsive">
                <table style="width:100%;" class="table">
                    <thead>
                    <tr style="border-top: 1px solid #d9d9d9;">
                        <th style="min-width: 30px" rowspan="2" class="text-center right-border header">No.</th>
                        <th style="min-width: 90px" class="text-center right-border header">Order #</th>
                        <th style="min-width: 90px" class="text-center right-border header">Item #</th>
                        
                        <th style="min-width: 120px" rowspan="2" class="text-center right-border header">Name</th>
                        <th colspan="5" class="text-center right-border header">Status</th>
                        <!-- <th style="min-width: 80px" rowspan="2" class="text-center right-border header">ID TAG</th> -->

                        <th style="min-width: 160px" class="text-center right-border header">Planning</th>
                        <th style="min-width: 80px" rowspan="2" class="text-center right-border header">Inspection</th>
                        <th style="min-width: 80px" rowspan="2" class="text-center right-border header">Loading</th>
                        <th style="min-width: 80px" rowspan="2" class="text-center right-border header">Quantity</th>
                        <th style="min-width: 60px" rowspan="2" class="text-center right-border header">Speed</th>
                        <th style="min-width: 50px" rowspan="2" class="text-center right-border header">Lead<br/>Time</th>
                        <th style="min-width: 50px" rowspan="2" class="text-center right-border header">Down<br/>Time</th>
                        <th style="min-width: 60px" rowspan="2" class="text-center header">Eff<br/>Rate</th>
                        <!-- <th colspan="4" class="text-center header">Perfomance</th> -->
                    </tr>
                    <tr>
                        <th class="text-center right-border header">Prod. Order #</th>
                        <th class="text-center right-border header">Buyer SKU</th>

                        <th style="min-width: 50px" class="text-center right-border header">M</th>
                        <th style="min-width: 50px" class="text-center right-border header">K</th>
                        <th style="min-width: 50px" class="text-center right-border header">B</th>
                        <th style="min-width: 50px" class="text-center right-border header">A</th>
                        <th style="min-width: 180px" class="text-center right-border header">Assembling</th>

                        <th class="text-center right-border header">Actual</th>
                        
                        <!-- <th style="min-width: 50px" class="text-center right-border header">AV</th>
                        <th style="min-width: 50px" class="text-center right-border header">PE</th>
                        <th style="min-width: 50px" class="text-center right-border header">QR</th>
                        <th style="min-width: 50px" class="text-center header">OEE</th> -->
                    </tr>
                    </thead>
                    <tbody id="production-body"></tbody>
                </table>
            </table>
        </div>
    </div>
</div>

<div id="down-time" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Down Time Reason</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id='form-down-time' method='get' action='' class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group row m-b-10">
                        <label class="col-4">Alasan: </label>
                        <div class="col-8">
                            <select  style='width:100%' class='form-control form-control-sm' id="dtProblems" name="dtProblems" multiple="multiple">
                                @foreach($problem_production as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row m-b-10">
                        <label class="col-4">Keterangan: </label>
                        <div class="col-8">
                            <input type="text" id="dt_reason" name="dt_reason" class="form-control form-control-sm" >
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" onclick="cancel();" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" onclick="downtime();">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="assembly-stop" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Assembly Result</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id='form-assembly' method='get' action='' class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group row m-b-10">
                        <label class="col-4">Qty Production: </label>
                        <div class="col-4">
                            <input type="text" id="qty_prod" name="qty_prod" class="inputMoney form-control form-control-sm" value="0">
                        </div>
                    </div>
                    <div class="form-group row m-b-10">
                        <label class="col-4">Qty Rejected: </label>
                        <div class="col-4">
                            <input type="text" id="qty_reject" name="qty_reject" class="inputMoney form-control form-control-sm" value="0">
                        </div>
                    </div>
                    <div class="form-group row m-b-10">
                        <label class="col-4">Qty: </label>
                        <div class="col-4">
                            <input type="text" id="qty" name="qty" class="inputMoney form-control form-control-sm" value="0" readonly>
                        </div>
                    </div>
                    <div class="form-group row m-b-10">
                        <label class="col-4">Alasan: </label>
                        <div class="col-8">
                            <select  style='width:100%' class='form-control form-control-sm' id="assProblems" name="assProblems" multiple="multiple">
                                @foreach($problem_production as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row m-b-10">
                        <label class="col-4">Keterangan: </label>
                        <div class="col-8">
                            <input type="text" id="ass_reason" name="ass_reason" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" onclick="cancel();" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" onclick="assembly_stop();">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection