@extends('mitbooster::layouts.admin')

@section('content')

    <div>

        @if(MITBooster::getCurrentMethod() != 'getProfile' && $button_cancel)
            @if(g('return_url'))
                <p><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i>
                        &nbsp; {{trans("mixtra.form_back_to_list",['module'=>MITBooster::getCurrentModule()->name])}}</a></p>
            @else
                <p><a title='Main Module' href='{{MITBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
                        &nbsp; {{trans("mixtra.form_back_to_list",['module'=>MITBooster::getCurrentModule()->name])}}</a></p>
            @endif
        @endif

        <div class="card">
            <div class="card-header">
                <strong><i class='{{MITBooster::getCurrentModule()->icon}}'></i> {!! $page_title or "Page Title" !!}</strong>
            </div>

            <div class="card-body" style="padding:0px 0px 0px 0px">
                <?php
                $action = (@$row) ? MITBooster::mainpath("edit-save/$row->id") : MITBooster::mainpath("add-save");
                $return_url = ($return_url) ?: g('return_url');
                ?>
                <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type='hidden' name='return_url' value='{{ @$return_url }}'/>
                    <input type='hidden' name='ref_mainpath' value='{{ MITBooster::mainpath() }}'/>
                    <input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                    @if($hide_form)
                        <input type="hidden" name="hide_form" value='{!! serialize($hide_form) !!}'>
                    @endif
                    <div class="card-body" id="parent-form-area">
                        @if($command == 'detail')
                            @include("mitbooster::default.form_detail")
                        @else
                            @include("mitbooster::default.form_body")
                        @endif
                    </div>

                    <div class="card-footer" style="background: #F5F5F5">

                        <div class="form-group">
                            <label class="control-label col-sm-2"></label>
                            <div class="col-sm-10">
                                @if($button_cancel && MITBooster::getCurrentMethod() != 'getDetail')
                                    @if(g('return_url'))
                                        <a href='{{g("return_url")}}' class='btn btn-default'><i
                                                    class='fa fa-chevron-circle-left'></i> {{trans("mixtra.button_back")}}</a>
                                    @else
                                        <a href='{{MITBooster::mainpath("?".http_build_query(@$_GET)) }}' class='btn btn-default'><i
                                                    class='fa fa-chevron-circle-left'></i> {{trans("mixtra.button_back")}}</a>
                                    @endif
                                @endif
                                @if(MITBooster::getCurrentMethod() == 'getProfile' || MITBooster::isCreate() || MITBooster::isUpdate())

                                    @if(MITBooster::isCreate() && $button_addmore==TRUE && $command == 'add')
                                        <input type="submit" name="submit" value='{{trans("mixtra.button_save_more")}}' class='btn btn-dark'>
                                    @endif

                                    @if(MITBooster::getCurrentMethod() == 'getProfile' || $button_save && $command != 'detail')
                                        <input type="submit" name="submit" value='{{trans("mixtra.button_save")}}' class='btn btn-dark'>
                                    @endif

                                @endif
                            </div>
                        </div>


                    </div><!-- /.box-footer-->

                </form>

            </div>
        </div>
    </div><!--END AUTO MARGIN-->

@endsection