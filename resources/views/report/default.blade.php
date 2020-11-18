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
        @import url(https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700);

        .skin-astari {}

        .skin-astari .topbar {
            background: #2d200b
        }

        .skin-astari .topbar .top-navbar .navbar-header .navbar-brand .dark-logo {
            display: none
        }

        .skin-astari .topbar .top-navbar .navbar-header .navbar-brand .light-logo {
            display: inline-block;
            color: rgba(255, 255, 255, 0.8)
        }

        .skin-astari .sidebar-nav ul li a.active, .skin-astari .sidebar-nav ul li a:hover {
            color: #2d200b
        }

        .skin-astari .sidebar-nav ul li a.active i, .skin-astari .sidebar-nav ul li a:hover i {
            color: #2d200b
        }

        .skin-astari .sidebar-nav > ul > li.active > a {
            color: #2d200b;
            border-left: 3px solid #2d200b
        }

        .skin-astari .sidebar-nav > ul > li.active > a i {
            color: #2d200b
        }

        .skin-astari .page-titles .breadcrumb .breadcrumb-item.active {
            color: #2d200b
        }

        .skin-astari-dark {}

        .skin-astari-dark .topbar {
            background: #2d200b
        }

        .skin-astari-dark .sidebar-nav ul li a.active, .skin-astari-dark .sidebar-nav ul li a:hover {
            color: #2d200b
        }

        .skin-astari-dark .sidebar-nav ul li a.active i, .skin-astari-dark .sidebar-nav ul li a:hover i {
            color: #2d200b
        }

        .skin-astari-dark .sidebar-nav > ul > li.active > a {
            color: #2d200b;
            border-left: 3px solid #2d200b
        }

        .skin-astari-dark .sidebar-nav > ul > li.active > a i {
            color: #2d200b
        }

        .skin-astari-dark .page-titles .breadcrumb .breadcrumb-item.active {
            color: #2d200b
        }

        .skin-astari-dark .topbar .top-navbar .navbar-header .navbar-brand .dark-logo {
            display: none
        }

        .skin-astari-dark .topbar .top-navbar .navbar-header .navbar-brand .light-logo {
            display: inline-block;
            color: rgba(255, 255, 255, 0.8)
        }

        .skin-astari-dark .left-sidebar {
            background: #2d200b
        }

        .skin-astari-dark .left-sidebar .user-pro-body a.link {
            color: #2d200b
        }

        @media (min-width:768px) {
            .skin-astari-dark.mini-sidebar .sidebar-nav #sidebarnav > li:hover > a,
            .skin-astari-dark.mini-sidebar .sidebar-nav #sidebarnav > li > ul {
                background: #2d200b
            }
        }

        html {
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0)
        }

        body {
            margin: 0;
            font-family: "Open Sans", sans-serif;
            font-size: 11px;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #ffffff;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 0.5rem
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem
        }

        .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
            margin-bottom: 0.5rem;
            font-family: "Open Sans", sans-serif;
            font-weight: 300;
            line-height: 1.2;
            color: inherit
        }

        .h1, h1 {
            font-size: 36px
        }

        .h2, h2 {
            font-size: 30px
        }

        .h3, h3 {
            font-size: 24px
        }

        .h4, h4 {
            font-size: 18px
        }

        .h5, h5 {
            font-size: 16px
        }

        .h6, h6 {
            font-size: 14px
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
        }

        .table td, .table th {
            padding: 0px 5px 0px 5px;
            vertical-align: middle;
        }

        .table thead th {
            vertical-align: middle;
            text-align: center;
        }

        .font-weight-bold {
            font-weight: 700 !important
        }

        .primary {
            background-color: #2d200b;
            color: #dee2e6;
        }

        .center {
            text-align: center;
        }

        .text-right {
            text-align: right !important
        }
        .detail {
            margin-left: auto;
            margin-right: auto;
        }
        .detail tbody td {
            padding-left: 10px;
            border-bottom: 1px solid #dadada;
        }
        .signature {
            margin-left: auto;
            margin-right: auto;
            height: 200px;
        }
    </style>
</head>

<body class="skin-astari fixed-layout">
    <table class="table">
    <tr>
        <td style="width:200px"><img src="{{ public_path('images/unity.png') }}" width="200px"></td>
        <td>
            <table class="table">
                @foreach($header as $head)
                <tr><td class="center">{!! $head['content'] !!}</td></tr>
                @endforeach
            </table>
        </td>
        <td style="width:200px">&nbsp;</td>
    </tr>
    </table>


    <table class="table detail">
        <thead>
            <tr class="primary">
                @foreach($columns as $col)
                    <?php
                    $width = '';
                    if($col['width'])
                        $width = 'width="'.$col['width'].'"';
                    if($col['name'] == 'index')
                        $width = 'width="1%"';
                    ?>
                <th {!!$width!!}>{{$col['label']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php ($i = 1)
            @foreach($data as $item)
            <tr>
                @foreach($columns as $col)
                    <?php
                    $value = $item->{$col['name']};
                    if ($col['callback_php']) {
                        foreach ($item as $k => $v) {
                            $col['callback_php'] = str_replace("[".$k."]", "'".$v."'", $col['callback_php']);
                        }
                        @eval("\$value = ".$col['callback_php'].";");
                    }
                    if ($col['text-align']) {
                        $value = "<div style='text-align:".$col['text-align']."'>".$value."</div>";
                    }
                    if($col['name'] == 'index')
                        $value = $i;
                    ?>
                <td>{!!$value!!}</td>
                @endforeach
                @php ($i++)
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="primary">
                <?php
                foreach($columns as $col) {
                    $value = '';
                    if ($col['text-align'] && $col['label'] != 'No') {
                        $total = 0;
                        foreach($data as $val => $item) {
                            $col_name = $col['name']; 
                            $total += $item->$col_name;
                        }
                        $decimal = 0;
                        if($col['decimal'])
                            $decimal = $col['decimal'];
                        
                        $value = number_format($total, $decimal);
                    }
                    echo '<th style="text-align:right">'.$value.'</th>';
                }
                ?>
            </tr>
        </tfoot>
    </table>
</body>

</html>
