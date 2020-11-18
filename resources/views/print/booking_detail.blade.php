<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{$title}}</title>

    <link href="{{ public_path('assets/vendor/bootstrap/dist/css/bootstrap.css') }}" rel='stylesheet'/>
    <link href="{{ public_path('assets/css/style.css') }}" rel='stylesheet'/>

    <!-- <link href="http://localhost/production/public/assets/vendor/bootstrap/dist/css/bootstrap.css" rel='stylesheet'/>
    <link href="http://localhost/production/public/assets/css/style.css" rel='stylesheet'/> -->
</head>

<body style="background-color: #ffffff;">
    <div class="row p-b-5 m-b-15" style="border-bottom: 2px solid #111111;">
        <!-- <div class="col-4"><img src="http://localhost/production/public/images/unity.png" height="80px"></div> -->
        <div class="col-4"><img src="{{ public_path('images/unity.png') }}" height="80px"></div>
        <div class="col-8 text-center">
            <h1>{{$title}}</h1>
            <h4>Printed: <strong>{{ $printed }} ({{ $printedBy }})</strong></h4></div>
    </div>
    <?php
        $header = $result[0];
    ?>
    <div class="row">
        <div class="col-4">
            <h4><strong>ORDER INFORMATIONS</strong></h4>
            <table width="100%">
                <tr><td width=100>Booking #</td><td><strong>{{$header->voucher_no}}</strong></td></tr>
                <tr><td>Order Date</td><td><strong>{{date('d M Y',strtotime($header->inquiry_date))}}</strong></td></tr>
                <tr><td>Type</td><td><strong>{{$header->type}}</strong></td></tr>
                <!-- <tr><td>Status</td><td><strong>{{$header->status}}</strong></td></tr> -->
                <tr><td>Customer</td><td><strong>{{$header->customer_name}}</strong></td></tr>
                <tr><td>P/O #</td><td><strong>{{$header->customer_po}}</strong></td></tr>
                <tr><td>Request D/O</td><td><strong>{{date('d M Y',strtotime($header->ship_date))}}</strong> to <strong>{{date('d M Y',strtotime($header->ship_end_date))}}</strong></td></tr>
                <tr><td>Lead Time</td><td><strong>{{number_format($header->lead_time,0)}}</strong> days</td></tr>
            </table>
        </div>
        <div class="col-4">
            <h4><strong>SHIPMENT INFORMATIONS</strong></h4>
            <table width="100%">
                <tr><td width=100>CBM</td><td><strong>{{number_format($header->cbm,3)}}</strong></td></tr>
                <tr><td>Inspection</td><td>
                    <strong>
                    {{$header->inspection == 1 ? 'YES' : 'NO'}}
                    {{empty($header->inspection_date) ? null : '('.date('d M Y',strtotime($header->inspection_date)).')'}}
                    </strong>
                </td></tr>
                <tr><td>Container Load</td><td><strong>{{$header->container_load}}</strong></td></tr>
                <tr><td>Loading Date</td><td><strong>{{empty($header->loading_date)?null:date('d M Y',strtotime($header->loading_date))}}</strong></td></tr>
            </table>
        </div>
        <div class="col-4">
            <h4><strong>PRODUCTION PLANNING</strong></h4>
            <table class="m-b-20" width="100%">
                <tr><td width=100>Work Hour</td><td><strong>{{number_format($header->work_hour,3)}}</strong> hours</td></tr>
                <tr><td>Plan Start</td><td><strong>{{date('d M Y H:i:s',strtotime($header->production_start))}}</strong></td></tr>
                <tr><td>Plan End</td><td><strong>{{date('d M Y H:i:s',strtotime($header->production_end))}}</strong></td></tr>
            </table>
            <h4><strong>MATERIAL RESOURCE PLANNING</strong></h4>
            <table width="100%">
                <tr><td width=100>Plan MRP Date</td><td><strong>{{empty($header->material_request)?null:date('d M Y',strtotime($header->material_request))}}</strong></td></tr>
                <tr><td>Actual MRP</td><td><strong>{{empty($header->actual_mrp)?null:date('d M Y',strtotime($header->actual_mrp))}}</strong></td></tr>
            </table>
        </div>
    </div>
    <table class="table table-bordered color-table primary-table">
        <thead>
            <tr>
                <th width="1%">NO.</th>
                @if($header->status == 'FINAL')
                <th width="7%">PROD. ORDER</th>
                @endif
                <th width="8%">ITEM FG</th>
                <th width="8%">BUYER SKU</th>
                <th width="15%">DESCRIPTION</th>
                <th width="7%">SIZE</th>
                <th width="7%">PROFILE</th>
                <th width="7%">COLOR</th>
                <th width="2%" class="text-right">QTY</th>
                <th width="3%" class="text-right">SPEED</th>
                <th width="2%" class="text-right">WH</th>
                <th width="3%" class="text-right">CBM</th>
                <th width="5%">CELL</th>
                <th width="7%">START</th>
                <th width="7%">STOP</th>
                <th width="10%">LAYOUT</th>
                <th width="6%">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $no => $item)
                <tr>
                    <td>{{ $no +1 }} </td>
                    @if($header->status == 'FINAL')
                    <td>{{ $item->production_order }}</td>
                    @endif
                    <td>{{ $item->item_no }}</td>
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->profile }}</td>
                    <td>{{ $item->color }}</td>
                    <td class="text-right">{{ number_format($item->qty,0) }}</td>
                    <td class="text-right">{{ number_format($item->speed,0) }}</td>
                    <td class="text-right">{{ number_format($item->detail_hour,3) }}</td>
                    <td class="text-right">{{ number_format($item->detail_cbm,3) }}</td>
                    <td>{{ $item->line_name }}</td>
                    <td>{{ $item->start_date }}</td>
                    <td>{{ $item->end_date }}</td>
                    <td>{{ $item->layout_code }}</td>
                    <td>{{ $item->status_detail }}</td>
                </tr>
            @endforeach
            <tr>
                <th>&nbsp;</th>
                @if($header->status == 'FINAL')
                <th>&nbsp;</th>
                @endif
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th class="text-right">{{ number_format($header->total_qty,0) }}</th>
                <th class="text-right">&nbsp;</th>
                <th class="text-right">{{ number_format($item->work_hour,3) }}</th>
                <th class="text-right">{{ number_format($item->cbm,3) }}</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
