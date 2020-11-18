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
    <script src="{{ asset ('assets/vendor/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js') }}" charset="UTF-8"></script>
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
            
            @if($qty == 0)
                $rejected = 0;
            @else
                $rejected = {{($qty - $rejected)/$qty*100}};
            @endif
            $av = {{$av}};
            $pe = {{$pe}};
            $qr = {{$qr}};
            $oee = {{$oee}};

            $('.chartRejected').attr("data-percent", $rejected);
            $('.chartRejected').easyPieChart({
                barColor : '#01c0c8',
                scaleColor : '#01c0c8',
                lineWidth: 10,
                trackColor : false,
                lineCap : 'butt',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text($rejected.toFixed(1));
                }
            });

            $('.chartAV').attr("data-percent", $av);
            $('.chartAV').easyPieChart({
                barColor : '#00642c',
                scaleColor : '#00642c',
                lineWidth: 10,
                trackColor : false,
                lineCap : 'butt',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text($av.toFixed(1));
                }
            });

            $('.chartPE').attr("data-percent", $pe);
            $('.chartPE').easyPieChart({
                barColor : '#cf7825',
                scaleColor : '#cf7825',
                lineWidth: 10,
                trackColor : false,
                lineCap : 'butt',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text($pe.toFixed(1));
                }
            });

            $('.chartQR').attr("data-percent", $qr);
            $('.chartQR').easyPieChart({
                barColor : '#6610f2',
                scaleColor : '#6610f2',
                lineWidth: 10,
                trackColor : false,
                lineCap : 'butt',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text($qr.toFixed(1));
                }
            });

            $('.chartOEE').attr("data-percent", $oee);
            $('.chartOEE').easyPieChart({
                barColor : '#2285dd',
                scaleColor : '#2285dd',
                lineWidth: 10,
                trackColor : false,
                lineCap : 'butt',
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text($oee.toFixed(1));
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
                            <form id='form-filter-item' method='get' action='{{ MITBooster::mainPath("item") }}' class="form-horizontal">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row" style="margin-top:10px;">
                                            <div class="col-6">
                                                <label for="startDate">Start Date</label>
                                                <input type="text" class="form-control notfocus input_date" id="startDate" name="start_date" value="{{ $start_date }}" placeholder="Start Date" autocomplete="off">
                                            </div>
                                            <div class="col-6">
                                                <label for="endDate">End Date</label>
                                                <input type="text" class="form-control input_date" id="endDate" name="end_date" value="{{ $end_date }}" placeholder="End Date" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:10px;">
                                            <div class="col-6">
                                                <label for="item">Item</label>
                                                <input type="text" value="{{ request()->input('item_no') }}" class="form-control" name="item_no" id="item" placeholder="Item No" autocomplete="off">
                                            </div>

                                            <div class="col-6">
                                                <label for="description">Description</label>
                                                <input type="text" value="{{ request()->input('description') }}" class="form-control" name="description" id="description" placeholder="Description" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row" style="margin-top:10px;">
                                            <div class="col-6">
                                                <label for="customerName">Customer</label>
                                                <input type="text" value="{{ request()->input('customer_name') }}" class="form-control" name="customer_name" id="customerName" placeholder="Customer Name" autocomplete="off">
                                            </div>
                                            <div class="col-6">
                                                <label>Speed</label>
                                                <select class="form-control custom-select" name="speed" id="speedReport">
                                                    <option value="" {{ request()->input('speed') == '' ? 'selected' : '' }}>All Speed...</option>
                                                    <option value="1" {{ request()->input('speed') == 1 ? 'selected' : '' }}>Speed up</option>
                                                    <option value="2" {{ request()->input('speed') == 2 ? 'selected' : '' }}>Speed down</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row" style="margin-top:10px;">
                                            <div class="col-6">
                                                <label for="cellName">All Cell</label>
                                                <select class="form-control custom-select" name="cell_name" id="cellName">
                                                    <option value="" {{ request()->input('cell_name') == '' ? 'selected' : '' }}>All Cell...</option>
                                                    <option value="1" {{ request()->input('cell_name') == 1 ? 'selected' : '' }}>Cell 1</option>
                                                    <option value="2" {{ request()->input('cell_name') == 2 ? 'selected' : '' }}>Cell 2</option>
                                                    <option value="3" {{ request()->input('cell_name') == 3 ? 'selected' : '' }}>Cell 3</option>
                                                </select>
                                            </div>
                                        </div>
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
        <div class="col-md-3 text-center">
            <div class="chart chartRejected" data-percent="45">
                <span class="percent">75</span> <br/>
            </div>
            <div>{{ number_format($qty) }} pcs</div>
            <h5 class="text-center">Result</h5>
        </div>
        <div class="col-md-9 text-center">
            <div class="row">
                <div class="col-md-3">
                    <div class="chart chartAV" data-percent="45">
                        <span class="percent">75</span> <br/>
                    </div>
                    <h4 class="text-center">AV</h4>
                </div>
                <div class="col-md-3">
                    <div class="chart chartPE" data-percent="45">
                        <span class="percent">75</span> <br/>
                    </div>
                    <h4 class="text-center">PE</h4>
                </div>
                <div class="col-md-3">
                    <div class="chart chartQR" data-percent="45">
                        <span class="percent">75</span> <br/>
                    </div>
                    <h4 class="text-center">QR</h4>
                </div>
                <div class="col-md-3">
                    <div class="chart chartOEE" data-percent="45">
                        <span class="percent">75</span> <br/>
                    </div>
                    <h4 class="text-center">OEE</h4>
                </div>
            </div>
        </div>
    </div>

<div class="row" style="overflow-x: auto; margin-top: 30px">
    <table id="result" class="table table-striped table-bordered small display nowrap result" style="background: #FFF;">
        <thead>
            <tr>
                <th rowspan="2" width="3%" class="text-center font-weight-bold">No.</th>
                <th rowspan="2" width="7%" class="text-center font-weight-bold">Cell</th>
                <th rowspan="2" width="7%" class="text-center font-weight-bold">Customer</th>
                <th rowspan="2" width="10%" class="text-center font-weight-bold">Item #</th>
                <th rowspan="2" class="text-center font-weight-bold">Description</th>
                <th rowspan="2" width="10%" class="text-center font-weight-bold">Start Date</th>
                <th colspan="2" width="6%" class="text-center font-weight-bold">Planning</th>
                <th colspan="3" width="9%" class="text-center font-weight-bold">Realization</th>
                <th colspan="3" width="9%" class="text-center font-weight-bold">Actual</th>
                <th colspan="4" width="12%" class="text-center font-weight-bold">Performance</th>
            </tr>
            <tr class="text-center">
                {{-- Planning --}}
                <th width="3%" class="font-weight-bold">Qty<br>(pc)</th>
                <th width="3%" class="font-weight-bold">Spd<br>(pc/h)</th>

                {{--  Realization  --}}
                <th width="3%" class="font-weight-bold">Result<br>(pc)</th>
                <th width="3%" class="font-weight-bold">Reject<br>(pc)</th>
                <th width="3%" class="font-weight-bold">Spd<br>(pc/h)</th>

                {{--  Actual  --}}
                <th width="3%" class="font-weight-bold">C/O<br>(h)</th>
                <th width="3%" class="font-weight-bold">D/T<br>(h)</th>
                <th width="3%" class="font-weight-bold">Spd<br>(pc/h)</th>

                {{--  Performance  --}}
                <th width="3%" class="font-weight-bold">AV<br>(%)</th>
                <th width="3%" class="font-weight-bold">PE<br>(%)</th>
                <th width="3%" class="font-weight-bold">QR<br>(%)</th>
                <th width="3%" class="font-weight-bold">OEE<br>(%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result ?:[] as $no => $item)
                <tr>
                    <td class="text-right">{{ $no + 1 }}</td>
                    <td>{{ $item->cell_name }}</td>
                    <td>{{ $item->customer_name }}</td>
                    <td>{{ $item->item_no }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ date('d M y H:i:s', strtotime($item->start_date)) }}</td>
                    <td class="text-right">{{ number_format($item->qty) }}</td>
                    <td class="text-right">{{ number_format($item->speed) }}</td>
                    <td class="text-right">{{ number_format($item->result) }}</td>
                    <td class="text-right">{{ number_format($item->rejected) }}</td>
                    <td class="text-right">{{ number_format($item->speed2) }}</td>
                    <td class="text-right">{{ number_format($item->co_hour,2) }}</td>
                    <td class="text-right">{{ number_format($item->dt_hour,2) }}</td>
                    <td class="text-right">{{ number_format($item->speed3) }}</td>

                    @if ($item->av <= 0)
                        <td class="text-right" style="background-color: #FFFFFF;color: #000000;">{{ number_format($item->av,2) }}</td>
                    @elseif ($item->av < 85)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @elseif (($item->av >= 85 && $item->av <= 90))
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @elseif ($item->av > 90)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->av,2) }}</td>
                    @endif

                    @if ($item->ar <= 0)
                        <td class="text-right" style="background-color: #FFFFFF;color: #000000;">{{ number_format($item->ar,2) }}</td>
                    @elseif ($item->ar < 95)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->ar,2) }}</td>
                    @elseif ($item->ar >= 95 && $item->ar <= 98)
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->ar,2) }}</td>
                    @elseif ($item->ar > 98)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->ar,2) }}</td>
                    @endif

                    @if ($item->rft <= 0)
                        <td class="text-right" style="background-color: #FFFFFF;color: #000000;">{{ number_format($item->rft,2) }}</td>
                    @elseif ($item->rft < 80)
                        <td class="text-right" style="background-color: #a80e19;color: #FFFFFF;">{{ number_format($item->rft,2) }}</td>
                    @elseif ($item->rft >= 80 && $item->rft <= 85)
                        <td class="text-right" style="background-color: #cf7825;color: #FFFFFF;">{{ number_format($item->rft,2) }}</td>
                    @elseif ($item->rft > 85)
                        <td class="text-right" style="background-color: #00642c;color: #FFFFFF;">{{ number_format($item->rft,2) }}</td>
                    @endif

                    @if ($item->oee <= 0)
                        <td class="text-right" style="background-color: #FFFFFF;color: #000000;">{{ number_format($item->oee,2) }}</td>
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
    <div class="table-bottom">&nbsp;</div>
</div>
@endsection
