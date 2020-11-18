@if($form['begin_group'] == '' || $form['begin_group'] == 'true')
<div class="form-group form-datepicker {{$header_group_class}} row {{ ($errors->first($name))?'has-error': '' }}" id="form-group-{{$name}}" style="{{@$form['style']}}">
@endif
    <label class="col-form-label font-weight-bold {{$label_width?:'col-sm-2'}}">{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('mixtra.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        $datamodal_field = explode(',', $form['datamodal_columns'])[0];
        $datamodal_value = DB::table($form['datamodal_table'])->where('id', $value)->first()->$datamodal_field;

        ?>
        <div id='{{$name}}' class="input-group">
            <input type="text" title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class="form-control input-label" name="{{$name}}" value="{{$datamodal_value}}" readonly>
            <input type="hidden" name="{{$name}}" class="input-id" value="{{$value}}">
            <span class="input-group-append">
                <button class="btn btn-primary btn-sm" onclick="showModal{{$name}}()" type="button">
                    @if($form['icon_only'])
                    <i class='fa fa-search'></i>
                    @else
                    <i class='fa fa-search'></i> {{trans('mixtra.datamodal_browse_data')}}
                    @endif
                </button>
                <?php if(strlen($form['datamodal_button_javascript']) > 1){ ?>
                <button class="btn btn-info btn-sm" onclick="{{$form['datamodal_button_javascript']}}" type="button">
                    <i class='{{$form["datamodal_button_icon"]}}'></i> {{$form['datamodal_button_caption']}}
                </button>
                <?php } ?>
            </span>
        </div><!-- /input-group -->

        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
@if($form['end_group'] == '' || $form['end_group'] == 'true')    
</div>
@endif


@push('bottom')
    <script type="text/javascript">
        var url_{{$name}} = "{{MITBooster::mainpath('modal-data')}}?table={{$form['datamodal_table']}}&columns=id,{{$form['datamodal_columns']}}&name_column={{$name}}&where={{urlencode($form['datamodal_where'])}}&select_to={{ urlencode($form['datamodal_select_to']) }}&columns_name_alias={{ urlencode($form['datamodal_columns_alias']) }}&orderby={{ urlencode($form['datamodal_orderby']) }}&link={{ urlencode($form['datamodal_link']) }}";

        function showModal{{$name}}() {
            $('#iframe-modal-{{$name}}').attr('src', url_{{$name}});
            $('#modal-datamodal-{{$name}}').modal('show');
        }

        function hideModal{{$name}}() {
            $('#modal-datamodal-{{$name}}').modal('hide');
        }

        function selectAdditionalData{{$name}}(select_to_json) {
            $.each(select_to_json, function (key, val) {
                if (key == 'datamodal_id') {
                    $('#{{$name}} .input-id').val(val).trigger('change');
                } else if (key == 'datamodal_label') {
                    $('#{{$name}} .input-label').val(val).trigger('change');
                } else {
                    $('#' + key).val(val).trigger('change');
                }
            })
            hideModal{{$name}}();
        }
    </script>


    <div id='modal-datamodal-{{$name}}' class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog {{ $form['datamodal_size']=='large'?'modal-lg':'' }} " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class='fa fa-search'></i> {{trans('mixtra.datamodal_browse_data')}} | {{$form['label']}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <iframe id='iframe-modal-{{$name}}' style="border:0;height: 430px;width: 100%" src=""></iframe>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endpush
