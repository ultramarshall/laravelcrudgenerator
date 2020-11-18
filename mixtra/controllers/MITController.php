<?php
namespace mixtra\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use MIT;
use MITBooster;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Schema;
use Carbon\Carbon;

class MITController extends Controller
{
    public $data_inputan;

    public $columns_table;

    public $module_name;

    public $page_title;

    public $table;

    public $title_field;

    public $primary_key = 'id';

    public $arr = [];

    public $col = [];

    public $form = [];

    public $data = [];

    public $addaction = [];

    public $orderby = null;

    public $password_candidate = null;

    public $date_candidate = null;

    public $limit = 20;

    public $global_privilege = false;

    public $show_numbering = false;

    public $alert = [];

    public $index_button = [];

    public $button_filter = true;

    public $button_export = true;

    public $button_import = true;

    public $button_show = true;

    public $button_addmore = true;

    public $button_table_action = true;

    public $button_bulk_action = true;

    public $button_add = true;

    public $button_delete = true;

    public $button_cancel = true;

    public $button_save = true;

    public $button_edit = true;

    public $button_detail = true;

    public $button_action_style = 'button_icon';

    public $button_action_width = null;

    public $index_statistic = [];

    public $index_additional_view = [];

    public $pre_index_html = null;
    
    public $pre_card_header_html = null;

    public $post_index_html = null;

    public $load_js = [];

    public $load_css = [];

    public $script_js = null;

    public $style_css = null;

    public $sub_module = [];

    public $show_addaction = true;

    public $table_row_color = [];

    public $button_selected = [];

    public $return_url = null;

    public $parent_field = null;

    public $parent_id = null;

    public $hide_form = [];

    public $index_return = false; //for export

    public $sidebar_mode = 'normal';

    public $link_first = true;

    public $is_index = false;

    public $pk = null;

    public function mitLoader()
    {
        $this->init();

        $this->checkHideForm();

        $this->primary_key = MIT::pk($this->table);
        if ($this->pk != null && $this->pk != '') {
            $this->primary_key = $this->pk;
        }
        $this->columns_table = $this->col;
        $this->data_inputan = $this->form;
        $this->data['pk'] = $this->primary_key;
        $this->data['forms'] = $this->data_inputan;
        $this->data['hide_form'] = $this->hide_form;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
        $this->data['table'] = $this->table;
        $this->data['title_field'] = $this->title_field;
        $this->data['appname'] = MITBooster::getSetting('appname');
        $this->data['alerts'] = $this->alert;
        $this->data['index_button'] = $this->index_button;
        $this->data['show_numbering'] = $this->show_numbering;
        $this->data['button_detail'] = $this->button_detail;
        $this->data['button_edit'] = $this->button_edit;
        $this->data['button_show'] = $this->button_show;
        $this->data['button_add'] = $this->button_add;
        $this->data['button_delete'] = $this->button_delete;
        $this->data['button_filter'] = $this->button_filter;
        $this->data['button_export'] = $this->button_export;
        $this->data['button_addmore'] = $this->button_addmore;
        $this->data['button_cancel'] = $this->button_cancel;
        $this->data['button_save'] = $this->button_save;
        $this->data['button_table_action'] = $this->button_table_action;
        $this->data['button_bulk_action'] = $this->button_bulk_action;
        $this->data['button_import'] = $this->button_import;
        $this->data['button_action_width'] = $this->button_action_width;
        $this->data['button_selected'] = $this->button_selected;
        $this->data['index_statistic'] = $this->index_statistic;
        $this->data['index_additional_view'] = $this->index_additional_view;
        $this->data['table_row_color'] = $this->table_row_color;
        $this->data['pre_index_html'] = $this->pre_index_html;
        $this->data['pre_card_header_html'] = $this->pre_card_header_html;
        
        $this->data['post_index_html'] = $this->post_index_html;
        $this->data['load_js'] = $this->load_js;
        $this->data['load_css'] = $this->load_css;
        $this->data['script_js'] = $this->script_js;
        $this->data['style_css'] = $this->style_css;
        $this->data['sub_module'] = $this->sub_module;
        $this->data['parent_field'] = (g('parent_field')) ?: $this->parent_field;
        $this->data['parent_id'] = (g('parent_id')) ?: $this->parent_id;

        if ($this->sidebar_mode == 'mini') {
            $this->data['sidebar_mode'] = 'sidebar-mini';
        } elseif ($this->sidebar_mode == 'collapse') {
            $this->data['sidebar_mode'] = 'sidebar-collapse';
        } elseif ($this->sidebar_mode == 'collapse-mini') {
            $this->data['sidebar_mode'] = 'sidebar-collapse sidebar-mini';
        } else {
            $this->data['sidebar_mode'] = '';
        }


        if (MITBooster::getCurrentMethod() == 'getProfile') {
            Session::put('current_row_id', MITBooster::myId());
            $this->data['return_url'] = Request::fullUrl();
        }

        view()->share($this->data);
    }

    public function cbView($template, $data)
    {
        $this->mitLoader();
        echo view($template, $data);
    }

    private function checkHideForm()
    {
        if ($this->hide_form && count($this->hide_form)) {
            foreach ($this->form as $i => $f) {
                if (in_array($f['name'], $this->hide_form)) {
                    unset($this->form[$i]);
                }
            }
        }
    }

    public function getIndex()
    {
        $this->mitLoader();

        $module = MITBooster::getCurrentModule();

        if (! MITBooster::isView() && $this->global_privilege == false) {
            MITBooster::insertLog(trans('MITBooster.log_try_view', ['module' => $module->name]));
            MITBooster::redirect(MITBooster::adminPath(), trans('MITBooster.denied_access'));
        }

        if (Request::get('parent_table')) {
            $parentTablePK = MIT::pk(g('parent_table'));
            $data['parent_table'] = DB::table(Request::get('parent_table'))->where($parentTablePK, Request::get('parent_id'))->first();
            if (Request::get('foreign_key')) {
                $data['parent_field'] = Request::get('foreign_key');
            } else {
                $data['parent_field'] = MIT::getTableForeignKey(g('parent_table'), $this->table);
            }

            if ($parent_field) {
                foreach ($this->columns_table as $i => $col) {
                    if ($col['name'] == $parent_field) {
                        unset($this->columns_table[$i]);
                    }
                }
            }
        }

        $data['table'] = $this->table;
        $data['table_pk'] = MIT::pk($this->table);
        $data['page_title'] = $module->name;
        if ($this->page_title) {
            $data['page_title'] = $this->page_title;
        }
        $data['page_description'] = trans('mixtra.default_module_description');
        $data['date_candidate'] = $this->date_candidate;
        $data['limit'] = $limit = (Request::get('limit')) ? Request::get('limit') : $this->limit;
        if ($this->export_limit) {
            $data['limit'] = $limit = $this->export_limit;
        }

        $tablePK = $data['table_pk'];
        $table_columns = MIT::getTableColumns($this->table);
        $result = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));

        if (Request::get('parent_id')) {
            $table_parent = $this->table;
            $table_parent = MITBooster::parseSqlTable($table_parent)['table'];
            $result->where($table_parent.'.'.Request::get('foreign_key'), Request::get('parent_id'));
        }

        $this->hook_query_index($result);

        if (in_array('deleted_at', $table_columns)) {
            $result->where($this->table.'.deleted_at', null);
        }

        $alias = [];
        $join_alias_count = 0;
        $join_table_temp = [];
        $table = $this->table;
        $columns_table = $this->columns_table;
        foreach ($columns_table as $index => $coltab) {
            $join = @$coltab['join'];
            $join_where = @$coltab['join_where'];
            $join_id = @$coltab['join_id'];
            $field = @$coltab['name'];
            $join_table_temp[] = $table;

            if (! $field) {
                continue;
            }

            if (strpos($field, ' as ') !== false) {
                $field = substr($field, strpos($field, ' as ') + 4);
                $field_with = (array_key_exists('join', $coltab)) ? str_replace(",", ".", $coltab['join']) : $field;
                $result->addselect(DB::raw($coltab['name']));
                $columns_table[$index]['type_data'] = 'varchar';
                $columns_table[$index]['field'] = $field;
                $columns_table[$index]['field_raw'] = $field;
                $columns_table[$index]['field_with'] = $field_with;
                $columns_table[$index]['is_subquery'] = true;
                continue;
            }

            if (strpos($field, '.') !== false) {
                $result->addselect($field);
            } else {
                $result->addselect($table.'.'.$field);
            }

            $field_array = explode('.', $field);

            if (isset($field_array[1])) {
                $field = $field_array[1];
                $table = $field_array[0];
            } else {
                $table = $this->table;
            }

            if ($join) {
                $join_exp = explode(',', $join);

                $join_table = $join_exp[0];
                $joinTablePK = MIT::pk($join_table);
                $join_column = $join_exp[1];
                $join_alias = str_replace(".", "_", $join_table);

                if (in_array($join_table, $join_table_temp)) {
                    $join_alias_count += 1;
                    $join_alias = $join_table.$join_alias_count;
                }
                $join_table_temp[] = $join_table;

                $result->leftjoin($join_table.' as '.$join_alias, $join_alias.(($join_id) ? '.'.$join_id : '.'.$joinTablePK), '=', DB::raw($table.'.'.$field.(($join_where) ? ' AND '.$join_where.' ' : '')));
                $result->addselect($join_alias.'.'.$join_column.' as '.$join_alias.'_'.$join_column);

                $join_table_columns = MITBooster::getTableColumns($join_table);
                if ($join_table_columns) {
                    foreach ($join_table_columns as $jtc) {
                        $result->addselect($join_alias.'.'.$jtc.' as '.$join_alias.'_'.$jtc);
                    }
                }

                $alias[] = $join_alias;
                $columns_table[$index]['type_data'] = MITBooster::getFieldType($join_table, $join_column);
                $columns_table[$index]['field'] = $join_alias.'_'.$join_column;
                $columns_table[$index]['field_with'] = $join_alias.'.'.$join_column;
                $columns_table[$index]['field_raw'] = $join_column;

                @$join_table1 = $join_exp[2];
                @$joinTable1PK = MIT::pk($join_table1);
                @$join_column1 = $join_exp[3];
                @$join_alias1 = $join_table1;

                if ($join_table1 && $join_column1) {
                    if (in_array($join_table1, $join_table_temp)) {
                        $join_alias_count += 1;
                        $join_alias1 = $join_table1.$join_alias_count;
                    }

                    $join_table_temp[] = $join_table1;

                    $result->leftjoin($join_table1.' as '.$join_alias1, $join_alias1.'.'.$joinTable1PK, '=', $join_alias.'.'.$join_column);
                    $result->addselect($join_alias1.'.'.$join_column1.' as '.$join_column1.'_'.$join_alias1);
                    $alias[] = $join_alias1;
                    $columns_table[$index]['type_data'] = MITBooster::getFieldType($join_table1, $join_column1);
                    $columns_table[$index]['field'] = $join_column1.'_'.$join_alias1;
                    $columns_table[$index]['field_with'] = $join_alias1.'.'.$join_column1;
                    $columns_table[$index]['field_raw'] = $join_column1;
                }
            } else {
                if (isset($field_array[1])) {
                    $result->addselect($table.'.'.$field.' as '.$table.'_'.$field);
                    $columns_table[$index]['type_data'] = MITBooster::getFieldType($table, $field);
                    $columns_table[$index]['field'] = $table.'_'.$field;
                    $columns_table[$index]['field_raw'] = $table.'.'.$field;
                } else {
                    $result->addselect($table.'.'.$field);
                    $columns_table[$index]['type_data'] = MITBooster::getFieldType($table, $field);
                    $columns_table[$index]['field'] = $field;
                    $columns_table[$index]['field_raw'] = $field;
                }
                
                $columns_table[$index]['field_with'] = $table.'.'.$field;
            }
        }

        if (Request::get('q')) {
            $result->where(function ($w) use ($columns_table, $request) {
                foreach ($columns_table as $col) {
                    if (! $col['field_with']) {
                        continue;
                    }
                    if ($col['is_subquery']) {
                        continue;
                    }
                    $w->orwhere($col['field_with'], "like", "%".Request::get("q")."%");
                }
            });
        }

        if (Request::get('where')) {
            foreach (Request::get('where') as $k => $v) {
                $result->where($table.'.'.$k, $v);
            }
        }

        $filter_is_orderby = false;
        if (Request::get('filter_column')) {
            $filter_column = Request::get('filter_column');
            $result->where(function ($w) use ($filter_column, $fc) {
                foreach ($filter_column as $key => $fc) {
                    $value = @$fc['value'];
                    $type = @$fc['type'];

                    if ($type == 'empty') {
                        $w->whereNull($key)->orWhere($key, '');
                        continue;
                    }

                    if ($value == '' || $type == '') {
                        continue;
                    }

                    if ($type == 'between') {
                        continue;
                    }

                    switch ($type) {
                        default:
                            if ($key && $type && $value) {
                                $w->where($key, $type, $value);
                            }
                            break;
                        case 'like':
                        case 'not like':
                            $value = '%'.$value.'%';
                            if ($key && $type && $value) {
                                $w->where($key, $type, $value);
                            }
                            break;
                        case 'in':
                        case 'not in':
                            if ($value) {
                                $value = explode(',', $value);
                                if ($key && $value) {
                                    $w->whereIn($key, $value);
                                }
                            }
                            break;
                    }
                }
            });

            foreach ($filter_column as $key => $fc) {
                $value = @$fc['value'];
                $type = @$fc['type'];
                $sorting = @$fc['sorting'];

                if ($sorting != '') {
                    if ($key) {
                        $result->orderby($key, $sorting);
                        $filter_is_orderby = true;
                    }
                }

                if ($type == 'between') {
                    if ($key && $value) {
                        $from = date('Y-m-d H:i:s', strtotime($value[0]));
                        $to = date('Y-m-d H:i:s', strtotime('+23 hour +59 minutes +59 seconds', strtotime($value[1])));
                        $result->whereBetween($key, [$from, $to]);
                    }
                } else {
                    continue;
                }
            }
        }

        if ($filter_is_orderby == true) {
            $data['result'] = $result->paginate($limit);
        } else {
            if ($this->orderby) {
                if (is_array($this->orderby)) {
                    foreach ($this->orderby as $k => $v) {
                        if (strpos($k, '.') !== false) {
                            $orderby_table = explode(".", $k)[0];
                            $k = explode(".", $k)[1];
                        } else {
                            $orderby_table = $this->table;
                        }
                        $result->orderby($orderby_table.'.'.$k, $v);
                    }
                } else {
                    $this->orderby = explode(";", $this->orderby);
                    foreach ($this->orderby as $o) {
                        $o = explode(",", $o);
                        $k = $o[0];
                        $v = $o[1];
                        if (strpos($k, '.') !== false) {
                            $orderby_table = explode(".", $k)[0];
                            $k = explode(".", $k)[1];
                        } else {
                            $orderby_table = $this->table;
                        }
                        $result->orderby($orderby_table.'.'.$k, $v);
                    }
                }
                $data['result'] = $result->paginate($limit);
            // dd($result->paginate($limit));
            } else {
                $data['result'] = $result->orderby($this->table.'.'.$this->primary_key, 'desc')->paginate($limit);
            }
        }

        $data['columns'] = $columns_table;
        // log::info($result->toSql());
        // log::info($result->getBindings());

        if ($this->index_return) {
            //dd($data['result']);
            return $data;
        }

        //LISTING INDEX HTML
        $addaction = $this->data['addaction'];

        if ($this->sub_module) {
            foreach ($this->sub_module as $s) {
                $table_parent = MITBooster::parseSqlTable($this->table)['table'];
                $addaction[] = [
                    'label' => $s['label'],
                    'icon' => $s['button_icon'],
                    'url' => MITBooster::adminPath($s['path']).'?return_url='.urlencode(Request::fullUrl()).'&parent_table='.$table_parent.'&parent_columns='.$s['parent_columns'].'&parent_columns_alias='.$s['parent_columns_alias'].'&parent_id=['.(! isset($s['custom_parent_id']) ? "id" : $s['custom_parent_id']).']&foreign_key='.$s['foreign_key'].'&label='.urlencode($s['label']),
                    'color' => $s['button_color'],
                    'showIf' => $s['showIf'],
                ];
            }
        }

        $mainpath = MITBooster::mainpath();
        $orig_mainpath = $this->data['mainpath'];
        $title_field = $this->title_field;
        $html_contents = [];
        $page = (Request::get('page')) ? Request::get('page') : 1;
        $number = ($page - 1) * $limit + 1;
        //dd($result->toSql());
        foreach ($data['result'] as $row) {
            $html_content = [];

            if ($this->button_bulk_action) {
                $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
            }

            if ($this->show_numbering) {
                $html_content[] = $number.'. ';
                $number++;
            }

            $first_column = true;

            foreach ($columns_table as $col) {
                if ($col['visible'] === false) {
                    continue;
                }

                $value = @$row->{$col['field']};
                $title = @$row->{$this->title_field};
                $label = $col['label'];

                if (isset($col['image'])) {
                    if ($value == '') {
                        $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('assets/images/user.png')."'><img width='40px' height='40px' src='".asset('assets/images/user.png')."'/></a>";
                    } else {
                        $pic = (strpos($value, 'http://') !== false) ? $value : asset($value);
                        $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
                    }
                }

                if (@$col['download']) {
                    $url = (strpos($value, 'http://') !== false) ? $value : asset($value).'?download=1';
                    if ($value) {
                        $value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
                    } else {
                        $value = " - ";
                    }
                }

                if ($col['str_limit']) {
                    $value = trim(strip_tags($value));
                    $value = str_limit($value, $col['str_limit']);
                }

                if ($col['nl2br']) {
                    $value = nl2br($value);
                }

                if ($col['callback_php']) {
                    foreach ($row as $k => $v) {
                        $col['callback_php'] = str_replace("[".$k."]", "'".$v."'", $col['callback_php']);
                    }
                    
                    //if($value) {
                    @eval("\$value = ".$col['callback_php'].";");
                    //}
                }

                //New method for callback
                if (isset($col['callback'])) {
                    $value = call_user_func($col['callback'], $row);
                }

                $datavalue = @unserialize($value);
                if ($datavalue !== false) {
                    if ($datavalue) {
                        $prevalue = [];
                        foreach ($datavalue as $d) {
                            if ($d['label']) {
                                $prevalue[] = $d['label'];
                            }
                        }
                        if ($prevalue && count($prevalue)) {
                            $value = implode(", ", $prevalue);
                        }
                    }
                }

                if ($this->link_first && $first_column) {
                    if (MITBooster::isUpdate() && $this->button_edit) {
                        $value = "<a href='".MITBooster::mainpath('edit/').$row->id."?return_url=".urlencode(Request::fullUrl())."'>".$value."</a>";
                    } elseif (MITBooster::isRead() && $this->button_detail) {
                        $value = "<a href='".MITBooster::mainpath('detail/').$row->id."?return_url=".urlencode(Request::fullUrl())."'>".$value."</a>";
                    }
                    $first_column = false;
                }


                $html_content[] = $value;
            } //end foreach columns_table


            if ($this->button_table_action):
                $parent_field = $data['parent_field'];
            $button_action_style = $this->button_action_style;
            $width = "width: 120px;";
            if ($this->button_action_width) {
                $width = 'min-width:'.$this->button_action_width.'px;max-width:'.$this->button_action_width.'px;';
            }
            $html_content[] = "<div class='button_action' style='text-align:right;".$width."'>".view('mitbooster::components.action', compact('addaction', 'row', 'button_action_style', 'parent_field'))->render()."</div>";

            endif;//button_table_action

            foreach ($html_content as $i => $v) {
                $this->hook_row_index($i, $v);
                $this->hook_row_data($row, $i, $v);
                $html_content[$i] = $v;
            }

            $html_contents[] = $html_content;
        } //end foreach data[result]

        $html_contents = ['html' => $html_contents, 'data' => $data['result']];

        $data['html_contents'] = $html_contents;
        $data['is_index'] = $this->is_index;

        return view("mitbooster::default.index", $data);
    }

    public function getExportData()
    {
        return redirect(MITBooster::mainpath());
    }

    public function postExportData()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(180);

        $this->limit = Request::input('limit');
        $this->index_return = true;
        $filetype = Request::input('fileformat');
        $filename = Request::input('filename');
        $papersize = Request::input('page_size');
        $paperorientation = Request::input('page_orientation');
        $response = $this->getIndex();
        //dd($response);

        if (Request::input('default_paper_size')) {
            DB::table('mit_settings')->where('name', 'default_paper_size')->update(['content' => $papersize]);
        }

        switch ($filetype) {
            case "pdf":
                return $this->exportPdf($response, $papersize, $paperorientation, $filename);
            case 'xls':
                return $this->exportXls($response, $papersize, $paperorientation, $filename);
            case 'csv':
                return $this->exportCsv($response, $papersize, $paperorientation, $filename);
        }
    }

    public function exportPdf($response, $papersize, $paperorientation, $filename)
    {
        $view = view('mitbooster::export', $response)->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper($papersize, $paperorientation);

        return $pdf->stream($filename.'.pdf');
    }

    public function exportXls($response, $papersize, $paperorientation, $filename)
    {
        Excel::create($filename, function ($excel) use ($response) {
            $excel->setTitle($filename)->setCreator("MITBooster.com")->setCompany(MITBooster::getSetting('appname'));
            $excel->sheet($filename, function ($sheet) use ($response) {
                $sheet->setOrientation($paperorientation);
                $sheet->loadview('mitbooster::export', $response);
            });
        })->export('xls');
    }

    public function exportCsv($response, $papersize, $paperorientation, $filename)
    {
        Excel::create($filename, function ($excel) use ($response) {
            $excel->setTitle($filename)->setCreator("MITBooster.com")->setCompany(MITBooster::getSetting('appname'));
            $excel->sheet($filename, function ($sheet) use ($response) {
                $sheet->setOrientation($paperorientation);
                $sheet->loadview('mitbooster::export', $response);
            });
        })->export('csv');
    }

    public function postDataQuery()
    {
        $query = Request::get('query');
        $query = DB::select(DB::raw($query));

        return response()->json($query);
    }

    public function getDataTable()
    {
        $table = Request::get('table');
        $label = Request::get('label');
        $datatableWhere = urldecode(Request::get('datatable_where'));
        $foreign_key_name = Request::get('fk_name');
        $foreign_key_value = Request::get('fk_value');
        if ($table && $label && $foreign_key_name && $foreign_key_value) {
            $query = DB::table($table);
            if ($datatableWhere) {
                $query->whereRaw($datatableWhere);
            }
            $query->select('id as select_value', $label.' as select_label');
            $query->where($foreign_key_name, $foreign_key_value);
            $query->orderby($label, 'asc');

            return response()->json($query->get());
        } else {
            return response()->json([]);
        }
    }

    public function getModalData()
    {
        $table = Request::get('table');
        $orderby = Request::get('orderby');
        $where = Request::get('where');
        $where = urldecode($where);
        $columns = Request::get('columns');
        $columns = explode(",", $columns);

        $table = MITBooster::parseSqlTable($table)['table'];
        $tablePK = MIT::pk($table);
        $result = DB::table($table);

        if (Request::get('q')) {
            $result->where(function ($where) use ($columns) {
                foreach ($columns as $c => $col) {
                    if ($c == 0) {
                        $where->where($col, 'like', '%'.str_replace(' ', '%', Request::get('q')).'%');
                    } else {
                        $where->orWhere($col, 'like', '%'.str_replace(' ', '%', Request::get('q')).'%');
                    }
                }
            });
        }

        if ($where) {
            $result->whereraw($where);
        }

        if ($orderby) {
            $result->orderby($orderby, 'asc');
        } else {
            $result->orderby($tablePK, 'desc');
        }

        $data['result'] = $result->paginate(6);
        $data['columns'] = $columns;

        return view('mitbooster::default.type_components.datamodal.browser', $data);
    }

    public function getUpdateSingle()
    {
        $table = Request::get('table');
        $column = Request::get('column');
        $value = Request::get('value');
        $id = Request::get('id');
        $tablePK = MIT::pk($table);
        DB::table($table)->where($tablePK, $id)->update([$column => $value]);

        return redirect()->back()->with(['message_type' => 'success', 'message' => trans('MITBooster.alert_delete_data_success')]);
    }

    public function getFindData()
    {
        $q = Request::get('q');
        $id = Request::get('id');
        $limit = Request::get('limit') ?: 10;
        $format = Request::get('format');

        $table1 = (Request::get('table1')) ?: $this->table;
        $table1PK = MIT::pk($table1);
        $column1 = (Request::get('column1')) ?: $this->title_field;

        @$table2 = Request::get('table2');
        @$column2 = Request::get('column2');

        @$table3 = Request::get('table3');
        @$column3 = Request::get('column3');

        $where = Request::get('where');

        $fk = Request::get('fk');
        $fk_value = Request::get('fk_value');

        if ($q || $id || $table1) {
            $rows = DB::table($table1);
            $rows->select($table1.'.*');
            $rows->take($limit);

            if (MITBooster::isColumnExists($table1, 'deleted_at')) {
                $rows->where($table1.'.deleted_at', null);
            }

            if ($fk && $fk_value) {
                $rows->where($table1.'.'.$fk, $fk_value);
            }

            if ($table1 && $column1) {
                $orderby_table = $table1;
                $orderby_column = $column1;
            }

            if ($table2 && $column2) {
                $table2PK = MIT::pk($table2);
                $rows->join($table2, $table2.'.'.$table2PK, '=', $table1.'.'.$column1);
                $columns = MITBooster::getTableColumns($table2);
                foreach ($columns as $col) {
                    $rows->addselect($table2.".".$col." as ".$table2."_".$col);
                }
                $orderby_table = $table2;
                $orderby_column = $column2;
            }

            if ($table3 && $column3) {
                $table3PK = MIT::pk($table3);
                $rows->join($table3, $table3.'.'.$table3PK, '=', $table2.'.'.$column2);
                $columns = MITBooster::getTableColumns($table3);
                foreach ($columns as $col) {
                    $rows->addselect($table3.".".$col." as ".$table3."_".$col);
                }
                $orderby_table = $table3;
                $orderby_column = $column3;
            }

            if ($id) {
                $rows->where($table1.".".$table1PK, $id);
            }

            if ($where) {
                $rows->whereraw($where);
            }

            if ($format) {
                $format = str_replace('&#039;', "'", $format);
                $rows->addselect(DB::raw("CONCAT($format) as text"));
                if ($q) {
                    $rows->whereraw("CONCAT($format) like '%".$q."%'");
                }
            } else {
                $rows->addselect($orderby_table.'.'.$orderby_column.' as text');
                if ($q) {
                    $rows->where($orderby_table.'.'.$orderby_column, 'like', '%'.$q.'%');
                }
                $rows->orderBy($orderby_table.'.'.$orderby_column, 'asc');
            }

            $result = [];
            $result['items'] = $rows->get();
        } else {
            $result = [];
            $result['items'] = [];
        }

        return response()->json($result);
    }

    public function validation($id = null)
    {
        $request_all = Request::all();
        $array_input = [];
        foreach ($this->data_inputan as $di) {
            if (!$this->validation_input($di, $request_all, $array_input)) {
                continue;
            }
        }

        $validator = Validator::make($request_all, $array_input);

        if ($validator->fails()) {
            $message = $validator->messages();
            $message_all = $message->all();

            if (Request::ajax()) {
                $res = response()->json([
                    'message' => trans('mixtra.alert_validation_error', ['error' => implode(', ', $message_all)]),
                    'message_type' => 'warning',
                ])->send();
                exit;
            } else {
                $res = redirect()->back()->with("errors", $message)->with([
                    'message' => trans('mixtra.alert_validation_error', ['error' => implode(', ', $message_all)]),
                    'message_type' => 'warning',
                ])->withInput();
                \Session::driver()->save();
                $res->send();
                exit;
            }
        }
    }

    public function validation_input($di, $request_all, &$array_input)
    {
        $ai = [];
        $name = $di['name'];

        // if ($di['type'] != 'tab' && !isset($request_all[$name])) {
        //     return false;
        // }

        if ($di['type'] != 'upload') {
            if (@$di['required']) {
                $ai[] = 'required';
            }
        }

        if ($di['type'] == 'upload') {
            if ($id) {
                $row = DB::table($this->table)->where($this->primary_key, $id)->first();
                if ($row->{$di['name']} == '') {
                    $ai[] = 'required';
                }
            }
        }

        if (@$di['min']) {
            $ai[] = 'min:'.$di['min'];
        }
        if (@$di['max']) {
            $ai[] = 'max:'.$di['max'];
        }
        if (@$di['image']) {
            $ai[] = 'image';
        }
        if (@$di['mimes']) {
            $ai[] = 'mimes:'.$di['mimes'];
        }
        $name = $di['name'];

        if (! $name) {
            return false;
        }

        if ($di['type'] == 'money') {
            $request_all[$name] = preg_replace('/[^\d-]+/', '', $request_all[$name]);
        }

        if ($di['type'] == 'tab') {
            foreach ($di['tabpages'] as $tabpage) {
                foreach ($tabpage['pages'] as $page) {
                    if (!$this->validation_input($page, $request_all, $array_input)) {
                        continue;
                    }
                }
            }
            return false;
        }

        if ($di['type'] == 'child') {
            $slug_name = str_slug($di['label'], '');
            foreach ($di['columns'] as $child_col) {
                if (isset($child_col['validation'])) {
                    //https://laracasts.com/discuss/channels/general-discussion/array-validation-is-not-working/
                    if (strpos($child_col['validation'], 'required') !== false) {
                        $array_input[$slug_name.'-'.$child_col['name']] = 'required';

                        str_replace('required', '', $child_col['validation']);
                    }

                    $array_input[$slug_name.'-'.$child_col['name'].'.*'] = $child_col['validation'];
                }
            }
        }

        if (@$di['validation']) {
            $exp = explode('|', $di['validation']);
            if ($exp && count($exp)) {
                foreach ($exp as &$validationItem) {
                    if (substr($validationItem, 0, 6) == 'unique') {
                        $parseUnique = explode(',', str_replace('unique:', '', $validationItem));
                        $uniqueTable = ($parseUnique[0]) ?: $this->table;
                        $uniqueColumn = ($parseUnique[1]) ?: $name;

                        $first_data = db::table($uniqueTable)->where($uniqueColumn, $request_all[$uniqueColumn])->first();
                        $uniqueIgnoreId = $first_data->id;
                        // dd($uniqueIgnoreId);
                        
                        //Make sure table name
                        $uniqueTable = MIT::parseSqlTable($uniqueTable)['table'];

                        //Rebuild unique rule
                        $uniqueRebuild = [];
                        $uniqueRebuild[] = $uniqueTable;
                        $uniqueRebuild[] = $uniqueColumn;
                        if ($uniqueIgnoreId) {
                            $uniqueRebuild[] = $uniqueIgnoreId;
                        } else {
                            $uniqueRebuild[] = 'NULL';
                        }

                        //Check whether deleted_at exists or not
                        if (MIT::isColumnExists($uniqueTable, 'deleted_at')) {
                            $uniqueRebuild[] = MIT::findPrimaryKey($uniqueTable);
                            $uniqueRebuild[] = 'deleted_at';
                            $uniqueRebuild[] = 'NULL';
                        }
                        $uniqueRebuild = array_filter($uniqueRebuild);
                        $validationItem = 'unique:'.implode(',', $uniqueRebuild);
                    }
                }
            } else {
                $exp = [];
            }

            $validation = implode('|', $exp);
            

            $array_input[$name] = $validation;
        } else {
            $array_input[$name] = implode('|', $ai);
        }
        return true;
    }

    public function input_assignment($id = null)
    {
        $hide_form = (Request::get('hide_form')) ? unserialize(Request::get('hide_form')) : [];
        foreach ($this->data_inputan as $ro) {
            if (!$this->input_arr($ro)) {
                continue;
            }
        }
    }

    public function input_arr($ro)
    {
        $name = $ro['name'];


        if ($ro['type'] == 'tabpage' || $ro['type'] == 'hr' || $ro['type'] == 'custom'
            || $ro['type'] == 'blank' || $ro['type'] == 'image' || $ro['type'] == 'label') {
            return false;
        }
        // Log::info($ro['type']);
        
        if (! $name) {
            return false;
        }

        if ($ro['exception']) {
            return false;
        }

        if ($name == 'hide_form') {
            return false;
        }

        if ($hide_form && count($hide_form)) {
            if (in_array($name, $hide_form)) {
                return false;
            }
        }

        if ($ro['type'] == 'checkbox' && $ro['relationship_table']) {
            return false;
        }

        if ($ro['type'] == 'select2' && $ro['relationship_table']) {
            return false;
        }

        $inputdata = Request::get($name);

        if ($ro['type'] == 'money' || $ro['type'] == 'number') {
            $inputdata = str_replace(',', '', $inputdata);
        }

        if ($ro['type'] == 'datetime') {
            if ($inputdata) {
                $inputdata = date('Y-m-d H:i:s', strtotime($inputdata));
            }
        }

        if ($ro['type'] == 'date') {
            if ($inputdata) {
                $inputdata = date('Y-m-d', strtotime($inputdata));
            }
        }

        if ($ro['type'] == 'tab') {
            foreach ($ro['tabpages'] as $tabpage) {
                foreach ($tabpage['pages'] as $page) {
                    if (!$this->input_arr($page)) {
                        continue;
                    }
                }
            }
            return false;
        }

        if ($ro['type'] == 'group') {
            foreach ($ro['groups'] as $group) {
                foreach ($group['pane'] as $pane) {
                    if (!$this->input_arr($pane)) {
                        continue;
                    }
                }
            }
            return false;
        }

        if ($ro['type'] == 'child') {
            return false;
        }

        if ($ro['type'] == 'order_detail') {
            return false;
        }

        if ($ro['type'] == 'data_detail') {
            return false;
        }

        if ($ro['type'] == 'custom_summary') {
            // dd($ro);
            $name = $ro['name'].'_value';
            $inputdata = Request::get($name);
            $inputdata = str_replace(',', '', $inputdata);
            $this->arr[$name] = $inputdata;
            return true;
        }

        if ($ro['type'] == 'custom_percent') {
            // dd($ro);
            $name = $ro['name'].'_perc';
            $inputdata = Request::get($name);
            $inputdata = str_replace(',', '', $inputdata);
            $this->arr[$name] = $inputdata;
            
            $name = $ro['name'].'_value';
            $inputdata = Request::get($name);
            $inputdata = str_replace(',', '', $inputdata);
            $this->arr[$name] = $inputdata;
            return true;
        }

        if ($name) {
            if ($inputdata != '') {
                $this->arr[$name] = $inputdata;
            } else {
                // dd($name);
                if (MIT::isColumnNULL($this->table, $name) 
                    && $ro['type'] != 'upload') {
                    if($ro['type'] != 'date' || $ro['type'] != 'datetime')
                        $this->arr[$name] = null;    
                    else
                        return false;
                } else {
                    $this->arr[$name] = "";
                }
            }
        }

        $password_candidate = explode(',', config('mixtra.PASSWORD_FIELDS_CANDIDATE'));
        if (in_array($name, $password_candidate)) {
            if (! empty($this->arr[$name])) {
                $this->arr[$name] = Hash::make($this->arr[$name]);
            } else {
                unset($this->arr[$name]);
            }
        }

        if ($ro['type'] == 'checkbox') {
            if (is_array($inputdata)) {
                if ($ro['datatable'] != '') {
                    $table_checkbox = explode(',', $ro['datatable'])[0];
                    $field_checkbox = explode(',', $ro['datatable'])[1];
                    $table_checkbox_pk = MIT::pk($table_checkbox);
                    $data_checkbox = DB::table($table_checkbox)->whereIn($table_checkbox_pk, $inputdata)->pluck($field_checkbox)->toArray();
                    $this->arr[$name] = implode(";", $data_checkbox);
                } else {
                    $this->arr[$name] = implode(";", $inputdata);
                }
            }
        }

        //multitext colomn
        if ($ro['type'] == 'multitext') {
            $name = $ro['name'];
            $multitext = "";
            $maxI = ($this->arr[$name])?count($this->arr[$name]):0;
            for ($i = 0; $i <= $maxI - 1; $i++) {
                $multitext .= $this->arr[$name][$i]."|";
            }
            $multitext = substr($multitext, 0, strlen($multitext) - 1);
            $this->arr[$name] = $multitext;
        }

        if ($ro['type'] == 'googlemaps') {
            if ($ro['latitude'] && $ro['longitude']) {
                $latitude_name = $ro['latitude'];
                $longitude_name = $ro['longitude'];
                $this->arr[$latitude_name] = Request::get('input-latitude-'.$name);
                $this->arr[$longitude_name] = Request::get('input-longitude-'.$name);
            }
        }

        if ($ro['type'] == 'select' || $ro['type'] == 'select2') {
            if ($ro['datatable']) {
                if ($inputdata == '') {
                    $this->arr[$name] = 0;
                }
            }
        }
        // if($ro['type'] == 'date')
        //     $this->arr[$name];

        if (@$ro['type'] == 'upload') {
            $this->arr[$name] = MITBooster::uploadFile($name, $ro['encrypt'] || $ro['upload_encrypt'], $ro['resize_width'], $ro['resize_height'], MIT::myId());

            if (! $this->arr[$name]) {
                $this->arr[$name] = Request::get('_'.$name);
            }
        }

        if (@$ro['type'] == 'filemanager') {
            $filename = str_replace('/'.config('lfm.prefix').'/'.config('lfm.files_folder_name').'/', '', $this->arr[$name]);
            $url = 'uploads/'.$filename;
            $this->arr[$name] = $url;
        }
        return true;
    }

    public function getAdd()
    {
        Session::put('current_row_id', 0);
        $this->mitLoader();
        if (! MITBooster::isCreate() && $this->global_privilege == false || $this->button_add == false) {
            MITBooster::insertLog(trans('mixtra.log_try_add', ['module' => MITBooster::getCurrentModule()->name]));
            MITBooster::redirect(MITBooster::adminPath(), trans("mixtra.denied_access"));
        }

        $page_title = trans("mixtra.add_data_page_title", ['module' => MITBooster::getCurrentModule()->name]);
        $page_menu = Route::getCurrentRoute()->getActionName();
        $command = 'add';

        return view('mitbooster::default.form', compact('page_title', 'page_menu', 'command'));
    }

    public function postAddSave()
    {
        $this->mitLoader();
        if (! MITBooster::isCreate() && $this->global_privilege == false) {
            MITBooster::insertLog(trans('mixtra.log_try_add_save', [
                'name' => Request::input($this->title_field),
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans("mixtra.denied_access"));
        }
        $this->validation();
        $this->input_assignment();

        if (Schema::hasColumn($this->table, 'created_at')) {
            $this->arr['created_at'] = date('Y-m-d H:i:s');
        }

        $this->hook_before_add($this->arr);
//         $this->arr[$this->primary_key] = $id = MITBooster::newId($this->table); //error on sql server
        // dd($this->arr);
        $lastInsertId = $id = DB::table($this->table)->insertGetId($this->arr);

        //Looping Data Input Again After Insert
        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];
            if (! $name) {
                continue;
            }

            $inputdata = Request::get($name);

            //Insert Data Checkbox if Type Datatable
            if ($ro['type'] == 'checkbox') {
                if ($ro['relationship_table']) {
                    $datatable = explode(",", $ro['datatable'])[0];
                    $foreignKey2 = MITBooster::getForeignKey($datatable, $ro['relationship_table']);
                    $foreignKey = MITBooster::getForeignKey($this->table, $ro['relationship_table']);
                    DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                    if ($inputdata) {
                        $relationship_table_pk = MIT::pk($ro['relationship_table']);
                        foreach ($inputdata as $input_id) {
                            DB::table($ro['relationship_table'])->insert([
//                                 $relationship_table_pk => MITBooster::newId($ro['relationship_table']),
                                $foreignKey => $id,
                                $foreignKey2 => $input_id,
                            ]);
                        }
                    }
                }
            }

            if ($ro['type'] == 'select2') {
                if ($ro['relationship_table']) {
                    $datatable = explode(",", $ro['datatable'])[0];
                    $foreignKey2 = MITBooster::getForeignKey($datatable, $ro['relationship_table']);
                    $foreignKey = MITBooster::getForeignKey($this->table, $ro['relationship_table']);
                    DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                    if ($inputdata) {
                        foreach ($inputdata as $input_id) {
                            $relationship_table_pk = MIT::pk($row['relationship_table']);
                            DB::table($ro['relationship_table'])->insert([
//                                 $relationship_table_pk => MITBooster::newId($ro['relationship_table']),
                                $foreignKey => $id,
                                $foreignKey2 => $input_id,
                            ]);
                        }
                    }
                }
            }

            if ($ro['type'] == 'child') {
                $name = str_slug($ro['label'], '');
                $columns = $ro['columns'];
                $getColName = Request::get($name.'-'.$columns[0]['name']);
                $count_input_data = ($getColName)?(count($getColName) - 1):0;
                $child_array = [];

                for ($i = 0; $i <= $count_input_data; $i++) {
                    $fk = $ro['foreign_key'];
                    $column_data = [];
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        if ($col['type'] == 'money' || $col['type'] == 'number') {
                            $temp_data = Request::get($name.'-'.$colname)[$i];
                            $temp_data = str_replace(',', '', $temp_data);
                            $column_data[$colname] = $temp_data;
                        } else {
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                    }
                    $child_array[] = $column_data;
                }

                $childtable = MITBooster::parseSqlTable($ro['table'])['table'];
                DB::table($childtable)->insert($child_array);
            }

            if ($ro['type'] == 'order_detail') {
                $name = str_slug($ro['label'], '_');
                $name = $ro['name'];
                $columns = $ro['columns'];
                $getColName = Request::get($name.'-'.$columns[0]['name']);
                $count_input_data = ($getColName)?(count($getColName) - 1):0;
                $child_array = [];
                $childtable = MITBooster::parseSqlTable($ro['table'])['table'];
                $fk = $ro['foreign_key'];

                DB::table($childtable)->where($fk, $id)->delete();
                $lastId = MITBooster::newId($childtable);
                $childtablePK = MIT::pk($childtable);
                
                for ($i = 0; $i <= $count_input_data; $i++) {
                    $column_data = [];
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        if ($col['type'] == 'money' || $col['type'] == 'number') {
                            $temp_data = Request::get($name.'-'.$colname)[$i];
                            $temp_data = str_replace(',', '', $temp_data);
                            $column_data[$colname] = $temp_data;
                        } else {
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                    }
                    $child_array[] = $column_data;

                    $lastId++;
                }

                $child_array = array_reverse($child_array);

                DB::table($childtable)->insert($child_array);
            }
        }

        $this->hook_after_add($lastInsertId);

        $this->return_url = ($this->return_url) ? $this->return_url : Request::get('return_url');

        //insert log
        // MITBooster::insertLog(trans("mixtra.log_add", ['name' => $lastInsertId, 'module' => MITBooster::getCurrentModule()->name]));
        MITBooster::insertLog(trans("mixtra.log_add", ['name' => $this->arr[$this->title_field], 'module' => MITBooster::getCurrentModule()->name]));

        if ($this->return_url) {
            if (Request::get('submit') == trans('mixtra.button_save_more')) {
                MITBooster::redirect(Request::server('HTTP_REFERER'), trans("mixtra.alert_add_data_success"), 'success');
            } else {
                MITBooster::redirect($this->return_url, trans("mixtra.alert_add_data_success"), 'success');
            }
        } else {
            if (Request::get('submit') == trans('mixtra.button_save_more')) {
                MITBooster::redirect(MITBooster::mainpath('add'), trans("mixtra.alert_add_data_success"), 'success');
            } else {
                MITBooster::redirect(MITBooster::mainpath(), trans("mixtra.alert_add_data_success"), 'success');
            }
        }
    }

    public function getEdit($id)
    {
        Session::put('current_row_id', $id);
        $this->mitLoader();
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isRead() && $this->global_privilege == false || $this->button_edit == false) {
            MITBooster::insertLog(trans("mixtra.log_try_edit", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("mixtra.edit_data_page_title", ['module' => MITBooster::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'edit';

        return view('mitbooster::default.form', compact('id', 'row', 'page_menu', 'page_title', 'command'));
    }

    public function postEditSave($id)
    {
        $this->mitLoader();
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isUpdate() && $this->global_privilege == false) {
            MITBooster::insertLog(trans("mixtra.log_try_add", ['name' => $row->{$this->title_field}, 'module' => MITBooster::getCurrentModule()->name]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $this->validation($id);
        $this->input_assignment($id);
        // dd($this->arr);

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $this->arr['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->hook_before_edit($this->arr, $id);
        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        //Looping Data Input Again After Insert
        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];
            if (! $name) {
                continue;
            }

            $inputdata = Request::get($name);

            //Insert Data Checkbox if Type Datatable
            if ($ro['type'] == 'checkbox') {
                if ($ro['relationship_table']) {
                    $datatable = explode(",", $ro['datatable'])[0];

                    $foreignKey2 = MITBooster::getForeignKey($datatable, $ro['relationship_table']);
                    $foreignKey = MITBooster::getForeignKey($this->table, $ro['relationship_table']);
                    DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                    if ($inputdata) {
                        foreach ($inputdata as $input_id) {
                            $relationship_table_pk = MIT::pk($ro['relationship_table']);
                            DB::table($ro['relationship_table'])->insert([
//                                 $relationship_table_pk => MITBooster::newId($ro['relationship_table']),
                                $foreignKey => $id,
                                $foreignKey2 => $input_id,
                            ]);
                        }
                    }
                }
            }

            if ($ro['type'] == 'select2') {
                if ($ro['relationship_table']) {
                    $datatable = explode(",", $ro['datatable'])[0];

                    $foreignKey2 = MITBooster::getForeignKey($datatable, $ro['relationship_table']);
                    $foreignKey = MITBooster::getForeignKey($this->table, $ro['relationship_table']);
                    DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                    if ($inputdata) {
                        foreach ($inputdata as $input_id) {
                            $relationship_table_pk = MIT::pk($ro['relationship_table']);
                            DB::table($ro['relationship_table'])->insert([
//                                 $relationship_table_pk => MITBooster::newId($ro['relationship_table']),
                                $foreignKey => $id,
                                $foreignKey2 => $input_id,
                            ]);
                        }
                    }
                }
            }

            if ($ro['type'] == 'child') {
                $name = str_slug($ro['label'], '');
                $columns = $ro['columns'];
                $getColName = Request::get($name.'-'.$columns[0]['name']);
                $count_input_data = ($getColName)?(count($getColName) - 1):0;
                $child_array = [];
                $childtable = MITBooster::parseSqlTable($ro['table'])['table'];
                $fk = $ro['foreign_key'];

                DB::table($childtable)->where($fk, $id)->delete();
                $lastId = MITBooster::newId($childtable);
                $childtablePK = MIT::pk($childtable);

                for ($i = 0; $i <= $count_input_data; $i++) {
                    $column_data = [];
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        if ($col['type'] == 'money' || $col['type'] == 'number') {
                            $temp_data = Request::get($name.'-'.$colname)[$i];
                            $temp_data = str_replace(',', '', $temp_data);
                            $column_data[$colname] = $temp_data;
                        } else {
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                    }
                    $child_array[] = $column_data;

                    $lastId++;
                }

                $child_array = array_reverse($child_array);

                DB::table($childtable)->insert($child_array);
            }

            if ($ro['type'] == 'order_detail') {
                $name = str_slug($ro['label'], '_');
                $name = $ro['name'];
                $columns = $ro['columns'];
                $getColName = Request::get($name.'-'.$columns[0]['name']);
                $count_input_data = ($getColName)?(count($getColName) - 1):0;
                $child_array = [];
                $childtable = MITBooster::parseSqlTable($ro['table'])['table'];
                $fk = $ro['foreign_key'];

                DB::table($childtable)->where($fk, $id)->delete();
                $lastId = MITBooster::newId($childtable);
                $childtablePK = MIT::pk($childtable);
                
                for ($i = 0; $i <= $count_input_data; $i++) {
                    $column_data = [];
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        if ($col['type'] == 'money' || $col['type'] == 'number') {
                            $temp_data = Request::get($name.'-'.$colname)[$i];
                            $temp_data = str_replace(',', '', $temp_data);
                            $column_data[$colname] = $temp_data;
                        } else {
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                    }
                    $child_array[] = $column_data;

                    $lastId++;
                }

                $child_array = array_reverse($child_array);

                DB::table($childtable)->insert($child_array);
            }

            if ($ro['type'] == 'data_detail') {
                $name = $ro['name'];
                $columns = $ro['columns'];
                $getColName = Request::get($name.'-id');
                $count_input_data = ($getColName)?(count($getColName) - 1):0;

                for ($i = 0; $i <= $count_input_data; $i++) {
                    $column_data = [];

                    foreach ($columns as $col) {
                        if($col['exception']) continue;
                        $colname = $col['name'];
                        if ($col['type'] == 'money' || $col['type'] == 'number') {
                            $temp_data = Request::get($name.'-'.$colname)[$i];
                            $temp_data = str_replace(',', '', $temp_data);
                            $column_data[$colname] = $temp_data;
                        } else {
                            $column_data[$colname] = Request::get($name.'-'.$colname)[$i];
                        }
                    }
                    
                    db::table($ro['table'])
                        ->where('id', $getColName[$i])
                        ->update($column_data);
                }
            }
        }

        $this->hook_after_edit($id);

        $this->return_url = ($this->return_url) ? $this->return_url : Request::get('return_url');

        //insert log
        $old_values = json_decode(json_encode($row), true);
        MITBooster::insertLog(trans("mixtra.log_update", [
            'name' => $this->arr[$this->title_field],
            'module' => MITBooster::getCurrentModule()->name,
        ]), LogsController::displayDiff($old_values, $this->arr));

        if ($this->return_url) {
            MITBooster::redirect($this->return_url, trans("mixtra.alert_update_data_success"), 'success');
        } else {
            if (Request::get('submit') == trans('mixtra.button_save_more')) {
                MITBooster::redirect(MITBooster::mainpath('add'), trans("mixtra.alert_update_data_success"), 'success');
            } else {
                MITBooster::redirect(MITBooster::mainpath(), trans("mixtra.alert_update_data_success"), 'success');
            }
        }
    }

    public function getDelete($id)
    {
        $this->mitLoader();
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isDelete() && $this->global_privilege == false || $this->button_delete == false) {
            MITBooster::insertLog(trans("mixtra.log_try_delete", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        //insert log
        MITBooster::insertLog(trans("mixtra.log_delete", ['name' => $row->{$this->title_field}, 'module' => MITBooster::getCurrentModule()->name]));

        $this->hook_before_delete($id);

        if (MITBooster::isColumnExists($this->table, 'deleted_at')) {
            DB::table($this->table)->where($this->primary_key, $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this->table)->where($this->primary_key, $id)->delete();
        }

        $this->hook_after_delete($id);

        $url = g('return_url') ?: MITBooster::referer();

        MITBooster::redirect($url, trans("mixtra.alert_delete_data_success"), 'success');
    }

    public function getDetail($id)
    {
        $this->mitLoader();
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isRead() && $this->global_privilege == false || $this->button_detail == false) {
            MITBooster::insertLog(trans("mixtra.log_try_view", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $module = MITBooster::getCurrentModule();

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("mixtra.detail_data_page_title", ['module' => $module->name, 'name' => $row->{$this->title_field}]);
        $command = 'detail';

        Session::put('current_row_id', $id);

        return view('mitbooster::default.form', compact('row', 'page_menu', 'page_title', 'command', 'id'));
    }

    public function getImportData()
    {
        $this->mitLoader();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = 'Import Data '.$module->name;

        if (Request::get('file') && ! Request::get('import')) {
            $file = base64_decode(Request::get('file'));
            $file = storage_path('app/'.$file);
            $rows = Excel::load($file, function ($reader) {
            })->get();
            
            $countRows = ($rows)?count($rows):0;

            Session::put('total_data_import', $countRows);

            $data_import_column = [];
            foreach ($rows as $value) {
                $a = [];
                foreach ($value as $k => $v) {
                    $a[] = $k;
                }
                if ($a && count($a)) {
                    $data_import_column = $a;
                }
                break;
            }

            $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

            $data['table_columns'] = $table_columns;
            $data['data_import_column'] = $data_import_column;
        }

        return view('mitbooster::import', $data);
    }

    public function postDoneImport()
    {
        $this->mitLoader();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = trans('mixtra.import_page_title', ['module' => $module->name]);
        Session::put('select_column', Request::get('select_column'));

        return view('mitbooster::import', $data);
    }

    public function getDoImportChunk()
    {
        $this->mitLoader();
        $file_md5 = md5(Request::get('file'));

        if (Request::get('file') && Request::get('resume') == 1) {
            $total = Session::get('total_data_import');
            $prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
            $prog = round($prog, 2);
            if ($prog >= 100) {
                Cache::forget('success_'.$file_md5);
            }

            return response()->json(['progress' => $prog, 'last_error' => Cache::get('error_'.$file_md5)]);
        }

        $select_column = Session::get('select_column');
        $select_column = array_filter($select_column);
        $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

        $file = base64_decode(Request::get('file'));
        $file = storage_path('app/'.$file);

        $rows = Excel::load($file, function ($reader) {
        })->get();

        $has_created_at = false;
        if (MITBooster::isColumnExists($this->table, 'created_at')) {
            $has_created_at = true;
        }

        $data_import_column = [];
        foreach ($rows as $value) {
            $a = [];
            foreach ($select_column as $sk => $s) {
                $colname = $table_columns[$sk];

                if (MITBooster::isForeignKey($colname)) {

                    //Skip if value is empty
                    if ($value->$s == '') {
                        continue;
                    }

                    if (intval($value->$s)) {
                        $a[$colname] = $value->$s;
                    } else {
                        $relation_table = MITBooster::getTableForeignKey($colname);
                        $relation_moduls = DB::table('mit_moduls')->where('table_name', $relation_table)->first();

                        $relation_class = __NAMESPACE__.'\\'.$relation_moduls->controller;
                        if (! class_exists($relation_class)) {
                            $relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
                        }
                        $relation_class = new $relation_class;
                        $relation_class->mitLoader();

                        $title_field = $relation_class->title_field;

                        $relation_insert_data = [];
                        $relation_insert_data[$title_field] = $value->$s;

                        if (MITBooster::isColumnExists($relation_table, 'created_at')) {
                            $relation_insert_data['created_at'] = date('Y-m-d H:i:s');
                        }

                        try {
                            $relation_exists = DB::table($relation_table)->where($title_field, $value->$s)->first();
                            if ($relation_exists) {
                                $relation_primary_key = $relation_class->primary_key;
                                $relation_id = $relation_exists->$relation_primary_key;
                            } else {
                                $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                            }

                            $a[$colname] = $relation_id;
                        } catch (\Exception $e) {
                            exit($e);
                        }
                    } //END IS INT
                } else {
                    $a[$colname] = $value->$s;
                }
            }

            $has_title_field = true;
            foreach ($a as $k => $v) {
                if ($k == $this->title_field && $v == '') {
                    $has_title_field = false;
                    break;
                }
            }

            if ($has_title_field == false) {
                continue;
            }

            try {
                if ($has_created_at) {
                    $a['created_at'] = date('Y-m-d H:i:s');
                }

                DB::table($this->table)->insert($a);
                Cache::increment('success_'.$file_md5);
            } catch (\Exception $e) {
                $e = (string) $e;
                Cache::put('error_'.$file_md5, $e, 500);
            }
        }

        return response()->json(['status' => true]);
    }

    public function postDoUploadImportData()
    {
        $this->mitLoader();
        if (Request::hasFile('userfile')) {
            $file = Request::file('userfile');
            $ext = $file->getClientOriginalExtension();

            $validator = Validator::make([
                'extension' => $ext,
            ], [
                'extension' => 'in:xls,xlsx,csv',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->all();

                return redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning']);
            }

            //Create Directory Monthly
            $filePath = 'uploads/'.MIT::myId().'/'.date('Y-m');
            Storage::makeDirectory($filePath);

            //Move file to storage
            $filename = md5(str_random(5)).'.'.$ext;
            $url_filename = '';
            if (Storage::putFileAs($filePath, $file, $filename)) {
                $url_filename = $filePath.'/'.$filename;
            }
            $url = MITBooster::mainpath('import-data').'?file='.base64_encode($url_filename);

            return redirect($url);
        } else {
            return redirect()->back();
        }
    }

    public function postActionSelected()
    {
        $this->mitLoader();
        $id_selected = Request::input('checkbox');
        $button_name = Request::input('button_name');

        if (! $id_selected) {
            MITBooster::redirect($_SERVER['HTTP_REFERER'], trans("mixtra.alert_select_a_data"), 'warning');
        }

        if ($button_name == 'delete') {
            if (! MITBooster::isDelete()) {
                MITBooster::insertLog(trans("mixtra.log_try_delete_selected", ['module' => MITBooster::getCurrentModule()->name]));
                MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
            }

            $this->hook_before_delete($id_selected);
            $tablePK = MIT::pk($this->table);
            if (MITBooster::isColumnExists($this->table, 'deleted_at')) {
                DB::table($this->table)->whereIn($tablePK, $id_selected)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            } else {
                DB::table($this->table)->whereIn($tablePK, $id_selected)->delete();
            }
            MITBooster::insertLog(trans("mixtra.log_delete", ['name' => implode(',', $id_selected), 'module' => MITBooster::getCurrentModule()->name]));

            $this->hook_after_delete($id_selected);

            $message = trans("mixtra.alert_delete_selected_success");

            return redirect()->back()->with(['message_type' => 'success', 'message' => $message]);
        }

        $action = str_replace(['-', '_'], ' ', $button_name);
        $action = ucwords($action);
        $type = 'success';
        $message = trans("mixtra.alert_action", ['action' => $action]);

        if ($this->actionButtonSelected($id_selected, $button_name) === false) {
            $message = ! empty($this->alert['message']) ? $this->alert['message'] : 'Error';
            $type = ! empty($this->alert['type']) ? $this->alert['type'] : 'danger';
        }

        return redirect()->back()->with(['message_type' => $type, 'message' => $message]);
    }

    public function getDeleteImage()
    {
        $this->mitLoader();
        $id = Request::get('id');
        $column = Request::get('column');

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isDelete() && $this->global_privilege == false) {
            MITBooster::insertLog(trans("mixtra.log_try_delete_image", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $file = str_replace('uploads/', '', $row->{$column});
        if (Storage::exists($file)) {
            Storage::delete($file);
        }

        DB::table($this->table)->where($this->primary_key, $id)->update([$column => null]);

        MITBooster::insertLog(trans("mixtra.log_delete_image", [
            'name' => $row->{$this->title_field},
            'module' => MITBooster::getCurrentModule()->name,
        ]));

        MITBooster::redirect(Request::server('HTTP_REFERER'), trans('mixtra.alert_delete_data_success'), 'success');
    }

    public function postUploadSummernote()
    {
        $this->mitLoader();
        $name = 'userfile';
        if ($file = MITBooster::uploadFile($name, true)) {
            echo asset($file);
        }
    }

    public function postUploadFile()
    {
        $this->mitLoader();
        $name = 'userfile';
        if ($file = MITBooster::uploadFile($name, true)) {
            echo asset($file);
        }
    }

    public function actionButtonSelected($id_selected, $button_name)
    {
    }

    public function hook_query_index(&$query)
    {
    }

    public function hook_row_index($index, &$value)
    {
    }

    public function hook_row_data($row, $index, &$value)
    {
    }

    public function hook_before_add(&$arr)
    {
    }

    public function hook_after_add($id)
    {
    }

    public function hook_before_edit(&$arr, $id)
    {
    }

    public function hook_after_edit($id)
    {
    }

    public function hook_before_delete($id)
    {
    }

    public function hook_after_delete($id)
    {
    }
}
