<?php 
namespace mixtra\controllers;

use MITBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;

class NotificationsController extends MITController
{
    public function init()
    {
        $this->table = "mit_notifications";
        $this->primary_key = "id";
        $this->title_field = "content";
        $this->limit = 20;
        $this->index_orderby = ["id" => "desc"];
        $this->button_show = true;
        $this->button_add = false;
        $this->button_delete = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->global_privilege = true;

        $read_notification_url = url(config('mixtra.ADMIN_PATH')).'/notifications/read';

        $this->col = [];
        $this->col[] = ["label" => "Content", "name" => "content", "callback_php" => '"<a href=\"'.$read_notification_url.'/$row->id\">$row->content</a>"'];
        $this->col[] = [
            'label' => 'Read',
            'name' => 'is_read',
            'callback_php' => '($row->is_read)?"<span class=\"label label-default\">Already Read</span>":"<span class=\"label label-danger\">NEW</span>"',
        ];

        $this->form = [];
        $this->form[] = ["label" => "Content", "name" => "content", "type" => "text"];
        $this->form[] = ["label" => "Icon", "name" => "icon", "type" => "text"];
        $this->form[] = ["label" => "Notification Command", "name" => "notification_command", "type" => "textarea"];
        $this->form[] = ["label" => "Is Read", "name" => "is_read", "type" => "text"];
    }

    public function hook_query_index(&$query)
    {
        $query->where('mit_users_id', MITBooster::myId());
    }

    public function getLatestJson()
    {

        $rows = DB::table('mit_notifications')->where('mit_users_id', 0)->orWhere('mit_users_id', MITBooster::myId())->orderby('id', 'desc')->where('is_read', 0)->take(25);
        if (\Schema::hasColumn('mit_notifications', 'deleted_at')) {
            $rows->whereNull('deleted_at');
        }

        $total = count($rows->get());

        return response()->json(['items' => $rows->get(), 'total' => $total]);
    }

    public function getRead($id)
    {
        DB::table('mit_notifications')->where('id', $id)->update(['is_read' => 1]);
        $row = DB::table('mit_notifications')->where('id', $id)->first();

        return redirect($row->url);
    }
}