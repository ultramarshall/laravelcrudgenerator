<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/dist/css/bootstrap.css') }}"> -->
    <title>{{$title}}</title>
</head>

<body>
    <table style="width: 100%;">
    @foreach ($data as $key => $d)
        @if ($key % 2 == 0)
        <tr>
        @endif
            <td width="45%">
                <div style="margin-top: 10px; border: 1px solid #000000;padding: 10px;">
                    <div class="text-center" style="font-size:20px; font-weight:bolder; margin-bottom:10px;">{{$title}}</div>
                    <table>
                        <tr>
                            <td width="40%">No.Prod. Order</td>
                            <td width="20%">: {{ $d->production_order }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Item #</td>
                            <td width="20%">: {{ $d->item_no }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Customer PO</td>
                            <td width="20%">: {{ $d->customer_po }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Retailer PO</td>
                            <td width="20%">: {{ $d->retailer_po }}</td>
                        </tr>
                        <tr>
                            <td width="40%">Molding Start</td>
                            <td width="20%">
                                : {{ Carbon\Carbon::parse($d->molding_start)->format('d M') }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%">Molding End</td>
                            <td width="20%">
                                : {{ Carbon\Carbon::parse($d->molding_end)->format('d M') }}
                            </td>
                        </tr>
                        <tr>
                            <td width="40%">Qty</td>
                            <td width="20%">: {{ $d->qty}}</td>
                        </tr>
                        <tr>
                            <td width="40%">Cell</td>
                            <td width="20%">: {{ $d->name }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            @if ($key % 2 == 0)
            <td width="10%">&nbsp;</td>
            @endif

        @if ($key % 2 == 1)
        </tr>
        @endif
    @endforeach
    @if ($key % 2 == 0)
            <td width="10%">&nbsp;</td>
        </tr>
    @endif
    </table>

</body>
</html>
