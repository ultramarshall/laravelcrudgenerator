<?php
namespace mixtra\controllers;

use MITBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use mixtra\helpers\FontAwesome;

class ModulesController extends MITController
{
    public function init()
    {
        $this->table = 'mit_modules';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->limit = 100;
        $this->button_add = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_filter = false;
        $this->button_detail = true;
        $this->button_bulk_action = false;
        $this->button_action_style = 'button_icon';
        $this->orderby = ['is_protected' => 'asc', 'name' => 'asc'];

        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Table", "name" => "table_name"];
        $this->col[] = ["label" => "Path", "name" => "path"];
        $this->col[] = ["label" => "Icon", "name" => "icon"];
        $this->col[] = ["label" => "Controller", "name" => "controller"];
        $this->col[] = ["label" => "Protected", "name" => "is_protected", "visible" => false];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", "placeholder" => "Module name here", 'required' => true];

        $tables = MITBooster::listTables();
        $tables_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = $value;

                if (substr($value, 0, 4) == 'mit_') {
                    continue;
                }

                $tables_list[] = $value."|".$label;
            }
        }
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = "[Default] ".$value;
                if (substr($value, 0, 4) == 'mit_') {
                    $tables_list[] = $value."|".$label;
                }
            }
        }

        $this->form[] = ["label" => "Table Name", "name" => "table_name", "type" => "select2", "dataenum" => $tables_list, 'required' => true];

        // $fontawesome = FontAwesome::getIcons();

        // $row = MITBooster::first($this->table, MITBooster::getCurrentId());
        // $custom = view('mitbooster::components.list_icon', compact('fontawesome', 'row'))->render();
        $this->form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'text', 'required' => true];

        $this->script_js = "
 			$(function() {
 				$('#table_name').change(function() {
					var v = $(this).val();
					$('#path').val(v);
				})	
 			})
 			";

        $this->form[] = ["label" => "Path", "name" => "path", "required" => true, 'placeholder' => 'Optional'];
        $this->form[] = ["label" => "Controller", "name" => "controller", "type" => "text", "placeholder" => "(Optional) Auto Generated"];
    }
}
