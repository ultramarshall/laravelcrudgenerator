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
    <style>
        thead { display: table-header-group }
        tfoot { display: table-row-group }
        tr { page-break-inside: avoid }
    </style>
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
            <h4>Printed: <strong>{{ $printed }} ({{ $printedBy }})</strong></h4>
        </div>
    </div>
    <table class="table table-bordered color-table primary-table">
        <thead>
            <tr>
                <th width="1%">NO.</th>
                <th width="3%">CELL</th>
                <th width="4%">PO BUYER</th>
                <th width="4%">PROD. ORDER</th>
                <th width="5%">ITEM FG</th>
                <th width="5%">BUYER SKU</th>
                <th width="10%">DESCRIPTION</th>
                <th width="3%">SIZE</th>
                <th width="5%">PROFILE</th>
                <th width="5%">COLOR</th>
                <th width="2%" class="text-right">QTY</th>
                <th width="3%" class="text-right">SPEED</th>
                <th width="2%" class="text-right">WH</th>
                <th width="3%" class="text-right">CBM</th>
                <th width="5%">START</th>
                <th width="5%">STOP</th>
                <th width="3%">LAYOUT</th>
                <th width="2%">TYPE</th>
                <th width="5%">START SHIP DATE</th>
                <th width="5%">END SHIP DATE</th>
                <th width="5%">INSPECTION DATE</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_order = 0;
            ?>
            @foreach ($result as $no => $item)
            <?php
            $style_shipdate = "";
            $style_inspection = "";
            
            if ($item->our_bookings_ship_date <= $item->our_bookings_detail_end_date) {
                $style_shipdate = "background-color: #a80e19;color: #f8f9fa;";
            }
            // if($item->our_bookings_inspection_date <= $item->our_bookings_detail_end_date)
            //     $style_inspection = "background-color: #a80e19;color: #f8f9fa;";

            ?>
                <tr>
                    <td>{{ $no +1 }} </td>
                    <td>{{ $item->our_lines_name }}</td>
                    <td>{{ $item->our_bookings_customer_po }}</td>
                    <td>{{ $item->production_order }}</td>
                    <td>{{ $item->our_items_item_no }}</td>
                    <td>{{ $item->our_items_sku }}</td>
                    <td>{{ $item->our_items_description }}</td>
                    <td>{{ $item->our_items_size }}</td>
                    <td>{{ $item->our_items_profile }}</td>
                    <td>{{ $item->our_items_color }}</td>
                    <td class="text-right">{{ number_format($item->qty,0) }}</td>
                    <td class="text-right">{{ number_format($item->our_bookings_detail_speed,0) }}</td>
                    <td class="text-right">{{ number_format($item->our_bookings_detail_work_hour,3) }}</td>
                    <td class="text-right">{{ number_format($item->our_bookings_detail_cbm_qty,3) }}</td>
                    <td>{{ $item->our_bookings_detail_start_date }}</td>
                    <td>{{ $item->our_bookings_detail_end_date }}</td>
                    <td>{{ $item->our_items_layout_code }}</td>
                    <td>{{ $item->our_bookings_type }}</td>
                    <td style="{{$style_shipdate}}">{{ $item->our_bookings_ship_date }}</td>
                    <td style="{{$style_shipdate}}">{{ $item->our_bookings_ship_end_date }}</td>
                    <td style="{{$style_inspection}}">{{ $item->our_bookings_inspection_date }}</td>
                </tr>
            <?php
                $total_order = $total_order + $item->qty;
            ?>
            @endforeach
                <tr>
                    <td colspan="20">Total Qty: {{ number_format($total_order,0) }}</td>
                </tr>
            <!-- <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th class="text-right">{{ number_format($total_order,0) }}</th>
                <th class="text-right">&nbsp;</th>
                <th class="text-right">&nbsp;</th>
                <th class="text-right">&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr> -->
        </tbody>
    </table>
</body>

</html>
