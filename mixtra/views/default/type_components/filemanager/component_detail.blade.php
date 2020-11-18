<?php
$ext = pathinfo($value, PATHINFO_EXTENSION);
$files_type = array('pdf');
if(Storage::exists($value) || file_exists($value)):
if(in_array(strtolower($ext), $files_type)):?>
<a data-lightbox='roadtrip' href='{{asset($value)}}'>
        <object style='max-width:150px' title="File For {{$form['label']}}" src='{{asset($value)}}'/></></a>
<?php else:?>
<a href='{{asset($value)}}?download=1' target="_blank">{{trans("mixtra.button_download_file")}}: {{basename($value)}} <i class="fa fa-download"></i></a>
<?php endif;?>
<?php endif;?>
