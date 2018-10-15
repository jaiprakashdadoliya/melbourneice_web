<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth, Mail;
use App\Helpers\UserHelper;
use App\Models\CommonModel;
// use App\Http\Middleware\Backend;


class AdminController extends Controller
{
	public function __construct()
	{
		$this->commonModel = new CommonModel();
		// $this->middleware('Backend');
	}

	function getIndex() 
	{
		$is_admin_logged_in = Session::get('is_admin_logged_in');
		if($is_admin_logged_in) {
			$data = array();			
			$data['page_title'] = 'Dashboard';		
			// // $data['page_menu']        = Route::getCurrentRoute()->getActionName();	
			return view('admin.dashboard',$data);
		}
		return view('admin.login');	
	}
    public function getLogin()
	{
		$data = array();
		$data['page_title_login'] = 'Login';
		return view('admin.login', $data);
	}

	public function postLogin(Request $request)
	{		
	    $validator = Validator::make(Request::all(), [
            'email' => 'required|email|exists:admin_users',
	        'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return redirect('admin')
                        ->withErrors($validator)
                        ->withInput();
        }

        $email 		= Request::input("email");
		$password 	= Request::input("password");

        $remember = (Request::input('remember')) ? true : false;		
        
        $users = DB::table('admin_users')->where("email",$email)->first(); 

        if(\Hash::check($password,$users->password)) {
        	/*if (Auth::attempt(array('email' => $email, 'password' => $password), true))
			{
			    // The user is being remembered...
			}*/

			// $photo = ($users->photo)?asset($users->photo):'https://www.gravatar.com/avatar/'.md5($users->email).'?s=100';
			Session::put('admin_id',$users->id);
			Session::put('admin_name',$users->name);
			Session::put('is_admin_logged_in',true);	

			// f::insertLog(trans("f.log_login",['email'=>$users->email,'ip'=>Request::server('REMOTE_ADDR')]));

			return redirect()->action('AdminController@getIndex'); 
		}else{
			return redirect()->route('getLogin')->with('message', trans('messages.alert_password_wrong'));			
		}
	}

	public function getLogout()
	{
		// $me = f::me();
		// f::insertLog(trans("f.log_logout",['email'=>$me->email]));
		Session::flush();
		return redirect()->route('getLogin')->with('message',trans("messages.message_after_logout"));
	}

	public function profile(Request $request)
	{
		$is_admin_logged_in = Session::get('is_admin_logged_in');
		$myId = Session::get('admin_id');
		if($is_admin_logged_in) {
			$data = array();			
			$data['page_title'] = 'Profile';
			$data['box_title'] = 'Profile';

			$data['userDetails'] = DB::table('admin_users')->where('id', $myId)->first();
			return view('admin.profile', $data);
		}
		return view('admin.login');			
	}

	public function saveProfile(Request $request)
	{
		$is_admin_logged_in = Session::get('is_admin_logged_in');
		$myId = Session::get('admin_id');
		if($is_admin_logged_in) {
			$validator = Validator::make(Request::all(),[
					"name" => 'required|min:6',
					"email" => 'required|email',
					// "photo" => "required|photo|mimes:jpeg,png,jpg,gif,svg|max:2048",
				]);

			if ($validator->fails()) {
	            return redirect('profile')
	                        ->withErrors($validator)
	                        ->withInput();
	        }

	        $name = Request::input('name');
	        $email = Request::input('email');
	        $password = Request::input('password');
	        $photo = Request::file('photo');
	        // image request process
	        if(Request::hasFile('photo')){
	        	$input['photo'] = time().'_'.$photo->getClientOriginalName();
	        	$destinationPath = public_path(ADMIN_UPLOAD_PATH);
    			$photo->move($destinationPath, $input['photo']);

    			$this->commonModel->updateRecords('admin_users', array('photo'=>$input['photo'], 'updated_at' => date('Y-m-d H:i:s')), array('id' => $myId));
	        }

	        if(!empty($password)) {
	        	$password = \Hash::make($password);
	        	$this->commonModel->updateRecords('admin_users', array('name'=>$name, 'email' => $email, 'password' => $password, 'updated_at' => date('Y-m-d H:i:s')), array('id' => $myId));

	        	return redirect()->route('profile')->with('message', trans('messages.profie_updated_success'));	

	        } else {
	        	$this->commonModel->updateRecords('admin_users', array('name'=>$name, 'email' => $email, 'updated_at' => date('Y-m-d H:i:s')), array('id' => $myId));

	        	return redirect()->route('profile')->with('message', trans('messages.profie_updated_success'));	
	        }
	    }
	    return view('admin.login');
	}

	public function getForget()
	{
		$data = array();
		$data['page_title_password'] = 'Forgot Password';
		return view('admin.forgetPassword', $data);
	}

	public function postForget(Request $request)
	{
		$validator = Validator::make(Request::all(),[
					"email" => 'required|email|exists:admin_users',
			]);

		if ($validator->fails()) {
            return redirect('forget_password')
                        ->withErrors($validator)
                        ->withInput();
        }

        $email = Request::input('email');
        $rand_string = str_random(5);
        $password = \Hash::make($rand_string);

        $data['userDetails'] = DB::table('admin_users')->where('email', $email)->first();
        $name = $data['userDetails']->name;

       	$this->commonModel->updateRecords('admin_users', array('password' => $password, 'updated_at' => date('Y-m-d H:i:s')), array('email' => $email));

       	/* Send Email */
        $html = sprintf(FORGOT_PASSWORD_MESSAGE, ucfirst($name), $rand_string);
        try{
            Mail::send(array(), array(), function ($message) use ($email, $html) {
            $message->to($email)
                ->subject(FORGOT_PASSWORD_SUBJECT)
                ->from(FROM_EMAIL)
                ->setBody($html, 'text/html');
            });

            return redirect()->route('getForget')->with('message', trans('messages.new_password_email'));
        } 
        catch(\Exception $e){
            return redirect()->route('getForget')->with('message', trans('messages.unable_to_send_email'));
        }

	}
}
