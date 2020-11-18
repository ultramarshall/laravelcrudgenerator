<?php 
namespace mixtra\controllers;

use MITBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class PrivilegesController extends MITController
{
    public function init()
    {
        $this->module_name = "Privilege";
        $this->table = 'mit_privileges';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->button_import = false;
        $this->button_export = false;
        $this->button_action_style = 'button_icon';
        $this->button_detail = false;
        $this->button_bulk_action = false;

        $this->col = [];
        $this->col[] = ["label" => "ID", "name" => "id"];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = [
            "label" => "Superadmin",
            "name" => "is_superadmin",
            'callback_php' => '($row->is_superadmin)?"<span class=\"label label-success\">Superadmin</span>":"<span class=\"label label-warning\">Standard</span>"',
        ];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", 'required' => true];
        $this->form[] = ["label" => "Is Superadmin", "name" => "is_superadmin", 'required' => true];
        $this->form[] = ["label" => "Theme Color", "name" => "theme_color", 'required' => true];

        $this->alert[] = [
            'message' => "You can use the helper <code>MITBooster::getMyPrivilegeId()</code> to get current user login privilege id, or <code>MITBooster::getMyPrivilegeName()</code> to get current user login privilege name",
            'type' => 'info',
        ];
    }

    public function getAdd()
    {
        $this->mitLoader();

        if (! MITBooster::isCreate() && $this->global_privilege == false) {
            MITBooster::insertLog(trans('mixtra.log_try_add', ['module' => MITBooster::getCurrentModule()->name]));
            MITBooster::redirect(MITBooster::adminPath(), trans("mixtra.denied_access"));
        }

        $id = 0;
        $data['page_title'] = "Add Data";
        $data['moduls'] = DB::table("mit_modules")->where('is_protected', 0)->whereNull('deleted_at')->select("mit_modules.*", DB::raw("(select is_visible from mit_privileges_roles where mit_modules_id = mit_modules.id and mit_privileges_id = '$id') as is_visible"), DB::raw("(select is_create from mit_privileges_roles where mit_modules_id  = mit_modules.id and mit_privileges_id = '$id') as is_create"), DB::raw("(select is_read from mit_privileges_roles where mit_modules_id    = mit_modules.id and mit_privileges_id = '$id') as is_read"), DB::raw("(select is_edit from mit_privileges_roles where mit_modules_id    = mit_modules.id and mit_privileges_id = '$id') as is_edit"), DB::raw("(select is_delete from mit_privileges_roles where mit_modules_id  = mit_modules.id and mit_privileges_id = '$id') as is_delete"))->orderby("name", "asc")->get();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();

        return view('mitbooster::privileges', $data);
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

        $this->validation($request);
        $this->input_assignment($request);

        $this->arr[$this->primary_key] = DB::table($this->table)->max($this->primary_key) + 1;

        DB::table($this->table)->insert($this->arr);
        $id = $this->arr[$this->primary_key];

        //set theme
        Session::put('theme_color', $this->arr['theme_color']);

        $priv = Request::input("privileges");
        if ($priv) {
            foreach ($priv as $id_modul => $data) {
                $arrs = [];
                $arrs['id'] = DB::table('mit_privileges_roles')->max('id') + 1;
                $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                $arrs['is_create'] = @$data['is_create'] ?: 0;
                $arrs['is_read'] = @$data['is_read'] ?: 0;
                $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                $arrs['mit_privileges_id'] = $id;
                $arrs['mit_modules_id'] = $id_modul;
                DB::table("mit_privileges_roles")->insert($arrs);

                $module = DB::table('mit_modules')->where('id', $id_modul)->first();
            }
        }

        //Refresh Session Roles
        $roles = DB::table('mit_privileges_roles')->where('mit_privileges_id', MITBooster::myPrivilegeId())->join('mit_modules', 'mit_modules.id', '=', 'mit_modules_id')->select('mit_modules.name', 'mit_modules.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        MITBooster::redirect(MITBooster::mainpath(), trans("mixtra.alert_add_data_success"), 'success');
    }

    public function getEdit($id)
    {
        $this->mitLoader();

        $row = DB::table($this->table)->where("id", $id)->first();

        if (! MITBooster::isRead() && $this->global_privilege == false) {
            MITBooster::insertLog(trans("mixtra.log_try_edit", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $page_title = trans('mixtra.edit_data_page_title', ['module' => 'Privilege', 'name' => $row->name]);

        $moduls = DB::table("mit_modules")->where('is_protected', 0)->where('deleted_at', null)->select("mit_modules.*")->orderby("name", "asc")->get();
        $page_menu = Route::getCurrentRoute()->getActionName();

        return view('mitbooster::privileges', compact('row', 'page_title', 'moduls', 'page_menu'));
    }

    public function postEditSave($id)
    {
        $this->mitLoader();

        $row = MITBooster::first($this->table, $id);

        if (! MITBooster::isUpdate() && $this->global_privilege == false) {
            MITBooster::insertLog(trans("mixtra.log_try_add", ['name' => $row->{$this->title_field}, 'module' => MITBooster::getCurrentModule()->name]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        $this->validation($id);
        $this->input_assignment($id);

        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        $priv = Request::input("privileges");

        // This solves issue #1074
        DB::table("mit_privileges_roles")->where("mit_privileges_id", $id)->delete();

        if ($priv) {

            foreach ($priv as $id_modul => $data) {
                //Check Menu
                $module = DB::table('mit_modules')->where('id', $id_modul)->first();
                $currentPermission = DB::table('mit_privileges_roles')->where('mit_modules_id', $id_modul)->where('mit_privileges_id', $id)->first();

                if ($currentPermission) {
                    $arrs = [];
                    $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                    $arrs['is_create'] = @$data['is_create'] ?: 0;
                    $arrs['is_read'] = @$data['is_read'] ?: 0;
                    $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                    $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                    DB::table('mit_privileges_roles')->where('id', $currentPermission->id)->update($arrs);
                } else {
                    $arrs = [];
                    $arrs['id'] = DB::table('mit_privileges_roles')->max('id') + 1;
                    $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                    $arrs['is_create'] = @$data['is_create'] ?: 0;
                    $arrs['is_read'] = @$data['is_read'] ?: 0;
                    $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                    $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                    $arrs['mit_privileges_id'] = $id;
                    $arrs['mit_modules_id'] = $id_modul;
                    DB::table("mit_privileges_roles")->insert($arrs);
                }
            }
        }

        //Refresh Session Roles
        if ($id == MITBooster::myPrivilegeId()) {
            $roles = DB::table('mit_privileges_roles')->where('mit_privileges_id', MITBooster::myPrivilegeId())->join('mit_modules', 'mit_modules.id', '=', 'mit_modules_id')->select('mit_modules.name', 'mit_modules.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
            Session::put('admin_privileges_roles', $roles);

            Session::put('theme_color', $this->arr['theme_color']);
        }

        MITBooster::redirect(MITBooster::mainpath(), trans("mixtra.alert_update_data_success", [
            'module' => "Privilege",
            'title' => $row->name,
        ]), 'success');
    }

    public function getDelete($id)
    {
        $this->mitLoader();

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! MITBooster::isDelete() && $this->global_privilege == false) {
            MITBooster::insertLog(trans("mixtra.log_try_delete", [
                'name' => $row->{$this->title_field},
                'module' => MITBooster::getCurrentModule()->name,
            ]));
            MITBooster::redirect(MITBooster::adminPath(), trans('mixtra.denied_access'));
        }

        DB::table($this->table)->where($this->primary_key, $id)->delete();
        DB::table("mit_privileges_roles")->where("mit_privileges_id", $row->id)->delete();

        MITBooster::redirect(MITBooster::mainpath(), trans("mixtra.alert_delete_data_success"), 'success');
    }
}
