@extends('mitbooster::layouts.admin')

@push('head')
<style>
.chart {
        position: relative;
        display: inline-block;
        width: 100px;
        height: 100px;
        margin-top: 0px;
        margin-bottom: 20px;
        text-align: center; 
}

.chart canvas {
        position: absolute;
        top: 0;
        left: 0; 
}

.percent {
        display: inline-block;
        line-height: 100px;
        z-index: 2;
        font-weight: 600;
        font-size: 16px;
        color: #343a40; 
}

.percent:after {
        content: '%';
        margin-left: 0.1em;
        font-size: .8em; 
}

#cellcpa{
 	text-align: center;
}

.monitoring thead tr th {
	text-align: center;
	vertical-align: middle;
	padding: 5px 10px 5px 10px;
}
.monitoring tbody tr td {
	vertical-align: middle;
	padding: 5px 10px 5px 10px;
	height: 50px;
}
.btn-monitoring {
	padding: 0px 10px 0px 10px;
}
.date-monitor {
	border-bottom: 1px solid #000000;
}

</style>
@endpush


@push('bottom')
    
    <script src="{{ asset ('assets/vendor/Chart.js/Chart.min.js') }}" charset="UTF-8"></script>

    <script>

        var lang = '{{App::getLocale()}}';

        $('.input_date').datepicker({
            format: 'yyyy/mm/dd',
            language: lang
        });

        $('.open-datetimepicker').click(function () {
            $(this).next('.input_date').datepicker('show');
        });

        var chart1 = null;
        var chart2 = null;
        var chart3 = null;
        var chart4 = null;

        var perfomance1 = null;
        var perfomance2 = null;
        var perfomance3 = null;
        var perfomance4 = null;

        var delay = null;
        var reason = null;
        var perform = null;

		$(document).ready(function() {
            refreshData();
        });

        function refreshData() {

            // confirmFinal();

            totalEffective(1);
            totalEffective(2);
            totalEffective(3);
            totalEffective(4);

            orderCell(1);
            orderCell(2);
            orderCell(3);
            orderCell(4);

            performanceAnalysis(1);
            performanceAnalysis(2);
            performanceAnalysis(3);
            performanceAnalysis(4);

            chartDelay();
            chartReason();

            itemDownSpeed(1);
            itemDownSpeed(2);
            itemDownSpeed(3);

            itemDownTime(1);
            itemDownTime(2);
            itemDownTime(3);

            itemReject(1);
            itemReject(2);
            itemReject(3);

            itemReason(1);
            itemReason(2);
            itemReason(3);

            chartPerform();

            itemPerform(1);
            itemPerform(2);
            itemPerform(3);

            purchase(1);
            purchase(2);
            purchase(3);

        }

        function parseStringFloat(text) {
            if(text != null) {
                text = text.replace(/,/g, '');
                return parseFloat(text);
            }
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        function confirmFinal(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('confirm-final')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'start_date':start_date,
                    'end_date':end_date,
                },
                success:function(data){
                   // console.log(data);

                //    $('#confirmFinal').html(formatNumber(data.totalPo.totalPo))
                },
            });
        }
        
        function totalEffective(id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('effective')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    'our_lines_id': id,
                },
                success:function(data){
                    var result = "";
                    var title = "CELL "+id;
                    if(id == 4)
                        title = "TOTAL";
                    if(data.effective < 0)
                        result = title + ' <div class="text-danger" style="font-size: 24px;float:right;">'+formatNumber(data.effective)+' <i class="fa fa-sort-amount-down"></i></div>';
                    else
                        result = title + ' <div class="text-success" style="font-size: 24px;float:right;">'+formatNumber(data.effective)+' <i class="fa fa-sort-amount-up"></i></div>';
                    $('.effective'+id).html(result);
                },
            });

        }

		function orderCell(id) {
            $.ajax({
                url: "{{MITBooster::mainpath('best')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    /* console.log(data); */

                    $('#final'+id).html(formatNumber(data.final.po))
                    $('#finalSum'+id).html(formatNumber(data.final.qty))
                    $('#finalDate'+id).html(data.final.last_date)

                    $('#confirm'+id).html(formatNumber(data.confirm.po))
                    $('#confirmSum'+id).html(formatNumber(data.confirm.qty))
                    $('#confirmDate'+id).html(formatNumber(data.confirm.last_date))

                    $('#booking'+id).html(formatNumber(data.booking.po))
                    $('#bookingSum'+id).html(formatNumber(data.booking.qty))
                    $('#bookingDate'+id).html(formatNumber(data.booking.last_date))

                    $('#complete'+id).html(formatNumber(data.complete.po))
                    $('#completeSum'+id).html(formatNumber(data.complete.qty))
                    $('#completeDate'+id).html(formatNumber(data.complete.last_date))
                    
                    $('#totalCount'+id).html(formatNumber(parseFloat(data.final.po) + parseFloat(data.confirm.po) + parseFloat(data.booking.po) + parseFloat(data.complete.po)))
                    $('#totalSum'+id).html(formatNumber(parseFloat(data.final.qty) + parseFloat(data.confirm.qty) + parseFloat(data.booking.qty) + parseFloat(data.complete.qty)))

                    $('#chart-graph'+id).html(''); 
                    $('#chart-graph'+id).append('<canvas id="chart'+id+'"><canvas>');
  
                    var chart = null;
                    if(id == 1) chart = chart1;
                    else if(id == 2) chart = chart2;
                    else if(id == 3) chart = chart3;
                    else if(id == 4) chart = chart4;
                    
                    if(chart != null) chart.destroy();

                    chart = new Chart(document.getElementById("chart"+id), {
                        "type":"pie",
                        "data":{
                            "labels":["Complete","Final","Confirm", "Booking"],
                        "datasets":[{
                            "label":"My First Dataset",
                            "data":[
                                data.complete.po,
                                data.final.po,
                                data.confirm.po,
                                data.booking.po
                            ],
                            "backgroundColor":[
                                "#00642c",
                                "#a80e19",
                                "#cf7825",
                                "#2285dd"
                            ]}
                        ]},
                        options: {
                            legend: {
                                display: false
                            },
                        }
                    });

                    if(id == 1) chart1 = chart;
                    else if(id == 2) chart2 = chart;
                    else if(id == 3) chart3 = chart;
                    else if(id == 4) chart4 = chart;

                }
            });
        }

        function performanceAnalysis(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('performance')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);

                    var no = 0;
                    var total_problem = data.total_problem;

                    $('#planningPO'+id).html(formatNumber(data.planning));
                    $('#totalPO'+id).html(formatNumber(data.actual))
                    $('#cot'+id).html(formatNumber(data.cot))
                    $('#cot_perc'+id).html(formatNumber(data.cot_perc))
                    $('#delay'+id).html(formatNumber(data.delay))
                    $('#delay_perc'+id).html(formatNumber(data.delay_perc))
                    
                    var chart = null;
                    if(id == 1) chart = perfomance1;
                    else if(id == 2) chart = perfomance2;
                    else if(id == 3) chart = perfomance3;
                    else if(id == 4) chart = perfomance4;
                    
                    if(chart != null) chart.destroy();
                    
                    chart = new Chart(document.getElementById("performance"+id), {
                        "type":"doughnut",
                        "data":{"labels":["On Time","Delay"],
                        "datasets":[{
                            "label":"My First Dataset",
                            "data":[
                                data.cot,
                                data.delay
                            ],
                            "backgroundColor":["#00642c","#a80e19"]}
                        ]},
                    });

                    if(id == 1) perfomance1 = chart;
                    else if(id == 2) perfomance2 = chart;
                    else if(id == 3) perfomance3 = chart;
                    else if(id == 4) perfomance4 = chart;

                }
            });
        }

        function chartDelay() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('chart-delay')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data.max);

                    if(delay != null) delay.destroy();
                    delay = new Chart(document.getElementById('delay'), {
                        "type":"bar",
                        "data": {
                            labels: ['Cell 1', 'Cell 2', 'Cell 3'],
                            datasets: [{
                                label: 'Down Speed',
                                backgroundColor: "#cf7825",
                                data: [
                                    data.cell1.speed,
                                    data.cell2.speed,
                                    data.cell3.speed,
                                ]
                            }, {
                                label: 'Down Time',
                                backgroundColor: "#2285dd",
                                data: [
                                    data.cell1.down,
                                    data.cell2.down,
                                    data.cell3.down,
                                ]
                            }, {
                                label: 'Both',
                                backgroundColor: "#00642c",
                                data: [
                                    data.cell1.both,
                                    data.cell2.both,
                                    data.cell3.both,
                                ]
                            }]
                        },   
                        options: {
                            events: false,
                            tooltips: {
                                enabled: false
                            },
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        
                                    }
                                }]
                            },
                            hover: {
                                animationDuration: 0
                            },
                            animation: {
                                duration: 1,
                                onComplete: function () {
                                    var chartInstance = this.chart,
                                        ctx = chartInstance.ctx;
                                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';

                                    this.data.datasets.forEach(function (dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);
                                        meta.data.forEach(function (bar, index) {
                                            var data = dataset.data[index];  
                                            var y_pos = bar._model.y;
                                            if(data > 97)
                                                y_pos = bar._model.y + 20;
                                            ctx.fillText(data, bar._model.x, y_pos);
                                        });
                                    });
                                }
                            }
                        }
                    });
                }
            });
        }

        function chartReason() {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('chart-reason')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                success: function (data, textStatus, jqXHR) {
                    console.log(data);

                    if(reason != null) reason.destroy();
                    reason = new Chart(document.getElementById('reason'), {
                        "type":"bar",
                        "data": {
                            labels: ['Last Week', 'This Week'],
                            datasets: data.result,
                        },   
                        options: {
                            events: false,
                            tooltips: {
                                enabled: false
                            },
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                    }
                                }]
                            },
                            hover: {
                                animationDuration: 0
                            },
                            animation: {
                                duration: 1,
                                onComplete: function () {
                                    var chartInstance = this.chart,
                                        ctx = chartInstance.ctx;
                                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';

                                    this.data.datasets.forEach(function (dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);
                                        meta.data.forEach(function (bar, index) {
                                            var data = dataset.data[index];  
                                            var y_pos = bar._model.y;
                                            if(data > 97)
                                                y_pos = bar._model.y + 20;
                                            ctx.fillText(data, bar._model.x, y_pos);
                                        });
                                    });
                                }
                            }
                        }
                    });
                }
            });
        }

        function itemDownSpeed(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('down-speed')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);
                    
                    var no = 0;
                    $("#itemDownSpeed"+id).empty();
                    
                    data.forEach(function(item, index) {
                        var row = ""+
                            "<tr>"+
                            "   <td scope='row'>"+ (no+1) +"</td>"+
                            "   <td>"+ item.production_order +"<br/>"+item.description+"</td>"+
                            "       <td class='text-right'>"+ item.speed +"</td>"+
                            "       <td class='text-right'>"+ item.speed3 +"</td>"+
                            "       <td class='text-right'>"+ item.perc +"</td>"+
                            "</tr>";
                        $("#itemDownSpeed"+id).append(row);
                        no++;
                    });
                }
            });
        }

        function itemDownTime(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('down-time')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);
                    
                    var no = 0;
                    $("#itemDownTime"+id).empty();
                    
                    data.forEach(function(item, index) {
                        var row = ""+
                            "<tr>"+
                            "   <td scope='row'>"+ (no+1) +"</td>"+
                            "   <td>"+ item.production_order +"</td>"+
                            "       <td>"+ item.description +"</td>"+
                            "       <td class='text-right'>"+ item.dt_hour +"</td>"+
                            "</tr>";
                        $("#itemDownTime"+id).append(row);
                        no++;
                    });
                }
            });
        }

        function itemReject(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('reject')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);

                    var no = 0;
                    $("#itemReject"+id).empty();

                    data.forEach(function(item, index) {
                        // console.log(item);
                        var row = ""+
                            "<tr>"+
                            "   <td scope='row'>"+ (no+1) +"</td>"+
                            "   <td>"+ item.production_order +"<br/>"+item.description+"</td>"+
                            "       <td class='text-right'>"+ item.qty +"</td>"+
                            "       <td class='text-right'>"+ item.rejected +"</td>"+
                            "       <td class='text-right'>"+ item.perc +"</td>"+
                            "</tr>";
                        $("#itemReject"+id).append(row);
                        no++;
                    });
                }
            });
        }

        function itemReason(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('reason')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);

                    var no = 0;
                    $("#reasonAnalysis"+id).empty();

                    data.forEach(function(item, index) {
                        // console.log(item);
                        var row = ""+
                            "<tr>"+
                            "   <td scope='row'>"+ (no+1) +"</td>"+
                            "       <td>"+ item.reason +"</td>"+
                            "       <td class='text-right'>"+ item.qty +"</td>"+
                            "</tr>";
                        $("#reasonAnalysis"+id).append(row);
                        no++;
                    });
                }
            });
        }

        function chartPerform(type) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('chart-perform')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);

                    if(perform != null) perform.destroy();
                    perform = new Chart(document.getElementById('perform'), {
                        "type":"bar",
                        "data": {
                            labels: ['Cell 1', 'Cell 2', 'Cell 3'],
                            datasets: [{
                                label: 'UP Speed',
                                backgroundColor: "#cf7825",
                                data: [
                                    data.cell1.speed,
                                    data.cell2.speed,
                                    data.cell3.speed,
                                ]
                            }]
                        },   
                        options: {
                            events: false,
                            tooltips: {
                                enabled: false
                            },
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                }]
                            },
                            hover: {
                                animationDuration: 0
                            },
                            animation: {
                                duration: 1,
                                onComplete: function () {
                                    var chartInstance = this.chart,
                                        ctx = chartInstance.ctx;
                                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';

                                    this.data.datasets.forEach(function (dataset, i) {
                                        var meta = chartInstance.controller.getDatasetMeta(i);
                                        meta.data.forEach(function (bar, index) {
                                            var data = dataset.data[index];  
                                            var y_pos = bar._model.y;
                                            if(data > 97)
                                                y_pos = bar._model.y + 20;
                                            ctx.fillText(data + '%', bar._model.x, y_pos);
                                        });
                                    });
                                }
                            }
                        }
                    });                 
                }
            });
        }

        function itemPerform(id) {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            $.ajax({
                url: "{{MITBooster::mainpath('perform')}}",
                type: 'GET',
                contentType: 'application/json',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    our_lines_id: id,
                },
                success: function (data, textStatus, jqXHR) {
                    // console.log(data);

                    var no = 0;
                    $("#itemPerform"+id).empty();

                    data.forEach(function(item, index) {
                        // console.log(item);
                        var row = ""+
                            "<tr>"+
                            "   <td scope='row'>"+ (no+1) +"</td>"+
                            "   <td>"+ item.production_order +"</td>"+
                            "       <td>"+ item.description +"</td>"+
                            "       <td class='text-right'>"+ item.speed +"</td>"+
                            "       <td class='text-right'>"+ item.speed3 +"</td>"+
                            "       <td class='text-right'>"+ item.perc +"</td>"+
                            "</tr>";
                        $("#itemPerform"+id).append(row);
                        no++;
                    });
                }
            });
        }

        function purchase(id) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name=\'csrf-token\']').attr('content')
                },
                url: "{{MITBooster::mainpath('purchase')}}",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    our_lines_id:id
                },
                success:function(data){
                    // console.log(data);
                    var no = 0;

                    $("#rencana_pembelian"+id+" > tr").remove();
                    data.data.forEach(function(item, index) {
                        var row = 
                            `
                            <tr>
                                <td>`+(no+1)+`</td>
                                <td>`+item.production_order+`</td>
                                <td>`+item.name+`</td>
                                <td>`+item.start_date+`</td>
                                <td class="text-right">`+formatNumber(item.qty)+`</td>
                            </tr>
                            `
                        $("#rencana_pembelian"+id).append(row);
                        no++;
                    });
                },
            });
        }

        function printDocument(id) {

            w=window.open();
            w.document.write($('#'+id).html());
            w.print();
            w.close();

        }

        $('#form-filter').submit(function(e){
            e.preventDefault();
            refreshData();
        })

    </script>
@endpush

@section('content')
<div class="row">
        <h5 style="margin-top: 3px; margin-left: 10px;">Periods :</h5>
        <form id='form-filter' data-filter="form-date" class="m-l-10">
            <input type="text" id="start_date" value="{{ $start_date }}" class="input_date m-r-10" style="max-width: 80px;" /> to
            <input type="text" name="end_date" id="end_date" value="{{ $end_date }}" class="input_date m-l-10" style="max-width: 80px;" />
            <input class="btn btn-primary btn-sm m-l-10" type="submit" value="Refresh">
            <!-- <input class="btn btn-primary btn-sm m-l-10" type="button" value="Print" onclick="PrintMe('print-area');"> -->
        </form>
</div>

<div id="print-area">
    <!-- Order Status -->
    <div class="row m-t-10">
        <div class="col-sm-12">
            <h3>Order Status</h3>
            
            <div class="card-group">
                <!-- Column -->

                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title effective1">CELL 1</h5>
                        <hr>
                        <center>
                            <div class="chart-graph1">
                                <canvas id="chart1" height="200"></canvas>
                            </div>
                        </center>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="background:  #F0F0F0; font-family: arial;" align="center">
                                        <th><b>Status</b></th>
                                        <th><b>PO</b></th>
                                        <th><b>Qty</b></th>
                                        <th style="min-width: 50px;"><b>Date</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th width="10px"><b>COMPLETE</b></th>
                                        <td id="complete1" class="text-right">0</td>
                                        <td id="completeSum1" class="text-right"><span >0</span></td>
                                        <td id="completeDate1" class="text-center"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>FINAL</b></th>
                                        <td id="final1" class="text-right">0</td>
                                        <td id="finalSum1" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="finalDate1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>CONFIRM</b></th>
                                        <td id="confirm1" class="text-right">0</td>
                                        <td id="confirmSum1" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="confirmDate1"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>BOOKING</b></th>
                                        <td id="booking1" class="text-right">0</td>
                                        <td id="bookingSum1" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="bookingDate1"></td>
                                    </tr>
                                    <tr id="total">
                                        <th scope="row"><b>TOTAL</b></th>
                                        <td id="totalCount1" class="text-right"><b>0</b></td>
                                        <td id="totalSum1" class="text-right"><b><span >0</span></b></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title effective2">CELL 1</h5>
                        <hr>
                        <center>
                            <div class="chart-graph2">
                                <canvas id="chart2" height="200"></canvas>
                            </div>
                        </center>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="background:  #F0F0F0; font-family: arial;" align="center">
                                        <th><b>Status</b></th>
                                        <th><b>PO</b></th>
                                        <th><b>Qty</b></th>
                                        <th style="min-width: 50px;"><b>Date</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th width="10px"><b>COMPLETE</b></th>
                                        <td id="complete2" class="text-right">0</td>
                                        <td id="completeSum2" class="text-right"><span >0</span></td>
                                        <td id="completeDate2" class="text-center"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>FINAL</b></th>
                                        <td id="final2" class="text-right">0</td>
                                        <td id="finalSum2" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="finalDate2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>CONFIRM</b></th>
                                        <td id="confirm2" class="text-right">0</td>
                                        <td id="confirmSum2" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="confirmDate2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>BOOKING</b></th>
                                        <td id="booking2" class="text-right">0</td>
                                        <td id="bookingSum2" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="bookingDate2"></td>
                                    </tr>
                                    <tr id="total">
                                        <th scope="row"><b>TOTAL</b></th>
                                        <td id="totalCount2" class="text-right"><b>0</b></td>
                                        <td id="totalSum2" class="text-right"><b><span >0</span></b></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title effective3">CELL 3</h5>                   
                        <hr>
                        <center>
                            <div class="chart-graph3">
                                <canvas id="chart3" height="200"></canvas>
                            </div>
                        </center>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="background:  #F0F0F0; font-family: arial;" align="center">
                                        <th><b>Status</b></th>
                                        <th><b>PO</b></th>
                                        <th><b>Qty</b></th>
                                        <th style="min-width: 50px;"><b>Date</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th width="10px"><b>COMPLETE</b></th>
                                        <td id="complete3" class="text-right">0</td>
                                        <td id="completeSum3" class="text-right"><span >0</span></td>
                                        <td id="completeDate3" class="text-center"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>FINAL</b></th>
                                        <td id="final3" class="text-right">0</td>
                                        <td id="finalSum3" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="finalDate3"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>CONFIRM</b></th>
                                        <td id="confirm3" class="text-right">0</td>
                                        <td id="confirmSum3" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="confirmDate3"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>BOOKING</b></th>
                                        <td id="booking3" class="text-right">0</td>
                                        <td id="bookingSum3" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="bookingDate3"></td>
                                    </tr>
                                    <tr id="total">
                                        <th scope="row"><b>TOTAL</b></th>
                                        <td id="totalCount3" class="text-right"><b>0</b></td>
                                        <td id="totalSum3" class="text-right"><b><span >0</span></b></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title effective4">TOTAL</h5>
                        <hr>
                        <center>
                            <div class="chart-graph4">
                                <canvas id="chart4" height="200"></canvas>
                            </div>
                        </center>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr style="background:  #F0F0F0; font-family: arial;" align="center">
                                        <th><b>Status</b></th>
                                        <th><b>PO</b></th>
                                        <th><b>Qty</b></th>
                                        <th style="min-width: 50px;"><b>Date</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th width="10px"><b>COMPLETE</b></th>
                                        <td id="complete4" class="text-right">0</td>
                                        <td id="completeSum4" class="text-right"><span >0</span></td>
                                        <td id="completeDate4" class="text-center"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>FINAL</b></th>
                                        <td id="final4" class="text-right">0</td>
                                        <td id="finalSum4" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="finalDate4"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>CONFIRM</b></th>
                                        <td id="confirm4" class="text-right">0</td>
                                        <td id="confirmSum4" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="confirmDate4"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><b>BOOKING</b></th>
                                        <td id="booking4" class="text-right">0</td>
                                        <td id="bookingSum4" class="text-right"><span >0</span></td>
                                        <td class="text-center" id="bookingDate4"></td>
                                    </tr>
                                    <tr id="total">
                                        <th scope="row"><b>TOTAL</b></th>
                                        <td id="totalCount4" class="text-right"><b>0</b></td>
                                        <td id="totalSum4" class="text-right"><b><span >0</span></b></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </div>
    </div>

    <!-- Performance Analysis -->
    <div class="row m-t-10">
        <div class="col-sm-12">
            <h3>Performance Analysis</h3>
            <div class="card-group">
                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 1</h5>
                        <hr>
                            <div class="chart-performance1">
                                <canvas id="performance1" height="200"> </canvas>
                            </div>
                        <hr>
                        <div align="center"><h4>Planning PO : <span id="planningPO1">0</span></h4></div>
                        <div align="center"><h4>Complete PO : <span id="totalPO1">0</span></h4></div>
                        <hr>
                        <div class="stats-row m-b-10" align="center">
                            <div class="stat-item text-right">
                                <h6>On Time</h6> <b><span style="font-size: 11px;"><span id="cot_perc1">0</span>%</b> / <b>(<span id="cot1">0</span> PO)</span></b>
                            </div>
                            <div class="stat-item text-right">
                                <h6>Delay</h6> <b><span style="font-size: 11px;"><span id="delay_perc1">0</span>%</b> / <b>(<span id="delay1">0</span> PO)</span></b>
                            </div>
                        </div>
                    </div>
                </div> 

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 2</h5>
                        <hr>
                            <div class="chart-performance2">
                                <canvas id="performance2" height="200"> </canvas>
                            </div>
                        <hr>
                        <div align="center"><h4>Planning PO : <span id="planningPO2">0</span></h4></div>
                        <div align="center"><h4>Complete PO : <span id="totalPO2">0</span></h4></div>
                        <hr>
                        <div class="stats-row m-b-10" align="center">
                            <div class="stat-item text-right">
                                <h6>On Time</h6> <b><span style="font-size: 11px;"><span id="cot_perc2">0</span>%</b> / <b>(<span id="cot2">0</span> PO)</span></b>
                            </div>
                            <div class="stat-item text-right">
                                <h6>Delay</h6> <b><span style="font-size: 11px;"><span id="delay_perc2">0</span>%</b> / <b>(<span id="delay2">0</span> PO)</span></b>
                            </div>
                        </div>
                    </div>
                </div> 

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 3</h5>
                        <hr>
                            <div class="chart-performance3">
                                <canvas id="performance3" height="200"> </canvas>
                            </div>
                        <hr>
                        <div align="center"><h4>Planning PO : <span id="planningPO3">0</span></h4></div>
                        <div align="center"><h4>Complete PO : <span id="totalPO3">0</span></h4></div>
                        <hr>
                        <div class="stats-row m-b-10" align="center">
                            <div class="stat-item text-right">
                                <h6>On Time</h6> <b><span style="font-size: 11px;"><span id="cot_perc3">0</span>%</b> / <b>(<span id="cot3">0</span> PO)</span></b>
                            </div>
                            <div class="stat-item text-right">
                                <h6>Delay</h6> <b><span style="font-size: 11px;"><span id="delay_perc3">0</span>%</b> / <b>(<span id="delay3">0</span> PO)</span></b>
                            </div>
                        </div>
                    </div>
                </div> 

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">TOTAL</h5>
                        <hr>
                            <div class="chart-performance4">
                                <canvas id="performance4" height="200"> </canvas>
                            </div>
                        <hr>
                        <div align="center"><h4>Planning PO : <span id="planningPO4">0</span></h4></div>
                        <div align="center"><h4>Complete PO : <span id="totalPO4">0</span></h4></div>
                        <hr>
                        <div class="stats-row m-b-10" align="center">
                            <div class="stat-item text-right">
                                <h6>On Time</h6> <b><span style="font-size: 11px;"><span id="cot_perc4">0</span>%</b> / <b>(<span id="cot4">0</span> PO)</span></b>
                            </div>
                            <div class="stat-item text-right">
                                <h6>Delay</h6> <b><span style="font-size: 11px;"><span id="delay_perc4">0</span>%</b> / <b>(<span id="delay4">0</span> PO)</span></b>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <!-- Gap Analysis - Bad Performance -->
    <div class="row m-t-10">
        <div class="col-sm-12">
            <h3>Gap Analysis - Bad Performance</h3>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="card-title">Performance Overview</h5>
                            <hr>
                            <canvas id="delay" height="150"> </canvas>
                        </div>
                        <div class="col-sm-6">
                            <h5 class="card-title">Reason Overview</h5>
                            <hr>
                            <canvas id="reason" height="150"> </canvas>
                        </div>
                    </div>
                    <hr>

                    <h5 class="card-title">Down Speed</h5>
                    <div class="card-group">
                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 1</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-right">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownSpeed1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 2</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-right">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownSpeed2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 3</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-right">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownSpeed3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>

                    <h5 class="card-title">Down Time</h5>
                    <div class="card-group">
                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 1</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">D/T</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownTime1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 2</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">D/T</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownTime2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 3</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">D/T</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemDownTime3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>

                    <h5 class="card-title">Rejected</h5>
                    <div class="card-group">
                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 1</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Qty</th>
                                        <th scope="col" class="text-right">Reject</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemReject1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 2</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Qty</th>
                                        <th scope="col" class="text-right">Reject</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemReject2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 3</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No<br/>Description</th>
                                        <th scope="col" class="text-right">Qty</th>
                                        <th scope="col" class="text-right">Reject</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemReject3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>

                    <h5 class="card-title">Reason</h5>
                    <div class="card-group">
                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 1</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Reason</th>
                                        <th scope="col" class="text-right">P/O</th>
                                    </tr>
                                    </thead>
                                    <tbody id="reasonAnalysis1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card m-r-10">
                            <div class="card-body">
                                <h5 class="card-title">CELL 2</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Reason</th>
                                        <th scope="col" class="text-right">P/O</th>
                                    </tr>
                                    </thead>
                                    <tbody id="reasonAnalysis2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 3</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Reason</th>
                                        <th scope="col" class="text-right">P/O</th>
                                    </tr>
                                    </thead>
                                    <tbody id="reasonAnalysis3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div>
            </div> 
        </div>
    </div>

    <!-- Gap Analysis - Good Performance -->
    <div class="row m-t-10">
        <div class="col-sm-12">
            <h3>Gap Analysis - Good Performance</h3>
            <div class="card-group">
                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Complete On Time</h5>
                        <hr>
                        <canvas id="perform" height="200"> </canvas>
                    </div>
                </div> 

                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Performed Item</h5>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 1</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemPerform1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 2</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemPerform2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">CELL 3</h5>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. No</th>
                                        <th scope="col">Description</th>
                                        <th scope="col" class="text-right">Speed</th>
                                        <th scope="col" class="text-right">Actual</th>
                                        <th scope="col" class="text-center">%</th>
                                    </tr>
                                    </thead>
                                    <tbody id="itemPerform3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <!-- //Rencana Pembelian / Item yang harus diperhatikan untuk proses permintaan pembelian-->
    <div class="row">
        <div class="col-sm-12">
            <h3>Purchase Planning</h3>
            <div class="card-group">
                
                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 1</h5>
                        <div class="card">
                            <div class="card-body">
                                <!-- <h5 class="card-title">CELL 1</h5> -->
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. Order</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Start</th>
                                        <th scope="col">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody id="rencana_pembelian1">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
    
                    </div>
                </div>
                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 2</h5>
                        <div class="card">
                            <div class="card-body">
                                <!-- <h5 class="card-title">CELL 1</h5> -->
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. Order</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Start</th>
                                        <th scope="col">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody id="rencana_pembelian2">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
                    </div>
                </div>
                <!-- Column -->
                <div class="card m-r-10">
                    <div class="card-body">
                        <h5 class="card-title">Cell 3</h5>
                        <div class="card">
                            <div class="card-body">
                                <!-- <h5 class="card-title">CELL 1</h5> -->
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Prod. Order</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Start</th>
                                        <th scope="col">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody id="rencana_pembelian3">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div> 
    
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>
@endsection
