@extends('mitbooster::layouts.admin')

@section('content')
@if(!is_null($pre_index_html) && !empty($pre_index_html))
    {!! $pre_index_html !!}
@endif

@if(g('return_url'))
        <p><a href='{{g("return_url")}}'><i class='fa fa-chevron-circle-{{ trans('mixtra.left') }}'></i>
                &nbsp; {{trans('mixtra.form_back_to_list',['module'=>urldecode(g('label'))])}}</a></p>
    @endif

    @if($parent_table)
        <div class="box box-default">
            <div class="box-body table-responsive no-padding">
                <table class='table table-bordered'>
                    <tbody>
                    <tr class='active'>
                        <td colspan="2"><strong><i class='fa fa-bars'></i> {{ ucwords(urldecode(g('label'))) }}</strong></td>
                    </tr>
                    @foreach(explode(',',urldecode(g('parent_columns'))) as $c)
                        <tr>
                            <td width="25%"><strong>
                                    @if(urldecode(g('parent_columns_alias')))
                                        {{explode(',',urldecode(g('parent_columns_alias')))[$loop->index]}}
                                    @else
                                        {{  ucwords(str_replace('_',' ',$c)) }}
                                    @endif
                                </strong></td>
                            <td> {{ $parent_table->$c }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

<!-- <div class="row">
    <div class="col-12">
 -->        
 		<div class="card">
            <div class="card-header">
            	<div class="row">
	            	<div class="col-sm-6 selected-action">
						@if($button_bulk_action && ( ($button_delete && MITBooster::isDelete()) || $button_selected) )
						<div class="btn-group">
							<button type="button" 
								class="btn waves-effect waves-light btn-sm btn-secondary dropdown-toggle" 
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-check-square"></i> {{trans("mixtra.button_selected_action")}}</button>
							<div class="dropdown-menu">
								@if($button_delete && MITBooster::isDelete())
								<a class="dropdown-item small" data-name='delete' href="javascript:void(0)" title="{{trans('mixtra.action_delete_selected')}}"><i class="fa fa-trash"></i> {{trans('mixtra.action_delete_selected')}} </a>
								@endif
								@if($button_selected)
									@foreach($button_selected as $button)
										<a class="dropdown-item small" href="javascript:void(0)" data-name='{{$button["name"]}}' title='{{$button["label"]}}'><i
														class="fa fa-{{$button['icon']}}"></i> {{$button['label']}}</a>
									@endforeach
								@endif
							</div>
						</div>
		                @endif
						@if(!is_null($pre_card_header_html) && !empty($pre_card_header_html))
							{!! $pre_card_header_html !!}
						@endif

		            </div>
		            <div class="col-sm-6 text-right">
			            <div class="btn-group btn-group-sm">
							@if($button_filter)
			                    <a href="javascript:void(0)" id='btn_advanced_filter' data-url-parameter='{{$build_query}}'
			                       title="{{trans('mixtra.filter_dialog_title')}}" class="btn btn-secondary {{(Request::get('filter_column'))?'active':''}}">
			                        <i class="fa fa-filter"></i> {{trans("mixtra.button_filter")}}
			                    </a>
			                @endif
		                </div>
		                <div class="btn-group">
		                	<form method='get' action='{{Request::url()}}'>
					            <div class="btn-group btn-group-sm">
				                    <input type="text" class="form-control form-control-sm" 
				                    	placeholder="{{trans('mixtra.filter_search')}}" 
			                    		name="q" value="{{ Request::get('q') }}" />
					                <button type="submit" 
				                		class="btn waves-effect waves-light btn-sm btn-secondary">
				                		<i class="fa fa-search"></i></button>
				                </div>
			                </form>
		            	</div>
			            	
		                <div class="btn-group">
		                	<form method='get' id='form-limit-paging' action='{{Request::url()}}'>
		                		<input type="hidden" name="limit" id="limit" value="{{ $limit }}" />
					            <div class="btn-group btn-group-sm">
					            	<button type="button" 
					                	class="btn waves-effect waves-light btn-sm btn-secondary dropdown-toggle" 
					                	data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $limit }}</button>
					            	<div class="dropdown-menu">
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(5); $('#form-limit-paging').submit()}">5</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(10); $('#form-limit-paging').submit()}">10</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(20); $('#form-limit-paging').submit()}">20</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(25); $('#form-limit-paging').submit()}">25</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(50); $('#form-limit-paging').submit()}">50</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(100); $('#form-limit-paging').submit()}">100</a>
					                    <a class="dropdown-item small" href="javascript:{$('#limit').val(200); $('#form-limit-paging').submit()}">200</a>
					                </div>
					            </div>
				            </form>
			            </div>
		            </div>
		        </div>
            </div>

            <div class="p-2">
            	<div class="table-responsive">
		            @include("mitbooster::default.table")
		        </div>
	        </div>

	        <div class="card-footer row" style="margin-top: -20px;">
	        	<div class="col-sm-4">{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</div>

				<div class="col-sm-8 mt-1 text-right">
					<p>
						<?php
						$from = $result->count() ? ($result->perPage() * $result->currentPage() - $result->perPage() + 1) : 0;
						$to = $result->perPage() * $result->currentPage() - $result->perPage() + $result->count();
						$total = $result->total();
						?>
						{{ trans("mixtra.filter_rows_total") }} : {{ $from }} {{ trans("mixtra.filter_rows_to") }} {{ $to }} 
						{{ trans("mixtra.filter_rows_of") }} {{ $total }}</p>
				</div>
			</div>

        </div>
    <!-- </div>
</div>
 -->

@if(!is_null($post_index_html) && !empty($post_index_html))
    {!! $post_index_html !!}
@endif

@endsection

@push('head')
	<!--alerts CSS -->
    <link href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('bottom')
    <!-- Sweet-Alert  -->
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert/jquery.sweet-alert.custom.js') }}"></script>
@endpush