<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ $filename }}</title>
    <style>
        thead { display: table-header-group }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid }
    </style>

    <link href="{{ public_path('assets/vendor/bootstrap/dist/css/bootstrap.css') }}" rel='stylesheet'/>
    <link href="{{ public_path('assets/css/style.css') }}" rel='stylesheet'/>
</head>

<body style="background-color: #ffffff;">
    <div class="row p-b-5 m-b-15" style="border-bottom: 2px solid #111111;">
        <!-- <div class="col-4"><img src="http://localhost/production/public/images/unity.png" height="80px"></div> -->
        <div class="col-4"><img src="{{ public_path('images/unity.png') }}" height="80px"></div>
        <div class="col-8 text-center">
            <h1>Performances Line</h1>
            <h4>Printed: <strong>{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}</strong> To <strong>{{ \Carbon\Carbon::parse($end_date)->format('d M Y') }} ({{ MITBooster::myName() }})</strong></h4>
        </div>
    </div>
    <div style="padding:20px;"></div>
    <table class="table table-bordered primary-table">
        <thead>
            <tr>
                <th rowspan="2" class="text-center">No.</th>
                <th rowspan="2" class="text-center">Production Order</th>
                <th rowspan="2" class="text-center">Customer</th>
                <th rowspan="2" class="text-center">Cell Name</th>
                <th rowspan="2" class="text-center">Item #</th>
                <th rowspan="2" width="20%" class="text-center">Description</th>
                <th rowspan="2" class="text-center">Buyer Sku</th>
                <th rowspan="2" class="text-center">Start Date</th>
                <th colspan="2" width="10%" class="text-center">Planning</th>
                <th colspan="3" width="10%" class="text-center">Actual</th>
                <th colspan="2" class="text-center">Real</th>
                <th colspan="4" class="text-center">Performance</th>
            </tr>
            <tr>
                {{-- PLANNING --}}
                <th width="3%">Qty</th>
                <th width="3%">Speed</th>
                {{--  ACTUAL  --}}
                <th width="3%">Qty<br>(result)</th>
                <th width="3%">Reject</th>
                <th width="3%">Speed</th>

                {{--  Real  --}}
                <th width="2%">DT</th>
                <th width="2%">Speed</th>

                {{--  Performance  --}}
                <th width="2%">AV<br>(%)</th>
                <th width="2%">PE<br>(%)</th>
                <th width="2%">QR<br>(%)</th>
                <th width="3%">OEE<br>(%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($line_performances ?:[] as $no => $item)
                <tr>
                    <td>{{ $no + 1 }}</td>
                    <td>{{ $item->production_order }}</td>
                    <td>{{ $item->cs_name }}</td>
                    <td>{{ $item->cell_name }}</td>
                    <td class="text-right">{{ $item->item_no }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->customer_po }}</td>
                    <td>{{ $item->start_date }}</td>
                    <td class="text-right">{{ number_format($item->qty) }}</td>
                    <td class="text-right">{{ number_format($item->speed) }}</td>
                    <td class="text-right">{{ number_format($item->result) }}</td>
                    <td class="text-right">{{ number_format($item->rejected) }}</td>
                    <td class="text-right">{{ number_format($item->speed2) }}</td>
                    <td class="text-right">{{ number_format($item->downtime) }}</td>
                    <td class="text-right">{{ number_format($item->speed3) }}</td>

                    @if ($item->av <= 0)
                        <td class="text-right" style="background-color: #fff">{{ number_format($item->av) }}</td>
                    @elseif ($item->av < 90)
                        <td class="text-right" style="background-color: #FF0000">{{ number_format($item->av) }}</td>
                    @elseif (($item->ar >= 90 && $item->ar <= 95))
                        <td class="text-right" style="background-color: #FFA500">{{ number_format($item->av) }}</td>
                    @elseif ($item->av > 95)
                        <td class="text-right" style="background-color: #00FF00">{{ number_format($item->av) }}</td>
                    @endif

                    @if ($item->ar <= 0)
                        <td class="text-right" style="background-color: #fff">{{ number_format($item->ar) }}</td>
                    @elseif ($item->ar < 90)
                        <td class="text-right" style="background-color: #FF0000">{{ number_format($item->ar) }}</td>
                    @elseif ($item->ar >= 90 && $item->ar <= 95)
                        <td class="text-right" style="background-color: #FFA500">{{ number_format($item->ar) }}</td>
                    @elseif ($item->ar > 95)
                        <td class="text-right" style="background-color: #00FF00">{{ number_format($item->ar) }}</td>
                    @endif

                    @if ($item->rtf <= 0)
                        <td class="text-right" style="background-color: #fff">{{ number_format($item->rtf) }}</td>
                    @elseif ($item->rtf < 90)
                        <td class="text-right" style="background-color: #FF0000">{{ number_format($item->rtf) }}</td>
                    @elseif ($item->ar >= 90 && $item->ar <= 95)
                        <td class="text-right" style="background-color: #FFA500">{{ number_format($item->rtf) }}</td>
                    @elseif ($item->rtf > 95)
                        <td class="text-right" style="background-color: #00FF00">{{ number_format($item->rtf) }}</td>
                    @endif

                    @if ($item->oee <= 0)
                        <td class="text-right" style="background-color: #fff">{{ number_format($item->oee) }}</td>
                    @elseif ($item->oee < 90)
                        <td class="text-right" style="background-color: #FF0000">{{ number_format($item->oee) }}</td>
                    @elseif ($item->ar >= 90 && $item->ar <= 95)
                        <td class="text-right" style="background-color: #FFA500">{{ number_format($item->oee) }}</td>
                    @elseif ($item->oee > 95)
                        <td class="text-right" style="background-color: #00FF00">{{ number_format($item->oee) }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
