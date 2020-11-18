@extends('mitbooster::layouts.admin')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <style>
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
    .date-monitor {
        border-bottom: 1px solid #000000;
    }
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
    </style>

    <link rel='stylesheet' href='<?php echo asset("assets/vendor/datepicker/css/bootstrap-datepicker.min.css")?>'/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/jqueryui/jquery-ui.css') }}">
@endpush


@push('bottom')
    @if (App::getLocale() != 'en')
        <script src="{{ asset ('assets/vendor/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}"
            charset="UTF-8"></script>
    @else
        <script src="{{ asset ('assets/vendor/datepicker/js/bootstrap-datepicker.min.js') }}"
            charset="UTF-8"></script>
    @endif
    <script src="{{ asset('assets/vendor/jqueryui/jquery-ui.js') }}"></script>
    <script src="{{ asset ('assets/vendor/Chart.js/Chart.min.js') }}" charset="UTF-8"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" charset="UTF-8"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js" charset="UTF-8"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js" charset="UTF-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" charset="UTF-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" charset="UTF-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" charset="UTF-8"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js" charset="UTF-8"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js" charset="UTF-8"></script>

	<script>
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

            $('.open-datetimepicker').click(function () {
                $(this).next('.input_date').datepicker('show');
            });

            
            new Chart(document.getElementById("chartLine"),
            {
                "type":"bar",
                "data":{"labels":["January","February","March","April","May","June","July","August","September", "October", "November", "December"],
                "datasets":[
                    {
                        "label":"Target",
                        "data":<?php echo json_encode($chart['target']); ?>,
                        "fill":false,
                        "backgroundColor":"rgba(168, 14, 25, 0.2)",
                        "borderColor":"rgb(168, 14, 25)",
                        "borderWidth":1},
                    {
                        "label":"AV",
                        "data":<?php echo json_encode($chart['av']); ?>,
                        "fill":false,
                        "backgroundColor":"rgba(0, 100, 44, 0.2)",
                        "borderColor":"rgb(0, 100, 44)",
                        "borderWidth":1},
                    {
                        "label":"PE",
                        "data":<?php echo json_encode($chart['pe']); ?>,
                        "fill":false,
                        "backgroundColor":"rgba(207, 120, 37, 0.2)",
                        "borderColor":"rgb(207, 120, 37)",
                        "borderWidth":1},
                    {
                        "label":"QR",
                        "data":<?php echo json_encode($chart['qr']); ?>,
                        "fill":false,
                        "backgroundColor":"rgba(102, 16, 242, 0.2)",
                        "borderColor":"rgb(102, 16, 242)",
                        "borderWidth":1},
                    {
                        "label":"OEE",
                        "data":<?php echo json_encode($chart['oee']); ?>,
                        "fill":false,
                        "backgroundColor":"rgba(34, 133, 221, 0.2)",
                        "borderColor":"rgb(34, 133, 221)",
                        "borderWidth":1}
                            ]},
                "options":{
                    "scales":{"yAxes":[{"ticks":{"beginAtZero":true}}]}
                }
            });
        });

        $(document).ready(function() {
            $('#result').DataTable({
                searching: false,
                paging: false,
                dom: 'Bfrtip',
                buttons: ['csv']
            });
        });

    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col col-md-12">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Filter Data Report
                        </button>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <form id='form-filter-item' method='get' action='{{ MITBooster::mainPath("line") }}' class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="startDate">Start Date</label>
                                        <input type="text" class="form-control notfocus input_date" id="startDate" name="start_date" value="{{ $start_date }}" placeholder="Start Date" autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="endDate">End Date</label>
                                        <input type="text" class="form-control input_date" id="endDate" name="end_date" value="{{ $end_date }}" placeholder="End Date" autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cellName">All Cell</label>
                                        <select class="form-control custom-select" name="cell_name" id="cellName">
                                            <option value="" {{ request()->input('cell_name') == '' ? 'selected' : '' }}>All Cell...</option>
                                            <option value="1" {{ request()->input('cell_name') == 1 ? 'selected' : '' }}>Cell 1</option>
                                            <option value="2" {{ request()->input('cell_name') == 2 ? 'selected' : '' }}>Cell 2</option>
                                            <option value="3" {{ request()->input('cell_name') == 3 ? 'selected' : '' }}>Cell 3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-6">
                                        <input class="btn btn-primary" name="submit" type="submit" value="Search">
                                        <!-- <input class="btn btn-primary" name="print" type="submit" value="Print"> -->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-md-12">
        <canvas id="chartLine" height="224" width="1050" class="chartjs-render-monitor" style="display: block; height: 162px; width: 525px;"></canvas>
    </div>
</div>

<div class="row" style="overflow-x: auto; margin-top: 30px">
    <table id="result" class="table table-striped table-bordered small display nowrap" style="background: #FFF;">
        <thead>
            <tr>
                <th width="5%" class="text-center font-weight-bold">No.</th>
                <th class="text-center font-weight-bold">Cell</th>
                <th width="10%" class="text-center font-weight-bold">Target</th>
                <th width="10%" class="text-center font-weight-bold">Achievement</th>
                <th width="5%" class="text-center font-weight-bold">Rate</th>
                <th width="5%" class="text-center font-weight-bold">AV<br>(%)</th>
                <th width="5%" class="text-center font-weight-bold">PE<br>(%)</th>
                <th width="5%" class="text-center font-weight-bold">QR<br>(%)</th>
                <th width="5%" class="text-center font-weight-bold">OEE<br>(%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result ?:[] as $no => $item)
                <tr>
                    <td class="text-right">{{ $no + 1 }}</td>
                    <td>{{ $item->cell_name }}</td>
                    <td class="text-right">{{ number_format($item->qty_prod) }}</td>
                    <td class="text-right">{{ number_format($item->achievement) }}</td>

                    @if ($item->rate <= 0)
                        <td class="text-right">{{ number_format($item->rate,2) }}</td>
                    @elseif ($item->rate < 85)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->rate,2) }}</td>
                    @elseif (($item->rate >= 85 && $item->rate <= 90))
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->rate,2) }}</td>
                    @elseif ($item->rate > 90)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->rate,2) }}</td>
                    @endif

                    
                    @if ($item->av <= 0)
                        <td class="text-right">{{ number_format($item->av,2) }}</td>
                    @elseif ($item->av < 85)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @elseif (($item->av >= 85 && $item->av <= 90))
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @elseif ($item->av > 90)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @endif

                    @if ($item->pe <= 0)
                        <td class="text-right">{{ number_format($item->pe,2) }}</td>
                    @elseif ($item->pe < 95)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->pe,2) }}</td>
                    @elseif ($item->pe >= 95 && $item->pe <= 98)
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->pe,2) }}</td>
                    @elseif ($item->pe > 98)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->pe,2) }}</td>
                    @endif

                    @if ($item->qr <= 0)
                        <td class="text-right">{{ number_format($item->qr,2) }}</td>
                    @elseif ($item->qr < 80)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->qr,2) }}</td>
                    @elseif ($item->qr >= 80 && $item->qr <= 85)
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->qr,2) }}</td>
                    @elseif ($item->qr > 85)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->qr,2) }}</td>
                    @endif

                    @if ($item->oee <= 0)
                        <td class="text-right">{{ number_format($item->oee,2) }}</td>
                    @elseif ($item->oee < 90)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->oee,2) }}</td>
                    @elseif ($item->oee >= 90 && $item->oee <= 95)
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->oee,2) }}</td>
                    @elseif ($item->oee > 95)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->oee,2) }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
