<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Input, DB, Session;
use Auth, Password, Mail, JWTAuth;
use Validator;
use App\Models\CommonModel;
use App\Http\Controllers\Controller;
use App\User;
use App\Helpers\UserHelper;
use Tymon\JWTAuth\Exceptions\JWTException;
use Laravel\Socialite\Facades\Socialite;

// use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Hash;
// use Redirect;


class UserController extends Controller
{
	protected $changed = 0;

    public function __construct()
    {        
    	$this->commonModel = new CommonModel();
    }

    /**
    * Index method 
    * @param null
    * @return redirect to main login page
    */
    public function index()
    {
        if(UserHelper::user_session_data('is_user_logged_in')){            
            return redirect('home');
        }
        return view('web.login');
    }

    /**
    * getRegister method 
    * @param null
    * @return redirect to main signup page
    */
    public function getRegister()
    {
    	return view('web.register');
    }

    /**
	* User postRegister
	* @param Request $request
	* @return Create user
    */
    public function postRegister(Request $request)
    {
        // check validations
    	$Validator = Validator::make($request->all(),[
		    'firstName' => 'required',
		    'lastName' => 'required',
		    'email' => 'required|unique:users',
		    'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/',
            'password_confirmation' =>  'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|same:password'
		]);

        if ($Validator->fails()) {
            return redirect('register')
                        ->withErrors($Validator)
                        ->withInput();
        }

        // check email address
        $checkEmail = User::where('email', $request['email'])->count();
        if($checkEmail > 0) {
        	return redirect()->back()->with('message', trans('messages.email_already_exist'));
        }

        $userName = ucfirst($request['firstName'].' '.$request['lastName']);
        // create user
        $userid = $this->commonModel->addRecord(USER, array('userName' => $userName, 'email' => $request['email'], 'password' => bcrypt($request['password']), 'emailConfirmed' => 1));

        if(isset($userid)){
            // insert data into profile table
            // $memberNo = $this->commonModel->getUniqueId(PROFILE,'memberNumber');
            // $getLatestId = DB::table(PROFILE)->select('id')->orderBy('id', 'desc')->first();
            // $prefix = "MIM";
            // $memberNumber = UserHelper::generateMemberNo($prefix);

            $this->commonModel->addRecord(PROFILE, array('firstName' => $request['firstName'], 'lastName' => $request['lastName'], 'userId' => $userid, 'profileEmail' => $request['email']));
        	// Email verification token
        	$token = getToken(50);
        	User::where('id', $userid)->update(array('securityStamp'=>$token));

        	$email = $request['email'];
        	$link = WEB_URL.EMAIL_VERIFICATION_FRONT_SLUG.$userid.'/'.base64_encode($request['password']).'/'.$token;
        	$html = sprintf(EMAIL_VERIFICATION_MESSAGE, $userName, $link);

        	// send email
            try{
                Mail::send(array(), array(), function ($message) use ($email, $html) {
                $message->to($email)
                    ->subject(EMAIL_VERIFICATION_SUBJECT)
                    ->from(FROM_EMAIL)
                    ->setBody($html, 'text/html');
                });
            } 
            catch(\Exception $e){
                return redirect('register')->with('message', trans('messages.unable_to_send_email'));
            }

            return redirect('/')->with('success', trans('messages.user_created_successfully'));
        } else {
        	return redirect('register')->with('message', trans('messages.unable_to_create_user'));
        }
    }

    /**
    * Verify email
    * @param Request $request
    * @return Verify email address
    */
    public function verifyEmail($id, $password, $token)
    {
    	$user = User::where(array('id' => $id, 'securityStamp' => $token))->first();
    	if(isset($user)){            
    		if($token == $user->securityStamp)
    		{                
    			User::where('id', $id)->update(array('emailConfirmed' => '2'));  
    			try {
                    $token = JWTAuth::attempt(array('email' => $user->email, 'password' => base64_decode($password)));
                    return redirect('/')->with('success', trans('messages.user_verified'));
    			} catch (Exception $e) {
    				return redirect('register')->with('message', trans('messages.server_error'));
    			}
    		} else {
    			return redirect('register')->with('message', trans('messages.invalid_token'));
    		}
    	} else {
    		return redirect('register')->with('message', trans('messages.user_not_exist'));
    	}
    }

    /**
    * User login
    * @param Request $request
    * @return user login
    */
    public function login(Request $request)
    {        
    	// Check validation rules
    	$validator = Validator::make($request->all(), [
    		'email'	=>	'required|email|exists:users',
    		'password'	=>	'required'
		]);

    	if ($validator->fails()) {
            return redirect('/')
                        ->withErrors($validator)
                        ->withInput();
        }
        $remember = ($request['remember']) ? true : false;        
        // Process request
        if(Auth::attempt(array('email' => $request['email'], 'password' => $request['password']), $remember)){
        	try {
        		$token = JWTAuth::attempt(array('email' => $request['email'], 'password' => $request['password']));
                $user = Auth::user();
                if($user->emailConfirmed == 2) {
                	// put data into session
                	$request->session()->put('user_session_data', array('user_id' => $user->id, 'is_user_logged_in' => true));
                    return redirect('home')->with('success', trans('messages.logged_in'));
                } else {
                	return redirect('/')->with('message', trans('messages.email_not_verified'));
                }
        	} catch (Exception $e) {
        		return redirect('/')->with('message', trans('messages.server_error'));
        	}
        } else {
        	return redirect('/')->with('message', trans('messages.invalid_credentials'));
        }    	
    }

    
    /**
    * Logout
    * @param null
    */
    public function logout()
    {
    	Session::flush();
		return redirect('/')->with('message',trans("messages.message_after_logout"));
    }

    /**
    * pagenotfound 
    * @return Custom error page 
    */
    public function pagenotfound()
    {
    	return view('errors.error');
    }

    /**
     * Redirect the user to the facebook authentication page.
     *
     * @return Response
     */
    public function social_redirect($service)
    {
        return Socialite::driver($service)->redirect();
    }

    /**
     * Obtain the user information from facebook.
     *
     * @return Response
     */
    public function social_callback($service)
    {
        $user = Socialite::driver($service)->user();
        $authUser = $this->findOrCreateUser($user, $service);
        // echo $authUser->id;
        // p($user); die;
        $userId = $authUser->userId;
        Session::put('user_session_data', array('user_id' => $userId, 'is_user_logged_in' => true));
        return redirect('home')->with('success', trans('messages.logged_in'));
    }

    /**
     * If a user has registered before using social auth, return the user
     * else, create a new user object.
     * @param  $user Socialite user object
     * @param $provider Social auth provider
     * @return  User
     */
    public function findOrCreateUser($user, $provider)
    {
        $checkUser = User::where('email', $user->email)->first();
        // p($checkUser->id); die;
        if($checkUser){
            $authUser = $this->commonModel->getSingleRecord(USER_LOGONS, array('providerKey' => $user->id));
            // p($authUser); die;
            if ($authUser) {
                return $authUser;
            } else {
                // add record into user logs table
                $userData = $this->commonModel->addRecord(USER_LOGONS, array('logonProvider' => $provider, 'providerKey' => $user->id, 'userId' => $checkUser->id, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));

                $authUser = $this->commonModel->getSingleRecord(USER_LOGONS, array('providerKey' => $user->id));
                return $authUser;
            }
            
        } else {
            // create User
            $userId = $this->commonModel->addRecord(USER, array('userName' => $user->name, 'email' => $user->email, 'emailConfirmed' => 2, 'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d')));

            // explode string $user->name
            $userName = explode(" ", $user->name, 2);
            $firstName = $userName[0];
            $lastName = $userName[1];
            if($lastName){
                $lastName = $userName[1];
            } else {
                $lastName = NULL;
            }
            
            // create userLogon
            if($userId){

                $authUser = $this->commonModel->getSingleRecord(USER_LOGONS, array('providerKey' => $user->id));
                // p($authUser); die;
                if ($authUser) {
                    return $authUser;
                } else {
                    // add record into profile table
                    // $memberNo = $this->commonModel->getUniqueId(PROFILE,'memberNumber');
                    /*$getLatestId = DB::table(PROFILE)->select('id')->orderBy('id', 'desc')->first();
                    $prefix = "MIM";
                    $memberNumber = UserHelper::generateMemberNo($prefix);*/
                                        
                    $userData = $this->commonModel->addRecord(PROFILE, array('firstName' => $firstName, 'lastName' => $lastName, 'userId' => $userId, 'profileEmail' => $user->email));
                    // add record into user logs table
                    $userData = $this->commonModel->addRecord(USER_LOGONS, array('logonProvider' => $provider, 'providerKey' => $user->id, 'userId' => $userId, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')));

                    $authUser = $this->commonModel->getSingleRecord(USER_LOGONS, array('providerKey' => $user->id));

                    // send email to registeres user with membership no.
                    $email = $user->email;
                    $userName = $user->name;
                    $html = sprintf(SOCIAL_LOGIN_MESSAGE, $userName);
                    try{
                        Mail::send(array(), array(), function ($message) use ($email, $html) {
                        $message->to($email)
                            ->subject(SOCIAL_LOGIN_SUBJECT)
                            ->from(FROM_EMAIL)
                            ->setBody($html, 'text/html');
                        });
                    } 
                    catch(\Exception $e){
                        Session::flush(); // removes all session data
                        return redirect('login')->with('message', trans('messages.unable_to_send_email'));
                    }
                    return $authUser;
                }
            }
        }
        //gender
    }

    /**
    * Forget password
    */
    public function forgetPassword()
    {
    	return view('web.forgot_password');
    }

    /**
    * Reset password
    * @param Request $request 
    */
    public function resetpassword(Request $request)
    {
    	if($request['email'] != '') {
			$user = User::where('email', $request['email'])->first();
			if($user){
				$userName = $user->userName;
				$token = Password::createToken($user);
				$email = $request['email'];
				$link = WEB_URL.CHANGE_PASSWORD_FRONT_SLUG.$user->id.'/'.$token;
				$html = sprintf(FORGOT_PASSWORD_MESSAGE_WEB, $userName, $link);
				// p($html); die;
				/* Send Email */
				try{
					Mail::send(array(), array(), function ($message) use ($email, $html) {
					$message->to($email)                
						->subject(FORGOT_PASSWORD_SUBJECT)
						->from(FROM_EMAIL)
						->setBody($html, 'text/html');
					});
	
					return redirect('/')->with('success', trans('messages.reset_pwd_mail'));
				} 
				catch(\Exception $e){
					return redirect('forget-password')->with('message', trans('messages.unable_to_send_email'));
				}
	
			} else {
				$html = (FORGOT_PASSWORD_MESSAGE_WEB_NOT_FOUND);
				$email = $request['email'];
				 try{
					Mail::send(array(), array(), function ($message) use ($email, $html) {
					$message->to($email)                
						->subject(FORGOT_PASSWORD_SUBJECT_NOT_FOUND)
						->from(FROM_EMAIL)
						->setBody($html, 'text/html');
					});
	
					//return redirect('/')->with('success', trans('messages.reset_pwd_mail'));
				} 
				catch(\Exception $e){
					return redirect('forget-password')->with('message', trans('messages.unable_to_send_email'));
				}
					return redirect('forget-password')->with('message', trans('messages.user_not_exist'));
				}
		} else {
			return redirect('forget-password')->with('message', 'Please enter an email address.');
		}
    }

    /**
    * Change password
    * @param Request request
    */
    public function changePassword($id, $token)
    {
    	$data = array();
    	$data['id'] = $id;
    	$data['token'] = $token;
    	return view('web.change_password', $data);
    }

    /**
    * Save password 
    * @param Request $request
    */
	public function savePassword(Request $request)
	{
		// Check validations rules
    	$validator = Validator::make($request->all(),[
    		'new_password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/',
    		'password_confirmation'	=>	'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|same:new_password'
		]);

		if ($validator->fails()) {
            return redirect('change-password/'.$request['id'].'/'.$request['token'])
                        ->withErrors($validator)
                        ->withInput();
        }

        /* Check for valid user */
        $userId = $request['id'];
        $user = User::find($userId);

        if(isset($user)) {
            /* Validate token */
            try {
                $credentials = array('email' => $user->email, 'password' => $request['new_password'], 'password_confirmation' => $request['new_password'], 'token' => $request['token']);
                Password::reset($credentials, function ($user, $password) {
                    $user->password = bcrypt($password);
                    $this->changed = $user->save();
                });
            }
            catch(\Exception $e){
                return redirect('change-password/'.$request['id'].'/'.$request['token'])->with('message', trans('messages.server_error'));
            }
  
            if($this->changed == 1) {
                return redirect('/')->with('success', trans('messages.pwd_changed_success'));
            } else {
                return redirect('change-password/'.$request['id'].'/'.$request['token'])->with('message', trans('messages.alreaty_updated_pwd'));
            }
        } else {
            return redirect('change-password/'.$request['id'].'/'.$request['token'])->with('message', trans('messages.user_not_exist'));
        }
	}    
}