<?php 
namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use MITBooster;

class UsersController extends \mixtra\controllers\MITController {


	public function init() {
		# START CONFIGURATION DO NOT REMOVE THIS LINE
		$this->table               = 'mit_users';
		$this->primary_key         = 'id';
		$this->title_field         = "name";
		$this->button_action_style = 'button_icon';	
		$this->button_import 	   = FALSE;	
		$this->button_export 	   = FALSE;	
		# END CONFIGURATION DO NOT REMOVE THIS LINE
	
		# START COLUMNS DO NOT REMOVE THIS LINE
		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"name");
		$this->col[] = array("label"=>"Username","name"=>"username");
		$this->col[] = array("label"=>"Privilege","name"=>"mit_privileges_id","join"=>"mit_privileges,name");
		# END COLUMNS DO NOT REMOVE THIS LINE

		# START FORM DO NOT REMOVE THIS LINE
		$this->form = array(); 		
		$this->form[] = array("label"=>"Name","name"=>"name",'required'=>true,'validation'=>'required|alpha_spaces|min:3');
		$this->form[] = array("label"=>"Username","name"=>"username",'required'=>true,'type'=>'text','validation'=>'required|unique:mit_users,username,'.MITBooster::getCurrentId());		
		$this->form[] = array("label"=>"Photo","name"=>"photo","type"=>"upload","help"=>"Recommended resolution is 90x90px",'validation'=>'image|max:1000','resize_width'=>90,'resize_height'=>90);											
		$this->form[] = array("label"=>"Privilege","name"=>"mit_privileges_id","type"=>"select","datatable"=>"mit_privileges,name",'required'=>true);						
		$this->form[] = array("label"=>"Password","name"=>"password","type"=>"password","help"=>"Please leave empty if not change");
		$this->form[] = array("label"=>"Password Confirmation","name"=>"password_confirmation","type"=>"password","help"=>"Please leave empty if not change");
		# END FORM DO NOT REMOVE THIS LINE
				
	}

	public function getProfile() {			

		$this->button_addmore = FALSE;
		$this->button_cancel  = FALSE;
		$this->button_show    = FALSE;			
		$this->button_add     = FALSE;
		$this->button_delete  = FALSE;	
		$this->hide_form 	  = ['mit_privileges'];

		$data['page_title'] = trans("mixtra.label_button_profile");
		$data['row']        = MITBooster::first('mit_users',MITBooster::myId());		
		$this->cbView('mitbooster::default.form',$data);				
	}
	public function hook_before_edit(&$postdata,$id) { 
		unset($postdata['password_confirmation']);
	}
	public function hook_before_add(&$postdata) {      
	    unset($postdata['password_confirmation']);
	}
}
