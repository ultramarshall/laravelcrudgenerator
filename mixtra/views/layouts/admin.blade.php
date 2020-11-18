@extends('mitbooster::layouts.app')

@section('body-class','fixed-layout')

@section('title')
    <title>
    {{ ($page_title)?MITBooster::getSetting('appname').' : '.strip_tags($page_title) : "Admin Area" }}
    </title>
<!--         ($page_title)?Session::get('appname').': '.strip_tags($page_title):"Admin Area" }} -->

@endsection

@section('admin_css')
    <!--JQuery UI -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/jqueryui/jquery-ui.css') }}">
@endsection

@section('wrapper')
<div id="main-wrapper">
<!-- ============================================================== -->
<!-- Topbar header - style you can find in pages.scss -->
<!-- ============================================================== -->
@include('mitbooster::layouts.header')
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
@include('mitbooster::layouts.sidebar')
<!-- ============================================================== -->
<!-- End Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->


<!-- ============================================================== -->
<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-9 align-self-center">
            <?php
            $module = MITBooster::getCurrentModule();
            ?>
            @if($module)
            <div class="row">
                <h4 class="text-themecolor ml-1 mt-1"><i class='{{$module->icon}}'></i> {{($page_title)?:$module->name}}</h4> &nbsp;&nbsp;
                <!--START BUTTON -->
                @if(MITBooster::getCurrentMethod() == 'getIndex' || $is_index == 1)
                    @if($button_show)
                        <a href="{{ MITBooster::mainpath().'?'.http_build_query(Request::all()) }}" id='btn_show_data' class="btn btn-sm btn-info mr-1"
                           title="{{trans('mixtra.action_show_data')}}">
                            <i class="fa fa-table"></i> {{trans('mixtra.action_show_data')}}
                        </a>
                    @endif

                    @if(($button_add && MITBooster::isCreate()) || $is_index == 1)
                        <a href="{{ MITBooster::mainpath('add').'?return_url='.urlencode(Request::fullUrl()).'&parent_id='.g('parent_id').'&parent_field='.$parent_field }}"
                           id='btn_add_new_data' class="btn btn-sm btn-dark mr-1" title="{{trans('mixtra.action_add_data')}}">
                            <i class="fa fa-plus-circle"></i> {{trans('mixtra.action_add_data')}}
                        </a>
                    @endif
                @endif


                @if($button_export && (MITBooster::getCurrentMethod() == 'getIndex' || $is_index == 1))
                    <a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data'
                       class="btn btn-sm btn-warning btn-export-data mr-1">
                        <i class="fa fa-upload"></i> {{trans("mixtra.button_export")}}
                    </a>
                @endif

                @if($button_import && (MITBooster::getCurrentMethod() == 'getIndex' || $is_index == 1))
                    <a href="{{ MITBooster::mainpath('import-data') }}" id='btn_import_data' data-url-parameter='{{$build_query}}' title='Import Data'
                       class="btn btn-sm btn-success btn-import-data mr-1">
                        <i class="fa fa-download"></i> {{trans("mixtra.button_import")}}
                    </a>
                @endif

                <!--ADD ACTION-->
                @if(!empty($index_button))
                    @foreach($index_button as $ib)
                        <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}' class='btn {{($ib['color'])?'btn-'.$ib['color']:'btn-primary'}} btn-sm m-r-5'
                           @if($ib['onClick']) onClick='return {{$ib["onClick"]}}' @endif
                           @if($ib['onMouseOver']) onMouseOver='return {{$ib["onMouseOver"]}}' @endif
                           @if($ib['onMouseOut']) onMouseOut='return {{$ib["onMouseOut"]}}' @endif
                           @if($ib['onKeyDown']) onKeyDown='return {{$ib["onKeyDown"]}}' @endif
                           @if($ib['onLoad']) onLoad='return {{$ib["onLoad"]}}' @endif
                        >
                            <i class='{{$ib["icon"]}}'></i> {{$ib["label"]}}
                        </a>
                    @endforeach
                @endif

            <!--END OF START BUTTON -->
            </div>
            @else
            <h4 class="text-themecolor">{{MITBooster::getSetting('appname')}}</h4>
            <small>Information</small>
            @endif
            </div>
            @if($module)
            <div class="col-md-3 align-self-center text-right">
                <div class="d-flex justify-content-end align-items-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{MITBooster::adminPath()}}">Home</a></li>
                        <li class="breadcrumb-item active">{{($page_title)?:$module->name}}</li>
                    </ol>
                </div>
            </div>
            @endif
        </div>        
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        @if (Session::get('message')!='')
        <div class="alert alert-{{ Session::get('message_type') }}">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
                <h3 class="text-{{ Session::get('message_type') }}"><i class="fa fa-info"></i> {{ Session::get('message_type') }}</h3> {!!Session::get('message')!!}
            </div>
            
        @endif

        <!-- Your Page Content Here -->
        @yield('content')

        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->

<!-- ============================================================== -->
<!-- footer -->
<!-- ============================================================== -->
@include('mitbooster::layouts.footer')
<!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
@endsection

@section('admin_js')
    <!--Slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('assets/js/perfect-scrollbar.jquery.min.js') }}"></script>
    <!--MONEY FORMAT-->
    <script src="{{ asset('assets/js/jquery.price_format.2.0.min.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('assets/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <!--Timepicker -->
    <script src="{{ asset('assets/vendor/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!--JQuery UI -->
    <script src="{{ asset('assets/vendor/jqueryui/jquery-ui.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('assets/vendor/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sparkline/jquery.sparkline.min.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
@endsection
