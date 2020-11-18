<?php

namespace mixtra\controllers;

use MITBooster;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;

class AdminController extends MITController
{
	public function init()
	{
		
	}
	public function getLogin()
    {
        $this->mitLoader();

        if (MITBooster::myId()) {
            return redirect(MITBooster::adminPath());
        }


        return view('mitbooster::login');
    }

    public function postLogin()
    {
    	try {
	        $validator = Validator::make(Request::all(), [
	            'username' => 'required|exists:'.config('mixtra.USER_TABLE'),
	            'password' => 'required',
	        ]);

	        if ($validator->fails()) {
	            $message = $validator->errors()->all();

	            return redirect()->back()->with(['message' => implode(', ', $message), 'message_type' => 'danger']);
	        }

	        $username = Request::input("username");
	        $password = Request::input("password");
	        $users = DB::table(config('mixtra.USER_TABLE'))->where("username", $username)->first();

	        if (\Hash::check($password, $users->password)) {
	            $priv = DB::table("mit_privileges")->where("id", $users->mit_privileges_id)->first();

	            $roles = DB::table('mit_privileges_roles')->where('mit_privileges_id', $users->mit_privileges_id)->join('mit_modules', 'mit_modules.id', '=', 'mit_modules_id')->select('mit_modules.name', 'mit_modules.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();

	            $photo = ($users->photo) ? asset($users->photo) : asset('assets/images/user.png');
	            Session::put('admin_id', $users->id);
	            Session::put('admin_is_superadmin', $priv->is_superadmin);
	            Session::put('admin_name', $users->name);
	            Session::put('admin_photo', $photo);
	            Session::put('admin_privileges_roles', $roles);
	            Session::put("admin_privileges", $users->mit_privileges_id);
	            Session::put('admin_privileges_name', $priv->name);
	            Session::put('admin_lock', 0);
	            Session::put('theme_color', $priv->theme_color);

	            MITBooster::insertLog(trans("mixtra.log_login", ['username' => $users->username, 'ip' => Request::server('REMOTE_ADDR')]));

	            //$cb_hook_session = new \App\Http\Controllers\CBHook;
	            //$cb_hook_session->afterLogin();

	            return redirect(MITBooster::adminPath());
	        } else {
	            return redirect()->route('getLogin')->with('message', 'Sorry your password is wrong !');
	        }
	    }catch(\Exception $e) {
			return redirect()->back()->with(['message' => $e->getMessage(), 'message_type' => 'danger']);
	    }
    }

    function getIndex()
    {
        $this->mitLoader();

        $data = [];
        $data['page_title'] = '<strong>Dashboard</strong>';

        return view('mitbooster::home', $data);
    }

	public function getLogout()
    {
        $me = MITBooster::me();
        MITBooster::insertLog(trans("mixtra.log_logout", ['username' => $me->username]));

        Session::flush();

        return redirect()->route('getLogin')->with('message', 'Thank You, See You Later !');
    }

    public function getLockscreen()
    {

        if (! MITBooster::myId()) {
            Session::flush();

            return redirect()->route('getLogin')->with('message', 'Your session was expired, please login again !');
        }

        Session::put('admin_lock', 1);
        return view('mitbooster::lockscreen');
    }

    public function postUnlockScreen()
    {
        $id = MITBooster::myId();
        $password = Request::input('password');
        $users = DB::table(config('mixtra.USER_TABLE'))->where('id', $id)->first();

        if (\Hash::check($password, $users->password)) {
            Session::put('admin_lock', 0);
            return redirect(MITBooster::adminPath());
        } else {
            echo "<script>alert('Sorry your password is wrong !');history.go(-1);</script>";
        }
    }

}